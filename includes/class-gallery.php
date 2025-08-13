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
        $query = new WP_Query([
            'post_type' => 'wedding_photo',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
        $photos = [];
        foreach ($query->posts as $post) {
            $att_id = get_post_meta($post->ID, 'attachment_id', true);
            $photos[] = [
                'id' => $post->ID,
                'url' => wp_get_attachment_image_url($att_id, 'medium'),
                'full' => wp_get_attachment_image_url($att_id, 'full'),
                'title' => get_the_title($post)
            ];
        }
        return $photos;
    }
}
