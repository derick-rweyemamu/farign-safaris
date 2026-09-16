<?php

namespace App\Traits;

/**
 * Reusable trait that adds locale-tabbed translation support to Livewire admin list components.
 * Models must use Spatie\Translatable\HasTranslations.
 *
 * Usage: add  `use HasLocaleTranslations;` to the Livewire component.
 * Provides: $locales, $activeLocaleTab, $names, $descriptions arrays,
 *           plus helper methods applyTranslationsToModel() and loadTranslationsFromModel().
 */
trait HasLocaleTranslations
{
    public array $locales = ['en', 'fr', 'de', 'es', 'it', 'zh'];
    public string $activeLocaleTab = 'en';
    public array $names = [];
    public array $descriptions = [];

    public function initLocaleTranslations(): void
    {
        $this->names = array_fill_keys($this->locales, '');
        $this->descriptions = array_fill_keys($this->locales, '');
        $this->activeLocaleTab = 'en';
    }

    /**
     * Load translations from a model into the $names / $descriptions arrays.
     */
    public function loadTranslationsFromModel($model, array $fields = ['name', 'description']): void
    {
        foreach ($this->locales as $locale) {
            if (in_array('name', $fields)) {
                $this->names[$locale] = $model->getTranslation('name', $locale, false) ?? '';
            }
            if (in_array('description', $fields)) {
                $this->descriptions[$locale] = $model->getTranslation('description', $locale, false) ?? '';
            }
        }
    }

    /**
     * Save $names / $descriptions arrays back to a model using setTranslation().
     */
    public function applyTranslationsToModel($model, array $fields = ['name', 'description']): void
    {
        foreach ($this->locales as $locale) {
            if (in_array('name', $fields) && !empty($this->names[$locale])) {
                $model->setTranslation('name', $locale, $this->names[$locale]);
            }
            if (in_array('description', $fields) && !empty($this->descriptions[$locale])) {
                $model->setTranslation('description', $locale, $this->descriptions[$locale]);
            }
        }
        $model->save();
    }
}
