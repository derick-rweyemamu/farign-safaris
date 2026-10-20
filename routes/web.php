<?php

use App\Livewire\Admin\ActivityLog\ActivityLogList;
use App\Livewire\Shared\Auth\Login;
use App\Livewire\Admin\Dashboard\Overview;
use App\Livewire\Admin\Roles\RolesList;
use App\Livewire\Admin\Settings\CompanySettings;
use App\Livewire\Admin\Settings\ProfileSettings;
use App\Livewire\Admin\Settings\CurrencyManagement;
use App\Livewire\Admin\Settings\EmailSettings;
use App\Livewire\Admin\Settings\TranslationManager;
use App\Livewire\Admin\CMS\PagesList;
use App\Livewire\Admin\CMS\PostsList;
use App\Livewire\Admin\CMS\NavigationMenuManager;
use App\Livewire\Admin\Countries\Country;
use App\Livewire\Admin\Cities\City;
use App\Livewire\Admin\Products\ProductList;
use App\Livewire\Admin\Products\ProductCategoryList;
use App\Livewire\Admin\Clients\ClientsList;
use App\Livewire\Admin\Users\UsersList;
use App\Livewire\Admin\Estimations\EstimationsList;
use App\Livewire\Admin\Invoices\InvoicesList;
use App\Livewire\Admin\Invoices\InvoiceExpensesList;
use App\Livewire\Admin\Financial\ExpensesList;
use App\Livewire\Admin\Financial\ExpenseTypesList;
use App\Livewire\Admin\Financial\ProfitLossReport;
use App\Livewire\Admin\Payments\PaymentsList;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ───────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register/client', \App\Livewire\Shared\Auth\ClientRegister::class)->name('register.client');
    Route::get('/2fa-challenge', \App\Livewire\Shared\Auth\TwoFactorChallenge::class)->name('2fa.challenge');
    Route::get('/2fa-setup', \App\Livewire\Shared\Auth\TwoFactorSetup::class)->name('2fa.setup');
    Route::get('/forgot-password', \App\Livewire\Shared\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', \App\Livewire\Shared\Auth\ResetPassword::class)->name('password.reset');
});

// ─── Logout ─────────────────────────────────────────────────────────────────
Route::match(['get', 'post'], '/logout', function () {
    if (request()->isMethod('get')) {
        return redirect()->route('login');
    }
    \App\Models\ActivityLog::record('auth.logout', 'User signed out');
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

// ─── Authenticated Routes ────────────────────────────────────────────────────

// ─── Client Portal ───────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/client/dashboard', \App\Livewire\Admin\Client\ClientDashboard::class)->name('client.dashboard');
    Route::get('/client/estimations', \App\Livewire\Admin\Client\ClientEstimations::class)->name('client.estimations');
    Route::get('/client/invoices', \App\Livewire\Admin\Client\ClientInvoices::class)->name('client.invoices');
    Route::get('/client/payments', \App\Livewire\Admin\Client\ClientPayments::class)->name('client.payments');
});

// ─── Admin Backend ───────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Direct file upload endpoint (bypasses Livewire upload-file to avoid WAF/ModSecurity 403)
    Route::post('/direct-upload', [\App\Http\Controllers\Api\FileUploadController::class, 'store'])->name('admin.direct-upload');

    // Dashboard
    Route::get('/dashboard', Overview::class)->name('dashboard');

    // CRM
    Route::get('/clients', ClientsList::class)->middleware('can:view clients')->name('clients.index');
    Route::get('/users', UsersList::class)->middleware('can:view users')->name('users.index');
    Route::get('/crm/newsletters', \App\Livewire\Admin\Newsletters\NewsletterManager::class)->middleware('can:view newsletters')->name('crm.newsletters');

    // Estimations
    Route::get('/estimations', EstimationsList::class)->middleware('can:view estimates')->name('estimations.index');

    // Roles & Permissions
    Route::get('/roles', RolesList::class)->middleware('can:view roles')->name('roles.index');

    // Activity Log
    Route::get('/activity-log', ActivityLogList::class)->middleware('can:view activities')->name('activity-log.index');

    // Settings
    Route::get('/settings/profile', ProfileSettings::class)->name('settings.profile');
    Route::get('/settings/company', CompanySettings::class)->middleware('can:view company settings')->name('settings.company');
    Route::get('/settings/currencies', CurrencyManagement::class)->middleware('can:view currencies setting')->name('settings.currencies');
    Route::get('/settings/email', EmailSettings::class)->middleware('can:view company settings')->name('settings.email');
    Route::get('/settings/translations', TranslationManager::class)->middleware('can:view settings')->name('settings.translations');

    // Countries
    Route::get('/countries', Country::class)->middleware('can:view countries')->name('countries.index');

    // Cities
    Route::get('/cities', City::class)->middleware('can:view cities')->name('cities.index');

    // Products
    Route::get('/products', ProductList::class)->middleware('can:view product')->name('products.index');
    Route::get('/products/categories', ProductCategoryList::class)->middleware('can:view product category')->name('product-categories.index');
    // Itineraries
    Route::get('/itineraries', \App\Livewire\Admin\Itineraries\ItineraryList::class)->middleware('can:view itineraries')->name('itineraries.index');
    Route::get('/itineraries/builder/{id?}', \App\Livewire\Admin\Itineraries\ItineraryBuilder::class)->middleware('can:view itineraries')->name('itineraries.builder');

    // Tours
    Route::prefix('tours')->name('admin.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Tours\TourList::class)->middleware('can:view product')->name('tours.index');
        Route::get('/categories', \App\Livewire\Admin\Tours\TourCategoryList::class)->middleware('can:view product')->name('tour-categories.index');
        Route::get('/routes', \App\Livewire\Admin\Tours\TourRouteList::class)->middleware('can:view product')->name('tours.routes.index');
        Route::get('/tags', \App\Livewire\Admin\Tours\TourTagList::class)->middleware('can:view product')->name('tour-tags.index');
        Route::get('/types', \App\Livewire\Admin\Tours\TourTypeList::class)->middleware('can:view product')->name('tour-types.index');
        Route::get('/builder/{id}', \App\Livewire\Admin\Tours\TourItineraryBuilder::class)->middleware('can:view product')->name('tours.builder');
        Route::get('/destinations', \App\Livewire\Admin\Destinations\DestinationList::class)->middleware('can:view destinations')->name('destinations.index');
        Route::get('/destinations/{destination_id}/attractions', \App\Livewire\Admin\Destinations\ManageAttractions::class)->middleware('can:edit destinations')->name('destinations.attractions');
        Route::get('/accommodations', \App\Livewire\Admin\Accommodations\AccommodationList::class)->middleware('can:view accommodations')->name('accommodations.index');
        Route::get('/activities', \App\Livewire\Admin\Activities\ActivityList::class)->middleware('can:view tour activities')->name('activities.index');
        Route::get('/transports', \App\Livewire\Admin\Transports\TransportList::class)->middleware('can:view transports')->name('transports.index');
        Route::get('/meal-plans', \App\Livewire\Admin\MealPlans\MealPlanList::class)->middleware('can:view meal plans')->name('meal-plans.index');
        Route::get('/animals', \App\Livewire\Admin\Tours\AnimalList::class)->middleware('can:view product')->name('tours.animals');
        Route::get('/cost-sheets', \App\Livewire\Admin\CostSheets\CostSheetList::class)->middleware('can:view cost sheets')->name('cost-sheets.index');
        Route::get('/cost-sheets/create', \App\Livewire\Admin\CostSheets\CostSheetForm::class)->middleware('can:create cost sheets')->name('cost-sheets.create');
        Route::get('/cost-sheets/{id}/edit', \App\Livewire\Admin\CostSheets\CostSheetForm::class)->middleware('can:edit cost sheets')->name('cost-sheets.edit');
        Route::get('/group-categories', \App\Livewire\Admin\GroupTours\GroupCategoryList::class)->middleware('can:view product')->name('group-categories.index');
        Route::get('/group-tours', \App\Livewire\Admin\GroupTours\GroupTourList::class)->middleware('can:view product')->name('group-tours.index');
    });

    // CRM / Leads
    Route::get('/crm/leads', \App\Livewire\Admin\Crm\LeadList::class)->middleware('can:view leads')->name('crm.leads');
    Route::get('/crm/leads/kanban', \App\Livewire\Admin\Crm\LeadKanban::class)->middleware('can:view leads')->name('crm.kanban');
    Route::get('/crm/leads/{id}', \App\Livewire\Admin\Crm\LeadDetail::class)->middleware('can:view leads')->name('crm.detail');

    // Invoices
    Route::get('/invoices', InvoicesList::class)->middleware('can:view invoices')->name('invoices.index');

    // Invoice Expenses
    Route::get('/invoice-expenses', InvoiceExpensesList::class)->name('invoice-expenses.index')->middleware('can:view invoice expenses');

    // General Expenses
    Route::get('/expenses', ExpensesList::class)->middleware('can:view expenses')->name('expenses.index');

    // Expense Types / Categories
    Route::get('/expense-types', ExpenseTypesList::class)->middleware('can:view expense type')->name('expense-types.index');

    // Profit & Loss Report
    Route::get('/profit-loss', ProfitLossReport::class)->middleware('can:view profit loss')->name('financial.profit-loss');

    // Banks
    Route::get('/settings/banks', \App\Livewire\Admin\Settings\BanksList::class)->middleware('can:view banks')->name('banks.index');

    // Payments
    Route::get('/payments', PaymentsList::class)->middleware('can:view payments')->name('payments.index');


    // Media Library
    Route::get('/media', \App\Livewire\Admin\Media\MediaManager::class)->name('admin.media.index');

    // CMS
    Route::get('/cms/pages', PagesList::class)->middleware('can:view pages')->name('cms.pages.index');
    Route::get('/cms/posts', PostsList::class)->middleware('can:view posts')->name('cms.posts.index');
    Route::get('/cms/post-categories', \App\Livewire\Admin\CMS\PostCategoryList::class)->middleware('can:view posts')->name('cms.post-categories.index');
    Route::get('/cms/navigation', NavigationMenuManager::class)->middleware('can:view pages')->name('cms.navigation.index');

    // Visual Editor (full-screen GrapesJS) - Support pages, posts, and destinations
    Route::get('/cms/{type}-editor/{id}', function ($type, $id, \App\Settings\GeneralSetting $settings) {
        if (!in_array($type, ['page', 'post', 'destination']))
            abort(404);

        $permission = match ($type) {
            'post' => 'edit posts',
            'destination' => 'view destinations',
            default => 'edit pages',
        };

        if (!auth()->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        $modelClass = match ($type) {
            'post' => \App\Models\Post::class,
            'destination' => \App\Models\Destination::class,
            default => \App\Models\Page::class,
        };
        $record = $modelClass::findOrFail($id);
        $tourCategories = \App\Models\TourCategory::where('is_active', true)->orderBy('sort_order', 'asc')->get(['name', 'slug']);
        $tourRoutes = \App\Models\TourRoute::where('is_active', true)->orderBy('name')->get();
        $groupCategories = \App\Models\GroupCategory::where('is_active', true)->get(['name', 'slug']);
        $tourTypes = \App\Models\TourType::where('is_active', true)->get(['name', 'slug']);
        $countries = \App\Models\Country::orderBy('name', 'asc')->get(['id', 'name']);
        $postCategories = \App\Models\PostCategory::where('is_active', true)->get(['name', 'slug']);
        $tourTags = \App\Models\TourTag::where('is_active', true)->orderBy('name', 'asc')->get(['name', 'slug']);
        $tourCountries = \App\Livewire\Frontend\ToursBlock::editorCountryData();
        $activities = \App\Models\Activity::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug', 'image', 'description']);
        $trekkingTours = \App\Models\Tour::where('type', 'like', '%trek%')
            ->orWhere('type', 'like', '%hik%')
            ->orWhere('type', 'like', '%climb%')
            ->orWhereHas('tourType', function ($tq) {
                $tq->where('slug', 'trekking')
                    ->orWhere('slug', 'hiking')
                    ->orWhere('name', 'like', '%trek%')
                    ->orWhere('name', 'like', '%hik%');
            })
            ->take(10)
            ->get(['id', 'name', 'slug', 'featured_image', 'duration_days', 'adult_price']);

        return view('livewire.admin.cms.page-editor', [
            'record' => $record,
            'type' => $type,
            'title' => $record->title,
            'tinymce_key' => env('TINYMCE_API_KEY', ''),
            'tourCategories' => $tourCategories,
            'tourRoutes' => $tourRoutes,
            'groupCategories' => $groupCategories,
            'tourTypes' => $tourTypes,
            'tourTags' => $tourTags,
            'countries' => $countries,
            'postCategories' => $postCategories,
            'tourCountries' => $tourCountries,
            'activities' => $activities,
            'trekkingTours' => $trekkingTours
        ]);
    })->name('visual-editor');

    Route::get('/api/{type}/{id}/content', [\App\Http\Controllers\Api\PageContentController::class, 'show'])->name('api.content.show');
    Route::post('/api/{type}/{id}/content', [\App\Http\Controllers\Api\PageContentController::class, 'store'])->name('api.content.store');

    // PDF Generation
    Route::get('/cms/pdf/invoice/{invoice}', [App\Http\Controllers\Billing\PdfController::class, 'invoice'])->name('cms.pdf.invoice');
    Route::get('/cms/pdf/estimate/{estimate}', [App\Http\Controllers\Billing\PdfController::class, 'estimate'])->name('cms.pdf.estimate');
    Route::get('/cms/pdf/receipt/{invoice}', [App\Http\Controllers\Billing\PdfController::class, 'receipt'])->name('cms.pdf.receipt');
    Route::get('/cms/pdf/itinerary/{itinerary}', [App\Http\Controllers\Billing\PdfController::class, 'itinerary'])->name('cms.pdf.itinerary');
    Route::get('/cms/pdf/profit-loss', [App\Http\Controllers\Billing\PdfController::class, 'profitLoss'])->name('cms.pdf.profit-loss');
    Route::get('/cms/excel/cost-sheet/{costSheet}', [App\Http\Controllers\Billing\ExcelController::class, 'costSheet'])->middleware('can:view cost sheets')->name('cms.excel.cost-sheet');
});

// Meta Lead Ads Webhook
Route::get('/webhooks/meta-leads', [\App\Http\Controllers\Api\MetaLeadWebhookController::class, 'verify'])->name('webhooks.meta-leads.verify');
Route::post('/webhooks/meta-leads', [\App\Http\Controllers\Api\MetaLeadWebhookController::class, 'handle'])->name('webhooks.meta-leads.handle');

// Frontend Routes
Route::get('/lang/{locale}', function ($locale) {
    if (array_key_exists($locale, config('locales.supported', []))) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

Route::post('/planning/submit', [\App\Http\Controllers\Frontend\PlanningController::class, 'submit'])->middleware('throttle:5,1')->name('planning.submit');
Route::post('/newsletter/subscribe', [\App\Http\Controllers\Frontend\NewsletterController::class, 'subscribe'])->middleware('throttle:3,1')->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\Frontend\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/tours', [\App\Http\Controllers\Frontend\TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{slug}', [\App\Http\Controllers\Frontend\TourController::class, 'show'])->name('tours.show');

// Destinations
Route::get('/destinations', [\App\Http\Controllers\Frontend\DestinationController::class, 'index'])->name('frontend.destinations.index');
Route::get('/destinations/{slug}', [\App\Http\Controllers\Frontend\DestinationController::class, 'show'])->name('frontend.destinations.show');

// Articles Routes
Route::get('/articles', [\App\Http\Controllers\Frontend\PostController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [\App\Http\Controllers\Frontend\PostController::class, 'show'])->name('articles.show');

// Landing Page & CMS Pages
Route::get('/', function () {
    $page = \App\Models\Page::where('slug', 'home')->first() ?? \App\Models\Page::first();
    if (!$page)
        abort(404);
    return view('frontend.page', compact('page'));
})->name('home');

Route::get('/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->firstOrFail();
    return view('frontend.page', compact('page'));
})->name('frontend.page.show');
