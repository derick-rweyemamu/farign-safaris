<?php

namespace App\Livewire\Frontend;

use App\Models\Tour;
use App\Models\TourType;
use App\Settings\GeneralSetting;
use Livewire\Component;

class TrekkingSliderBlock extends Component
{
    public $tourType = 'trekking';
    public $subheadingTag = 'Trekking Experience';
    public $subheadingClass = 'text-[#4E8243] font-serif font-bold text-lg sm:text-xl tracking-tight';
    public $headingPrefix = 'Package for';
    public $headingPrefixClass = 'text-gray-900 font-extrabold';
    public $headingAccent = 'Mt.Kilimanjaro';
    public $headingAccentClass = 'text-[#8FA5B5] font-bold';
    public $headingSuffix = 'Adventure';
    public $headingSuffixClass = 'text-gray-900 font-extrabold';
    public $description = null;
    public $descriptionClass = 'text-gray-600 text-sm sm:text-base leading-relaxed font-normal max-w-lg';
    public $cursiveText = 'Trusted Partner for a Safe and Memorable Kilimanjaro Adventure';
    public $cursiveClass = 'trekking-cursive text-2xl sm:text-3xl text-gray-600 tracking-wide select-none pt-2 mb-6';
    public $bgImage = null;
    public $limit = 10;
    public $autoplay = true;

    public function mount(
        $tourType = 'trekking',
        $subheadingTag = 'Trekking Experience',
        $subheadingClass = null,
        $headingPrefix = 'Package for',
        $headingPrefixClass = null,
        $headingAccent = 'Mt.Kilimanjaro',
        $headingAccentClass = null,
        $headingSuffix = 'Adventure',
        $headingSuffixClass = null,
        $description = null,
        $descriptionClass = null,
        $cursiveText = 'Trusted Partner for a Safe and Memorable Kilimanjaro Adventure',
        $cursiveClass = null,
        $bgImage = null,
        $limit = 10,
        $autoplay = true
    ) {
        $this->tourType = $tourType ?: 'trekking';
        $this->subheadingTag = $subheadingTag ?: 'Trekking Experience';
        $this->subheadingClass = $subheadingClass ?: 'text-[#4E8243] font-serif font-bold text-lg sm:text-xl tracking-tight';
        $this->headingPrefix = $headingPrefix ?: 'Package for';
        $this->headingPrefixClass = $headingPrefixClass ?: 'text-gray-900 font-extrabold';
        $this->headingAccent = $headingAccent ?: 'Mt.Kilimanjaro';
        $this->headingAccentClass = $headingAccentClass ?: 'text-[#8FA5B5] font-bold';
        $this->headingSuffix = $headingSuffix ?: 'Adventure';
        $this->headingSuffixClass = $headingSuffixClass ?: 'text-gray-900 font-extrabold';
        $this->description = $description;
        $this->descriptionClass = $descriptionClass ?: 'text-gray-600 text-sm sm:text-base leading-relaxed font-normal max-w-lg';
        $this->cursiveText = $cursiveText ?: 'Trusted Partner for a Safe and Memorable Kilimanjaro Adventure';
        $this->cursiveClass = $cursiveClass ?: 'trekking-cursive text-2xl sm:text-3xl text-gray-600 tracking-wide select-none pt-2 mb-6';
        $this->bgImage = $bgImage;
        $this->limit = (int) $limit ?: 10;
        $this->autoplay = filter_var($autoplay, FILTER_VALIDATE_BOOLEAN);
    }

    public function render()
    {
        $companyName = app(GeneralSetting::class)?->company_name ?? 'Mwangwi Safaris';
        $resolvedDescription = $this->description ?: __("Experience the thrill of Africa's highest peak with :company, guided by experts through stunning landscapes.", ['company' => $companyName]);

        $query = Tour::query();

        if ($this->tourType === 'trekking' || $this->tourType === 'hiking') {
            $query->where(function ($q) {
                $q->where('type', 'like', '%trek%')
                    ->orWhere('type', 'like', '%hik%')
                    ->orWhere('type', 'like', '%climb%')
                    ->orWhereHas('tourType', function ($tq) {
                        $tq->where('slug', 'trekking')
                            ->orWhere('slug', 'hiking')
                            ->orWhere('name', 'like', '%trek%')
                            ->orWhere('name', 'like', '%hik%')
                            ->orWhere('name', 'like', '%climb%');
                    });
            });
        } elseif ($this->tourType !== 'all' && !empty($this->tourType)) {
            $query->where(function ($q) {
                $q->where('type', $this->tourType)
                    ->orWhereHas('tourType', fn ($tq) => $tq->where('slug', $this->tourType)->orWhere('name', $this->tourType));
            });
        }

        $tours = $query->take($this->limit)->get();

        if ($tours->isEmpty()) {
            $tours = Tour::where('name', 'like', '%Kilimanjaro%')
                ->orWhere('name', 'like', '%Climb%')
                ->take($this->limit)
                ->get();

            if ($tours->isEmpty()) {
                $tours = Tour::take($this->limit)->get();
            }
        }

        return view('livewire.frontend.trekking-slider-block', [
            'tours' => $tours,
            'resolvedDescription' => $resolvedDescription,
        ]);
    }
}
