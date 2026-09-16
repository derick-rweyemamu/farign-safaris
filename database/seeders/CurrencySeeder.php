<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Settings\GeneralSetting;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(GeneralSetting::class);

        foreach ($settings->currencies as $curr) {
            $code = $curr['code'];
            $symbol = $curr['symbol'];
            $rate = $settings->currency_rates[$code] ?? 1.0;

            Currency::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $this->getName($code),
                    'symbol' => $symbol,
                    'exchange_rate' => $rate,
                    'is_active' => true,
                    'decimal_places' => 2,
                ]
            );
        }
    }

    private function getName(string $code): string
    {
        return match ($code) {
            'TZS' => 'Tanzanian Shilling',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'KES' => 'Kenyan Shilling',
            default => $code,
        };
    }
}
