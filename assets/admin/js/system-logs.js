// public/assets/admin/js/system-logs.js
// Realtime log viewer with polling - HARDENED

(function () {
    'use strict';
    
    const path = window.location.pathname;
    const baseUrl = path.includes('/eventprint/public')
        ? window.location.origin + '/eventprint/public'
        : window.location.origin;
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

        // Render logs (SQL already orders DESC - newest first)
        const fragment = document.createDocumentFragment();
        logs.forEach(log => {
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

    // View context modal with card format
    function showContext(context) {
        const modal = new bootstrap.Modal(document.getElementById('contextModal'));
        const container = document.getElementById('contextJson');

        // Convert context to card format
        const cardHTML = generateContextCard(context);
        container.innerHTML = cardHTML;

        // Store JSON for copy function
        window.currentContextJSON = JSON.stringify(context, null, 2);

        modal.show();
    }

    /**
     * Generate card-based HTML for context data
     */
    function generateContextCard(context) {
        if (!context || typeof context !== 'object') {
            return '<p class="text-muted text-center py-4">No context data available</p>';
        }

        let html = '<div class="context-cards">';

        Object.entries(context).forEach(([key, value]) => {
            const icon = getIconForKey(key);
            const displayKey = formatKeyName(key);
            const displayValue = formatValue(value);

            html += `
                <div class="context-card-item">
                    <div class="context-key">
                        <i class="fas fa-${icon} me-2 text-primary"></i>
                        <strong>${displayKey}</strong>
                    </div>
                    <div class="context-value">${displayValue}</div>
                </div>
            `;
        });

        html += '</div>';
        return html;
    }

    function getIconForKey(key) {
        const iconMap = {
            'id': 'hashtag', 'user_id': 'user', 'username': 'user-circle',
            'email': 'envelope', 'phone': 'phone', 'product': 'box',
            'category': 'tag', 'status': 'info-circle', 'created_at': 'clock',
            'ip_address': 'network-wired', 'settings': 'cog', 'message': 'comment'
        };
        const lowerKey = key.toLowerCase();
        for (const [keyword, icon] of Object.entries(iconMap)) {
            if (lowerKey.includes(keyword)) return icon;
        }
        return 'circle';
    }

    function formatKeyName(key) {
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatValue(value) {
        if (value === null) return '<span class="text-muted fst-italic">null</span>';
        if (value === undefined) return '<span class="text-muted fst-italic">undefined</span>';
        if (typeof value === 'boolean') {
            return value ? '<span class="badge bg-success">true</span>' : '<span class="badge bg-secondary">false</span>';
        }
        if (typeof value === 'number') return `<code class="text-primary">${value}</code>`;
        if (typeof value === 'object') {
            return `<pre class="mb-0 p-2 bg-light border rounded" style="font-size: 12px;">${JSON.stringify(value, null, 2)}</pre>`;
        }
        const escaped = String(value).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        if (escaped.match(/^https?:\/\//)) {
            return `<a href="${escaped}" target="_blank">${escaped}</a>`;
        }
        return `<span>${escaped}</span>`;
    }

    /**
     * Copy context JSON to clipboard
     */
    window.copyContextToClipboard = function () {
        if (!window.currentContextJSON) {
            alert('No context data to copy');
            return;
        }

        navigator.clipboard.writeText(window.currentContextJSON).then(() => {
            const btn = document.getElementById('btnCopyContext');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-secondary');

            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2000);
        }).catch(err => {
            alert('Failed to copy: ' + err);
        });
    };

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
