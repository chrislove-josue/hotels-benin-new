@php
    $pickerLat = is_numeric($lat ?? 'x') ? (float) $lat : 'null';
    $pickerLng = is_numeric($lng ?? 'x') ? (float) $lng : 'null';
@endphp
{{-- Sélecteur de localisation sur carte (Bénin). Cliquer sur la carte place le marqueur et remplit latitude/longitude. --}}
<div x-data="locationPicker({ lat: {{ $pickerLat }}, lng: {{ $pickerLng }} })">
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

        <div class="border-b border-gray-100 bg-white p-4">
            <div class="relative">
                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="relative flex-1">
                        <input type="search" x-model.trim="query" @input.debounce.300ms="lookupSuggestions()" @focus="if (query) lookupSuggestions()" @keydown.down.prevent="highlightNext()" @keydown.up.prevent="highlightPrev()" @keydown.enter.prevent="chooseHighlighted()" @focusout="setTimeout(() => showSuggestions = false, 200)"
                            class="input-field w-full" placeholder="Tapez une ville, un quartier ou un lieu au Bénin (ex : Cotonou, Abomey, Ouidah)"
                            aria-label="Rechercher une ville ou un lieu au Bénin" autocomplete="off" role="combobox" aria-expanded="showSuggestions">

                        {{-- Suggestions --}}
                        <ul x-ref="suggestionsList" x-show="showSuggestions && suggestions.length" x-cloak class="absolute inset-x-0 top-full z-[1000] mt-1 max-h-72 overflow-auto rounded-xl border border-gray-200 bg-white shadow-xl">
                            <template x-for="(s, i) in suggestions" :key="i">
                                <li>
                                    <button type="button" @mousedown.prevent="chooseSuggestion(s)" @mouseenter="selectedIndex = i"
                                        class="flex w-full items-start gap-2.5 px-3 py-2.5 text-left transition hover:bg-hb-cream"
                                        :class="selectedIndex === i ? 'bg-hb-cream' : ''">
                                        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-hb-blue/10 text-hb-blue"><i class="bi bi-geo-alt text-sm"></i></span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-hb-blue-deeper" x-text="s.display_name"></span>
                                            <span class="block text-[11px] text-gray-500" x-text="s.type"></span>
                                        </span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                        <p x-show="showSuggestions && query && !searching && !suggestions.length" x-cloak class="absolute inset-x-0 top-full z-[1000] mt-1 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs text-gray-500 shadow-xl">
                            Aucun lieu trouvé. Appuyez sur Entrée pour rechercher sur la carte, ou placez le marqueur manuellement.
                        </p>
                    </div>
                    <button type="button" @click="searchAddress()" :disabled="searching || !query"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-hb-blue px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hb-blue-dark disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="bi" :class="searching ? 'bi-arrow-repeat animate-spin' : 'bi-search'"></i>
                        Rechercher
                    </button>
                    <button type="button" @click="useCurrentPosition()" :disabled="locating"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-hb-blue/20 px-4 py-2.5 text-sm font-semibold text-hb-blue transition hover:bg-hb-blue/5 disabled:cursor-not-allowed disabled:opacity-50">
                        <i class="bi bi-crosshair"></i> Ma position
                    </button>
                </div>
            </div>
            <p x-show="message" x-text="message" class="mt-2 text-xs text-gray-500" role="status"></p>
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
            <div class="flex items-center gap-2">
                <input type="number" x-model.number="lat" @change="syncCoordinates()" step="0.0000001" min="-90" max="90"
                    class="w-32 rounded-lg border border-gray-200 px-2 py-1.5 font-mono text-xs text-hb-blue" aria-label="Latitude">
                <input type="number" x-model.number="lng" @change="syncCoordinates()" step="0.0000001" min="-180" max="180"
                    class="w-32 rounded-lg border border-gray-200 px-2 py-1.5 font-mono text-xs text-hb-blue" aria-label="Longitude">
                <button type="button" @click="focusBenin()" class="inline-flex items-center gap-1.5 rounded-lg bg-hb-blue/5 px-2.5 py-1.5 text-[11px] font-semibold text-hb-blue transition hover:bg-hb-blue/10">
                    <i class="bi bi-zoom-in"></i> Voir
                </button>
            </div>
        </div>
    </div>

    <style>
        .hb-marker-wrap { position: relative; width: 40px; height: 44px; }
        .hb-marker-wrap .hb-marker-pin {
            position: absolute; left: 0; top: 0; width: 40px; height: 40px;
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
    </style>

    <script>
        function locationPicker(initial) {
            const hasInit = initial.lat !== null && initial.lng !== null;
            const BENIN_CENTER = [9.3077, 2.3158];
            return {
                lat: hasInit ? initial.lat : BENIN_CENTER[0],
                lng: hasInit ? initial.lng : BENIN_CENTER[1],
                hasPoint: hasInit,
                query: '',
                searching: false,
                locating: false,
                message: '',
                suggestions: [],
                showSuggestions: false,
                selectedIndex: -1,
                _map: null,
                _marker: null,
                async lookupSuggestions() {
                    this.showSuggestions = true;
                    if (!this.query || this.query.length < 2) {
                        this.suggestions = [];
                        this.selectedIndex = -1;
                        return;
                    }
                    this.searching = true;
                    try {
                        const response = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=8&countrycodes=bj&accept-language=fr&q=' + encodeURIComponent(this.query), {
                            headers: { Accept: 'application/json' }
                        });
                        if (!response.ok) throw new Error('suggest-failed');
                        const results = await response.json();
                        this.suggestions = (results || []).map((r) => ({
                            lat: r.lat,
                            lon: r.lon,
                            display_name: r.display_name,
                            type: ['Ville', 'Village', 'Quartier', 'Région', 'Lieu'].includes(r.type)
                                ? ({ city: 'Ville', town: 'Ville', village: 'Village', suburb: 'Quartier', neighbourhood: 'Quartier', state: 'Département', country: 'Pays' }[r.type] || r.type)
                                : (r.type || 'Lieu')
                        }));
                        this.selectedIndex = this.suggestions.length ? 0 : -1;
                    } catch (error) {
                        this.suggestions = [];
                        this.selectedIndex = -1;
                    } finally {
                        this.searching = false;
                    }
                },
                highlightNext() {
                    if (!this.suggestions.length) return;
                    this.selectedIndex = (this.selectedIndex + 1) % this.suggestions.length;
                    this.$refs.suggestionsList && this.$el.querySelectorAll('li')[this.selectedIndex]?.scrollIntoView({ block: 'nearest' });
                },
                highlightPrev() {
                    if (!this.suggestions.length) return;
                    this.selectedIndex = (this.selectedIndex - 1 + this.suggestions.length) % this.suggestions.length;
                    this.$el.querySelectorAll('li')[this.selectedIndex]?.scrollIntoView({ block: 'nearest' });
                },
                chooseHighlighted() {
                    if (this.suggestions[this.selectedIndex]) {
                        this.chooseSuggestion(this.suggestions[this.selectedIndex]);
                    } else {
                        this.searchAddress();
                    }
                },
                chooseSuggestion(s) {
                    this.lat = Number(Number(s.lat).toFixed(7));
                    this.lng = Number(Number(s.lon).toFixed(7));
                    this.hasPoint = true;
                    this.query = s.display_name;
                    this.showSuggestions = false;
                    this.suggestions = [];
                    this.selectedIndex = -1;
                    this.message = 'Lieu sélectionné : ' + s.display_name;
                    if (this._map) {
                        this._setMarker(this.lat, this.lng);
                        this._map.setView([this.lat, this.lng], 15, { animate: true });
                    }
                },
                init() {
                    if (typeof L === 'undefined') return;
                    this._map = L.map(this.$refs.map, {
                        scrollWheelZoom: false,
                        zoomControl: true
                    }).setView([this.lat, this.lng], hasInit ? 13 : 7);

                    // Fond Esri World Street Map : gratuit sans clé, rendu type Google Maps (rues + labels).
                    // maxNativeZoom: 18 => Leaflet étire les tuiles au-delà, sans requêter un zoom
                    // qui n'existe pas (évite les tuiles « Map data not yet available »).
                    const base = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri &mdash; Source: Esri and the GIS User Community',
                        maxZoom: 19,
                        maxNativeZoom: 18
                    }).addTo(this._map);

                    // Repli OSM standard si les tuiles Esri échouent.
                    let fallenBack = false;
                    this._map.on('tileerror', () => {
                        if (fallenBack) return;
                        fallenBack = true;
                        this._map.removeLayer(base);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                            maxZoom: 19,
                            maxNativeZoom: 19
                        }).addTo(this._map);
                    });

                    if (hasInit) this._setMarker(this.lat, this.lng);

                    this._map.on('click', (e) => {
                        this.lat = e.latlng.lat;
                        this.lng = e.latlng.lng;
                        this.hasPoint = true;
                        this._setMarker(this.lat, this.lng);
                        this._map.setView([this.lat, this.lng], 15, { animate: true });
                    });

                    this.$nextTick(() => this._map.invalidateSize());
                },
                syncCoordinates() {
                    const lat = Number(this.lat);
                    const lng = Number(this.lng);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                        this.message = 'Veuillez saisir des coordonnées valides.';
                        return;
                    }
                    this.lat = Number(lat.toFixed(7));
                    this.lng = Number(lng.toFixed(7));
                    this.hasPoint = true;
                    this.message = '';
                    if (this._map) {
                        this._setMarker(this.lat, this.lng);
                        this._map.setView([this.lat, this.lng], 15, { animate: true });
                    }
                },
                async searchAddress() {
                    if (!this.query || this.searching) return;
                    this.searching = true;
                    this.message = 'Recherche en cours…';
                    try {
                        const response = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=bj&q=' + encodeURIComponent(this.query), {
                            headers: { Accept: 'application/json' }
                        });
                        if (!response.ok) throw new Error('search-failed');
                        const results = await response.json();
                        if (!results.length) {
                            this.message = 'Aucun résultat trouvé au Bénin.';
                            return;
                        }
                        this.lat = Number(Number(results[0].lat).toFixed(7));
                        this.lng = Number(Number(results[0].lon).toFixed(7));
                        this.hasPoint = true;
                        this.message = 'Adresse trouvée. Vérifiez le marqueur puis ajustez-le si nécessaire.';
                        this._setMarker(this.lat, this.lng);
                        this._map.setView([this.lat, this.lng], 16, { animate: true });
                    } catch (error) {
                        this.message = 'La recherche est indisponible. Vous pouvez placer le marqueur manuellement.';
                    } finally {
                        this.searching = false;
                    }
                },
                useCurrentPosition() {
                    if (!navigator.geolocation) {
                        this.message = 'La géolocalisation n’est pas disponible sur cet appareil.';
                        return;
                    }
                    this.locating = true;
                    this.message = 'Obtention de votre position…';
                    navigator.geolocation.getCurrentPosition((position) => {
                        this.lat = Number(position.coords.latitude.toFixed(7));
                        this.lng = Number(position.coords.longitude.toFixed(7));
                        this.hasPoint = true;
                        this.message = 'Position actuelle utilisée. Ajustez le marqueur si nécessaire.';
                        this._setMarker(this.lat, this.lng);
                        this._map.setView([this.lat, this.lng], 16, { animate: true });
                        this.locating = false;
                    }, () => {
                        this.message = 'Position inaccessible. Autorisez la géolocalisation ou placez le marqueur manuellement.';
                        this.locating = false;
                    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                },
                _setMarker(lat, lng) {
                    if (this._marker) this._marker.remove();
                    const icon = L.divIcon({
                        className: '',
                        html: '<div class="hb-marker-wrap"><span class="hb-marker-pin"><i class="bi bi-geo-alt-fill"></i></span></div>',
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
                        this._map.setView([this.lat, this.lng], 11, { animate: true });
                    } else {
                        this._map.setView(BENIN_CENTER, 7);
                    }
                }
            };
        }
    </script>
</div>