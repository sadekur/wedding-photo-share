<?php
class Wedding_Live_Gallery {
    public static function shortcode() {
        ob_start(); ?>
        <div id="wedding_gallery_controls">
            <button id="wedding_download_selected">Download Selected</button>
        </div>
        <div id="wedding_gallery" class="wedding-gallery-grid"></div>
        <?php return ob_get_clean();
    }

    public static function register_routes() {
        register_rest_route('wedding/v1', '/photos', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_photos'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function get_photos(WP_REST_Request $req) {
        global $wpdb;
        $table = $wpdb->prefix . "wedding_photos";
        $rows  = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

        $photos = [];
        foreach ($rows as $row) {
            $photos[] = [
                'id'    => $row->id,
                'url'   => $row->file_url,
                'title' => $row->title
            ];
        }
        return $photos;
    }
}
