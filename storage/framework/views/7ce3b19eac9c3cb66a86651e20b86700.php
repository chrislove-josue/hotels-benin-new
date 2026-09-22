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

        <div class="border-b border-gray-100 bg-white p-4">
            <div class="relative">
                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="relative flex-1">
                        <input type="search" x-model.trim="query" @input.debounce.300ms="lookupSuggestions()" @focus="if (query) lookupSuggestions()" @keydown.down.prevent="highlightNext()" @keydown.up.prevent="highlightPrev()" @keydown.enter.prevent="chooseHighlighted()" @focusout="setTimeout(() => showSuggestions = false, 200)"
                            class="input-field w-full" placeholder="Rechercher une ville, zone, entreprise, hôtel, lieu… au Bénin"
                            aria-label="Rechercher une ville, zone, entreprise ou lieu au Bénin" autocomplete="off" role="combobox" aria-expanded="showSuggestions">

                        
                        <div x-show="showSuggestions && query" x-cloak class="absolute inset-x-0 top-full z-[1000] mt-1 rounded-xl border border-gray-200 bg-white shadow-xl">
                            <ul x-ref="suggestionsList" class="max-h-72 overflow-auto">
                                <template x-for="(s, i) in suggestions" :key="i">
                                    <li>
                                        <button type="button" @mousedown.prevent="chooseSuggestion(s)" @mouseenter="selectedIndex = i"
                                            class="flex w-full items-start gap-2.5 px-3 py-2.5 text-left transition hover:bg-hb-cream"
                                            :class="selectedIndex === i ? 'bg-hb-cream' : ''">
                                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-hb-blue/10 text-hb-blue" x-html="s.icon"></span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block truncate text-sm font-semibold text-hb-blue-deeper" x-text="s.display_name"></span>
                                                <span class="block text-[11px] text-gray-500" x-text="s.fullLabel"></span>
                                            </span>
                                            <span class="mt-1 shrink-0 rounded-full bg-hb-gold/10 px-2 py-0.5 text-[10px] font-semibold text-hb-blue" x-text="s.typeLabel"></span>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                            <p x-show="query && !searching && !suggestions.length" class="px-3 py-2.5 text-xs text-gray-500">
                                Aucun lieu trouvé. Appuyez sur Entrée pour rechercher sur la carte, ou placez le marqueur manuellement.
                            </p>
                        </div>
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
            // Villes / communes du Bénin (les 77 communes officielles) + quartiers + points de repère,
            // avec coordonnées — complète Nominatim (OSM) qui couvre mal le pays.
            const MUNI = [ // [nom, lat, lng] — chef-lieu de chaque commune
                ['Cotonou', 6.3703, 2.3912], ['Porto-Novo', 6.4969, 2.6289], ['Abomey-Calavi', 6.4550, 2.3400],
                ['Banikoara', 11.3020, 2.4380], ['Gogounou', 10.8370, 2.8320], ['Kandi', 11.1333, 2.9333],
                ['Karimama', 12.0667, 3.0500], ['Malanville', 11.8667, 3.3833], ['Ségbana', 10.9333, 3.7000],
                ['Boukoumbé', 10.1667, 1.1000], ['Cobly', 10.5000, 1.0000], ['Kérou', 11.3167, 2.0833],
                ['Kouandé', 10.3333, 1.7000], ['Matéri', 10.6833, 1.0667], ['Natitingou', 10.3056, 1.3786],
                ['Péhunco', 10.2833, 1.5833], ['Tanguiéta', 10.6167, 1.2667], ['Toucountouna', 10.4833, 1.3833],
                ['Allada', 6.6667, 2.1500], ['Kpomassè', 6.4000, 2.0000], ['Ouidah', 6.3631, 2.0849],
                ['Sô-Ava', 6.5000, 2.3667], ['Toffo', 6.8700, 2.1000], ['Tori-Bossito', 6.6000, 2.1500], ['Zè', 6.8500, 2.1000],
                ['Bembèrèkè', 10.2167, 2.6833], ['Kalalé', 10.3000, 3.3833], ['N\'Dali', 10.6333, 2.9500],
                ['Nikki', 9.9333, 3.2000], ['Parakou', 9.3371, 2.6303], ['Pèrèrè', 9.8000, 2.9833],
                ['Sinendé', 10.3333, 2.3833], ['Tchaourou', 8.9167, 2.6500],
                ['Bantè', 8.4167, 1.8833], ['Dassa-Zoumé', 7.7500, 2.1833], ['Glazoué', 7.9833, 2.2333],
                ['Ouèssè', 8.4833, 2.4333], ['Savalou', 6.9333, 1.8167], ['Savè', 8.0333, 2.4833],
                ['Aplahoué', 6.9333, 1.6833], ['Djakotomey', 6.9000, 1.7167], ['Dogbo', 6.8167, 1.7833],
                ['Klouékanmè', 7.0000, 1.8667], ['Lalo', 6.8833, 1.8833], ['Toviklin', 6.8833, 1.8000],
                ['Bassila', 9.0167, 1.6667], ['Copargo', 9.8333, 1.5500], ['Djougou', 9.7069, 1.6666], ['Ouaké', 9.6667, 1.3833],
                ['Athiémè', 6.5833, 1.6833], ['Bopa', 6.6167, 1.9833], ['Comè', 6.4056, 2.0867],
                ['Grand-Popo', 6.2776, 1.8286], ['Houéyogbé', 6.4833, 1.9667], ['Lokossa', 6.6344, 1.7183],
                ['Adjarra', 6.5333, 2.5833], ['Adjohoun', 6.7167, 2.4833], ['Aguégués', 6.6333, 2.5333],
                ['Akpro-Missérété', 6.5667, 2.6000], ['Avrankou', 6.5500, 2.6333], ['Bonou', 6.6167, 2.3000],
                ['Dangbo', 6.5000, 2.4500], ['Sèmè-Podji', 6.3686, 2.6078],
                ['Adja-Ouèrè', 6.9500, 2.7833], ['Ifangni', 6.6333, 2.7333], ['Kétou', 7.3667, 2.6000],
                ['Pobè', 6.9833, 2.6667], ['Sakété', 6.7167, 2.6500],
                ['Abomey', 7.1856, 1.9914], ['Agbangnizoun', 7.0667, 1.9667], ['Bohicon', 7.1817, 2.0667],
                ['Covè', 7.2167, 2.3167], ['Djidja', 7.3500, 1.9500], ['Ouinhi', 7.6333, 2.1833],
                ['Za-Kpota', 7.2333, 2.2167], ['Zagnanado', 7.2667, 2.3500], ['Zogbodomey', 7.1167, 2.0833],
            ];
            // Quartiers / zones principales
            const ZONE_SITES = [
                ['Cotonou — Quartier Fidjrossè', 6.3486, 2.3711, ['fidjrossè', 'fidjrosse', 'fidj']],
                ['Cotonou — Quartier Akpakpa', 6.3772, 2.4178, ['akpakpa']],
                ['Cotonou — Quartier Ganhi', 6.3686, 2.3997, ['ganhi']],
                ['Cotonou — Quartier Vèdoko', 6.3603, 2.4083, ['vedoko', 'vèdoko']],
                ['Cotonou — Quartier Sainte-Rita', 6.3639, 2.4347, ['sainte-rita', 'sainte rita']],
                ['Cotonou — Quartier Agla', 6.3439, 2.3997, ['agla']],
                ['Cotonou — Quartier Gbégamey', 6.3769, 2.3989, ['gbegamey', 'gbègamey']],
                ['Cotonou — Quartier Zogbo', 6.3836, 2.3961, ['zogbo']],
                ['Cotonou — Quartier Mènontin', 6.3544, 2.3839, ['menontin', 'mènontin']],
                ['Cotonou — Quartier Plage', 6.3567, 2.3617, ['plage', 'cotonou plage']],
                ['Cotonou — Quartier Haie Vive', 6.3603, 2.3986, ['haie vive']],
                ['Cotonou — Quartier Aéroport', 6.3571, 2.3847, ['aeroport cadjehoun', 'cadjèhoun']],
                ['Abomey-Calavi — Quartier Godomey', 6.3694, 2.3364, ['godomey']],
                ['Abomey-Calavi — Quartier Zinvié', 6.4611, 2.3467, ['zinvié', 'zinvie']],
                ['Abomey-Calavi — Quartier Ogoudou', 6.4344, 2.3428, ['ogoudou', 'oglou']],
                ['Abomey-Calavi — Quartier Togba', 6.4142, 2.3439, ['togba']],
                ['Porto-Novo — Quartier Dowa', 6.5028, 2.6353, ['dowa']],
                ['Porto-Novo — Quartier Adjarra', 6.5411, 2.6178, ['adjarra']],
                ['Sèmè-Podji — Somekpo', 6.3731, 2.6489, ['somekpo']],
                ['Ouidah — Quartier Pydjè', 6.3611, 2.0722, ['pydjè', 'pydje']],
                ['Parakou — Quartier Guéma', 9.3400, 2.6200, ['guema', 'guéma']],
            ];
            // Points de repère / POI
            const POI_SITES = [
                ['Aéroport international Cadjèhoun (Cotonou)', 6.3571, 2.3847, ['aeroport', 'aéroport', 'cadjèhoun', 'cadjeahun']],
                ['Gare ferroviaire de Cotonou', 6.3500, 2.4372, ['gare cotonou']],
                ['Marché Dantokpa (Cotonou)', 6.3619, 2.3839, ['dantokpa', 'marche dantokpa', 'marché dantokpa']],
                ['Pont du Mono (Grand-Popo)', 6.2417, 1.8250, ['pont du mono']],
                ['Route des Pêches (Ouidah)', 6.3400, 2.2300, ['route des peches', 'route des pêches', 'route des pecheurs']],
                ['Pendjari — Porte Nationale Annexée de la Pendjari', 11.0000, 1.4000, ['pendjari', 'parc national pendjari']],
                ['Parc national de la Pendjari', 11.0500, 1.3833, ['pendjari']],
                ['Cathédrale Notre-Dame de l\'Immaculée Conception (Cotonou)', 6.3653, 2.4222, ['cathedrale cotonou', 'cathédrale cotonou']],
                ['Stade de l\'Amitié (Cotonou)', 6.3767, 2.4244, ['stade amitie', 'stade de l amitie']],
                ['Université d\'Abomey-Calavi', 6.4222, 2.3433, ['universite abomey calavi', 'uac']],
            ];
            const LOCAL_SITES = [
                ...MUNI.map(([name, lat, lng]) => ({ name, lat, lng, type: 'city', hints: [name] })),
                ...ZONE_SITES.map(([name, lat, lng, hints]) => ({ name, lat, lng, type: 'suburb', hints })),
                ...POI_SITES.map(([name, lat, lng, hints]) => ({ name, lat, lng, type: 'amenity', hints })),
            ];
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
                        // Nominatim (OSM) couvre mal le Bénin : résultats locaux fusionnés par-dessus.
                        const local = this._searchLocal(this.query);
                        const response = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=25&countrycodes=bj&accept-language=fr&q=' + encodeURIComponent(this.query), {
                            headers: { Accept: 'application/json' }
                        });
                        if (!response.ok) throw new Error('suggest-failed');
                        const results = await response.json();
                        const nominated = (results || []).map((r) => this._decorateSuggestion(r));
                        const seen = new Set(nominated.map((s) => s.norm));
                        const merged = [...local.filter((s) => !seen.has(s.norm)), ...nominated];
                        this.suggestions = merged.slice(0, 12);
                        this.selectedIndex = this.suggestions.length ? 0 : -1;
                    } catch (error) {
                        this.suggestions = this._searchLocal(this.query);
                        this.selectedIndex = this.suggestions.length ? 0 : -1;
                    } finally {
                        this.searching = false;
                    }
                },
                _searchLocal(query) {
                    const q = this._normalize(query).replace(/[\s-]/g, '');
                    const out = [];
                    for (const site of LOCAL_SITES) {
                        const candidate = this._normalize(site.name).replace(/[\s-]/g, '');
                        const hintMatch = site.hints.some((h) => this._normalize(h).replace(/[\s-]/g, '').includes(q));
                        if (q && (candidate.includes(q) || hintMatch)) {
                            out.push(this._decorateSuggestion(site));
                        }
                    }
                    return out;
                },
                _normalize(str) {
                    return (str || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
                },
                _decorateSuggestion(r) {
                    const t = r.type || '';
                    const cat = r.category || '';
                    const labels = {
                        city: 'Ville', town: 'Ville', village: 'Village', county: 'Zone',
                        suburb: 'Quartier', municipality: 'Zone', neighbourhood: 'Quartier',
                        borough: 'Arrondissement', quarter: 'Quartier', residential: 'Zone résidentielle',
                        industrial: 'Zone industrielle', commercial: 'Zone commerciale',
                        state: 'Département', country: 'Pays', road: 'Rue', amenity: 'Lieu',
                        shop: 'Commerce', office: 'Entreprise', bureau: 'Entreprise',
                        tourism: 'Tourisme', building: 'Bâtiment', hotel: 'Hôtel', guest_house: 'Auberge',
                        hostel: 'Auberge', motel: 'Motel', factory: 'Usine', restaurant: 'Restaurant',
                        cafe: 'Café', hospital: 'Hôpital', school: 'École', university: 'Université',
                        market: 'Marché', bank: 'Banque', fuel: 'Station-service', police: 'Police',
                        place_of_worship: 'Lieu de culte', fire_station: 'Caserne de pompiers',
                        station: 'Gare', aerodrome: 'Aéroport', boundary: 'Limite administratif',
                    };
                    const typeLabel = labels[t] || labels[cat] || 'Lieu';
                    const icons = {
                        city: 'bi-buildings', town: 'bi-buildings', village: 'bi-house-gear',
                        county: 'bi-map', suburb: 'bi-cast', municipality: 'bi-map',
                        neighbourhood: 'bi-cast', borough: 'bi-diagram-3', quarter: 'bi-cast',
                        residential: 'bi-house', industrial: 'bi-gear', commercial: 'bi-bag',
                        state: 'bi-pin-map', country: 'bi-globe', road: 'bi-signpost',
                        amenity: 'bi-geo-alt', shop: 'bi-bag', office: 'bi-briefcase', bureau: 'bi-briefcase',
                        tourism: 'bi-camera', building: 'bi-buildings', hotel: 'bi-building', guest_house: 'bi-house',
                        hostel: 'bi-house', motel: 'bi-car-front', factory: 'bi-gear-wide-connected',
                        restaurant: 'bi-shop', cafe: 'bi-cup-hot', hospital: 'bi-hospital', school: 'bi-mortarboard',
                        university: 'bi-mortarboard', market: 'bi-basket', bank: 'bi-bank', fuel: 'bi-fuel-pump',
                        police: 'bi-shield-lock', place_of_worship: 'bi-brightness-alt-high', fire_station: 'bi-fire',
                        station: 'bi-train', aerodrome: 'bi-airplane', boundary: 'bi-bounding-box',
                    };
                    const icon = icons[t] || icons[cat] || 'bi-geo-alt-fill';
                    const name = r.display_name || r.name || '';
                    return {
                        lat: r.lat,
                        lon: r.lon,
                        display_name: name,
                        typeLabel,
                        fullLabel: name.split(',')[0],
                        icon: '<i class="bi ' + icon + ' text-sm"></i>',
                        norm: this._normalize(name),
                    };
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
                        this._map.setView([this.lat, this.lng], 11, { animate: true });
                    }
                },
                init() {
                    if (typeof L === 'undefined') return;
                    this._map = L.map(this.$refs.map, {
                        scrollWheelZoom: false,
                        zoomControl: true
                    }).setView([this.lat, this.lng], hasInit ? 11 : 7);

                    // Fond Esri World Topo Map : relief (ombrage + courbes de niveau), routes détaillées
                    // et noms de lieux/zones — rendu type « relief » Google Maps. Gratuit sans clé.
                    const base = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
                        maxZoom: 19,
                        maxNativeZoom: 18
                    }).addTo(this._map);

                    // Repli Esri World Street Map si les tuiles topo échouent.
                    let fallenBack = false;
                    this._map.on('tileerror', () => {
                        if (fallenBack) return;
                        fallenBack = true;
                        this._map.removeLayer(base);
                        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Tiles &copy; Esri &mdash; Source: Esri and the GIS User Community',
                            maxZoom: 19,
                            maxNativeZoom: 18
                        }).addTo(this._map);
                    });

                    if (hasInit) this._setMarker(this.lat, this.lng);

                    this._map.on('click', (e) => {
                        this.lat = e.latlng.lat;
                        this.lng = e.latlng.lng;
                        this.hasPoint = true;
                        this._setMarker(this.lat, this.lng);
                        this._map.setView([this.lat, this.lng], 11, { animate: true });
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
                        this._map.setView([this.lat, this.lng], 11, { animate: true });
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
                        this._map.setView([this.lat, this.lng], 11, { animate: true });
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
                        this._map.setView([this.lat, this.lng], 11, { animate: true });
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
</div><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/partials/location-picker.blade.php ENDPATH**/ ?>