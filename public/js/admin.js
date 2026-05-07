// SalesTrack Pro — Admin JS

// Clock
function updateClock() {
    const el = document.getElementById('topbar-time');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();

// Sidebar toggle (mobile)
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar = document.getElementById('sidebar');
if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}

// Online rep count badge (poll every 30s)
async function fetchRepCount() {
    try {
        const res = await fetch('/admin/map/live-data');
        const json = await res.json();
        const online = (json.reps || []).filter(r => r.is_online).length;
        const total  = (json.reps || []).length;
        const badge  = document.getElementById('online-count');
        const repEl  = document.getElementById('rep-count');
        if (badge) badge.textContent = online;
        if (repEl) repEl.textContent = `${online}/${total} online`;
    } catch (e) {}
}
fetchRepCount();
setInterval(fetchRepCount, 30000);

// Confirm delete
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
        if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
});
