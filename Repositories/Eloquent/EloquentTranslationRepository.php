<?php namespace Modules\Translation\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Translation\Repositories\TranslationRepository;

class EloquentTranslationRepository extends EloquentBaseRepository implements TranslationRepository
{
    /**
     * @param string $key
     * @param string $locale
     * @return string
     */
    public function findByKeyAndLocale($key, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $translation = $this->model->whereKey($key)->with('translations')->first();
        if ($translation && $translation->hasTranslation($locale)) {
            return $translation->translate($locale)->value;
        }

        return '';
    }

    public function allFormatted()
    {
        $allRows = $this->all();
        $allDatabaseTranslations = [];
        foreach ($allRows as $translation) {
            foreach (config('laravellocalization.supportedLocales') as $locale => $language) {
                if ($translation->hasTranslation($locale)) {
                    $allDatabaseTranslations[$locale][$translation->key] = $translation->translate($locale)->value;
                }
            }
        }
        return $allDatabaseTranslations;
    }
}
