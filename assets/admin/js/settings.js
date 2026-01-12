/* ============================================================================
   settings.js — EventPrint Admin Settings
   - SweetAlert helpers (lazy-load)
   - Sales contacts (WA normalize + test link)
   - Logo cropper (no preview inside modal)
   - Google Maps preview (right column)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const page = window.__SETTINGS_PAGE__ || {};
    initSweetAlertHelpers();
    initContacts();
    initLogoCrop(page.initialLogoSrc || '');
    initGmapsPreviewRight();
});

/* ============================================================================
   SweetAlert helpers
   ========================================================================== */
function initSweetAlertHelpers() {
    window.ensureSwal = async function ensureSwal() {
        if (window.Swal) return true;

        await new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });

        return !!window.Swal;
    };

    window.toast = async function toast(icon, title) {
        if (!(await window.ensureSwal())) return;

        Swal.fire({
            toast: true,
            position: 'top',
            icon,
            title,
            showConfirmButton: false,
            timer: 1400,
            timerProgressBar: true
        });
    };

    window.confirmBox = async function confirmBox({
        title,
        text,
        icon = 'question',
        confirmText = 'Ya',
        cancelText = 'Batal'
    }) {
        if (!(await window.ensureSwal())) return true;

        const res = await Swal.fire({
            title,
            text,
            icon,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true,
            focusCancel: true
        });

        return res.isConfirmed;
    };
}

/* ============================================================================
   Contacts
   ========================================================================== */
function initContacts() {
    const container = document.getElementById('contacts-container');
    const addBtn = document.getElementById('add-contact');
    if (!addBtn || !container) return;

    const normalizeWaNumber = (raw) => {
        let s = (raw || '').toString().trim();
        if (!s) return '';

        s = s.replace(/[\s\-().]/g, '');
        if (s.startsWith('+')) s = s.slice(1);
        s = s.replace(/[^\d]/g, '');
        if (!s) return '';

        if (s.startsWith('08')) s = '62' + s.slice(1);
        else if (s.startsWith('8')) s = '62' + s;
        else if (s.startsWith('0')) s = '62' + s.slice(1);

        return s.length < 8 ? '' : s;
    };

    const disableWaBtn = (btn) => {
        if (!btn) return;
        btn.classList.add('is-disabled');
        btn.setAttribute('aria-disabled', 'true');
        btn.removeAttribute('href');
        btn.removeAttribute('target');
        btn.removeAttribute('rel');
        btn.setAttribute('role', 'button');
    };

    const enableWaBtn = (btn, href) => {
        if (!btn) return;
        btn.classList.remove('is-disabled');
        btn.setAttribute('aria-disabled', 'false');
        btn.setAttribute('href', href);
        btn.setAttribute('target', '_blank');
        btn.setAttribute('rel', 'noopener');
        btn.removeAttribute('role');
    };

    const updateRowState = (row) => {
        const input = row.querySelector('.wa-number');
        const btn = row.querySelector('.btn-wa-test');
        if (!input || !btn) return;

        const normalized = normalizeWaNumber(input.value);
        if (!normalized) return disableWaBtn(btn);

        enableWaBtn(btn, `https://wa.me/${normalized}`);
    };

    const createRow = () => {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 contact-row';
        row.innerHTML = `
      <div class="col-12 col-md-5">
        <input type="text" name="sales_contacts[name][]" class="form-control form-control-sm"
               placeholder="Nama / Label" autocomplete="off">
      </div>
      <div class="col-12 col-md-5">
        <input type="tel" inputmode="numeric" name="sales_contacts[number][]"
               class="form-control form-control-sm wa-number"
               placeholder="Nomor WhatsApp (contoh: 6281… atau 081…)" autocomplete="tel">
      </div>
      <div class="col-12 col-md-2">
        <div class="d-flex gap-2">
          <a class="btn btn-outline-secondary btn-sm flex-grow-1 btn-wa-test is-disabled"
             aria-disabled="true" title="Test WhatsApp">↗</a>
          <button type="button" class="btn btn-outline-danger btn-sm btn-icon remove-contact" title="Hapus kontak">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    `;
        return row;
    };

    const refreshAll = () => {
        container.querySelectorAll('.contact-row').forEach(updateRowState);
    };

    refreshAll();

    addBtn.addEventListener('click', () => {
        const row = createRow();
        container.appendChild(row);
        updateRowState(row);
        window.toast?.('success', 'Kontak ditambahkan');
    });

    container.addEventListener('input', (e) => {
        const input = e.target.closest('.wa-number');
        if (!input) return;
        const row = input.closest('.contact-row');
        if (row) updateRowState(row);
    });

    container.addEventListener(
        'blur',
        (e) => {
            const input = e.target.closest('.wa-number');
            if (!input) return;
            const row = input.closest('.contact-row');
            if (row) updateRowState(row);
        },
        true
    );

    container.addEventListener('click', async (e) => {
        const removeBtn = e.target.closest('.remove-contact');
        if (removeBtn) {
            const row = removeBtn.closest('.contact-row');
            if (!row) return;

            const ok = await window.confirmBox?.({
                title: 'Hapus kontak ini?',
                text: 'Kontak akan hilang dari form. Simpan pengaturan untuk menerapkan.',
                icon: 'warning',
                confirmText: 'Ya, hapus',
                cancelText: 'Batal'
            });

            if (!ok) return;
            row.remove();
            window.toast?.('success', 'Kontak dihapus');
            return;
        }

        const waBtn = e.target.closest('.btn-wa-test');
        if (waBtn && waBtn.classList.contains('is-disabled')) e.preventDefault();
    });

    window.__refreshContactsLinks = refreshAll;
}

/* ============================================================================
   Logo Crop (NO preview)
   ========================================================================== */
function initLogoCrop(initialSrc) {
    const logoBox = document.getElementById('logoBox');
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoEmpty = document.getElementById('logoEmpty');
    const logoError = document.getElementById('logoError');
    const logoActions = document.getElementById('logoActions');
    const logoChangeBtn = document.getElementById('logoChangeBtn');
    const logoClearBtn = document.getElementById('logoClearBtn');

    const modalEl = document.getElementById('logoCropModal');
    const cropImg = document.getElementById('cropperImage');

    const zoomIn = document.getElementById('cropZoomIn');
    const zoomOut = document.getElementById('cropZoomOut');
    const resetBtn = document.getElementById('cropReset');
    const applyBtn = document.getElementById('cropApply');

    const ratioFreeBtn = document.getElementById('ratioFree');
    const ratioSquareBtn = document.getElementById('ratioSquare');

    if (!logoInput || !modalEl || !cropImg) return;

    const MAX_SIZE = 2 * 1024 * 1024;
    const ALLOWED = ['image/png', 'image/jpeg', 'image/webp'];

    let cropper = null;
    let pickObjectUrl = null;
    let previewObjectUrl = null;
    let modalInstance = null;

    const setError = (msg) => {
        if (!logoError) return;
        logoError.textContent = msg || '';
        logoError.classList.toggle('d-none', !msg);
    };

    const setPreview = (url) => {
        if (!logoPreview) return;
        logoPreview.src = url;
        logoPreview.classList.remove('d-none');
        logoEmpty && logoEmpty.classList.add('d-none');
    };

    const restoreInitialPreview = () => {
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }

        if (initialSrc) {
            setPreview(initialSrc);
            return;
        }

        logoPreview && logoPreview.classList.add('d-none');
        logoEmpty && logoEmpty.classList.remove('d-none');
    };

    const validateFile = (file) => {
        if (!file) return 'File tidak valid.';
        if (!ALLOWED.includes(file.type)) return 'Format logo harus PNG / JPG / WEBP.';
        if (file.size > MAX_SIZE) return 'Ukuran logo maksimal 2MB.';
        return '';
    };

    const openCropModal = (file) => {
        const err = validateFile(file);
        if (err) {
            setError(err);
            restoreInitialPreview();
            logoActions && logoActions.classList.add('d-none');
            return;
        }

        setError('');

        if (pickObjectUrl) {
            URL.revokeObjectURL(pickObjectUrl);
            pickObjectUrl = null;
        }

        pickObjectUrl = URL.createObjectURL(file);
        cropImg.src = pickObjectUrl;

        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            setError('Bootstrap JS belum ter-load.');
            return;
        }

        modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    };

    if (logoBox) logoBox.addEventListener('click', () => logoInput.click());
    if (logoChangeBtn) logoChangeBtn.addEventListener('click', () => logoInput.click());

    if (logoClearBtn) {
        logoClearBtn.addEventListener('click', async () => {
            const ok = await window.confirmBox?.({
                title: 'Batalkan perubahan logo?',
                text: 'Logo akan kembali ke kondisi sebelumnya.',
                icon: 'warning',
                confirmText: 'Ya, batalkan',
                cancelText: 'Batal'
            });
            if (!ok) return;

            setError('');
            logoInput.value = '';
            restoreInitialPreview();
            logoActions && logoActions.classList.add('d-none');
            window.toast?.('success', 'Perubahan logo dibatalkan');
        });
    }

    logoInput.addEventListener('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;
        if (!file) return;
        openCropModal(file);
        this.value = '';
    });

    const setActiveRatio = (mode) => {
        if (!cropper) return;

        if (mode === 'free') {
            cropper.setAspectRatio(NaN);
            ratioFreeBtn && ratioFreeBtn.classList.add('active');
            ratioSquareBtn && ratioSquareBtn.classList.remove('active');
            return;
        }

        cropper.setAspectRatio(1);
        ratioSquareBtn && ratioSquareBtn.classList.add('active');
        ratioFreeBtn && ratioFreeBtn.classList.remove('active');
        cropper.reset();
    };

    ratioFreeBtn && ratioFreeBtn.addEventListener('click', () => setActiveRatio('free'));
    ratioSquareBtn && ratioSquareBtn.addEventListener('click', () => setActiveRatio('square'));

    modalEl.addEventListener('shown.bs.modal', () => {
        if (!cropImg.src) return;

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        cropper = new Cropper(cropImg, {
            viewMode: 1,
            aspectRatio: NaN,
            autoCropArea: 0.92,
            background: false,
            dragMode: 'move',
            responsive: true,
            guides: true,
            center: true,
            zoomOnWheel: true,
            wheelZoomRatio: 0.08,
            cropBoxResizable: true,
            cropBoxMovable: true,
            toggleDragModeOnDblclick: false
        });

        ratioFreeBtn && ratioFreeBtn.classList.add('active');
        ratioSquareBtn && ratioSquareBtn.classList.remove('active');
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        if (pickObjectUrl) {
            URL.revokeObjectURL(pickObjectUrl);
            pickObjectUrl = null;
        }

        cropImg.src = '';

        if (modalInstance) {
            modalInstance.dispose();
            modalInstance = null;
        }
    });

    zoomIn && zoomIn.addEventListener('click', () => cropper && cropper.zoom(0.1));
    zoomOut && zoomOut.addEventListener('click', () => cropper && cropper.zoom(-0.1));
    resetBtn && resetBtn.addEventListener('click', () => cropper && cropper.reset());

    const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

    applyBtn &&
        applyBtn.addEventListener('click', async () => {
            if (!cropper) return;

            const data = cropper.getData(true);
            const w = Math.max(1, Math.round(data.width));
            const h = Math.max(1, Math.round(data.height));

            const maxSide = 1024;
            const scale = Math.min(1, maxSide / Math.max(w, h));
            const outW = clamp(Math.round(w * scale), 1, maxSide);
            const outH = clamp(Math.round(h * scale), 1, maxSide);

            const canvas = cropper.getCroppedCanvas({
                width: outW,
                height: outH,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', 0.92));
            if (!blob) return setError('Gagal memproses crop.');
            if (blob.size > MAX_SIZE) return setError('Hasil crop melebihi 2MB. Crop lebih kecil atau zoom out.');

            const file = new File([blob], 'logo.webp', { type: 'image/webp' });

            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                logoInput.files = dt.files;
            } catch {
                return setError('Browser tidak mendukung set file programmatically.');
            }

            if (previewObjectUrl) {
                URL.revokeObjectURL(previewObjectUrl);
                previewObjectUrl = null;
            }

            previewObjectUrl = URL.createObjectURL(blob);
            setPreview(previewObjectUrl);

            logoActions && logoActions.classList.remove('d-none');
            setError('');

            modalInstance && modalInstance.hide();
            window.toast?.('success', 'Logo siap disimpan');
        });

    /* ==========================================================================
       Settings form submit lock
       ========================================================================== */
    const form = document.getElementById('settingsForm');
    const saveBtn = document.getElementById('saveBtn');
    if (form && saveBtn) {
        form.addEventListener('submit', () => {
            saveBtn.disabled = true;
            saveBtn.classList.add('disabled');
            const el = saveBtn.querySelector('.btn-text');
            if (el) el.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
        });
    }
}

/* ============================================================================
   Google Maps Preview (Right Column)
   ========================================================================== */
function initGmapsPreviewRight() {
    const embedInput = document.getElementById('gmapsEmbedInput');
    const linkInput = document.getElementById('mapsLinkInput');
    const addressInput = document.querySelector('textarea[name="address"]');
    const frame = document.getElementById('gmapsPreviewFrame');
    const empty = document.getElementById('gmapsPreviewEmpty');
    const err = document.getElementById('gmapsEmbedError');
    const openNewTab = document.getElementById('gmapsOpenNewTab');

    if (!frame) return;

    const extractIframeSrc = (html) => {
        if (!html) return '';
        const m = String(html).match(/src\s*=\s*["']([^"']+)["']/i);
        if (m && m[1]) return m[1].trim();

        const t = String(html).trim();
        if (/^https?:\/\/www\.google\.com\/maps\/embed\?/i.test(t)) return t;
        return '';
    };

    const isAllowedEmbedUrl = (url) => {
        try {
            const u = new URL(url);
            const hostOk = u.hostname === 'www.google.com' || u.hostname === 'google.com';
            if (!hostOk) return false;
            if (u.pathname.startsWith('/maps/embed')) return true;
            if (u.searchParams.get('output') === 'embed') return true;
            return false;
        } catch {
            return false;
        }
    };

    const buildFallbackEmbedSrc = () => {
        const addr = (addressInput ? addressInput.value : '').trim();
        if (!addr) return '';
        return `https://www.google.com/maps?q=${encodeURIComponent(addr)}&output=embed`;
    };

    const setError = (msg) => {
        if (!err) return;
        err.textContent = msg || '';
        err.classList.toggle('d-none', !msg);
    };

    const setPreviewSrc = (src) => {
        if (!src) {
            frame.src = '';
            empty && empty.classList.remove('d-none');
            return;
        }
        frame.src = src;
        empty && empty.classList.add('d-none');
    };

    const updateOpenLink = () => {
        if (!openNewTab) return;
        const linkVal = (linkInput ? linkInput.value : '').trim();

        if (!linkVal) {
            openNewTab.classList.add('d-none');
            openNewTab.removeAttribute('href');
            return;
        }

        openNewTab.href = linkVal;
        openNewTab.classList.remove('d-none');
    };

    const updatePreview = () => {
        const embedVal = embedInput ? embedInput.value : '';
        let src = extractIframeSrc(embedVal);

        if (!src) {
            src = buildFallbackEmbedSrc();
            setError('');
            setPreviewSrc(src);
            updateOpenLink();
            return;
        }

        if (!isAllowedEmbedUrl(src)) {
            setPreviewSrc('');
            setError('Embed tidak valid. Gunakan embed Google Maps (/maps/embed) atau isi alamat untuk auto-preview.');
            updateOpenLink();
            return;
        }

        setError('');
        setPreviewSrc(src);
        updateOpenLink();
    };

    let t = null;
    const onInput = () => {
        clearTimeout(t);
        t = setTimeout(updatePreview, 250);
    };

    updatePreview();

    embedInput && embedInput.addEventListener('input', onInput);
    linkInput && linkInput.addEventListener('input', onInput);
    addressInput && addressInput.addEventListener('input', onInput);
}
