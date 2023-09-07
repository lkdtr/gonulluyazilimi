<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
 <script>
   tinymce.init({
        selector: '.wysiwyg', // Replace this CSS selector to match the placeholder element for TinyMCE
        plugins: 'code table lists',
        menubar: 'edit view insert format tools table help',
        toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link | table | lineheight outdent indent| forecolor backcolor removeformat | code fullscreen preview | ltr rtl",
        toolbar_sticky: true,
        toolbar_sticky_offset: isSmallScreen ? 102 : 108,
   });
 </script>
