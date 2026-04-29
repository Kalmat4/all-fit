<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

// Fix Leaflet default marker icons broken by Vite bundling
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
    iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
    iconUrl:       new URL('leaflet/dist/images/marker-icon.png',    import.meta.url).href,
    shadowUrl:     new URL('leaflet/dist/images/marker-shadow.png',  import.meta.url).href,
})

const props = defineProps({
    currentZone: { type: Object, default: null },
})

// ── Oblast data ───────────────────────────────────────────────────────────────
const OBLASTS = [
    { name: 'Акмолинская',            west: 68.9, south: 50.5, east: 75.5, north: 54.0 },
    { name: 'Актюбинская',            west: 55.0, south: 46.0, east: 67.5, north: 51.5 },
    { name: 'Алматинская',            west: 75.0, south: 42.5, east: 80.5, north: 45.5 },
    { name: 'Атырауская',             west: 49.0, south: 45.5, east: 56.5, north: 48.5 },
    { name: 'Восточно-Казахстанская', west: 79.5, south: 46.5, east: 87.5, north: 50.5 },
    { name: 'Жамбылская',             west: 69.5, south: 42.0, east: 77.0, north: 45.5 },
    { name: 'Западно-Казахстанская',  west: 49.5, south: 49.5, east: 56.5, north: 52.5 },
    { name: 'Карагандинская',         west: 68.0, south: 46.0, east: 78.0, north: 50.5 },
    { name: 'Костанайская',           west: 61.0, south: 51.0, east: 68.5, north: 54.5 },
    { name: 'Кызылординская',         west: 60.0, south: 43.5, east: 69.5, north: 47.5 },
    { name: 'Мангыстауская',          west: 50.0, south: 41.5, east: 57.0, north: 46.0 },
    { name: 'Павлодарская',           west: 73.5, south: 50.5, east: 79.5, north: 54.5 },
    { name: 'Северо-Казахстанская',   west: 67.0, south: 53.0, east: 73.5, north: 55.5 },
    { name: 'Туркестанская',          west: 66.0, south: 41.0, east: 72.5, north: 44.5 },
    { name: 'Улытауская',             west: 60.0, south: 46.5, east: 68.0, north: 50.5 },
    { name: 'г. Алматы',              west: 76.6, south: 43.0, east: 77.3, north: 43.5 },
    { name: 'г. Астана',              west: 71.2, south: 50.9, east: 71.8, north: 51.3 },
]

// ── Reactive state ────────────────────────────────────────────────────────────
const mapEl          = ref(null)
const selectedOblast = ref(null)
const hotspots       = ref([])
const loading        = ref(false)
const errorMsg       = ref(null)

// ── Leaflet internals (non-reactive) ─────────────────────────────────────────
let map             = null
let rectLayers      = {}
let hotspotLayer    = null

// ── Styles ───────────────────────────────────────────────────────────────────
const normalStyle = () => ({ color: '#e85c00', weight: 1.5, fillColor: '#e85c00', fillOpacity: 0.08 })
const activeStyle = () => ({ color: '#ff4500', weight: 2.5, fillColor: '#ff4500', fillOpacity: 0.22 })

function oblastBounds(o) {
    return [[o.south, o.west], [o.north, o.east]]
}

// ── Map init ──────────────────────────────────────────────────────────────────
function initMap() {
    map = L.map(mapEl.value, { center: [48.0, 67.0], zoom: 5 })

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map)

    hotspotLayer = L.layerGroup().addTo(map)

    OBLASTS.forEach(oblast => {
        const rect = L.rectangle(oblastBounds(oblast), normalStyle())
            .addTo(map)
            .bindTooltip(oblast.name, { sticky: true })

        rect.on('click', () => selectOblast(oblast))
        rectLayers[oblast.name] = rect
    })

    if (props.currentZone) {
        const saved = OBLASTS.find(o => o.name === props.currentZone.oblast_name)
        if (saved) restoreSelection(saved)
    }
}

// ── Selection ─────────────────────────────────────────────────────────────────
function selectOblast(oblast) {
    if (selectedOblast.value) {
        rectLayers[selectedOblast.value.name]?.setStyle(normalStyle())
    }
    selectedOblast.value = oblast
    rectLayers[oblast.name]?.setStyle(activeStyle())
    map.fitBounds(oblastBounds(oblast), { padding: [30, 30] })
    saveAndFetch(oblast)
}

async function saveAndFetch(oblast) {
    loading.value  = true
    errorMsg.value = null
    hotspots.value = []
    hotspotLayer.clearLayers()

    try {
        const { data } = await axios.patch('/zone', {
            oblast_name: oblast.name,
            bbox_west:   oblast.west,
            bbox_south:  oblast.south,
            bbox_east:   oblast.east,
            bbox_north:  oblast.north,
        })
        hotspots.value = data.hotspots
        renderHotspots(data.hotspots)
    } catch {
        errorMsg.value = 'Ошибка получения данных о пожарах.'
    } finally {
        loading.value = false
    }
}

async function restoreSelection(oblast) {
    selectedOblast.value = oblast
    rectLayers[oblast.name]?.setStyle(activeStyle())
    loading.value = true
    try {
        const { data } = await axios.get('/zone/fires')
        hotspots.value = data.hotspots
        renderHotspots(data.hotspots)
    } catch { /* silent */ } finally {
        loading.value = false
    }
}

// ── Hotspot markers ───────────────────────────────────────────────────────────
function renderHotspots(spots) {
    hotspotLayer.clearLayers()
    spots.forEach(spot => {
        L.circleMarker([spot.lat, spot.lon], {
            radius: 6, color: '#cc0000', fillColor: '#ff2200', fillOpacity: 0.85, weight: 1,
        })
        .bindPopup(
            `<b>Очаг возгорания</b><br>` +
            `Яркость: ${spot.brightness} K<br>` +
            `FRP: ${spot.frp} МВт<br>` +
            `Уверенность: ${spot.confidence}<br>` +
            `Период: ${spot.daynight === 'D' ? 'День' : 'Ночь'}`
        )
        .addTo(hotspotLayer)
    })
}

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(initMap)
onBeforeUnmount(() => map?.remove())

const logout = () => router.post('/logout')
</script>

<template>
    <AppLayout>
        <Head title="AgriFireShield" />

        <!-- Header -->
        <header class="afs-header">
            <div class="afs-header__inner">
                <Link href="/dashboard" class="afs-logo">
                    <span class="afs-logo__icon">🔥</span>
                    <span class="afs-logo__text">AgriFireShield</span>
                </Link>
                <nav class="afs-nav">
                    <Link href="/dashboard" class="afs-nav__btn">Главная</Link>
                    <Link href="/profile"   class="afs-nav__btn">Профиль</Link>
                    <button class="afs-nav__logout" @click="logout">Выйти</button>
                </nav>
            </div>
        </header>

        <!-- Error banner -->
        <div v-if="errorMsg" class="afs-error-banner">{{ errorMsg }}</div>

        <!-- Workspace -->
        <div class="afs-workspace">

            <!-- Sidebar -->
            <aside class="afs-sidebar">
                <div class="afs-sidebar__header">
                    <span class="afs-sidebar__title">Регионы Казахстана</span>
                    <span v-if="hotspots.length" class="afs-fire-badge">🔥 {{ hotspots.length }}</span>
                </div>

                <ul class="afs-oblast-list">
                    <li
                        v-for="oblast in OBLASTS"
                        :key="oblast.name"
                        class="afs-oblast-item"
                        :class="{ 'afs-oblast-item--active': selectedOblast?.name === oblast.name }"
                        @click="selectOblast(oblast)"
                    >
                        <span class="afs-oblast-item__name">{{ oblast.name }}</span>
                        <span
                            v-if="selectedOblast?.name === oblast.name && hotspots.length"
                            class="afs-oblast-item__count"
                        >
                            {{ hotspots.length }}
                        </span>
                    </li>
                </ul>
            </aside>

            <!-- Map panel -->
            <div class="afs-map-panel">
                <div ref="mapEl" class="afs-map"></div>

                <!-- Loading overlay -->
                <div v-if="loading" class="afs-map-loading">
                    <div class="afs-spinner"></div>
                    <span>Загрузка данных NASA FIRMS...</span>
                </div>

                <!-- Summary bar -->
                <div v-if="selectedOblast && !loading" class="afs-summary-bar">
                    <span class="afs-summary-bar__oblast">{{ selectedOblast.name }}</span>
                    <span v-if="hotspots.length" class="afs-summary-bar__count">
                        Обнаружено очагов: <strong>{{ hotspots.length }}</strong>
                    </span>
                    <span v-else class="afs-summary-bar__none">
                        Активных пожаров не обнаружено
                    </span>
                </div>

                <!-- Placeholder when nothing selected -->
                <div v-if="!selectedOblast && !loading" class="afs-map-hint">
                    Выберите регион на карте или в списке слева
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* ── Header ──────────────────────────────────────────────────────────────── */
.afs-header {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d1a00 100%);
    border-bottom: 3px solid #e85c00;
    box-shadow: 0 2px 12px rgba(232, 92, 0, 0.3);
    position: sticky;
    top: 0;
    z-index: 1000;
}
.afs-header__inner {
    max-width: 100%;
    padding: 0 24px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.afs-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    user-select: none;
}
.afs-logo__icon {
    font-size: 28px;
    filter: drop-shadow(0 0 6px rgba(255, 120, 0, 0.8));
}
.afs-logo__text {
    font-size: 20px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.5px;
}
.afs-nav { display: flex; align-items: center; gap: 8px; }
.afs-nav__btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #e0d6cc;
    text-decoration: none;
    border: 1px solid transparent;
    transition: background 0.18s, color 0.18s;
}
.afs-nav__btn:hover,
.afs-nav__btn.router-link-active {
    background: rgba(232, 92, 0, 0.18);
    color: #fff;
    border-color: rgba(232, 92, 0, 0.4);
}
.afs-nav__logout {
    display: inline-flex;
    align-items: center;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #e0d6cc;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.2);
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
}
.afs-nav__logout:hover {
    background: rgba(255, 60, 0, 0.2);
    color: #fff;
    border-color: rgba(255, 60, 0, 0.5);
}

/* ── Error banner ─────────────────────────────────────────────────────────── */
.afs-error-banner {
    background: #3d0a00;
    border-bottom: 2px solid #cc2200;
    color: #ff8888;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 500;
}

/* ── Workspace ────────────────────────────────────────────────────────────── */
.afs-workspace {
    display: flex;
    height: calc(100vh - 64px);
    overflow: hidden;
}

/* ── Sidebar ──────────────────────────────────────────────────────────────── */
.afs-sidebar {
    width: 260px;
    min-width: 220px;
    background: #1a1a1a;
    border-right: 2px solid #2d1a00;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.afs-sidebar__header {
    padding: 14px 18px;
    background: #2d1a00;
    border-bottom: 1px solid #3d2400;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.afs-sidebar__title {
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.afs-fire-badge {
    background: #e85c00;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}
.afs-oblast-list {
    list-style: none;
    padding: 6px 0;
    margin: 0;
    overflow-y: auto;
    flex: 1;
}
.afs-oblast-list::-webkit-scrollbar { width: 4px; }
.afs-oblast-list::-webkit-scrollbar-track { background: #111; }
.afs-oblast-list::-webkit-scrollbar-thumb { background: #3d2400; border-radius: 2px; }
.afs-oblast-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 18px;
    cursor: pointer;
    border-left: 3px solid transparent;
    transition: background 0.15s, border-color 0.15s;
}
.afs-oblast-item:hover {
    background: rgba(232, 92, 0, 0.12);
    border-left-color: rgba(232, 92, 0, 0.5);
}
.afs-oblast-item--active {
    background: rgba(232, 92, 0, 0.2);
    border-left-color: #e85c00;
}
.afs-oblast-item__name {
    color: #c8bfb5;
    font-size: 13px;
    font-weight: 500;
}
.afs-oblast-item--active .afs-oblast-item__name { color: #fff; }
.afs-oblast-item__count {
    background: #cc2200;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
}

/* ── Map panel ────────────────────────────────────────────────────────────── */
.afs-map-panel {
    flex: 1;
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.afs-map {
    flex: 1;
    min-height: 0;
    z-index: 1;
}

/* ── Loading overlay ──────────────────────────────────────────────────────── */
.afs-map-loading {
    position: absolute;
    inset: 0;
    background: rgba(26, 26, 26, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    z-index: 500;
    color: #fff;
    font-size: 14px;
}
.afs-spinner {
    width: 36px;
    height: 36px;
    border: 3px solid rgba(232, 92, 0, 0.3);
    border-top-color: #e85c00;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Summary bar ──────────────────────────────────────────────────────────── */
.afs-summary-bar {
    background: #2d1a00;
    border-top: 2px solid #e85c00;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-shrink: 0;
    z-index: 2;
}
.afs-summary-bar__oblast { color: #ff8c00; font-weight: 700; font-size: 14px; }
.afs-summary-bar__count  { color: #e0d6cc; font-size: 13px; }
.afs-summary-bar__count strong { color: #ff4500; font-size: 15px; }
.afs-summary-bar__none   { color: #777; font-size: 13px; }

/* ── Map hint ─────────────────────────────────────────────────────────────── */
.afs-map-hint {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(26, 26, 26, 0.82);
    color: #c8bfb5;
    font-size: 13px;
    padding: 10px 20px;
    border-radius: 20px;
    border: 1px solid #3d2400;
    pointer-events: none;
    z-index: 10;
    white-space: nowrap;
}
</style>
