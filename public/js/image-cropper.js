/**
 * Multi Image Cropper Module
 * Handles multiple image upload, validation, and cropping with Cropper.js
 */

const ImageCropper = (function() {
    // Configuration
    const CONFIG = {
        MAX_SIZE: 2 * 1024 * 1024, // 2 MB
        ALLOWED_TYPES: ['image/jpeg', 'image/png', 'image/webp'],
        JPEG_QUALITY: 0.92,
        PNG_QUALITY: 1,
        CROPPER_DELAY: 300
    };

    // Store multiple cropper instances
    const croppers = {};
    const currentFiles = {};

    function init(options) {
        const id = options.id;
        
        // Get DOM elements
        const elements = {
            input: document.getElementById(options.inputId),
            preview: document.getElementById(options.previewId),
            previewWrapper: options.previewWrapperId ? document.getElementById(options.previewWrapperId) : null,
            editBtn: options.editBtnId ? document.getElementById(options.editBtnId) : null,
            container: options.containerId ? document.getElementById(options.containerId) : null,
            removeInput: options.removeInputId ? document.getElementById(options.removeInputId) : null,
            cropModal: $(`#${options.modalId}`),
            imageToCrop: document.getElementById(options.imageToCropId),
            cropBtn: document.getElementById(options.cropBtnId),
            clearBtn: options.clearBtnId ? document.getElementById(options.clearBtnId) : null
        };

        if (!elements.input) {
            console.error(`Image input element not found: ${options.inputId}`);
            return;
        }

        // Store config
        const config = {
            id: id,
            aspectRatio: options.aspectRatio || 1,
            outputWidth: options.outputWidth || 800,
            outputHeight: options.outputHeight || (options.outputWidth || 800) / (options.aspectRatio || 1),
            elements: elements
        };

        bindEvents(config);
    }

    /**
     * Bind all event listeners for a cropper instance
     */
    function bindEvents(config) {
        const { id, elements } = config;

        // Click handlers to trigger file input
        if (elements.editBtn) {
            elements.editBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                elements.input.click();
            });
        }

        if (elements.container) {
            elements.container.addEventListener('click', function(e) {
                e.preventDefault();
                elements.input.click();
            });
        }

        // Image upload handler
        elements.input.addEventListener('change', function(e) {
            handleImageUpload(e, config);
        });

        // Modal events
        elements.cropModal.on('shown.bs.modal', function() {
            initializeCropper(config);
        });

        elements.cropModal.on('hidden.bs.modal', function() {
            destroyCropper(id);
            currentFiles[id] = null;
        });

        // Crop button handler
        if (elements.cropBtn) {
            elements.cropBtn.addEventListener('click', function() {
                handleCrop(config);
            });
        }

        // Clear button handler
        if (elements.clearBtn) {
            elements.clearBtn.addEventListener('click', function() {
                handleClear(config);
            });
        }

        // Right-click to remove (optional feature)
        if (elements.container && elements.preview) {
            elements.container.addEventListener('contextmenu', function(e) {
                if (elements.preview.classList.contains('active') || elements.preview.src) {
                    e.preventDefault();
                    handleClear(config);
                }
            });
        }
    }

    /**
     * Handle image file upload
     */
    function handleImageUpload(e, config) {
        const { id, elements } = config;
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        if (!CONFIG.ALLOWED_TYPES.includes(file.type)) {
            alert('Tipe file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
            elements.input.value = '';
            return;
        }

        // Validate file size
        if (file.size > CONFIG.MAX_SIZE) {
            alert('Ukuran file lebih dari 2 MB. Pilih file yang lebih kecil.');
            elements.input.value = '';
            return;
        }

        currentFiles[id] = file;
        const reader = new FileReader();

        reader.onload = function(event) {
            if (elements.imageToCrop) {
                elements.imageToCrop.src = event.target.result;
                elements.cropModal.modal('show');
            }
        };

        reader.onerror = function() {
            alert('Error membaca file. Silakan coba lagi.');
            elements.input.value = '';
        };

        reader.readAsDataURL(file);

        // Reset remove flag if exists
        if (elements.removeInput) {
            elements.removeInput.value = '0';
        }
    }

    /**
     * Initialize Cropper.js when modal is shown
     */
    function initializeCropper(config) {
        const { id, aspectRatio, elements } = config;
        
        destroyCropper(id);

        if (!elements.imageToCrop || !elements.imageToCrop.src) return;

        setTimeout(function() {
            // Check if this is a circle crop (aspect ratio 1 for profile)
            const isCircleCrop = id.includes('profile') && aspectRatio === 1;
            
            croppers[id] = new Cropper(elements.imageToCrop, {
                aspectRatio: aspectRatio,
                viewMode: isCircleCrop ? 1 : 2,
                dragMode: 'move',
                autoCropArea: 0.9,
                restore: true,
                guides: !isCircleCrop,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                responsive: true,
                ready: function() {
                    console.log(`Cropper ${id} ready`);
                    
                    // Make crop box circular for profile images
                    if (isCircleCrop) {
                        const cropBox = document.querySelector('.cropper-crop-box');
                        const face = document.querySelector('.cropper-face');
                        if (cropBox) cropBox.style.borderRadius = '50%';
                        if (face) face.style.borderRadius = '50%';
                    }
                }
            });
        }, CONFIG.CROPPER_DELAY);
    }

    /**
     * Destroy cropper instance
     */
    function destroyCropper(id) {
        if (croppers[id]) {
            croppers[id].destroy();
            croppers[id] = null;
        }
    }

    /**
     * Handle crop button click
     */
    function handleCrop(config) {
        const { id, outputWidth, outputHeight, elements } = config;
        const cropper = croppers[id];
        const currentFile = currentFiles[id];

        if (!cropper) {
            alert('Cropper tidak siap. Silakan coba lagi.');
            return;
        }

        if (!currentFile) {
            alert('Tidak ada file yang dipilih. Silakan coba lagi.');
            return;
        }

        const btn = elements.cropBtn;
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

        try {
            const isTransparent = currentFile.type === 'image/png' || currentFile.type === 'image/webp';
            const outputType = isTransparent ? currentFile.type : 'image/jpeg';
            const quality = isTransparent ? CONFIG.PNG_QUALITY : CONFIG.JPEG_QUALITY;
            
            const canvas = cropper.getCroppedCanvas({
                width: outputWidth,
                height: outputHeight,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: isTransparent ? 'transparent' : '#fff'
            });

            if (!canvas) {
                throw new Error('Gagal membuat canvas');
            }

            canvas.toBlob(function(blob) {
                if (!blob) {
                    alert('Gagal memproses gambar. Silakan coba lagi.');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    return;
                }

                const croppedFile = new File([blob], currentFile.name, {
                    type: outputType,
                    lastModified: Date.now()
                });

                // Update file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                elements.input.files = dataTransfer.files;

                // Update preview
                updatePreview(blob, config);

                // Close modal
                elements.cropModal.modal('hide');

                // Reset button
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                console.log(`Image ${id} cropped successfully`);
            }, outputType, quality);

        } catch (error) {
            console.error('Crop error:', error);
            alert('Error memproses gambar: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    /**
     * Update preview image
     */
    function updatePreview(blob, config) {
        const { elements } = config;
        const url = URL.createObjectURL(blob);
        
        if (elements.preview) {
            elements.preview.src = url;
            elements.preview.style.display = 'block';
        }
        
        if (elements.previewWrapper) {
            elements.previewWrapper.classList.remove('d-none');
        }
    }

    /**
     * Handle clear/delete button
     */
    function handleClear(config) {
        const { elements } = config;
        const confirmMsg = 'Apakah Anda yakin ingin menghapus gambar ini?';
        
        if (confirm(confirmMsg)) {
            if (elements.previewWrapper) {
                elements.previewWrapper.classList.add('d-none');
            }
            
            if (elements.preview) {
                elements.preview.src = '';
                elements.preview.style.display = 'none';
            }
            
            if (elements.input) {
                elements.input.value = '';
            }

            if (elements.removeInput) {
                elements.removeInput.value = '1';
            }
        }
    }

    /**
     * Public API
     */
    return {
        init: init,
        destroy: destroyCropper,
        destroyAll: function() {
            Object.keys(croppers).forEach(id => destroyCropper(id));
        }
    };
})();

// Export for use in modules (optional)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ImageCropper;
}