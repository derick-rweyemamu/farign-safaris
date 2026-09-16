<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleTranslateService;

class TranslateModelData extends Command
{
    protected $signature = 'translate:model-data
        {--model= : Only translate a specific model (e.g. TourCategory)}
        {--locale= : Only translate to a specific locale (e.g. fr)}
        {--force : Re-translate even if a value already exists}';

    protected $description = 'Auto-translate all database model fields (name, description, etc.) to all configured locales using Google Translate.';

    /** Locales to translate into */
    protected array $targetLocales = ['fr', 'de', 'es', 'it', 'zh'];

    /** Map: ModelClass => [translatable fields] */
    protected array $models = [
        \App\Models\TourCategory::class  => ['name', 'description'],
        \App\Models\TourType::class      => ['name'],
        \App\Models\Animal::class        => ['name'],
        \App\Models\TourTag::class       => ['name', 'description'],
        \App\Models\TourRoute::class     => ['name', 'description'],
        \App\Models\PostCategory::class  => ['name', 'description'],
        \App\Models\GroupCategory::class => ['name', 'description'],
        \App\Models\MealPlan::class      => ['name', 'description'],
        \App\Models\Activity::class      => ['name', 'description'],
        \App\Models\Transport::class     => ['name', 'description'],
        \App\Models\Destination::class   => ['name', 'tagline'],
    ];

    public function handle(GoogleTranslateService $translator): int
    {
        $force        = $this->option('force');
        $onlyModel    = $this->option('model');
        $onlyLocale   = $this->option('locale');
        $targetLocales = $onlyLocale ? [$onlyLocale] : $this->targetLocales;

        $totalTranslated  = 0;
        $totalSkipped     = 0;
        $totalErrors      = 0;

        foreach ($this->models as $modelClass => $fields) {
            $shortName = class_basename($modelClass);

            // Skip if a specific model was requested and this isn't it
            if ($onlyModel && strtolower($shortName) !== strtolower($onlyModel)) {
                continue;
            }

            if (!class_exists($modelClass)) {
                $this->warn("  ⚠  Model [{$shortName}] not found, skipping.");
                continue;
            }

            $this->info("\n📦 Processing <comment>{$shortName}</comment>...");

            $records = $modelClass::all();

            if ($records->isEmpty()) {
                $this->line("   No records found.");
                continue;
            }

            $bar = $this->output->createProgressBar($records->count() * count($fields) * count($targetLocales));
            $bar->start();

            foreach ($records as $record) {
                foreach ($fields as $field) {
                    // Get the English (source) value
                    try {
                        $sourceText = $record->getTranslation($field, 'en', false);
                    } catch (\Throwable $e) {
                        $sourceText = $record->getRawOriginal($field);
                        // If it's a JSON string, decode and get 'en'
                        if (is_string($sourceText) && str_starts_with(trim($sourceText), '{')) {
                            $decoded = json_decode($sourceText, true);
                            $sourceText = $decoded['en'] ?? ($decoded[array_key_first($decoded)] ?? null);
                        }
                    }

                    if (empty($sourceText)) {
                        // Try to get the raw value directly as English fallback
                        $raw = $record->getRawOriginal($field);
                        if (!empty($raw) && !str_starts_with(trim((string)$raw), '{')) {
                            $sourceText = $raw;
                        }
                    }

                    if (empty($sourceText)) {
                        $bar->advance(count($targetLocales));
                        $totalSkipped++;
                        continue;
                    }

                    foreach ($targetLocales as $locale) {
                        $bar->advance();

                        // Check if translation already exists
                        if (!$force) {
                            try {
                                $existing = $record->getTranslation($field, $locale, false);
                                if (!empty($existing) && $existing !== $sourceText) {
                                    $totalSkipped++;
                                    continue;
                                }
                            } catch (\Throwable) {}
                        }

                        // Translate via Google
                        try {
                            $translated = $translator->translate($sourceText, $locale, 'en', false);

                            if (!empty($translated) && $translated !== $sourceText) {
                                $record->setTranslation($field, $locale, $translated);
                                $totalTranslated++;
                            } else {
                                $totalSkipped++;
                            }
                        } catch (\Throwable $e) {
                            $totalErrors++;
                            $this->newLine();
                            $this->warn("   ✗ Error translating [{$shortName}] id={$record->id} field={$field} locale={$locale}: " . $e->getMessage());
                        }

                        // Small delay to avoid rate limiting
                        usleep(120_000); // 120ms
                    }

                    // Save all locale translations for this field after processing all locales
                    try {
                        $record->save();
                    } catch (\Throwable $e) {
                        $totalErrors++;
                        $this->newLine();
                        $this->warn("   ✗ Error saving [{$shortName}] id={$record->id}: " . $e->getMessage());
                    }
                }
            }

            $bar->finish();
            $this->newLine();
            $this->line("   ✅ Done: <info>{$shortName}</info>");
        }

        $this->newLine(2);
        $this->info("🏁 Translation complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Translated', $totalTranslated],
                ['Skipped (already done)', $totalSkipped],
                ['Errors', $totalErrors],
            ]
        );

        return Command::SUCCESS;
    }
}
