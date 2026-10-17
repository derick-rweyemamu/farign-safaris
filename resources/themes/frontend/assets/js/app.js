import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

document.addEventListener('livewire:init', () => {
    // Register Alpine plugins
    Alpine.plugin(focus);
    Alpine.plugin(collapse);

    // Global store for sidebar state
    Alpine.store('sidebar', {
        collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        mobileOpen: false,

        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebar_collapsed', this.collapsed);
            this.apply();
        },

        apply() {
            document.documentElement.classList.toggle('sidebar-collapsed', this.collapsed);
        },

        toggleMobile() {
            this.mobileOpen = !this.mobileOpen;
        },

        close() {
            this.mobileOpen = false;
        }
    });

    // Global store for dark mode
    Alpine.store('theme', {
        dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),

        isFrontend() {
            return window.__IS_FRONTEND__ === true || document.body?.classList.contains('frontend-body');
        },

        toggle() {
            if (this.isFrontend()) return;
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            this.apply();
        },

        apply() {
            if (this.isFrontend()) {
                document.documentElement.classList.remove('dark');
                return;
            }
            document.documentElement.classList.toggle('dark', this.dark);
        },

        init() {
            if (this.isFrontend()) {
                document.documentElement.classList.remove('dark');
                return;
            }
            this.apply();
        }
    });

    // Global notifications store
    Alpine.store('notifications', {
        items: [],
        count: 0,

        add(message, type = 'info', duration = 4000) {
            const id = Date.now();
            this.items.push({ id, message, type });
            this.count = this.items.length;

            if (duration > 0) {
                setTimeout(() => this.remove(id), duration);
            }
            return id;
        },

        remove(id) {
            this.items = this.items.filter(n => n.id !== id);
            this.count = this.items.length;
        },

        success(msg) { return this.add(msg, 'success'); },
        error(msg)   { return this.add(msg, 'error'); },
        info(msg)    { return this.add(msg, 'info'); },
        warning(msg) { return this.add(msg, 'warning'); }
    });


});

// Livewire hooks
document.addEventListener('livewire:initialized', () => {
    // Success / error feedback from Livewire dispatch
    Livewire.on('notify', ({ message, type }) => {
        Alpine.store('notifications').add(message, type ?? 'info');
    });

    // Close mobile sidebar and re-apply theme/sidebar on navigation
    document.addEventListener('livewire:navigated', () => {
        Alpine.store('sidebar').close();
        if (Alpine.store('theme')) {
            Alpine.store('theme').apply();
        }
        if (Alpine.store('sidebar')) {
            Alpine.store('sidebar').apply();
        }
    });
});

window.showNativeToast = (msg, isError) => {
    const bg = isError ? 'bg-red-600' : 'bg-green-600';
    const icon = isError 
        ? '<svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        : '<svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 ${bg} text-white px-6 py-4 rounded-[3px] shadow-2xl z-[9999] transition-opacity duration-500 font-medium max-w-sm`;
    toast.innerHTML = `<div class="flex items-center gap-3">${icon}<p>${msg}</p></div>`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 5000);
};

// Global form listener for Start Planning block
document.addEventListener('submit', function (e) {
    const form = e.target.closest('.request-form');
    if (form) {
        e.preventDefault();

        // Ensure we only process if it's our start planning form or has the class
        const submitBtn = form.querySelector('button[type="submit"], .btn-primary');
        const originalText = submitBtn ? submitBtn.innerText : 'Submit';
        if (submitBtn) {
            submitBtn.innerText = 'Sending...';
            submitBtn.disabled = true;
        }

        // Gather data from inputs or selects inside the form
        const formData = new FormData();
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name) {
                formData.append(input.name, input.value);
            }
        });

        const token = document.head.querySelector('meta[name="csrf-token"]');
        
        fetch('/planning/submit', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token ? token.content : ''
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.showNativeToast(data.message || 'Your request has been sent successfully!', false);
                if (form.tagName === 'FORM') {
                    form.reset();
                } else {
                    inputs.forEach(input => {
                        if (input.tagName === 'INPUT' && input.type !== 'submit') input.value = '';
                    });
                }
            } else {
                window.showNativeToast(data.message || 'Something went wrong.', true);
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
            window.showNativeToast('Network error. Please try again.', true);
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});

// Global form listener for Newsletter block
document.addEventListener('submit', function (e) {
    const form = e.target.closest('#newsletter-form');
    if (form) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"], .btn-primary');
        const originalText = submitBtn ? submitBtn.innerText : 'Subscribe';
        if (submitBtn) {
            submitBtn.innerText = 'Subscribing...';
            submitBtn.disabled = true;
        }

        const formData = new FormData(form);
        const token = document.head.querySelector('meta[name="csrf-token"]');
        
        fetch('/newsletter/subscribe', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token ? token.content : ''
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.showNativeToast(data.message || 'Subscribed successfully!', false);
                form.reset();
            } else {
                window.showNativeToast(data.message || 'Subscription failed.', true);
            }
        })
        .catch(error => {
            console.error('Error submitting newsletter form:', error);
            window.showNativeToast('Network error. Please try again.', true);
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});

// Global click listener for Start Planning button trigger
document.addEventListener('click', function (e) {
    if (e.target.closest('.start-planning-btn-trigger')) {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('open-planning-modal'));
    }
});
