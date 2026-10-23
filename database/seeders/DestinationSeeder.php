<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use App\Models\City;
use App\Models\Animal;
use App\Models\Activity;
use App\Models\NavigationMenu;
use App\Models\Tour;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'slug' => 'serengeti-national-park',
                'name' => 'Serengeti National Park',
                'tagline' => 'Endless Plains, Epic Predator Drama & the Legendary Great Migration',
                'city_name' => 'Mara', // fallback Arusha
                'fallback_city_name' => 'Arusha',
                'description' => '<p><strong>Serengeti National Park</strong> is widely recognized as one of the most iconic wildlife sanctuaries on earth. Derived from the Maasai word <em>"Siringet"</em>, meaning "the place where the land moves on forever", this UNESCO World Heritage Site spans nearly 15,000 square kilometers of untamed African savannah, riverine forests, and dramatic granite kopjes.</p>
<h3>The Greatest Wildlife Spectacle on Earth</h3>
<p>Each year, over 1.5 million wildebeest accompanied by hundreds of thousands of zebras and gazelles traverse the Serengeti ecosystem in search of fresh grazing. Visitors can witness heart-pounding predator ambushes on the southern plains, spectacular calving seasons, and the world-famous river crossings over the Grumeti and Mara rivers where giant Nile crocodiles await.</p>
<h3>Big Cat Territory & Kopjes</h3>
<p>The Serengeti boasts the highest concentration of lions in Africa, with healthy prides often seen lounging on ancient rock outcroppings known as kopjes. The Seronera River Valley is famed for secretive leopards resting in yellow-barked acacia trees, while the sweeping short-grass plains offer premier territory for high-speed cheetah hunts.</p>
<h3>Signature Experiences</h3>
<ul>
    <li>Early-morning hot air balloon flights drifting silently over the waking savannah at sunrise.</li>
    <li>Full-day 4x4 game drives tracing the seasonal movements of the Great Migration herds.</li>
    <li>Spectacular sunset sundowners overlooking the golden plains of Seronera.</li>
</ul>',
                'park_fee_adult_high_season' => 82.60,
                'park_fee_adult_low_season'  => 70.80,
                'park_fee_child_high_season' => 23.60,
                'park_fee_child_low_season'  => 23.60,
                'concession_fee_adult_high_season' => 59.00,
                'concession_fee_adult_low_season'  => 59.00,
                'concession_fee_child_high_season' => 11.80,
                'concession_fee_child_low_season'  => 11.80,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Lion', 'Leopard', 'Cheetah', 'Elephant', 'Buffalo', 'Zebra', 'Wildebeest', 'Giraffe', 'Hippo', 'Hyena'],
                'activities' => ['Game Drives', 'Hot air balloon flights', 'Walking safaris'],
                'seo_title' => 'Serengeti National Park Safaris | Fargin Safaris',
                'seo_description' => 'Experience the legendary Serengeti National Park in Tanzania. Witness the Great Migration, big cat prides, and endless savannah plains with expert safari guides.',
                'tour_keywords' => ['serengeti', 'grumeti', 'migration'],
            ],
            [
                'slug' => 'ngorongoro-conservation-area',
                'name' => 'Ngorongoro Conservation Area',
                'tagline' => 'The World’s Largest Intact Volcanic Caldera & Living Garden of Eden',
                'city_name' => 'Arusha',
                'fallback_city_name' => 'Manyara',
                'description' => '<p>The <strong>Ngorongoro Conservation Area (NCA)</strong> is a world-renowned UNESCO World Heritage site and a biosphere reserve. At its heart lies the breathtaking Ngorongoro Crater—the world’s largest unflooded, unbroken volcanic caldera, dropping more than 600 meters from its mist-draped rim to a fertile 260-square-kilometer floor.</p>
<h3>Africa’s Ultimate Big Five Amphitheater</h3>
<p>The crater floor serves as a natural enclosure for an estimated 25,000 large mammals. It remains one of the few places in East Africa where you have a genuine chance of viewing the complete Big Five—lion, leopard, elephant, buffalo, and the critically endangered black rhino—all within a single exhilarating game drive.</p>
<h3>Living in Harmony with Nature</h3>
<p>Unlike traditional national parks, the Ngorongoro Conservation Area is pioneering a multi-use model where semi-nomadic Maasai pastoralists graze their cattle alongside wild animals. Nearby lies the prehistoric Olduvai Gorge, the archaeological "Cradle of Humankind" where Dr. Louis and Mary Leakey unearthed early hominid fossils dating back nearly 2 million years.</p>',
                'park_fee_adult_high_season' => 70.80,
                'park_fee_adult_low_season'  => 70.80,
                'park_fee_child_high_season' => 23.60,
                'park_fee_child_low_season'  => 23.60,
                'concession_fee_adult_high_season' => 59.00,
                'concession_fee_adult_low_season'  => 59.00,
                'concession_fee_child_high_season' => 11.80,
                'concession_fee_child_low_season'  => 11.80,
                'crater_fee_adult_high_season' => 295.00,
                'crater_fee_adult_low_season'  => 295.00,
                'animals' => ['Black Rhino', 'Lion', 'Elephant', 'Buffalo', 'Zebra', 'Wildebeest', 'Hippo', 'Hyena', 'Cheetah'],
                'activities' => ['Game Drives', 'Walking safaris'],
                'seo_title' => 'Ngorongoro Crater Safaris | Fargin Safaris',
                'seo_description' => 'Descend into the Ngorongoro Crater in Tanzania. Encounter dense black rhino populations, giant tuskers, and stunning caldera landscapes.',
                'tour_keywords' => ['ngorongoro', 'crater'],
            ],
            [
                'slug' => 'tarangire-national-park',
                'name' => 'Tarangire National Park',
                'tagline' => 'The Kingdom of Giants: Ancient Baobab Forests & Massive Elephant Herds',
                'city_name' => 'Manyara',
                'fallback_city_name' => 'Arusha',
                'description' => '<p><strong>Tarangire National Park</strong> is renowned for its strikingly scenic topography, where rolling acacia woodlands and seasonal swamps are punctuated by towering ancient baobab trees that have stood for centuries. Spanning 2,850 square kilometers, Tarangire is the sixth-largest national park in Tanzania.</p>
<h3>The Sanctuary of Elephants</h3>
<p>Tarangire boasts one of the highest elephant population densities in all of Africa. During the dry season, herds of up to 300 elephants gather along the Tarangire River, digging into dry riverbeds to find underground freshwater and creating lifelines for other wildlife species.</p>
<h3>The Dry-Season Wildlife Lifeline</h3>
<p>From July through October, Tarangire experiences its own seasonal mini-migration. When surrounding areas dry out, the permanent Tarangire River draws tens of thousands of zebras, wildebeest, elands, giraffes, and impalas, pursued closely by lions, leopards, and elusive wild dogs.</p>
<h3>Birdwatcher’s Paradise</h3>
<p>With more than 550 bird species recorded, the park is an avian wonderland. From the endemic ash starlings and yellow-collared lovebirds to magnificent martial eagles, birding in Tarangire is exceptional throughout the year.</p>',
                'park_fee_adult_high_season' => 59.00,
                'park_fee_adult_low_season'  => 53.10,
                'park_fee_child_high_season' => 17.70,
                'park_fee_child_low_season'  => 17.70,
                'concession_fee_adult_high_season' => 59.00,
                'concession_fee_adult_low_season'  => 59.00,
                'concession_fee_child_high_season' => 11.80,
                'concession_fee_child_low_season'  => 11.80,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Elephant', 'Lion', 'Leopard', 'Giraffe', 'Buffalo', 'Zebra', 'Wildebeest', 'Cheetah', 'Hyena'],
                'activities' => ['Game Drives', 'Walking safaris', 'Night drives'],
                'seo_title' => 'Tarangire National Park Safaris | Fargin Safaris',
                'seo_description' => 'Discover Tarangire National Park, famous for massive elephant herds and ancient baobab trees. Book unforgettable guided game drives with Fargin Safaris.',
                'tour_keywords' => ['tarangire'],
            ],
            [
                'slug' => 'lake-manyara-national-park',
                'name' => 'Lake Manyara National Park',
                'tagline' => 'Tree-Climbing Lions, Lush Underground Springs & Pink Flamingo Shallows',
                'city_name' => 'Manyara',
                'fallback_city_name' => 'Arusha',
                'description' => '<p>Nestled at the base of the dramatic 600-meter Great Rift Valley escarpment, <strong>Lake Manyara National Park</strong> was famously described by Ernest Hemingway as <em>"the loveliest I had seen in Africa"</em>. Despite its modest size of 330 square kilometers, the park packs an astonishing variety of ecosystems into a narrow corridor.</p>
<h3>Lions in the Canopy</h3>
<p>Lake Manyara is legendary for its tree-climbing lions. While lions rarely scale trees in other parts of Africa, the prides here regularly lounge comfortably across the horizontal boughs of giant mahogany and umbrella acacia trees, escaping tsetse flies and taking advantage of cool breezes.</p>
<h3>A Microcosm of African Ecosystems</h3>
<p>Upon entering the park, visitors travel through a dense groundwater forest fed by gushing subterranean springs, home to troops of hundreds of olive baboons and blue monkeys. The forest opens into acacia scrub, expansive floodplains, and the shimmering alkaline lake shore which turns vibrant pink with seasonal flocks of lesser flamingos.</p>',
                'park_fee_adult_high_season' => 59.00,
                'park_fee_adult_low_season'  => 53.10,
                'park_fee_child_high_season' => 17.70,
                'park_fee_child_low_season'  => 17.70,
                'concession_fee_adult_high_season' => 59.00,
                'concession_fee_adult_low_season'  => 59.00,
                'concession_fee_child_high_season' => 11.80,
                'concession_fee_child_low_season'  => 11.80,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Lion', 'Elephant', 'Giraffe', 'Buffalo', 'Zebra', 'Hippo', 'Leopard'],
                'activities' => ['Game Drives', 'Walking safaris', 'Night drives'],
                'seo_title' => 'Lake Manyara National Park Safaris | Fargin Safaris',
                'seo_description' => 'Explore Lake Manyara National Park in Tanzania. See tree-climbing lions, pink flamingo shallows, and lush groundwater forests with Fargin Safaris.',
                'tour_keywords' => ['manyara'],
            ],
            [
                'slug' => 'mount-kilimanjaro-national-park',
                'name' => 'Mount Kilimanjaro National Park',
                'tagline' => 'The Roof of Africa: Climb the World’s Tallest Free-Standing Mountain (5,895m)',
                'city_name' => 'Kilimanjaro',
                'fallback_city_name' => 'Arusha',
                'description' => '<p>Towering majestically above the acacia plains of northern Tanzania, <strong>Mount Kilimanjaro</strong> is an awe-inspiring dormant stratovolcano. At 5,895 meters (19,341 feet), it is the highest peak in Africa and the highest free-standing mountain in the world.</p>
<h3>A Journey Through Five Climate Zones</h3>
<p>Trekking Kilimanjaro is a world-class physical and spiritual adventure. Climbing from the gate to Uhuru Peak is equivalent to traveling from the tropics to the Arctic in less than a week, traversing five distinct ecological zones:</p>
<ul>
    <li><strong>Cultivated Foothills:</strong> Lush farmlands and mountain villages.</li>
    <li><strong>Montane Rainforest:</strong> Ancient forest alive with colobus monkeys and exotic birds.</li>
    <li><strong>Heath & Moorland:</strong> Giant lobelias and otherworldly senecio plants.</li>
    <li><strong>Alpine Desert:</strong> Stark, lunar landscapes with sweeping vistas.</li>
    <li><strong>Arctic Summit:</strong> Pristine glaciers and the legendary Uhuru Peak crater rim.</li>
</ul>
<h3>Established Climbing Routes</h3>
<p>Fargin Safaris operates guided treks along the mountain’s top routes, including the scenic <strong>Machame Route</strong> ("Whiskey Route"), the pristine <strong>Lemosho Route</strong> with optimal acclimatization, the direct northern <strong>Rongai Route</strong>, and the classic hut-based <strong>Marangu Route</strong>.</p>',
                'park_fee_adult_high_season' => 82.60,
                'park_fee_adult_low_season'  => 82.60,
                'park_fee_child_high_season' => 23.60,
                'park_fee_child_low_season'  => 23.60,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Buffalo', 'Leopard', 'Elephant', 'Giraffe'],
                'activities' => ['Walking safaris'],
                'seo_title' => 'Mount Kilimanjaro Climbing Routes & Treks | Fargin Safaris',
                'seo_description' => 'Summit Mount Kilimanjaro with experienced mountain guides. Explore Machame, Lemosho, Marangu, and Rongai route packages with Fargin Safaris.',
                'tour_keywords' => ['kilimanjaro', 'climb', 'machame', 'lemosho', 'rongai', 'marangu'],
            ],
            [
                'slug' => 'zanzibar-archipelago',
                'name' => 'Zanzibar Archipelago',
                'tagline' => 'Historic Spice Island: UNESCO Stone Town, Turquoise Coral Lagoons & White Beaches',
                'city_name' => 'Unguja Urban West',
                'fallback_city_name' => 'Dar es Salaam',
                'description' => '<p>Located 35 kilometers off the coast of mainland Tanzania in the azure Indian Ocean, the <strong>Zanzibar Archipelago</strong> (Unguja) is an idyllic tropical haven infused with centuries of Swahili, Arabic, Persian, and European history.</p>
<h3>UNESCO Stone Town</h3>
<p>The cultural heart of Zanzibar is Stone Town, a maze of narrow alleyways lined with coral-rag mansions, intricately carved wooden doorways, bustling open-air bazaars, and fragrant spice stalls. Highlights include the Sultan’s Palace (Beit-al-Sahel), the House of Wonders, and the poignant Old Slave Market memorial.</p>
<h3>Pristine Tropical Beaches & Coral Reefs</h3>
<p>Zanzibar offers some of the most celebrated coastline in the world. The northern shores around Nungwi and Kendwa feature powder-soft white sand and crystal-clear tides ideal for swimming all day. The east coast villages of Paje and Matemwe are renowned for kite surfing, reef walks, and proximity to Mnemba Atoll’s world-class scuba diving and dolphin encounters.</p>',
                'park_fee_adult_high_season' => 0.00,
                'park_fee_adult_low_season'  => 0.00,
                'park_fee_child_high_season' => 0.00,
                'park_fee_child_low_season'  => 0.00,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Dolphin', 'Zebra'],
                'activities' => ['Walking safaris', 'Private game reserves'],
                'seo_title' => 'Zanzibar Beach Holidays & Stone Town Tours | Fargin Safaris',
                'seo_description' => 'Unwind on the pristine beaches of Zanzibar after your Tanzanian safari. Experience UNESCO Stone Town, spice tours, and turquoise ocean resorts.',
                'tour_keywords' => ['zanzibar'],
            ],
            [
                'slug' => 'lake-eyasi',
                'name' => 'Lake Eyasi',
                'tagline' => 'Ancient Cultural Sanctuary of the Hadzabe Bushmen & Datoga Blacksmiths',
                'city_name' => 'Arusha',
                'fallback_city_name' => 'Manyara',
                'description' => '<p><strong>Lake Eyasi</strong> is an untamed, mildly seasonal salt lake lying on the floor of the Great Rift Valley southwest of the Ngorongoro Highlands. Renowned far more for its human heritage than traditional game drives, Eyasi provides one of Africa’s deepest cultural immersions.</p>
<h3>The Hadzabe: Earth’s Last Hunter-Gatherers</h3>
<p>Lake Eyasi is home to the indigenous Hadzabe people, an ancient tribe whose lifestyle has remained virtually unchanged for over 10,000 years. Speaking an ancient click language, they live in harmony with the bush without agriculture, livestock, or modern property, hunting with hand-carved bows and poison-tipped arrows.</p>
<h3>The Datoga Pastoralists</h3>
<p>Sharing the Eyasi basin are the Datoga, proud pastoralists and master metalsmiths. Visitors can witness their traditional open-air smelting workshops where scrap metal is transformed over glowing embers into arrowheads, knives, and intricately engraved brass jewelry.</p>',
                'park_fee_adult_high_season' => 35.40,
                'park_fee_adult_low_season'  => 35.40,
                'park_fee_child_high_season' => 11.80,
                'park_fee_child_low_season'  => 11.80,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Zebra', 'Hippo', 'Hyena'],
                'activities' => ['Walking safaris'],
                'seo_title' => 'Lake Eyasi Cultural Safaris | Hadzabe & Datoga Tours',
                'seo_description' => 'Visit Lake Eyasi in Tanzania to meet the indigenous Hadzabe hunter-gatherers and Datoga blacksmiths. Authentic cultural safaris with Fargin Safaris.',
                'tour_keywords' => ['eyasi'],
            ],
            [
                'slug' => 'arusha-national-park',
                'name' => 'Arusha National Park & Mount Meru',
                'tagline' => 'Verdant Volcanic Landscapes, Momella Lakes & Tranquil Walking Safaris',
                'city_name' => 'Arusha',
                'fallback_city_name' => 'Kilimanjaro',
                'description' => '<p>Located just 45 minutes from Arusha city, <strong>Arusha National Park</strong> is a richly multifaceted jewel often referred to as Tanzania’s secret garden. Dominated by the jagged volcanic silhouette of <strong>Mount Meru</strong> (4,566m), the park features spectacular terrain shifts across small distances.</p>
<h3>Volcanic Calderas & Alkaline Lakes</h3>
<p>The park encompasses the dramatic Ngurdoto Crater, an extinct caldera whose swampy floor is teeming with warthogs, buffaloes, and antelopes. Further north lie the seven shallow Momella Lakes, each mineral-rich body of water hosting thousands of feeding flamingos and waterbirds.</p>
<h3>Unrivaled Walking Safaris</h3>
<p>Because there are few large feline predators in the lower forest reaches, Arusha National Park is Tanzania’s premier destination for guided walking safaris. Accompanied by an armed TANAPA ranger, you can stroll quietly beside herds of giraffes, zebras, and buffaloes against the backdrop of thundering mountain waterfalls.</p>',
                'park_fee_adult_high_season' => 59.00,
                'park_fee_adult_low_season'  => 53.10,
                'park_fee_child_high_season' => 17.70,
                'park_fee_child_low_season'  => 17.70,
                'concession_fee_adult_high_season' => 59.00,
                'concession_fee_adult_low_season'  => 59.00,
                'concession_fee_child_high_season' => 11.80,
                'concession_fee_child_low_season'  => 11.80,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Giraffe', 'Buffalo', 'Zebra', 'Leopard', 'Hippo', 'Hyena'],
                'activities' => ['Walking safaris', 'Game Drives'],
                'seo_title' => 'Arusha National Park & Mount Meru Safaris | Fargin Safaris',
                'seo_description' => 'Explore Arusha National Park and hike Mount Meru. Enjoy guided walking safaris, black-and-white colobus sightings, and Momella Lakes views.',
                'tour_keywords' => ['meru', 'arusha national park'],
            ],
            [
                'slug' => 'lake-natron',
                'name' => 'Lake Natron',
                'tagline' => 'Crimson Waters, Sacred Ol Doinyo Lengai & Africa’s Flamingo Nursery',
                'city_name' => 'Arusha',
                'fallback_city_name' => 'Manyara',
                'description' => '<p>Set in a remote, desolate corner of the northern Rift Valley near the Kenyan border, <strong>Lake Natron</strong> is one of the most surreal and hauntingly beautiful landscapes in East Africa. The highly alkaline, salt-encrusted lake glows in shifting shades of fiery crimson, burnt orange, and deep pink.</p>
<h3>The Mountain of God: Ol Doinyo Lengai</h3>
<p>Brooding above the southern shore of Lake Natron stands <strong>Ol Doinyo Lengai</strong>, meaning "Mountain of God" in the Maasai tongue. It is the only active volcano on the planet that erupts rare natrocarbonatite lava, which flows black in the sunlight and rapidly weathers into a ghostly chalk-white crust.</p>
<h3>The Sacred Nursery of Lesser Flamingos</h3>
<p>Despite water temperatures reaching 60°C (140°F) and caustic pH levels, Lake Natron is the single most critical breeding habitat in the world for over 2.5 million Lesser Flamingos. The corrosive mud flats protect their delicate nests from mainland predators, providing a safe cradle for young chicks.</p>',
                'park_fee_adult_high_season' => 35.40,
                'park_fee_adult_low_season'  => 35.40,
                'park_fee_child_high_season' => 11.80,
                'park_fee_child_low_season'  => 11.80,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Zebra', 'Giraffe', 'Hyena'],
                'activities' => ['Walking safaris'],
                'seo_title' => 'Lake Natron & Ol Doinyo Lengai Treks | Fargin Safaris',
                'seo_description' => 'Visit Lake Natron in northern Tanzania. Witness millions of breeding flamingos, climb sacred Ol Doinyo Lengai, and hike Engare Sero waterfalls.',
                'tour_keywords' => ['natron', 'lengai'],
            ],
            [
                'slug' => 'nyerere-national-park',
                'name' => 'Nyerere National Park',
                'tagline' => 'Africa’s Largest Pristine Wilderness & Scenic Rufiji River Safaris',
                'city_name' => 'Morogoro',
                'fallback_city_name' => 'Pwani',
                'description' => '<p>Encompassing over 30,000 square kilometers, <strong>Nyerere National Park</strong> (formerly the northern portion of the legendary Selous Game Reserve) is the largest national park in Tanzania and one of the grandest wildlife refuges on the African continent.</p>
<h3>Riverine Splendor of the Rufiji</h3>
<p>The lifeblood of Nyerere is the mighty Rufiji River, which flows across the park through a network of lakes, sandbanks, and tranquil channels. River boat safaris allow travelers to drift silently within feet of bathing elephants, lounging pods of hippos, and basking Nile crocodiles.</p>
<h3>Wild Dog Stronghold & Raw Solitude</h3>
<p>Nyerere hosts one of Africa\'s most robust remaining populations of the endangered African wild dog (painted wolf). With significantly fewer tourist vehicles than the northern circuit, Nyerere provides an authentic, unhurried, and immensely peaceful safari experience.</p>',
                'park_fee_adult_high_season' => 82.60,
                'park_fee_adult_low_season'  => 70.80,
                'park_fee_child_high_season' => 23.60,
                'park_fee_child_low_season'  => 23.60,
                'concession_fee_adult_high_season' => 59.00,
                'concession_fee_adult_low_season'  => 59.00,
                'concession_fee_child_high_season' => 11.80,
                'concession_fee_child_low_season'  => 11.80,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Wild Dog', 'Lion', 'Leopard', 'Elephant', 'Buffalo', 'Hippo', 'Giraffe', 'Hyena'],
                'activities' => ['Game Drives', 'Walking safaris', 'Private game reserves'],
                'seo_title' => 'Nyerere National Park (Selous) Safaris | Fargin Safaris',
                'seo_description' => 'Venture into Nyerere National Park in southern Tanzania. Experience Rufiji River boat safaris, walking safaris, and wild dog tracking.',
                'tour_keywords' => ['nyerere', 'selous'],
            ],
            [
                'slug' => 'ruaha-national-park',
                'name' => 'Ruaha National Park',
                'tagline' => 'The Predator Capital: Vast Lion Prides, Baobab Vistas & Remote Southern Solitude',
                'city_name' => 'Iringa',
                'fallback_city_name' => 'Mbeya',
                'description' => '<p><strong>Ruaha National Park</strong> is a rugged, untamed wilderness spanning 20,226 square kilometers in south-central Tanzania. Characterized by rolling hills, rocky kopjes, and sprawling groves of giant baobab trees, Ruaha is widely celebrated by seasoned safari purists.</p>
<h3>The Predator Capital of East Africa</h3>
<p>Ruaha is home to 10% of the world’s remaining wild lion population, with colossal super-prides numbering 20 or more individuals roaming along the Great Ruaha River. The park also hosts thriving populations of cheetahs, leopards, and spotted hyenas.</p>
<h3>Where Southern & Eastern Africa Meet</h3>
<p>Because Ruaha sits at the intersection of southern African Zambezian vegetation and East African acacia savannah, it features unique wildlife crossovers. It is one of the few places where you can spot both Greater and Lesser Kudu, alongside roan and sable antelopes.</p>',
                'park_fee_adult_high_season' => 59.00,
                'park_fee_adult_low_season'  => 53.10,
                'park_fee_child_high_season' => 17.70,
                'park_fee_child_low_season'  => 17.70,
                'concession_fee_adult_high_season' => 59.00,
                'concession_fee_adult_low_season'  => 59.00,
                'concession_fee_child_high_season' => 11.80,
                'concession_fee_child_low_season'  => 11.80,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Lion', 'Leopard', 'Cheetah', 'Wild Dog', 'Elephant', 'Buffalo', 'Giraffe', 'Zebra', 'Hyena'],
                'activities' => ['Game Drives', 'Walking safaris', 'Night drives'],
                'seo_title' => 'Ruaha National Park Safaris | Fargin Safaris',
                'seo_description' => 'Experience Ruaha National Park in southern Tanzania. Discover massive lion prides, wild dog packs, and pristine baobab landscapes with Fargin Safaris.',
                'tour_keywords' => ['ruaha'],
            ],
            [
                'slug' => 'amboseli-national-park',
                'name' => 'Amboseli National Park',
                'tagline' => 'Free-Roaming Elephant Herds Against the Silhouette of Mount Kilimanjaro',
                'city_name' => 'Kajiado',
                'fallback_city_name' => 'Nairobi',
                'description' => '<p>Located in southern Kenya just across the Tanzanian frontier, <strong>Amboseli National Park</strong> is world-famous for offering the ultimate classic African safari postcard: massive herds of big-tusked elephants grazing quietly across the plains against the majestic, snow-capped backdrop of Mount Kilimanjaro.</p>
<h3>Living Elephant Legends</h3>
<p>Amboseli is home to the longest-running elephant research study in the world, founded by Cynthia Moss in 1972. Because these elephants have been protected and studied for generations, they are exceptionally calm around vehicles, granting photographers extraordinary close-up encounters with mature matriarchs and grand bulls.</p>
<h3>Observation Hill & Freshwater Swamps</h3>
<p>Fed by subterranean meltwater streams flowing underground from Mount Kilimanjaro, the park’s Enkongo Narok and Olokenya swamps create verdant oases teeming with hippos, African fish eagles, pelicans, and yellow-billed storks.</p>',
                'park_fee_adult_high_season' => 70.00,
                'park_fee_adult_low_season'  => 60.00,
                'park_fee_child_high_season' => 20.00,
                'park_fee_child_low_season'  => 20.00,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Elephant', 'Lion', 'Cheetah', 'Giraffe', 'Zebra', 'Buffalo', 'Hippo', 'Hyena'],
                'activities' => ['Game Drives', 'Walking safaris'],
                'seo_title' => 'Amboseli National Park Safaris | Kenya Wildlife Safaris',
                'seo_description' => 'Tour Amboseli National Park in Kenya. View giant elephant herds with Mount Kilimanjaro in the background on guided safaris with Fargin Safaris.',
                'tour_keywords' => ['amboseli'],
            ],
            [
                'slug' => 'masai-mara-national-reserve',
                'name' => 'Masai Mara National Reserve',
                'tagline' => 'World-Famous Savannah Arena: River Crossings, Big Cats & Endless Wildlife',
                'city_name' => 'Narok',
                'fallback_city_name' => 'Nairobi',
                'description' => '<p>The <strong>Masai Mara National Reserve</strong> is Kenya’s crown wildlife destination, spanning 1,510 square kilometers of sweeping grasslands, rolling acacia hills, and riverine forests that connect directly with Tanzania’s Serengeti to form one magnificent ecosystem.</p>
<h3>The Great Mara River Crossing</h3>
<p>Between July and October, the Great Migration reaches its apex in the Mara. Millions of wildebeest and zebras muster along the cliffs of the Mara and Talek rivers before surging across the waters in dramatic battles against swirling currents and apex crocodile predators.</p>
<h3>Unrivaled Big Cat Action</h3>
<p>The Mara’s abundant prey base sustains remarkable populations of lions, cheetahs, and leopards. It is globally recognized as one of the very best locations for wildlife documentary filming and high-action predator photography.</p>',
                'park_fee_adult_high_season' => 100.00,
                'park_fee_adult_low_season'  => 80.00,
                'park_fee_child_high_season' => 50.00,
                'park_fee_child_low_season'  => 40.00,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Lion', 'Leopard', 'Cheetah', 'Elephant', 'Buffalo', 'Zebra', 'Wildebeest', 'Giraffe', 'Hippo', 'Hyena'],
                'activities' => ['Game Drives', 'Hot air balloon flights', 'Walking safaris'],
                'seo_title' => 'Masai Mara Safari Packages | Kenya Wildebeest Migration',
                'seo_description' => 'Experience the world-famous Masai Mara National Reserve in Kenya. Witness the Great Migration river crossing and big cats on expert guided safaris.',
                'tour_keywords' => ['masai mara', 'maasai mara', 'mara'],
            ],
            [
                'slug' => 'lake-nakuru-national-park',
                'name' => 'Lake Nakuru National Park',
                'tagline' => 'Rift Valley Sanctuary for Endangered Black & White Rhinos and Bird Spectacles',
                'city_name' => 'Nakuru',
                'fallback_city_name' => 'Nairobi',
                'description' => '<p>Encircling a picturesque alkaline lake on the floor of the Great Rift Valley, <strong>Lake Nakuru National Park</strong> is a 188-square-kilometer wildlife haven famous for its striking scenery, fever-tree woodlands, and remarkable rhino conservation success.</p>
<h3>Pioneering Rhino Sanctuary</h3>
<p>Lake Nakuru is one of Kenya’s leading rhino sanctuaries, providing a heavily protected habitat for more than 100 black and white rhinos. Visitors frequently enjoy rare, close-range viewings of white rhinos grazing in open grasslands along the lakeshore.</p>
<h3>Flamingos & Rothschild Giraffes</h3>
<p>Depending on seasonal water salinity, millions of lesser and greater flamingos carpet the lake surface in shimmering pink. The park is also a vital sanctuary for the rare, endangered Rothschild\'s giraffe, waterbucks, and resident tree-climbing lions.</p>',
                'park_fee_adult_high_season' => 70.00,
                'park_fee_adult_low_season'  => 60.00,
                'park_fee_child_high_season' => 20.00,
                'park_fee_child_low_season'  => 20.00,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['White Rhino', 'Black Rhino', 'Lion', 'Leopard', 'Giraffe', 'Buffalo', 'Zebra', 'Hippo'],
                'activities' => ['Game Drives', 'Walking safaris'],
                'seo_title' => 'Lake Nakuru National Park Safaris | Rhino & Flamingo Tours',
                'seo_description' => 'Visit Lake Nakuru National Park in Kenya. See black and white rhinos, Rothschild giraffes, and picturesque alkaline lakes with Fargin Safaris.',
                'tour_keywords' => ['nakuru'],
            ],
            [
                'slug' => 'nairobi',
                'name' => 'Nairobi & Nairobi National Park',
                'tagline' => 'The Safari Capital of the World: Urban Energy Meets Untamed Wilderness',
                'city_name' => 'Nairobi',
                'fallback_city_name' => 'Nairobi',
                'description' => '<p><strong>Nairobi is the Safari Capital of the World</strong>. Experience a metropolis like no other—where sleek modern skyscrapers meet untamed wilderness. As East Africa\'s cultural and economic hub, Nairobi seamlessly blends thrilling urban energy with world-class wildlife conservation, offering the ultimate gateway to your East African adventure.</p>
<h3>The Only Capital with a National Park</h3>
<p>Just a 20-minute drive from the city center lies Nairobi National Park, where wide-open savannah plains are backdropped by dramatic city skylines. This unique sanctuary is a renowned stronghold for endangered black rhinos, alongside healthy populations of lions, leopards, hyenas, buffalos, and over 400 recorded bird species.</p>
<h3>Conservation Centers & Cultural Heritage</h3>
<p>Beyond the national park, Nairobi offers immersive conservation landmarks including the David Sheldrick Wildlife Trust elephant orphanage and the Giraffe Centre in Karen. Discover rich local heritage at the Karen Blixen Museum, Bomas of Kenya, and vibrant craft markets showcasing handmade artisan treasures.</p>
<h3>Signature Experiences</h3>
<ul>
    <li>Early-morning game drive in Nairobi National Park with lion prides posed against the backdrop of skyscrapers.</li>
    <li>Hand-feeding endangered Rothschild giraffes at the Giraffe Centre.</li>
    <li>Exploring artisanal Maasai markets and savoring contemporary East African fine dining.</li>
</ul>',
                'park_fee_adult_high_season' => 43.00,
                'park_fee_adult_low_season'  => 43.00,
                'park_fee_child_high_season' => 22.00,
                'park_fee_child_low_season'  => 22.00,
                'concession_fee_adult_high_season' => 0.00,
                'concession_fee_adult_low_season'  => 0.00,
                'concession_fee_child_high_season' => 0.00,
                'concession_fee_child_low_season'  => 0.00,
                'crater_fee_adult_high_season' => 0.00,
                'crater_fee_adult_low_season'  => 0.00,
                'animals' => ['Black Rhino', 'Lion', 'Leopard', 'Giraffe', 'Buffalo', 'Zebra', 'Hyena'],
                'activities' => ['Game Drives', 'Walking safaris'],
                'seo_title' => 'Nairobi Day Tours & Safaris | Fargin Safaris',
                'seo_description' => 'Explore Nairobi, Kenya\'s vibrant capital and wildlife hub. Discover Nairobi National Park, Giraffe Centre, elephant orphanage, and cultural excursions.',
                'tour_keywords' => ['nairobi'],
            ],
        ];

        // Cache animals & activities for fast pivot linking
        $allAnimals = Animal::all()->keyBy(fn($a) => strtolower($a->name));
        $allActivities = Activity::all()->keyBy(fn($a) => strtolower($a->name));
        $allTours = Tour::all();

        foreach ($destinations as $data) {
            // Find city
            $city = City::where('name', $data['city_name'])->first()
                ?? City::where('name', $data['fallback_city_name'])->first();

            $cityId = $city?->id ?? 1;

            // Find or create destination by slug
            $destination = Destination::firstOrNew(['slug' => $data['slug']]);

            // Set translatable fields properly
            $destination->setTranslation('name', 'en', $data['name']);
            $destination->setTranslation('tagline', 'en', $data['tagline']);
            $destination->setTranslation('description', 'en', $data['description']);

            $destination->city_id = $cityId;
            $destination->is_active = true;
            $destination->is_featured = true;
            $destination->show_on_page = true;

            // Fee fields
            $destination->park_fee_adult_high_season = $data['park_fee_adult_high_season'];
            $destination->park_fee_adult_low_season = $data['park_fee_adult_low_season'];
            $destination->park_fee_child_high_season = $data['park_fee_child_high_season'];
            $destination->park_fee_child_low_season = $data['park_fee_child_low_season'];

            $destination->concession_fee_adult_high_season = $data['concession_fee_adult_high_season'];
            $destination->concession_fee_adult_low_season = $data['concession_fee_adult_low_season'];
            $destination->concession_fee_child_high_season = $data['concession_fee_child_high_season'];
            $destination->concession_fee_child_low_season = $data['concession_fee_child_low_season'];

            $destination->crater_fee_adult_high_season = $data['crater_fee_adult_high_season'];
            $destination->crater_fee_adult_low_season = $data['crater_fee_adult_low_season'];

            // DO NOT OVERWRITE IMAGES - preserve existing or leave as null for user to upload
            if (!$destination->exists) {
                $destination->featured_image = null;
                $destination->gallery = null;
            }

            $destination->save();

            // Sync Animals
            $animalIds = [];
            foreach ($data['animals'] as $animName) {
                $key = strtolower($animName);
                if (isset($allAnimals[$key])) {
                    $animalIds[] = $allAnimals[$key]->id;
                }
            }
            if (!empty($animalIds)) {
                $destination->animals()->sync($animalIds);
            }

            // Sync Activities
            $activityIds = [];
            foreach ($data['activities'] as $actName) {
                $key = strtolower($actName);
                if (isset($allActivities[$key])) {
                    $activityIds[] = $allActivities[$key]->id;
                }
            }
            if (!empty($activityIds)) {
                $destination->activities()->sync($activityIds);
            }

            // Update SEO
            if (method_exists($destination, 'seo')) {
                $destination->seo()->updateOrCreate(
                    [],
                    [
                        'title' => $data['seo_title'],
                        'description' => $data['seo_description'],
                    ]
                );
            }

            // Connect destination to matching tours
            if (!empty($data['tour_keywords'])) {
                $matchedTourIds = $allTours->filter(function ($tour) use ($data) {
                    $tName = strtolower($tour->name . ' ' . $tour->slug);
                    foreach ($data['tour_keywords'] as $kw) {
                        if (str_contains($tName, strtolower($kw))) {
                            return true;
                        }
                    }
                    return false;
                })->pluck('id')->toArray();

                if (!empty($matchedTourIds)) {
                    $destination->tours()->syncWithoutDetaching($matchedTourIds);
                }
            }
        }

        // Link footer destinations navigation menu items to newly created slugs
        $menuMapping = [
            'Serengeti National Park' => '/destinations/serengeti-national-park',
            'Ngorongoro Crater' => '/destinations/ngorongoro-conservation-area',
            'Mount Kilimanjaro' => '/destinations/mount-kilimanjaro-national-park',
            'Zanzibar Islands' => '/destinations/zanzibar-archipelago',
        ];

        foreach ($menuMapping as $label => $url) {
            $menu = NavigationMenu::where('location', 'footer_destinations')
                ->where(function ($q) use ($label) {
                    $q->where('label->en', $label)
                      ->orWhere('label', 'like', '%' . $label . '%');
                })
                ->first();

            if ($menu) {
                $menu->update(['url' => $url]);
            }
        }
    }
}
