/**
 * Data Client (Simple Cache)
 * Avoids re-fetching JSON multiple times.
 */
const DataClient = {
    cache: {},

    // Generic get
    async get(url) {
        if (this.cache[url]) {
            return this.cache[url];
        }
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`DataClient: Failed to load ${url}`);
            const data = await response.json();
            this.cache[url] = data;
            return data;
        } catch (error) {
            console.error(error);
            return null;
        }
    },

    // Specific getters
    async getProducts() {
        return this.get('../data/products.json');
    },

    async getSite() {
        return this.get('../data/site.json');
    }
};

window.DataClient = DataClient;
