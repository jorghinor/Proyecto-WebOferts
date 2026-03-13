<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <meta name="csrf-token" content="{{ csrf_token()}}">

    <title>Admin Webofertas</title>
    <style>
        :root {
            --admin-bg: #f7fbff;
            --admin-panel: #ffffff;
            --admin-ink: #2d3f52;
            --admin-muted: #6f8192;
            --admin-border: #dbe7f1;
            --admin-accent: #4aa3a2;
            --admin-accent-strong: #3a8e8d;
            --admin-sidebar: #e9f4ff;
            --admin-sidebar-ink: #355169;
            --admin-shadow: 0 14px 28px rgba(42, 73, 106, 0.12);
        }

        html, body {
            min-height: 100%;
            background: linear-gradient(180deg, #fbfeff 0%, #f3f9ff 55%, #edf6ff 100%);
            color: var(--admin-ink);
        }

        #app,
        #app .v-application {
            font-family: "Manrope", "Segoe UI", sans-serif;
            color: var(--admin-ink);
            background: transparent !important;
        }

        #app .v-main {
            padding-top: 8px;
            background:
                radial-gradient(circle at 12% 0%, rgba(122, 190, 255, 0.22) 0%, rgba(122, 190, 255, 0) 42%),
                radial-gradient(circle at 88% 0%, rgba(128, 233, 210, 0.2) 0%, rgba(128, 233, 210, 0) 44%),
                linear-gradient(180deg, #f4faff 0%, #e9f4ff 52%, #e4f3ff 100%);
        }

        html body #app .v-application .v-app-bar.v-sheet.theme--light {
            background: linear-gradient(90deg, #ffd9ef 0%, #ffc8e7 48%, #ffd8c9 100%) !important;
            color: #5b3a4c !important;
            box-shadow: 0 8px 16px rgba(146, 92, 120, 0.16) !important;
            border-bottom: 1px solid #f1bdd8 !important;
        }

        html body #app .v-application .v-app-bar.v-sheet.theme--light .v-toolbar__content .v-toolbar-title,
        html body #app .v-application .v-app-bar.v-sheet.theme--light .v-toolbar-title {
            display: inline-block;
            background: none !important;
            -webkit-background-clip: initial !important;
            background-clip: initial !important;
            -webkit-text-fill-color: initial !important;
            color: #c96c00 !important;
            font-weight: 800 !important;
            text-shadow: 1px 1px 0 rgba(54, 20, 38, 0.55) !important;
            letter-spacing: 0.35px;
        }

        #app .v-navigation-drawer {
            background: linear-gradient(180deg, #eef7ff 0%, #e8f4ff 52%, #e8fdf5 100%) !important;
            border-right: 1px solid #d5e4ef !important;
            color: var(--admin-sidebar-ink) !important;
        }

        #app .v-navigation-drawer .v-list-item__title,
        #app .v-navigation-drawer .v-list-item__subtitle,
        #app .v-navigation-drawer .v-icon {
            color: var(--admin-sidebar-ink) !important;
        }

        #app .v-navigation-drawer .v-list-item {
            border-radius: 12px;
            margin: 4px 10px;
            transition: background-color 160ms ease, transform 160ms ease;
        }

        #app .v-navigation-drawer .v-list-item:hover {
            transform: translateX(2px);
            background: rgba(126, 188, 230, 0.22);
        }

        #app .v-card {
            background: var(--admin-panel) !important;
            border: 1px solid var(--admin-border) !important;
            border-radius: 16px !important;
            box-shadow: var(--admin-shadow) !important;
            overflow: hidden;
            animation: adminFadeIn 260ms ease-out;
        }

        #app .v-toolbar {
            background: linear-gradient(180deg, #f9fcff 0%, #eef5fa 100%) !important;
            border-bottom: 1px solid var(--admin-border) !important;
        }

        #app .v-toolbar__title,
        #app .title {
            font-weight: 800 !important;
            letter-spacing: 0.2px;
        }

        #app .v-btn {
            border-radius: 10px !important;
            text-transform: none !important;
            font-weight: 700 !important;
            transition: transform 140ms ease, box-shadow 140ms ease, filter 140ms ease;
        }

        #app .v-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(21, 48, 72, 0.16);
            filter: saturate(1.04);
        }

        #app .v-chip {
            border-radius: 999px !important;
            font-weight: 700 !important;
        }

        #app .v-input input,
        #app .v-input textarea {
            color: var(--admin-ink) !important;
        }

        #app .v-text-field--outlined fieldset {
            border-color: #b7c8d6 !important;
        }

        #app .v-text-field--outlined:hover fieldset {
            border-color: var(--admin-accent) !important;
        }

        #app .v-data-table,
        #app table {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--admin-border);
        }

        #app .v-data-table-header th,
        #app table thead th {
            background: #edf3f8 !important;
            color: #2a4256 !important;
            font-weight: 800 !important;
        }

        #app .v-data-table tbody tr:hover,
        #app table tbody tr:hover {
            background: linear-gradient(90deg, #fff8fe 0%, #f3fbff 100%) !important;
            transition: background-color 180ms ease;
        }

        #app .v-main .v-card .row.no-gutters.py-1 {
            border-radius: 10px;
            transition: background-color 180ms ease, transform 180ms ease, box-shadow 180ms ease;
        }

        #app .v-main .v-card .row.no-gutters.py-1:hover {
            background: linear-gradient(90deg, rgba(255, 234, 247, 0.86) 0%, rgba(233, 248, 255, 0.92) 100%);
            transform: translateX(2px);
            box-shadow: inset 0 0 0 1px rgba(233, 166, 206, 0.35);
        }

        #app .v-main .v-card .v-data-table tbody tr,
        #app .v-main .v-card .v-simple-table tbody tr,
        #app .v-main .v-card table tbody tr {
            transition: background-color 180ms ease, transform 180ms ease;
        }

        #app .v-main .v-card .v-data-table tbody tr:hover,
        #app .v-main .v-card .v-simple-table tbody tr:hover,
        #app .v-main .v-card table tbody tr:hover {
            background: linear-gradient(90deg, #fff3fb 0%, #eef8ff 100%) !important;
            transform: translateX(1px);
        }

        #app .v-snack__content {
            font-weight: 700;
        }

        @keyframes adminFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div id="app">
        @yield('main_section')
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
