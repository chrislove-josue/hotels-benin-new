<?php
    $pickerLat = is_numeric($lat ?? 'x') ? (float) $lat : 'null';
    $pickerLng = is_numeric($lng ?? 'x') ? (float) $lng : 'null';
?>

<div x-data="locationPicker({ lat: <?php echo e($pickerLat); ?>, lng: <?php echo e($pickerLng); ?> })">
    <input type="hidden" name="latitude" :value="hasPoint ? lat : ''">
    <input type="hidden" name="longitude" :value="hasPoint ? lng : ''">

    <div class="overflow-hidden rounded-2xl border border-hb-blue/10 bg-white shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 bg-hb-cream px-4 py-3">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue">
                    <i class="bi bi-geo-alt-fill text-lg"></i>
                </span>
                <div>
                    <p class="text-sm font-bold text-hb-blue">Localisation géographique</p>
                    <p class="text-[11px] text-gray-500" x-text="hasPoint ? 'Position enregistrée' : 'Cliquez sur la carte pour placer le marqueur'"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="hasPoint" x-cloak class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-[11px] font-semibold text-green-700">
                    <i class="bi bi-check-circle-fill"></i> Positionnée
                </span>
                <span x-show="!hasPoint" x-cloak class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2.5 py-1 text-[11px] font-semibold text-yellow-700">
                    <i class="bi bi-exclamation-triangle-fill"></i> Non définie
                </span>
                <button type="button" @click="clearPoint()" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-gray-600 transition hover:border-hb-orange hover:text-hb-orange">
                    <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                </button>
            </div>
        </div>

        <div x-ref="map" class="h-[20rem] w-full bg-gray-100 md:h-[24rem]"></div>

        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 bg-white px-4 py-2.5">
            <p class="flex items-center gap-1.5 text-xs text-gray-500">
                <i class="bi bi-crosshair text-hb-orange"></i>
                <span x-show="hasPoint" x-cloak>
                    Coordonnées :
                    <span class="font-mono font-semibold text-hb-blue" x-text="lat.toFixed(6) + ', ' + lng.toFixed(6)"></span>
                </span>
                <span x-show="!hasPoint" x-cloak>Cliquez sur la carte pour positionner l'établissement au Bénin.</span>
            </p>
            <button type="button" @click="focusBenin()" class="inline-flex items-center gap-1.5 rounded-lg bg-hb-blue/5 px-2.5 py-1.5 text-[11px] font-semibold text-hb-blue transition hover:bg-hb-blue/10">
                <i class="bi bi-zoom-in"></i> Voir le Bénin
            </button>
        </div>
    </div>

    <style>
        .hb-marker-wrap { position: relative; width: 40px; height: 44px; }
        .hb-marker-wrap .hb-marker-pulse {
            position: absolute; left: 0; top: 0; width: 40px; height: 40px; border-radius: 50%;
            background: rgba(245, 154, 35, 0.35); animation: hbMarkerPulse 1.8s ease-out infinite; z-index: 0;
        }
        .hb-marker-wrap .hb-marker-pin {
            position: relative; left: 0; top: 0; width: 40px; height: 40px;
            display: flex; align-items: flex-end; justify-content: center;
        }
        .hb-marker-wrap .hb-marker-pin::before {
            content: ''; position: absolute; bottom: 2px; left: 50%; width: 26px; height: 26px;
            transform: translateX(-50%) rotate(45deg); border-radius: 50% 50% 50% 0;
            background: linear-gradient(135deg, #F59A23, #D9820F);
            border: 3px solid #fff; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.35);
        }
        .hb-marker-wrap .hb-marker-pin i {
            position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
            color: #fff; font-size: 15px; z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        @keyframes hbMarkerPulse {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(1.6); opacity: 0; }
        }
    </style>

    <script>
        function locationPicker(initial) {
            const hasInit = initial.lat !== null && initial.lng !== null;
            const BENIN_CENTER = [9.3077, 2.3158];
            return {
                lat: hasInit ? initial.lat : BENIN_CENTER[0],
                lng: hasInit ? initial.lng : BENIN_CENTER[1],
                hasPoint: hasInit,
                _map: null,
                _marker: null,
                init() {
                    if (typeof L === 'undefined') return;
                    this._map = L.map(this.$refs.map, {
                        scrollWheelZoom: false,
                        zoomControl: true
                    }).setView([this.lat, this.lng], hasInit ? 14 : 7);

                    // Fond avec relief (ombrage + courbes de niveau), rues détaillées et localités.
                    const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                        attribution: 'Données &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, fond &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (CC-BY-SA)',
                        maxZoom: 17,
                        subdomains: 'abc'
                    }).addTo(this._map);

                    // Repli automatique sur le fond OSM standard si les tuiles topo échouent.
                    let fallenBack = false;
                    this._map.on('tileerror', () => {
                        if (fallenBack) return;
                        fallenBack = true;
                        this._map.removeLayer(topo);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                            maxZoom: 19
                        }).addTo(this._map);
                    });

                    if (hasInit) this._setMarker(this.lat, this.lng);

                    this._map.on('click', (e) => {
                        this.lat = e.latlng.lat;
                        this.lng = e.latlng.lng;
                        this.hasPoint = true;
                        this._setMarker(this.lat, this.lng);
                    });

                    this.$nextTick(() => this._map.invalidateSize());
                },
                _setMarker(lat, lng) {
                    if (this._marker) this._marker.remove();
                    const icon = L.divIcon({
                        className: '',
                        html: '<div class="hb-marker-wrap"><span class="hb-marker-pulse"></span><span class="hb-marker-pin"><i class="bi bi-geo-alt-fill"></i></span></div>',
                        iconSize: [40, 44],
                        iconAnchor: [20, 42],
                        popupAnchor: [0, -40]
                    });
                    this._marker = L.marker([lat, lng], { icon }).addTo(this._map);
                },
                clearPoint() {
                    if (this._marker) { this._marker.remove(); this._marker = null; }
                    this.hasPoint = false;
                    this.lat = BENIN_CENTER[0];
                    this.lng = BENIN_CENTER[1];
                    if (this._map) this._map.setView(BENIN_CENTER, 7);
                },
                focusBenin() {
                    if (!this._map) return;
                    if (this.hasPoint) {
                        this._map.setView([this.lat, this.lng], Math.max(this._map.getZoom(), 14));
                    } else {
                        this._map.setView(BENIN_CENTER, 7);
                    }
                }
            };
        }
    </script>
</div><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/partials/location-picker.blade.php ENDPATH**/ ?>