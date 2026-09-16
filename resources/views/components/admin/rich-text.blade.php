@props(['label' => '', 'id' => 'editor-' . uniqid(), 'height' => 300])

@once
<style>
    .tox-tinymce-aux,
    .tox-silver-sink {
        position: relative !important;
        z-index: 100000 !important;
        overflow: visible !important;
        pointer-events: auto !important;
    }
    .tox-pop,
    .tox-menu {
        z-index: 100000 !important;
        pointer-events: auto !important;
    }
    .tox-dialog-wrap {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 100000 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        pointer-events: auto !important;
    }
    .tox-dialog {
        position: relative !important;
        z-index: 100001 !important;
        margin: auto !important;
        width: 520px !important;
        max-width: 95vw !important;
        height: auto !important;
        min-height: auto !important;
        max-height: 90vh !important;
        background: #ffffff !important;
        border-radius: 8px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        overflow: visible !important;
    }
    .dark .tox-dialog {
        background: #1e293b !important;
        color: #f8fafc !important;
    }
    .tox-dialog__header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 14px 18px !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .dark .tox-dialog__header {
        border-bottom-color: #334155 !important;
    }
    .tox-dialog__title {
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
    }
    .dark .tox-dialog__title {
        color: #f8fafc !important;
    }
    .tox-dialog__body {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        height: auto !important;
        min-height: auto !important;
        padding: 0 !important;
        overflow: visible !important;
    }
    .tox-dialog__body-nav {
        display: none !important;
    }
    .tox-dialog__body-content {
        display: flex !important;
        flex: 1 1 auto !important;
        flex-direction: column !important;
        padding: 16px 20px !important;
        width: 100% !important;
        height: auto !important;
        min-height: auto !important;
        box-sizing: border-box !important;
        overflow: visible !important;
    }
    .tox-dialog__body-content .tox-form {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        gap: 12px !important;
    }
    .tox-dialog__body-content .tox-form__group {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        gap: 6px !important;
        margin-bottom: 6px !important;
    }
    .tox-label {
        display: block !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #334155 !important;
    }
    .dark .tox-label {
        color: #cbd5e1 !important;
    }
    .tox-form__controls-h-stacked {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .tox-textfield,
    .tox-textarea {
        display: block !important;
        width: 100% !important;
        min-height: 38px !important;
        padding: 8px 12px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        font-size: 13px !important;
        box-sizing: border-box !important;
    }
    .dark .tox-textfield,
    .dark .tox-textarea {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    .tox-button--icon {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 38px !important;
        height: 38px !important;
        padding: 0 10px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        background: #f8fafc !important;
        color: #475569 !important;
        cursor: pointer !important;
    }
    .dark .tox-button--icon {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #94a3b8 !important;
    }
    .tox-dialog__footer {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        padding: 12px 18px !important;
        border-top: 1px solid #e2e8f0 !important;
        gap: 8px !important;
    }
    .dark .tox-dialog__footer {
        border-top-color: #334155 !important;
    }
    .tox-dialog-wrap__backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: -1 !important;
    }
    .tox-tinymce,
    .tox .tox-editor-header,
    .tox .tox-toolbar,
    .tox .tox-toolbar-overlord {
        z-index: 1 !important;
    }

    /* ── Dark Mode for TinyMCE ────────────────────────────────────────────── */
    .dark .tox-tinymce {
        border-color: #334155 !important;
        background-color: #0f172a !important;
        border-radius: 6px !important;
    }
    .dark .tox .tox-menubar,
    .dark .tox .tox-editor-header,
    .dark .tox .tox-toolbar,
    .dark .tox .tox-toolbar__primary,
    .dark .tox .tox-toolbar__overflow,
    .dark .tox .tox-toolbar-overlord {
        background-color: #1e293b !important;
        border-bottom-color: #334155 !important;
    }
    .dark .tox .tox-tbtn {
        color: #cbd5e1 !important;
    }
    .dark .tox .tox-tbtn:hover {
        background-color: #334155 !important;
        color: #ffffff !important;
    }
    .dark .tox .tox-tbtn svg {
        fill: #cbd5e1 !important;
    }
    .dark .tox .tox-tbtn:hover svg {
        fill: #ffffff !important;
    }
    .dark .tox .tox-tbtn--enabled,
    .dark .tox .tox-tbtn--active {
        background-color: #334155 !important;
    }
    .dark .tox .tox-tbtn--enabled svg,
    .dark .tox .tox-tbtn--active svg {
        fill: #818cf8 !important;
    }
    .dark .tox .tox-statusbar {
        background-color: #1e293b !important;
        border-top-color: #334155 !important;
        color: #94a3b8 !important;
    }
    .dark .tox .tox-statusbar a,
    .dark .tox .tox-statusbar__wordcount,
    .dark .tox .tox-statusbar__path-item {
        color: #94a3b8 !important;
    }
    .dark .tox .tox-edit-area__iframe {
        background-color: #0f172a !important;
    }
    .dark .tox-menu {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }
    .dark .tox-collection__item {
        color: #cbd5e1 !important;
    }
    .dark .tox-collection__item--active,
    .dark .tox-collection__item--enabled {
        background-color: #334155 !important;
        color: #ffffff !important;
    }
    .dark .tox-collection__item-label {
        color: inherit !important;
    }
</style>
@endonce

<div class="col-span-full" x-data="{
        content: @entangle($attributes->wire('model')),
        init() {
            // Cleanup when Alpine component is destroyed (e.g. Livewire if evaluates to false)
            this._cleanup = () => this.destroy();
            document.addEventListener('livewire:navigating', this._cleanup);

            if (typeof tinymce === 'undefined') {
                if (!document.getElementById('tinymce-script')) {
                    const script = document.createElement('script');
                    script.id = 'tinymce-script';
                    script.src = '{{ asset("js/tinymce/tinymce.min.js") }}';
                    script.referrerPolicy = 'origin';
                    document.head.appendChild(script);
                }
                
                const checkInterval = setInterval(() => {
                    if (typeof tinymce !== 'undefined') {
                        clearInterval(checkInterval);
                        this.initTinyMCE();
                    }
                }, 50);
            } else {
                this.initTinyMCE();
            }

            if (window.Alpine && window.Alpine.store('theme')) {
                this.$watch('$store.theme.dark', (val) => {
                    const editor = tinymce.get(this.$refs.editorTextarea.id);
                    if (editor) {
                        this.content = editor.getContent();
                        editor.remove();
                        this.$nextTick(() => {
                            this.initTinyMCE();
                        });
                    }
                });
            }
        },
        destroy() {
            if (this.$refs.editorTextarea) {
                const editor = tinymce.get(this.$refs.editorTextarea.id);
                if (editor) {
                    editor.remove();
                }
            }
            document.removeEventListener('livewire:navigating', this._cleanup);
        },
        initTinyMCE() {
            const isDark = document.documentElement.classList.contains('dark') 
                || (window.Alpine && window.Alpine.store('theme') && window.Alpine.store('theme').dark)
                || localStorage.getItem('theme') === 'dark';

            tinymce.init({
                target: this.$refs.editorTextarea,
                base_url: '{{ asset("js/tinymce") }}',
                suffix: '.min',
                height: {{ $height }},
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic forecolor backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'link image media table | removeformat | help',
                toolbar_mode: 'sliding',
                default_link_target: '_blank',
                link_default_protocol: 'https',
                link_assume_external_targets: true,
                setup: (editor) => {
                    editor.on('init', () => {
                        if (this.content) {
                            editor.setContent(this.content);
                        }
                    });

                    editor.on('change input undo redo keyup', () => {
                        this.content = editor.getContent();
                    });
                },
                skin: isDark ? 'oxide-dark' : 'oxide',
                content_css: isDark ? 'dark' : 'default',
                branding: false,
                promotion: false,
                license_key: 'gpl',
                file_picker_callback: (callback, value, meta) => {
                    const callbackId = 'tinymce_' + Math.random().toString(36).substring(2, 9);
                    
                    const wire = $wire;
                    const handler = (event) => {
                        const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                        if (detail && detail.callbackId === callbackId) {
                            const url = detail.url || ('/storage/' + detail.storagePath);
                            callback(url, { alt: detail.name || '' });
                            window.removeEventListener('media-picker-chosen', handler);
                        }
                    };
                    
                    window.addEventListener('media-picker-chosen', handler);
                    Livewire.dispatch('open-media-picker', { 
                        callbackId: callbackId, 
                        filterType: meta.filetype === 'image' ? 'image' : (meta.filetype === 'media' ? 'video' : 'all'),
                        multiple: false
                    });
                },
            });

            this.$watch('content', value => {
                const editor = tinymce.get(this.$refs.editorTextarea.id);
                if (editor && value !== editor.getContent() && value !== undefined) {
                    editor.setContent(value || '');
                }
            });
        }
    }"
    x-effect="() => { 
        // Force cleanup on unmount
        if (!$el.isConnected) destroy(); 
    }">
    
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ $label }}</label>
    @endif
    
    <div wire:ignore class="rounded-[3px]">
        <textarea x-ref="editorTextarea" id="{{ $id }}"></textarea>
    </div>
</div>

