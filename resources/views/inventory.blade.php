<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Inventory Management</title>

        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/inventory-app.ts'])
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        <div id="inventory-app">
            <div class="p-6 text-sm text-muted-foreground">
                Loading inventory...
            </div>
        </div>
    </body>
</html>
