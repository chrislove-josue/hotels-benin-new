<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadsImages
{
    /**
     * Gère le champ image : si un fichier est uploadé, on le stocke et retourne
     * l'URL publique ; sinon on retourne le lien fourni (ou null).
     *
     * L'upload est prioritaire sur le lien URL.
     */
    protected function resolveImage($file, ?string $url): ?string
    {
        if ($file instanceof UploadedFile && $file->isValid()) {
            return $this->storeUploadedImage($file);
        }

        return $url ?: null;
    }

    /**
     * Stocke une image uploadée dans storage/app/public/uploads et retourne son URL publique.
     */
    protected function storeUploadedImage(UploadedFile $file): string
    {
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '-' . Str::lower(Str::random(8)) . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('uploads', $name, 'public');

        return '/storage/' . $path;
    }

    /**
     * Traite un tableau d'images uploadées (galerie) et retourne les URLs.
     */
    protected function storeUploadedImages($files): array
    {
        if (! is_iterable($files)) {
            return [];
        }

        $urls = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $urls[] = $this->storeUploadedImage($file);
            }
        }

        return $urls;
    }
}
