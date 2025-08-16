<?php
class Wedding_Live_Upload {
    public static function activate() {
        global $wpdb;
        $table = $wpdb->prefix . "wedding_photos";
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            file_url TEXT NOT NULL,
            file_path TEXT NOT NULL,
            title VARCHAR(255) DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
    public static function shortcode() {
        ob_start(); ?>
        <div class="wedding-upload-wrap">
            <input type="file" id="wedding_files" multiple accept="image/*">
            <button id="wedding_upload_btn">Upload</button>
            <div id="wedding_upload_status"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function register_routes() {
        register_rest_route('wedding/v1', '/upload', [
            'methods' => 'POST',
            'callback' => [self::class, 'handle_upload'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function handle_upload(WP_REST_Request $req) {
        if ( empty($_FILES['upload_file']) || !is_array($_FILES['upload_file']['name']) ) {
            return ['success' => false, 'message' => 'No files found'];
        }

        // Load necessary functions
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $files = $_FILES['upload_file'];
        $ids = [];

        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            // Build a single file array
            $single_file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i]
            ];

            // Handle the upload
            $upload = wp_handle_upload($single_file, ['test_form' => false]);

            if (!isset($upload['file'])) continue;

            $filetype = wp_check_filetype($upload['file']);
            $attachment = [
                'post_mime_type' => $filetype['type'],
                'post_title'     => sanitize_file_name($name),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];

            $attach_id = wp_insert_attachment($attachment, $upload['file']);

            // Generate metadata
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $ids[] = $attach_id;
        }

        return ['success' => true, 'ids' => $ids];
    }
}


