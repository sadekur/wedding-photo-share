jQuery(document).ready(function($){
    $('#wedding_upload_btn').on('click', function(){
        let files = $('#wedding_files')[0].files;
        if(files.length === 0) return alert('Select files');

        let formData = new FormData();
        $.each(files, function(i, file){
            formData.append('upload_file[]', file); // must match PHP key
        });

        $.ajax({
            url: weddingObj.restUrl + 'upload',
            method: 'POST',
            headers: { 'X-WP-Nonce': weddingObj.nonce },
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                console.log(res);
                if (res.success && res.ids.length > 0) {
                    $('#wedding_upload_status').html('Uploaded successfully!');
                } else {
                    $('#wedding_upload_status').html('Upload failed or no files saved.');
                }
            },
            error: function(err){
                console.error(err);
                $('#wedding_upload_status').html('Upload error.');
            }
        });
    });
});
