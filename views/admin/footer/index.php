<?php
// views/admin/footer/index.php

$content = $vars['content'] ?? [];
$productLinks = json_decode($content['product_links'] ?? '[]', true);
$paymentMethods = json_decode($content['payment_methods'] ?? '[]', true);
$copyright = $content['copyright'] ?? '';
$csrfToken = Security::csrfToken();
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Manage Footer Content</h1>
        <p class="text-muted small mb-0">Atur konten bagian bawah website (Footer)</p>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="p-4">
        <form action="<?= $vars['baseUrl'] ?>/admin/footer/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Copyright -->
            <div class="mb-5">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-copyright me-2"></i>Copyright Info</h5>
                <label class="dash-form-label">COPYRIGHT TEXT</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-pen"></i></span>
                    <input type="text" class="form-control" name="copyright" value="<?= htmlspecialchars($copyright) ?>"
                        placeholder="© 2024 EventPrint. All rights reserved.">
                </div>
            </div>

            <hr class="border-light my-5">

            <!-- Product Links -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0"><i class="fas fa-link me-2"></i>Product Links (Kolom 2)</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm"
                        onclick="addProductLinkRow()">
                        <i class="fas fa-plus me-1"></i> Add Link
                    </button>
                </div>

                <div class="p-3 bg-light rounded border border-light">
                    <div id="productLinksContainer">
                        <?php if (empty($productLinks)): ?>
                            <!-- Blank Row -->
                            <div class="row g-2 mb-2 link-row">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="product_names[]"
                                        placeholder="Link Name (e.g. Spanduk)">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="product_urls[]"
                                        placeholder="URL (e.g. /products?cat=spanduk)">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                            class="fas fa-times"></i></button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($productLinks as $link): ?>
                                <div class="row g-2 mb-2 link-row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="product_names[]"
                                            value="<?= htmlspecialchars($link['label']) ?>" placeholder="Link Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="product_urls[]"
                                            value="<?= htmlspecialchars($link['url']) ?>" placeholder="URL">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                                class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr class="border-light my-5">

            <!-- Payment Methods -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0"><i class="fas fa-credit-card me-2"></i>Payment Methods</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="addPaymentRow()">
                        <i class="fas fa-plus me-1"></i> Add Method
                    </button>
                </div>

                <div class="p-3 bg-light rounded border border-light">
                    <div id="paymentMethodsContainer">
                        <?php if (empty($paymentMethods)): ?>
                            <!-- Blank Row -->
                            <div class="row g-2 mb-2 payment-row align-items-center">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="payment_labels[]"
                                        placeholder="Label (e.g. BCA)">
                                </div>
                                <div class="col-md-4">
                                    <input type="file" class="form-control" name="payment_images[]" accept="image/*">
                                    <input type="hidden" name="payment_old_images[]" value="">
                                </div>
                                <div class="col-md-3 text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">No
                                        Image</span>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                            class="fas fa-times"></i></button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($paymentMethods as $pm): ?>
                                <div class="row g-2 mb-2 payment-row align-items-center">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="payment_labels[]"
                                            value="<?= htmlspecialchars($pm['label'] ?? '') ?>" placeholder="Label">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" class="form-control" name="payment_images[]" accept="image/*">
                                        <input type="hidden" name="payment_old_images[]"
                                            value="<?= htmlspecialchars($pm['image'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <?php if (!empty($pm['image'])): ?>
                                            <div class="bg-white p-1 rounded border d-inline-block">
                                                <img src="<?= $vars['baseUrl'] ?>/<?= htmlspecialchars($pm['image']) ?>" alt="Icon"
                                                    style="height: 30px; object-fit: contain;">
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">No
                                                Image</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                                class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end pt-3 border-top">
                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function addProductLinkRow() {
        const container = document.getElementById('productLinksContainer');
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 link-row fade-in';
        div.innerHTML = `
        <div class="col-md-5">
            <input type="text" class="form-control" name="product_names[]" placeholder="Link Name">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" name="product_urls[]" placeholder="URL">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
        </div>
    `;
        container.appendChild(div);
    }

    function addPaymentRow() {
        const container = document.getElementById('paymentMethodsContainer');
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 payment-row align-items-center fade-in';
        div.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="payment_labels[]" placeholder="Label">
        </div>
        <div class="col-md-4">
            <input type="file" class="form-control" name="payment_images[]" accept="image/*">
            <input type="hidden" name="payment_old_images[]" value="">
        </div>
        <div class="col-md-3 text-center">
            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">New</span>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
        </div>
    `;
        container.appendChild(div);
    }

    function removeRow(btn) {
        if (confirm('Remove this row?')) {
            btn.closest('.row').remove();
        }
    }
</script>