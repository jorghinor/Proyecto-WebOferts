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

    <title>Webofertas para Clientes</title>
    <style>
        /* ESTILOS COPIADOS DEL ADMIN PARA ESTANDARIZACIÓN */
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
            --client-glow-pink: rgba(255, 175, 211, 0.22);
            --client-title-strong: #634b59;
            --client-title-soft: #7b5f6d;
            --client-text-main: #514753;
            --client-text-soft: #756a76;
            --client-btn-solid: #1affa3;
            --client-btn-text: rgba(0, 0, 0, 0.87);
            --client-btn-shadow: 0 12px 24px rgba(26, 210, 140, 0.2);
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

        #app .v-main .v-toolbar__title,
        #app .v-main .headline,
        #app .v-main .title,
        #app .v-main .subtitle-1,
        #app .v-main .subtitle1 {
            color: var(--client-title-strong) !important;
            text-shadow: 0 4px 12px var(--client-glow-pink);
            letter-spacing: 0.25px;
            transition: transform 180ms ease, text-shadow 180ms ease, color 180ms ease;
        }

        #app .v-main .v-toolbar__title:hover,
        #app .v-main .headline:hover,
        #app .v-main .title:hover,
        #app .v-main .subtitle-1:hover,
        #app .v-main .subtitle1:hover {
            color: var(--client-title-soft) !important;
            text-shadow: 0 6px 16px rgba(255, 175, 211, 0.28);
        }

        #app .v-main,
        #app .v-main .v-card__text,
        #app .v-main .v-list-item__title,
        #app .v-main .v-list-item__subtitle,
        #app .v-main .v-card__subtitle,
        #app .v-main .caption,
        #app .v-main .body-1,
        #app .v-main .body-2,
        #app .v-main .grey--text {
            color: var(--client-text-main);
        }

        #app .v-main .caption,
        #app .v-main .grey--text,
        #app .v-main .v-label {
            color: var(--client-text-soft) !important;
            text-shadow: none;
        }

        #app .v-main .v-input input,
        #app .v-main .v-input textarea,
        #app .v-main .v-select__selection {
            color: var(--client-text-main) !important;
            font-weight: 600;
        }

        /* Navbar (Encabezado) */
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
            color: #6f4d60 !important;
            font-weight: 800 !important;
            text-shadow: 1px 1px 0 rgba(255, 245, 250, 0.55), 0 4px 10px rgba(255, 175, 211, 0.2) !important;
            letter-spacing: 0.35px;
        }

        /* Sidebar (Menú Lateral - si se usa) */
        #app .v-navigation-drawer {
            background: linear-gradient(180deg, #eef7ff 0%, #e8f4ff 52%, #e8fdf5 100%) !important;
            border-right: 1px solid #d5e4ef !important;
            color: var(--admin-sidebar-ink) !important;
        }

        /* Tarjetas */
        #app .v-card {
            background: var(--admin-panel) !important;
            border: 1px solid var(--admin-border) !important;
            border-radius: 16px !important;
            box-shadow: var(--admin-shadow) !important;
            overflow: hidden;
            animation: adminFadeIn 260ms ease-out;
        }

        /* Toolbars internos */
        #app .v-toolbar {
            background: linear-gradient(180deg, #f9fcff 0%, #eef5fa 100%) !important;
            border-bottom: 1px solid var(--admin-border) !important;
        }

        #app .v-toolbar__title,
        #app .title {
            font-weight: 800 !important;
            letter-spacing: 0.2px;
        }

        /* Botones */
        #app .v-btn {
            border-radius: 10px !important;
            text-transform: none !important;
            font-weight: 700 !important;
            letter-spacing: 0.18px;
            transition: transform 160ms ease, box-shadow 180ms ease, filter 180ms ease, background 180ms ease, color 180ms ease;
        }

        #app .v-btn:hover {
            transform: translateY(-2px) scale(1.015);
            box-shadow: 0 12px 24px rgba(26, 210, 140, 0.24);
            filter: brightness(0.99) saturate(1.03);
        }

        #app .v-btn:not(.v-btn--icon) {
            position: relative;
            overflow: hidden;
            background: var(--client-btn-solid) !important;
            color: var(--client-btn-text) !important;
            box-shadow: var(--client-btn-shadow);
            border: 1px solid rgba(12, 163, 103, 0.18);
            text-shadow: none;
        }

        #app .v-btn:not(.v-btn--icon):hover {
            background: var(--client-btn-solid) !important;
            box-shadow: 0 18px 32px rgba(26, 210, 140, 0.28);
        }

        #app .v-btn--icon {
            transition: transform 180ms ease, background-color 180ms ease, box-shadow 180ms ease, color 180ms ease;
        }

        #app .v-btn--icon:hover {
            background: rgba(255, 255, 255, 0.65);
            box-shadow: 0 8px 16px rgba(118, 55, 84, 0.12);
        }

        #app .v-icon {
            transition: transform 180ms ease, color 180ms ease, filter 180ms ease;
        }

        #app .v-btn:hover .v-icon,
        #app a:hover .v-icon {
            transform: scale(1.05);
            color: inherit !important;
            filter: none;
        }

        #app .v-chip {
            box-shadow: 0 8px 16px rgba(78, 177, 83, 0.12);
            transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
        }

        #app .v-chip:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(78, 177, 83, 0.16);
            filter: saturate(1.05);
        }

        /* Tablas */
        #app .v-data-table,
        #app table {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--admin-border);
        }

        #app .v-data-table-header th,
        #app table thead th {
            background: #edf3f8 !important;
            color: #6a5260 !important;
            font-weight: 800 !important;
        }

        #app .v-data-table tbody tr:hover,
        #app table tbody tr:hover {
            background: linear-gradient(90deg, #fff8fe 0%, #f3fbff 100%) !important;
            transition: background-color 180ms ease;
        }

        /* Footer */
        html body #app .v-application .v-footer.client-footer-override {
            background: linear-gradient(90deg, #ffcae6 0%, #ffabd7 48%, #ffc0ae 100%) !important;
            color: #5b3a4c !important;
            font-size: 12px;
            box-shadow: 0 -10px 18px rgba(146, 92, 120, 0.2) !important;
            border-top: 1px solid #eba7ca !important;
        }

        html body #app .v-application .v-footer.client-footer-override .v-card {
            background: transparent !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        @keyframes adminFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Fix para que v-app no rompa el layout */
        .v-application--wrap {
            min-height: fit-content !important;
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
