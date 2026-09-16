{{-- Encode Livewire payloads to bypass WAF/ModSecurity/Imunify360 on shared hosting.
     Patches fetch() to base64-encode Livewire update request bodies before they hit
     the WAF, preventing 403 Forbidden from serialized PHP class names and HTML content.
     Paired with App\Http\Middleware\DecodeLivewirePayload on the server side. --}}
<script>
    (function() {
        const _originalFetch = window.fetch;
        const livewireUpdatePath = '/app/data/update';

        window.fetch = function(input, init) {
            try {
                const url = (typeof input === 'string') ? input : (input instanceof Request ? input.url : '');

                if (url.includes(livewireUpdatePath) && init && init.method === 'POST' && init.body && typeof init.body === 'string') {
                    // Check if it's already encoded to prevent double-encoding
                    const isAlreadyEncoded = init.headers && 
                        ((init.headers instanceof Headers && init.headers.has('X-Livewire-Encoded')) || 
                         (typeof init.headers === 'object' && ('X-Livewire-Encoded' in init.headers || 'x-livewire-encoded' in init.headers)));
                         
                    if (!isAlreadyEncoded) {
                        // Base64 encode the JSON body so WAF can't inspect it
                        const encoded = btoa(unescape(encodeURIComponent(init.body)));

                        init = Object.assign({}, init);
                        init.body = encoded;
                        init.headers = new Headers(init.headers || {});
                        init.headers.set('X-Livewire-Encoded', '1');
                        init.headers.set('Content-Type', 'text/plain');
                    }
                }
            } catch (e) {
                // If encoding fails, send original request
                console.warn('Livewire payload encoding skipped:', e);
            }

            return _originalFetch.call(this, input, init);
        };
    })();
</script>
