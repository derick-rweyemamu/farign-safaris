<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDatabaseTranslationsCommand extends Command
{
    protected $signature = 'translations:update-db 
                            {--models : Also update database content models (NavigationMenu, Pages, TourCategories) with multilingual JSON} 
                            {--auto : Automatically translate English strings using Google Translate API endpoint}';

    protected $description = 'Synchronize and update database translations across all supported languages (EN, ES, DE, FR, IT, ZH)';

    protected array $locales = ['es', 'de', 'fr', 'it', 'zh'];

    // Curated high-precision dictionary for Tourism & Admin UI
    protected array $dictionary = [
        // Common Navigation & General
        'Home' => ['es' => 'Inicio', 'de' => 'Startseite', 'fr' => 'Accueil', 'it' => 'Home', 'zh' => '首页'],
        'About' => ['es' => 'Acerca de', 'de' => 'Über uns', 'fr' => 'À propos', 'it' => 'Chi siamo', 'zh' => '关于我们'],
        'About Us' => ['es' => 'Sobre nosotros', 'de' => 'Über uns', 'fr' => 'À propos de nous', 'it' => 'Chi siamo', 'zh' => '关于我们'],
        'Tours' => ['es' => 'Tours', 'de' => 'Touren', 'fr' => 'Circuits', 'it' => 'Tour', 'zh' => '旅游线路'],
        'Destinations' => ['es' => 'Destinos', 'de' => 'Reiseziele', 'fr' => 'Destinations', 'it' => 'Destinazioni', 'zh' => '目的地'],
        'Accommodations' => ['es' => 'Alojamientos', 'de' => 'Unterkünfte', 'fr' => 'Hébergements', 'it' => 'Alloggi', 'zh' => '住宿'],
        'Contact' => ['es' => 'Contacto', 'de' => 'Kontakt', 'fr' => 'Contact', 'it' => 'Contatti', 'zh' => '联系我们'],
        'Contact Us' => ['es' => 'Contáctenos', 'de' => 'Kontaktieren Sie uns', 'fr' => 'Contactez-nous', 'it' => 'Contattaci', 'zh' => '联系我们'],
        'Book Now' => ['es' => 'Reservar ahora', 'de' => 'Jetzt buchen', 'fr' => 'Réserver', 'it' => 'Prenota ora', 'zh' => '立即预订'],
        'Explore' => ['es' => 'Explorar', 'de' => 'Entdecken', 'fr' => 'Explorer', 'it' => 'Esplora', 'zh' => '探索'],

        // Admin & Form Controls
        'Save' => ['es' => 'Guardar', 'de' => 'Speichern', 'fr' => 'Enregistrer', 'it' => 'Salva', 'zh' => '保存'],
        'Cancel' => ['es' => 'Cancelar', 'de' => 'Abbrechen', 'fr' => 'Annuler', 'it' => 'Annulla', 'zh' => '取消'],
        'Edit' => ['es' => 'Editar', 'de' => 'Bearbeiten', 'fr' => 'Modifier', 'it' => 'Modifica', 'zh' => '编辑'],
        'Delete' => ['es' => 'Eliminar', 'de' => 'Löschen', 'fr' => 'Supprimer', 'it' => 'Elimina', 'zh' => '删除'],
        'Create' => ['es' => 'Crear', 'de' => 'Erstellen', 'fr' => 'Créer', 'it' => 'Crea', 'zh' => '创建'],
        'Add' => ['es' => 'Agregar', 'de' => 'Hinzufügen', 'fr' => 'Ajouter', 'it' => 'Aggiungi', 'zh' => '添加'],
        'Add Accommodation' => ['es' => 'Agregar Alojamiento', 'de' => 'Unterkunft hinzufügen', 'fr' => 'Ajouter un hébergement', 'it' => 'Aggiungi alloggio', 'zh' => '添加住宿'],
        'Actions' => ['es' => 'Acciones', 'de' => 'Aktionen', 'fr' => 'Actions', 'it' => 'Azioni', 'zh' => '操作'],
        'Status' => ['es' => 'Estado', 'de' => 'Status', 'fr' => 'Statut', 'it' => 'Stato', 'zh' => '状态'],
        'Active' => ['es' => 'Activo', 'de' => 'Aktiv', 'fr' => 'Actif', 'it' => 'Attivo', 'zh' => '激活'],
        'Inactive' => ['es' => 'Inactivo', 'de' => 'Inaktiv', 'fr' => 'Inactif', 'it' => 'Inattivo', 'zh' => '未激活'],
        'All Statuses' => ['es' => 'Todos los estados', 'de' => 'Alle Status', 'fr' => 'Tous les statuts', 'it' => 'Tutti gli stati', 'zh' => '所有状态'],
        'Search' => ['es' => 'Buscar', 'de' => 'Suchen', 'fr' => 'Rechercher', 'it' => 'Cerca', 'zh' => '搜索'],
        'Search...' => ['es' => 'Buscar...', 'de' => 'Suchen...', 'fr' => 'Rechercher...', 'it' => 'Cerca...', 'zh' => '搜索...'],
        'Reports' => ['es' => 'Informes', 'de' => 'Berichte', 'fr' => 'Rapports', 'it' => 'Report', 'zh' => '报告'],

        // Tourism & Booking
        'Duration' => ['es' => 'Duración', 'de' => 'Dauer', 'fr' => 'Durée', 'it' => 'Durata', 'zh' => '持续时间'],
        'Price' => ['es' => 'Precio', 'de' => 'Preis', 'fr' => 'Prix', 'it' => 'Prezzo', 'zh' => '价格'],
        'Total' => ['es' => 'Total', 'de' => 'Gesamt', 'fr' => 'Total', 'it' => 'Totale', 'zh' => '总计'],
        'Page' => ['es' => 'Página', 'de' => 'Seite', 'fr' => 'Page', 'it' => 'Pagina', 'zh' => '页'],
        'page' => ['es' => 'página', 'de' => 'Seite', 'fr' => 'page', 'it' => 'pagina', 'zh' => '页'],
        'Days' => ['es' => 'Días', 'de' => 'Tage', 'fr' => 'Jours', 'it' => 'Giorni', 'zh' => '天'],
        'Nights' => ['es' => 'Noches', 'de' => 'Nächte', 'fr' => 'Nuits', 'it' => 'Notti', 'zh' => '晚'],
        'Itinerary' => ['es' => 'Itinerario', 'de' => 'Reiseroute', 'fr' => 'Itinéraire', 'it' => 'Itinerario', 'zh' => '行程'],
        'Inclusions' => ['es' => 'Inclusiones', 'de' => 'Inklusivleistungen', 'fr' => 'Inclusions', 'it' => 'Inclusioni', 'zh' => '包含项目'],
        'Exclusions' => ['es' => 'Exclusiones', 'de' => 'Nicht enthalten', 'fr' => 'Exclusions', 'it' => 'Esclusioni', 'zh' => '不包含'],
        'View Details' => ['es' => 'Ver detalles', 'de' => 'Details ansehen', 'fr' => 'Voir les détails', 'it' => 'Vedi dettagli', 'zh' => '查看详情'],
        'View Tour' => ['es' => 'Ver tour', 'de' => 'Tour ansehen', 'fr' => 'Voir le circuit', 'it' => 'Vedi tour', 'zh' => '查看线路'],
        'Group Tours' => ['es' => 'Tours en grupo', 'de' => 'Gruppenreisen', 'fr' => 'Voyages en groupe', 'it' => 'Tour di gruppo', 'zh' => '跟团游'],
        'Private Tours' => ['es' => 'Tours privados', 'de' => 'Privattouren', 'fr' => 'Circuits privés', 'it' => 'Tour privati', 'zh' => '私家团'],
        'Kilimanjaro Climbing' => ['es' => 'Ascensión al Kilimanjaro', 'de' => 'Kilimandscharo-Besteigung', 'fr' => 'Ascension du Kilimandjaro', 'it' => 'Scalata del Kilimangiaro', 'zh' => '乞力马扎罗登山'],
        'Tanzania Safari' => ['es' => 'Safari en Tanzania', 'de' => 'Tansania-Safari', 'fr' => 'Safari en Tanzanie', 'it' => 'Safari in Tanzania', 'zh' => '坦桑尼亚游猎'],
        'Zanzibar Island' => ['es' => 'Isla de Zanzíbar', 'de' => 'Insel Sansibar', 'fr' => 'Île de Zanzibar', 'it' => 'Isola di Zanzibar', 'zh' => '桑给巴尔岛'],
        'All Tours' => ['es' => 'Todos los tours', 'de' => 'Alle Touren', 'fr' => 'Tous les circuits', 'it' => 'Tutti i tour', 'zh' => '全部旅游线路'],
        'Start Planning' => ['es' => 'Comenzar a planificar', 'de' => 'Planung beginnen', 'fr' => 'Commencer à planifier', 'it' => 'Inizia a pianificare', 'zh' => '开始定制行程'],
        'Dashboard' => ['es' => 'Panel', 'de' => 'Dashboard', 'fr' => 'Tableau de bord', 'it' => 'Dashboard', 'zh' => '控制台'],
        'Settings' => ['es' => 'Configuración', 'de' => 'Einstellungen', 'fr' => 'Paramètres', 'it' => 'Impostazioni', 'zh' => '设置'],
        'Translations' => ['es' => 'Traducciones', 'de' => 'Übersetzungen', 'fr' => 'Traductions', 'it' => 'Traduzioni', 'zh' => '翻译'],
        'Yes' => ['es' => 'Sí', 'de' => 'Ja', 'fr' => 'Oui', 'it' => 'Sì', 'zh' => '是'],
        'No' => ['es' => 'No', 'de' => 'Nein', 'fr' => 'Non', 'it' => 'No', 'zh' => '否'],
        'ENGLISH' => ['es' => 'INGLÉS', 'de' => 'ENGLISCH', 'fr' => 'ANGLAIS', 'it' => 'INGLESE', 'zh' => '英语'],
        'WHATSAPP' => ['es' => 'WHATSAPP', 'de' => 'WHATSAPP', 'fr' => 'WHATSAPP', 'it' => 'WHATSAPP', 'zh' => 'WHATSAPP'],
    ];

    public function handle()
    {
        $this->info("1. Synchronizing all English keys across all languages in database...");
        Translation::syncMissingKeys();

        $this->info("2. Updating UI translations in database from curated dictionary...");
        $updatedUI = 0;

        foreach ($this->dictionary as $enKey => $translations) {
            foreach ($translations as $locale => $translatedValue) {
                $affected = Translation::where('lang', $locale)
                    ->where(function ($q) use ($enKey) {
                        $q->where('lang_key', $enKey)
                          ->orWhere('lang_key', strtolower($enKey));
                    })
                    ->update(['lang_value' => $translatedValue]);

                $updatedUI += $affected;
            }
        }

        $this->info("Updated {$updatedUI} UI translation records from dictionary.");

        // Optional: Auto-translate remaining strings if --auto passed
        if ($this->option('auto')) {
            $this->info("3. Running automatic API translation for untranslated strings...");
            $this->autoTranslateMissing();
        }

        // Optional: Update Database Models if --models passed
        if ($this->option('models')) {
            $this->info("4. Updating database content modules (NavigationMenu, TourCategories, Pages) to multilingual JSON...");
            $this->updateDatabaseModels();
        }

        Translation::flushCache();
        $this->info("SUCCESS: Database translation sync and update completed!");
        return 0;
    }

    protected function autoTranslateMissing(): void
    {
        foreach ($this->locales as $targetLang) {
            $this->info("Translating missing strings for locale: {$targetLang}...");

            $records = Translation::where('lang', $targetLang)->get();
            $count = 0;

            foreach ($records as $record) {
                // If it equals the English key and hasn't been translated
                if ($record->lang_value === $record->lang_key && mb_strlen($record->lang_key) < 80) {
                    $translated = $this->translateText($record->lang_key, 'en', $targetLang);
                    if ($translated && $translated !== $record->lang_key) {
                        $record->lang_value = $translated;
                        $record->save();
                        $count++;
                    }
                    usleep(100000); // 100ms pause to prevent rate limiting
                }
            }

            $this->info(" -> Automatically translated {$count} strings for {$targetLang}.");
        }
    }

    protected function translateText(string $text, string $source, string $target): ?string
    {
        try {
            return app(\App\Services\GoogleTranslateService::class)->translate($text, $target, $source, false);
        } catch (\Throwable $e) {
            // Ignore API failures
        }
        return null;
    }

    protected function updateDatabaseModels(): void
    {
        // 1. Navigation Menus
        if (Schema::hasTable('navigation_menus')) {
            $menus = DB::table('navigation_menus')->get();
            foreach ($menus as $menu) {
                if (!str_starts_with($menu->label, '{')) {
                    $jsonLabel = json_encode([
                        'en' => $menu->label,
                        'es' => $this->dictionary[$menu->label]['es'] ?? $menu->label,
                        'de' => $this->dictionary[$menu->label]['de'] ?? $menu->label,
                        'fr' => $this->dictionary[$menu->label]['fr'] ?? $menu->label,
                        'it' => $this->dictionary[$menu->label]['it'] ?? $menu->label,
                        'zh' => $this->dictionary[$menu->label]['zh'] ?? $menu->label,
                    ], JSON_UNESCAPED_UNICODE);

                    DB::table('navigation_menus')->where('id', $menu->id)->update(['label' => $jsonLabel]);
                }
            }
            $this->info(" -> Updated NavigationMenu labels to multilingual JSON.");
        }

        // 2. Tour Categories
        if (Schema::hasTable('tour_categories')) {
            $categories = DB::table('tour_categories')->get();
            foreach ($categories as $cat) {
                if (!str_starts_with($cat->name, '{')) {
                    $jsonName = json_encode([
                        'en' => $cat->name,
                        'es' => $this->dictionary[$cat->name]['es'] ?? $cat->name,
                        'de' => $this->dictionary[$cat->name]['de'] ?? $cat->name,
                        'fr' => $this->dictionary[$cat->name]['fr'] ?? $cat->name,
                        'it' => $this->dictionary[$cat->name]['it'] ?? $cat->name,
                        'zh' => $this->dictionary[$cat->name]['zh'] ?? $cat->name,
                    ], JSON_UNESCAPED_UNICODE);

                    DB::table('tour_categories')->where('id', $cat->id)->update(['name' => $jsonName]);
                }
            }
            $this->info(" -> Updated TourCategories names to multilingual JSON.");
        }
    }
}
