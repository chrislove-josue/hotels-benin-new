{{-- Sélecteur de localité avec option d'ajout d'une nouvelle ville --}}
<div x-data="{ addNew: {{ $addNewInit ?? 'false' }} }">
    <label class="label">Localité *</label>

    <template x-if="!addNew">
        <div>
            <select name="locality_id" class="input-field" required>
                <option value="">Sélectionner...</option>
                @foreach($localities as $locality)
                    <option value="{{ $locality->id }}" @if(($selected ?? null) == $locality->id) selected @endif>{{ $locality->name }}</option>
                @endforeach
            </select>
            <button type="button" @click="addNew = true"
                class="mt-1.5 inline-flex items-center gap-1 text-xs font-semibold text-hb-blue transition hover:text-hb-orange">
                <i class="bi bi-plus-circle"></i> Votre ville n'existe pas ? Ajoutez-la
            </button>
        </div>
    </template>

    <template x-if="addNew">
        <div>
            <input type="text" name="new_locality" class="input-field" placeholder="Nom de la ville (ex : Abomey-Calavi)"
                value="{{ $newLocalityValue ?? '' }}" maxlength="100">
            <button type="button" @click="addNew = false"
                class="mt-1.5 inline-flex items-center gap-1 text-xs font-semibold text-gray-500 transition hover:text-hb-orange">
                <i class="bi bi-arrow-left-circle"></i> Choisir une localité existante
            </button>
        </div>
    </template>

    @error('locality_id')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
    @error('new_locality')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>