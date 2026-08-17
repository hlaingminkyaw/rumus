<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Reports</title>
    <style>
        body { font: 14px system-ui; margin: 2rem; color: #18212f; }
        .filter, table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        td, th { border: 1px solid #d7dce3; padding: .6rem; text-align: left; }
        th { background: #f4f6f8; }
        .filter td { border: 0; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
        .card { border: 1px solid #d7dce3; padding: 1rem; }
        .total { font-weight: 700; }
        input, select, button { padding: .5rem; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
