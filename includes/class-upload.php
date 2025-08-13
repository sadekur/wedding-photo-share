<?php
class Wedding_Live_Upload {
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
        if ( ! wp_verify_nonce($req->get_header('x-wp-nonce'), 'wp_rest') ) {
            return new WP_Error('forbidden', 'Invalid nonce', ['status' => 403]);
        }

        // Load WP media functions
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        if ( empty($_FILES['upload_file']) || !is_array($_FILES['upload_file']['name']) ) {
            return ['success' => false, 'message' => 'No files found'];
        }

        $files = $_FILES['upload_file'];
        $ids = [];

        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue; // skip failed uploads
            }

            // Build a single file array for WP
            $single_file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i]
            ];

            // Temporarily replace $_FILES with this single file
            $_FILES = ['upload_file' => $single_file];

            $id = media_handle_upload('upload_file', 0);
            if (!is_wp_error($id)) {
                wp_insert_post([
                    'post_type'   => 'wedding_photo',
                    'post_status' => 'publish',
                    'post_title'  => sanitize_file_name($name),
                    'meta_input'  => ['attachment_id' => $id]
                ]);
                $ids[] = $id;
            }
        }

        return ['success' => true, 'ids' => $ids];
    }


}
