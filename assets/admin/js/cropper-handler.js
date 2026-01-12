/**
 * Generic Cropper Handlers
 * Usage: Add data-cropper="true" and data-aspect-ratio="1.77" to file inputs.
 */
document.addEventListener('DOMContentLoaded', function () {
    let cropper;
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
    const image = document.getElementById('imageToCrop');
    let currentInput = null;
    let aspectRatio = NaN; // Free by default

    // Handler for all file inputs triggering cropper
    document.querySelectorAll('input[type="file"][data-cropper="true"]').forEach(input => {
        input.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const url = URL.createObjectURL(file);

                // Set current input context
                currentInput = this;

                // Get aspect ratio from usage
                const ratioAttr = this.getAttribute('data-aspect-ratio');
                aspectRatio = ratioAttr ? parseFloat(ratioAttr) : NaN;

                // Setup image
                image.src = url;

                // Show modal
                cropModal.show();

                // Reset value to allow re-selecting same file if cancelled
                this.value = '';
            }
        });
    });

    // Init Cropper when modal opens
    document.getElementById('cropModal').addEventListener('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            aspectRatio: aspectRatio,
            viewMode: 2, // Restrict crop box to not exceed image
            autoCropArea: 0.9,
            responsive: true,
            restore: false // Prevent issue where cropper remembers previous crop box
        });
    });

    // Destroy Cropper when modal closes
    document.getElementById('cropModal').addEventListener('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        image.src = '';
    });

    // Handle Crop Button
    document.getElementById('cropBtn').addEventListener('click', function () {
        if (!cropper) return;

        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            // Set max dimensions if needed, or stick to natural
            // width: 1920, 
            // height: 1080
        });

        // Convert to blob
        canvas.toBlob(function (blob) {
            // Create a new file from blob
            // We use a trick: standard file inputs cannot be set programmatically due to security.
            // So we use DataTransfer interface.
            const dataTransfer = new DataTransfer();

            // Name it 'cropped.jpg' or keep original name if likely
            const fileName = 'cropped_image.jpg';
            const newFile = new File([blob], fileName, { type: 'image/jpeg' });

            dataTransfer.items.add(newFile);

            // Assign to input
            if (currentInput) {
                currentInput.files = dataTransfer.files;

                // Optional: Update preview if exists
                const previewContainer = document.getElementById('imgPreviewContainer');
                const previewImg = document.getElementById('imgPreview');
                if (previewContainer && previewImg) {
                    previewImg.src = canvas.toDataURL('image/png');
                    previewContainer.style.display = 'block';
                }
            }

            cropModal.hide();
        });
    }, 'image/png');
});
