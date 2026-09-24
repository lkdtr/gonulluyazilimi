<script>
    // Load TinyMCE only on pages that have an editor field.
    document.addEventListener('DOMContentLoaded', function () {
        if (!document.querySelector('.wysiwyg')) {
            return;
        }

        var script = document.createElement('script');
        script.src = @json(asset('js/tinymce/tinymce.min.js'));
        script.referrerPolicy = 'origin';
        script.onload = function () {
            // Fields with data-upload-url accept images uploaded into the editor.
            document.querySelectorAll('.wysiwyg[data-upload-url]').forEach(function (field) {
                tinymce.init({
                    target: field,
                    license_key: 'gpl',
                    promotion: false,
                    plugins: 'code table lists link image fullscreen preview directionality',
                    menubar: 'edit view insert format tools table help',
                    toolbar: "undo redo | blocks | bold italic underline | align numlist bullist | link image | table | outdent indent | forecolor backcolor removeformat | code fullscreen preview",
                    toolbar_sticky: true,
                    relative_urls: false,
                    image_caption: false,
                    images_upload_handler: function (blobInfo) {
                        var data = new FormData();
                        data.append('file', blobInfo.blob(), blobInfo.filename());
                        return fetch(field.dataset.uploadUrl, {
                            method: 'POST',
                            body: data,
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        }).then(function (response) {
                            if (!response.ok) { throw new Error('Görsel yüklenemedi (' + response.status + ')'); }
                            return response.json();
                        }).then(function (json) { return json.location; });
                    },
                });
            });

            tinymce.init({
                license_key: 'gpl',
                promotion: false,
                selector: '.wysiwyg:not([data-upload-url])',
                plugins: 'code table lists link fullscreen preview directionality',
                menubar: 'edit view insert format tools table help',
                toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link | table | lineheight outdent indent| forecolor backcolor removeformat | code fullscreen preview | ltr rtl",
                toolbar_sticky: true,
            });
        };
        document.head.appendChild(script);
    });
</script>
