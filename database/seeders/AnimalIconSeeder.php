<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Animal;

class AnimalIconSeeder extends Seeder
{
    public function run(): void
    {
        $animals = [
            'Elephant',
            'Giraffe',
            'Lion',
            'Buffalo',
            'Zebra',
            'Wildebeest',
            'Hippo',
            'Hyena',
            'Leopard',
            'Cheetah',
            'Wild Dog',
            'White Rhino',
            'Black Rhino',
        ];

        // Ensure target storage directory exists
        Storage::disk('public')->makeDirectory('animals/icons');

        foreach ($animals as $name) {
            $filename = strtolower(str_replace(' ', '-', $name)) . '.png';
            $sourcePath = public_path('images/default-animals/' . $filename);
            $targetPath = 'animals/icons/' . $filename;

            if (File::exists($sourcePath)) {
                // Copy the file from public/images/default-animals to storage/app/public/animals/icons
                Storage::disk('public')->put($targetPath, File::get($sourcePath));
                
                // Create or update animal record
                Animal::updateOrCreate(
                    ['name' => $name],
                    ['icon' => $targetPath]
                );
                
                $this->command->info("Added {$name} with icon {$targetPath}");
            } else {
                $this->command->error("Local icon not found for {$name} at {$sourcePath}");
            }
        }
    }
}
