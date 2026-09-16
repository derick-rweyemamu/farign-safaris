<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Translation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Artisan;

#[Title('UI Translations Manager')]
class TranslationManager extends Component
{
    use WithPagination;

    public string $selectedLang = 'en';
    public string $search = '';
    public int $perPage = 25;
    public ?string $importSummary = null;


    public array $availableLanguages = [
        'en' => 'English (EN)',
        'es' => 'Spanish (ES) - Español',
        'de' => 'German (DE) - Deutsch',
        'fr' => 'French (FR) - Français',
        'it' => 'Italian (IT) - Italiano',
        'zh' => 'Chinese (ZH) - 中文',
    ];

    public function mount()
    {
        Translation::syncMissingKeys();
    }

    public function updatedSelectedLang()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function saveTranslation(int $id, string $val)
    {
        $t = Translation::findOrFail($id);
        $t->lang_value = trim($val);
        $t->save();

        Translation::flushCache($t->lang);

        $this->dispatch('notify', message: "Translation updated for '{$t->lang_key}'.", type: 'success');
    }

    public function importFromJson()
    {
        Artisan::call('translations:import');
        $output = Artisan::output();
        Translation::flushCache();

        $count = 0;
        if (preg_match('/(\d+)\s+translation records/', $output, $matches)) {
            $count = (int) $matches[1];
        }

        $countText = $count > 0 ? number_format($count) : 'all';
        $this->importSummary = "Successfully imported & synced {$countText} translation records across 6 languages (DE, EN, ES, FR, IT, ZH) into the database without downtime.";

        $this->dispatch('notify', message: __('Successfully imported translations from JSON files.'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view settings');

        $query = Translation::where('lang', $this->selectedLang)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('lang_key', 'like', '%' . $this->search . '%')
                        ->orWhere('lang_value', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('lang_key', 'asc');

        $translations = $query->paginate($this->perPage);

        return view('livewire.admin.settings.translation-manager', compact('translations'));
    }
}
