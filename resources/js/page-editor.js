import grapesjs from 'grapesjs';
import grapesjsPresetWebpage from 'grapesjs-preset-webpage';
import grapesjsForms from 'grapesjs-plugin-forms';
import grapesjsCountdown from 'grapesjs-component-countdown';
import grapesjsTabs from 'grapesjs-tabs';
import grapesjsCustomCode from 'grapesjs-custom-code';
import grapesjsTooltip from 'grapesjs-tooltip';
import grapesjsTyped from 'grapesjs-typed';
import grapesjsBlocksFlexbox from 'grapesjs-blocks-flexbox';
import { 
    tableComponent, 
    accordionComponent, 
    iconifyComponent,
    shapeDividerComponent,
    fsLightboxComponent
} from '@grapesjs/studio-sdk-plugins';
import 'grapesjs/dist/css/grapes.min.css';

// TinyMCE is loaded as a standalone script via <script> tag in page-editor.blade.php
// (from public/js/tinymce/tinymce.min.js) - same approach as the rich-text component.
// window.tinymce is set automatically by that standalone script.

// ── Initialize Editor ─────────────────────────────────────────────────────────
let editor;
try {
    editor = window.editor = grapesjs.init({
    container: '#gjs',
    height: '100%',
    width: 'auto',
    storageManager: false,

    // Canvas styles — inject JKI brand into the editor iframe
    canvas: {
        styles: [
            'https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Indie+Flower&family=Instrument+Sans:wght@400;500;600;700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap',
            '/css/pagebuilder.css',
            window.APP_CSS_URL || ''
        ].filter(Boolean),
        scripts: [
            '/js/tinymce/tinymce.min.js'
        ]
    },
    plugins: [
        grapesjsPresetWebpage, 
        grapesjsForms,
        grapesjsCountdown,
        grapesjsTabs,
        grapesjsCustomCode,
        grapesjsTooltip,
        grapesjsTyped,
        grapesjsBlocksFlexbox,
        tableComponent.init(),
        accordionComponent.init(),
        iconifyComponent.init({ extendIconComponent: false }),
        shapeDividerComponent.init(),
        fsLightboxComponent.init(),
    ],
    pluginsOpts: {
        'gjs-preset-webpage': {
            // Options for the webpage preset
            modalImportTitle: 'Import Code',
            modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste your HTML/CSS here and click Import</div>',
            modalImportContent: '',
        },
    },

    // Disable the GrapesJS built-in RTE toolbar — we use TinyMCE inline via setCustomRte() instead.
    // Without this, GrapesJS renders its own black B/I/U/S toolbar on top of TinyMCE.
    richTextEditor: { actions: [] },
});
} catch (e) {
    console.error('GrapesJS Init Error:', e);
    setTimeout(() => {
        const toast = document.getElementById('status-toast');
        if(toast) {
            toast.className = 'error';
            toast.style.display = 'flex';
            document.getElementById('toast-msg').textContent = 'Init Error: ' + e.message;
        }
    }, 1000);
}

// ── Override GrapesJS Default Asset Manager to use Safaris Livewire Media Library ──
if (editor && editor.Commands) {
    editor.Commands.add('open-assets', {
        run(editor, sender, opts = {}) {
            const target = opts.target || editor.getSelected();
            const callbackId = 'gjs_asset_' + Math.random().toString(36).substring(2, 9);

            const handler = (event) => {
                const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                if (detail && detail.callbackId === callbackId) {
                    const imageUrl = detail.url;
                    if (!imageUrl) return;

                    const asset = editor.AssetManager.add(imageUrl);

                    if (opts.onSelect && typeof opts.onSelect === 'function') {
                        opts.onSelect(asset);
                    } else if (target) {
                        const isImg = (target.is && (target.is('image') || target.is('video'))) || target.get('tagName') === 'img';
                        if (isImg && typeof target.set === 'function') {
                            target.set('src', imageUrl);
                            const attrs = target.get('attributes') || {};
                            attrs.src = imageUrl;
                            target.set('attributes', attrs);
                        }
                    }

                    if (opts.onClose && typeof opts.onClose === 'function') {
                        opts.onClose();
                    }

                    window.removeEventListener('media-picker-chosen', handler);
                }
            };

            window.addEventListener('media-picker-chosen', handler);

            if (window.Livewire) {
                window.Livewire.dispatch('open-media-picker', {
                    callbackId: callbackId,
                    filterType: 'image',
                    multiple: false
                });
            } else {
                window.dispatchEvent(new CustomEvent('open-media-picker', {
                    detail: { callbackId: callbackId, filterType: 'image', multiple: false }
                }));
            }
        }
    });
}

// Custom Safaris Blocks will be added to the preset's BlockManager
const bm = editor.BlockManager;

bm.add('safaris-content-block', {
    label: '★ Content Block',
    category: window.__('Safaris Blocks'),
    attributes: { class: 'fa-solid fa-align-left' },
    content: `
    <section class="py-16 bg-white text-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold font-brand text-gray-900 mb-6">Section Heading</h2>
            <p class="text-lg text-gray-600 leading-relaxed mb-4">
                This is your premium content block. You can use it to build standard article bodies, description sections, or details about your itineraries and tours. It is pre-configured with a maximum width container (<code class="text-sm font-semibold bg-gray-100 px-1.5 py-0.5 rounded text-indigo-600">max-w-7xl</code>) to align beautifully with the rest of your site layout.
            </p>
            <p class="text-lg text-gray-600 leading-relaxed">
                Double-click any text here to start editing. Use the toolbar to apply formatting, list styles, or insert links and media.
            </p>
        </div>
    </section>
    `,
});

bm.add('safaris-page-header', {
    label: '★ Page Header',
    category: window.__('Safaris Blocks'),
    content: `
    <section data-gjs-type="safaris-page-header-block" class="safaris-page-header-block relative w-full h-72 md:h-96 overflow-hidden bg-gray-950">
        <div class="dynamic-bg-container absolute inset-0 w-full h-full">
            <img src="https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&q=80" alt="Header Background" class="absolute inset-0 w-full h-full object-cover opacity-50">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-gray-950/60 via-gray-950/30 to-gray-950/80 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-brand-primary/20 border border-brand-primary/40 text-brand-primary text-xs font-bold uppercase tracking-widest mb-4">
                Discover Africa
            </span>
            <h1 class="dynamic-page-title text-4xl md:text-6xl font-bold text-white font-brand leading-tight mb-4">
                Explore Our Destinations
            </h1>
            <p class="text-gray-300 text-base md:text-lg max-w-2xl">
                From the vast plains of the Serengeti to the misty heights of Kilimanjaro — discover Africa's most
                extraordinary places.
            </p>
        </div>
    </section>
    `,
});

bm.add('safaris-quick-nav', {
    label: '★ Post Navigation Block',
    category: window.__('Safaris Blocks'),
    content: `
    <div data-gjs-type="safaris-quick-nav-block" class="safaris-quick-nav-block" data-post-category="all" data-target="_self">
       <div class="text-center py-8 bg-gray-50 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
          <div class="w-12 h-12 mx-auto mb-3 bg-brand-light rounded-full flex items-center justify-center">
             <i class="fa-solid fa-list-ul text-xl text-brand-primary"></i>
          </div>
          <h3 class="text-lg font-bold text-gray-900 font-brand">Post Navigation Block</h3>
          <p class="text-xs text-gray-500 mt-2">Category: <span class="font-bold text-brand-primary">all</span> | Target: <span class="font-bold text-brand-primary">_self</span></p>
       </div>
    </div>
    `,
    attributes: { class: 'fa-solid fa-list-ul' }
});

bm.add('safaris-kili-calculator', {
    label: '★ Kilimanjaro Calculator',
    category: window.__('Safaris Blocks'),
    content: `
    <div data-gjs-type="safaris-kili-calculator-block" class="safaris-kili-calculator-block">
        <div class="max-w-7xl mx-auto my-8 px-4 font-sans">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-xl">
                <!-- Left Image Side -->
                <div class="lg:col-span-5 relative group overflow-hidden rounded-2xl min-h-[380px] flex flex-col justify-end p-6 text-white bg-gray-900">
                    <img src="https://images.unsplash.com/photo-1589553460732-57ef51160601?auto=format&fit=crop&q=80" alt="Mount Kilimanjaro Summit Climb" data-gjs-type="image" class="absolute inset-0 w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent pointer-events-none" style="pointer-events: none;"></div>
                    <div class="relative z-10 space-y-2">
                        <span class="inline-block px-3 py-1 rounded-full bg-[#EC9824] text-[#2E1F12] text-xs font-bold uppercase tracking-wider kili-badge" data-gjs-type="text">Mount Kilimanjaro (5,895m)</span>
                        <h3 class="text-2xl font-bold font-brand text-white kili-heading" data-gjs-type="text">Conquer the Roof of Africa</h3>
                        <p class="text-xs text-gray-200 kili-desc" data-gjs-type="text">Choosing the right route duration and acclimatization strategy can raise your summit success rate from 60% to over 95%.</p>
                    </div>
                </div>
                <!-- Right Calculator Preview -->
                <div class="lg:col-span-7 p-4 flex flex-col justify-between">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full bg-[#EC9824]/15 text-[#EC9824] text-xs font-bold uppercase tracking-wider mb-2">Summit Estimator</span>
                        <h2 class="text-2xl font-bold font-brand text-gray-900 dark:text-white kili-title" data-gjs-type="text">Kilimanjaro Success Rate Calculator</h2>
                        <p class="text-xs text-gray-500 mt-1 kili-sub" data-gjs-type="text">Estimate your summit success probability based on acclimatization and route profiles.</p>
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700 text-center">
                            <i class="fa-solid fa-calculator text-2xl text-[#EC9824] mb-2"></i>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Live Interactive Calculator Widget</p>
                            <p class="text-[11px] text-gray-400 mt-1">Users can calculate success rates & request quotes directly on your website.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
    attributes: { class: 'fa-solid fa-calculator' }
});

// ── Safaris Accordion Component ────────────────────────────────────────────────
// Self-contained FAQ/Accordion block with add & remove item capability via traits
(function registerSafarisAccordion() {

    // Helper: generate stable accordion item HTML
    function buildItemHtml(q, a, index) {
        return `<div class="safaris-acc-item" data-acc-index="${index}">
  <div class="safaris-acc-header" role="button" tabindex="0" aria-expanded="false" data-gjs-selectable="false" data-gjs-hoverable="false">
    <span class="safaris-acc-question" data-gjs-type="text">${q}</span>
    <span class="safaris-acc-icon" aria-hidden="true"></span>
  </div>
  <div class="safaris-acc-body">
    <div class="safaris-acc-body-inner" data-gjs-type="text">${a}</div>
  </div>
</div>`;
    }

    // Helper: rebuild full accordion inner HTML from a JS items array
    function buildAccordionHtml(items) {
        return items.map((item, i) => buildItemHtml(item.q, item.a, i)).join('\n');
    }

    // Parse items from current DOM
    function parseItems(comp) {
        const rawItems = [];
        try {
            const comps = comp.components().models;
            comps.forEach((c, i) => {
                const headerComp = c.components().models.find(m => m.getClasses().includes('safaris-acc-header'));
                const bodyComp   = c.components().models.find(m => m.getClasses().includes('safaris-acc-body'));
                const questionComp = headerComp?.components().models.find(m => m.getClasses().includes('safaris-acc-question'));
                const bodyInnerComp = bodyComp?.components().models.find(m => m.getClasses().includes('safaris-acc-body-inner'));
                rawItems.push({
                    q: questionComp?.toHTML().replace(/<[^>]+>/g, '').trim() || `Question ${i + 1}`,
                    a: bodyInnerComp?.toHTML().replace(/<span[^>]*>.*?<\/span>/g, '').replace(/<[^>]+>/g, '').trim() || `Answer ${i + 1}`
                });
            });
        } catch(e) {}
        return rawItems;
    }

    // Register the GrapesJS component type
    editor.DomComponents.addType('safaris-accordion', {
        model: {
            defaults: {
                tagName: 'div',
                attributes: { class: 'safaris-accordion max-w-7xl mx-auto px-4 sm:px-6 lg:px-8', 'data-gjs-type': 'safaris-accordion' },
                // Traits — rendered in the right-hand panel when the component is selected
                traits: [
                    {
                        // Info label
                        type: 'text',
                        name: '_acc_info',
                        label: 'Accordion Items',
                        placeholder: 'Use buttons below to add/remove items. Double-click text in the canvas to edit questions & answers.',
                        changeProp: false,
                    },
                    {
                        type: 'button',
                        label: false,
                        name: 'add_accordion_item',
                        text: '＋ Add Item',
                        full: true,
                        command(editor) {
                            const sel = editor.getSelected();
                            if (!sel) return;
                            const items = parseItems(sel);
                            const newIndex = items.length;
                            items.push({ q: `New Question ${newIndex + 1}`, a: 'Click on the question or answer text to edit it.' });
                            sel.components(buildAccordionHtml(items));
                            setTimeout(() => injectAccordionScript(editor), 80);
                        }
                    },
                    {
                        type: 'button',
                        label: false,
                        name: 'remove_accordion_item',
                        text: '✕ Remove Last Item',
                        full: true,
                        command(editor) {
                            const sel = editor.getSelected();
                            if (!sel) return;
                            const items = parseItems(sel);
                            if (items.length <= 1) {
                                alert('An accordion must have at least one item.');
                                return;
                            }
                            items.pop();
                            sel.components(buildAccordionHtml(items));
                            setTimeout(() => injectAccordionScript(editor), 80);
                        }
                    }
                ],
                // Default content: 5 starter items
                components: buildAccordionHtml([
                    { q: 'What is included in my safari package?',        a: 'All our safaris include airport transfers, park entrance fees, accommodation, meals as specified, an experienced guide, and a 4×4 safari vehicle. International flights are typically not included.' },
                    { q: 'How many days do I need for a Tanzania safari?', a: 'We recommend a minimum of 5–7 days for a meaningful safari experience. Shorter trips (3–4 days) can work for Tarangire or Lake Manyara, but the Serengeti and Ngorongoro benefit from more time.' },
                    { q: 'What is the best time to visit Tanzania?',       a: 'The dry seasons (June–October and January–February) are ideal for wildlife viewing. The Great Migration crosses the Serengeti year-round, with dramatic river crossings typically occurring July–October.' },
                    { q: 'Are your safaris suitable for families?',        a: 'Absolutely! We craft tailor-made family itineraries with age-appropriate activities, kid-friendly accommodations, and flexible schedules. Children must be at least 5 years old for most game drives.' },
                    { q: 'How do I book and what is the payment policy?',  a: 'Simply contact us via WhatsApp, email, or our online form. We send a detailed proposal within 24 hours. A 30% deposit confirms your booking, with the balance due 60 days before departure.' },
                ]),
            },
            init() {
                // After model is attached inject the accordion toggle script
                this.listenTo(this, 'change:components', () => {
                    setTimeout(() => injectAccordionScript(editor), 100);
                });
            }
        },
        view: {
            onRender({ el }) {
                setTimeout(() => injectAccordionScript(editor), 150);
            }
        }
    });

    // Inject the accordion toggle JS into the GrapesJS canvas iframe
    function injectAccordionScript(ed) {
        try {
            const doc = ed.Canvas.getDocument();
            if (!doc) return;

            // Remove any previous injection
            const old = doc.getElementById('safaris-acc-runtime');
            if (old) old.remove();

            const script = doc.createElement('script');
            script.id = 'safaris-acc-runtime';
            script.textContent = `
(function() {
    function initAccordion() {
        document.querySelectorAll('.safaris-acc-header').forEach(function(btn) {
            // Prevent duplicate listeners via data attribute instead of cloning, to preserve GrapesJS DOM bindings
            if (btn.dataset.accInitialized) return;
            btn.dataset.accInitialized = 'true';
            
            btn.addEventListener('click', function(e) {
                // If clicked exactly on the question text, ignore the accordion toggle 
                // so GrapesJS can reliably select and double-click to edit the text!
                if (e.target.closest('.safaris-acc-question')) {
                    return;
                }

                var item   = btn.closest('.safaris-acc-item');
                var body   = item ? item.querySelector('.safaris-acc-body') : null;
                var isOpen = btn.getAttribute('aria-expanded') === 'true';

                // Close all sibling items (single-open accordion)
                var accordion = item ? item.closest('.safaris-accordion') : null;
                if (accordion) {
                    accordion.querySelectorAll('.safaris-acc-item').forEach(function(sib) {
                        var sibBtn  = sib.querySelector('.safaris-acc-header');
                        var sibBody = sib.querySelector('.safaris-acc-body');
                        if (sibBtn && sib !== item) {
                            sibBtn.setAttribute('aria-expanded', 'false');
                            if (sibBody) sibBody.style.maxHeight = '0';
                        }
                    });
                }

                btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                if (body) body.style.maxHeight = isOpen ? '0' : body.scrollHeight + 'px';
            });
        });
    }
    // Run on DOM ready + any MutationObserver re-adds
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAccordion);
    } else {
        initAccordion();
    }
    // Re-init if GrapesJS adds new items via drag-n-drop or trait
    var _acc_observer = new MutationObserver(function(muts) {
        var relevant = muts.some(function(m) {
            return Array.from(m.addedNodes).some(function(n) {
                return n.nodeType === 1 && (n.classList && n.classList.contains('safaris-acc-item') || n.querySelector && n.querySelector('.safaris-acc-item'));
            });
        });
        if (relevant) initAccordion();
    });
    _acc_observer.observe(document.body, { childList: true, subtree: true });
})();
            `;
            doc.body.appendChild(script);
        } catch(e) {}
    }

    // Also register a trait "button" type if it isn't already (some GJS versions omit it)
    if (!editor.Traits.getType('button')) {
        editor.Traits.addType('button', {
            createInput({ trait }) {
                const el = document.createElement('button');
                el.type = 'button';
                el.textContent = trait.get('text') || 'Click';
                el.style.cssText = 'width:100%; padding:8px 12px; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; letter-spacing:0.03em; transition:all .15s;';
                el.onmouseover = () => el.style.opacity = '0.85';
                el.onmouseout  = () => el.style.opacity = '1';
                el.addEventListener('click', () => {
                    const cmd = trait.get('command');
                    if (typeof cmd === 'function') cmd(editor, trait);
                });
                return el;
            },
            onUpdate() {}
        });
    }

    // Re-inject accordion on every GrapesJS component selection change
    editor.on('component:selected', (comp) => {
        if (comp && comp.get('type') === 'safaris-accordion') {
            setTimeout(() => injectAccordionScript(editor), 120);
        }
    });

    // Also inject once after editor loads (in case the page already contains an accordion)
    editor.on('load', () => {
        setTimeout(() => injectAccordionScript(editor), 500);
    });

    // ── Register the Accordion block in the Safaris Blocks category ────────────
    bm.add('safaris-accordion', {
        label: 'FAQ Accordion',
        category: window.__('Safaris Blocks'),
        media: `<svg viewBox="0 0 24 24" style="fill: none !important;" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 6h16M4 6v3h16V6M4 12h16M4 12v3h16v-3M4 18h16M15 9l2 1.5L15 12"/>
        </svg>`,
        content: {
            type: 'safaris-accordion',
        }
    });

})();
// ── END Safaris Accordion Component ───────────────────────────────────────────

// Add fonts to GrapesJS Style Manager Typography font-family list
editor.on('load', () => {
    // Set canvas body default font
    const style = document.createElement('style');
    style.innerHTML = 'body { font-family: "Lora", serif !important; }';
    editor.Canvas.getDocument().head.appendChild(style);

    // Apply frontend body classes so global CSS rules match
    editor.Canvas.getBody().classList.add('frontend-body', 'font-lora');

    // ── Panel-based TinyMCE RTE ──────────────────────────────────────────────────
    // TinyMCE runs in the main document (outside the GrapesJS canvas iframe),
    // identical to the Tour form's rich-text component.
    // This avoids ALL iframe focus/blur/stopPropagation conflicts.

    const _rte = {
        el: null,             // the canvas DOM element currently being edited
        tiny: null,           // singleton TinyMCE instance
        ready: false,         // TinyMCE initialized flag
        originalContent: '',  // saved for Cancel

        panel()    { return document.getElementById('gjs-rte-panel'); },
        textarea() { return document.getElementById('gjs-rte-textarea'); },

        open()  { this.panel()?.classList.add('rte-open'); },
        close() { this.panel()?.classList.remove('rte-open'); },

        // Initialize TinyMCE once (singleton), then reuse
        initTiny(callback) {
            if (this.ready && this.tiny) { callback(this.tiny); return; }

            if (typeof tinymce === 'undefined') {
                console.error('TinyMCE not loaded'); return;
            }

            tinymce.init({
                target: this.textarea(),
                height: 370,
                menubar: false,
                license_key: 'gpl',
                branding: false,
                promotion: false,
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
                skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
                content_css: [
                    document.documentElement.classList.contains('dark') ? 'dark' : 'default',
                    ...Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(link => link.href),
                    'https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Indie+Flower&family=Instrument+Sans:wght@400;500;600;700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap',
                    '/css/pagebuilder.css',
                    window.APP_CSS_URL || ''
                ].filter(Boolean),
                forced_root_block: false,
                default_link_target: '_blank',
                link_default_protocol: 'https',
                link_assume_external_targets: true,
                base_url: '/js/tinymce',
                suffix: '.min',
                file_picker_callback(cb, value, meta) {
                    const callbackId = 'tinymce_' + Math.random().toString(36).substring(2, 9);

                    const handler = (event) => {
                        const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                        if (detail && detail.callbackId === callbackId) {
                            cb(detail.url, { alt: detail.name || '' });
                            window.removeEventListener('media-picker-chosen', handler);
                        }
                    };

                    window.addEventListener('media-picker-chosen', handler);

                    window.Livewire.dispatch('open-media-picker', {
                        callbackId,
                        filterType: meta.filetype === 'image' ? 'image' : (meta.filetype === 'media' ? 'video' : 'all')
                    });
                },
                setup: (ed) => {
                    _rte.tiny = ed;
                    ed.on('init', () => {
                        _rte.ready = true;
                        callback(ed);
                    });
                }
            });
        },

        // Apply TinyMCE content back to the canvas element
        applyContent() {
            if (!this.tiny || !this.el) return;
            try {
                let html = this.tiny.getContent();
                
                // TinyMCE often wraps inline text in <p> tags. 
                // Inserting <p> inside headings (h1-h6) causes GrapesJS parser to destroy the original element
                // and replace it with a generic text node, losing all Tailwind classes.
                // We must strip paragraph tags and replace them with breaks if needed.
                html = html.replace(/<p[^>]*>/gi, '').replace(/<\/p>/gi, '<br>').replace(/(<br>)+$/i, '');

                const selected = editor.getSelected();
                if (selected) {
                    selected.components(html);
                } else {
                    this.el.innerHTML = html;
                }
            } catch(e) {}
        },

        // Restore original content (Cancel)
        restoreContent() {
            if (!this.el) return;
            try {
                this.el.innerHTML = this.originalContent;
                if (this.tiny) this.tiny.setContent(this.originalContent || '');
            } catch(e) {}
        }
    };

    // ── Apply button ──────────────────────────────────────────────────────────────
    document.getElementById('gjs-rte-apply')?.addEventListener('click', () => {
        _rte.applyContent();
        _rte.close();
        _rte.el = null;
        editor.select(null); // deselect component in GrapesJS
    });

    // ── Cancel button ─────────────────────────────────────────────────────────────
    document.getElementById('gjs-rte-cancel')?.addEventListener('click', () => {
        _rte.restoreContent();
        _rte.close();
        _rte.el = null;
        editor.select(null);
    });

    // ── setCustomRte ─────────────────────────────────────────────────────────────
    editor.setCustomRte({
        enable(el, rte) {
            _rte.el = el;
            _rte.originalContent = el.innerHTML || '';
            _rte.open();

            const applyStyles = (ed) => {
                const body = ed.getBody();
                if (body && el) {
                    body.className = el.className;
                    body.classList.add('frontend-body', 'font-lora');
                    body.style.cssText = el.style.cssText;
                    
                    try {
                        const computed = el.ownerDocument.defaultView.getComputedStyle(el);
                        if (computed) {
                            body.style.fontFamily = computed.fontFamily;
                            body.style.fontSize = computed.fontSize;
                            body.style.fontWeight = computed.fontWeight;
                            body.style.color = computed.color;
                            body.style.lineHeight = computed.lineHeight;
                            body.style.letterSpacing = computed.letterSpacing;
                        }
                    } catch(e) {}

                    body.style.minHeight = '100px';
                    body.style.padding = '15px';
                    body.style.margin = '0';
                    body.style.overflowY = 'auto';
                }
            };

            // Already initialized — just update content
            if (_rte.ready && _rte.tiny) {
                try {
                    _rte.tiny.setContent(el.innerHTML || '');
                    applyStyles(_rte.tiny);
                    setTimeout(() => _rte.tiny.focus(), 80);
                } catch(e) {}
                return _rte.tiny;
            }

            // First use — initialize TinyMCE
            return new Promise((resolve) => {
                _rte.initTiny((ed) => {
                    ed.setContent(el.innerHTML || '');
                    applyStyles(ed);
                    setTimeout(() => ed.focus(), 80);
                    resolve(ed);
                });
            });
        },

        disable(el, rte) {
            // ── GUARD: do NOT close if the panel is still open ──────────────────
            // Clicking any TinyMCE toolbar button causes the canvas iframe to blur,
            // which makes GrapesJS call disable(). We must ignore that call while
            // the user is actively using the editor panel.
            // The panel only closes via the explicit Apply / Cancel buttons.
            if (_rte.panel()?.classList.contains('rte-open')) return;

            // Fallthrough: panel was already closed (e.g. Apply/Cancel pressed first)
            _rte.applyContent();
            _rte.close();
            _rte.el = null;
            // Keep TinyMCE alive as singleton — don't destroy
        },

        getContent(el, rte) {
            if (_rte.tiny) {
                try { return _rte.tiny.getContent(); } catch(e) {}
            }
            return el.innerHTML;
        }
    });

    const sm = editor.StyleManager;
    const fontProp = sm.getProperty('typography', 'font-family');
    if (fontProp) {
        if (typeof fontProp.addOption === 'function') {
            fontProp.addOption({ id: 'Lora, serif', label: 'Lora' });
            fontProp.addOption({ id: '"Playfair Display", serif', label: 'Playfair Display' });
            fontProp.addOption({ id: 'Jost, sans-serif', label: 'Jost' });
            fontProp.addOption({ id: 'Merriweather, serif', label: 'Merriweather' });
            fontProp.addOption({ id: '"Indie Flower", cursive', label: 'Indie Flower' });
            fontProp.addOption({ id: 'Montserrat, sans-serif', label: 'Montserrat' });
        } else {
            const list = fontProp.get('options') || fontProp.get('list') || [];
            list.push({ value: 'Lora, serif', name: 'Lora', id: 'Lora, serif', label: 'Lora' });
            list.push({ value: '"Playfair Display", serif', name: 'Playfair Display', id: '"Playfair Display", serif', label: 'Playfair Display' });
            list.push({ value: 'Jost, sans-serif', name: 'Jost', id: 'Jost, sans-serif', label: 'Jost' });
            list.push({ value: 'Merriweather, serif', name: 'Merriweather', id: 'Merriweather, serif', label: 'Merriweather' });
            list.push({ value: '"Indie Flower", cursive', name: 'Indie Flower', id: '"Indie Flower", cursive', label: 'Indie Flower' });
            list.push({ value: 'Montserrat, sans-serif', name: 'Montserrat', id: 'Montserrat, sans-serif', label: 'Montserrat' });
            fontProp.set('list', list);
            fontProp.set('options', list);
        }
        fontProp.set('default', 'Lora, serif');
        sm.render();
    }
});

// Register Multiselect Trait
editor.Traits.addType('multiselect', {
    createInput({ trait }) {
        const el = document.createElement('div');
        // Do not use gjs-field here to avoid GrapesJS hiding the inputs
        el.style.display = 'flex';
        el.style.flexDirection = 'column';
        el.style.gap = '8px';
        el.style.padding = '8px';
        el.style.maxHeight = '200px';
        el.style.overflowY = 'auto';
        el.style.background = 'rgba(0,0,0,0.2)';
        el.style.borderRadius = '3px';
        el.style.color = '#fff';
        
        const options = trait.get('options') || [];
        options.forEach(opt => {
            const label = document.createElement('label');
            label.style.display = 'flex';
            label.style.alignItems = 'center';
            label.style.gap = '8px';
            label.style.fontSize = '13px';
            label.style.cursor = 'pointer';
            label.style.margin = '0';
            
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = opt.value;
            checkbox.className = 'trait-checkbox';
            // Force checkbox visibility against any CSS resets
            checkbox.style.appearance = 'checkbox';
            checkbox.style.webkitAppearance = 'checkbox';
            checkbox.style.width = '16px';
            checkbox.style.height = '16px';
            checkbox.style.margin = '0';
            checkbox.style.cursor = 'pointer';
            
            checkbox.addEventListener('change', () => {
                const checkboxes = el.querySelectorAll('.trait-checkbox:checked');
                const selected = Array.from(checkboxes).map(c => c.value).join(',');
                trait.set('value', selected);
            });
            
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(opt.name));
            el.appendChild(label);
        });
        
        return el;
    },
    onUpdate({ elInput, trait }) {
        const value = trait.get('value') || '';
        const values = value.split(',');
        const checkboxes = elInput.querySelectorAll('.trait-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = values.includes(cb.value);
        });
    }
});

// Register Media Picker Trait
editor.Traits.addType('media_picker', {
    createInput({ trait }) {
        const el = document.createElement('div');
        el.innerHTML = `
            <div style="display:flex; gap:5px;">
                <input type="text" class="trait-input" style="flex:1; width:100%; color:#fff; background:rgba(0,0,0,0.2); border:1px solid #475569; padding:4px; border-radius:3px; font-size:12px;">
                <button class="trait-btn" type="button" style="background:#475569; border:none; border-radius:3px; color:#fff; cursor:pointer; padding:4px 8px;"><i class="fa fa-image"></i></button>
            </div>
        `;
        const input = el.querySelector('.trait-input');
        const btn = el.querySelector('.trait-btn');
        
        input.addEventListener('change', () => trait.set('value', input.value));
        
        btn.addEventListener('click', () => {
            editor.AssetManager.open({
                select(asset, complete) {
                    input.value = asset.getSrc();
                    trait.set('value', input.value);
                    if (complete) editor.AssetManager.close();
                }
            });
        });
        
        return el;
    },
    onUpdate({ elInput, trait }) {
        const input = elInput.querySelector('.trait-input');
        if (input) input.value = trait.get('value') || '';
    }
});

// Register Explore Card Component
editor.DomComponents.addType('explore-card', {
    model: {
        defaults: {
            tagName: 'a',
            attributes: {
                class: 'explore-card',
                href: '#'
            },
            traits: [
                {
                    type: 'text',
                    name: 'href',
                    label: 'Link URL'
                },
                {
                    type: 'media_picker',
                    name: 'img-src',
                    label: 'Background Image'
                }
            ],
        },
        init() {
            this.on('change:attributes:img-src', this.updateImage);
            
            // Sync initial state from parsed child img tag to the trait on load
            setTimeout(() => {
                const imgComp = this.components().models.find(m => m.get('tagName') === 'img');
                if (imgComp && !this.getAttributes()['img-src']) {
                    const src = imgComp.getAttributes().src;
                    if (src) {
                        this.addAttributes({ 'img-src': src });
                    }
                }
            }, 50);
        },
        updateImage() {
            const imgUrl = this.getAttributes()['img-src'];
            if (imgUrl) {
                const imgComp = this.components().models.find(m => m.get('tagName') === 'img');
                if (imgComp) {
                    imgComp.setAttributes({ src: imgUrl });
                }
            }
        }
    }
});

// Register Safaris Hero Component
editor.DomComponents.addType('safaris-hero-component', {
    model: {
        defaults: {
            tagName: 'section',
            attributes: {
                class: 'hero',
                id: 'home',
                'data-bg-type': 'image',
                'data-bg-image': 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1920&q=80',
                'data-bg-video': '',
                'data-bg-video-url': '',
            },
            traits: [
                {
                    type: 'select',
                    name: 'data-bg-type',
                    label: 'Background Type',
                    options: [
                        { value: 'image', name: 'Image' },
                        { value: 'video_upload', name: 'Uploaded Video' },
                        { value: 'video_url', name: 'External Video URL' }
                    ]
                },
                {
                    type: 'media_picker',
                    name: 'data-bg-image',
                    label: 'Background Image'
                },
                {
                    type: 'media_picker',
                    name: 'data-bg-video',
                    label: 'Uploaded Video (MP4)'
                },
                {
                    type: 'text',
                    name: 'data-bg-video-url',
                    label: 'Video URL (Direct MP4 link)'
                }
            ],
            components: `
                <div class="hero-bg-container" data-gjs-locked="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
                    <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1920&q=80')"></div>
                </div>
                <div class="hero-content" style="position: relative; z-index: 10;">
                    <div class="hero-badge">✦ Tanzania Safari Specialist</div>
                    <h1>Where the Wild<br><em>Calls Your Name</em></h1>
                    <p class="hero-sub">Tailor-made safaris, Kilimanjaro climbs & Zanzibar escapes — crafted by people who were born here, grew up here, and can't imagine anywhere else.</p>
                    <div class="hero-actions">
                        <a href="#contact" class="btn-primary">Plan My Adventure</a>
                        <a href="#trips" class="btn-ghost">View Itineraries</a>
                    </div>
                </div>
                <div class="hero-scroll" style="position: relative; z-index: 10;">Scroll</div>
            `
        },
        init() {
            this.on('change:attributes:data-bg-type change:attributes:data-bg-image change:attributes:data-bg-video change:attributes:data-bg-video-url', this.updateBackground);
            setTimeout(() => this.updateBackground(), 50);
        },
        updateBackground() {
            const bgType = this.getAttributes()['data-bg-type'] || 'image';
            const bgImage = this.getAttributes()['data-bg-image'] || '';
            const bgVideo = this.getAttributes()['data-bg-video'] || '';
            const bgVideoUrl = this.getAttributes()['data-bg-video-url'] || '';
            
            const bgContainer = this.components().models.find(m => m.getClasses().includes('hero-bg-container'));
            if (!bgContainer) return;
            
            // Enforce overflow hidden on the container to clip scaled videos
            bgContainer.addStyle({ overflow: 'hidden' });

            let comps = [];
            
            if (bgType === 'image') {
                comps = [
                    {
                        tagName: 'div',
                        style: {
                            'background-image': `url('${bgImage}')`,
                            'background-size': 'cover',
                            'background-position': 'center',
                            'width': '100%',
                            'height': '100%',
                            'position': 'absolute',
                            'inset': '0'
                        },
                        attributes: {
                            class: 'hero-bg'
                        }
                    }
                ];
            } else if ((bgType === 'video_upload' && bgVideo) || (bgType === 'video_url' && bgVideoUrl)) {
                const url = bgType === 'video_upload' ? bgVideo : bgVideoUrl;
                
                // Robust YouTube ID Extractor (handles watch, youtu.be, shorts, live, embed)
                const ytMatch = url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|shorts\/|live\/|watch\?v=|\&v=)([^#\&\?]*).*/);
                const ytId = (ytMatch && ytMatch[2].length === 11) ? ytMatch[2] : null;
                
                if (ytId) {
                    comps = [
                        {
                            tagName: 'iframe',
                            style: {
                                'position': 'absolute',
                                'top': '50%',
                                'left': '50%',
                                'width': '150vw',
                                'height': '150vh',
                                'transform': 'translate(-50%, -50%)',
                                'pointer-events': 'none',
                                'min-width': '100%',
                                'min-height': '100%'
                            },
                            attributes: {
                                class: 'hero-bg-video',
                                src: `https://www.youtube.com/embed/${ytId}?autoplay=1&loop=1&mute=1&controls=0&playlist=${ytId}&showinfo=0&rel=0&modestbranding=1&playsinline=1&disablekb=1&iv_load_policy=3`,
                                frameborder: '0',
                                allow: 'autoplay; fullscreen'
                            }
                        },
                        {
                            tagName: 'div',
                            style: {
                                'position': 'absolute',
                                'inset': '0',
                                'background': 'rgba(0,0,0,0.4)',
                                'z-index': '2'
                            },
                            attributes: {
                                class: 'hero-bg-overlay'
                            }
                        }
                    ];
                } else {
                    // Standard MP4 Video
                    comps = [
                        {
                            tagName: 'video',
                            style: {
                                'position': 'absolute',
                                'top': '0',
                                'left': '0',
                                'width': '100%',
                                'height': '100%',
                                'object-fit': 'cover',
                                'pointer-events': 'none'
                            },
                            attributes: {
                                class: 'hero-bg-video',
                                autoplay: 'autoplay',
                                loop: 'loop',
                                muted: 'muted',
                                playsinline: 'playsinline'
                            },
                            components: [
                                {
                                    tagName: 'source',
                                    attributes: {
                                        src: url,
                                        type: 'video/mp4'
                                    }
                                }
                            ]
                        },
                        {
                            tagName: 'div',
                            style: {
                                'position': 'absolute',
                                'inset': '0',
                                'background': 'rgba(0,0,0,0.4)',
                                'z-index': '2'
                            },
                            attributes: {
                                class: 'hero-bg-overlay'
                            }
                        }
                    ];
                }
            } else {
                comps = [
                    {
                        tagName: 'div',
                        style: {
                            'background-image': "url('')",
                            'background-size': 'cover',
                            'background-position': 'center',
                            'width': '100%',
                            'height': '100%',
                            'position': 'absolute',
                            'inset': '0'
                        },
                        attributes: {
                            class: 'hero-bg'
                        }
                    }
                ];
            }
            
            bgContainer.components(comps);
        }
    }
});

// Register Kilimanjaro Calculator Component with Live Canvas Preview
editor.DomComponents.addType('safaris-kili-calculator-block', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: {
                class: 'safaris-kili-calculator-block',
                'data-title': 'Kilimanjaro Success Rate Calculator',
                'data-subtitle': 'Estimate your summit success probability based on acclimatization and route profiles.',
                'data-image': '',
                'data-badge': 'Mount Kilimanjaro (5,895m)',
                'data-heading': 'Conquer the Roof of Africa',
                'data-description': 'Choosing the right route duration and acclimatization strategy can raise your summit success rate from 60% to over 95%.'
            }
        }
    }
});

// Register Dynamic Tour Listing Component
editor.DomComponents.addType('tours-listing', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: {
                class: 'tours-listing',
                'data-category': 'all',
                'data-country': 'all',
                'data-limit': '5'
            },
            traits: [
                {
                    type: 'multiselect',
                    name: 'data-category',
                    label: 'Category',
                    options: [
                        { value: 'all', name: window.__('All Categories') },
                        ...(window.TOUR_CATEGORIES || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-tour-type',
                    label: 'Tour Type',
                    options: [
                        { value: 'all', name: window.__('All Tour Types') },
                        ...(window.TOUR_TYPES || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-country',
                    label: 'Country',
                    options: [
                        { value: 'all', name: window.__('All Countries') },
                        ...(window.COUNTRIES || []).map(c => ({ value: c.id, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'number',
                    name: 'data-limit',
                    label: 'Limit / Per Page',
                    min: 1,
                    max: 100,
                    value: 6
                }
            ],
        },
        init() {
            this.on('change:attributes:data-category change:attributes:data-tour-type change:attributes:data-country change:attributes:data-limit', this.updateCanvasHtml);
            this.updateCanvasHtml();
        },
        updateCanvasHtml() {
            const category = this.getAttributes()['data-category'] || 'all';
            const tourType = this.getAttributes()['data-tour-type'] || 'all';
            const country = this.getAttributes()['data-country'] || 'all';
            const limit = this.getAttributes()['data-limit'] || '5';
            
            let tabsHtml = '';
            if (category !== 'all' && category.includes(',')) {
                const cats = category.split(',');
                tabsHtml = '<div class="flex gap-2 mt-4 justify-center">';
                cats.forEach(cat => {
                    tabsHtml += `<span class="px-4 py-1.5 bg-[#EC9824]/20 text-[#8B6914] text-xs font-bold uppercase rounded-full border border-[#EC9824]/30">${cat}</span>`;
                });
                tabsHtml += '</div>';
            }

            this.set('content', `
               <div class="text-center py-10 bg-gray-50 border border-dashed border-gray-300 rounded-[3px] p-6" style="pointer-events: none;">
                  <p class="text-gray-700 font-bold text-base">🚙 Dynamic Tour Listing</p>
                  <p class="text-sm text-gray-500 mt-2">Type: <span class="font-bold text-indigo-600">${tourType}</span> | Country: <span class="font-bold text-indigo-600">${country}</span> | Limit: <span class="font-bold text-indigo-600">${limit}</span></p>
                  ${tabsHtml ? tabsHtml : `<p class="text-sm text-gray-500 mt-2">Category: <span class="font-bold text-indigo-600">${category}</span></p>`}
                  <p class="text-[10px] text-gray-400 mt-4 uppercase tracking-wider font-semibold">Will load live tours from the database on the frontend</p>
               </div>
            `);
        }
    }
});

bm.add('safaris-tours-dynamic', {
    label: '🚙 Dynamic Tours',
    category: window.__('Safaris Blocks'),
    content: `
<section class="trips-section py-20" id="trips">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="trips-header flex justify-between items-end mb-12">
      <div>
        <div class="section-label text-[11px] tracking-[0.18em] uppercase text-[#8B6914] font-medium mb-[0.6rem] flex items-center gap-[10px]">
          <span class="inline-block w-[28px] h-[1px] bg-[#8B6914]"></span>
          Our Itineraries
        </div>
        <h2 class="section-title text-[clamp(2.2rem,3.5vw,3.4rem)] font-medium text-[#2E1F12] leading-[1.12] mb-[1.3rem] tracking-[-0.01em] font-display">Popular <em class="italic text-[#A67C5B] font-normal">Adventures</em></h2>
      </div>
      <a href="/tours" class="btn-primary bg-[#EC9824] text-[#2E1F12] py-[13px] px-[34px] rounded-[3px] text-[12px] font-semibold tracking-[0.1em] uppercase cursor-pointer no-underline inline-block transition-all duration-250 shadow-[0_3px_16px_rgba(201,168,76,0.3)] hover:bg-[#d4b060] hover:-translate-y-[2px] hover:shadow-[0_6px_24px_rgba(201,168,76,0.4)] whitespace-nowrap">View All Trips</a>
    </div>

    <!-- Tours Listing Container -->
    <div data-gjs-type="tours-listing" class="tours-listing" data-category="all" data-tour-type="all" data-country="all" data-limit="4">
       <div class="text-center py-10 bg-gray-50 border border-dashed border-gray-300 rounded-[3px] p-6" style="pointer-events: none;">
          <p class="text-gray-700 font-bold text-base">🚙 Dynamic Tour Listing</p>
          <p class="text-sm text-gray-500 mt-1">Category: <span class="font-bold text-indigo-600">all</span> | Type: <span class="font-bold text-indigo-600">all</span> | Country ID: <span class="font-bold text-indigo-600">all</span> | Limit: <span class="font-bold text-indigo-600">4</span></p>
          <p class="text-[10px] text-gray-400 mt-2 uppercase tracking-wider font-semibold">Will load live tours from the database on the frontend</p>
       </div>
    </div>
  </div>
</section>`,
});

// Register Dynamic Group Tour Listing Component
editor.DomComponents.addType('group-tours-listing', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: {
                class: 'group-tours-listing',
                'data-group-category': 'all',
                'data-limit': '3'
            },
            traits: [
                {
                    type: 'select',
                    name: 'data-group-category',
                    label: 'Group Category',
                    options: [
                        { value: 'all', name: window.__('All Group Categories') },
                        ...(window.GROUP_CATEGORIES || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'number',
                    name: 'data-limit',
                    label: 'Limit / Per Page',
                    min: 1,
                    max: 100,
                    value: 6
                }
            ],
        },
        init() {
            this.on('change:attributes:data-group-category change:attributes:data-limit', this.updateCanvasHtml);
            this.updateCanvasHtml();
        },
        updateCanvasHtml() {
            const category = this.getAttributes()['data-group-category'] || 'all';
            const limit = this.getAttributes()['data-limit'] || '3';
            
            this.set('content', `
               <div class="text-center py-10 bg-amber-50 border border-dashed border-amber-300 rounded-[3px] p-6" style="pointer-events: none;">
                  <p class="text-amber-800 font-bold text-base">🚌 Dynamic Group Tours Listing</p>
                  <p class="text-sm text-amber-700 mt-1">Category: <span class="font-bold">${category}</span> | Limit: <span class="font-bold">${limit}</span></p>
                  <p class="text-[10px] text-amber-500 mt-2 uppercase tracking-wider font-semibold">Will load live group tours from the database on the frontend</p>
               </div>
            `);
        }
    }
});

bm.add('safaris-group-tours-dynamic', {
    label: '🚌 Group Tours',
    category: window.__('Safaris Blocks'),
    content: `
<section class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-end mb-12">
      <div>
        <h2 class="text-4xl font-bold font-brand text-gray-900 mb-4">Upcoming Group Tours</h2>
        <p class="text-gray-600 max-w-2xl">Join a group of like-minded travelers on our scheduled group adventures.</p>
      </div>
      <a href="/group-tours" class="hidden md:inline-flex items-center text-amber-600 font-bold hover:text-amber-700">
        View All Group Tours
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
      </a>
    </div>

    <!-- Group Tours Listing Container -->
    <div data-gjs-type="group-tours-listing" class="group-tours-listing" data-group-category="all" data-limit="3">
       <div class="text-center py-10 bg-amber-50 border border-dashed border-amber-300 rounded-[3px] p-6" style="pointer-events: none;">
          <p class="text-amber-800 font-bold text-base">🚌 Dynamic Group Tours Listing</p>
          <p class="text-sm text-amber-700 mt-1">Category: <span class="font-bold">all</span> | Limit: <span class="font-bold">3</span></p>
          <p class="text-[10px] text-amber-500 mt-2 uppercase tracking-wider font-semibold">Will load live group tours from the database on the frontend</p>
       </div>
    </div>
  </div>
</section>`,
});

bm.add('safaris-start-planning', {
    label: '📋 Start Planning',
    category: window.__('Safaris Blocks'),
    content: `
<section class="request-section" id="contact">
  <div class="request-inner">
    <div>
      <div class="section-label">Start Planning</div>
      <h2 class="section-title">Let's Design Your<br><em>Dream Safari</em></h2>
      <p class="section-sub">Tell us your travel dates, group size, and interests — our Tanzania experts will craft a bespoke itinerary just for you. No cost, no obligation.</p>

      <ul class="why-list mt-8 text-text-mid">
        <li>
          <div class="why-check bg-earth-mid/10 border-earth-mid/25 text-earth-mid">✓</div>
          All trips 100% customisable to your pace &amp; budget
        </li>
        <li>
          <div class="why-check bg-earth-mid/10 border-earth-mid/25 text-earth-mid">✓</div>
          Response within 24 hours from our local team
        </li>
        <li>
          <div class="why-check bg-earth-mid/10 border-earth-mid/25 text-earth-mid">✓</div>
          Best price guarantee &amp; transparent pricing
        </li>
      </ul>
    </div>

    <form id="start-planning-form" class="request-form">
      <div class="mb-6">
        <div class="section-label mb-1.5 text-earth-mid">Free Quote</div>
        <h3 class="font-brand text-2xl font-normal text-earth">Request a Proposal</h3>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Adults</label>
          <input type="number" name="adults" min="1" max="50" value="1" required>
        </div>
        <div class="form-group">
          <label>Children</label>
          <input type="number" name="children" min="0" max="50" value="0" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Arrival Date</label>
          <input type="date" name="arrival_date" required>
        </div>
        <div class="form-group">
          <label>Trip Type</label>
          <select name="trip_type" class="dynamic-tour-types" required>
            <option>Loading...</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Your Email</label>
        <input type="email" name="email" placeholder="you@example.com" required>
      </div>
      <button type="submit" class="btn-primary w-full text-center block border-none cursor-pointer text-base">Get My Free Proposal →</button>
      <p class="form-note">No strings attached. We'll design your trip at no cost or commitment.</p>
    </form>
  </div>
</section>
    `,
});

bm.add('safaris-start-planning-btn', {
    label: '📋 Start Planning Button',
    category: window.__('Safaris Blocks'),
    content: `
      <div class="p-5 text-center">
        <button data-gjs-type="text" class="btn-primary start-planning-btn-trigger bg-brand-primary text-earth px-8 py-3 rounded-[3px] text-xs font-semibold tracking-[0.1em] uppercase cursor-pointer no-underline inline-block transition-all duration-250 border-none shadow-[var(--shadow-gold)]">
            Start Planning
        </button>
      </div>
    `,
});

bm.add('safaris-hero', {
    label: '🌟 Hero Section',
    category: window.__('Safaris Blocks'),
    content: { type: 'safaris-hero-component' }
});

bm.add('safaris-trust-bar', {
    label: '🛡️ Trust Bar',
    category: window.__('Safaris Blocks'),
    content: `
<div class="trust-bar" id="trust">
    <div class="trust-item">
        <span class="trust-stars">★★★★★</span>
        <span><strong>4.9/5</strong> on TripAdvisor - 1,200+ reviews</span>
    </div>
    <div class="trust-item">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        <span>100% Tailor-Made Trips</span>
    </div>
    <div class="trust-item">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        <span>Local Guides & Experts</span>
    </div>
    <div class="trust-item">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span>Free Quote — No Commitment</span>
    </div>
</div>`,
});

bm.add('safaris-features', {
    label: '✨ Why Us',
    category: window.__('Safaris Blocks'),
    content: `
<section class="why-section py-20" id="why">
  <div class="why-inner">
    <div class="why-img-wrap">
      <img class="why-img" src="https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800&q=80" alt="Safari guide with guests">
      <div class="why-img-badge">
        <div class="big">15+</div>
        <div class="small">Years of Experience</div>
      </div>
    </div>
    <div class="why-content">
      <div class="section-label">Why Jairo's Adventure</div>
      <h2 class="section-title">The Expert With<br><em>In-Depth Knowledge</em></h2>
      <p class="section-sub" style="color: rgba(255,255,255,0.6);">Born and raised in Tanzania, our team brings unmatched local knowledge, personal connections, and genuine passion for sharing this incredible country with the world.</p>
      <ul class="why-list">
        <li><div class="why-check">✓</div>100% specialized in Tanzania travel — nothing else</li>
        <li><div class="why-check">✓</div>Private safaris with dedicated vehicles &amp; expert guides</li>
        <li><div class="why-check">✓</div>Travel advisors who actually live in Tanzania</li>
        <li><div class="why-check">✓</div>Personalized service from first enquiry to return home</li>
        <li><div class="why-check">✓</div>Approachable team, fast responses via WhatsApp &amp; email</li>
        <li><div class="why-check">✓</div>Certified ethical and responsible travel practices</li>
      </ul>
      <div style="margin-top: 2.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="#contact" class="btn-primary">Request a Proposal</a>
        <a href="/tours" class="btn-ghost">Browse Itineraries</a>
      </div>
    </div>
  </div>
</section>`,
});

bm.add('safaris-about-story', {
    label: '📖 About Story',
    category: window.__('Safaris Blocks'),
    content: `
<section class="py-20 bg-white overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-16 items-center">
      <div class="w-full lg:w-1/2 relative">
        <div class="absolute -inset-4 bg-amber-100 rounded-[2rem] transform -rotate-3 z-0"></div>
        <img src="https://images.unsplash.com/photo-1534430480872-3498386e7856?auto=format&fit=crop&w=800&q=80" alt="Safari Experience" class="relative z-10 w-full h-[500px] object-cover rounded-[2rem] shadow-2xl">
        <div class="absolute -bottom-8 -right-8 bg-white p-6 rounded-[3px] shadow-xl z-20 hidden md:block">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Trusted by</p>
              <p class="text-xl font-bold font-brand text-gray-900">5,000+ Travelers</p>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full lg:w-1/2">
        <span class="inline-block py-1 px-3 rounded-full bg-amber-100 text-amber-700 text-sm font-bold tracking-widest uppercase mb-4">Your Journey, Our Passion</span>
        <h2 class="text-4xl md:text-5xl font-bold font-brand text-gray-900 mb-6 leading-tight">From a Local Dream to a Global Connection</h2>
        <p class="text-lg text-gray-600 mb-6 leading-relaxed">We believe going on a safari in Tanzania isn’t just about taking pictures. It’s about discovering stories that stay with you long after your journey ends.</p>
        <p class="text-lg text-gray-600 mb-8 leading-relaxed">  was born with a simple idea: to give you the real Tanzania, unfiltered, immersive and full of heart. Growing up surrounded by diverse wildlife and mountains, our founder knew he wanted to share his country with the world. Today, we are trusted by travelers globally, but our roots, values, and passion remain the same.</p>
        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-gray-100">
          <div>
            <h4 class="font-bold text-gray-900 mb-2">Our Mission</h4>
            <p class="text-sm text-gray-600">To design meaningful travel experiences that show Tanzania’s natural wonders while empowering local communities.</p>
          </div>
          <div>
            <h4 class="font-bold text-gray-900 mb-2">Our Vision</h4>
            <p class="text-sm text-gray-600">To become East Africa’s most trusted and responsible travel partner, leaving a positive impact.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>`,
});

bm.add('safaris-kilimanjaro-group-intro', {
    label: '🏔️ Kili Groups Intro',
    category: window.__('Safaris Blocks'),
    content: `
<section class="relative w-full py-28 flex items-center justify-center overflow-hidden bg-gray-900 text-white">
  <!-- Background Image with overlay -->
  <img src="https://images.unsplash.com/photo-1549880181-56a44cf4a9a5?auto=format&fit=crop&w=1920&q=80" class="absolute inset-0 w-full h-full object-cover opacity-40" />
  <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/50 to-transparent"></div>
  
  <!-- Content overlay -->
  <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
    <span class="inline-block py-1.5 px-4 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-bold tracking-widest uppercase mb-6">Kilimanjaro Group Climbs</span>
    <h1 class="text-4xl md:text-6xl font-bold font-brand mb-6 leading-tight text-white drop-shadow-lg animate-fade-in">Join Our Kilimanjaro Group Climbs</h1>
    <p class="text-base md:text-lg text-gray-250 mb-10 max-w-3xl mx-auto leading-relaxed drop-shadow">
      Kilimanjaro group climbs depart regularly throughout the month. Joining one of these climbs is a wonderful way to experience the grandeur of Africa’s highest peak while connecting with fellow adventurers from around the world. Securing your spot is simple: a 20% deposit is all that’s required. If none of the listed dates align with your plans, simply reach out to arrange a custom departure.
    </p>
    
    <div class="flex justify-center">
      <a href="#quote-form" class="inline-flex bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold py-3.5 px-8 rounded-[3px] transition-all duration-300 text-base shadow-xl hover:-translate-y-1">
        Get Your Custom Proposal
      </a>
    </div>
  </div>
</section>`,
});

bm.add('safaris-booking-steps', {
    label: '📋 How to Book',
    category: window.__('Safaris Blocks'),
    content: `
<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-[#f0f7eb] dark:bg-gray-900/30 rounded-[2rem] p-12 md:p-16 border border-lime-100/30 dark:border-gray-800 relative">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold font-brand text-gray-900 dark:text-white mb-3 leading-tight">How to Book Your Kilimanjaro Climb</h2>
        <p class="text-xs md:text-sm font-semibold tracking-widest text-[#72b02a] dark:text-[#88d631] uppercase">Simple steps to secure your spot on the mountain</p>
      </div>

      <!-- Timeline Steps Wrapper -->
      <div class="relative">
        <!-- Connecting Line for desktop -->
        <div class="absolute top-5 left-[12%] right-[12%] h-[2px] bg-lime-300 dark:bg-lime-900 hidden md:block z-0"></div>

        <!-- Steps Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-4 relative z-10">
          
          <!-- Step 1 -->
          <div class="flex flex-col items-center text-center group">
            <div class="relative flex items-center justify-center mb-6">
              <div class="absolute inset-0 rounded-full border border-lime-300 dark:border-lime-700/60 scale-125 transition-transform duration-300 group-hover:scale-135"></div>
              <div class="w-10 h-10 rounded-full bg-[#72b02a] text-white flex items-center justify-center font-bold text-sm border-2 border-white shadow-md z-10 relative">
                1
              </div>
            </div>
            <p class="text-sm font-semibold text-gray-750 dark:text-gray-300 max-w-[200px] leading-relaxed">
              Choose your trip and departure date
            </p>
          </div>

          <!-- Step 2 -->
          <div class="flex flex-col items-center text-center group">
            <div class="relative flex items-center justify-center mb-6">
              <div class="absolute inset-0 rounded-full border border-lime-300 dark:border-lime-700/60 scale-125 transition-transform duration-300 group-hover:scale-135"></div>
              <div class="w-10 h-10 rounded-full bg-[#72b02a] text-white flex items-center justify-center font-bold text-sm border-2 border-white shadow-md z-10 relative">
                2
              </div>
            </div>
            <p class="text-sm font-semibold text-gray-750 dark:text-gray-300 max-w-[200px] leading-relaxed">
              Confirm your spot with a small 10% deposit
            </p>
          </div>

          <!-- Step 3 -->
          <div class="flex flex-col items-center text-center group">
            <div class="relative flex items-center justify-center mb-6">
              <div class="absolute inset-0 rounded-full border border-lime-300 dark:border-lime-700/60 scale-125 transition-transform duration-300 group-hover:scale-135"></div>
              <div class="w-10 h-10 rounded-full bg-[#72b02a] text-white flex items-center justify-center font-bold text-sm border-2 border-white shadow-md z-10 relative">
                3
              </div>
            </div>
            <p class="text-sm font-semibold text-gray-750 dark:text-gray-300 max-w-[200px] leading-relaxed">
              Receive your confirmation and travel details
            </p>
          </div>

          <!-- Step 4 -->
          <div class="flex flex-col items-center text-center group">
            <div class="relative flex items-center justify-center mb-6">
              <div class="absolute inset-0 rounded-full border border-lime-300 dark:border-lime-700/60 scale-125 transition-transform duration-300 group-hover:scale-135"></div>
              <div class="w-10 h-10 rounded-full bg-[#72b02a] text-white flex items-center justify-center font-bold text-sm border-2 border-white shadow-md z-10 relative">
                4
              </div>
            </div>
            <p class="text-sm font-semibold text-gray-750 dark:text-gray-300 max-w-[200px] leading-relaxed">
              Meet your group in Moshi/Arusha and start your trek
            </p>
          </div>

        </div>
      </div>
      
      <!-- Bottom decorative line -->
      <div class="mt-16 flex justify-center">
        <div class="w-20 h-0.5 bg-lime-300 dark:bg-lime-800 rounded-full"></div>
      </div>
    </div>
  </div>
</section>`,
});

bm.add('safaris-about-values', {
    label: '💎 Core Values',
    category: window.__('Safaris Blocks'),
    content: `
<section class="py-20 bg-gray-900 text-white relative overflow-hidden">
  <div class="absolute inset-0 bg-amber-500/5"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <span class="inline-block py-1 px-3 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 text-sm font-bold tracking-widest uppercase mb-4">What Drives Us</span>
      <h2 class="text-4xl md:text-5xl font-bold font-brand mb-4">Our Core Values</h2>
      <p class="text-gray-400 max-w-2xl mx-auto text-lg">The principles that guide every safari, trek, and connection we make.</p>
    </div>
    
    <div class="flex flex-wrap justify-center items-center gap-8">
      <!-- Value 1 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-amber-500/20 text-amber-400 rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Human Connection</h3>
        <p class="text-gray-400 leading-relaxed">Travel isn’t about buses or itineraries. It’s about people. We treat every guest like family and every community with respect.</p>
      </div>
      
      <!-- Value 2 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-amber-500/20 text-amber-400 rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Authenticity</h3>
        <p class="text-gray-400 leading-relaxed">We take you beyond the postcards to the real, raw, and beautiful heart of Tanzania.</p>
      </div>

      <!-- Value 3 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-amber-500/20 text-amber-400 rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Integrity</h3>
        <p class="text-gray-400 leading-relaxed">No hidden costs. No false promises. Just honest, transparent, and thoughtful service.</p>
      </div>

      <!-- Value 4 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-amber-500/20 text-amber-400 rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Excellence</h3>
        <p class="text-gray-400 leading-relaxed">From safari vehicles to mountain gear, everything we offer is carefully selected for comfort, safety, and quality.</p>
      </div>

      <!-- Value 5 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-amber-500/20 text-amber-400 rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Sustainability</h3>
        <p class="text-gray-400 leading-relaxed">We support conservation and eco-friendly practices that ensure tourism benefits everyone.</p>
      </div>
    </div>
  </div>
</section>`,
});

bm.add('safaris-contact-section', {
    label: '📞 Contact Details',
    category: window.__('Safaris Blocks'),
    content: `
<section class="py-20 bg-gray-50 overflow-hidden relative">
  <div class="absolute inset-0 bg-white/50 backdrop-blur-3xl"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <span class="inline-block py-1 px-3 rounded-full bg-amber-100 text-amber-700 text-sm font-bold tracking-widest uppercase mb-4">Contact Us</span>
      <h2 class="text-4xl md:text-5xl font-bold font-brand text-gray-900 mb-4">Let Plan Together your Safari</h2>
      <p class="text-gray-600 max-w-2xl mx-auto text-lg leading-relaxed">Every unforgettable journey begins with a single step. Reach out to us today and take the first step toward an experience you'll remember for a lifetime.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
      <!-- Contact Details -->
      <div>
        <h3 class="text-2xl font-bold font-brand text-gray-900 mb-8">Contact Information</h3>
        <div class="space-y-8">
          
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-[3px] flex items-center justify-center shrink-0">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div>
              <h4 class="text-lg font-bold text-gray-900 mb-1">Physical Address</h4>
              <p class="text-gray-600 leading-relaxed">Boman street, Near Clock Tower,<br>Arusha - NIC Building 1st Floor, Tanzania</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-[3px] flex items-center justify-center shrink-0">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            </div>
            <div>
              <h4 class="text-lg font-bold text-gray-900 mb-1">Call Us Today</h4>
              <p class="text-gray-600 leading-relaxed mb-2">Vienna (Tour Consultant): +255 685 000 070</p>
              <p class="text-gray-600 leading-relaxed mb-2">Salome (Local Guide): +255 685 000 070</p>
              <p class="text-gray-600 leading-relaxed">Administration: +255 685 000 070</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-[3px] flex items-center justify-center shrink-0">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <h4 class="text-lg font-bold text-gray-900 mb-1">Email Address</h4>
              <p class="text-gray-600 leading-relaxed">Contact us for a detailed quote.</p>
              <a href="mailto:info@safarisadventure.com" class="text-amber-600 font-bold hover:text-amber-700">info@safarisadventure.com</a>
            </div>
          </div>
          
        </div>
      </div>

      <!-- Contact Form -->
      <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100">
        <h3 class="text-2xl font-bold font-brand text-gray-900 mb-6">Enquire Now</h3>
        <form class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
              <input type="text" class="w-full px-4 py-3 rounded-[3px] border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="John Doe">
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
              <input type="email" class="w-full px-4 py-3 rounded-[3px] border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="john@example.com">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
              <input type="tel" class="w-full px-4 py-3 rounded-[3px] border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="+1 234 567 890">
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Subject</label>
              <input type="text" class="w-full px-4 py-3 rounded-[3px] border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="Safari Inquiry">
            </div>
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Your Message</label>
            <textarea rows="4" class="w-full px-4 py-3 rounded-[3px] border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="Tell us about your dream safari..."></textarea>
          </div>
          <button type="button" class="w-full py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-[3px] transition-all shadow-lg hover:shadow-amber-500/30 transform hover:-translate-y-0.5">Send Message</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Google Map Full Width -->
  <div class="w-full h-[450px] mt-16 relative z-10">
    <iframe src="https://maps.google.com/maps?q=Safaris%20Adventure%20Arusha&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</section>`,
});


// ── Destinations Block ──────────────────────────────────────────────
editor.DomComponents.addType('destinations-listing', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: {
                class: 'destinations-listing',
                'data-limit': '6'
            },
            traits: [
                {
                    type: 'select',
                    name: 'data-country-id',
                    label: 'Filter by Country',
                    options: [
                        { value: '', name: 'All Countries' },
                        ...(window.EDITOR_COUNTRIES_DATA || []).map(c => ({ value: c.id.toString(), name: c.name }))
                    ]
                },
                {
                    type: 'number',
                    name: 'data-limit',
                    label: 'Number of Destinations',
                    min: 1,
                    max: 20
                }
            ],
        },
        init() {
            this.on('change:attributes:data-limit', this.updateCanvasHtml);
            this.updateCanvasHtml();
        },
        updateCanvasHtml() {
            const limit = this.getAttributes()['data-limit'] || 6;
            const countryId = this.getAttributes()['data-country-id'] || '';
            const countryName = countryId ? ((window.EDITOR_COUNTRIES_DATA || []).find(c => c.id == countryId)?.name || 'a Specific Country') : 'All Countries';
            this.set('content', `
               <div class="text-center py-12 bg-gray-50 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
                  <div class="w-16 h-16 mx-auto mb-4 bg-brand-light rounded-full flex items-center justify-center">
                     <i class="fa-solid fa-map-location-dot text-2xl text-brand-primary"></i>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 font-brand">Destinations Block</h3>
                  <p class="text-sm text-gray-500 mt-2">Displaying <span class="font-bold text-brand-primary">${limit}</span> Destinations from <span class="font-bold text-brand-primary">${countryName}</span></p>
                  <p class="text-xs text-gray-400 mt-4 max-w-md mx-auto">This block will render the iconic destinations dynamically based on the database.</p>
               </div>
            `);
        }
    }
});

bm.add('safaris-header', {
    label: '🏷️ Heading',
    category: window.__('Safaris Blocks'),
    content: `
        <h2 class="text-[25px] font-bold font-brand text-gray-900 dark:text-white mb-6 text-center">Your Heading Here</h2>
    `
});

bm.add('safaris-text', {
    label: '📝 Text Paragraph',
    category: window.__('Safaris Blocks'),
    content: `
        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6">Your text here. This block is perfect for paragraphs, descriptions, or any standard text content on your page.</p>
    `
});

bm.add('safaris-image', {
    label: '🖼️ Image',
    category: window.__('Safaris Blocks'),
    content: `
        <img src="https://placehold.co/800x500/f8fafc/475569?text=Double+Click+to+Change+Image" alt="Placeholder" class="w-full h-auto rounded-[3px] shadow-sm mb-6" />
    `
});

bm.add('safaris-list', {
    label: '📋 Bullet List',
    category: window.__('Safaris Blocks'),
    content: `
        <ul class="list-disc text-gray-700 dark:text-gray-300 leading-relaxed mb-6 space-y-2 ml-6">
            <li>First list item</li>
            <li>Second list item</li>
            <li>Third list item</li>
        </ul>
    `
});

bm.add('destinations-block', {
    label: '🏔️ Destinations Block',
    category: window.__('Safaris Blocks'),
    content: `
    <section class="dest-section" id="destinations">
      <div class="dest-inner">
        <div class="section-label">Iconic Destinations</div>
        <h2 class="section-title">Tanzania's Greatest <em>Wonders</em></h2>
        
        <div data-gjs-type="destinations-listing" class="destinations-listing" data-limit="6" data-country-id="">
           <div class="text-center py-12 bg-gray-50 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
              <div class="w-16 h-16 mx-auto mb-4 bg-brand-light rounded-full flex items-center justify-center">
                 <i class="fa-solid fa-map-location-dot text-2xl text-brand-primary"></i>
              </div>
              <h3 class="text-xl font-bold text-gray-900 font-brand">Destinations Grid</h3>
              <p class="text-sm text-gray-500 mt-2">Displaying <span class="font-bold text-brand-primary">6</span> Featured Destinations</p>
              <p class="text-xs text-gray-400 mt-4 max-w-md mx-auto">This area will be replaced with the dynamic grid of destinations on the live site.</p>
           </div>
        </div>
      </div>
    </section>
    `,
    attributes: { class: 'fa-solid fa-map' }
});


// ── Full Tour Page Block ──────────────────────────────────────────
editor.DomComponents.addType('safaris-tour-page-block', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: {
                class: 'safaris-tour-page-block',
                'data-country': 'all',
                'data-category': 'all',
                'data-tag': 'all',
                'data-type': 'all',
                'data-limit': '12'
            },
            traits: [
                {
                    type: 'select',
                    name: 'data-country',
                    label: 'Country Filter',
                    options: [
                        { value: 'all', name: window.__('All Countries') },
                        ...(window.COUNTRIES || []).map(c => ({ value: c.id, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-category',
                    label: 'Category Filter',
                    options: [
                        { value: 'all', name: window.__('All Categories') },
                        ...(window.TOUR_CATEGORIES || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-tag',
                    label: 'Tag Filter',
                    options: [
                        { value: 'all', name: window.__('All Tags') },
                        ...(window.TOUR_TAGS || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-type',
                    label: 'Tour Type Filter',
                    options: [
                        { value: 'all', name: window.__('All Types') },
                        ...(window.TOUR_TYPES || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'number',
                    name: 'data-limit',
                    label: 'Tours Per Page',
                    min: 1,
                    max: 100,
                    value: 12
                }
            ],
        },
        init() {
            this.on('change:attributes:data-country', this.updateCanvasHtml);
            this.on('change:attributes:data-category', this.updateCanvasHtml);
            this.on('change:attributes:data-tag', this.updateCanvasHtml);
            this.on('change:attributes:data-type', this.updateCanvasHtml);
            this.on('change:attributes:data-limit', this.updateCanvasHtml);
            this.updateCanvasHtml();
        },
        updateCanvasHtml() {
            const country = this.getAttributes()['data-country'] || 'all';
            const category = this.getAttributes()['data-category'] || 'all';
            const tag = this.getAttributes()['data-tag'] || 'all';
            const type = this.getAttributes()['data-type'] || 'all';
            const limit = this.getAttributes()['data-limit'] || '12';

            this.set('content', `
               <div class="text-center py-12 bg-gray-50 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
                  <div class="w-16 h-16 mx-auto mb-4 bg-brand-light rounded-full flex items-center justify-center">
                     <i class="fa-solid fa-map-location-dot text-2xl text-brand-primary"></i>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 font-brand">Full Tour Directory Page</h3>
                  <p class="text-sm text-gray-500 mt-2">Country: <span class="font-bold text-brand-primary">${country}</span> | Cat: <span class="font-bold text-brand-primary">${category}</span> | Tag: <span class="font-bold text-brand-primary">${tag}</span> | Type: <span class="font-bold text-brand-primary">${type}</span> | Per Page: <span class="font-bold text-brand-primary">${limit}</span></p>
                  <p class="text-xs text-gray-400 mt-4 max-w-md mx-auto">This block will render the entire interactive tour searching experience with sidebars, sliders, and live results.</p>
               </div>
            `);
        }
    }
});

bm.add('safaris-tour-page', {
    label: '🌍 Tour Page Block',
    category: window.__('Safaris Blocks'),
    content: `
    <section class="py-10 bg-gray-50 dark:bg-gray-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-8">
        <h2 class="text-3xl md:text-5xl font-bold font-brand text-gray-900 dark:text-white mb-4">Explore Our Safaris &amp; Tours</h2>
        <p class="text-gray-600 dark:text-gray-300 text-base md:text-lg max-w-3xl mx-auto">Discover unforgettable adventures across Tanzania and East Africa.</p>
      </div>
      <div data-gjs-type="safaris-tour-page-block" class="safaris-tour-page-block" data-country="all" data-category="all" data-tag="all" data-type="all" data-limit="12">
         <div class="text-center py-12 bg-white dark:bg-gray-900 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
            <div class="w-16 h-16 mx-auto mb-4 bg-brand-light rounded-full flex items-center justify-center">
               <i class="fa-solid fa-map-location-dot text-2xl text-brand-primary"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 font-brand">Full Tour Directory Page</h3>
            <p class="text-sm text-gray-500 mt-2">Country: <span class="font-bold text-brand-primary">all</span> | Cat: <span class="font-bold text-brand-primary">all</span> | Tag: <span class="font-bold text-brand-primary">all</span> | Type: <span class="font-bold text-brand-primary">all</span> | Per Page: <span class="font-bold text-brand-primary">12</span></p>
            <p class="text-xs text-gray-400 mt-4 max-w-md mx-auto">This block will render the entire interactive tour searching experience with sidebars, sliders, and live results.</p>
         </div>
      </div>
    </section>
    `,
    attributes: { class: 'fa-solid fa-globe' }
});

// ── Full Hiking Tour Page Block ──────────────────────────────────────────
editor.DomComponents.addType('safaris-hiking-tour-page-block', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: {
                class: 'safaris-hiking-tour-page-block',
                'data-country': 'all',
                'data-route': 'all',
                'data-type': 'all',
                'data-limit': '12'
            },
            traits: [
                {
                    type: 'select',
                    name: 'data-country',
                    label: 'Country Filter',
                    options: [
                        { value: 'all', name: window.__('All Countries') },
                        ...(window.COUNTRIES || []).map(c => ({ value: c.id, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'multiselect',
                    name: 'data-route',
                    label: 'Route Filter',
                    options: [
                        { value: 'all', name: window.__('All Routes') },
                        ...(window.TOUR_ROUTES || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-type',
                    label: 'Tour Type Filter',
                    options: [
                        { value: 'all', name: window.__('All Types') },
                        ...(window.TOUR_TYPES || []).map(c => ({ value: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'number',
                    name: 'data-limit',
                    label: 'Tours Per Page',
                    min: 1,
                    max: 100,
                    value: 12
                }
            ],
        },
        init() {
            this.on('change:attributes:data-country', this.updateCanvasHtml);
            this.on('change:attributes:data-route', this.updateCanvasHtml);
            this.on('change:attributes:data-type', this.updateCanvasHtml);
            this.on('change:attributes:data-limit', this.updateCanvasHtml);
            this.updateCanvasHtml();
        },
        updateCanvasHtml() {
            const country = this.getAttributes()['data-country'] || 'all';
            const route = this.getAttributes()['data-route'] || 'all';
            const type = this.getAttributes()['data-type'] || 'all';
            const limit = this.getAttributes()['data-limit'] || '12';

            this.set('content', `
               <div class="text-center py-12 bg-gray-50 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
                  <div class="w-16 h-16 mx-auto mb-4 bg-brand-light rounded-full flex items-center justify-center">
                     <i class="fa-solid fa-mountain text-2xl text-brand-primary"></i>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 font-brand">Full Hiking Tour Directory Page</h3>
                  <p class="text-sm text-gray-500 mt-2">Country: <span class="font-bold text-brand-primary">${country}</span> | Route: <span class="font-bold text-brand-primary">${route}</span> | Type: <span class="font-bold text-brand-primary">${type}</span> | Per Page: <span class="font-bold text-brand-primary">${limit}</span></p>
                  <p class="text-xs text-gray-400 mt-4 max-w-md mx-auto">This block will render the entire interactive hiking tour searching experience with sidebars, sliders, and live results.</p>
               </div>
            `);
        }
    }
});

bm.add('safaris-hiking-tour-page', {
    label: '🏔️ Hiking Tour Page Block',
    category: window.__('Safaris Blocks'),
    content: `
    <section class="py-10 bg-gray-50 dark:bg-gray-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-8">
        <h2 class="text-3xl md:text-5xl font-bold font-brand text-gray-900 dark:text-white mb-4">Kilimanjaro &amp; Mountain Trekking Routes</h2>
        <p class="text-gray-600 dark:text-gray-300 text-base md:text-lg max-w-3xl mx-auto">Choose your route and start your summit ascent with our expert mountain guides.</p>
      </div>
      <div data-gjs-type="safaris-hiking-tour-page-block" class="safaris-hiking-tour-page-block" data-country="all" data-route="all" data-type="all" data-limit="12">
         <div class="text-center py-12 bg-white dark:bg-gray-900 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
            <div class="w-16 h-16 mx-auto mb-4 bg-brand-light rounded-full flex items-center justify-center">
               <i class="fa-solid fa-mountain text-2xl text-brand-primary"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 font-brand">Full Hiking Tour Directory Page</h3>
            <p class="text-sm text-gray-500 mt-2">Country: <span class="font-bold text-brand-primary">all</span> | Route: <span class="font-bold text-brand-primary">all</span> | Type: <span class="font-bold text-brand-primary">all</span> | Per Page: <span class="font-bold text-brand-primary">12</span></p>
            <p class="text-xs text-gray-400 mt-4 max-w-md mx-auto">This block will render the entire interactive hiking tour searching experience with sidebars, sliders, and live results.</p>
         </div>
      </div>
    </section>
    `,
    attributes: { class: 'fa-solid fa-mountain' }
});

// ── Quick Nav (Posts) Block ──────────────────────────────────────────
editor.DomComponents.addType('safaris-quick-nav-block', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: {
                class: 'safaris-quick-nav-block',
                'data-post-category': 'all',
                'data-target': '_self'
            },
            traits: [
                {
                    type: 'select',
                    name: 'data-post-category',
                    label: 'Post Category',
                    options: [
                        { id: 'all', name: window.__('All Categories') },
                        ...(window.POST_CATEGORIES || []).map(c => ({ id: c.slug, name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-target',
                    label: 'Open Links In',
                    options: [
                        { id: '_self', name: 'Same Window' },
                        { id: '_blank', name: 'New Window' }
                    ]
                }
            ],
        },
        init() {
            this.on('change:attributes:data-post-category', this.updateCanvasHtml);
            this.on('change:attributes:data-target', this.updateCanvasHtml);
            this.updateCanvasHtml();
        },
        updateCanvasHtml() {
            const category = this.getAttributes()['data-post-category'] || 'all';
            const target = this.getAttributes()['data-target'] || '_self';
            this.set('content', `
               <div class="text-center py-8 bg-gray-50 border-2 border-dashed border-brand-primary rounded-[3px] p-6 m-4" style="pointer-events: none;">
                  <div class="w-12 h-12 mx-auto mb-3 bg-brand-light rounded-full flex items-center justify-center">
                     <i class="fa-solid fa-list-ul text-xl text-brand-primary"></i>
                  </div>
                  <h3 class="text-lg font-bold text-gray-900 font-brand">Quick Nav (Posts)</h3>
                  <p class="text-xs text-gray-500 mt-2">Category: <span class="font-bold text-brand-primary">${category}</span> | Target: <span class="font-bold text-brand-primary">${target}</span></p>
               </div>
            `);
        }
    }
});

// ── Load existing content from API ────────────────────────────────────────────
window.CURRENT_LOCALE = 'en';

window.switchLocale = async function(locale) {
    if (locale === window.CURRENT_LOCALE) return;
    if (!confirm(`Switch editing language to ${locale.toUpperCase()}? Make sure you have saved any changes to ${window.CURRENT_LOCALE.toUpperCase()} first.`)) {
        return;
    }
    window.CURRENT_LOCALE = locale;
    document.querySelectorAll('.lang-tab-btn').forEach(btn => {
        const isCurrent = btn.getAttribute('data-locale') === locale;
        btn.classList.toggle('active', isCurrent);
        btn.style.background = isCurrent ? '#6366f1' : 'transparent';
        btn.style.color = isCurrent ? '#fff' : '#94a3b8';
    });
    showToast(`Loading ${locale.toUpperCase()} translation...`, 'success');
    await loadContent(locale);
};

async function loadContent(locale = window.CURRENT_LOCALE) {
    try {
        const res = await fetch(`/admin/api/${window.RECORD_TYPE}/${window.RECORD_ID}/content?locale=${locale}&t=${Date.now()}`, {
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.builder_data && data.builder_data.components) {
            editor.setComponents(data.builder_data.components);
            if (data.builder_data.styles) {
                editor.setStyle(data.builder_data.styles);
            }
        } else if (data.builder_html) {
            // Fallback to HTML if JSON data is not present (e.g. freshly seeded)
            editor.setComponents(data.builder_html);
        } else {
            editor.setComponents('');
        }
    } catch (e) {
        console.warn('No existing content to load:', e);
    }
}

loadContent();

// ── Device toggle ─────────────────────────────────────────────────────────────
window.setDevice = function(device) {
    const deviceMap = { desktop: 'Desktop', tablet: 'Tablet', mobile: 'Mobile portrait' };
    editor.setDevice(deviceMap[device] || 'Desktop');
    document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(`btn-${device}`)?.classList.add('active');
};

// ── Preview ───────────────────────────────────────────────────────────────────
window.previewPage = function() {
    editor.runCommand('preview');
};

// ── Save ──────────────────────────────────────────────────────────────────────
window.savePage = async function() {
    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    try {
        const html = editor.getHtml();
        const css  = editor.getCss();
        const components = editor.getComponents().toJSON();
        const styles     = editor.getStyle().toJSON();

        const payloadHtml = `<style>${css}</style>${html}`;
        const payloadData = JSON.stringify({ components, styles });

        const res = await fetch(`/admin/api/${window.RECORD_TYPE}/${window.RECORD_ID}/content?locale=${window.CURRENT_LOCALE}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                html_base64: btoa(unescape(encodeURIComponent(payloadHtml))),
                data_base64: btoa(unescape(encodeURIComponent(payloadData))),
            }),
        });

        const result = await res.json();

        if (result.success) {
            showToast(`✓ Saved (${window.CURRENT_LOCALE.toUpperCase()}) successfully!`, 'success');
        } else {
            showToast('✗ Save failed. Try again.', 'error');
        }
    } catch (e) {
        showToast('✗ Network error. Try again.', 'error');
        console.error(e);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
};

// ── Toast notification ────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const toast = document.getElementById('status-toast');
    document.getElementById('toast-msg').textContent = msg;
    toast.className = type;
    setTimeout(() => { toast.className = ''; }, 3500);
}

bm.add('reviews-block', {
    label: '⭐ Reviews Block',
    category: window.__('Safaris Blocks'),
    content: `<!-- REVIEWS -->
<section class="reviews-section" id="reviews">
  <div class="reviews-inner">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <div class="section-label">What Travellers Say</div>
        <h2 class="section-title">Genuine Reviews From<br><em>Our Guests</em></h2>
      </div>
      <!-- Platform Score Badges -->
      <div style="display: flex; gap: 1.2rem; flex-wrap: wrap;">
        <!-- TripAdvisor Badge -->
        <a href="https://www.tripadvisor.com/Attraction_Review-g293747-d8660687-Reviews-Safaris_Adventure-Arusha_Arusha_Region.html" target="_blank" style="text-decoration:none; display:flex; align-items:center; gap:10px; background:#fff; border:1.5px solid #00aa6c; border-radius: 3px; padding:10px 16px; box-shadow: 0 2px 12px rgba(0,170,108,0.10);">
          <svg width="36" height="36" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="50" fill="#00AA6C"/>
            <circle cx="50" cy="50" r="28" fill="white"/>
            <circle cx="50" cy="50" r="16" fill="#00AA6C"/>
            <circle cx="28" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
            <circle cx="72" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
            <circle cx="28" cy="50" r="5" fill="#00AA6C"/>
            <circle cx="72" cy="50" r="5" fill="#00AA6C"/>
          </svg>
          <div>
            <div style="font-size:11px; font-weight:600; color:#00aa6c; letter-spacing:0.04em; text-transform:uppercase;">TripAdvisor</div>
            <div style="font-size:18px; font-weight:700; color:#1a1108; line-height:1.1;">5.0 <span style="font-size:13px; color:#00aa6c;">★★★★★</span></div>
            <div style="font-size:11px; color:#666;">800+ reviews</div>
          </div>
        </a>
        <!-- Google Badge -->
        <a href="https://www.google.com/search?q=Safaris+Adventure+Tanzania" target="_blank" style="text-decoration:none; display:flex; align-items:center; gap:10px; background:#fff; border:1.5px solid #e0e0e0; border-radius: 3px; padding:10px 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.07);">
          <svg width="36" height="36" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            <path fill="none" d="M0 0h48v48H0z"/>
          </svg>
          <div>
            <div style="font-size:11px; font-weight:600; color:#4285F4; letter-spacing:0.04em; text-transform:uppercase;">Google</div>
            <div style="font-size:18px; font-weight:700; color:#1a1108; line-height:1.1;">4.9 <span style="font-size:13px; color:#fbbc05;">★★★★★</span></div>
            <div style="font-size:11px; color:#666;">400+ reviews</div>
          </div>
        </a>
      </div>
    </div>

    <div class="reviews-grid" style="margin-top: 2.5rem;">
      <!-- TripAdvisor Review 1 -->
      <div class="review-card">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.8rem;">
          <svg width="22" height="22" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="50" fill="#00AA6C"/>
            <circle cx="50" cy="50" r="28" fill="white"/>
            <circle cx="50" cy="50" r="16" fill="#00AA6C"/>
            <circle cx="28" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
            <circle cx="72" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
            <circle cx="28" cy="50" r="5" fill="#00AA6C"/>
            <circle cx="72" cy="50" r="5" fill="#00AA6C"/>
          </svg>
          <span style="font-size:12px; font-weight:600; color:#00aa6c; letter-spacing:0.03em;">TripAdvisor</span>
        </div>
        <div class="review-stars" style="color:#00aa6c;">★★★★★</div>
        <p class="review-text">"An absolutely unforgettable honeymoon. Jairo's team handled everything — from the airport pickup to sunset game drives. Our guide Issa felt like family within minutes. Seeing the Big Five in Ngorongoro was pure magic."</p>
        <div class="review-author">
          <div class="review-avatar">MK</div>
          <div><div class="review-name">Maria &amp; Karl</div><div class="review-loc">Germany · Safari + Zanzibar, 2025</div></div>
        </div>
      </div>

      <!-- Google Review -->
      <div class="review-card">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.8rem;">
          <svg width="22" height="22" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            <path fill="none" d="M0 0h48v48H0z"/>
          </svg>
          <span style="font-size:12px; font-weight:600; color:#4285F4; letter-spacing:0.03em;">Google</span>
        </div>
        <div class="review-stars" style="color:#fbbc05;">★★★★★</div>
        <p class="review-text">"From the very first email, the service was outstanding. Our Kilimanjaro climb was tough but our guides kept spirits high the whole way. Summiting at dawn above the clouds is something I'll never forget."</p>
        <div class="review-author">
          <div class="review-avatar">JL</div>
          <div><div class="review-name">James L.</div><div class="review-loc">United Kingdom · Kili Climb, 2025</div></div>
        </div>
      </div>

      <!-- TripAdvisor Review 2 -->
      <div class="review-card">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.8rem;">
          <svg width="22" height="22" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="50" fill="#00AA6C"/>
            <circle cx="50" cy="50" r="28" fill="white"/>
            <circle cx="50" cy="50" r="16" fill="#00AA6C"/>
            <circle cx="28" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
            <circle cx="72" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
            <circle cx="28" cy="50" r="5" fill="#00AA6C"/>
            <circle cx="72" cy="50" r="5" fill="#00AA6C"/>
          </svg>
          <span style="font-size:12px; font-weight:600; color:#00aa6c; letter-spacing:0.03em;">TripAdvisor</span>
        </div>
        <div class="review-stars" style="color:#00aa6c;">★★★★★</div>
        <p class="review-text">"We traveled as a family of five with three kids. Jairo's team tailored every single detail — pace, lodges, activities. Our children saw lions, elephants, and a cheetah! This trip changed all of us."</p>
        <div class="review-author">
          <div class="review-avatar">SF</div>
          <div><div class="review-name">Sarah F.</div><div class="review-loc">USA · Family Safari, 2024</div></div>
        </div>
      </div>
    </div>

    <!-- Review platform links -->
    <div style="display:flex; gap:1rem; margin-top:2rem; flex-wrap:wrap;">
      <a href="https://www.tripadvisor.com/Attraction_Review-g293747-d8660687-Reviews-Safaris_Adventure-Arusha_Arusha_Region.html" target="_blank" style="display:inline-flex; align-items:center; gap:6px; background:#00aa6c; color:#fff; padding:9px 18px; border-radius: 3px; font-size:12px; font-weight:500; text-decoration:none; letter-spacing:0.05em; text-transform:uppercase; transition:background 0.2s;" onmouseover="this.style.background='#009960'" onmouseout="this.style.background='#00aa6c'">
        Read All TripAdvisor Reviews →
      </a>
      <a href="https://www.google.com/search?q=Safaris+Adventure+Tanzania+reviews" target="_blank" style="display:inline-flex; align-items:center; gap:6px; background:#4285F4; color:#fff; padding:9px 18px; border-radius: 3px; font-size:12px; font-weight:500; text-decoration:none; letter-spacing:0.05em; text-transform:uppercase; transition:background 0.2s;" onmouseover="this.style.background='#2b6de0'" onmouseout="this.style.background='#4285F4'">
        Read All Google Reviews →
      </a>
    </div>
  </div>
</section>`,
    attributes: { class: 'fa-solid fa-star' }
});

bm.add('explore-block', {
    label: '🔍 Discover More Block',
    category: window.__('Safaris Blocks'),
    content: `<section class="explore-section">
  <div class="explore-inner">
    <div class="section-label">Discover More</div>
    <h2 class="section-title">Get <em>Inspired</em></h2>
    <div class="explore-grid">
      <a href="#" class="explore-card" data-gjs-type="explore-card">
        <img src="https://images.unsplash.com/photo-1516426122078-c23e76319801?w=1200&q=80" alt="Great Migration">
        <div class="explore-overlay">
          <div class="explore-tag">Wildlife Event</div>
          <div class="explore-title">The Great Migration</div>
          <div class="explore-desc">Witness over 1.5 million wildebeest thunder across the Serengeti in the world's greatest natural spectacle.</div>
        </div>
      </a>
      <a href="#" class="explore-card" data-gjs-type="explore-card">
        <img src="https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=700&q=80" alt="Accommodations">
        <div class="explore-overlay">
          <div class="explore-tag">Stay</div>
          <div class="explore-title">Lodges &amp; Camps</div>
          <div class="explore-desc">From luxury tented camps to boutique lodges deep in the bush.</div>
        </div>
      </a>
      <a href="#" class="explore-card" data-gjs-type="explore-card">
        <img src="https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=700&q=80" alt="Activities">
        <div class="explore-overlay">
          <div class="explore-tag">Experiences</div>
          <div class="explore-title">Activities</div>
          <div class="explore-desc">Hot air balloons, Maasai villages, walking safaris, and more.</div>
        </div>
      </a>
    </div>
  </div>
</section>`,
    attributes: { class: 'fa-solid fa-compass' }
});

bm.add('cta-block', {
    label: '✉️ Newsletter CTA Block',
    category: window.__('Safaris Blocks'),
    content: `<section class="cta-section">
  <div class="cta-inner">
    <div class="section-label">Stay Inspired</div>
    <h2 class="section-title">Ready for the<br><em>Adventure of a Lifetime?</em></h2>
    <p class="section-sub">Join 8,000+ travellers who receive our safari inspiration, seasonal guides, and exclusive trip ideas straight to their inbox.</p>
    <form id="newsletter-form" class="cta-form" action="/newsletter/subscribe" method="POST">
      <input type="hidden" name="_token" value="${window.CSRF_TOKEN || ''}">
      <input type="text" name="website_url" style="display:none" tabindex="-1" autocomplete="off">
      <input type="email" name="email" placeholder="Your email address" required>
      <button type="submit" class="btn-primary" style="white-space:nowrap; border: none; cursor: pointer; font-family: inherit; font-size: 1rem;">Subscribe</button>
    </form>
    <p style="font-size:12px; color:rgba(255,255,255,0.35); margin-top:1rem;">No spam, ever. Unsubscribe anytime.</p>
  </div>
</section>`,
    attributes: { class: 'fa-solid fa-envelope-open-text' }
});

// ── Safaris Table Block ────────────────────────────────────────────────────────

const TH_STYLE = "padding: 14px 18px; text-align: left; color: #EC9824; font-family: 'Playfair Display', Georgia, serif; font-size: 0.85rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; border: none;";
const TD_STYLE_DARK = "padding: 14px 18px; color: #2E1F12; font-family: 'Lora', Georgia, serif; font-size: 15px;";
const TD_STYLE = "padding: 14px 18px; color: #4A3728; font-family: 'Lora', Georgia, serif; font-size: 15px;";

bm.add('safaris-table', {
    label: '📋 Table',
    category: window.__('Safaris Blocks'),
    attributes: { class: 'fa-solid fa-table' },
    content: `
<section data-gjs-name="Table Section" style="padding: 60px 20px; background: #fff;">
  <div data-gjs-name="Table Container" style="max-width: 900px; margin: 0 auto;">
    <h2 data-gjs-type="text" style="font-family: 'Playfair Display', Georgia, serif; font-size: 25px; font-weight: 700; color: #2E1F12; margin-bottom: 1.5rem;">Table Heading</h2>
    <div style="overflow-x: auto; border-radius: 6px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
      <table style="width: 100%; border-collapse: collapse; font-family: 'Lora', Georgia, serif; font-size: 15px; background: #fff;">
        <thead>
          <tr style="background: #2E1F12;">
            <th style="${TH_STYLE}"><div data-gjs-type="text">Column 1</div></th>
            <th style="${TH_STYLE}"><div data-gjs-type="text">Column 2</div></th>
            <th style="${TH_STYLE}"><div data-gjs-type="text">Column 3</div></th>
            <th style="${TH_STYLE}"><div data-gjs-type="text">Column 4</div></th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom: 1px solid #EEE5D4;">
            <td style="${TD_STYLE_DARK}"><div data-gjs-type="text">Row 1, Cell 1</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 1, Cell 2</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 1, Cell 3</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 1, Cell 4</div></td>
          </tr>
          <tr style="border-bottom: 1px solid #EEE5D4; background: #FAF6EF;">
            <td style="${TD_STYLE_DARK}"><div data-gjs-type="text">Row 2, Cell 1</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 2, Cell 2</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 2, Cell 3</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 2, Cell 4</div></td>
          </tr>
          <tr style="border-bottom: 1px solid #EEE5D4;">
            <td style="${TD_STYLE_DARK}"><div data-gjs-type="text">Row 3, Cell 1</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 3, Cell 2</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 3, Cell 3</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 3, Cell 4</div></td>
          </tr>
          <tr style="background: #FAF6EF;">
            <td style="${TD_STYLE_DARK}"><div data-gjs-type="text">Row 4, Cell 1</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 4, Cell 2</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 4, Cell 3</div></td>
            <td style="${TD_STYLE}"><div data-gjs-type="text">Row 4, Cell 4</div></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>`
});

bm.add('safaris-zanzibar-page', {
    label: '🏝️ Zanzibar Island Block',
    category: window.__('Safaris Blocks'),
    attributes: { class: 'fa-solid fa-umbrella-beach' },
    content: `<div class="safaris-zanzibar-page-block zanzibar-block">
  <!-- Sticky Top Navigation Bar (Single Tour Tab Bar Structure) -->
  <header class="zanzibar-nav">
    <div class="zanzibar-nav-row">
      <nav class="zanzibar-links">
        <a href="#zanzibar-why"><i class="fa-solid fa-circle-info"></i> <span data-gjs-type="text">Why Zanzibar</span></a>
        <a href="#zanzibar-coast"><i class="fa-solid fa-compass"></i> <span data-gjs-type="text">The Coast</span></a>
        <a href="#zanzibar-stonetown"><i class="fa-solid fa-city"></i> <span data-gjs-type="text">Stone Town</span></a>
        <a href="#zanzibar-experiences"><i class="fa-solid fa-umbrella-beach"></i> <span data-gjs-type="text">Experiences</span></a>
        <a href="#zanzibar-when"><i class="fa-solid fa-calendar-days"></i> <span data-gjs-type="text">When To Go</span></a>
        <a href="#zanzibar-stay"><i class="fa-solid fa-hotel"></i> <span data-gjs-type="text">Where To Stay</span></a>
        <a href="#zanzibar-plan" class="zanzibar-nav-cta"><i class="fa-solid fa-paper-plane"></i> <span data-gjs-type="text">Plan My Trip</span></a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="zanzibar-hero">
    <div class="wrap">
      <div class="zanzibar-hero-grid">
        <div>
          <div class="eyebrow" data-gjs-type="text">  · Tanzania &amp; Zanzibar</div>
          <h1 data-gjs-type="text">Where the savannah<br>gives way to <em>the sea.</em></h1>
          <p class="lede" data-gjs-type="text">Zanzibar is the reward at the end of a Tanzanian journey — spice-scented lanes, warm shallows, and a horizon lined with dhow sails.   builds the route that gets you there, from the plains to the shore.</p>
          <div class="zanzibar-hero-ctas">
            <a class="zanzibar-btn-primary" href="#zanzibar-plan" data-gjs-type="text">Start Planning</a>
            <a class="zanzibar-btn-ghost" href="#zanzibar-why" data-gjs-type="text">See Why Go</a>
          </div>
          <div class="zanzibar-stat-strip">
            <div><div class="zanzibar-stat-num" data-gjs-type="text">40+</div><div class="zanzibar-stat-label" data-gjs-type="text">islands in archipelago</div></div>
            <div><div class="zanzibar-stat-num" data-gjs-type="text">25</div><div class="zanzibar-stat-label" data-gjs-type="text">beaches on Unguja</div></div>
            <div><div class="zanzibar-stat-num" data-gjs-type="text">20min</div><div class="zanzibar-stat-label" data-gjs-type="text">flight from Dar</div></div>
            <div><div class="zanzibar-stat-num" data-gjs-type="text">Jun–Oct</div><div class="zanzibar-stat-label" data-gjs-type="text">prime dry season</div></div>
          </div>
        </div>
        <div>
          <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&q=80" alt="Zanzibar Dhow Sail" style="width: 100%; max-height: 520px; object-fit: cover; border-radius: 4px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); display: block;" />
        </div>
      </div>
    </div>
  </section>

  <!-- Why Zanzibar Section -->
  <section class="zanzibar-section" id="zanzibar-why">
    <div class="wrap">
      <div class="zanzibar-section-head">
        <div class="eyebrow" data-gjs-type="text">Why Zanzibar</div>
        <h2 data-gjs-type="text">Five reasons this island earns a place on every Tanzania itinerary</h2>
        <p data-gjs-type="text">Zanzibar sits at a crossroads of African, Arab and Asian trade routes, and that layered history still shapes everything from its food to its architecture.</p>
      </div>
      <div class="zanzibar-why-grid">
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">01</div>
          <div>
            <h3 data-gjs-type="text">Stone Town's living history</h3>
            <p data-gjs-type="text">A UNESCO-listed old quarter of carved doorways, palace ruins and spice-scented alleys, still very much a working town rather than a museum piece.</p>
          </div>
        </div>
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">02</div>
          <div>
            <h3 data-gjs-type="text">Beaches for every mood</h3>
            <p data-gjs-type="text">Powder-soft sand rings the island, from lively northern shores to tidal, wind-swept coves in the east and south.</p>
          </div>
        </div>
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">03</div>
          <div>
            <h3 data-gjs-type="text">A reef worth diving for</h3>
            <p data-gjs-type="text">Warm, clear water around Mnemba Atoll and beyond shelters turtles, reef sharks and dense coral gardens, all within reach of the shore.</p>
          </div>
        </div>
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">04</div>
          <div>
            <h3 data-gjs-type="text">Stays at every price point</h3>
            <p data-gjs-type="text">Family-run guesthouses sit a short drive from private-island lodges, so the same coastline can suit a honeymoon or a group trip.</p>
          </div>
        </div>
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">05</div>
          <div>
            <h3 data-gjs-type="text">The safari's natural finale</h3>
            <p data-gjs-type="text">A short hop from the northern parks or the southern reserves, Zanzibar is where a dust-and-game-drive trip ends in salt air and stillness.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- The Coast Compass Section -->
  <section class="zanzibar-section zanzibar-compass-section" id="zanzibar-coast">
    <div class="wrap">
      <div class="zanzibar-section-head">
        <div class="eyebrow" data-gjs-type="text">The Coast, Point By Point</div>
        <h2 data-gjs-type="text">Which shore of Zanzibar fits your holiday?</h2>
        <p data-gjs-type="text">Unguja, the main island, wears a different character on each side. Here's how the four coasts compare.</p>
      </div>
      <div class="zanzibar-compass-grid">
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">North</div>
          <h3 data-gjs-type="text">Nungwi &amp; Kendwa</h3>
          <p data-gjs-type="text">The island's liveliest shoreline, with tide-free water year-round, a dense run of restaurants and bars, and the easiest swimming conditions on Zanzibar.</p>
        </div>
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">East</div>
          <h3 data-gjs-type="text">Matemwe &amp; Pongwe</h3>
          <p data-gjs-type="text">A long reef runs just offshore, making this the island's best base for snorkelling and kite-surfing, though the tide pulls the sea far out twice a day.</p>
        </div>
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">South-East</div>
          <h3 data-gjs-type="text">Paje, Jambiani &amp; Bwejuu</h3>
          <p data-gjs-type="text">Steady trade winds and soft, sweeping sand draw sailors and kite-surfers to these quiet fishing villages, still among the least crowded stretches of coast.</p>
        </div>
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">South-West</div>
          <h3 data-gjs-type="text">Kizimkazi &amp; Stone Town</h3>
          <p data-gjs-type="text">Quieter and less developed, with dolphin-watching off Kizimkazi and easy access to historic Stone Town and the Menai Bay marine conservation area.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Stone Town Section -->
  <section class="zanzibar-section" id="zanzibar-stonetown">
    <div class="wrap">
      <div class="zanzibar-stone">
        <div class="zanzibar-stone-visual" style="aspect-ratio: 4/5; border-radius: 4px; overflow: hidden; position: relative;">
          <img src="https://images.unsplash.com/photo-1586861635167-e5223aadc9fe?w=1000&q=80" alt="Stone Town Historic Streets" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
        </div>
        <div class="zanzibar-stone-copy">
          <div class="eyebrow" data-gjs-type="text">Stone Town</div>
          <h2 data-gjs-type="text">The old trading capital, still very much alive</h2>
          <p data-gjs-type="text">Most visitors arrive and depart through Stone Town, the island's historic hub of commerce and culture. Its narrow, winding streets carry the fingerprints of Arab, Persian, Indian and African traders who passed through for centuries — grand doorways, courtyard houses and the crumbling remains of a Sultan's palace.</p>
          <p data-gjs-type="text">The call to prayer, market chatter and the smell of cloves and cardamom still fill the same lanes today. A night or two is usually enough to take it in before heading for the beach.</p>
          <div class="zanzibar-stone-list">
            <div><span class="mark"></span><span data-gjs-type="text">Wander the carved-door alleys and Forodhani or Darajani food markets.</span></div>
            <div><span class="mark"></span><span data-gjs-type="text">Visit the Old Fort, House of Wonders and the former Sultan's Palace.</span></div>
            <div><span class="mark"></span><span data-gjs-type="text">Take a guided walking tour to unpack the layered Swahili, Arab and colonial history.</span></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Experiences Section -->
  <section class="zanzibar-section" id="zanzibar-experiences">
    <div class="wrap">
      <div class="zanzibar-section-head">
        <div class="eyebrow" data-gjs-type="text">On The Island</div>
        <h2 data-gjs-type="text">Beyond the beach chair</h2>
        <p data-gjs-type="text">Zanzibar rewards travellers who leave the sunbed for a morning. A few excursions worth building into any itinerary.</p>
      </div>
      <div class="zanzibar-exp-grid">
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Culture</div><h3 data-gjs-type="text">Spice Farm Tour</h3><p data-gjs-type="text">Walk through working plantations and learn to recognise clove, nutmeg, vanilla and cardamom by sight and smell — the trade that gave the island its nickname.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Wildlife</div><h3 data-gjs-type="text">Jozani Forest</h3><p data-gjs-type="text">Zanzibar's only national park protects mangroves and the last patch of indigenous forest, home to the rare, endemic red colobus monkey.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">History</div><h3 data-gjs-type="text">Prison Island</h3><p data-gjs-type="text">A short boat ride from Stone Town leads to a former quarantine station, now home to giant Aldabra tortoises and a calm beach for snorkelling.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">On The Water</div><h3 data-gjs-type="text">Dhow Sunset Sail</h3><p data-gjs-type="text">Traditional wooden dhows still cruise the coast at golden hour — a slow, wind-powered way to watch the island's famous sunsets.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Underwater</div><h3 data-gjs-type="text">Mnemba Atoll Snorkelling</h3><p data-gjs-type="text">One of the archipelago's best reef systems, with clear water, healthy coral and regular sightings of turtles and reef fish.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Dining</div><h3 data-gjs-type="text">The Rock Restaurant</h3><p data-gjs-type="text">Perched on a rock in the Indian Ocean and reachable on foot at low tide or by boat at high tide — as much a landmark as a meal.</p></div>
      </div>
    </div>
  </section>

  <!-- When To Go Section -->
  <section class="zanzibar-section" id="zanzibar-when">
    <div class="wrap">
      <div class="zanzibar-section-head">
        <div class="eyebrow" data-gjs-type="text">When To Go</div>
        <h2 data-gjs-type="text">Reading Zanzibar's seasons</h2>
        <p data-gjs-type="text">Zanzibar is a year-round destination, but the calendar still shapes what your trip will feel like.</p>
      </div>
      <div class="zanzibar-season-wrap">
        <div class="zanzibar-season-row">
          <div class="zanzibar-month zanzibar-m-good" data-gjs-type="text">Jan</div>
          <div class="zanzibar-month zanzibar-m-good" data-gjs-type="text">Feb</div>
          <div class="zanzibar-month zanzibar-m-shoulder" data-gjs-type="text">Mar</div>
          <div class="zanzibar-month zanzibar-m-rain" data-gjs-type="text">Apr</div>
          <div class="zanzibar-month zanzibar-m-rain" data-gjs-type="text">May</div>
          <div class="zanzibar-month zanzibar-m-shoulder" data-gjs-type="text">Jun</div>
          <div class="zanzibar-month zanzibar-m-prime" data-gjs-type="text">Jul</div>
          <div class="zanzibar-month zanzibar-m-prime" data-gjs-type="text">Aug</div>
          <div class="zanzibar-month zanzibar-m-prime" data-gjs-type="text">Sep</div>
          <div class="zanzibar-month zanzibar-m-prime" data-gjs-type="text">Oct</div>
          <div class="zanzibar-month zanzibar-m-shoulder" data-gjs-type="text">Nov</div>
          <div class="zanzibar-month zanzibar-m-good" data-gjs-type="text">Dec</div>
        </div>
      </div>
      <div class="zanzibar-legend">
        <span><i style="background:var(--clove-bright)"></i><span data-gjs-type="text">Prime dry season, best for beaches &amp; diving</span></span>
        <span><i style="background:var(--gold)"></i><span data-gjs-type="text">Warm and reliable, slightly busier</span></span>
        <span><i style="background:var(--sand-soft); border:1px solid var(--line)"></i><span data-gjs-type="text">Shoulder season, good rates</span></span>
        <span><i style="border:1px dashed var(--line)"></i><span data-gjs-type="text">Short or long rains, quieter and cheaper</span></span>
      </div>
    </div>
  </section>

  <!-- Where To Stay Section -->
  <section class="zanzibar-section" id="zanzibar-stay" style="background:var(--sand-soft);">
    <div class="wrap">
      <div class="zanzibar-section-head">
        <div class="eyebrow" data-gjs-type="text">Where To Stay</div>
        <h2 data-gjs-type="text">Three ways to sleep on the island</h2>
        <p data-gjs-type="text">Zanzibar's accommodation spans the full range —   matches the stay to the traveller, not the other way around.</p>
      </div>
      <div class="zanzibar-stay-grid">
        <div class="zanzibar-stay-card">
          <div class="price" data-gjs-type="text">Simple</div>
          <h3 data-gjs-type="text">Beach guesthouses</h3>
          <p data-gjs-type="text">Small, family-run lodges close to the sand, often with a warm, personal welcome and easy access to nearby restaurants and bars in the evening.</p>
        </div>
        <div class="zanzibar-stay-card">
          <div class="price" data-gjs-type="text">Considered</div>
          <h3 data-gjs-type="text">Boutique beach hotels</h3>
          <p data-gjs-type="text">Mid-size properties with real character — private plunge pools, sea-facing rooms and a stronger focus on food and service without the ultra-luxury price tag.</p>
        </div>
        <div class="zanzibar-stay-card">
          <div class="price" data-gjs-type="text">Exclusive</div>
          <h3 data-gjs-type="text">Private island &amp; villa lodges</h3>
          <p data-gjs-type="text">From private-island retreats to sprawling beachfront villas, the top tier of Zanzibar stays trades scale for total seclusion.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Plan My Trip / CTA Section -->
  <section class="zanzibar-cta" id="zanzibar-plan">
    <div class="wrap">
      <div class="eyebrow" data-gjs-type="text"> </div>
      <h2 data-gjs-type="text">Let's route your Zanzibar journey</h2>
      <p data-gjs-type="text">Tell us how many days you have, whether a mainland safari comes first, and what kind of coast you're picturing. We'll sketch an itinerary built around it.</p>
      <div class="zanzibar-cta-ctas">
        <a class="zanzibar-btn-primary" href="/contact" data-gjs-type="text">Request A Proposal</a>
        <a class="zanzibar-btn-ghost" href="/contact" data-gjs-type="text">Talk To A Travel Expert</a>
      </div>
    </div>
  </section>
</div>`
});

// ── Missing Component Types Registration ───────────────────────────────
editor.DomComponents.addType('safaris-zanzibar-page-block', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: { class: 'safaris-zanzibar-page-block' }
        }
    }
});

editor.DomComponents.addType('safaris-page-header-block', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: { class: 'safaris-page-header-block' }
        }
    }
});

editor.DomComponents.addType('safaris-kili-calculator-block', {
    model: {
        defaults: {
            tagName: 'div',
            attributes: { class: 'safaris-kili-calculator-block' }
        }
    }
});


