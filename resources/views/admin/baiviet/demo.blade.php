<!DOCTYPE html>
<html>
<head>

</head>
<body>
<form method="post">
    <textarea id="post_content" placeholder="Nhập nội dung bài viết"> </textarea>
</form>

<script src="https://cdn.tiny.cloud/1/i0z2ha4nhvnfun7eqz0ao13jdmejcafe2auqgas89ihdyynv/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#post_content', // Replace this CSS selector to match the placeholder element for TinyMCE
        plugins: 'code table lists',
        menubar: 'file edit view insert',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
    });
</script>
</body>
</html>
