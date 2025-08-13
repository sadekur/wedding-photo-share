jQuery(document).ready(function($){
    $('#wedding_upload_btn').on('click', function(){
        let files = $('#wedding_files')[0].files;
        if(files.length === 0) return alert('Select files');

        let formData = new FormData();
        $.each(files, function(i, file){
            formData.append('files[]', file);
        });

        $.ajax({
            url: weddingObj.restUrl + 'upload',
            method: 'POST',
            headers: { 'X-WP-Nonce': weddingObj.nonce },
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                $('#wedding_upload_status').html('Uploaded successfully!');
            }
        });
    });
});
