<!-- upload.blade.php -->
<html>
<head>
    <title>Upload File</title>
</head>
<body>
    <form action="{{ route('upload.file') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file">
        <button type="submit">Upload</button>
    </form>
</body>
</html>