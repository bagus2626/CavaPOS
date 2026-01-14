/**
 * Image Remove Handler Module
 * Handles remove button functionality for image uploads
 * Works with both create and edit pages
 */

const ImageRemoveHandler = (function() {
    

    function init(options) {
        const removeBtn = document.getElementById(options.removeBtnId);
        const imageInput = document.getElementById(options.imageInputId);
        const imagePreview = document.getElementById(options.imagePreviewId);
        const uploadPlaceholder = document.getElementById(options.uploadPlaceholderId);
        const removeInput = options.removeInputId ? document.getElementById(options.removeInputId) : null;
        
        if (!removeBtn || !imageInput || !imagePreview || !uploadPlaceholder) {
            console.error('ImageRemoveHandler: Required elements not found');
            return;
        }

        // Remove button click handler
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Show confirmation if enabled
            if (options.confirmRemove && !confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                return;
            }
            
            handleRemove(imageInput, imagePreview, uploadPlaceholder, removeBtn, removeInput);
        });

        // Observer to show/hide remove button based on preview state
        setupObserver(imagePreview, removeBtn, removeInput);
    }

    /**
     * Handle image removal
     */
    function handleRemove(imageInput, imagePreview, uploadPlaceholder, removeBtn, removeInput) {
        // Reset file input
        imageInput.value = '';
        
        // Hide preview and show placeholder
        imagePreview.style.display = 'none';
        imagePreview.src = '';
        imagePreview.classList.remove('active');
        uploadPlaceholder.style.display = 'flex';
        
        // Hide remove button
        removeBtn.style.display = 'none';
        
        // Set remove flag to 1 (for edit page - tells server to delete image)
        if (removeInput) {
            removeInput.value = '1';
        }
    }

    /**
     * Setup MutationObserver to auto show/hide remove button
     */
    function setupObserver(imagePreview, removeBtn, removeInput) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'style' || mutation.attributeName === 'src') {
                    const hasImage = imagePreview.style.display === 'block' && imagePreview.src;
                    
                    if (hasImage) {
                        removeBtn.style.display = 'block';
                        // Reset remove flag when new image uploaded
                        if (removeInput) {
                            removeInput.value = '0';
                        }
                    }
                }
            });
        });

        observer.observe(imagePreview, {
            attributes: true,
            attributeFilter: ['style', 'src']
        });
    }

    /**
     * Public API
     */
    return {
        init: init
    };
})();

// Export for use in modules (optional)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ImageRemoveHandler;
}