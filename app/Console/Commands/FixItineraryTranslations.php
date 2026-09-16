<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixItineraryTranslations extends Command
{
    protected $signature = 'fix:itinerary-translations {--locale=en : The locale to wrap plain strings as}';
    protected $description = 'Convert plain-string title/description fields in tour_itineraries to JSON-translated format';

    public function handle(): int
    {
        $locale = $this->option('locale');
        $rows = DB::table('tour_itineraries')->get(['id', 'title', 'description']);

        $fixed = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $updates = [];

            // Fix title
            $titleDecoded = json_decode($row->title, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($titleDecoded)) {
                $updates['title'] = json_encode([$locale => $row->title], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            // Fix description
            $descDecoded = json_decode($row->description, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($descDecoded)) {
                $updates['description'] = json_encode([$locale => $row->description], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            if (!empty($updates)) {
                DB::table('tour_itineraries')->where('id', $row->id)->update($updates);
                $this->line("  Fixed row ID={$row->id}: " . implode(', ', array_keys($updates)));
                $fixed++;
            } else {
                $skipped++;
            }
        }

        $this->info("\nDone. Fixed: $fixed, Already correct (skipped): $skipped");
        return 0;
    }
}
