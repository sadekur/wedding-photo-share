jQuery(document).ready(function($){
   $('#wedding_upload_btn').on('click', function(){
        let files = $('#wedding_files')[0].files;
        if(files.length === 0) return alert('Select files');

        let formData = new FormData();
        $.each(files, function(i, file){
            formData.append('upload_file[]', file);
        });

        $.ajax({
            url: weddingObj.restUrl + 'upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                if (res.success) {
                    $('#wedding_upload_status').html('Uploaded successfully!');
                    loadGallery();
                } else {
                    $('#wedding_upload_status').html('Upload failed.');
                }
            },
            error: function(err){
                console.error(err);
                $('#wedding_upload_status').html('Upload error.');
            }
        });
    });
    function loadGallery() {
        $.get(weddingObj.restUrl + 'photos', function(data){
            let html = '';
            $.each(data, function(i, photo){
                html += `
                    <div class="wedding-photo">
                        <input type="checkbox" class="wedding-select" value="${photo.id}">
                        <img src="${photo.url}" data-full="${photo.url}" alt="${photo.title}" class="wedding-lightbox">
                    </div>
                `;
            });
            $('#wedding_gallery').html(html);
        });
    }
});
