<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory Management</title>
    @vite(['resources/css/app.css', 'resources/js/inventory-app.ts'])
</head>
<body class="bg-background text-foreground">
    <div id="inventory-app"></div>
</body>
</html>
