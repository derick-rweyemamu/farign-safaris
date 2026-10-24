<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;

class PageContentController extends Controller
{
    /**
     * Return the GrapesJS builder data for the given model.
     */
    public function show(Request $request, $type, $id)
    {
        $permission = match ($type) {
            'post' => 'edit posts',
            'destination' => 'view destinations',
            default => 'edit pages',
        };

        if (!auth()->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        $locale = $request->query('locale', config('app.locale', 'en'));
        $model = $this->getModel($type, $id);

        $builderHtml = $model->getTranslation('builder_html', $locale, false);
        $builderData = $model->getTranslation('builder_data', $locale, false);

        $isEmptyHtml = function ($html) {
            if (empty($html))
                return true;
            $trimmed = trim($html);
            if (
                $trimmed === '' ||
                $trimmed === '<body></body>' ||
                $trimmed === '<style>* { box-sizing: border-box; } body {margin: 0;}</style><body></body>' ||
                $trimmed === '<style>* { box-sizing: border-box; } body {margin: 0;}</style>'
            ) {
                return true;
            }
            return false;
        };

        if ($isEmptyHtml($builderHtml)) {
            $builderHtml = $model->getTranslation('builder_html', 'en', false) ?: '';
        }
        if ($isEmptyHtml($builderHtml)) {
            $builderHtml = $model->getTranslation('content', $locale, false) ?: '';
        }
        if ($isEmptyHtml($builderHtml)) {
            $builderHtml = $model->getTranslation('content', 'en', false) ?: '';
        }
        if ($isEmptyHtml($builderHtml)) {
            $builderHtml = $model->builder_html ?? $model->content ?? '';
        }

        if ($isEmptyHtml($builderHtml)) {
            $title = $model->title ?? $model->name ?? 'Page Title';
            if (is_array($title)) {
                $title = $title[$locale] ?? $title['en'] ?? 'Page Title';
            }
            $builderHtml = "<style>* { box-sizing: border-box; } body {margin: 0;} .page-container { max-width: 1100px; margin: 0 auto; padding: 80px 20px; font-family: 'Instrument Sans', sans-serif; color: #2E1F12; } .page-subtitle { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: #A67C5B; margin-bottom: 12px; } .page-title { font-family: Lora, serif; font-size: 42px; font-weight: 600; color: #2E1F12; line-height: 1.2; margin-bottom: 24px; } .page-body { font-size: 16px; line-height: 1.8; color: #5D4E41; }</style><body><section class=\"page-container\"><div class=\"page-subtitle\"> </div><h1 class=\"page-title\">" . htmlspecialchars($title) . "</h1><div class=\"page-body\"><p>Welcome to our " . htmlspecialchars($title) . " page. Use the visual editor tools on the right to add text, images, columns, and interactive components.</p></div></section></body>";
        }

        if (empty($builderData)) {
            $builderData = $model->getTranslation('builder_data', 'en', false) ?: null;
        }

        return response()->json([
            'id' => $model->id,
            'title' => $model->title ?? $model->name,
            'builder_html' => $builderHtml,
            'builder_data' => is_string($builderData) ? json_decode($builderData, true) : ($builderData ?: null),
            'locale' => $locale,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Save the GrapesJS output (HTML + JSON) for the given model.
     */
    public function store(Request $request, $type, $id)
    {
        // 1. Authorization Check
        $permission = match ($type) {
            'post' => 'edit posts',
            'destination' => 'view destinations',
            default => 'edit pages',
        };

        if (!auth()->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Validate payload size to prevent database flooding
        $request->validate([
            'html_base64' => 'nullable|string|max:2000000',
            'data_base64' => 'nullable|string|max:2000000',
            'html' => 'nullable|string|max:2000000',
            'data' => 'nullable',
        ]);

        $locale = $request->query('locale', config('app.locale', 'en'));
        $model = $this->getModel($type, $id);

        if ($request->has('html_base64')) {
            $html = base64_decode($request->input('html_base64'));
        } else {
            $html = $request->input('html', '');
        }

        if ($request->has('data_base64')) {
            $data = base64_decode($request->input('data_base64'));
        } else {
            $data = json_encode($request->input('data', []));
        }

        // Normalize any hardcoded local development URLs (127.0.0.1 / localhost) to root-relative paths
        $cleanLocalUrls = function($str) {
            return preg_replace_callback('#https?://(?:127\.0\.0\.1|localhost)(?::\d+)?(/[^"\'\s<>\\]*)?#i', function($m) {
                return !empty($m[1]) ? $m[1] : '/';
            }, $str ?? '');
        };

        $html = $cleanLocalUrls($html);
        $data = $cleanLocalUrls($data);

        $model->setTranslation('builder_html', $locale, $html);
        $model->setTranslation('builder_data', $locale, $data);
        $model->save();

        // 2. Log Activity
        $titleOrName = $model->title ?? $model->name;
        \App\Models\ActivityLog::record(
            "{$type}.update_visual",
            "Updated visual content for {$type}: {$titleOrName}",
            ['module' => ucfirst($type)]
        );

        return response()->json(['success' => true, 'message' => ucfirst($type) . ' saved successfully.']);
    }

    private function getModel($type, $id)
    {
        return match ($type) {
            'post' => Post::findOrFail($id),
            'destination' => \App\Models\Destination::findOrFail($id),
            default => Page::findOrFail($id),
        };
    }
}
