<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First convert any existing plain strings to JSON structure {"en": "..."}
        $rows = DB::table('navigation_menus')->get();

        foreach ($rows as $row) {
            $val = $row->label;
            if ($val !== null && !is_array(json_decode($val, true))) {
                $jsonVal = json_encode(['en' => $val], JSON_UNESCAPED_UNICODE);
                DB::table('navigation_menus')
                    ->where('id', $row->id)
                    ->update(['label' => $jsonVal]);
            }
        }
    }

    public function down(): void
    {
        $rows = DB::table('navigation_menus')->get();

        foreach ($rows as $row) {
            $val = $row->label;
            $decoded = json_decode((string) $val, true);
            if (is_array($decoded)) {
                $plainVal = $decoded['en'] ?? reset($decoded) ?? '';
                DB::table('navigation_menus')
                    ->where('id', $row->id)
                    ->update(['label' => $plainVal]);
            }
        }
    }
};
