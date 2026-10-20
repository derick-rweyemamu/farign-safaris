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
    public $subheadingClass = 'text-[#4E8243] font-serif font-normal text-lg sm:text-xl tracking-tight';
    public $headingPrefix = 'Package for';
    public $headingPrefixClass = 'text-gray-900 font-normal';
    public $headingAccent = 'Mt.Kilimanjaro';
    public $headingAccentClass = 'text-[#8FA5B5] font-normal';
    public $headingSuffix = 'Adventure';
    public $headingSuffixClass = 'text-gray-900 font-normal';
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
        $this->subheadingClass = $subheadingClass ?: 'text-[#4E8243] font-serif font-normal text-lg sm:text-xl tracking-tight';
        $this->headingPrefix = $headingPrefix ?: 'Package for';
        $this->headingPrefixClass = $headingPrefixClass ?: 'text-gray-900 font-normal';
        $this->headingAccent = $headingAccent ?: 'Mt.Kilimanjaro';
        $this->headingAccentClass = $headingAccentClass ?: 'text-[#8FA5B5] font-normal';
        $this->headingSuffix = $headingSuffix ?: 'Adventure';
        $this->headingSuffixClass = $headingSuffixClass ?: 'text-gray-900 font-normal';
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

    /**
     * Converts common Tailwind CSS utility classes into inline CSS styles.
     * Guarantees that custom/arbitrary Tailwind classes (e.g. text-[#4E8243], font-serif,
     * text-xl, font-bold) are rendered reliably regardless of whether they were pre-compiled.
     */
    public static function tailwindToStyles(?string $classString): string
    {
        if (empty($classString)) {
            return '';
        }

        $classes = preg_split('/\s+/', trim($classString));
        $styles = [];

        foreach ($classes as $cls) {
            $cls = trim($cls);
            if (empty($cls)) continue;

            // Strip responsive prefixes for base style (sm:, md:, lg:, etc.)
            $baseCls = preg_replace('/^(?:sm|md|lg|xl|2xl):/', '', $cls);

            // 1. Arbitrary text color: text-[#...], text-[rgb(...)], text-[hsl(...)]
            if (preg_match('/^text-\[(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\)|hsla?\([^)]+\))\]$/', $baseCls, $m)) {
                $styles['color'] = $m[1];
                continue;
            }

            // 2. Arbitrary background color: bg-[#...]
            if (preg_match('/^bg-\[(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\)|hsla?\([^)]+\))\]$/', $baseCls, $m)) {
                $styles['background-color'] = $m[1];
                continue;
            }

            // 3. Arbitrary font size: text-[24px], text-[1.5rem]
            if (preg_match('/^text-\[([0-9.]+(?:px|rem|em|%|pt))\]$/', $baseCls, $m)) {
                $styles['font-size'] = $m[1];
                continue;
            }

            // 4. Arbitrary letter spacing: tracking-[0.1em]
            if (preg_match('/^tracking-\[([0-9.-]+(?:em|px|rem))\]$/', $baseCls, $m)) {
                $styles['letter-spacing'] = $m[1];
                continue;
            }

            // 5. Arbitrary line height: leading-[1.2]
            if (preg_match('/^leading-\[([0-9.]+(?:rem|em|px|%)?)\]$/', $baseCls, $m)) {
                $styles['line-height'] = $m[1];
                continue;
            }

            // 6. Font Family
            switch ($baseCls) {
                case 'font-serif':
                    $styles['font-family'] = "'Lora', Georgia, serif";
                    break;
                case 'font-sans':
                    $styles['font-family'] = "'Instrument Sans', 'Montserrat', -apple-system, sans-serif";
                    break;
                case 'font-mono':
                    $styles['font-family'] = "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace";
                    break;
                case 'font-display':
                case 'font-playfair':
                    $styles['font-family'] = "'Playfair Display', Georgia, serif";
                    break;
                case 'font-alex':
                case 'font-cursive':
                case 'trekking-cursive':
                    $styles['font-family'] = "'Alex Brush', cursive, Georgia, serif";
                    break;
                case 'font-montserrat':
                    $styles['font-family'] = "'Montserrat', sans-serif";
                    break;
            }

            // 7. Font Weight
            switch ($baseCls) {
                case 'font-thin':       $styles['font-weight'] = '100'; break;
                case 'font-extralight': $styles['font-weight'] = '200'; break;
                case 'font-light':      $styles['font-weight'] = '300'; break;
                case 'font-normal':     $styles['font-weight'] = '400'; break;
                case 'font-medium':     $styles['font-weight'] = '500'; break;
                case 'font-semibold':   $styles['font-weight'] = '600'; break;
                case 'font-bold':       $styles['font-weight'] = '700'; break;
                case 'font-extrabold':  $styles['font-weight'] = '800'; break;
                case 'font-black':      $styles['font-weight'] = '900'; break;
            }

            // 8. Standard Font Sizes
            switch ($baseCls) {
                case 'text-xs':   $styles['font-size'] = '0.75rem'; break;
                case 'text-sm':   $styles['font-size'] = '0.875rem'; break;
                case 'text-base': $styles['font-size'] = '1rem'; break;
                case 'text-lg':   $styles['font-size'] = '1.125rem'; break;
                case 'text-xl':   $styles['font-size'] = '1.25rem'; break;
                case 'text-2xl':  $styles['font-size'] = '1.5rem'; break;
                case 'text-3xl':  $styles['font-size'] = '1.875rem'; break;
                case 'text-4xl':  $styles['font-size'] = '2.25rem'; break;
                case 'text-5xl':  $styles['font-size'] = '3rem'; break;
                case 'text-6xl':  $styles['font-size'] = '3.75rem'; break;
                case 'text-7xl':  $styles['font-size'] = '4.5rem'; break;
            }

            // 9. Text Colors (Standard Tailwind & Project Palettes)
            $colorMap = [
                'text-black'        => '#000000',
                'text-white'        => '#ffffff',
                'text-transparent'  => 'transparent',
                'text-current'      => 'currentColor',
                'text-gray-50'      => '#f9fafb',
                'text-gray-100'     => '#f3f4f6',
                'text-gray-200'     => '#e5e7eb',
                'text-gray-300'     => '#d1d5db',
                'text-gray-400'     => '#9ca3af',
                'text-gray-500'     => '#6b7280',
                'text-gray-600'     => '#4b5563',
                'text-gray-700'     => '#374151',
                'text-gray-800'     => '#1f2937',
                'text-gray-900'     => '#111827',
                'text-gray-950'     => '#030712',
                'text-red-500'      => '#ef4444',
                'text-red-600'      => '#dc2626',
                'text-amber-500'    => '#f59e0b',
                'text-amber-600'    => '#d97706',
                'text-yellow-500'   => '#eab308',
                'text-emerald-500'  => '#10b981',
                'text-emerald-600'  => '#059669',
                'text-emerald-700'  => '#047857',
                'text-green-600'    => '#16a34a',
                'text-green-700'    => '#15803d',
                'text-blue-500'     => '#3b82f6',
                'text-blue-600'     => '#2563eb',
                'text-gold'         => '#CA9A2E',
                'text-brand-primary'=> '#455537',
            ];
            if (isset($colorMap[$baseCls])) {
                $styles['color'] = $colorMap[$baseCls];
            }

            // 10. Letter Spacing (Tracking)
            switch ($baseCls) {
                case 'tracking-tighter': $styles['letter-spacing'] = '-0.05em'; break;
                case 'tracking-tight':   $styles['letter-spacing'] = '-0.025em'; break;
                case 'tracking-normal':  $styles['letter-spacing'] = '0em'; break;
                case 'tracking-wide':    $styles['letter-spacing'] = '0.025em'; break;
                case 'tracking-wider':   $styles['letter-spacing'] = '0.05em'; break;
                case 'tracking-widest':  $styles['letter-spacing'] = '0.1em'; break;
            }

            // 11. Text Transform & Style
            switch ($baseCls) {
                case 'uppercase':   $styles['text-transform'] = 'uppercase'; break;
                case 'lowercase':   $styles['text-transform'] = 'lowercase'; break;
                case 'capitalize':  $styles['text-transform'] = 'capitalize'; break;
                case 'normal-case': $styles['text-transform'] = 'none'; break;
                case 'italic':      $styles['font-style'] = 'italic'; break;
                case 'not-italic':  $styles['font-style'] = 'normal'; break;
                case 'text-left':   $styles['text-align'] = 'left'; break;
                case 'text-center': $styles['text-align'] = 'center'; break;
                case 'text-right':  $styles['text-align'] = 'right'; break;
            }

            // 12. Line Height (Leading)
            switch ($baseCls) {
                case 'leading-none':    $styles['line-height'] = '1'; break;
                case 'leading-tight':   $styles['line-height'] = '1.25'; break;
                case 'leading-snug':    $styles['line-height'] = '1.375'; break;
                case 'leading-normal':  $styles['line-height'] = '1.5'; break;
                case 'leading-relaxed': $styles['line-height'] = '1.625'; break;
                case 'leading-loose':   $styles['line-height'] = '2'; break;
            }
        }

        $res = [];
        foreach ($styles as $prop => $val) {
            $res[] = "{$prop}: {$val};";
        }

        return implode(' ', $res);
    }
}
