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
            'https://fonts.googleapis.com/css2?family=Baskervville:ital@0;1&family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Indie+Flower&family=Instrument+Sans:wght@400;500;600;700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap',
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
                    } else if (opts.select && typeof opts.select === 'function') {
                        opts.select(asset, true);
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
                    } else if (opts.close && typeof opts.close === 'function') {
                        opts.close();
                    }

                    window.removeEventListener('media-picker-chosen', handler);
                }
            };

            window.addEventListener('media-picker-chosen', handler);

            const filterType = (opts.types && opts.types.includes('video')) || opts.type === 'video' ? 'video' : 'image';

            if (window.Livewire) {
                window.Livewire.dispatch('open-media-picker', {
                    callbackId: callbackId,
                    filterType: filterType,
                    multiple: false
                });
            } else {
                window.dispatchEvent(new CustomEvent('open-media-picker', {
                    detail: { callbackId: callbackId, filterType: filterType, multiple: false }
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
                This is your premium content block. You can use it to build standard article bodies, description sections, or details about your itineraries and tours. It is pre-configured with a maximum width container (<code class="text-sm font-semibold bg-[#EDF2EA] px-1.5 py-0.5 rounded text-[#455537]">max-w-7xl</code>) to align beautifully with the rest of your site layout.
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
    <section data-gjs-type="safaris-page-header-block" class="safaris-page-header-block relative w-full h-96 md:h-[480px] overflow-hidden bg-gray-900">
        <div class="dynamic-bg-container absolute inset-0 w-full h-full">
            <img src="https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&q=80" alt="Header Background" class="absolute inset-0 w-full h-full object-cover opacity-85">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-black/15 to-black/45 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <h1 class="dynamic-page-title text-4xl md:text-6xl !font-normal font-normal text-white font-brand leading-tight mb-4" style="font-weight: 400; text-shadow: 0 2px 10px rgba(0,0,0,0.6);">
                Explore Our Destinations
            </h1>
            <p class="text-white/90 text-base md:text-lg max-w-2xl" style="text-shadow: 0 1px 6px rgba(0,0,0,0.6);">
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
                        <span class="inline-block px-3 py-1 rounded-full bg-[#455537] text-white text-xs font-bold uppercase tracking-wider kili-badge" data-gjs-type="text">Mount Kilimanjaro (5,895m)</span>
                        <h3 class="text-2xl font-bold font-brand text-white kili-heading" data-gjs-type="text">Conquer the Roof of Africa</h3>
                        <p class="text-xs text-gray-200 kili-desc" data-gjs-type="text">Choosing the right route duration and acclimatization strategy can raise your summit success rate from 60% to over 95%.</p>
                    </div>
                </div>
                <!-- Right Calculator Preview -->
                <div class="lg:col-span-7 p-4 flex flex-col justify-between">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full bg-[#455537]/15 text-[#455537] text-xs font-bold uppercase tracking-wider mb-2">Summit Estimator</span>
                        <h2 class="text-2xl font-bold font-brand text-gray-900 dark:text-white kili-title" data-gjs-type="text">Kilimanjaro Success Rate Calculator</h2>
                        <p class="text-xs text-gray-500 mt-1 kili-sub" data-gjs-type="text">Estimate your summit success probability based on acclimatization and route profiles.</p>
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700 text-center">
                            <i class="fa-solid fa-calculator text-2xl text-[#CA9A2E] mb-2"></i>
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
                el.style.cssText = 'width:100%; padding:8px 12px; background:linear-gradient(135deg,#455537,#354329); color:#fff; border:none; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; letter-spacing:0.03em; transition:all .15s;';
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
                    'https://fonts.googleapis.com/css2?family=Baskervville:ital@0;1&family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Indie+Flower&family=Instrument+Sans:wght@400;500;600;700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap',
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
            fontProp.addOption({ id: 'Baskervville, serif', label: 'Baskervville' });
            fontProp.addOption({ id: 'Lora, serif', label: 'Lora' });
            fontProp.addOption({ id: '"Playfair Display", serif', label: 'Playfair Display' });
            fontProp.addOption({ id: 'Jost, sans-serif', label: 'Jost' });
            fontProp.addOption({ id: 'Merriweather, serif', label: 'Merriweather' });
            fontProp.addOption({ id: '"Indie Flower", cursive', label: 'Indie Flower' });
            fontProp.addOption({ id: 'Montserrat, sans-serif', label: 'Montserrat' });
        } else {
            const list = fontProp.get('options') || fontProp.get('list') || [];
            list.push({ value: 'Baskervville, serif', name: 'Baskervville', id: 'Baskervville, serif', label: 'Baskervville' });
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
        const traitName = trait.get('name') || '';
        const isVideo = traitName.toLowerCase().includes('video');
        const placeholder = isVideo ? 'Video URL or pick from media library...' : 'Image URL or pick from media library...';
        
        el.innerHTML = `
            <div style="display:flex; gap:6px; align-items:center; width:100%;">
                <input type="text" class="trait-input" placeholder="${placeholder}" style="flex:1; min-width:0; color:#fff; background:rgba(0,0,0,0.25); border:1px solid #475569; padding:6px 8px; border-radius:4px; font-size:12px;">
                <button class="trait-btn" type="button" title="Choose from Media Library" style="background:#2563eb; border:none; border-radius:4px; color:#fff; cursor:pointer; padding:6px 10px; font-size:11px; font-weight:600; white-space:nowrap; display:inline-flex; align-items:center; gap:4px; flex-shrink:0;">
                    <i class="fa fa-folder-open"></i> Browse
                </button>
            </div>
        `;
        const input = el.querySelector('.trait-input');
        const btn = el.querySelector('.trait-btn');

        const updateVal = (val) => {
            input.value = val;
            trait.set('value', val);
            const target = trait.target || editor.getSelected();
            if (target && target.addAttributes) {
                target.addAttributes({ [traitName]: val });
            }
        };

        input.addEventListener('change', () => updateVal(input.value));
        input.addEventListener('input', () => updateVal(input.value));

        btn.addEventListener('click', () => {
            const callbackId = 'media_picker_' + Math.random().toString(36).substring(2, 9);
            const handler = (event) => {
                const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                if (detail && detail.callbackId === callbackId) {
                    const mediaUrl = detail.url;
                    if (mediaUrl) {
                        updateVal(mediaUrl);
                    }
                    window.removeEventListener('media-picker-chosen', handler);
                }
            };
            window.addEventListener('media-picker-chosen', handler);

            if (window.Livewire) {
                window.Livewire.dispatch('open-media-picker', {
                    callbackId: callbackId,
                    filterType: isVideo ? 'video' : 'image',
                    multiple: false
                });
            } else {
                window.dispatchEvent(new CustomEvent('open-media-picker', {
                    detail: { callbackId: callbackId, filterType: isVideo ? 'video' : 'image', multiple: false }
                }));
            }
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
    isComponent(el) {
        if (!el || !el.classList) return false;
        if (el.classList.contains('hero') && (el.tagName === 'SECTION' || el.id === 'home' || (el.getAttribute && el.getAttribute('data-gjs-type') === 'safaris-hero-component'))) {
            return { type: 'safaris-hero-component' };
        }
        return false;
    },
    model: {
        defaults: {
            tagName: 'section',
            name: 'Hero Section',
            droppable: true,
            attributes: {
                class: 'hero',
                id: 'home',
                'data-gjs-type': 'safaris-hero-component',
                'data-bg-type': 'image',
                'data-bg-image': 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1920&q=80',
                'data-bg-video': '',
                'data-bg-video-url': '',
                'data-overlay-opacity': '0.4',
            },
            traits: [
                {
                    type: 'select',
                    name: 'data-bg-type',
                    label: 'Background Type',
                    options: [
                        { value: 'image', name: 'Background Image' },
                        { value: 'video_url', name: 'Video URL (YouTube / MP4)' },
                        { value: 'video_upload', name: 'Uploaded Video (Media Library)' }
                    ]
                },
                {
                    type: 'media_picker',
                    name: 'data-bg-image',
                    label: 'Background Image'
                },
                {
                    type: 'text',
                    name: 'data-bg-video-url',
                    label: 'Video URL (YouTube or MP4)',
                    placeholder: 'https://www.youtube.com/watch?v=... or .mp4'
                },
                {
                    type: 'media_picker',
                    name: 'data-bg-video',
                    label: 'Uploaded Video (MP4)'
                },
                {
                    type: 'select',
                    name: 'data-overlay-opacity',
                    label: 'Dark Overlay',
                    options: [
                        { value: '0', name: 'None (0%)' },
                        { value: '0.2', name: 'Light (20%)' },
                        { value: '0.4', name: 'Standard (40%)' },
                        { value: '0.6', name: 'Dark (60%)' },
                        { value: '0.8', name: 'Heavy (80%)' }
                    ]
                }
            ],
            components: `
                <div class="hero-bg-container" data-gjs-locked="true" data-gjs-selectable="false" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
                    <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1920&q=80')"></div>
                </div>
                <div class="hero-content-tge" style="position: relative; z-index: 10;">
                    <div class="hero-tge-layout">
                        <div class="hero-tge-main-row">
                            <div class="hero-tge-left-col">
                                <h1 class="hero-tge-title">Begin Your<br><em>African Story.</em></h1>
                                <p class="hero-tge-sub">Uncover the magic of Tanzania's wilderness,<br>culture, and adventure.</p>
                            </div>
                            <div class="hero-tge-right-col">
                                <div class="hero-tge-feature-cards">
                                    <div class="hero-tge-card">
                                        <div class="hero-tge-card-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                        </div>
                                        <span>Tanzania's<br>Wild Side</span>
                                    </div>
                                    <div class="hero-tge-card">
                                        <div class="hero-tge-card-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                        </div>
                                        <span>Travel with<br>Experts</span>
                                    </div>
                                    <div class="hero-tge-card hero-tge-card--active">
                                        <div class="hero-tge-card-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                                        </div>
                                        <span>Tailored<br>Experiences</span>
                                    </div>
                                </div>
                                <p class="hero-tge-quote">"Dive into Tanzania's breathtaking landscapes and vibrant wildlife. Let us guide you to memories that last a lifetime."</p>
                            </div>
                        </div>
                        <div class="hero-tge-actions">
                            <a href="#safaris" class="hero-tge-btn hero-tge-btn--left">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M15.5 14.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4zm-7-4c0 2.7 1.5 5 3.7 6.3L10.5 20H13l1.3-2.7c.4.1.8.2 1.2.2s.8-.1 1.2-.2L18 20h2.5l-1.7-3.2c2.2-1.3 3.7-3.6 3.7-6.3 0-4-3.1-7.2-7-7.2S8.5 6.5 8.5 10.5zM4 12c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm3.5 3.5C6.1 16.9 4.1 18 2 18v2c2.8 0 5.2-1.4 6.7-3.5L7.5 15.5z"/></svg>
                                Tanzania Safaris
                            </a>
                            <a href="#trekking" class="hero-tge-btn hero-tge-btn--right">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm4 5.28c-1.23-.37-2.22-1.17-2.8-2.22l-1-1.6c-.4-.64-1.07-1-1.8-.96-.75 0-1.33.4-1.7 1l-3.2 5.4c-.34.56-.34 1.24 0 1.8l2.8 4.4.4.65V21h2v-2.5l-1.4-2.65 1.2-2L15 16v5h2v-6l-2.6-1.5.6-1 .74 1.28c.56.93 1.33 1.69 2.26 2.22V15h2v-2.5c-1.1 0-2-.9-2-2.22z"/></svg>
                                Mount Trekking
                            </a>
                        </div>
                    </div>
                </div>
            `
        },
        init() {
            this.on('change:attributes:data-bg-type change:attributes:data-bg-image change:attributes:data-bg-video change:attributes:data-bg-video-url change:attributes:data-overlay-opacity', this.updateBackground);

            setTimeout(() => {
                const attrs = this.getAttributes() || {};
                if (!attrs['data-bg-image']) {
                    const bgEl = (this.find && this.find('.hero-bg')[0]);
                    if (bgEl) {
                        const style = bgEl.getStyle() || {};
                        const bgImg = style['background-image'] || '';
                        const match = bgImg.match(/url\(['"]?(.*?)['"]?\)/);
                        if (match && match[1]) {
                            this.addAttributes({ 'data-bg-image': match[1] });
                        }
                    }
                }
                this.updateBackground();
            }, 80);
        },
        updateBackground() {
            const attrs = this.getAttributes() || {};
            const bgType = attrs['data-bg-type'] || 'image';
            const bgImage = attrs['data-bg-image'] || '';
            const bgVideo = attrs['data-bg-video'] || '';
            const bgVideoUrl = attrs['data-bg-video-url'] || '';
            const overlayOpacity = attrs['data-overlay-opacity'] || '0.4';

            let bgContainer = (this.find && this.find('.hero-bg-container')[0]) ||
                              this.components().models.find(m => m.getClasses && m.getClasses().includes('hero-bg-container'));

            if (!bgContainer) {
                const existingBg = (this.find && this.find('.hero-bg')[0]);
                if (existingBg && existingBg.parent && existingBg.parent()) {
                    bgContainer = existingBg.parent();
                } else {
                    bgContainer = this.components().add({
                        tagName: 'div',
                        attributes: { class: 'hero-bg-container', 'data-gjs-locked': 'true', 'data-gjs-selectable': 'false' },
                        style: { position: 'absolute', top: '0', left: '0', width: '100%', height: '100%', 'z-index': '1', overflow: 'hidden' }
                    }, { at: 0 });
                }
            }

            if (!bgContainer) return;
            bgContainer.addStyle({ overflow: 'hidden', position: 'absolute', top: '0', left: '0', width: '100%', height: '100%', 'z-index': '1' });

            let comps = [];

            const isVideoMode = bgType === 'video_url' || bgType === 'video_upload' || (!bgImage && (bgVideoUrl || bgVideo));

            if (isVideoMode) {
                const url = bgType === 'video_upload' ? (bgVideo || bgVideoUrl) : (bgVideoUrl || bgVideo);

                // Robust YouTube ID Extractor
                const ytMatch = url ? url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|shorts\/|live\/|watch\?v=|\&v=)([^#\&\?]*).*/) : null;
                const ytId = (ytMatch && ytMatch[2].length === 11) ? ytMatch[2] : null;

                if (ytId) {
                    comps = [
                        {
                            tagName: 'iframe',
                            style: {
                                'position': 'absolute',
                                'top': '50%',
                                'left': '50%',
                                'width': 'max(100vw, calc(100vh * (16 / 9)))',
                                'height': 'max(100vh, calc(100vw * (9 / 16)))',
                                'transform': 'translate(-50%, -50%) scale(1.25)',
                                'pointer-events': 'none',
                                'min-width': '100vw',
                                'min-height': '100vh',
                                'border': 'none',
                                'z-index': '1'
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
                                'background': `rgba(0,0,0,${overlayOpacity})`,
                                'z-index': '2'
                            },
                            attributes: {
                                class: 'hero-bg-overlay'
                            }
                        }
                    ];
                } else if (url) {
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
                                'pointer-events': 'none',
                                'z-index': '1'
                            },
                            attributes: {
                                class: 'hero-bg-video',
                                autoplay: 'autoplay',
                                loop: 'loop',
                                muted: 'muted',
                                playsinline: 'playsinline',
                                src: url
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
                                'background': `rgba(0,0,0,${overlayOpacity})`,
                                'z-index': '2'
                            },
                            attributes: {
                                class: 'hero-bg-overlay'
                            }
                        }
                    ];
                }
            }

            if (comps.length === 0) {
                const finalImg = bgImage || 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1920&q=80';
                comps = [
                    {
                        tagName: 'div',
                        style: {
                            'background-image': `url('${finalImg}')`,
                            'background-size': 'cover',
                            'background-position': 'center',
                            'width': '100%',
                            'height': '100%',
                            'position': 'absolute',
                            'inset': '0',
                            'z-index': '1'
                        },
                        attributes: {
                            class: 'hero-bg'
                        }
                    },
                    {
                        tagName: 'div',
                        style: {
                            'position': 'absolute',
                            'inset': '0',
                            'background': `rgba(0,0,0,${overlayOpacity})`,
                            'z-index': '2'
                        },
                        attributes: {
                            class: 'hero-bg-overlay'
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

// Register custom multiselect trait type for GrapesJS (compat for both TraitManager and Traits)
const tm = editor.TraitManager || editor.Traits;
if (tm && typeof tm.addType === 'function') {
    tm.addType('multiselect', {
        createInput({ trait }) {
            const container = document.createElement('div');
            container.className = 'gjs-multiselect-container';
            container.style.cssText = 'width: 100%; box-sizing: border-box;';

            // Fast search filter input
            const search = document.createElement('input');
            search.type = 'text';
            search.placeholder = '🔍 Search country...';
            search.className = 'gjs-multiselect-search';
            search.style.cssText = 'width: 100%; box-sizing: border-box; background: #0f172a; border: 1px solid #334155; border-radius: 4px; color: #cbd5e1; font-size: 11px; padding: 6px 8px; margin-bottom: 6px; outline: none;';
            container.appendChild(search);

            const wrap = document.createElement('div');
            wrap.className = 'gjs-multiselect-wrap';
            wrap.style.cssText = 'width: 100%; max-height: 200px; overflow-y: auto; background: #0f172a; border: 1px solid #334155; border-radius: 4px; padding: 6px; box-sizing: border-box;';
            
            const options = trait.get('options') || [];
            options.forEach(opt => {
                const label = document.createElement('label');
                label.className = 'gjs-multiselect-item';
                label.setAttribute('data-label', (opt.name || '').toLowerCase());
                label.style.cssText = 'display: flex; align-items: center; gap: 8px; font-size: 12px; color: #cbd5e1; cursor: pointer; padding: 4px; user-select: none; border-radius: 3px; margin-bottom: 2px;';
                label.onmouseover = () => label.style.background = '#1e293b';
                label.onmouseout = () => label.style.background = 'transparent';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = opt.value;
                checkbox.className = 'gjs-multiselect-checkbox';
                checkbox.style.cssText = 'cursor: pointer; accent-color: #455537; width: 15px; height: 15px;';
                
                const span = document.createElement('span');
                span.textContent = opt.name;
                
                label.appendChild(checkbox);
                label.appendChild(span);
                wrap.appendChild(label);
            });

            search.addEventListener('input', (e) => {
                const q = (e.target.value || '').toLowerCase().trim();
                wrap.querySelectorAll('.gjs-multiselect-item').forEach(item => {
                    const text = item.getAttribute('data-label') || '';
                    item.style.display = (!q || text.includes(q)) ? 'flex' : 'none';
                });
            });

            container.appendChild(wrap);
            return container;
        },
        onUpdate({ elInput, component, trait }) {
            const raw = component.getAttributes()[trait.get('name')] || 'all';
            const selected = raw.split(',').map(s => s.trim());
            const checkboxes = elInput.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.checked = selected.includes(cb.value);
            });
        },
        onEvent({ elInput, component, trait }) {
            const checkboxes = elInput.querySelectorAll('input[type="checkbox"]:checked');
            const values = Array.from(checkboxes).map(cb => cb.value);
            let finalVal = values.join(',');
            if (!finalVal || values.includes('all')) {
                finalVal = values.includes('all') && values.length > 1 
                    ? values.filter(v => v !== 'all').join(',') 
                    : 'all';
            }
            component.addAttributes({ [trait.get('name')]: finalVal });
            if (component.view && typeof component.view.renderView === 'function') {
                component.view.renderView();
            }
        }
    });
}

// Register custom ordered-multiselect trait type with drag-and-drop & Up/Down reordering
const registerOrderedMultiselectTrait = () => {
    const handler = {
        createInput({ trait, component }) {
            const container = document.createElement('div');
            container.className = 'gjs-ordered-multiselect-container';
            container.style.cssText = 'width: 100%; box-sizing: border-box; font-family: sans-serif;';

            const options = trait.get('options') || [];
            const getComp = () => trait.target || component || (editor.getSelected && editor.getSelected());

            // Read current attribute value
            const getInitialValues = () => {
                const comp = getComp();
                const raw = (comp ? comp.getAttributes()[trait.get('name')] : trait.get('value')) || 'all';
                if (raw === 'all' || !raw.trim()) return ['all'];
                return raw.split(',').map(s => s.trim()).filter(Boolean);
            };

            let orderedValues = getInitialValues();

            // 1. Order Management Section (Header & Ordered list)
            const orderSection = document.createElement('div');
            orderSection.style.cssText = 'background: #0b1322; border: 1px solid #1e293b; border-radius: 6px; padding: 8px; margin-bottom: 8px;';

            const orderHeader = document.createElement('div');
            orderHeader.style.cssText = 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 11px;';
            orderSection.appendChild(orderHeader);

            const orderList = document.createElement('div');
            orderList.style.cssText = 'display: flex; flex-direction: column; gap: 4px; max-height: 160px; overflow-y: auto;';
            orderSection.appendChild(orderList);

            container.appendChild(orderSection);

            // 2. Search input
            const search = document.createElement('input');
            search.type = 'text';
            search.placeholder = '🔍 Search country to add...';
            search.className = 'gjs-ordered-multiselect-search';
            search.style.cssText = 'width: 100%; box-sizing: border-box; background: #0f172a; border: 1px solid #334155; border-radius: 4px; color: #cbd5e1; font-size: 11px; padding: 6px 8px; margin-bottom: 6px; outline: none;';
            container.appendChild(search);

            // 3. Checkbox options container
            const wrap = document.createElement('div');
            wrap.className = 'gjs-ordered-multiselect-wrap';
            wrap.style.cssText = 'width: 100%; max-height: 180px; overflow-y: auto; background: #0f172a; border: 1px solid #334155; border-radius: 4px; padding: 6px; box-sizing: border-box;';
            container.appendChild(wrap);

            const checkboxElements = {};
            const badgeElements = {};

            options.forEach(opt => {
                const label = document.createElement('label');
                label.className = 'gjs-multiselect-item';
                label.setAttribute('data-label', (opt.name || '').toLowerCase());
                label.style.cssText = 'display: flex; align-items: center; gap: 8px; font-size: 12px; color: #cbd5e1; cursor: pointer; padding: 4px 6px; user-select: none; border-radius: 4px; margin-bottom: 2px; transition: background 0.15s;';
                label.onmouseover = () => label.style.background = '#1e293b';
                label.onmouseout = () => label.style.background = 'transparent';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = opt.value;
                checkbox.className = 'gjs-multiselect-checkbox';
                checkbox.style.cssText = 'cursor: pointer; accent-color: #455537; width: 14px; height: 14px; margin: 0;';
                checkboxElements[opt.value] = checkbox;

                const nameSpan = document.createElement('span');
                nameSpan.style.cssText = 'flex: 1;';
                nameSpan.textContent = opt.name;

                const orderBadge = document.createElement('span');
                orderBadge.style.cssText = 'font-size: 10px; font-weight: 700; color: #CA9A2E; background: rgba(202,154,46,0.15); padding: 1px 5px; border-radius: 3px; display: none;';
                badgeElements[opt.value] = orderBadge;

                label.appendChild(checkbox);
                label.appendChild(nameSpan);
                label.appendChild(orderBadge);
                wrap.appendChild(label);

                checkbox.addEventListener('change', () => {
                    if (opt.value === 'all') {
                        orderedValues = ['all'];
                    } else {
                        orderedValues = orderedValues.filter(v => v !== 'all');
                        if (checkbox.checked) {
                            if (!orderedValues.includes(opt.value)) {
                                orderedValues.push(opt.value);
                            }
                        } else {
                            orderedValues = orderedValues.filter(v => v !== opt.value);
                            if (orderedValues.length === 0) {
                                orderedValues = ['all'];
                            }
                        }
                    }
                    syncState(true);
                });
            });

            // Fast search filter
            search.addEventListener('input', (e) => {
                const q = (e.target.value || '').toLowerCase().trim();
                wrap.querySelectorAll('.gjs-multiselect-item').forEach(item => {
                    const text = item.getAttribute('data-label') || '';
                    item.style.display = (!q || text.includes(q)) ? 'flex' : 'none';
                });
            });

            // Sync state to component attributes and render order list + checkboxes
            const syncState = (triggerRender = true) => {
                let finalVal = orderedValues.join(',');
                if (!finalVal || orderedValues.includes('all')) {
                    finalVal = 'all';
                    orderedValues = ['all'];
                }

                trait.set('value', finalVal);
                const comp = getComp();
                if (comp) {
                    comp.addAttributes({ [trait.get('name')]: finalVal });
                    if (triggerRender && comp.view && typeof comp.view.renderView === 'function') {
                        comp.view.renderView();
                    }
                    try { editor.trigger('change:canvas'); } catch (_) {}
                }

                // Update Checkboxes & Badges
                options.forEach(opt => {
                    const cb = checkboxElements[opt.value];
                    const bg = badgeElements[opt.value];
                    if (!cb) return;

                    if (finalVal === 'all') {
                        cb.checked = (opt.value === 'all');
                        if (bg) bg.style.display = 'none';
                    } else {
                        const idx = orderedValues.indexOf(opt.value);
                        cb.checked = idx !== -1;
                        if (bg) {
                            if (idx !== -1) {
                                bg.textContent = `#${idx + 1}`;
                                bg.style.display = 'inline-block';
                            } else {
                                bg.style.display = 'none';
                            }
                        }
                    }
                });

                // Update Order List UI
                renderOrderListUI();
            };

            const renderOrderListUI = () => {
                orderList.innerHTML = '';
                const isAll = orderedValues.length === 0 || orderedValues.includes('all');

                if (isAll) {
                    orderHeader.innerHTML = `
                        <span style="font-weight: 700; color: #8fa593; letter-spacing: 0.05em; text-transform: uppercase;">
                            Active Order: Auto
                        </span>
                        <span style="color: #64748b; font-size: 10px;">Default DB Order</span>
                    `;
                    orderList.innerHTML = `
                        <div style="padding: 8px 10px; background: rgba(69,85,55,0.2); border: 1px dashed #455537; border-radius: 4px; font-size: 11px; color: #a9c3b0; display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 13px;">🌍</span>
                            <span><strong>All Countries:</strong> Featured order from database. Check countries below to set custom sequence.</span>
                        </div>
                    `;
                    return;
                }

                orderHeader.innerHTML = `
                    <span style="font-weight: 700; color: #CA9A2E; letter-spacing: 0.05em; text-transform: uppercase;">
                        Active Order (${orderedValues.length})
                    </span>
                    <button type="button" class="js-reset-auto" style="background: transparent; border: none; color: #94a3b8; font-size: 10px; text-decoration: underline; cursor: pointer; padding: 0;">
                        Reset to Auto
                    </button>
                `;

                const resetBtn = orderHeader.querySelector('.js-reset-auto');
                if (resetBtn) {
                    resetBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        orderedValues = ['all'];
                        syncState(true);
                    });
                }

                let draggedIdx = null;

                orderedValues.forEach((val, idx) => {
                    const opt = options.find(o => o.value === val) || { name: `Country #${val}`, value: val };
                    const cleanName = opt.name.replace(/\s*\(.*?\)/, '');
                    const isFirst = idx === 0;
                    const isLast = idx === orderedValues.length - 1;

                    const row = document.createElement('div');
                    row.className = 'gjs-ordered-row';
                    row.draggable = true;
                    row.setAttribute('data-idx', idx);
                    row.style.cssText = 'display: flex; align-items: center; gap: 6px; background: #162032; border: 1px solid #334155; border-radius: 4px; padding: 4px 6px; cursor: grab; transition: all 0.15s;';

                    row.innerHTML = `
                        <span style="color: #64748b; font-size: 12px; cursor: grab;" title="Drag to reorder">☰</span>
                        <span style="font-family: monospace; font-size: 10px; font-weight: 700; color: #CA9A2E; background: rgba(202,154,46,0.15); padding: 1px 4px; border-radius: 3px; shrink: 0;">
                            ${String(idx + 1).padStart(2, '0')}
                        </span>
                        <span style="flex: 1; font-size: 11px; font-weight: 500; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${cleanName}">
                            ${cleanName}
                        </span>
                        <div style="display: flex; gap: 2px; align-items: center;">
                            <button type="button" class="btn-up" style="width: 20px; height: 20px; border-radius: 3px; border: 1px solid #334155; background: ${isFirst ? '#1e293b' : '#334155'}; color: ${isFirst ? '#475569' : '#e2e8f0'}; cursor: ${isFirst ? 'default' : 'pointer'}; font-size: 10px; display: flex; align-items: center; justify-content: center; padding: 0;" ${isFirst ? 'disabled' : ''} title="Move Up">↑</button>
                            <button type="button" class="btn-down" style="width: 20px; height: 20px; border-radius: 3px; border: 1px solid #334155; background: ${isLast ? '#1e293b' : '#334155'}; color: ${isLast ? '#475569' : '#e2e8f0'}; cursor: ${isLast ? 'default' : 'pointer'}; font-size: 10px; display: flex; align-items: center; justify-content: center; padding: 0;" ${isLast ? 'disabled' : ''} title="Move Down">↓</button>
                            <button type="button" class="btn-remove" style="width: 20px; height: 20px; border-radius: 3px; border: 1px solid rgba(239,68,68,0.25); background: rgba(239,68,68,0.12); color: #f87171; cursor: pointer; font-size: 10px; display: flex; align-items: center; justify-content: center; padding: 0; margin-left: 2px;" title="Remove">✕</button>
                        </div>
                    `;

                    // Button actions
                    const btnUp = row.querySelector('.btn-up');
                    const btnDown = row.querySelector('.btn-down');
                    const btnRemove = row.querySelector('.btn-remove');

                    if (!isFirst && btnUp) {
                        btnUp.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            const temp = orderedValues[idx];
                            orderedValues[idx] = orderedValues[idx - 1];
                            orderedValues[idx - 1] = temp;
                            syncState(true);
                        });
                    }

                    if (!isLast && btnDown) {
                        btnDown.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            const temp = orderedValues[idx];
                            orderedValues[idx] = orderedValues[idx + 1];
                            orderedValues[idx + 1] = temp;
                            syncState(true);
                        });
                    }

                    if (btnRemove) {
                        btnRemove.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            orderedValues = orderedValues.filter(v => v !== val);
                            if (orderedValues.length === 0) orderedValues = ['all'];
                            syncState(true);
                        });
                    }

                    // Drag & Drop
                    row.addEventListener('dragstart', (e) => {
                        draggedIdx = idx;
                        e.dataTransfer.effectAllowed = 'move';
                        row.style.opacity = '0.5';
                    });
                    row.addEventListener('dragend', () => {
                        row.style.opacity = '1';
                    });
                    row.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        e.dataTransfer.dropEffect = 'move';
                        row.style.borderColor = '#CA9A2E';
                    });
                    row.addEventListener('dragleave', () => {
                        row.style.borderColor = '#334155';
                    });
                    row.addEventListener('drop', (e) => {
                        e.preventDefault();
                        row.style.borderColor = '#334155';
                        if (draggedIdx !== null && draggedIdx !== idx) {
                            const item = orderedValues.splice(draggedIdx, 1)[0];
                            orderedValues.splice(idx, 0, item);
                            syncState(true);
                        }
                    });

                    orderList.appendChild(row);
                });
            };

            // Initial sync on mount
            syncState(false);

            return container;
        },
        onUpdate({ elInput, component, trait }) {
            const raw = (component ? component.getAttributes()[trait.get('name')] : trait.get('value')) || 'all';
            let values = raw === 'all' || !raw.trim() ? ['all'] : raw.split(',').map(s => s.trim()).filter(Boolean);

            const checkboxes = elInput.querySelectorAll('.gjs-multiselect-checkbox');
            checkboxes.forEach(cb => {
                if (values.includes('all')) {
                    cb.checked = cb.value === 'all';
                } else {
                    cb.checked = values.includes(cb.value);
                }
            });
        }
    };

    if (editor.Traits && typeof editor.Traits.addType === 'function') {
        editor.Traits.addType('ordered-multiselect', handler);
    }
    const tm = editor.TraitManager || editor.Traits;
    if (tm && typeof tm.addType === 'function') {
        tm.addType('ordered-multiselect', handler);
    }
};

registerOrderedMultiselectTrait();

// Auto-switch to Trait Manager (Settings / ⚙ tab) when clicking Dynamic Tours block or its section
editor.on('component:selected', (component) => {
    if (!component) return;
    const type = component.get ? component.get('type') : null;
    const classes = component.getClasses ? component.getClasses() : [];
    
    let target = component;
    if (type !== 'tours-listing' && !classes.includes('tours-listing')) {
        let found = null;
        if (typeof component.closest === 'function') {
            found = component.closest('.tours-listing') || component.closest('[data-gjs-type="tours-listing"]');
        }
        if (!found && typeof component.find === 'function') {
            const children = component.find('.tours-listing');
            if (children && children.length) {
                found = children[0];
            }
        }
        if (found) {
            target = found;
        }
    }
    
    const isToursListing = target && (
        target.get('type') === 'tours-listing' || 
        (target.getClasses && target.getClasses().includes('tours-listing'))
    );

    const isHero = target && (
        target.get('type') === 'safaris-hero-component' ||
        (target.getClasses && target.getClasses().includes('hero'))
    );
    
    if (isToursListing || isHero) {
        if (target !== component) {
            editor.select(target);
            return;
        }
        
        try {
            const pn = editor.Panels;
            const btnTm = pn ? pn.getButton('views', 'open-tm') : null;
            if (btnTm) {
                btnTm.set('active', true);
            } else {
                editor.runCommand('open-tm');
            }
        } catch (err) {
            try { editor.runCommand('open-tm'); } catch (_) {}
        }
    }
});

// Database-driven country multiselect options (pulls from window.TOUR_COUNTRIES / window.COUNTRIES)
const getCountryMultiselectOptions = () => {
    const list = window.TOUR_COUNTRIES || window.COUNTRIES || [];
    
    // Sort: countries with destinations/tours first, then alphabetically
    const sorted = [...list].sort((a, b) => {
        const destDiff = ((b.destinations_count || 0) + (b.tours_count || 0)) - ((a.destinations_count || 0) + (a.tours_count || 0));
        if (destDiff !== 0) return destDiff;
        const nameA = typeof a.name === 'object' ? (a.name.en || Object.values(a.name)[0]) : a.name;
        const nameB = typeof b.name === 'object' ? (b.name.en || Object.values(b.name)[0]) : b.name;
        return (nameA || '').localeCompare(nameB || '');
    });

    const opts = [
        { value: 'all', name: '🌍 All Countries (Auto)' }
    ];

    sorted.forEach(c => {
        const cName = typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name;
        const countParts = [];
        if (c.destinations_count) countParts.push(`${c.destinations_count} dest`);
        if (c.tours_count) countParts.push(`${c.tours_count} tours`);
        const badge = countParts.length ? ` (${countParts.join(', ')})` : '';
        opts.push({
            value: c.id.toString(),
            name: `${cName}${badge}`
        });
    });

    return opts;
};

const resolveToursSelectedCountries = (countryAttr) => {
    const dbList = window.TOUR_COUNTRIES || window.COUNTRIES || [];
    if (countryAttr && countryAttr !== 'all') {
        const ids = countryAttr.split(',').map(s => s.trim()).filter(Boolean);
        const list = [];
        ids.forEach(id => {
            const found = dbList.find(c => c.id.toString() === id);
            if (found) {
                const name = typeof found.name === 'object' ? (found.name.en || Object.values(found.name)[0]) : found.name;
                list.push({ id: found.id, name, meta: found });
            }
        });
        if (list.length > 0) return list;
    }

    // Default 'all': countries with destinations/tours first, or East Africa, or top from DB
    const withDest = dbList.filter(c => (c.destinations_count || 0) > 0 || (c.tours_count || 0) > 0);
    const pool = withDest.length ? withDest : dbList.filter(c => {
        const n = typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name;
        return ['Tanzania', 'Kenya', 'Uganda', 'Rwanda', 'Zanzibar'].includes(n);
    });
    const active = (pool.length ? pool : dbList).slice(0, 5);

    return active.map(c => ({
        id: c.id,
        name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name,
        meta: c
    }));
};

const buildToursListingPreviewHtml = (attrs, activeIdx = 0) => {
    const countryAttr = (attrs && attrs['data-country']) || 'all';
    const layoutAttr = (attrs && attrs['data-layout']) || 'auto';
    const heading = (attrs && attrs['data-heading']) || '';
    const headingAccent = (attrs && attrs['data-heading-accent']) || '';
    const subheading = (attrs && attrs['data-subheading']) || "Choose a destination to explore Africa's iconic safaris and signature experiences";
    const limit = (attrs && attrs['data-limit']) || '4';

    const selectedCountries = resolveToursSelectedCountries(countryAttr);
    const safeIdx = Math.max(0, Math.min(activeIdx, selectedCountries.length - 1));
    const activeCountryObj = selectedCountries[safeIdx] || { id: 90, name: 'Tanzania', meta: {} };
    const activeCountry = activeCountryObj.name;
    const activeMeta = activeCountryObj.meta || {};

    const activeTagline = activeMeta.tagline || `${activeCountry} Safaris & Adventures`;
    const activeDesc = activeMeta.description || `Explore extraordinary landscapes, wildlife reserves, and signature safari journeys across ${activeCountry}.`;
    const activeImage = activeMeta.image || 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80';
    const activeTours = (activeMeta.tours && activeMeta.tours.length) ? activeMeta.tours : [
        { name: `7-Day ${activeCountry} Wildlife & Safari Explorer`, days: 7, price: '$2,850', image: activeImage },
        { name: `5-Day Best of ${activeCountry} Signature Journey`, days: 5, price: '$2,100', image: activeImage }
    ];

    const displayHeading = heading || (selectedCountries.length === 1 ? selectedCountries[0].name : (selectedCountries.length + ' Destinations.'));
    const displayAccent = headingAccent || (selectedCountries.length === 1 ? 'Signature Safaris.' : 'One Journey.');

    const countryNamesList = selectedCountries.map(c => c.name).join(', ');

    // Configuration Toolbar Banner
    const topToolbar = `
        <div style="background: linear-gradient(135deg, #1b281f, #0d1611); border: 1px solid #455537; border-radius: 8px; padding: 12px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.3); font-family: sans-serif; gap: 12px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="background: #455537; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">🌍 DYNAMIC TOURS SHOWCASE</span>
                <span style="font-size: 13px; color: #e2e8f0;">Display Order: <strong style="color: #CA9A2E; background: rgba(202,154,46,0.15); padding: 2px 8px; border-radius: 4px;">${countryNamesList}</strong></span>
                <span style="font-size: 11px; color: #8fa593;">(Use ▲ ▼ on tabs or open Settings to reorder)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 11px; color: #CA9A2E;">💡 Click heading text to edit</span>
                <button type="button" class="js-open-settings" style="background: #CA9A2E; color: #1E271C; font-weight: 700; font-size: 12px; padding: 6px 14px; border: none; border-radius: 4px; cursor: pointer; transition: opacity .15s;">
                    ⚙ Edit Order & Settings
                </button>
            </div>
        </div>
    `;

    if (layoutAttr === 'grid') {
        const gridTours = activeTours.slice(0, Math.min(3, parseInt(limit, 10) || 3));
        return `
            <div style="color: #e8eee9; padding: 10px; font-family: sans-serif; position: relative; min-height: 200px;">
                ${topToolbar}
                <div style="text-align: center; margin-bottom: 32px;">
                    <div style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="width: 28px; height: 2px; background: #CA9A2E; display: inline-block;"></span>
                        <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #CA9A2E; text-transform: uppercase;">SAFARI ADVENTURES</span>
                    </div>
                    <div style="margin: 0 0 10px;">
                        <h2 class="js-edit-heading" data-bind-trait="data-heading" contenteditable="true" title="Click to edit heading" style="display: inline-block; font-family: 'Playfair Display', Georgia, serif; font-size: 38px; color: #fff; margin: 0; cursor: text; outline: none; border-bottom: 1px dashed rgba(202,154,46,0.3);">
                            ${displayHeading}
                        </h2>
                        <em class="js-edit-accent" data-bind-trait="data-heading-accent" contenteditable="true" title="Click to edit accent" style="display: inline-block; font-family: 'Playfair Display', Georgia, serif; font-size: 34px; font-style: italic; color: #CA9A2E; cursor: text; outline: none; border-bottom: 1px dashed rgba(202,154,46,0.3); margin-left: 8px;">
                            ${displayAccent}
                        </em>
                    </div>
                    <p class="js-edit-subheading" data-bind-trait="data-subheading" contenteditable="true" title="Click to edit description" style="font-size: 15px; color: #8fa593; max-width: 600px; margin: 0 auto; cursor: text; outline: none; border-bottom: 1px dashed rgba(255,255,255,0.15);">
                        ${subheading}
                    </p>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                    ${gridTours.map(t => {
                        const title = t.name || t.title;
                        const days = t.days ? (typeof t.days === 'string' && t.days.includes('Day') ? t.days : `${t.days} Days`) : '7 Days';
                        const price = t.price ? (typeof t.price === 'string' && t.price.includes('$') ? t.price : `$${Number(t.price).toLocaleString()}`) : '$2,400';
                        const img = t.image || t.img || activeImage;
                        return `
                        <div style="background: #142319; border: 1px solid #213829; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                            <div style="height: 180px; position: relative;">
                                <img src="${img}" style="width: 100%; height: 100%; object-fit: cover;" />
                                <span style="position: absolute; top: 12px; right: 12px; background: rgba(13,22,17,0.85); color: #CA9A2E; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 4px;">${days}</span>
                            </div>
                            <div style="padding: 18px;">
                                <h3 style="font-family: 'Playfair Display', Georgia, serif; font-size: 18px; color: #fff; margin: 0 0 6px;">${title}</h3>
                                <p style="font-size: 12px; color: #7f9c85; margin: 0 0 14px;">${activeCountry} · ${days}</p>
                                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 12px;">
                                    <span style="font-size: 14px; font-weight: 700; color: #CA9A2E;">From ${price}</span>
                                    <span style="font-size: 12px; color: #fff;">View Safari &rarr;</span>
                                </div>
                            </div>
                        </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }

    // Default SafariHQ Showcase with Interactive Country Tabs
    let countryTabsHtml = '';
    selectedCountries.forEach((cObj, idx) => {
        const pad = String(idx + 1).padStart(2, '0');
        const isActive = idx === safeIdx;
        const tag = (cObj.meta && cObj.meta.tagline) || `${cObj.name} Safaris & Adventures`;
        const isFirst = idx === 0;
        const isLast = idx === selectedCountries.length - 1;
        countryTabsHtml += `
            <div class="js-tour-tab" data-country-idx="${idx}" style="padding: 14px 18px; border-radius: 8px; margin-bottom: 8px; border: 1px solid ${isActive ? '#2e4c36' : 'rgba(255,255,255,0.06)'}; background: ${isActive ? '#223829' : 'transparent'}; display: flex; align-items: start; gap: 14px; position: relative; cursor: pointer; transition: all 0.2s ease;">
                ${isActive ? '<div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #CA9A2E; border-top-left-radius: 8px; border-bottom-left-radius: 8px;"></div>' : ''}
                <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: ${isActive ? '#CA9A2E' : '#587360'}; padding-top: 2px;">${pad}</span>
                <div style="flex: 1;">
                    <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 19px; font-weight: 500; color: ${isActive ? '#fff' : '#b8cbbd'};">${cObj.name}</div>
                    <div style="font-size: 12px; color: ${isActive ? '#9cb5a2' : '#6a8773'}; margin-top: 2px;">${tag}</div>
                </div>
                <!-- In-canvas Quick Reorder Buttons -->
                <div class="js-tab-reorder-group" style="display: flex; flex-direction: column; gap: 2px; align-self: center;" onclick="event.stopPropagation();">
                    ${!isFirst ? `<button type="button" class="js-reorder-canvas" data-action="up" data-country-idx="${idx}" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #CA9A2E; border-radius: 3px; font-size: 9px; line-height: 1; padding: 2px 5px; cursor: pointer;" title="Move ${cObj.name} Up">▲</button>` : ''}
                    ${!isLast ? `<button type="button" class="js-reorder-canvas" data-action="down" data-country-idx="${idx}" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #CA9A2E; border-radius: 3px; font-size: 9px; line-height: 1; padding: 2px 5px; cursor: pointer;" title="Move ${cObj.name} Down">▼</button>` : ''}
                </div>
                ${isActive ? '<span style="color: #CA9A2E; font-size: 14px; align-self: center;">●</span>' : ''}
            </div>
        `;
    });

    const toursListHtml = activeTours.map(t => {
        const title = t.name || t.title;
        const days = t.days ? (typeof t.days === 'string' && t.days.includes('Day') ? t.days : `${t.days} Days`) : '7 Days';
        const price = t.price ? (typeof t.price === 'string' && t.price.includes('$') ? t.price : `$${Number(t.price).toLocaleString()}`) : '$2,400';
        const img = t.image || t.img || activeImage;
        return `
        <div style="background: #142319; border: 1px solid #213829; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 10px;">
            <div style="width: 70px; height: 50px; border-radius: 6px; background: #223829; overflow: hidden; flex-shrink: 0;">
                <img src="${img}" style="width: 100%; height: 100%; object-fit: cover;" />
            </div>
            <div style="flex: 1;">
                <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 15px; font-weight: 600; color: #fff;">${title}</div>
                <div style="font-size: 12px; color: #7f9c85; margin-top: 2px;">${days} · Signature Tour</div>
            </div>
            <div style="text-align: right; flex-shrink: 0;">
                <div style="font-size: 13px; font-weight: 700; color: #CA9A2E;">${price}</div>
                <span style="color: #CA9A2E; font-size: 14px;">&rarr;</span>
            </div>
        </div>
        `;
    }).join('');

    return `
        <div style="color: #e8eee9; padding: 10px; font-family: sans-serif; position: relative; min-height: 300px;">
            ${topToolbar}
            
            <div style="display: grid; grid-template-columns: 5fr 7fr; gap: 36px; align-items: start;">
                <!-- Left Column -->
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="display: inline-block; width: 28px; height: 2px; background: #CA9A2E;"></span>
                        <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #CA9A2E; text-transform: uppercase;">WHERE WILL YOU GO</span>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <h2 class="js-edit-heading" data-bind-trait="data-heading" contenteditable="true" title="Click to edit heading" style="font-family: 'Playfair Display', Georgia, serif; font-size: 38px; font-weight: normal; line-height: 1.15; color: #fff; margin: 0; cursor: text; outline: none; border-bottom: 1px dashed rgba(202,154,46,0.3); display: inline-block;">
                            ${displayHeading}
                        </h2>
                        <div class="js-edit-accent" data-bind-trait="data-heading-accent" contenteditable="true" title="Click to edit accent" style="font-family: 'Playfair Display', Georgia, serif; font-size: 34px; font-style: italic; color: #CA9A2E; cursor: text; outline: none; border-bottom: 1px dashed rgba(202,154,46,0.3); display: inline-block; margin-top: 4px;">
                            ${displayAccent}
                        </div>
                    </div>
                    <p class="js-edit-subheading" data-bind-trait="data-subheading" contenteditable="true" title="Click to edit description" style="font-size: 14px; color: #8fa593; line-height: 1.6; margin-bottom: 24px; max-width: 400px; cursor: text; outline: none; border-bottom: 1px dashed rgba(255,255,255,0.15);">
                        ${subheading}
                    </p>
                    <div style="border-top: 1px solid #1c2e22; padding-top: 12px;">
                        ${countryTabsHtml}
                    </div>
                </div>

                <!-- Right Column Preview -->
                <div>
                    <div style="position: relative; border-radius: 14px; overflow: hidden; height: 320px; background: #142319; border: 1px solid #223a2a; margin-bottom: 24px;">
                        <img src="${activeImage}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.88;" />
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, #0c1610 0%, rgba(12,22,16,0.4) 60%, transparent 100%);"></div>
                        <div style="position: absolute; bottom: 20px; left: 24px; right: 24px;">
                            <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 34px; font-weight: 500; color: #fff; margin-bottom: 6px;">${activeCountry}</div>
                            <div style="font-size: 13px; color: #d1ded4; line-height: 1.5;">${activeDesc}</div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #CA9A2E; text-transform: uppercase;">FEATURED JOURNEYS (${activeCountry})</span>
                        <span style="font-size: 12px; color: #8fa593;">Live Tours from Database &rarr;</span>
                    </div>

                    <div>
                        ${toursListHtml}
                    </div>

                    <!-- Bottom Pagination Arrows -->
                    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 8px; margin-top: 16px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
                        <div style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid #2e4c36; background: #16271c; color: #CA9A2E; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                            <i class="fa-solid fa-arrow-left"></i>
                        </div>
                        <div style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid #2e4c36; background: #16271c; color: #CA9A2E; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
};

// Register Dynamic Tour Listing Component (Multi-Country SafariHQ & Grid Showcase)
editor.DomComponents.addType('tours-listing', {
    isComponent: el => (el && el.classList && el.classList.contains('tours-listing')) ? { type: 'tours-listing' } : false,
    model: {
        defaults: {
            tagName: 'div',
            droppable: false,
            highlightable: true,
            attributes: {
                class: 'tours-listing',
                'data-category': 'all',
                'data-tour-type': 'all',
                'data-country': 'all',
                'data-limit': '4',
                'data-layout': 'auto',
                'data-heading': '',
                'data-heading-accent': '',
                'data-subheading': "Choose a destination to explore Africa's iconic safaris and signature experiences"
            },
            traits: [
                {
                    type: 'text',
                    name: 'data-heading',
                    label: 'Section Heading',
                    placeholder: 'Auto (e.g. 6 Destinations. or Tanzania)'
                },
                {
                    type: 'text',
                    name: 'data-heading-accent',
                    label: 'Accent Heading (Italic)',
                    placeholder: 'Auto (e.g. One Journey. or Signature Safaris.)'
                },
                {
                    type: 'text',
                    name: 'data-subheading',
                    label: 'Subheading Description',
                    value: "Choose a destination to explore Africa's iconic safaris and signature experiences"
                },
                {
                    type: 'ordered-multiselect',
                    name: 'data-country',
                    label: 'Countries (Select & Order)',
                    options: getCountryMultiselectOptions()
                },
                {
                    type: 'select',
                    name: 'data-layout',
                    label: 'Showcase Layout',
                    options: [
                        { value: 'auto', name: 'Auto (SafariHQ Showcase)' },
                        { value: 'safarihq', name: 'SafariHQ (Showcase with Tabs)' },
                        { value: 'grid', name: 'Standard Card Grid' }
                    ]
                },
                {
                    type: 'select',
                    name: 'data-category',
                    label: 'Tour Category',
                    options: [
                        { value: 'all', name: window.__('All Categories') },
                        ...(window.TOUR_CATEGORIES || []).map(c => ({
                            value: c.slug,
                            name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name
                        }))
                    ]
                },
                {
                    type: 'select',
                    name: 'data-tour-type',
                    label: 'Tour Type Filter',
                    options: [
                        { value: 'all', name: window.__('All Tour Types') },
                        ...(window.TOUR_TYPES || []).map(c => ({
                            value: c.slug,
                            name: typeof c.name === 'object' ? (c.name.en || Object.values(c.name)[0]) : c.name
                        }))
                    ]
                },
                {
                    type: 'number',
                    name: 'data-limit',
                    label: 'Tours Per Page',
                    min: 1,
                    max: 20,
                    value: 4
                }
            ],
        }
    },
    view: {
        init() {
            this.activeCountryIdx = 0;
            this.listenTo(this.model, 'change:attributes', this.renderView);
        },
        onRender() {
            this.renderView();
        },
        renderView() {
            if (!this.el) return;
            const attrs = this.model.getAttributes();
            this.el.innerHTML = buildToursListingPreviewHtml(attrs, this.activeCountryIdx);
            this.bindEvents();
        },
        bindEvents() {
            if (!this.el) return;

            // Country tab switching
            this.el.querySelectorAll('.js-tour-tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    if (e.target.closest('.js-reorder-canvas')) return;
                    e.stopPropagation();
                    const idx = parseInt(tab.getAttribute('data-country-idx'), 10) || 0;
                    this.activeCountryIdx = idx;
                    this.renderView();
                });
            });

            // Direct in-canvas country tab reordering (▲ and ▼ buttons)
            this.el.querySelectorAll('.js-reorder-canvas').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    const action = btn.getAttribute('data-action');
                    const idx = parseInt(btn.getAttribute('data-country-idx'), 10);
                    const attrs = this.model.getAttributes();
                    const currentAttr = attrs['data-country'] || 'all';

                    let ids = [];
                    if (currentAttr !== 'all') {
                        ids = currentAttr.split(',').map(s => s.trim()).filter(Boolean);
                    } else {
                        const resolved = resolveToursSelectedCountries('all');
                        ids = resolved.map(c => c.id.toString());
                    }

                    if (action === 'up' && idx > 0 && ids[idx] && ids[idx - 1]) {
                        const temp = ids[idx];
                        ids[idx] = ids[idx - 1];
                        ids[idx - 1] = temp;
                        this.activeCountryIdx = idx - 1;
                    } else if (action === 'down' && idx < ids.length - 1 && ids[idx] && ids[idx + 1]) {
                        const temp = ids[idx];
                        ids[idx] = ids[idx + 1];
                        ids[idx + 1] = temp;
                        this.activeCountryIdx = idx + 1;
                    }

                    this.model.addAttributes({ 'data-country': ids.join(',') });
                    this.renderView();

                    // Re-render Trait Manager if open
                    try {
                        const tr = this.model.getTrait('data-country');
                        if (tr && tr.view && typeof tr.view.render === 'function') {
                            tr.view.render();
                        }
                    } catch (_) {}
                });
            });

            // Open Settings button
            this.el.querySelectorAll('.js-open-settings').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    editor.select(this.model);
                    try {
                        const pn = editor.Panels;
                        const btnTm = pn ? pn.getButton('views', 'open-tm') : null;
                        if (btnTm) btnTm.set('active', true);
                        else editor.runCommand('open-tm');
                    } catch (_) {}
                });
            });

            // Direct inline editing of headings & subheading
            this.el.querySelectorAll('[data-bind-trait]').forEach(inputEl => {
                inputEl.addEventListener('blur', () => {
                    const traitName = inputEl.getAttribute('data-bind-trait');
                    const val = inputEl.innerText.trim();
                    this.model.addAttributes({ [traitName]: val });
                });
                inputEl.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && inputEl.tagName !== 'P') {
                        e.preventDefault();
                        inputEl.blur();
                    }
                });
            });
        }
    }
});

bm.add('safaris-tours-dynamic', {
    label: '🌍 Dynamic Tours',
    category: window.__('Safaris Blocks'),
    attributes: { class: 'fa-solid fa-earth-africa' },
    content: `
<section class="trips-section py-16 md:py-24 bg-[#0d1611] text-[#e8eee9] relative overflow-hidden" id="trips">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div data-gjs-type="tours-listing" class="tours-listing" data-category="all" data-tour-type="all" data-country="all" data-limit="4" data-layout="auto">
      ${buildToursListingPreviewHtml({
          'data-category': 'all',
          'data-tour-type': 'all',
          'data-country': 'all',
          'data-limit': '4',
          'data-layout': 'auto'
      }, 0)}
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
               <div class="text-center py-10 bg-[#EDF2EA] border border-dashed border-[#455537]/40 rounded-[3px] p-6" style="pointer-events: none;">
                  <p class="text-[#455537] font-bold text-base">🚌 Dynamic Group Tours Listing</p>
                  <p class="text-sm text-[#354329] mt-1">Category: <span class="font-bold">${category}</span> | Limit: <span class="font-bold">${limit}</span></p>
                  <p class="text-[10px] text-[#CA9A2E] mt-2 uppercase tracking-wider font-semibold">Will load live group tours from the database on the frontend</p>
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
      <a href="/group-tours" class="hidden md:inline-flex items-center text-[#455537] font-bold hover:text-[#354329]">
        View All Group Tours
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
      </a>
    </div>

    <!-- Group Tours Listing Container -->
    <div data-gjs-type="group-tours-listing" class="group-tours-listing" data-group-category="all" data-limit="3">
       <div class="text-center py-10 bg-[#EDF2EA] border border-dashed border-[#455537]/40 rounded-[3px] p-6" style="pointer-events: none;">
          <p class="text-[#455537] font-bold text-base">🚌 Dynamic Group Tours Listing</p>
          <p class="text-sm text-[#354329] mt-1">Category: <span class="font-bold">all</span> | Limit: <span class="font-bold">3</span></p>
          <p class="text-[10px] text-[#CA9A2E] mt-2 uppercase tracking-wider font-semibold">Will load live group tours from the database on the frontend</p>
       </div>
    </div>
  </div>
</section>`,
});

bm.add('safaris-start-planning', {
    label: '📋 Start Planning',
    category: window.__('Safaris Blocks'),
    content: `
<section class="request-section py-20 bg-[#faf8f5] dark:bg-[#121c15] text-gray-800 dark:text-gray-100 relative overflow-hidden" id="contact" data-gjs-type="default">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 request-inner">
    <div class="flex flex-col justify-center">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#CA9A2E]/10 border border-[#CA9A2E]/25 text-[#CA9A2E] text-xs font-bold tracking-widest uppercase mb-4 self-start" data-gjs-type="text">
        <span>✦</span> Start Planning
      </div>
      <h2 class="request-heading text-4xl sm:text-5xl lg:text-[3.25rem] font-extrabold text-gray-900 dark:text-white leading-[1.08] tracking-tight mb-6" style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; letter-spacing: -0.025em;" data-gjs-type="text">
        Let us be your<br>guide to the<br><span style="color: #c05621;" class="wonders-highlight text-[#c05621]">wonders</span> of<br>Tanzania
      </h2>
      <p class="request-desc text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-normal mb-8 max-w-xl" data-gjs-type="text">
        Fargin Safaris is a tour operator specializing exclusively in Tanzania adventures, offering a variety of travel experiences that highlight the rich culture, diverse wildlife, and stunning landscapes of Tanzania.
      </p>

      <ul class="why-list space-y-3.5 text-text-mid">
        <li class="flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
          <div class="why-check w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0" style="color: var(--brand-primary, #CA9A2E); background-color: color-mix(in srgb, var(--brand-primary, #CA9A2E) 12%, transparent);">✓</div>
          <span data-gjs-type="text">All trips 100% customisable to your pace &amp; budget</span>
        </li>
        <li class="flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
          <div class="why-check w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0" style="color: var(--brand-primary, #CA9A2E); background-color: color-mix(in srgb, var(--brand-primary, #CA9A2E) 12%, transparent);">✓</div>
          <span data-gjs-type="text">Response within 24 hours from our local team</span>
        </li>
        <li class="flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
          <div class="why-check w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0" style="color: var(--brand-primary, #CA9A2E); background-color: color-mix(in srgb, var(--brand-primary, #CA9A2E) 12%, transparent);">✓</div>
          <span data-gjs-type="text">Best price guarantee &amp; transparent pricing</span>
        </li>
      </ul>
    </div>

    <form id="start-planning-form" class="request-form bg-white dark:bg-[#18231c] rounded-3xl p-8 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-stone-200/80 dark:border-white/10 relative">
      <div class="mb-6">
        <div class="text-xs font-bold tracking-widest uppercase mb-1" style="color: var(--brand-primary, #CA9A2E);" data-gjs-type="text">Free Quote</div>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">Request a Proposal</h3>
      </div>
      <div class="form-row grid grid-cols-2 gap-4 mb-4">
        <div class="form-group">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5" data-gjs-type="text">Adults</label>
          <input type="number" name="adults" min="1" max="50" value="1" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-[#CA9A2E] focus:outline-none">
        </div>
        <div class="form-group">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5" data-gjs-type="text">Children</label>
          <input type="number" name="children" min="0" max="50" value="0" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-[#CA9A2E] focus:outline-none">
        </div>
      </div>
      <div class="form-row grid grid-cols-2 gap-4 mb-4">
        <div class="form-group">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5" data-gjs-type="text">Arrival Date</label>
          <input type="date" name="arrival_date" required class="w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-[#CA9A2E] focus:outline-none">
        </div>
        <div class="form-group">
          <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5" data-gjs-type="text">Trip Type</label>
          <select name="trip_type" class="dynamic-tour-types w-full px-4 py-3 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-[#CA9A2E] focus:outline-none" required>
            <option>Loading...</option>
          </select>
        </div>
      </div>
      <div class="form-group mb-6">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5" data-gjs-type="text">Your Email</label>
        <input type="email" name="email" placeholder="you@example.com" required class="w-full px-4 py-3.5 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-[#CA9A2E] focus:outline-none">
      </div>
      <button type="submit" class="w-full py-4 bg-[#ea963e] hover:bg-[#d97706] text-white font-bold rounded-xl shadow-lg shadow-[#ea963e]/25 hover:shadow-[#ea963e]/40 transform hover:-translate-y-0.5 transition-all text-sm tracking-wide uppercase cursor-pointer border-none block text-center">
        <span data-gjs-type="text">Get My Free Proposal →</span>
      </button>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center font-light" data-gjs-type="text">No strings attached. We'll design your trip at no cost or commitment.</p>
    </form>
  </div>
</section>
    `,
});

bm.add('safaris-wonders-tanzania', {
    label: ' Wonders of Tanzania',
    category: window.__('Safaris Blocks'),
    content: `
<section class="py-24 bg-white dark:bg-[#0e1611] text-gray-900 dark:text-gray-100 relative overflow-hidden" data-gjs-type="default">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
      
      <!-- Left Column: Heading & Description -->
      <div class="lg:col-span-5 sticky top-24">
        <h2 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-bold text-gray-900 dark:text-white leading-[1.1] tracking-tight mb-8" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
          Let us be your<br>guide to the<br><span style="color: #c05621;">wonders</span> of<br>Tanzania
        </h2>
        <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light mb-8" data-gjs-type="text">
          Fargin Safaris is a tour operator specializing exclusively in Tanzania adventures, offering a variety of travel experiences that highlight the rich culture, diverse wildlife, and stunning landscapes of Tanzania.
        </p>
      </div>

      <!-- Right Column: 01 to 06 Numbered List -->
      <div class="lg:col-span-7 divide-y divide-stone-200 dark:divide-white/10 border-t border-b border-stone-200 dark:border-white/10">
        
        <!-- 01 -->
        <div class="py-6 flex items-start gap-6 group hover:bg-stone-50/50 dark:hover:bg-white/[0.02] transition px-2 rounded-xl">
          <span class="text-lg font-bold text-[#ea963e] shrink-0 w-8" data-gjs-type="text">01</span>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1.5" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
              Tailor-Made Tanzania Safaris
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-light leading-relaxed" data-gjs-type="text">
              Customized, private safari experiences tailored to individual preferences or group sizes.
            </p>
          </div>
        </div>

        <!-- 02 -->
        <div class="py-6 flex items-start gap-6 group hover:bg-stone-50/50 dark:hover:bg-white/[0.02] transition px-2 rounded-xl">
          <span class="text-lg font-bold text-[#ea963e] shrink-0 w-8" data-gjs-type="text">02</span>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1.5" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
              Authentic Cultural Encounters
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-light leading-relaxed" data-gjs-type="text">
              Visit traditional villages and learn about their customs: Maasai villages, Chagga and Hadzabe tribes.
            </p>
          </div>
        </div>

        <!-- 03 -->
        <div class="py-6 flex items-start gap-6 group hover:bg-stone-50/50 dark:hover:bg-white/[0.02] transition px-2 rounded-xl">
          <span class="text-lg font-bold text-[#ea963e] shrink-0 w-8" data-gjs-type="text">03</span>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1.5" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
              Conquer the Roof of Africa
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-light leading-relaxed" data-gjs-type="text">
              Guided treks to the summit of Mount Kilimanjaro, the highest peak in Africa.
            </p>
          </div>
        </div>

        <!-- 04 -->
        <div class="py-6 flex items-start gap-6 group hover:bg-stone-50/50 dark:hover:bg-white/[0.02] transition px-2 rounded-xl">
          <span class="text-lg font-bold text-[#ea963e] shrink-0 w-8" data-gjs-type="text">04</span>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1.5" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
              Tropical Escapes — Zanzibar
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-light leading-relaxed" data-gjs-type="text">
              Beach holidays in Zanzibar, including Stone Town, spice farms and Mafia Island.
            </p>
          </div>
        </div>

        <!-- 05 -->
        <div class="py-6 flex items-start gap-6 group hover:bg-stone-50/50 dark:hover:bg-white/[0.02] transition px-2 rounded-xl">
          <span class="text-lg font-bold text-[#ea963e] shrink-0 w-8" data-gjs-type="text">05</span>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1.5" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
              Luxury &amp; Wilderness Camping
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-light leading-relaxed" data-gjs-type="text">
              Accommodation in high-end lodges and tented camps in prime wildlife areas.
            </p>
          </div>
        </div>

        <!-- 06 -->
        <div class="py-6 flex items-start gap-6 group hover:bg-stone-50/50 dark:hover:bg-white/[0.02] transition px-2 rounded-xl">
          <span class="text-lg font-bold text-[#ea963e] shrink-0 w-8" data-gjs-type="text">06</span>
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1.5" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
              Ultimate Outdoor Adventures
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-light leading-relaxed" data-gjs-type="text">
              Explore the iconic Serengeti National Park, Ngorongoro Crater, Tarangire and Lake Manyara.
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
    `,
});

bm.add('safaris-start-planning-btn', {
    label: '📋 Start Planning Button',
    category: window.__('Safaris Blocks'),
    content: `
      <div class="p-5 text-center">
        <button data-gjs-type="text" class="btn-primary start-planning-btn-trigger bg-brand-primary text-white px-8 py-3 rounded-[3px] text-xs font-semibold tracking-[0.1em] uppercase cursor-pointer no-underline inline-block transition-all duration-250 border-none shadow-[var(--shadow-gold)]">
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
      <div class="section-label">Why Choose Us</div>
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
    label: '📖 About Story / What We Do',
    category: window.__('Safaris Blocks'),
    content: `
<section data-gjs-type="safaris-about-block" class="safaris-about-block">
  <div class="wrap">
    <div class="safaris-about-grid">
      <!-- Left Copy Column -->
      <div>
        <div class="eyebrow" data-gjs-type="text">About Mwangwi Safaris</div>
        <h2 data-gjs-type="text">What We Do</h2>
        <p class="lede" data-gjs-type="text">At Mwangwi Safaris, we organize safe, comfortable, and enjoyable Kilimanjaro climbing adventures and wildlife safaris throughout Tanzania.</p>
        
        <p data-gjs-type="text">We understand that a trip to Tanzania is not just another vacation; it’s a special event that requires careful planning and significant investment. With this in mind, we’ve established a dedicated adventure planning team. Our team members are highly experienced in organizing trips across Tanzania and have an intimate knowledge of the country’s national parks, conservation areas, and hidden gems. Our consultants will work with you to select the perfect itinerary, ensuring you experience the very best that Tanzania has to offer.</p>
        
        <p data-gjs-type="text"><strong>Mwangwi Safaris</strong> prides itself on providing personalized and high-quality service, with a strong commitment to sustainable tourism. The company works closely with local communities and conservation organizations to ensure that their tours contribute positively to the environment and local economies. By focusing exclusively on these areas, Mwangwi Safaris ensures that each journey is expertly managed and delivers a truly enriching experience.</p>
        
        <p data-gjs-type="text">Our team is dedicated to providing exceptional customer service, and we work closely with our clients to tailor our safari packages to their unique needs and preferences. We believe that every safari experience should be as unique and unforgettable as the individual who embarks on it.</p>
        
        <p style="font-weight: 600; color: var(--forest); margin-top: 24px;" data-gjs-type="text">Thank you for considering Mwangwi Safaris for your Tanzanian safari adventure. We look forward to sharing the wonder and beauty of Tanzania with you.</p>

        <div class="safaris-about-actions">
          <a href="/tours" class="safaris-about-btn-primary" data-gjs-type="text">Explore Our Safaris <i class="fa-solid fa-arrow-right"></i></a>
          <a href="/contact" class="safaris-about-btn-ghost" data-gjs-type="text">Plan Custom Itinerary</a>
        </div>
      </div>

      <!-- Right Visual & Mission Column -->
      <div class="safaris-about-visual">
        <div class="safaris-about-img-wrap">
          <img src="/storage/4/about-us-1791623834.webp" alt="Mwangwi Safaris Expert Guide" />
        </div>

        <div class="safaris-about-mission-card">
          <div class="safaris-about-mission-tag">
            <i class="fa-solid fa-compass"></i> <span data-gjs-type="text">Our Mission</span>
          </div>
          <h3 data-gjs-type="text">“To make Tanzania a memory-making destination for every adventurer.”</h3>
        </div>

        <div class="safaris-about-stats">
          <div class="safaris-about-stat-item">
            <span class="safaris-about-stat-val" data-gjs-type="text">100%</span>
            <span class="safaris-about-stat-lbl" data-gjs-type="text">Tailored Safaris</span>
          </div>
          <div class="safaris-about-stat-item">
            <span class="safaris-about-stat-val" data-gjs-type="text">Top Rated</span>
            <span class="safaris-about-stat-lbl" data-gjs-type="text">Local Expertise</span>
          </div>
          <div class="safaris-about-stat-item">
            <span class="safaris-about-stat-val" data-gjs-type="text">Eco-First</span>
            <span class="safaris-about-stat-lbl" data-gjs-type="text">Community Care</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Core Pillars Grid -->
    <div class="safaris-about-pillars">
      <div class="safaris-about-pillar-card">
        <div class="safaris-about-pillar-num" data-gjs-type="text">01</div>
        <h4 data-gjs-type="text">Dedicated Planning Team</h4>
        <p data-gjs-type="text">Specialists with intimate knowledge of Tanzania’s national parks, wildlife migrations, and routes to craft your ideal journey.</p>
      </div>
      <div class="safaris-about-pillar-card">
        <div class="safaris-about-pillar-num" data-gjs-type="text">02</div>
        <h4 data-gjs-type="text">Sustainable Tourism</h4>
        <p data-gjs-type="text">Collaborating with local Tanzanian communities and conservation projects so every trip protects nature and empowers locals.</p>
      </div>
      <div class="safaris-about-pillar-card">
        <div class="safaris-about-pillar-num" data-gjs-type="text">03</div>
        <h4 data-gjs-type="text">Personalized Service</h4>
        <p data-gjs-type="text">Attentive care from arrival to departure, customizing pacing, lodging, and activities to your personal expectations.</p>
      </div>
    </div>
  </div>
</section>`,
});

bm.add('safaris-about-block', {
    label: '★ About Us Block',
    category: window.__('Safaris Blocks'),
    content: `
<section data-gjs-type="safaris-about-block" class="safaris-about-block">
  <div class="wrap">
    <div class="safaris-about-grid">
      <!-- Left Copy Column -->
      <div>
        <div class="eyebrow" data-gjs-type="text">About Mwangwi Safaris</div>
        <h2 data-gjs-type="text">What We Do</h2>
        <p class="lede" data-gjs-type="text">At Mwangwi Safaris, we organize safe, comfortable, and enjoyable Kilimanjaro climbing adventures and wildlife safaris throughout Tanzania.</p>
        
        <p data-gjs-type="text">We understand that a trip to Tanzania is not just another vacation; it’s a special event that requires careful planning and significant investment. With this in mind, we’ve established a dedicated adventure planning team. Our team members are highly experienced in organizing trips across Tanzania and have an intimate knowledge of the country’s national parks, conservation areas, and hidden gems. Our consultants will work with you to select the perfect itinerary, ensuring you experience the very best that Tanzania has to offer.</p>
        
        <p data-gjs-type="text"><strong>Mwangwi Safaris</strong> prides itself on providing personalized and high-quality service, with a strong commitment to sustainable tourism. The company works closely with local communities and conservation organizations to ensure that their tours contribute positively to the environment and local economies. By focusing exclusively on these areas, Mwangwi Safaris ensures that each journey is expertly managed and delivers a truly enriching experience.</p>
        
        <p data-gjs-type="text">Our team is dedicated to providing exceptional customer service, and we work closely with our clients to tailor our safari packages to their unique needs and preferences. We believe that every safari experience should be as unique and unforgettable as the individual who embarks on it.</p>
        
        <p style="font-weight: 600; color: var(--forest); margin-top: 24px;" data-gjs-type="text">Thank you for considering Mwangwi Safaris for your Tanzanian safari adventure. We look forward to sharing the wonder and beauty of Tanzania with you.</p>

        <div class="safaris-about-actions">
          <a href="/tours" class="safaris-about-btn-primary" data-gjs-type="text">Explore Our Safaris <i class="fa-solid fa-arrow-right"></i></a>
          <a href="/contact" class="safaris-about-btn-ghost" data-gjs-type="text">Plan Custom Itinerary</a>
        </div>
      </div>

      <!-- Right Visual & Mission Column -->
      <div class="safaris-about-visual">
        <div class="safaris-about-img-wrap">
          <img src="/storage/4/about-us-1791623834.webp" alt="Mwangwi Safaris Expert Guide" />
        </div>

        <div class="safaris-about-mission-card">
          <div class="safaris-about-mission-tag">
            <i class="fa-solid fa-compass"></i> <span data-gjs-type="text">Our Mission</span>
          </div>
          <h3 data-gjs-type="text">“To make Tanzania a memory-making destination for every adventurer.”</h3>
        </div>

        <div class="safaris-about-stats">
          <div class="safaris-about-stat-item">
            <span class="safaris-about-stat-val" data-gjs-type="text">100%</span>
            <span class="safaris-about-stat-lbl" data-gjs-type="text">Tailored Safaris</span>
          </div>
          <div class="safaris-about-stat-item">
            <span class="safaris-about-stat-val" data-gjs-type="text">Top Rated</span>
            <span class="safaris-about-stat-lbl" data-gjs-type="text">Local Expertise</span>
          </div>
          <div class="safaris-about-stat-item">
            <span class="safaris-about-stat-val" data-gjs-type="text">Eco-First</span>
            <span class="safaris-about-stat-lbl" data-gjs-type="text">Community Care</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Core Pillars Grid -->
    <div class="safaris-about-pillars">
      <div class="safaris-about-pillar-card">
        <div class="safaris-about-pillar-num" data-gjs-type="text">01</div>
        <h4 data-gjs-type="text">Dedicated Planning Team</h4>
        <p data-gjs-type="text">Specialists with intimate knowledge of Tanzania’s national parks, wildlife migrations, and routes to craft your ideal journey.</p>
      </div>
      <div class="safaris-about-pillar-card">
        <div class="safaris-about-pillar-num" data-gjs-type="text">02</div>
        <h4 data-gjs-type="text">Sustainable Tourism</h4>
        <p data-gjs-type="text">Collaborating with local Tanzanian communities and conservation projects so every trip protects nature and empowers locals.</p>
      </div>
      <div class="safaris-about-pillar-card">
        <div class="safaris-about-pillar-num" data-gjs-type="text">03</div>
        <h4 data-gjs-type="text">Personalized Service</h4>
        <p data-gjs-type="text">Attentive care from arrival to departure, customizing pacing, lodging, and activities to your personal expectations.</p>
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
    <span class="inline-block py-1.5 px-4 rounded-full bg-[#455537]/25 text-[#CA9A2E] border border-[#CA9A2E]/30 text-xs font-bold tracking-widest uppercase mb-6">Kilimanjaro Group Climbs</span>
    <h1 class="text-4xl md:text-6xl font-bold font-brand mb-6 leading-tight text-white drop-shadow-lg animate-fade-in">Join Our Kilimanjaro Group Climbs</h1>
    <p class="text-base md:text-lg text-gray-250 mb-10 max-w-3xl mx-auto leading-relaxed drop-shadow">
      Kilimanjaro group climbs depart regularly throughout the month. Joining one of these climbs is a wonderful way to experience the grandeur of Africa’s highest peak while connecting with fellow adventurers from around the world. Securing your spot is simple: a 20% deposit is all that’s required. If none of the listed dates align with your plans, simply reach out to arrange a custom departure.
    </p>
    
    <div class="flex justify-center">
      <a href="#quote-form" class="inline-flex bg-[#455537] hover:bg-[#354329] active:bg-[#25301c] text-white font-bold py-3.5 px-8 rounded-[3px] transition-all duration-300 text-base shadow-xl hover:-translate-y-1">
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
  <div class="absolute inset-0 bg-[#455537]/10"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <span class="inline-block py-1 px-3 rounded-full bg-[#455537]/25 text-[#CA9A2E] border border-[#CA9A2E]/30 text-sm font-bold tracking-widest uppercase mb-4">What Drives Us</span>
      <h2 class="text-4xl md:text-5xl font-bold font-brand mb-4">Our Core Values</h2>
      <p class="text-gray-400 max-w-2xl mx-auto text-lg">The principles that guide every safari, trek, and connection we make.</p>
    </div>
    
    <div class="flex flex-wrap justify-center items-center gap-8">
      <!-- Value 1 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-[#455537]/30 text-[#CA9A2E] rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Human Connection</h3>
        <p class="text-gray-400 leading-relaxed">Travel isn’t about buses or itineraries. It’s about people. We treat every guest like family and every community with respect.</p>
      </div>
      
      <!-- Value 2 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-[#455537]/30 text-[#CA9A2E] rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Authenticity</h3>
        <p class="text-gray-400 leading-relaxed">We take you beyond the postcards to the real, raw, and beautiful heart of Tanzania.</p>
      </div>

      <!-- Value 3 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-[#455537]/30 text-[#CA9A2E] rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Integrity</h3>
        <p class="text-gray-400 leading-relaxed">No hidden costs. No false promises. Just honest, transparent, and thoughtful service.</p>
      </div>

      <!-- Value 4 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-[#455537]/30 text-[#CA9A2E] rounded-[3px] flex items-center justify-center mb-6">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-3">Excellence</h3>
        <p class="text-gray-400 leading-relaxed">From safari vehicles to mountain gear, everything we offer is carefully selected for comfort, safety, and quality.</p>
      </div>

      <!-- Value 5 -->
      <div class="w-full md:w-[calc(50%-16px)] lg:w-[calc(33.333%-22px)] bg-gray-800/50 backdrop-blur-sm p-8 rounded-[3px] border border-gray-700/50 hover:bg-gray-800 transition-colors">
        <div class="w-14 h-14 bg-[#455537]/30 text-[#CA9A2E] rounded-[3px] flex items-center justify-center mb-6">
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
<section class="safaris-contact-block pt-20 pb-0 bg-gradient-to-b from-stone-50 via-white to-stone-100 dark:from-[#0d1410] dark:via-[#131c16] dark:to-[#0d1410] text-gray-800 dark:text-gray-100 relative overflow-hidden" data-gjs-type="default">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

    <!-- Section Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#CA9A2E]/10 border border-[#CA9A2E]/25 text-[#CA9A2E] text-xs font-bold tracking-widest uppercase mb-4" data-gjs-type="text">
        <span></span> Inquire &amp; Connect
      </div>
      <h2 class="text-4xl sm:text-5xl font-bold tracking-tight mb-5" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif; line-height: 1.15;" data-gjs-type="text">
        Request a Program
      </h2>
      <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light" data-gjs-type="text">
        We are always here to help. Our team is ready to assist you with any inquiries and help you create an unforgettable experience.
      </p>
      <div class="w-24 h-1 bg-gradient-to-r from-transparent via-[#CA9A2E] to-transparent mx-auto mt-6 rounded-full"></div>
    </div>

    <!-- Main 2-Column Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
      
      <!-- Left Column: Inquiry Form Card -->
      <div class="lg:col-span-7 bg-white dark:bg-[#18231c] rounded-3xl p-8 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-stone-200/80 dark:border-white/10 relative">
        <div class="mb-8">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
            Start Your Journey
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400" data-gjs-type="text">
            Fill out the details below and our safari specialists will get back to you within 24 hours.
          </p>
        </div>

        <form class="space-y-6" onsubmit="event.preventDefault();">
          <!-- Full Name -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" data-gjs-type="text">Your Name *</label>
            <input type="text" placeholder="Full Name" required class="w-full px-4 py-3.5 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#CA9A2E] focus:border-transparent transition text-sm">
          </div>

          <!-- Mobile & Email in 2 columns -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" data-gjs-type="text">Mobile Phone *</label>
              <input type="tel" placeholder="+255 ... or international code" required class="w-full px-4 py-3.5 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#CA9A2E] focus:border-transparent transition text-sm">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" data-gjs-type="text">Email Address *</label>
              <input type="email" placeholder="name@example.com" required class="w-full px-4 py-3.5 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#CA9A2E] focus:border-transparent transition text-sm">
            </div>
          </div>

          <!-- Message Field -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" data-gjs-type="text">Message / Tour Preferences</label>
            <textarea rows="4" placeholder="Tell us about your dream safari, estimated travel dates, group size, or questions..." class="w-full px-4 py-3.5 rounded-xl border border-stone-200 dark:border-white/15 bg-stone-50/50 dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#CA9A2E] focus:border-transparent transition text-sm"></textarea>
          </div>

          <!-- Communication Preference -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2.5" data-gjs-type="text">I Prefer to be Contacted Via:</label>
            <div class="flex flex-wrap gap-4 text-sm">
              <label class="inline-flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg bg-stone-100 dark:bg-white/10 hover:bg-[#CA9A2E]/10 transition">
                <input type="radio" name="contact_pref" value="email" checked class="text-[#CA9A2E] focus:ring-[#CA9A2E]">
                <span class="text-gray-800 dark:text-gray-200 font-medium">✉️ Email</span>
              </label>
              <label class="inline-flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg bg-stone-100 dark:bg-white/10 hover:bg-[#CA9A2E]/10 transition">
                <input type="radio" name="contact_pref" value="phone" class="text-[#CA9A2E] focus:ring-[#CA9A2E]">
                <span class="text-gray-800 dark:text-gray-200 font-medium">📞 Phone</span>
              </label>
              <label class="inline-flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg bg-stone-100 dark:bg-white/10 hover:bg-[#CA9A2E]/10 transition">
                <input type="radio" name="contact_pref" value="whatsapp" class="text-[#CA9A2E] focus:ring-[#CA9A2E]">
                <span class="text-gray-800 dark:text-gray-200 font-medium">💬 WhatsApp</span>
              </label>
            </div>
          </div>

          <!-- Privacy & Submit -->
          <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-500 dark:text-gray-400" data-gjs-type="text">
              By clicking 'Send Request', you agree to our <a href="/privacy" class="underline hover:text-[#CA9A2E]">Privacy Policy</a>.
            </p>
            <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-[#ea963e] hover:bg-[#d97706] text-white font-bold rounded-xl shadow-lg shadow-[#ea963e]/25 hover:shadow-[#ea963e]/40 transform hover:-translate-y-0.5 transition-all text-sm tracking-wide uppercase inline-flex items-center justify-center gap-2">
              <span data-gjs-type="text">Send Request</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </form>
      </div>

      <!-- Right Column: Information & Direct Contacts -->
      <div class="lg:col-span-5 space-y-8">
        
        <!-- Fargin Safaris Advantage Card -->
        <div class="bg-gradient-to-br from-[#1d2b22] to-[#121c15] text-white p-8 rounded-3xl shadow-xl border border-white/10 relative overflow-hidden">
          <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-[#CA9A2E]/15 rounded-full blur-2xl pointer-events-none"></div>
          <div class="inline-flex items-center gap-1.5 text-[#CA9A2E] text-xs font-bold tracking-widest uppercase mb-4" data-gjs-type="text">
            <span>★</span> About Fargin Safaris
          </div>
          <h3 class="text-2xl font-bold mb-4" style="font-family: 'Baskervville', 'Playfair Display', Georgia, serif;" data-gjs-type="text">
            Dedicated Exclusively to Tanzania
          </h3>
          <p class="text-white/80 text-sm leading-relaxed mb-4 font-light" data-gjs-type="text">
            As a company dedicated exclusively to Tanzania, we are proud to be one of the best in the country’s tourism sector. When you travel with Fargin Safaris, you can expect transparent pricing with no intermediaries or hidden costs.
          </p>
          <p class="text-white/80 text-sm leading-relaxed font-light" data-gjs-type="text">
            Our team includes the most experienced Kilimanjaro guides and safari drivers in the region, and we are committed to providing personalized customer service.
          </p>
        </div>

        <!-- Contact Channels Section -->
        <div>
          <h4 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4" data-gjs-type="text">
            For more information or to plan your adventure:
          </h4>

          <div class="space-y-4">
            <!-- Address Card -->
            <div class="flex items-start gap-4 p-4 rounded-2xl bg-white dark:bg-[#18231c] border border-stone-200/80 dark:border-white/10 shadow-sm hover:shadow-md transition">
              <div class="w-12 h-12 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center shrink-0" style="background-color: color-mix(in srgb, var(--brand-primary, #CA9A2E) 12%, transparent); color: var(--brand-primary, #CA9A2E);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              </div>
              <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-brand-primary mb-0.5" style="color: var(--brand-primary, #CA9A2E);" data-gjs-type="text">Head Office</span>
                <p class="text-sm font-semibold text-gray-800 dark:text-white" data-gjs-type="text">
                  Fargin Safaris P.O.Box 10344, Arusha Tanzania
                </p>
              </div>
            </div>

            <!-- Phone Card -->
            <div class="flex items-start gap-4 p-4 rounded-2xl bg-white dark:bg-[#18231c] border border-stone-200/80 dark:border-white/10 shadow-sm hover:shadow-md transition">
              <div class="w-12 h-12 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center shrink-0" style="background-color: color-mix(in srgb, var(--brand-primary, #CA9A2E) 12%, transparent); color: var(--brand-primary, #CA9A2E);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
              </div>
              <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-brand-primary mb-0.5" style="color: var(--brand-primary, #CA9A2E);" data-gjs-type="text">Direct Phone &amp; WhatsApp</span>
                <a href="tel:+255795618776" class="text-base font-bold text-gray-900 dark:text-white hover:text-brand-primary transition" style="--hover-color: var(--brand-primary, #CA9A2E);" data-gjs-type="text">
                  +255 795 618 776
                </a>
              </div>
            </div>

            <!-- Email Card -->
            <div class="flex items-start gap-4 p-4 rounded-2xl bg-white dark:bg-[#18231c] border border-stone-200/80 dark:border-white/10 shadow-sm hover:shadow-md transition">
              <div class="w-12 h-12 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center shrink-0" style="background-color: color-mix(in srgb, var(--brand-primary, #CA9A2E) 12%, transparent); color: var(--brand-primary, #CA9A2E);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              </div>
              <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-brand-primary mb-0.5" style="color: var(--brand-primary, #CA9A2E);" data-gjs-type="text">Email Inquiries</span>
                <div class="flex flex-col gap-0.5">
                  <a href="mailto:info@farginsafaris.com" class="text-sm font-semibold text-gray-800 dark:text-white hover:text-brand-primary transition" style="--hover-color: var(--brand-primary, #CA9A2E);" data-gjs-type="text">
                    info@farginsafaris.com
                  </a>
                  <a href="mailto:sales@farginsafaris.com" class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-brand-primary transition" style="--hover-color: var(--brand-primary, #CA9A2E);" data-gjs-type="text">
                    sales@farginsafaris.com
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Social Media Icons -->
        <div class="pt-2">
          <span class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3" data-gjs-type="text">Follow Fargin Safaris</span>
          <div class="flex items-center gap-3">
            <a href="https://www.instagram.com/farginsafaris_/" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-stone-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 hover:bg-[#CA9A2E] hover:text-white flex items-center justify-center transition shadow-sm" title="Instagram">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-stone-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 hover:bg-[#CA9A2E] hover:text-white flex items-center justify-center transition shadow-sm" title="Facebook">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-stone-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 hover:bg-[#CA9A2E] hover:text-white flex items-center justify-center transition shadow-sm" title="X (Twitter)">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-stone-100 dark:bg-white/10 text-gray-700 dark:text-gray-200 hover:bg-[#CA9A2E] hover:text-white flex items-center justify-center transition shadow-sm" title="YouTube">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Full-Width Location Map (Edge-to-Edge, No Titles) -->
  <div class="w-full h-[450px] md:h-[500px] mt-16 relative overflow-hidden border-t border-stone-200/80 dark:border-white/10" data-gjs-type="default">
    <iframe src="https://maps.google.com/maps?q=Arusha%2C%20Tanzania&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="100%" height="100%" style="border:0; width:100%; height:100%; display:block;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
        btn.style.background = isCurrent ? '#455537' : 'transparent';
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

        if (data.builder_data && data.builder_data.components && (!Array.isArray(data.builder_data.components) || data.builder_data.components.length > 0)) {
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
        <p class="review-text">"An absolutely unforgettable honeymoon. The team handled everything — from the airport pickup to sunset game drives. Our guide Issa felt like family within minutes. Seeing the Big Five in Ngorongoro was pure magic."</p>
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
        <p class="review-text">"We traveled as a family of five with three kids. The team tailored every single detail — pace, lodges, activities. Our children saw lions, elephants, and a cheetah! This trip changed all of us."</p>
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

const TH_STYLE = "padding: 14px 18px; text-align: left; color: #CA9A2E; font-family: 'Playfair Display', Georgia, serif; font-size: 0.85rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; border: none;";
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
  <!-- Sticky Top Navigation Bar -->
  <header class="zanzibar-nav">
    <div class="zanzibar-nav-row">
      <nav class="zanzibar-links">
        <a href="#zanzibar-why"><i class="fa-solid fa-circle-info"></i> <span data-gjs-type="text">Why Zanzibar</span></a>
        <a href="#zanzibar-coast"><i class="fa-solid fa-compass"></i> <span data-gjs-type="text">The Coast</span></a>
        <a href="#zanzibar-stonetown"><i class="fa-solid fa-city"></i> <span data-gjs-type="text">Stone Town</span></a>
        <a href="#zanzibar-experiences"><i class="fa-solid fa-umbrella-beach"></i> <span data-gjs-type="text">Experiences</span></a>
        <a href="#zanzibar-when"><i class="fa-solid fa-calendar-days"></i> <span data-gjs-type="text">When To Go</span></a>
        <a href="#zanzibar-plan" class="zanzibar-nav-cta"><i class="fa-solid fa-paper-plane"></i> <span data-gjs-type="text">Plan My Trip</span></a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="zanzibar-hero">
    <div class="wrap">
      <div class="zanzibar-hero-grid">
        <div>
          <div class="eyebrow" data-gjs-type="text">Mwangwi Safaris · Zanzibar Island Escape</div>
          <h1 data-gjs-type="text">Where the savannah gives way to <em>paradise.</em></h1>
          <p class="lede" data-gjs-type="text">Zanzibar truly is paradise, offering an idyllic blend of rich history, stunning nature, and serene white-sand beaches. It’s an ideal destination for both relaxation and adventure seekers alike. Exploring Zanzibar is an immersive sensory journey, from the bustling markets of Stone Town to the tranquil turquoise shallows and diverse cuisine. Mwangwi Safaris builds the seamless route that takes you from thrilling game drives on the mainland to complete coastal relaxation.</p>
          <div class="zanzibar-hero-ctas">
            <a class="zanzibar-btn-primary" href="#zanzibar-plan" data-gjs-type="text">Plan Your Zanzibar Escape</a>
            <a class="zanzibar-btn-ghost" href="#zanzibar-why" data-gjs-type="text">Why Visit Zanzibar</a>
          </div>
          <div class="zanzibar-stat-strip">
            <div><div class="zanzibar-stat-num" data-gjs-type="text">40+</div><div class="zanzibar-stat-label" data-gjs-type="text">islands in archipelago</div></div>
            <div><div class="zanzibar-stat-num" data-gjs-type="text">Top 10</div><div class="zanzibar-stat-label" data-gjs-type="text">world-class white beaches</div></div>
            <div><div class="zanzibar-stat-num" data-gjs-type="text">UNESCO</div><div class="zanzibar-stat-label" data-gjs-type="text">Stone Town World Heritage</div></div>
            <div><div class="zanzibar-stat-num" data-gjs-type="text">Jun–Feb</div><div class="zanzibar-stat-label" data-gjs-type="text">prime dry beach season</div></div>
          </div>
        </div>
        <div>
          <img src="https://farginsafaris.com/wp-content/uploads/2024/10/zanzibar-fargin-1024x683.webp" alt="Zanzibar White Sand Beach - Mwangwi Safaris" style="width: 100%; max-height: 520px; object-fit: cover; border-radius: 4px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); display: block;" />
        </div>
      </div>
    </div>
  </section>

  <!-- Why Zanzibar Section (The 4 Core Pillars) -->
  <section class="zanzibar-section" id="zanzibar-why">
    <div class="wrap">
      <div class="zanzibar-section-head">
        <div class="eyebrow" data-gjs-type="text">Why Visit Zanzibar</div>
        <h2 data-gjs-type="text">Four reasons this paradise earns a place on every Tanzania itinerary</h2>
        <p data-gjs-type="text">Zanzibar provides exceptional diving and snorkeling experiences, mouthwatering cuisine, and a relaxed atmosphere ideal for unwinding after your African safari. The island’s unique blend of cultures and stunning natural beauty makes it a truly unforgettable destination.</p>
      </div>
      <div class="zanzibar-why-grid">
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">01</div>
          <div>
            <h3 data-gjs-type="text">Stunning White Beaches</h3>
            <p data-gjs-type="text">Zanzibar boasts some of the world’s most beautiful beaches with powder-soft white sand and crystal-clear turquoise waters. Popular shores like Nungwi, Kendwa, and Paje offer idyllic settings for relaxation, swimming, and water sports.</p>
          </div>
        </div>
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">02</div>
          <div>
            <h3 data-gjs-type="text">Rich Stone Town History</h3>
            <p data-gjs-type="text">Zanzibar’s Stone Town, a UNESCO World Heritage Site, is a historic gem with narrow winding alleys, ancient coral-stone buildings, and vibrant markets reflecting centuries of trade, culture, and coastal Swahili civilization.</p>
          </div>
        </div>
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">03</div>
          <div>
            <h3 data-gjs-type="text">Unique Cultural Blend</h3>
            <p data-gjs-type="text">The island is an enchanting melting pot of cultures, with rich influences from Africa, Arabia, Persia, and India. This heritage is wonderfully evident in its aromatic cuisine, intricate architecture, and warm hospitality.</p>
          </div>
        </div>
        <div class="zanzibar-why-card">
          <div class="zanzibar-why-num" data-gjs-type="text">04</div>
          <div>
            <h3 data-gjs-type="text">Legendary Spice Tours</h3>
            <p data-gjs-type="text">Known worldwide as the “Spice Island” due to its heritage of producing cloves, nutmeg, cinnamon, and black pepper. Guided spice plantation tours offer a fascinating sensory insight into their cultivation and traditional uses.</p>
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
        <p data-gjs-type="text">Unguja, the main island, wears a different character on each side. Here is how the four coasts compare.</p>
      </div>
      <div class="zanzibar-compass-grid">
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">North</div>
          <h3 data-gjs-type="text">Nungwi &amp; Kendwa</h3>
          <p data-gjs-type="text">The island's liveliest shoreline, with tide-free crystal waters year-round, idyllic powder sands, oceanfront restaurants, and the easiest swimming conditions on Zanzibar.</p>
        </div>
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">East</div>
          <h3 data-gjs-type="text">Matemwe &amp; Pongwe</h3>
          <p data-gjs-type="text">A coral reef runs just offshore facing Mnemba Atoll, making this the island's premier base for snorkelling, scuba diving, and tranquil beachfront retreats.</p>
        </div>
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">South-East</div>
          <h3 data-gjs-type="text">Paje, Jambiani &amp; Bwejuu</h3>
          <p data-gjs-type="text">Steady Indian Ocean trade winds and soft sand draw kite-surfers, ocean lovers, and sailors to these authentic coastal fishing villages and crystal lagoons.</p>
        </div>
        <div class="zanzibar-coast-card">
          <div class="dir" data-gjs-type="text">South-West</div>
          <h3 data-gjs-type="text">Kizimkazi &amp; Stone Town</h3>
          <p data-gjs-type="text">Quiet and traditional, famous for dolphin safaris off Kizimkazi, protected marine sanctuaries in Menai Bay, and immediate access to historic Stone Town.</p>
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
          <div class="eyebrow" data-gjs-type="text">Stone Town Heritage</div>
          <h2 data-gjs-type="text">The ancient trading capital, alive with Swahili culture</h2>
          <p data-gjs-type="text">Most visitors begin their Zanzibar adventure in Stone Town, the island's UNESCO-designated cultural hub. Its narrow, winding labyrinth carries the enduring footprints of Arab, Persian, Indian, and African trade — intricately carved doorways, coral-stone mansions, and historic palace ruins.</p>
          <p data-gjs-type="text">The call to prayer, lively street chatter, and the rich scent of cloves, cardamom, and fresh seafood fill the air today. A stay here immerses you deep into the island’s culture, traditions, and coastal life before heading to the beach.</p>
          <div class="zanzibar-stone-list">
            <div><span class="mark"></span><span data-gjs-type="text">Wander through UNESCO-listed alleys, historic carved doors, and bustling Darajani spice markets.</span></div>
            <div><span class="mark"></span><span data-gjs-type="text">Explore the iconic Old Fort, House of Wonders, coastal gardens, and the former Sultan's Palace.</span></div>
            <div><span class="mark"></span><span data-gjs-type="text">Enjoy a guided walking and cultural tour to unpack Zanzibar's layered Swahili and merchant history.</span></div>
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
        <h2 data-gjs-type="text">Water sports, island excursions &amp; culinary journeys</h2>
        <p data-gjs-type="text">Guests have the opportunity to indulge in water sports, embark on spice tours, explore local villages, and witness breathtaking sunsets over the Indian Ocean.</p>
      </div>
      <div class="zanzibar-exp-grid">
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Culinary &amp; Culture</div><h3 data-gjs-type="text">Spice Farm Tour</h3><p data-gjs-type="text">Walk through working plantations and learn to recognise cloves, nutmeg, cinnamon, vanilla, and cardamom by touch and smell on Zanzibar's iconic Spice Island tour.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Wildlife Reserve</div><h3 data-gjs-type="text">Jozani Forest</h3><p data-gjs-type="text">Zanzibar's protected national park features ancient mahogany trees, coastal mangroves, and the only natural habitat of the rare, playful red colobus monkey.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">History &amp; Sea</div><h3 data-gjs-type="text">Prison Island Sanctuary</h3><p data-gjs-type="text">A scenic dhow boat ride from Stone Town leads to Changuu Island, home to century-old giant Aldabra tortoises and pristine coral reefs for swimming and snorkelling.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Sunset Sailing</div><h3 data-gjs-type="text">Dhow Sunset Cruise</h3><p data-gjs-type="text">Set sail on a handcrafted wooden dhow as the afternoon breeze carries you along the coastline, watching breathtaking golden-hour sunsets over the Indian Ocean.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Marine Adventures</div><h3 data-gjs-type="text">Mnemba Atoll Diving</h3><p data-gjs-type="text">A world-renowned marine conservation reserve boasting crystal-clear visibility, thriving coral gardens, green sea turtles, and playful schools of tropical reef fish.</p></div>
        <div class="zanzibar-exp-card"><div class="tag" data-gjs-type="text">Iconic Dining</div><h3 data-gjs-type="text">The Rock Restaurant</h3><p data-gjs-type="text">Perched atop an ancient coral outcrop in the Indian Ocean off Michamvi beach — reachable on foot at low tide or by boat at high tide for an unforgettable meal.</p></div>
      </div>
    </div>
  </section>

  <!-- When To Go Section -->
  <section class="zanzibar-section" id="zanzibar-when">
    <div class="wrap">
      <div class="zanzibar-section-head">
        <div class="eyebrow" data-gjs-type="text">When To Go</div>
        <h2 data-gjs-type="text">Best Time to Visit Zanzibar</h2>
        <p data-gjs-type="text">The best time to visit Zanzibar falls during the dry season from June to October, and from December to February. Travelers can anticipate clear skies, balmy temperatures, and reduced humidity, creating ideal conditions for beach exploration and water-based activities such as swimming, diving, and snorkeling.</p>
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
        <span><i style="background:var(--clove-bright)"></i><span data-gjs-type="text">Prime dry season: ideal for beaches, diving &amp; safaris</span></span>
        <span><i style="background:var(--gold)"></i><span data-gjs-type="text">Warm &amp; tropical: sunny skies and calm ocean</span></span>
        <span><i style="background:var(--sand-soft); border:1px solid var(--line)"></i><span data-gjs-type="text">Shoulder months: pleasant weather, fewer crowds</span></span>
        <span><i style="border:1px dashed var(--line)"></i><span data-gjs-type="text">Green season rains: lush landscapes &amp; quieter island</span></span>
      </div>
    </div>
  </section>

  <!-- Plan My Trip / CTA Section -->
  <section class="zanzibar-cta" id="zanzibar-plan">
    <div class="wrap">
      <div class="eyebrow" data-gjs-type="text">Mwangwi Safaris Experience</div>
      <h2 data-gjs-type="text">Explore our collection of unforgettable safaris in Tanzania &amp; Zanzibar</h2>
      <p data-gjs-type="text">Mwangwi Safaris offers various travel experiences that highlight Tanzania’s rich culture, diverse wildlife, and stunning landscapes. Tell us how many days you have, whether a mainland safari comes first, and we'll craft an itinerary tailored to your vision.</p>
      <div class="zanzibar-cta-ctas">
        <a class="zanzibar-btn-primary" href="/contact" data-gjs-type="text">Plan My Zanzibar Safari</a>
        <a class="zanzibar-btn-ghost" href="/tours" data-gjs-type="text">Explore All Tours &amp; Safaris</a>
      </div>
    </div>
  </section>
</div>`
});

// ── Missing Component Types Registration ───────────────────────────────
editor.DomComponents.addType('safaris-zanzibar-page-block', {
    isComponent: el => (el && el.classList && el.classList.contains('safaris-zanzibar-page-block')) ? { type: 'safaris-zanzibar-page-block' } : false,
    model: {
        defaults: {
            tagName: 'div',
            attributes: { class: 'safaris-zanzibar-page-block' }
        }
    }
});

editor.DomComponents.addType('safaris-page-header-block', {
    isComponent: el => (el && el.classList && el.classList.contains('safaris-page-header-block')) ? { type: 'safaris-page-header-block' } : false,
    model: {
        defaults: {
            tagName: 'section',
            attributes: { class: 'safaris-page-header-block' }
        }
    }
});

editor.DomComponents.addType('safaris-kili-calculator-block', {
    isComponent: el => (el && el.classList && el.classList.contains('safaris-kili-calculator-block')) ? { type: 'safaris-kili-calculator-block' } : false,
    model: {
        defaults: {
            tagName: 'div',
            attributes: { class: 'safaris-kili-calculator-block' }
        }
    }
});

editor.DomComponents.addType('safaris-about-block', {
    isComponent: el => (el && el.classList && el.classList.contains('safaris-about-block')) ? { type: 'safaris-about-block' } : false,
    model: {
        defaults: {
            tagName: 'section',
            attributes: { class: 'safaris-about-block' }
        }
    }
});



