<div class="max-w-7xl mx-auto">
    @php $companySetting = app(\App\Settings\GeneralSetting::class); @endphp
    @include('frontend.partials.group-tours-grid', ['groupTours' => $groupTours, 'companySetting' => $companySetting])
</div>
