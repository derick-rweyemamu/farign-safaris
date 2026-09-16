<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NavigationMenu;
use App\Models\Page;

class NavigationMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First get the IDs of common pages, assuming they might exist based on slug
        $aboutPageId = Page::where('slug', 'about-us')->value('id');
        $contactPageId = Page::where('slug', 'contact-us')->value('id');
        $termsPageId = Page::where('slug', 'terms-and-conditions')->value('id');
        $privacyPageId = Page::where('slug', 'privacy-policy')->value('id');

        $menus = [
            // Header Links
            ['location' => 'header', 'label' => 'Home', 'url' => '/', 'page_id' => null, 'sort_order' => 1],
            ['location' => 'header', 'label' => 'About Us', 'url' => '/p/about-us', 'page_id' => $aboutPageId, 'sort_order' => 2],
            ['location' => 'header', 'label' => 'Tours', 'url' => '/tours', 'page_id' => null, 'sort_order' => 3],
            ['location' => 'header', 'label' => 'Destinations', 'url' => '/destinations', 'page_id' => null, 'sort_order' => 4],
            ['location' => 'header', 'label' => 'Contact', 'url' => '/p/contact-us', 'page_id' => $contactPageId, 'sort_order' => 5],

            // Footer Company Links
            ['location' => 'footer_company', 'label' => 'About Us', 'url' => '/p/about-us', 'page_id' => $aboutPageId, 'sort_order' => 1],
            ['location' => 'footer_company', 'label' => 'Our Team', 'url' => '#', 'page_id' => null, 'sort_order' => 2],
            ['location' => 'footer_company', 'label' => 'Reviews', 'url' => '#', 'page_id' => null, 'sort_order' => 3],
            ['location' => 'footer_company', 'label' => 'Contact Us', 'url' => '/p/contact-us', 'page_id' => $contactPageId, 'sort_order' => 4],

            // Footer Destinations
            ['location' => 'footer_destinations', 'label' => 'Serengeti National Park', 'url' => '#', 'page_id' => null, 'sort_order' => 1],
            ['location' => 'footer_destinations', 'label' => 'Ngorongoro Crater', 'url' => '#', 'page_id' => null, 'sort_order' => 2],
            ['location' => 'footer_destinations', 'label' => 'Mount Kilimanjaro', 'url' => '#', 'page_id' => null, 'sort_order' => 3],
            ['location' => 'footer_destinations', 'label' => 'Zanzibar Islands', 'url' => '#', 'page_id' => null, 'sort_order' => 4],

            // Footer Legal
            ['location' => 'footer_legal', 'label' => 'Terms & Conditions', 'url' => '/p/terms-and-conditions', 'page_id' => $termsPageId, 'sort_order' => 1],
            ['location' => 'footer_legal', 'label' => 'Privacy Policy', 'url' => '/p/privacy-policy', 'page_id' => $privacyPageId, 'sort_order' => 2],
        ];

        foreach ($menus as $menu) {
            NavigationMenu::firstOrCreate([
                'location' => $menu['location'],
                'label' => $menu['label'],
            ], [
                'url' => $menu['url'],
                'sort_order' => $menu['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
