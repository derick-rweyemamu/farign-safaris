<?php

namespace App\Traits;

trait UpdatesEnv
{
    /**
     * Update the .env file with the provided key-value pairs.
     */
    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);
        $originalContent = $envContent;

        foreach ($data as $key => $value) {
            // Handle null values
            if (is_null($value) || $value === 'null') {
                $formattedValue = 'null';
            } else {
                // Ensure value is quoted if it contains spaces or special characters
                $formattedValue = (str_contains($value, ' ') || str_contains($value, '$') || str_contains($value, '#')) 
                    ? '"' . $value . '"' 
                    : $value;
            }
            
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$formattedValue}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                // If the key doesn't exist, append it
                $envContent = rtrim($envContent) . "\n{$key}={$formattedValue}\n";
            }
        }

        if ($envContent !== $originalContent) {
            file_put_contents($envPath, $envContent);
        }
    }
}
