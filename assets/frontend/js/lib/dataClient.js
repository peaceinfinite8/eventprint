/**
 * Data Client (API Version with DEBUG)
 * Caches API responses to avoid re-fetching
 */
const DataClient = {
    cache: {},

    // Generic get with base URL support and DEBUG
    async get(url) {
        // Build full URL with base path
        let fullUrl = url;
        if (url.startsWith('/api/')) {
            const baseUrl = window.EP_BASE_URL || '';
            fullUrl = baseUrl + url;
        }

        // Check cache
        if (this.cache[fullUrl]) {
            if (window.EP_DEBUG) console.log('[DataClient CACHE HIT]', fullUrl);
            return this.cache[fullUrl];
        }

        try {
            if (window.EP_DEBUG) console.log('[DataClient FETCH]', fullUrl);
            const response = await fetch(fullUrl);
            const contentType = response.headers.get('content-type');

            if (window.EP_DEBUG) console.log('[DataClient STATUS]', response.status, contentType);

            if (!response.ok) {
                const text = await response.text();
                console.error('[DataClient ERROR RESPONSE]', text.substring(0, 200));
                throw new Error(`DataClient: Failed to load ${fullUrl} - Status ${response.status}`);
            }

            const text = await response.text();
            if (window.EP_DEBUG) console.log('[DataClient RESPONSE START]', text.substring(0, 100));

            let data;
            try {
                data = JSON.parse(text);
            } catch (parseError) {
                console.error('[DataClient JSON PARSE ERROR]');
                console.error('[DataClient RESPONSE TEXT]', text.substring(0, 500));
                throw new Error(`Invalid JSON from ${fullUrl}: ${parseError.message}`);
            }

            this.cache[fullUrl] = data;
            return data;
        } catch (error) {
            console.error('[DataClient FAILED]', url, error);
            return null;
        }
    },

    // Specific getters using API endpoints
    async getProducts() {
        return this.get('/api/products');
    },

    async getSite() {
        return this.get('/api/contact'); // Settings data
    },

    async getCategories() {
        return this.get('/api/categories');
    }
};

window.DataClient = DataClient;
