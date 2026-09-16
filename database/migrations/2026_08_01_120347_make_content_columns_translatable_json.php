<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $tablesAndColumns = [
        'pages' => ['title', 'content', 'builder_html', 'builder_data'],
        'tours' => ['name', 'description', 'overview', 'included_features', 'excluded_features'],
        'destinations' => ['name', 'tagline', 'description', 'builder_html', 'builder_data'],
        'posts' => ['title', 'excerpt', 'content', 'builder_html', 'builder_data'],
        'accommodations' => ['name', 'description'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tablesAndColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $updates = [];
                foreach ($columns as $column) {
                    if (!Schema::hasColumn($table, $column)) {
                        continue;
                    }

                    $val = $row->{$column};
                    if ($val === null || trim((string) $val) === '') {
                        $updates[$column] = null;
                        continue;
                    }

                    // Check if already in translatable format {"en": ...}
                    $decoded = json_decode((string) $val, true);
                    if (is_array($decoded) && (isset($decoded['en']) || isset($decoded['fr']) || isset($decoded['de']) || isset($decoded['zh']) || isset($decoded['es']) || isset($decoded['it']))) {
                        continue;
                    }

                    // Wrap existing content into {"en": $val}
                    $updates[$column] = json_encode(['en' => (is_array($decoded) ? $decoded : $val)], JSON_UNESCAPED_UNICODE);
                }

                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` JSON NULL");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tablesAndColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` LONGTEXT NULL");
                }
            }
        }
    }
};
