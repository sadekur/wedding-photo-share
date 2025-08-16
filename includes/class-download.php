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
        if (empty($ids) || !is_array($ids)) {
            return new WP_Error('no_ids', 'No images selected', ['status' => 400]);
        }

        global $wpdb;
        $table = $wpdb->prefix . "wedding_photos";
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $query = $wpdb->prepare("SELECT * FROM $table WHERE id IN ($placeholders)", $ids);
        $rows = $wpdb->get_results($query);

        if (!$rows) {
            return new WP_Error('not_found', 'No photos found', ['status' => 404]);
        }

        $zip = new ZipArchive();
        $tmp_file = tempnam(sys_get_temp_dir(), 'wedding_zip_') . ".zip";

        if ($zip->open($tmp_file, ZipArchive::CREATE) !== TRUE) {
            return new WP_Error('zip_error', 'Could not create ZIP file', ['status' => 500]);
        }

        foreach ($rows as $row) {
            if (file_exists($row->file_path)) {
                $zip->addFile($row->file_path, basename($row->file_path));
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
