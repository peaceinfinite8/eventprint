// Universal image fallback handler
document.addEventListener('DOMContentLoaded', () => {
    const handleImageError = (img) => {
        // Determine placeholder based on image path
        let placeholder = '/eventprint/public/assets/frontend/images/placeholder-general.png';
        
        if (img.src.includes('/stores/')) {
            placeholder = '/eventprint/public/assets/frontend/images/placeholder-store.png';
        } else if (img.src.includes('/blog/')) {
            placeholder = '/eventprint/public/assets/frontend/images/placeholder-blog.png';
        } else if (img.src.includes('/products/')) {
            placeholder = '/eventprint/public/assets/frontend/images/placeholder-product.png';
        }
        
        img.src = placeholder;
        img.onerror = null; // Prevent infinite loop
    };
    
    // Add error handler to all images
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('error', () => handleImageError(img));
    });
});
