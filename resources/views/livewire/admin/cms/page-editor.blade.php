<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        const originalWarn = console.warn;
        console.warn = function(...args) {
            if (typeof args[0] === 'string' && (args[0].includes('cdn.tailwindcss.com should not be used in production') || args[0].includes('licenseKey') || args[0].includes('Cleanup plugin:'))) return;
            originalWarn.apply(console, args);
        };
    </script>
    <title>Editing: {{ $record->title }} — {{ ucfirst($type) }} Builder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Sidebar Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/themes/admin/assets/css/app.css', 'resources/themes/admin/assets/js/app.js'])
    @livewireStyles
    <style>
        body, #top-bar, .editor-row, .editor-canvas { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body { background: #0f172a; color: #e2e8f0; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }

        /* ── Top Bar ─────────────────────────────────────────────────────── */
        #top-bar {
            height: 52px; background: #1e293b; border-bottom: 1px solid #334155;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 16px; gap: 12px; flex-shrink: 0; z-index: 100;
        }
        #top-bar .left   { display: flex; align-items: center; gap: 12px; }
        #top-bar .center { display: flex; align-items: center; gap: 6px; }
        #top-bar .right  { display: flex; align-items: center; gap: 8px; }

        .page-title-badge {
            background: #334155; border-radius: 3px; padding: 4px 12px;
            font-size: 13px; font-weight: 600; color: #94a3b8;
            display: flex; align-items: center; gap: 6px;
        }
        .page-title-badge span { color: #e2e8f0; }

        .btn-toolbar {
            height: 32px; border-radius: 3px; border: 1px solid #475569;
            background: #334155; color: #cbd5e1; font-size: 12px; font-weight: 500;
            padding: 0 12px; cursor: pointer; display: inline-flex; align-items: center;
            gap: 6px; transition: all .15s;
        }
        .btn-toolbar:hover { background: #475569; border-color: #64748b; color: #fff; }

        .btn-save {
            height: 34px; border-radius: 3px; border: none;
            background: linear-gradient(135deg, #455537, #354329);
            color: #fff; font-size: 13px; font-weight: 600;
            padding: 0 18px; cursor: pointer; display: inline-flex; align-items: center;
            gap: 7px; transition: all .15s; box-shadow: 0 2px 8px rgba(69, 85, 55, .4);
        }
        .btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(69, 85, 55, .5); }
        .btn-save:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        .lang-tab-btn:hover { color: #fff !important; }
        .lang-tab-btn.active { background: #455537 !important; color: #fff !important; box-shadow: 0 1px 3px rgba(69, 85, 55, 0.4); }

        .btn-back {
            height: 32px; border-radius: 3px; border: 1px solid #475569;
            background: transparent; color: #94a3b8; font-size: 12px;
            padding: 0 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none; transition: all .15s;
        }
        .btn-back:hover { border-color: #64748b; color: #e2e8f0; }

        /* Device buttons */
        .device-btn {
            height: 30px; width: 34px; border-radius: 3px; border: 1px solid #475569;
            background: transparent; color: #64748b; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            transition: all .15s; font-size: 14px;
        }
        .device-btn:hover, .device-btn.active { background: #455537; border-color: #455537; color: #fff; }
        .device-btn i { color: #cbd5e1; }
        .device-btn.active i, .device-btn:hover i { color: #fff; }

        /* Status toast */
        #status-toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            padding: 12px 20px; border-radius: 3px; font-size: 13px; font-weight: 600;
            display: none; align-items: center; gap: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,.4); animation: slideUp .3s ease;
        }
        #status-toast.success { display: flex; background: #059669; color: #fff; }
        #status-toast.error   { display: flex; background: #dc2626; color: #fff; }
        @keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

        /* ── Layout ──────────────────────────────────────────────────────── */
        .editor-row {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
            position: relative;
        }

        .editor-canvas {
            flex-grow: 1;
            height: 100%;
            background: #f8fafc;
        }

        .sidebar-container {
            display: flex;
            height: 100%;
            flex-shrink: 0;
            z-index: 50;
        }

        /* The vertical button strip (narrow) */
        .panel__buttons {
            width: 54px;
            background: #0f172a;
            border-left: 1px solid #334155;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 12px;
            gap: 8px;
        }

        /* The active tool panel (wide) */
        .panel__right {
            width: 300px;
            background: #1e293b;
            border-left: 1px solid #334155;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transition: width 0.2s ease;
        }
        .panel__right.collapsed { width: 0; border-left: none; overflow: hidden; }

        /* GrapesJS internal containers */
        #gjs { height: 100%; }

        /* GrapesJS dark theme overrides */
        .gjs-one-bg  { background-color: #1e293b; }
        .gjs-two-bg  { background-color: #0f172a; }
        .gjs-three-bg{ background-color: #334155; }
        .gjs-four-bg { background-color: #475569; }
        .gjs-color-main     { color: #e2e8f0; }
        .gjs-color-second   { color: #94a3b8; }
        .gjs-color-active   { color: #CA9A2E; }
        
        /* Sidebar Button Overrides */
        .gjs-pn-btn.gjs-pn-active { color: #fff !important; background: #455537 !important; box-shadow: 0 4px 12px rgba(69,85,55,0.3); }

        /* General Icon Fix for GrapesJS UI */
        .gjs-pn-btn i, .gjs-pn-btn svg, .gjs-four-color, .gjs-toolbar i, .gjs-toolbar svg, .gjs-layer-vis, .gjs-layer-lock {
            color: #cbd5e1 !important;
            fill: #cbd5e1 !important;
            opacity: 1 !important;
        }
        .gjs-pn-btn.gjs-pn-active i, .gjs-pn-btn.gjs-pn-active svg {
            color: #fff !important;
            fill: #fff !important;
        }

        /* Blocks grid */
        .gjs-blocks-c {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            padding: 12px !important;
        }

        .gjs-block { 
            border-radius: 3px; 
            border: 1px solid #334155; 
            background: #1e293b; 
            transition: all 0.2s;
            width: calc(50% - 6px) !important;
            min-height: 90px !important;
            padding: 12px !important;
            margin: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            cursor: pointer;
        }
        .gjs-block:hover { border-color: #455537; background: rgba(69,85,55,0.15); transform: translateY(-2px); }
        .gjs-block__media { 
            color: #CA9A2E; 
            line-height: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            width: 100%; 
            height: 40px; 
        }
        .gjs-block__media svg, .gjs-block__media i, .gjs-block__media img, .gjs-block__media div {
            width: 32px !important;
            height: 32px !important;
            font-size: 32px !important;
            fill: #CA9A2E !important;
            stroke: #CA9A2E !important;
            display: block;
        }
        /* Fix for layout column preview blocks (which use div borders instead of SVGs) */
        .gs-block-item__flex-row > div {
            border-style: solid !important;
            border-color: #455537 !important;
        }

        .gjs-block__label { font-size: 10px !important; font-weight: 600 !important; color: #cbd5e1 !important; text-transform: uppercase; letter-spacing: 0.05em; text-align: center; }

        .gjs-cv-canvas { background: #f8fafc; }
        .gjs-toolbar { background: #1e293b; border: 1px solid #455537; border-radius: 3px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .gjs-selected { outline: 3px solid #455537 !important; outline-offset: -3px; }
        .gjs-hovered  { outline: 1px dashed #455537 !important; }

        /* GrapesJS built-in RTE — hidden, we use panel-based TinyMCE instead */
        .gjs-rte-toolbar { display: none !important; }

        /* ── Rich Text Editor Panel ──────────────────────────────────────────── */
        #gjs-rte-panel {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 440px;
            z-index: 9999;
            flex-direction: column;
            background: #1e293b;
            border-top: 2px solid #455537;
            box-shadow: 0 -8px 32px rgba(0,0,0,0.5);
            animation: rteSlideUp 0.2s ease;
        }
        #gjs-rte-panel.rte-open { display: flex; }
        @keyframes rteSlideUp {
            from { transform: translateY(100%); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        #gjs-rte-header {
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 16px;
            background: #0f172a;
            border-bottom: 1px solid #334155;
        }
        #gjs-rte-header .rte-title {
            font-size: 12px; font-weight: 600; color: #94a3b8;
            display: flex; align-items: center; gap: 8px;
        }
        #gjs-rte-header .rte-title i { color: #CA9A2E; }
        #gjs-rte-actions { display: flex; gap: 8px; }
        #gjs-rte-apply {
            height: 30px; padding: 0 14px;
            background: linear-gradient(135deg, #455537, #354329);
            border: none; border-radius: 3px;
            color: #fff; font-size: 12px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 6px;
            transition: all .15s;
        }
        #gjs-rte-apply:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(69,85,55,.4); }
        #gjs-rte-cancel {
            height: 30px; padding: 0 12px;
            background: #334155; border: 1px solid #475569; border-radius: 3px;
            color: #cbd5e1; font-size: 12px; cursor: pointer; transition: all .15s;
        }
        #gjs-rte-cancel:hover { background: #475569; color: #fff; }
        #gjs-rte-body { flex: 1; padding: 8px; overflow: visible; background: #1e293b; }
        #gjs-rte-body .tox-tinymce { border-radius: 3px !important; }

        /* TinyMCE dialogs, menus, and floating pickers.
         * Z-index ordering:
         *   RTE Panel      =   9,999  (#gjs-rte-panel)
         *   TinyMCE UI     =  50,000  (.tox-tinymce-aux — menus, dialogs, popovers)
         *   Media Picker   = 999,999  (Livewire z-[999999] — must be above TinyMCE dialogs)
         */
        .tox-tinymce-aux,
        .tox-silver-sink,
        .tox-pop,
        .tox-menu,
        .tox-dialog-wrap {
            z-index: 50000 !important;
            pointer-events: auto !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        /* Ensure dialog is properly positioned in the viewport */
        .tox-dialog-wrap {
            position: fixed !important;
        }
        /* Backdrop should not cover the panel's Apply/Cancel buttons */
        .tox-dialog-wrap__backdrop {
            z-index: -1 !important;
        }

    </style>
</head>
<body>

<!-- ── Top Bar ─────────────────────────────────────────────────────────────── -->
<div id="top-bar">
    <div class="left">
        @php
            $backRoute = match($type) {
                'destination' => 'admin.destinations.index',
                default => 'cms.' . $type . 's.index',
            };
        @endphp
        <a href="{{ route($backRoute) }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>{{ __('Back') }}</a>
        <div class="page-title-badge">
            <i class="fa-solid fa-file-lines"></i> <span>{{ $record->title }}</span>
        </div>

        <!-- Language Switcher Tabs -->
        <div class="lang-switcher" style="display: inline-flex; background: #0f172a; border-radius: 4px; padding: 2px; border: 1px solid #334155; gap: 2px; margin-left: 8px;">
            @foreach(['en' => '🇺🇸 EN', 'fr' => '🇫🇷 FR', 'de' => '🇩🇪 DE', 'es' => '🇪🇸 ES', 'it' => '🇮🇹 IT', 'zh' => '🇨🇳 ZH'] as $code => $label)
                <button type="button" 
                        class="lang-tab-btn {{ $code === 'en' ? 'active' : '' }}" 
                        data-locale="{{ $code }}"
                        id="lang-btn-{{ $code }}"
                        onclick="switchLocale('{{ $code }}')"
                        style="padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; border: none; cursor: pointer; background: {{ $code === 'en' ? '#455537' : 'transparent' }}; color: {{ $code === 'en' ? '#fff' : '#94a3b8' }}; transition: all .15s;">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="center">
        <button class="device-btn active" onclick="setDevice('desktop')" title="{{ __('Desktop') }}" id="btn-desktop"><i class="fa-solid fa-desktop"></i></button>
        <button class="device-btn" onclick="setDevice('tablet')" title="{{ __('Tablet') }}" id="btn-tablet"><i class="fa-solid fa-tablet-screen-button"></i></button>
        <button class="device-btn" onclick="setDevice('mobile')" title="{{ __('Mobile') }}" id="btn-mobile"><i class="fa-solid fa-mobile-screen-button"></i></button>
    </div>

    <div class="right">
        <button class="btn-toolbar" onclick="editor.runCommand('undo')" title="{{ __('Undo') }}"><i class="fa-solid fa-rotate-left"></i></button>
        <button class="btn-toolbar" onclick="editor.runCommand('redo')" title="{{ __('Redo') }}"><i class="fa-solid fa-rotate-right"></i></button>
        <button class="btn-toolbar" onclick="editor.runCommand('open-assets')" title="{{ __('Open Media Library') }}"><i class="fa-solid fa-images"></i>{{ __('Media Library') }}</button>
        <button class="btn-toolbar" onclick="previewPage()"><i class="fa-solid fa-eye"></i>{{ __('Preview') }}</button>
        <button class="btn-save" id="save-btn" onclick="savePage()">
            <i class="fa-solid fa-cloud-arrow-up"></i>{{ __('Save Changes') }}</button>
    </div>
</div>

<div class="editor-row">
    <!-- ── GrapesJS Editor Canvas ───────────────────────────────────────────── -->
    <div class="editor-canvas">
        <div id="gjs"></div>
    </div>
</div>


<!-- ── Rich Text Editor Panel (panel-based TinyMCE, same as Tour form) ─────── -->
<div id="gjs-rte-panel">
    <div id="gjs-rte-header">
        <span class="rte-title">
            <i class="fa-solid fa-pen-to-square"></i>
            Rich Text Editor — double-click any text block to edit
        </span>
        <div class="rte-actions">
            <button id="gjs-rte-apply" title="Apply changes">
                <i class="fa-solid fa-check"></i> Apply
            </button>
            <button id="gjs-rte-cancel" title="Discard changes">
                <i class="fa-solid fa-xmark"></i> Cancel
            </button>
        </div>
    </div>
    <div id="gjs-rte-body">
        <textarea id="gjs-rte-textarea"></textarea>
    </div>
</div>

<!-- ── Status Toast ───────────────────────────────────────────────────────── -->
<div id="status-toast">
    <span id="toast-msg"></span>
</div>

<script>
    window.RECORD_ID = {{ $record->id }};
    window.RECORD_TYPE = '{{ $type }}';
    window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    window.TINYMCE_KEY = '{{ $tinymce_key }}';
    window.TOUR_CATEGORIES = @json($tourCategories ?? []);
    window.TOUR_ROUTES = @json($tourRoutes ?? []);
    window.GROUP_CATEGORIES = @json($groupCategories ?? []);
    window.TOUR_TYPES = @json($tourTypes ?? []);
    window.TOUR_TAGS = @json($tourTags ?? []);
    window.COUNTRIES = @json($countries ?? []);
    window.POST_CATEGORIES = @json($postCategories ?? []);
    window.TOUR_COUNTRIES = @json($tourCountries ?? []);
    window.ACTIVITIES = @json($activities ?? []);
    window.APP_CSS_URL = '{{ Vite::asset('resources/css/app.css') }}';

    // Editor Translations Dictionary
    window.EDITOR_TRANSLATIONS = {
        'Safaris Blocks': '{{ __("Safaris Blocks") }}',
        'Text': '{{ __("Text") }}',
        'Image': '{{ __("Image") }}',
        'Button': '{{ __("Button") }}',
        'Section': '{{ __("Section") }}',
        'Row': '{{ __("Row") }}',
        'Column': '{{ __("Column") }}',
        'All Categories': '{{ __("All Categories") }}',
        'All Tour Types': '{{ __("All Tour Types") }}',
        'All Countries': '{{ __("All Countries") }}',
        'All Group Categories': '{{ __("All Group Categories") }}',
        'All Tags': '{{ __("All Tags") }}',
        'All Routes': '{{ __("All Routes") }}',
        'All Activities': '{{ __("All Activities") }}'
    };

    // Global JS translation helper
    window.__ = function(key) {
        return (window.EDITOR_TRANSLATIONS && window.EDITOR_TRANSLATIONS[key]) 
            ? window.EDITOR_TRANSLATIONS[key] 
            : key;
    };
    
    // Catch any global errors
    window.addEventListener('error', function(e) {
        console.error('Global Error Caught:', e.error);
        showToast('JS Error: ' + (e.error ? e.error.message : e.message), 'error');
    });

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('status-toast');
        if (toast) {
            document.getElementById('toast-msg').textContent = msg;
            toast.className = type;
            toast.style.display = 'flex';
            setTimeout(() => { toast.style.display = 'none'; }, 5000);
        }
    }

    window.EDITOR_COUNTRIES_DATA = @json(\App\Models\Country::orderBy('name')->get(['id', 'name']));
</script>
{{-- Load TinyMCE standalone (same source as rich-text component) BEFORE the GrapesJS bundle --}}
<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
@vite(['resources/js/page-editor.js'])
<livewire:admin.media.media-picker-modal />
@include('partials.livewire-waf-bypass')
@livewireScripts
</body>
</html>
