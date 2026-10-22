<?php

namespace Database\Seeders;

use App\Services\ReviewImportService;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Ritta C',
                'author_location' => null,
                'rating' => 5.0,
                'trip_type' => 'Couples',
                'review_date' => '2026-07-03',
                'title' => '15 Day East Africa Amazing Safari',
                'content' => 'We had a wonderful 15-day trip through Kenya, Tanzania, and Zanzibar with Fargin Safaris. From the beginning, everything was well organized, and the whole experience was smooth, enjoyable, and memorable. A big thank you to our tour consultant, Emmanuel Sosoma, for planning such a great itinerary and for being helpful and responsive throughout the process. Everything was arranged professionally, and we felt well taken care of from start to finish. We also want to thank our guide, Eric, who was excellent throughout the safari. He was friendly, knowledgeable, patient, and always made sure we were comfortable. His experience and guidance made the trip even more special, and we really appreciated his effort in making each day enjoyable. The combination of safari adventures in Kenya and Tanzania and the relaxing time in Zanzibar was perfect. We saw amazing wildlife, enjoyed beautiful landscapes, and created unforgettable memories. We highly recommend Fargin Safaris to anyone looking for a well-organized East Africa safari experience. Thank you, Emmanuel and Eric, for making our trip so special!',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1066781219-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Lauren R',
                'author_location' => null,
                'rating' => 5.0,
                'trip_type' => 'Friends',
                'review_date' => '2025-12-15',
                'title' => 'Unforgettable safari!',
                'content' => 'This safari was truly the trip of a lifetime. Traveling through Kenya and Tanzania. We went to Amboseli NP, Tarangire NP, Ngorongoro Crater, and the Serengeti NP. We were incredibly lucky to see all of the Big Five, plus an unbelievable number of animals along the way. So so many lions! Every day brought something new and jaw dropping. The accommodation and food exceeded expectations throughout the trip. We were comfortable, well located, and had exactly what you want after long days of game drives.',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1048785534-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Jamie A',
                'author_location' => 'Tocumwal, Australia',
                'rating' => 5.0,
                'trip_type' => 'Friends',
                'review_date' => '2026-01-12',
                'title' => 'Absolutely phenomenal',
                'content' => 'An unforgettable safari across Tanzania & Kenya – truly life-changing. From the moment our safari began, it was clear we were in the hands of absolute professionals who genuinely love what they do. Our journey through Amboseli, Serengeti, Ngorongoro Crater, and Tarangire National Park was nothing short of extraordinary — easily one of the greatest travel experiences of our lives. Every park offered something completely unique: Amboseli delivered jaw-dropping views of Mount Kilimanjaro with elephants calmly wandering beneath it; Serengeti was pure magic with wildlife encounters that felt straight out of a documentary; and Ngorongoro Crater was mind-blowing with its sheer density and diversity of wildlife. The planning was flawless from start to finish. Our guides were exceptional — deeply knowledgeable, passionate, and somehow able to spot wildlife from kilometres away. We would recommend them without hesitation and would travel with them again in a heartbeat.',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1045840642-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Martha W',
                'author_location' => null,
                'rating' => 5.0,
                'trip_type' => 'Friends',
                'review_date' => '2025-10-21',
                'title' => 'A day in Ngorongoro crater.',
                'content' => 'We had a good time at Ngorongoro crater. Our guide was incredible, knowledgeable, pointing out animal behaviours and geological features i would have missed otherwise within just a few hours we saw lions, elephants, zebra , hippos even the elusive black rhino. The crater is not just a place to see animals it is a living ecosystem a historical site and a place of deep cultural significance to the maasai people. Ngorongoro crater is one of the most beautiful and rewarding destinations i have ever visited.',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1035828191-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Cherly J',
                'author_location' => 'Portugal',
                'rating' => 5.0,
                'trip_type' => 'Friends',
                'review_date' => '2025-09-10',
                'title' => 'Unforgettable Ngorongoro crater trip',
                'content' => 'The trip was very comfortable, from starting point till the end. Our driver is so professional, quiet and friendly, he shows us everything that we were supposed to see and know... like all Big Five animals, crater and Lake Makat, Maasai boma with a lot of bird species. I recommend to anyone who plans a trip to Tanzania travel with Fargin Safaris for unforgettable memories where culture, adventures and wildlife experience meet together.',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1029707143-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Leticia M',
                'author_location' => null,
                'rating' => 5.0,
                'trip_type' => 'Safari',
                'review_date' => '2025-08-13',
                'title' => 'A truly unforgettable safaris experience with Fargin Safaris',
                'content' => 'I had the absolute pleasure of booking my trip with Fargin Safaris, and I can honestly say it was one of the best travel decisions I have ever made. From start to finish, the experience was seamless, well-organized, and filled with moments I will cherish forever. Special thanks go to our incredible tour guides, Wilson and Emmanuel. Their professionalism, deep knowledge of the wildlife, and warm personalities made the journey both educational and enjoyable. They went above and beyond to ensure I felt comfortable, safe, and completely immersed in the beauty of nature.',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1024166071-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'hadija M',
                'author_location' => null,
                'rating' => 5.0,
                'trip_type' => 'Safari',
                'review_date' => '2025-08-13',
                'title' => 'Once lifetime experience with Serengeti national park shall never die',
                'content' => 'An incredible experience in the Serengeti National Park with Fargin Safaris. The wildlife viewing, professional guidance, and seamless organization made it an unforgettable journey.',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1024149451-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Emmanuel S',
                'author_location' => null,
                'rating' => 5.0,
                'trip_type' => 'Couples',
                'review_date' => '2025-08-03',
                'title' => 'Amazing 3 days of Experience',
                'content' => 'We had an unforgettable safari in Tanzania with Fargin Safaris. Our guide, Wilson, was knowledgeable, friendly, and made the whole experience seamless. From spotting incredible wildlife to enjoying comfortable accommodations, everything was perfectly organized. Highly recommended for anyone visiting Tanzania!',
                'review_url' => 'https://www.tripadvisor.com/ShowUserReviews-g297913-d33011633-r1022346373-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'platform' => 'tripadvisor',
                'author_name' => 'Karen P',
                'author_location' => null,
                'rating' => 5.0,
                'trip_type' => 'Safari Tip',
                'review_date' => '2025-09-01',
                'title' => 'Tip / Advice Review',
                'content' => 'Make sure your phone is charged and you have extra battery. The wildlife viewing with Fargin Safaris is nonstop and spectacular!',
                'review_url' => 'https://www.tripadvisor.com/Attraction_Review-g297913-d33011633-Reviews-Fargin_Safaris-Arusha_Arusha_Region.html',
                'is_featured' => false,
                'is_published' => true,
            ],
        ];

        $service = new ReviewImportService();
        $service->importFromArray($reviews, 'tripadvisor');
    }
}
