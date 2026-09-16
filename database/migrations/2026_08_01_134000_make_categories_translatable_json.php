<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tablesAndColumns = [
        'tour_categories' => ['name', 'description'],
        'tour_tags'       => ['name'],
        'tour_types'      => ['name'],
        'post_categories' => ['name', 'description'],
        'product_categories' => ['name', 'description'],
    ];

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

                    $decoded = json_decode((string) $val, true);
                    if (is_array($decoded) && (isset($decoded['en']) || isset($decoded['fr']) || isset($decoded['de']) || isset($decoded['zh']) || isset($decoded['es']) || isset($decoded['it']))) {
                        continue;
                    }

                    $updates[$column] = json_encode(['en' => (string) $val], JSON_UNESCAPED_UNICODE);
                }

                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }

    public function down(): void
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
                    $decoded = json_decode((string) $val, true);
                    if (is_array($decoded)) {
                        $updates[$column] = $decoded['en'] ?? reset($decoded) ?? '';
                    }
                }

                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }
};
