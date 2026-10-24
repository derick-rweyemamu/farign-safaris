<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablesAndColumns = [
            'pages' => ['content', 'builder_html', 'builder_data'],
            'destinations' => ['description', 'builder_html', 'builder_data'],
            'posts' => ['content', 'builder_html', 'builder_data'],
        ];

        foreach ($tablesAndColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }

                // Replace common local development host patterns with relative root
                DB::table($table)
                    ->where($column, 'LIKE', '%http://127.0.0.1:8000%')
                    ->update([
                        $column => DB::raw("REPLACE({$column}, 'http://127.0.0.1:8000', '')")
                    ]);

                DB::table($table)
                    ->where($column, 'LIKE', '%http://localhost:8000%')
                    ->update([
                        $column => DB::raw("REPLACE({$column}, 'http://localhost:8000', '')")
                    ]);

                DB::table($table)
                    ->where($column, 'LIKE', '%http://localhost%')
                    ->update([
                        $column => DB::raw("REPLACE({$column}, 'http://localhost', '')")
                    ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback needed for sanitized URLs
    }
};
