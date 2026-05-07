@extends('layouts.admin')
@section('title', 'Live Map')
@section('page-title', '🗺 Live Map')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
.map-wrap {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 12px;
    height: calc(100vh - 108px);
}
.map-left { display:flex; flex-direction:column; gap:10px; overflow:hidden; }
.map-stats { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.map-stat { background:var(--bg-card); border:1px solid var(--border); border-radius:10px; padding:10px; text-align:center; }
.map-stat-val { font-size:22px; font-weight:800; color:var(--accent-light); line-height:1; }
.map-stat-lbl { font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-top:3px; }
.map-date-row { display:flex; gap:6px; align-items:center; }
.map-date-row input { flex:1; }
.rep-scroll { flex:1; overflow-y:auto; background:var(--bg-card); border:1px solid var(--border); border-radius:10px; padding:6px; }
.rep-scroll-title { font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.8px; padding:4px 6px 8px; }
.rep-row { display:flex; align-items:center; gap:8px; padding:9px 10px; border-radius:8px; cursor:pointer; transition:background .15s,border-color .15s; border:1px solid transparent; margin-bottom:2px; }
.rep-row:hover  { background:var(--bg-hover); }
.rep-row.active { background:var(--accent-glow); border-color:rgba(99,102,241,.35); }
.rep-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.rep-dot.online  { background:var(--green); box-shadow:0 0 5px var(--green); }
.rep-dot.offline { background:var(--text-muted); }
.rep-info { flex:1; min-width:0; }
.rep-info-name { font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rep-info-sub  { font-size:11px; color:var(--text-muted); }
.rep-visits-badge { font-size:11px; font-weight:700; background:var(--bg-surface); color:var(--accent-light); border-radius:5px; padding:2px 7px; border:1px solid var(--border); }
.btn-clear-sel { width:100%; justify-content:center; margin-top:6px; }

/* Map container */
.map-right { position:relative; height:100%; }
#map { height:100%; border-radius:12px; border:1px solid var(--border); }
.leaflet-container { background:#0d0f1a !important; }

/* ── Loading overlay ── */
#map-loader {
    position:absolute; inset:0;
    background:rgba(13,15,26,.75);
    backdrop-filter:blur(4px);
    border-radius:12px;
    z-index:800;
    display:none;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:14px;
}
#map-loader.visible { display:flex; }
.loader-spinner {
    width:42px; height:42px;
    border:3px solid var(--border);
    border-top-color:var(--accent);
    border-radius:50%;
    animation:spin .7s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }
.loader-text { color:var(--text-secondary); font-size:13px; }

/* Visit panel */
.visit-panel {
    position:absolute; top:12px; right:12px;
    width:290px;
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:12px; box-shadow:var(--shadow);
    z-index:700; display:none;
    animation:slideIn .2s ease;
}
@keyframes slideIn { from{opacity:0;transform:translateX(16px)} to{opacity:1;transform:translateX(0)} }
.visit-panel-header { display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--border); }
.visit-panel-header h3 { font-size:13px; font-weight:700; flex:1; margin-right:8px; }
.visit-panel-close { background:none; border:none; color:var(--text-muted); font-size:18px; cursor:pointer; }
.visit-panel-close:hover { color:var(--red); }
.visit-panel-body { padding:12px 14px; }
.vp-row { display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid var(--border); font-size:12.5px; }
.vp-row:last-child { border-bottom:none; }
.vp-label { color:var(--text-muted); font-size:10px; font-weight:700; text-transform:uppercase; }
.vp-val   { font-weight:500; text-align:right; max-width:170px; }
.visit-panel-footer { padding:10px 14px; border-top:1px solid var(--border); }
.visit-panel-footer a { display:block; text-align:center; }

/* Legend */
.map-legend {
    position:absolute; bottom:14px; left:14px; z-index:500;
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:8px; padding:10px 14px; font-size:11px;
    display:flex; flex-direction:column; gap:5px;
}
.legend-item { display:flex; align-items:center; gap:7px; color:var(--text-secondary); }
.legend-dot  { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.legend-line { width:20px; height:3px; border-radius:2px; flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="page-header" style="margin-bottom:10px">
    <div>
        <h1>Live Field Map</h1>
        <p>Click a rep to see their full route & visits • Click a visit marker for details</p>
    </div>
</div>

<div class="map-wrap">
    {{-- Left sidebar --}}
    <div class="map-left">
        <div class="map-stats">
            <div class="map-stat"><div class="map-stat-val" id="stat-online">—</div><div class="map-stat-lbl">Online Now</div></div>
            <div class="map-stat"><div class="map-stat-val" id="stat-visits">—</div><div class="map-stat-lbl">Visits Today</div></div>
        </div>

        <div class="map-date-row">
            <input type="date" id="route-date" class="form-control"
                   value="{{ today()->toDateString() }}" max="{{ today()->toDateString() }}">
            <button class="btn btn-outline btn-sm" onclick="reloadRoute()">Go</button>
        </div>

        <div class="rep-scroll">
            <div class="rep-scroll-title">Field Reps</div>
            <div id="rep-list"></div>
            <button id="btn-show-all" class="btn btn-outline btn-sm btn-clear-sel" style="display:none" onclick="clearSelection()">
                ✕ Clear Selection
            </button>
        </div>
    </div>

    {{-- Map --}}
    <div class="map-right">
        <div id="map"></div>

        {{-- Loader --}}
        <div id="map-loader">
            <div class="loader-spinner"></div>
            <div class="loader-text" id="loader-text">Loading route…</div>
        </div>

        {{-- Visit detail panel --}}
        <div class="visit-panel" id="visit-panel">
            <div class="visit-panel-header">
                <h3 id="vp-client">—</h3>
                <button class="visit-panel-close" onclick="closeVisitPanel()">✕</button>
            </div>
            <div class="visit-panel-body">
                <div class="vp-row"><span class="vp-label">Check-in</span><span class="vp-val" id="vp-checkin">—</span></div>
                <div class="vp-row"><span class="vp-label">Check-out</span><span class="vp-val" id="vp-checkout">—</span></div>
                <div class="vp-row"><span class="vp-label">Duration</span><span class="vp-val" id="vp-duration">—</span></div>
                <div class="vp-row"><span class="vp-label">Status</span><span class="vp-val" id="vp-status">—</span></div>
                <div class="vp-row"><span class="vp-label">Outcome</span><span class="vp-val" id="vp-outcome">—</span></div>
                <div class="vp-row"><span class="vp-label">Rating</span><span class="vp-val" id="vp-rating">—</span></div>
                <div class="vp-row"><span class="vp-label">Order (BDT)</span><span class="vp-val" id="vp-order">—</span></div>
                <div class="vp-row"><span class="vp-label">Notes</span><span class="vp-val" id="vp-notes" style="font-size:11px;color:var(--text-secondary)">—</span></div>
            </div>
            <div class="visit-panel-footer">
                <a id="vp-link" href="#" class="btn btn-primary btn-sm">View Full Details →</a>
            </div>
        </div>

        {{-- Legend --}}
        <div class="map-legend">
            <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div> Online rep</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--text-muted)"></div> Offline rep</div>
            <div class="legend-item"><div class="legend-line" style="background:var(--accent)"></div> Route trail</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div> Completed visit</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--blue)"></div> Active visit</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--red)"></div> Missed visit</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Map init ──────────────────────────────────────────────────────
const map = L.map('map', { zoomControl: false }).setView([23.7680, 90.4125], 12);
L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap © CARTO', maxZoom: 19
}).addTo(map);
L.control.zoom({ position: 'bottomright' }).addTo(map);

// ── State ─────────────────────────────────────────────────────────
const repMarkers  = {};   // id → L.Marker (current position dot)
let routePolyline = null;
let trailDots     = [];
let visitMarkers  = [];
let visitLabels   = [];
let selectedRepId = null;
let allRepsData   = [];

// ── Loader helpers ────────────────────────────────────────────────
function showLoader(text = 'Loading…') {
    document.getElementById('loader-text').textContent = text;
    document.getElementById('map-loader').classList.add('visible');
}
function hideLoader() {
    document.getElementById('map-loader').classList.remove('visible');
}

// ── Icons ─────────────────────────────────────────────────────────
function repIcon(online, selected) {
    const bg  = online   ? '#22c55e' : '#64748b';
    const bdr = selected ? '#818cf8' : '#fff';
    const glow = selected ? 'box-shadow:0 0 0 3px rgba(99,102,241,.55),0 3px 10px rgba(0,0,0,.5);' : 'box-shadow:0 3px 10px rgba(0,0,0,.5);';
    return L.divIcon({
        className: '',
        html: `<div style="width:36px;height:36px;border-radius:50%;background:${bg};border:3px solid ${bdr};${glow}display:flex;align-items:center;justify-content:center;font-size:17px;transition:all .2s">🚶</div>`,
        iconSize: [36, 36], iconAnchor: [18, 18],
    });
}
function visitIcon(status) {
    const cfg = {
        completed:   { bg: '#22c55e', label: '✓' },
        in_progress: { bg: '#3b82f6', label: '⟳' },
        missed:      { bg: '#ef4444', label: '✕' },
    };
    const c = cfg[status] || { bg: '#94a3b8', label: '?' };
    return L.divIcon({
        className: '',
        html: `<div style="width:28px;height:28px;border-radius:50%;background:${c.bg};border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px">${c.label}</div>`,
        iconSize: [28, 28], iconAnchor: [14, 14],
    });
}

// ── Clear route layers ────────────────────────────────────────────
function clearRouteLayers() {
    if (routePolyline) { map.removeLayer(routePolyline); routePolyline = null; }
    trailDots.forEach(d => map.removeLayer(d)); trailDots = [];
    visitMarkers.forEach(m => map.removeLayer(m)); visitMarkers = [];
    visitLabels.forEach(l => map.removeLayer(l));  visitLabels  = [];
    closeVisitPanel();
}

// ── Show / hide rep markers ───────────────────────────────────────
function setRepMarkersVisibility(onlyRepId) {
    Object.entries(repMarkers).forEach(([id, marker]) => {
        const intId = parseInt(id);
        if (onlyRepId === null || intId === onlyRepId) {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });
}

// ── Live data poll ────────────────────────────────────────────────
function loadLiveData() {
    fetch('/admin/map/live-data')
        .then(r => r.json())
        .then(({ reps }) => {
            allRepsData = reps;
            const online = reps.filter(r => r.is_online).length;
            const totalV = reps.reduce((s, r) => s + (r.visits_today || 0), 0);

            document.getElementById('stat-online').textContent  = online;
            document.getElementById('stat-visits').textContent  = totalV;
            document.getElementById('online-count').textContent = online;
            document.getElementById('rep-count').textContent    = `${online}/${reps.length} online`;

            // Build / update rep markers & sidebar
            const list = document.getElementById('rep-list');
            list.innerHTML = '';

            reps.forEach(rep => {
                // Current-position marker
                if (rep.lat && rep.lng) {
                    const ll   = [rep.lat, rep.lng];
                    const icon = repIcon(rep.is_online, selectedRepId === rep.id);
                    if (repMarkers[rep.id]) {
                        repMarkers[rep.id].setLatLng(ll).setIcon(icon);
                    } else {
                        repMarkers[rep.id] = L.marker(ll, { icon })
                            .addTo(map)
                            .on('click', () => selectRep(rep.id));
                    }
                }

                // Sidebar row
                const row = document.createElement('div');
                row.className = 'rep-row' + (selectedRepId === rep.id ? ' active' : '');
                row.id = `rep-row-${rep.id}`;
                row.innerHTML = `
                    <div class="rep-dot ${rep.is_online ? 'online' : 'offline'}"></div>
                    <div class="rep-info">
                        <div class="rep-info-name">${rep.name}</div>
                        <div class="rep-info-sub">${rep.last_seen || 'Never'}</div>
                    </div>
                    <span class="rep-visits-badge">${rep.visits_today}v</span>
                `;
                row.addEventListener('click', () => selectRep(rep.id));
                list.appendChild(row);
            });

            // Respect visibility if a rep is already selected
            if (selectedRepId !== null) setRepMarkersVisibility(selectedRepId);
        });
}

// ── Select rep ───────────────────────────────────────────────────
function selectRep(repId) {
    if (selectedRepId === repId) { clearSelection(); return; }  // toggle off

    selectedRepId = repId;

    // Sidebar highlight
    document.querySelectorAll('.rep-row').forEach(r => r.classList.remove('active'));
    document.getElementById(`rep-row-${repId}`)?.classList.add('active');

    // Update marker icons & visibility: hide all others
    Object.entries(repMarkers).forEach(([id, m]) =>
        m.setIcon(repIcon(true, parseInt(id) === repId))
    );
    setRepMarkersVisibility(repId);

    document.getElementById('btn-show-all').style.display = 'flex';

    clearRouteLayers();
    loadRoute(repId);
}

function clearSelection() {
    selectedRepId = null;
    clearRouteLayers();
    document.querySelectorAll('.rep-row').forEach(r => r.classList.remove('active'));
    document.getElementById('btn-show-all').style.display = 'none';
    // Restore all markers
    setRepMarkersVisibility(null);
    // Reset icons
    allRepsData.forEach(rep => {
        if (repMarkers[rep.id]) repMarkers[rep.id].setIcon(repIcon(rep.is_online, false));
    });
}

// ── Load route + visits ───────────────────────────────────────────
function loadRoute(repId) {
    const date = document.getElementById('route-date').value;
    const rep  = allRepsData.find(r => r.id === repId);
    showLoader(`Loading ${rep ? rep.name + "'s" : ''} route…`);

    fetch(`/admin/map/rep/${repId}/route?date=${date}`)
        .then(r => r.json())
        .then(({ points, visits }) => {
            hideLoader();

            // ── Draw GPS trail ──
            if (points.length > 1) {
                const latlngs = points.map(p => [p.lat, p.lng]);
                routePolyline = L.polyline(latlngs, {
                    color: '#6366f1', weight: 3.5, opacity: .9,
                }).addTo(map);

                // Small dots at each ping
                points.forEach(p => {
                    const d = L.circleMarker([p.lat, p.lng], {
                        radius: 3, color: '#6366f1',
                        fillColor: '#818cf8', fillOpacity: 1, weight: 1,
                    }).bindTooltip(p.recorded_at ?? '', { sticky: true, direction: 'top' })
                     .addTo(map);
                    trailDots.push(d);
                });

                map.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
            }

            // ── Visit markers ──
            visits.forEach((v, idx) => {
                if (!v.lat || !v.lng) return;

                const m = L.marker([v.lat, v.lng], {
                    icon: visitIcon(v.status), zIndexOffset: 1000,
                }).addTo(map).on('click', () => showVisitPanel(v));
                visitMarkers.push(m);

                // Label bubble
                const lbl = L.marker([v.lat, v.lng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:var(--bg-card);border:1px solid var(--border);border-radius:5px;padding:2px 8px;font-size:11px;font-weight:600;color:var(--text-primary);white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.4);margin-top:4px">${idx+1}. ${v.client_name}</div>`,
                        iconAnchor: [-16, 14],
                    }),
                    interactive: false,
                }).addTo(map);
                visitLabels.push(lbl);
            });

            // If no GPS trail but we have visits, fit to visits
            if (points.length <= 1 && visits.length) {
                const valid = visits.filter(v => v.lat && v.lng);
                if (valid.length && visitMarkers.length) {
                    const g = L.featureGroup(visitMarkers);
                    map.fitBounds(g.getBounds(), { padding: [60, 60] });
                }
            }
        })
        .catch(() => hideLoader());
}

function reloadRoute() { if (selectedRepId) loadRoute(selectedRepId); }

// ── Visit panel ───────────────────────────────────────────────────
const statusBadge = {
    completed:   '<span style="color:var(--green);font-weight:600">✓ Completed</span>',
    in_progress: '<span style="color:var(--blue);font-weight:600">⟳ In Progress</span>',
    missed:      '<span style="color:var(--red);font-weight:600">✕ Missed</span>',
};

function showVisitPanel(v) {
    document.getElementById('vp-client').textContent   = v.client_name;
    document.getElementById('vp-checkin').textContent  = v.checkin  || '—';
    document.getElementById('vp-checkout').textContent = v.checkout || '—';
    document.getElementById('vp-duration').textContent = v.duration ? `${v.duration} min` : '—';
    document.getElementById('vp-status').innerHTML     = statusBadge[v.status] || v.status;
    document.getElementById('vp-outcome').textContent  = v.outcome || '—';
    document.getElementById('vp-rating').textContent   = v.rating
        ? '★'.repeat(v.rating) + '☆'.repeat(5 - v.rating) : '—';
    document.getElementById('vp-order').textContent    = v.order_value
        ? Number(v.order_value).toLocaleString() : '—';
    document.getElementById('vp-notes').textContent    = v.notes || '—';
    document.getElementById('vp-link').href            = v.url;
    document.getElementById('visit-panel').style.display = 'block';
}
function closeVisitPanel() {
    document.getElementById('visit-panel').style.display = 'none';
}

// ── Boot ──────────────────────────────────────────────────────────
showLoader('Loading map data…');
loadLiveData();
setTimeout(hideLoader, 1500);   // hide initial loader after first load settles
setInterval(loadLiveData, 30000);
</script>
@endpush
