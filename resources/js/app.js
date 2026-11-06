import Alpine from 'alpinejs';
import L from 'leaflet';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

/* ---------- Mavzu (dark / light) ---------- */
Alpine.store('theme', {
    mode: localStorage.getItem('theme') || 'system',
    init() { this.apply(); matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => this.apply()); },
    apply() {
        const dark = this.mode === 'dark' || (this.mode === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', dark);
    },
    toggle() {
        const isDark = document.documentElement.classList.contains('dark');
        this.mode = isDark ? 'light' : 'dark';
        try { localStorage.setItem('theme', this.mode); } catch (e) {}
        this.apply();
        if (document.body.dataset.auth === '1') {
            fetch(`/theme/${this.mode}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf() } });
        }
    },
    get dark() { return document.documentElement.classList.contains('dark'); },
});

/* ---------- Xarita ---------- */
const PIN = {
    city: ['#0e4272', '🏙️'], attraction: ['#ff6b4a', '📍'], hotel: ['#14b8a6', '🛏️'],
    restaurant: ['#f59e0b', '🍽️'], uzbek: ['#16a34a', '🥘'], airport: ['#6366f1', '✈️'], default: ['#64748b', '•'],
};
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const icon = (type) => {
    const [bg, em] = PIN[type] || PIN.default;
    return L.divIcon({ className: '', html: `<div class="map-pin" style="background:${bg}"><span>${em}</span></div>`, iconSize: [28, 28], iconAnchor: [14, 28], popupAnchor: [0, -26] });
};

window.createMap = (el, opts = {}) => {
    const map = L.map(el, { scrollWheelZoom: opts.scroll ?? false }).setView(opts.center || [30, 40], opts.zoom || 3);
    L.tileLayer(el.dataset.tiles, { maxZoom: 19, attribution: el.dataset.attribution }).addTo(map);
    const layer = L.layerGroup().addTo(map);
    const api = {
        map, layer, markers: new Map(),
        setPoints(points, { fit = true, onPick = null } = {}) {
            layer.clearLayers(); api.markers.clear();
            const b = [];
            points.forEach((p) => {
                if (p.lat == null || p.lng == null) return;
                const m = L.marker([p.lat, p.lng], { icon: icon(p.type), title: p.name });
                const link = p.url ? `<a href="${esc(p.url)}" style="color:#0e6fa8;font-weight:600">${esc(el.dataset.open || 'Open')} →</a>` : '';
                m.bindPopup(`<strong>${esc(p.name)}</strong>${p.address ? `<br><small>${esc(p.address)}</small>` : ''}<br>${link}`);
                if (onPick) m.on('click', () => onPick(p));
                m.addTo(layer); api.markers.set(p.id, m); b.push([p.lat, p.lng]);
            });
            if (fit && b.length) map.fitBounds(b, { padding: [40, 40], maxZoom: 13 });
        },
        route(points, color = '#ff6b4a') {
            const pts = points.filter((p) => p.lat != null).map((p) => [p.lat, p.lng]);
            if (pts.length > 1) L.polyline(pts, { color, weight: 4, opacity: .8, dashArray: '8 8' }).addTo(layer);
        },
    };
    setTimeout(() => map.invalidateSize(), 150);
    return api;
};

Alpine.data('mapView', (points = [], opts = {}) => ({
    init() {
        const m = window.createMap(this.$refs.map, opts);
        m.setPoints(points);
        if (opts.route) m.route(points);
        if (!points.length) this.empty = true;
    },
    empty: false,
}));

Alpine.data('fullMap', (cfg) => ({
    types: ['city', 'attraction', 'hotel', 'restaurant', 'uzbek', 'airport'],
    active: ['city', 'attraction', 'hotel', 'restaurant', 'uzbek', 'airport'],
    q: '', loading: true, points: [], selected: [], routeLine: null, km: 0, api: null,
    async init() {
        this.api = window.createMap(this.$refs.map, { scroll: true });
        await this.load();
    },
    async load() {
        this.loading = true;
        try {
            const r = await fetch(`${cfg.url}?types=${this.active.join(',')}&q=${encodeURIComponent(this.q)}`);
            this.points = await r.json();
            this.api.setPoints(this.points, { onPick: (p) => this.pick(p) });
            this.drawRoute();
        } finally { this.loading = false; }
    },
    toggle(t) { this.active = this.active.includes(t) ? this.active.filter((x) => x !== t) : [...this.active, t]; this.load(); },
    pick(p) { if (this.selecting && !this.selected.find((s) => s.id === p.id)) { this.selected.push(p); this.drawRoute(); } },
    selecting: false,
    remove(i) { this.selected.splice(i, 1); this.drawRoute(); },
    clear() { this.selected = []; this.drawRoute(); },
    drawRoute() {
        if (this.routeLine) { this.api.map.removeLayer(this.routeLine); this.routeLine = null; }
        const pts = this.selected.map((p) => [p.lat, p.lng]);
        this.km = 0;
        for (let i = 1; i < pts.length; i++) this.km += this.api.map.distance(pts[i - 1], pts[i]) / 1000;
        if (pts.length > 1) this.routeLine = window.L.polyline(pts, { color: '#ff6b4a', weight: 4, dashArray: '8 8' }).addTo(this.api.map);
    },
    directionsUrl() {
        if (this.selected.length < 2) return '#';
        const route = this.selected.map((p) => `${p.lat}%2C${p.lng}`).join(';');
        return `https://www.openstreetmap.org/directions?engine=fossgis_osrm_foot&route=${route}`;
    },
}));
window.L = L;

/* ---------- Qidiruv takliflari ---------- */
Alpine.data('suggest', (url) => ({
    q: '', items: [], open: false, t: null,
    input() {
        clearTimeout(this.t);
        if (this.q.trim().length < 2) { this.items = []; return; }
        this.t = setTimeout(async () => {
            const r = await fetch(`${url}?q=${encodeURIComponent(this.q)}`);
            this.items = await r.json(); this.open = true;
        }, 200);
    },
}));

/* ---------- Valyuta konvertori ---------- */
Alpine.data('converter', (url) => ({
    amount: 100, from: 'USD', to: 'UZS', result: null, rate: null, stale: false, updated: null, loading: false,
    init() { this.run(); },
    swap() { [this.from, this.to] = [this.to, this.from]; this.run(); },
    async run() {
        if (this.amount === '' || this.amount < 0) return;
        this.loading = true;
        try {
            const r = await fetch(`${url}?amount=${this.amount}&from=${this.from}&to=${this.to}`, { headers: { Accept: 'application/json' } });
            const d = await r.json();
            this.result = d.formatted; this.rate = d.rate; this.stale = d.stale; this.updated = d.updated_at;
        } finally { this.loading = false; }
    },
}));

/* ---------- Byudjet kalkulyatori ---------- */
Alpine.data('budgetCalc', (cfg) => ({
    cats: cfg.cats, amounts: Object.fromEntries(cfg.cats.map((c) => [c, ''])), travelers: 2, days: 7,
    currency: cfg.currency, rates: cfg.rates, chart: null, labels: cfg.labels,
    country: '', budget: 500, scenario: null, scLoading: false,
    toUsd(v) { return (parseFloat(v) || 0) / (this.rates[this.currency] || 1); },
    get total() { return this.cats.reduce((s, c) => s + (parseFloat(this.amounts[c]) || 0), 0); },
    get perPerson() { return this.travelers > 0 ? this.total / this.travelers : 0; },
    get perDay() { return this.days > 0 ? this.total / this.days : 0; },
    fmt(v, cur = this.currency) { return new Intl.NumberFormat('en-US', { maximumFractionDigits: ['UZS', 'KZT', 'KRW', 'JPY', 'IDR'].includes(cur) ? 0 : 2 }).format(v) + ' ' + cur; },
    get totalUsd() { return this.total / (this.rates[this.currency] || 1); },
    get totalUzs() { return this.totalUsd * (this.rates.UZS || 0); },
    init() {
        this.$watch('amounts', () => this.draw()); this.$watch('currency', () => this.draw());
        this.$nextTick(() => this.draw());
    },
    draw() {
        const canvas = this.$refs.chart; if (!canvas) return;
        const data = this.cats.map((c) => parseFloat(this.amounts[c]) || 0);
        const cfgc = { type: 'doughnut', data: { labels: this.cats.map((c) => this.labels[c]), datasets: [{ data, backgroundColor: ['#0e4272', '#14b8a6', '#ff6b4a', '#f59e0b', '#6366f1', '#ec4899', '#22c55e', '#06b6d4', '#94a3b8'], borderWidth: 0 }] }, options: { plugins: { legend: { position: 'bottom', labels: { color: getComputedStyle(document.body).color } } }, cutout: '62%' } };
        if (this.chart) { this.chart.data = cfgc.data; this.chart.update(); } else { this.chart = new Chart(canvas, cfgc); }
    },
    async check() {
        if (!this.country) return;
        this.scLoading = true;
        const p = new URLSearchParams({ country: this.country, budget: this.budget, currency: this.currency, travelers: this.travelers, days: this.days });
        try { const r = await fetch(`${cfg.checkUrl}?${p}`, { headers: { Accept: 'application/json' } }); this.scenario = await r.json(); } finally { this.scLoading = false; }
    },
}));

/* ---------- Reja: kunlik xarita ---------- */
Alpine.data('planMap', (days) => ({
    day: 0,
    init() {
        this.api = window.createMap(this.$refs.map, { scroll: false });
        this.show();
    },
    show() {
        const pts = days[this.day] || [];
        this.api.setPoints(pts.map((p, i) => ({ ...p, id: 'p' + i, address: null, url: null })));
        this.api.route(pts);
    },
    pick(i) { this.day = i; this.show(); },
}));

/* ---------- AI yordamchi ---------- */
Alpine.data('assistant', (url) => ({
    open: false, msg: '', busy: false, log: [],
    async send() {
        const text = this.msg.trim(); if (!text || this.busy) return;
        this.log.push({ me: true, t: text }); this.msg = ''; this.busy = true;
        this.$nextTick(() => this.scroll());
        try {
            const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() }, body: JSON.stringify({ message: text }) });
            const d = await r.json();
            this.log.push({ me: false, t: d.answer || d.message || '…' });
        } catch (e) { this.log.push({ me: false, t: '⚠️' }); }
        this.busy = false; this.$nextTick(() => this.scroll());
    },
    scroll() { const b = this.$refs.box; if (b) b.scrollTop = b.scrollHeight; },
}));

/* ---------- Toast ---------- */
Alpine.data('toast', () => ({ show: true, init() { setTimeout(() => (this.show = false), 5000); } }));

Alpine.start();
