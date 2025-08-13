<?php
class Wedding_Live_Download {
    public static function register_routes() {
        register_rest_route('wedding/v1', '/download', [
            'methods' => 'POST',
            'callback' => [self::class, 'handle_download'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function handle_download(WP_REST_Request $req) {
        $ids = $req->get_param('ids');
        if(empty($ids) || !is_array($ids)) {
            return new WP_Error('no_images', 'No images selected', ['status' => 400]);
        }

        $zip = new ZipArchive();
        $tmp_file = tempnam(sys_get_temp_dir(), 'zip');
        if($zip->open($tmp_file, ZipArchive::CREATE) !== TRUE) {
            return new WP_Error('zip_error', 'Cannot create ZIP', ['status' => 500]);
        }

        foreach($ids as $post_id) {
            $att_id = get_post_meta($post_id, 'attachment_id', true);
            $file_path = get_attached_file($att_id);
            if(file_exists($file_path)) {
                $zip->addFile($file_path, basename($file_path));
            }
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="wedding_photos.zip"');
        header('Content-Length: ' . filesize($tmp_file));
        readfile($tmp_file);
        unlink($tmp_file);
        exit;
    }
}
