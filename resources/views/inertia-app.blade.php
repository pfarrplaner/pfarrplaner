<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link href="{{ mix('/css/app.css') }}&v={{ json_decode(file_get_contents(base_path('package.json')), true)['version'] }}" rel="stylesheet">
    @routes
    <script src="{{ mix('/js/inertia-app.js') }}&v={{ json_decode(file_get_contents(base_path('package.json')), true)['version'] }}" defer></script>
</head>
<body>
HI
</body>
</html>
