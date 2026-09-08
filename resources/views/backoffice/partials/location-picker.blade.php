{{-- Sélecteur de localisation sur carte (Bénin). Cliquer sur la carte place le marqueur et remplit latitude/longitude. --}}
<div x-data="locationPicker({ lat: {{ $lat ?? 'null' }}, lng: {{ $lng ?? 'null' }} })">
    <label class="label">Localisation sur la carte</label>
    <div x-ref="map" class="h-64 w-full rounded-xl border border-gray-200 shadow-sm"></div>
    <input type="hidden" name="latitude" :value="lat">
    <input type="hidden" name="longitude" :value="lng">
    <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-gray-500">
            <i class="bi bi-geo-alt text-hb-orange"></i>
            Cliquez sur la carte pour positionner l'établissement :
            <span class="font-mono font-semibold text-hb-blue" x-text="lat.toFixed(6) + ', ' + lng.toFixed(6)"></span>
        </p>
        <span x-show="!hasPoint" class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">
            <i class="bi bi-exclamation-triangle"></i> Localisation non définie
        </span>
    </div>

    <script>
        function locationPicker(initial) {
            const hasInit = initial.lat !== null && initial.lng !== null;
            return {
                lat: initial.lat !== null ? initial.lat : 9.3077,
                lng: initial.lng !== null ? initial.lng : 2.3158,
                hasPoint: hasInit,
                _map: null,
                _marker: null,
                init() {
                    if (typeof L === 'undefined') return;
                    this._map = L.map(this.$refs.map, { scrollWheelZoom: false }).setView([this.lat, this.lng], hasInit ? 13 : 7);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap', maxZoom: 19
                    }).addTo(this._map);
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
                    this._marker = L.marker([lat, lng]).addTo(this._map);
                }
            };
        }
    </script>
</div>