<?php

namespace App\Imports;

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class SubscribersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            foreach ($row as $cell) {
                if (is_string($cell) && filter_var(trim($cell), FILTER_VALIDATE_EMAIL)) {
                    NewsletterSubscriber::firstOrCreate(
                        ['email' => trim($cell)],
                        [
                            'status' => 'active',
                            'unsubscribe_token' => Str::random(32),
                        ]
                    );
                    break;
                }
            }
        }
    }
}
