<?php
class Wedding_Live_QR {
    public static function admin_menu() {
        add_menu_page( 'Wedding QR Code', 'Wedding Photos', 'manage_options', 'wedding-qr', [self::class, 'page'] );
    }

    public static function page() {
        $url = home_url('/wedding-upload');
        echo '<h1>QR Code for Guests</h1>';
        echo '<p>Scan this QR to upload photos:</p>';
        echo '<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($url) . '" />';
    }
}
