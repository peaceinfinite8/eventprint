/**
 * URL State Management Utilities
 * Handles query parameters efficiently
 */

// Get current query parameters as object
function getQueryParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        cat: params.get('cat'),
        sub: params.get('sub'),
        slug: params.get('slug')
    };
}

// Update query parameters without reloading page
function updateQueryParams(newParams) {
    const url = new URL(window.location);
    Object.keys(newParams).forEach(key => {
        if (newParams[key] === null || newParams[key] === undefined) {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, newParams[key]);
        }
    });
    window.history.pushState({}, '', url);
}

// Get specific query param
function getQueryParam(key) {
    const params = new URLSearchParams(window.location.search);
    return params.get(key);
}

// Remove query param
function removeQueryParam(key) {
    const url = new URL(window.location);
    url.searchParams.delete(key);
    window.history.pushState({}, '', url);
}