<?php
// views/admin/footer/index.php

$content = $vars['content'] ?? [];
$productLinks = json_decode($content['product_links'] ?? '[]', true);
$paymentMethods = json_decode($content['payment_methods'] ?? '[]', true);
$categories = $vars['categories'] ?? [];
$copyright = $content['copyright'] ?? '';
$csrfToken = Security::csrfToken();
?>

<style>
    /* Shared Admin Styles */
    .smart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        transition: all 0.2s;
    }
    .smart-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }
    .smart-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    .smart-card-body {
        padding: 20px;
    }
    
    .modern-form-control {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.9rem;
        transition: all 0.2s;
        width: 100%;
        color: #334155;
    }
    .modern-form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    
    /* List Items */
    .list-item-row {
        background: #fff;
        border: 1px solid #f1f5f9;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
        position: relative;
    }
    .list-item-row:hover {
        border-color: #e2e8f0;
        background: #fdfdfd;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .remove-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        background: #fef2f2;
        border: none;
        transition: all 0.2s;
        cursor: pointer;
    }
    .remove-btn:hover {
        background: #ef4444;
        color: white;
    }

    /* Payment Specific */
    .payment-icon-preview {
        width: 48px;
        height: 32px;
        object-fit: contain;
        border-radius: 4px;
        background: #fff;
        border: 1px solid #eee;
        padding: 2px;
    }
    /* Clickable area for icon upload */
    .payment-icon-box {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    .payment-icon-box:hover {
        border-color: #3b82f6 !important;
        background: #eff6ff !important;
    }
    .payment-icon-box:hover::after {
        content: '\f093'; /* fa-upload */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(59, 130, 246, 0.8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        border-radius: 4px;
    }
    .custom-file-trigger {
        font-size: 0.8rem;
        cursor: pointer;
        background: #f1f5f9;
        padding: 6px 12px;
        border-radius: 6px;
        color: #475569;
        font-weight: 500;
        display: inline-block;
        transition: all 0.2s;
    }
    .custom-file-trigger:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Manage Footer Content</h1>
        <p class="text-muted small mb-0">Atur konten pada bagian footer website Anda.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" form="footerForm" class="btn btn-primary shadow-sm px-4">
            <i class="fas fa-save me-2"></i> Simpan Perubahan
        </button>
    </div>
</div>

<form id="footerForm" action="<?= $vars['baseUrl'] ?>/admin/footer/update" method="POST" enctype="multipart/form-data" class="fade-in delay-1">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <!-- 1. Copyright Section -->
    <div class="smart-card">
        <div class="smart-card-header">
            <h5 class="section-title"><i class="fas fa-copyright me-2 text-primary"></i>Copyright Info</h5>
        </div>
        <div class="smart-card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small mb-0">Copyright Text</label>
                    <div class="text-xs text-muted">Teks yang muncul di paling bawah.</div>
                </div>
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-pen"></i></span>
                        <input type="text" class="form-control modern-form-control border-start-0 ps-0" 
                               name="copyright" 
                               value="<?= htmlspecialchars($copyright) ?>"
                               placeholder="© 2026 EventPrint. All rights reserved.">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- 2. Product Links -->
        <div class="col-lg-6">
            <div class="smart-card h-100">
                <div class="smart-card-header">
                    <h5 class="section-title"><i class="fas fa-link me-2 text-primary"></i>Product Links</h5>
                    <button type="button" class="btn btn-sm btn-light border shadow-sm text-primary fw-bold" onclick="addProductLinkRow()">
                        <i class="fas fa-plus me-1"></i> Add Link
                    </button>
                </div>
                <div class="smart-card-body bg-light">
                    <div id="productLinksContainer">
                        <?php if (empty($productLinks)): ?>
                           <!-- Default Empty State handled by JS if needed -->
                        <?php endif; ?>
                        
                        <?php foreach ($productLinks as $index => $link): ?>
                            <div class="list-item-row">
                                <div class="flex-grow-1">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <select class="form-select modern-form-control fw-bold text-dark" onchange="updateLinkData(this)">
                                                <option value="" data-url="">- Pilih Kategori -</option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <?php 
                                                        $catUrl = '/products?category=' . $cat['slug'];
                                                        $selected = ($link['url'] == $catUrl || $link['label'] == $cat['name']) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?= htmlspecialchars($cat['name']) ?>" 
                                                            data-url="<?= htmlspecialchars($catUrl) ?>"
                                                            <?= $selected ?>><?= htmlspecialchars($cat['name']) ?></option>
                                                <?php endforeach; ?>
                                                <!-- Handle custom/legacy links (not in category list) -->
                                                <?php 
                                                    // Simple check: if link label isn't in categories, add it as manual option
                                                    $found = false;
                                                    foreach($categories as $c) { if($c['name'] === $link['label']) $found = true; }
                                                    if (!$found && !empty($link['label'])):
                                                ?>
                                                    <option value="<?= htmlspecialchars($link['label']) ?>" data-url="<?= htmlspecialchars($link['url']) ?>" selected><?= htmlspecialchars($link['label']) ?> (Custom)</option>
                                                <?php endif; ?>
                                            </select>
                                            
                                            <!-- Hidden inputs that actually get sent -->
                                            <input type="hidden" name="product_names[]" class="hidden-name" value="<?= htmlspecialchars($link['label']) ?>">
                                            <input type="hidden" name="product_urls[]" class="hidden-url" value="<?= htmlspecialchars($link['url']) ?>">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="remove-btn" onclick="removeRow(this)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($productLinks)): ?>
                        <div class="text-center py-4 text-muted small empty-state">Belum ada link produk. Klik tombol "Add Link".</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 3. Payment Methods -->
        <div class="col-lg-6">
            <div class="smart-card h-100">
                <div class="smart-card-header">
                    <h5 class="section-title"><i class="fas fa-credit-card me-2 text-primary"></i>Payment Methods</h5>
                    <button type="button" class="btn btn-sm btn-light border shadow-sm text-primary fw-bold" onclick="addPaymentRow()">
                        <i class="fas fa-plus me-1"></i> Add Method
                    </button>
                </div>
                <div class="smart-card-body bg-light">
                    <div id="paymentMethodsContainer">
                        <?php foreach ($paymentMethods as $index => $pm): ?>
                            <div class="list-item-row">
                                <!-- Clickable Icon Box -->
                                <div class="d-flex align-items-center justify-content-center bg-white border rounded payment-icon-box"
                                     onclick="triggerFileInput(this)"
                                     title="Klik untuk ganti icon">
                                    <?php if (!empty($pm['image'])): ?>
                                        <img src="<?= $vars['baseUrl'] ?>/<?= htmlspecialchars($pm['image']) ?>" class="payment-icon-preview border-0">
                                    <?php else: ?>
                                        <i class="fas fa-image text-muted opacity-50"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <input type="text" class="modern-form-control mb-2 fw-bold text-dark" 
                                           name="payment_labels[]" 
                                           value="<?= htmlspecialchars($pm['label'] ?? '') ?>" 
                                           placeholder="Payment Name (e.g. BCA)">
                                    
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <!-- Hidden Input -->
                                        <input type="file" name="payment_images[]" class="d-none" accept="image/*" onchange="previewPaymentIcon(this)">
                                        
                                        <label class="custom-file-trigger me-0" onclick="triggerFileInput(this.parentElement.parentElement.previousElementSibling)">
                                            <i class="fas fa-upload me-1"></i> <?= !empty($pm['image']) ? 'Ganti Icon' : 'Upload Icon' ?>
                                        </label>
                                        <div class="text-xs text-muted">Max 2MB</div>
                                        <input type="hidden" name="payment_old_images[]" value="<?= htmlspecialchars($pm['image'] ?? '') ?>">
                                    </div>
                                </div>

                                <button type="button" class="remove-btn" onclick="removeRow(this)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                     <?php if (empty($paymentMethods)): ?>
                        <div class="text-center py-4 text-muted small empty-state">Belum ada metode pembayaran.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Pass categories to JS
    const categories = <?= json_encode($categories) ?>;

    // Helper to remove empty state text if present
    function clearEmptyState(containerId) {
        const container = document.getElementById(containerId);
        const emptyState = container.querySelector('.empty-state');
        if (emptyState) emptyState.remove();
    }

    // Update hidden inputs when dropdown changes
    function updateLinkData(select) {
        const row = select.closest('.list-item-row');
        const nameInput = row.querySelector('.hidden-name');
        const urlInput = row.querySelector('.hidden-url');
        
        const selectedOption = select.options[select.selectedIndex];
        const name = selectedOption.value;
        const url = selectedOption.getAttribute('data-url');
        
        nameInput.value = name;
        urlInput.value = url;

        updateDropdownAvailability();
    }

    // New Function: Disable options already selected in other dropdowns AND sort
    function updateDropdownAvailability() {
        const selects = document.querySelectorAll('#productLinksContainer select');
        const selectedValues = [];

        // 1. Gather all currently selected values
        selects.forEach(select => {
            if (select.value) {
                selectedValues.push(select.value);
            }
        });

        // 2. Iterate and update disabled state
        selects.forEach(select => {
            const currentValue = select.value;
            // Iterate options to update disabled status
            Array.from(select.options).forEach(option => {
                if (option.value === "") return; // Skip placeholder

                // Rule: Disable if selected elsewhere AND not current value
                if (selectedValues.includes(option.value) && option.value !== currentValue) {
                    option.disabled = true;
                    option.innerText = option.value + " (Sudah dipilih)";
                } else {
                    option.disabled = false;
                    // Reset text logic checking against categories list or custom
                    const cat = categories.find(c => c.name === option.value);
                    if (cat) {
                        option.innerText = cat.name;
                    } else if (option.value.includes("(Custom)")) {
                         option.innerText = option.value.replace(" (Sudah dipilih)", "");
                    } else {
                         option.innerText = option.value;
                    }
                }
            });

            // 3. SORT: Available First, Disabled Last
            // We strip non-placeholder options, sort them, and re-append
            const optionsArray = Array.from(select.options);
            const placeholder = optionsArray[0]; // Keep placeholder at top
            const rest = optionsArray.slice(1);

            // Stable sort: enable before disable, otherwise keep original order
            rest.sort((a, b) => {
                if (a.disabled === b.disabled) {
                    return 0; // Preserve relative order from PHP (sort_order)
                }
                return a.disabled ? 1 : -1; // Disabled goes to bottom
            });

            // Re-append sorted options
            rest.forEach(opt => select.appendChild(opt)); // Moves them in DOM
        });
    }

    // Call on load to set initial state
    document.addEventListener('DOMContentLoaded', updateDropdownAvailability);

    function addProductLinkRow() {
        clearEmptyState('productLinksContainer');
        const container = document.getElementById('productLinksContainer');
        const div = document.createElement('div');
        div.className = 'list-item-row fade-in';
        
        // Build Options
        let optionsHtml = '<option value="" data-url="">- Pilih Kategori -</option>';
        categories.forEach(cat => {
            const url = `/products?category=${cat.slug}`;
            optionsHtml += `<option value="${cat.name}" data-url="${url}">${cat.name}</option>`;
        });

        div.innerHTML = `
            <div class="flex-grow-1">
                 <select class="form-select modern-form-control fw-bold text-dark" onchange="updateLinkData(this)">
                    ${optionsHtml}
                 </select>
                 <input type="hidden" name="product_names[]" class="hidden-name" value="">
                 <input type="hidden" name="product_urls[]" class="hidden-url" value="">
            </div>
            <button type="button" class="remove-btn" onclick="removeRow(this)">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        container.appendChild(div);
        
        // Refresh availability
        updateDropdownAvailability();
    }

    function addPaymentRow() {
        clearEmptyState('paymentMethodsContainer');
        const container = document.getElementById('paymentMethodsContainer');
        const div = document.createElement('div');
        div.className = 'list-item-row fade-in';
        div.innerHTML = `
            <div class="d-flex align-items-center justify-content-center bg-white border rounded payment-icon-box"
                 onclick="triggerFileInput(this)"
                 title="Klik untuk upload icon">
                <i class="fas fa-image text-muted opacity-50"></i>
            </div>
            
            <div class="flex-grow-1">
                <input type="text" class="modern-form-control mb-2 fw-bold text-dark" 
                       name="payment_labels[]" 
                       placeholder="Payment Name">
                
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <input type="file" name="payment_images[]" class="d-none" accept="image/*" onchange="previewPaymentIcon(this)">
                    
                    <label class="custom-file-trigger me-0" onclick="triggerFileInput(this.parentElement.parentElement.previousElementSibling)">
                        <i class="fas fa-upload me-1"></i> Upload Icon
                    </label>
                    <div class="text-xs text-muted">Max 2MB</div>
                    <input type="hidden" name="payment_old_images[]" value="">
                </div>
            </div>

            <button type="button" class="remove-btn" onclick="removeRow(this)">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        container.appendChild(div);
         // Auto focus
        div.querySelector('input').focus();
    }

    function removeRow(btn) {
        Swal.fire({
            title: 'Hapus Item?',
            text: "Item ini akan dihapus dari daftar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
             customClass: {
                popup: 'small-swal'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const row = btn.closest('.list-item-row');
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();
                    // Refresh availability after removal
                    updateDropdownAvailability();
                }, 200);
            }
        });
    }

    function previewPaymentIcon(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            // Up to row, then find icon box
            const row = input.closest('.list-item-row');
            const iconBox = row.querySelector('.payment-icon-box'); // using new class

            reader.onload = function(e) {
                iconBox.innerHTML = `<img src="${e.target.result}" class="payment-icon-preview border-0">`;
            }
            reader.readAsDataURL(input.files[0]);
            
            // Update Text
            const rowBody = input.closest('.flex-grow-1');
            const trigger = rowBody.querySelector('.custom-file-trigger');
            if(trigger) trigger.innerHTML = `<i class="fas fa-check me-1"></i> Icon Dipilih`;
        }
    }

    function triggerFileInput(element) {
        // Element is either the box itself or the trigger button button
        // If box: it is in .list-item-row, input is in flex-grow-1 -> div -> input
        // Let's rely on finding closest row
        const row = element.closest('.list-item-row');
        const input = row.querySelector('input[type="file"]');
        if(input) input.click();
    }
</script>

<style>
/* Optional: Make SweetAlert smaller/cleaner */
.small-swal {
    width: 320px !important;
    font-size: 0.9rem !important;
}
</style>