<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Template</title>
    <style>
        .pdf-header,
        .pdf-footer {
            text-align: center;
            position: fixed;
            width: 100%;
            padding: 10px;
            font-size: 12px;
        }

        .pdf-footer {
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        Header Content Goes Here
    </div>

    <h1>{{ $title }}</h1>
    <p>{{ $content }}</p>

    <div class="pdf-footer">
        Footer Content Goes Here
    </div>
</body>
</html>
