<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    direccion: String, // "lat,lng" format
});

const emit = defineEmits(['update:location']);

const lat = ref(-17.7833); // Santa Cruz default
const lng = ref(-63.1821);
const map = ref(null);
const marker = ref(null);

async function ensureLeaflet() {
    if (typeof window === 'undefined') return;
    if (window.L) return window.L;

    if (!document.getElementById('leaflet-css')) {
        const link = document.createElement('link');
        link.id = 'leaflet-css';
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
    }

    if (!document.getElementById('leaflet-script')) {
        await new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.id = 'leaflet-script';
            s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            s.async = true;
            s.onload = () => resolve(true);
            s.onerror = (e) => reject(e);
            document.body.appendChild(s);
        });
    } else {
        await new Promise((resolve) => {
            const timer = setInterval(() => {
                if (window.L) {
                    clearInterval(timer);
                    resolve(true);
                }
            }, 50);
        });
    }
    return window.L;
}

const initMap = async () => {
    const L = await ensureLeaflet();
    
    // Parse initial location if exists
    if (props.direccion && props.direccion.includes(',')) {
        const [pLat, pLng] = props.direccion.split(',').map(Number);
        if (!isNaN(pLat) && !isNaN(pLng)) {
            lat.value = pLat;
            lng.value = pLng;
        }
    }

    map.value = L.map('map-picker').setView([lat.value, lng.value], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map.value);

    marker.value = L.marker([lat.value, lng.value], {
        draggable: true
    }).addTo(map.value);

    // Initial emit
    emit('update:location', { lat: lat.value, lng: lng.value });

    // Update lat/lng on drag end
    marker.value.on('dragend', (event) => {
        const position = event.target.getLatLng();
        lat.value = position.lat;
        lng.value = position.lng;
        emit('update:location', { lat: lat.value, lng: lng.value });
    });

    // Update lat/lng on map click
    map.value.on('click', (e) => {
        const { lat: clickLat, lng: clickLng } = e.latlng;
        marker.value.setLatLng([clickLat, clickLng]);
        lat.value = clickLat;
        lng.value = clickLng;
        emit('update:location', { lat: lat.value, lng: lng.value });
    });
};

onMounted(() => {
    setTimeout(initMap, 500); // Small delay for rendering
});
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-map-marker-alt text-red-600"></i> Ubicación de Entrega
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Mueve el marcador o haz clic en el mapa para indicar el punto exacto de entrega.
        </p>
        
        <div id="map-picker" class="w-full h-[300px] rounded-lg border border-gray-300 shadow-inner z-10 transition-all duration-300"></div>
        
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="block text-xs text-gray-400 font-bold uppercase">Latitud</span>
                <span class="text-gray-700 font-mono text-sm">{{ lat.toFixed(6) }}</span>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="block text-xs text-gray-400 font-bold uppercase">Longitud</span>
                <span class="text-gray-700 font-mono text-sm">{{ lng.toFixed(6) }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
#map-picker {
    cursor: crosshair;
}
</style>
