<?php
/**
 * Plugin Name: Wedding Live Photo Sharing
 * Description: Guests can scan a QR code, upload photos, and view a live gallery.
 * Version: 1.0
 * Author: You
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Autoload Classes
foreach ( glob( plugin_dir_path(__FILE__) . "includes/*.php" ) as $file ) {
    include_once $file;
}

// Hooks
add_action( 'init', ['Wedding_Live_CPT', 'register'] );
add_action( 'admin_menu', ['Wedding_Live_QR', 'admin_menu'] );
add_action( 'rest_api_init', ['Wedding_Live_Upload', 'register_routes'] );
add_action( 'rest_api_init', ['Wedding_Live_Gallery', 'register_routes'] );
add_action( 'rest_api_init', ['Wedding_Live_Download', 'register_routes'] );


add_shortcode( 'wedding_upload', ['Wedding_Live_Upload', 'shortcode'] );
add_shortcode( 'wedding_gallery', ['Wedding_Live_Gallery', 'shortcode'] );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'wedding-style', plugin_dir_url(__FILE__) . 'assets/css/style.css' );
    wp_enqueue_script( 'wedding-upload', plugin_dir_url(__FILE__) . 'assets/js/upload.js', ['jquery'], null, true );
    wp_enqueue_script( 'wedding-gallery', plugin_dir_url(__FILE__) . 'assets/js/gallery.js', ['jquery'], null, true );
    wp_localize_script( 'wedding-upload', 'weddingObj', [
        'restUrl' => esc_url( rest_url('wedding/v1/') ),
        'nonce'   => wp_create_nonce('wp_rest')
    ]);
});
