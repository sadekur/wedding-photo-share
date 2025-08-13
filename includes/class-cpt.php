<?php
class Wedding_Live_CPT {
    public static function register() {
        register_post_type('wedding_photo', [
            'labels' => [
                'name' => 'Wedding Photos',
                'singular_name' => 'Wedding Photo'
            ],
            'public' => true,
            'supports' => ['title', 'thumbnail'],
        ]);
    }
}
