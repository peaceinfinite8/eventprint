// public/assets/admin/js/system-logs.js
// Realtime log viewer with polling - HARDENED

(function () {
    'use strict';

    const baseUrl = window.location.origin + '/eventprint/public';
    const apiUrl = baseUrl + '/admin/api/system-logs';

    let lastLogId = 0;
    let isPolling = false;
    let pollInterval = null;
    let countdown = 2;
    let isUnauthorized = false;

    const tbody = document.getElementById('logsTableBody');
    const statusBadge = document.getElementById('statusBadge');
    const refreshCountdown = document.getElementById('refreshCountdown');
    const noLogsMessage = document.getElementById('noLogsMessage');
    const filterLevel = document.getElementById('filterLevel');
    const filterSource = document.getElementById('filterSource');
    const btnClearOld = document.getElementById('btnClearOld');

    // Level badge colors
    const levelColors = {
        'info': 'bg-primary',
        'warning': 'bg-warning',
        'error': 'bg-danger'
    };

    // Source badge colors
    const sourceColors = {
        'api': 'bg-info',
        'admin': 'bg-success',
        'system': 'bg-secondary'
    };

    // Fetch logs with HARDENING
    async function fetchLogs(afterId = 0) {
        const level = filterLevel.value;
        const source = filterSource.value;

        let url = `${apiUrl}?after_id=${afterId}&limit=200`;
        if (level) url += `&level=${level}`;
        if (source) url += `&source=${source}`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });

            const contentType = response.headers.get('content-type');

            // HARDENING: Check if response is actually JSON
            if (!contentType || !contentType.includes('application/json')) {
                const bodySnippet = await response.text();
                console.error('Expected JSON but received:', contentType);
                console.error('Response body (first 200 chars):', bodySnippet.substring(0, 200));

                // Check if it's an unauthorized redirect
                if (bodySnippet.includes('<!doctype html') || bodySnippet.includes('<html')) {
                    updateStatus('error', 'Session expired - Please refresh page');
                    stopPolling();
                    isUnauthorized = true;
                    return null;
                }

                throw new Error(`Server returned ${contentType} instead of JSON`);
            }

            // HARDENING: Check HTTP status
            if (response.status === 401 || response.status === 403) {
                const data = await response.json();
                updateStatus('error', data.message || 'Unauthorized');
                stopPolling();
                isUnauthorized = true;
                return null;
            }

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP ${response.status}`);
            }

            const data = await response.json();
            return data;

        } catch (error) {
            console.error('Fetch logs error:', error);
            updateStatus('error', error.message);
            return null;
        }
    }

    // Render logs
    function renderLogs(logs) {
        if (!logs || logs.length === 0) {
            if (tbody.children.length === 0 || tbody.children[0].children.length === 1) {
                // No logs at all
                tbody.innerHTML = '';
                noLogsMessage.style.display = 'block';
            }
            return;
        }

        noLogsMessage.style.display = 'none';

        // Prepend new logs (reverse order so newest on top)
        const fragment = document.createDocumentFragment();
        logs.reverse().forEach(log => {
            const tr = document.createElement('tr');

            const levelClass = levelColors[log.level] || 'bg-secondary';
            const sourceClass = sourceColors[log.source] || 'bg-secondary';

            // Extract useful context info
            let extraInfo = '';
            if (log.context) {
                const ctx = log.context;
                if (ctx.action) extraInfo += `<span class="badge bg-light text-dark border ms-1" title="Action">${escapeHtml(ctx.action)}</span>`;
                if (ctx.entity) extraInfo += `<span class="badge bg-light text-dark border ms-1" title="Entity">${escapeHtml(ctx.entity)}</span>`;
                if (ctx.user_id) extraInfo += `<span class="badge bg-secondary ms-1" title="User ID">UID:${ctx.user_id}</span>`;
            }

            const contextBtn = log.context
                ? `<button class="btn btn-sm btn-outline-secondary btn-view-context">View</button>`
                : '-';

            tr.innerHTML = `
                <td>${log.id}</td>
                <td><span class="badge ${levelClass}">${escapeHtml(log.level)}</span></td>
                <td><span class="badge ${sourceClass}">${escapeHtml(log.source)}</span></td>
                <td>
                    <div>${escapeHtml(log.message)}</div>
                    ${extraInfo ? `<div class="mt-1 small">${extraInfo}</div>` : ''}
                </td>
                <td>${formatTimestamp(log.created_at)}</td>
                <td>${contextBtn}</td>
            `;

            // SAFER data binding
            if (log.context) {
                const btn = tr.querySelector('.btn-view-context');
                if (btn) {
                    // Store directly in DOM property, not attribute (safest)
                    btn.logContext = log.context;
                }
            }

            fragment.appendChild(tr);

            // Update last ID
            if (log.id > lastLogId) {
                lastLogId = log.id;
            }
        });

        // Remove loading message if present
        if (tbody.children[0]?.children.length === 1) {
            tbody.innerHTML = '';
        }

        // Prepend new logs
        tbody.insertBefore(fragment, tbody.firstChild);

        // Cap at 200 rows
        while (tbody.children.length > 200) {
            tbody.removeChild(tbody.lastChild);
        }
    }

    // Initial load
    async function initialLoad() {
        updateStatus('loading', 'Loading logs...');
        const data = await fetchLogs(0);

        if (isUnauthorized) {
            return; // Stop if unauthorized
        }

        if (data && data.success) {
            tbody.innerHTML = '';
            renderLogs(data.logs);
            updateStatus('success', 'Connected');
            startPolling();
        } else {
            updateStatus('error', 'Failed to load');
        }
    }

    // Poll for new logs
    async function pollNewLogs() {
        if (isPolling || isUnauthorized) return;

        isPolling = true;
        const data = await fetchLogs(lastLogId);
        isPolling = false;

        if (isUnauthorized) {
            stopPolling();
            return;
        }

        if (data && data.success) {
            renderLogs(data.logs);
            if (data.logs.length > 0) {
                updateStatus('success', `Loaded ${data.logs.length} new`);
            }
        }
    }

    // Start polling
    function startPolling() {
        if (pollInterval) return;

        // Poll every 2 seconds
        pollInterval = setInterval(() => {
            countdown = 2;
            pollNewLogs();
        }, 2000);

        // Countdown timer
        setInterval(() => {
            if (countdown > 0) {
                countdown--;
                refreshCountdown.textContent = countdown;
            }
        }, 1000);
    }

    // Stop polling
    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    // Update status badge
    function updateStatus(type, message) {
        statusBadge.className = 'badge';
        if (type === 'success') statusBadge.classList.add('bg-success');
        else if (type === 'error') statusBadge.classList.add('bg-danger');
        else statusBadge.classList.add('bg-secondary');

        statusBadge.textContent = message;
    }

    // Clear old logs
    async function clearOldLogs() {
        if (!confirm('Delete all logs older than 30 days?')) return;

        try {
            const response = await fetch(baseUrl + '/admin/system-logs/clear-old', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });

            const data = await response.json();
            if (data.success) {
                alert(`Deleted ${data.deleted} old log entries`);
                location.reload();
            } else {
                alert('Failed to clear logs: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    // View context modal
    function showContext(context) {
        const modal = new bootstrap.Modal(document.getElementById('contextModal'));
        document.getElementById('contextJson').textContent = JSON.stringify(context, null, 2);
        modal.show();
    }

    // Event listeners
    filterLevel.addEventListener('change', () => {
        lastLogId = 0;
        initialLoad();
    });

    filterSource.addEventListener('change', () => {
        lastLogId = 0;
        initialLoad();
    });

    btnClearOld.addEventListener('click', clearOldLogs);

    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-view-context');
        if (btn && btn.logContext) {
            showContext(btn.logContext);
        }
    });

    // Helpers
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString('id-ID', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    // Initialize
    initialLoad();
})();
