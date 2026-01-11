document.addEventListener("DOMContentLoaded", function () {
    const tiersList = document.getElementById("tiersList");
    const btnAddTier = document.getElementById("btnAddTier");

    // Global references
    const productId = window.PRODUCT_ID;

    // Use injected absolute URL or fallback
    const API_BASE = window.TIERS_API_URL || "../../../admin/api/products/" + productId + "/tiers";
    console.log("Tier Pricing API Base:", API_BASE);

    if (!productId) {
        if (tiersList) tiersList.innerHTML = "<p class='text-danger'>Product ID missing.</p>";
        return;
    }

    if (tiersList) {
        loadTiers();
    }

    function loadTiers() {
        tiersList.innerHTML = "<p class='text-muted'>Loading...</p>";

        fetch(API_BASE)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderTiers(data.data);
                } else {
                    tiersList.innerHTML = `<p class='text-danger'>Error: ${data.message}</p>`;
                }
            })
            .catch(err => {
                tiersList.innerHTML = `<p class='text-danger'>Network Error: ${err.message}</p>`;
            });
    }

    function renderTiers(tiers) {
        if (!tiers || tiers.length === 0) {
            tiersList.innerHTML = "<p class='text-muted'>No pricing tiers configured.</p>";
            return;
        }

        let html = `<div class="table-responsive"><table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Min Qty</th>
                    <th>Max Qty</th>
                    <th>Unit Price (Rp)</th>
                    <th style="width:100px;">Action</th>
                </tr>
            </thead>
            <tbody>`;

        tiers.forEach(t => {
            html += `<tr>
                <td>${t.qty_min}</td>
                <td>${t.qty_max ? t.qty_max : '&infin;'}</td>
                <td>Rp ${new Intl.NumberFormat('id-ID').format(t.unit_price)}</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete-tier" data-id="${t.id}">Delete</button>
                </td>
            </tr>`;
        });

        html += `</tbody></table></div>`;
        tiersList.innerHTML = html;

        // Attach delete handlers
        document.querySelectorAll(".btn-delete-tier").forEach(btn => {
            btn.addEventListener("click", function () {
                if (confirm("Delete this tier?")) {
                    deleteTier(this.dataset.id);
                }
            });
        });
    }

    // Add Tier Button
    if (btnAddTier) {
        btnAddTier.addEventListener("click", function () {
            // Check if form already exists
            if (document.getElementById("addTierRow")) return;

            // If table doesn't exist, create it (or wrapper)
            // If list is empty/text, clear it and start table structure? 
            // Better: Prepend a form UI or replace the "No tiers" message with form + list placeholder.

            // Simplest: Show a SweetAlert or Modal if available.
            // Since we want native behavior, let's inject a "New Tier" form at the top of the container

            const formHtml = `
                <div class="card p-3 mb-3 border bg-light" id="addTierForm">
                    <h6 class="mb-2">Add New Tier</h6>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small">Min Qty</label>
                            <input type="number" id="newMin" class="form-control form-control-sm" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Max Qty (Optional)</label>
                            <input type="number" id="newMax" class="form-control form-control-sm" min="1" placeholder="Empty for ∞">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Unit Price</label>
                            <input type="number" id="newPrice" class="form-control form-control-sm" min="0" step="100">
                        </div>
                        <div class="col-md-2">
                            <button id="btnSaveTier" class="btn btn-sm btn-primary w-100">Save</button>
                        </div>
                    </div>
                </div>
            `;

            // Insert before list
            const div = document.createElement("div");
            div.innerHTML = formHtml;
            tiersList.parentNode.insertBefore(div.firstElementChild, tiersList);

            // Attach Save Handler
            document.getElementById("btnSaveTier").addEventListener("click", saveTier);
        });
    }

    function saveTier() {
        const min = document.getElementById("newMin").value;
        const max = document.getElementById("newMax").value;
        const price = document.getElementById("newPrice").value;

        if (!min || !price) {
            alert("Min Qty and Price are required.");
            return;
        }

        const formData = new FormData();
        formData.append("qty_min", min);
        if (max) formData.append("qty_max", max);
        formData.append("unit_price", price);

        fetch(API_BASE + "/store", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Remove form
                    document.getElementById("addTierForm").remove();
                    // Reload list
                    loadTiers();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => alert("Error: " + err.message));
    }

    function deleteTier(id) {
        // Use Independent Delete URL if available (preferred)
        const deleteUrl = window.TIERS_DELETE_API_URL
            ? (window.TIERS_DELETE_API_URL + "/" + id)
            : (API_BASE + "/delete/" + id);

        fetch(deleteUrl, {
            method: "POST"
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadTiers();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => alert("Error: " + err.message));
    }

});
