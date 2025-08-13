jQuery(document).ready(function($){
    let refreshInterval = 5000; // 5 seconds

    function loadGallery() {
        $.get(weddingObj.restUrl + 'photos', function(data){
            let html = '';
            $.each(data, function(i, photo){
                html += `
                    <div class="wedding-photo">
                        <input type="checkbox" class="wedding-select" value="${photo.id}">
                        <img src="${photo.url}" data-full="${photo.full}" alt="${photo.title}" class="wedding-lightbox">
                    </div>
                `;
            });
            $('#wedding_gallery').html(html);
        });
    }

    // Lightbox click
    $(document).on('click', '.wedding-lightbox', function(){
        let imgSrc = $(this).data('full');
        $('body').append(`
            <div id="wedding_lightbox_overlay">
                <img src="${imgSrc}" class="wedding-lightbox-image">
            </div>
        `);
    });

    // Close lightbox
    $(document).on('click', '#wedding_lightbox_overlay', function(){
        $(this).remove();
    });

    // Download selected
    $('#wedding_download_selected').on('click', function(){
        let ids = [];
        $('.wedding-select:checked').each(function(){
            ids.push($(this).val());
        });
        if(ids.length === 0) {
            alert('Select at least one image');
            return;
        }
        // Open ZIP download
        let form = $('<form>', {
            action: weddingObj.restUrl + 'download',
            method: 'POST'
        });
        ids.forEach(id => {
            form.append($('<input>', {type: 'hidden', name: 'ids[]', value: id}));
        });
        $('body').append(form);
        form.submit();
    });

    // Initial load
    loadGallery();

    // Auto-refresh
    setInterval(loadGallery, refreshInterval);
});
