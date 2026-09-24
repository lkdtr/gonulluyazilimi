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
            tinymce.init({
                license_key: 'gpl',
                promotion: false,
                selector: '.wysiwyg',
                plugins: 'code table lists link fullscreen preview directionality',
                menubar: 'edit view insert format tools table help',
                toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link | table | lineheight outdent indent| forecolor backcolor removeformat | code fullscreen preview | ltr rtl",
                toolbar_sticky: true,
            });
        };
        document.head.appendChild(script);
    });
</script>
