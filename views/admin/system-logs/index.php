<?php
// views/admin/system-logs/index.php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$baseUrl = rtrim($baseUrl, '/');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">System Logs (Realtime)</h1>
        <p class="text-muted small mb-0">Monitor aktivitas sistem dan error log</p>
    </div>
</div>

<style>
    /* Context Card Styles */
    .context-cards {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .context-card-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 16px;
        transition: all 0.2s ease;
    }

    .context-card-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    }

    .context-key {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .context-value {
        font-size: 14px;
        color: #111827;
        padding-left: 28px;
    }

    .context-value code {
        background: #f3f4f6;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>

<div class="dash-container-card fade-in delay-1">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
        <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-history me-2"></i>Activity Logs</h5>
        <div class="d-flex gap-2">
            <select id="filterLevel" class="form-select form-select-sm" style="width: 120px;">
                <option value="">All Levels</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="error">Error</option>
            </select>
            <select id="filterSource" class="form-select form-select-sm" style="width: 120px;">
                <option value="">All Sources</option>
                <option value="api">API</option>
                <option value="admin">Admin</option>
                <option value="system">System</option>
            </select>
            <button id="btnClearOld" class="btn btn-sm btn-outline-danger shadow-sm">
                <i class="fas fa-trash-alt me-1"></i> Clear Old (30d+)
            </button>
        </div>
    </div>

    <div class="p-4">
        <div class="mb-3 d-flex align-items-center">
            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill me-2"
                id="statusBadge">
                <i class="fas fa-circle-notch fa-spin me-1"></i> Connecting...
            </span>
            <span class="text-muted small">Auto-refresh: <span id="refreshCountdown"
                    class="fw-bold text-dark">2</span>s</span>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 60px;">ID</th>
                        <th style="width: 100px;">Level</th>
                        <th style="width: 100px;">Source</th>
                        <th>Message</th>
                        <th style="width: 180px;">Created At</th>
                        <th class="text-end pe-4" style="width: 80px;">Context</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2 opacity-25"></i>
                            <p class="small mb-0">Loading logs...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="noLogsMessage" class="text-center text-muted py-5" style="display: none;">
            <div class="mb-3">
                <i class="fas fa-clipboard-list fa-3x text-muted opacity-25"></i>
            </div>
            <p class="text-muted mb-0">No logs found matching filters.</p>
        </div>
    </div>
</div>

<!-- Context Modal -->
<div class="modal fade" id="contextModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-code me-2"></i>Log Context
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom">
                    <small class="text-muted">JSON Data</small>
                    <button id="btnCopyContext" class="btn btn-sm btn-outline-secondary"
                        onclick="copyContextToClipboard()">
                        <i class="fas fa-copy me-1"></i>Copy
                    </button>
                </div>
                <pre id="contextJson" class="p-3 m-0"
                    style="max-height: 500px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 13px; background: #f8f9fa; border-radius: 0;"></pre>
            </div>
        </div>
    </div>
</div>

<script src="<?= $baseUrl; ?>/assets/admin/js/system-logs.js?v=<?= time(); ?>"></script>