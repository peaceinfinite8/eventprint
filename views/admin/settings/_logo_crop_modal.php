<!-- _logo_crop_modal.php — No preview panel -->
<div class="modal fade" id="logoCropModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="crop-header">
        <div class="title-wrap">
          <div class="title"><i class="fas fa-crop-alt me-2"></i>Sesuaikan Logo</div>
          <div class="sub">Drag untuk geser, scroll untuk zoom. Pakai 1:1 kalau mau aman buat logo.</div>
        </div>

        <button type="button" class="btn-close-danger" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="crop-shell">
        <div class="crop-stage">
          <img id="cropperImage" alt="Crop target">
        </div>

        <!-- Controls (replaces preview side) -->
        <div class="crop-controls">
          <div class="left">
            <div class="btn-group" role="group" aria-label="Aspect ratio">
              <button type="button" class="btn btn-sm btn-outline-secondary active" id="ratioFree">Free</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" id="ratioSquare">1:1</button>
            </div>

            <div class="help">Tips: umumnya logo enak pakai <b>1:1</b>.</div>
          </div>

          <div class="right zoom-group">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="cropZoomOut" title="Zoom out">
              <i class="fas fa-search-minus"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="cropReset" title="Reset">
              <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="cropZoomIn" title="Zoom in">
              <i class="fas fa-search-plus"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="crop-footer">
        <div class="left">
          <button type="button" class="btn btn-sm btn-outline-dark" data-bs-dismiss="modal">Batal</button>
        </div>

        <div class="center"></div>

        <div class="right">
          <button type="button" class="btn btn-sm btn-primary" id="cropApply">
            <i class="fas fa-check me-1"></i> Pakai
          </button>
        </div>
      </div>

    </div>
  </div>
</div>
