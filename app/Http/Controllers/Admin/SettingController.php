<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\SiteSettings;
use App\Support\SnippetSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(SiteSettings $siteSettings): Response
    {
        return Inertia::render('Admin/Settings/Edit', [
            'settings' => $siteSettings->current()->toArray(),
        ]);
    }

    public function update(Request $request, SiteSettings $siteSettings): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:1000'],
            'site_keywords' => ['nullable', 'string', 'max:1000'],
            'default_meta_title' => ['nullable', 'string', 'max:255'],
            'default_meta_description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif,svg', 'max:4096'],
            'favicon' => ['nullable', 'file', 'max:2048', 'mimes:ico,png,svg,webp'],
            'default_og_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:4096'],
            'custom_head_code' => ['nullable', 'string'],
            'custom_body_end_code' => ['nullable', 'string'],
            'maintenance_enabled' => ['boolean'],
            'maintenance_title' => ['nullable', 'string', 'max:255'],
            'maintenance_message' => ['nullable', 'string', 'max:1000'],
            'remove_logo' => ['boolean'],
            'remove_favicon' => ['boolean'],
            'remove_default_og_image' => ['boolean'],
        ]);

        $this->validateSnippets($validated);

        $settings = $siteSettings->current();
        $validated = $this->syncUploads($request, $settings, $validated);

        unset($validated['remove_logo'], $validated['remove_favicon'], $validated['remove_default_og_image']);

        $settings->update($validated);
        $siteSettings->forget();

        return back()->with('message', 'Genel ayarlar güncellendi.');
    }

    protected function validateSnippets(array $validated): void
    {
        $errors = [];

        if (! SnippetSanitizer::isValid($validated['custom_head_code'] ?? null, ['meta', 'link', 'script', 'style', 'noscript'])) {
            $errors['custom_head_code'] = 'Head kodlarında yalnızca izin verilen etiketler kullanılabilir.';
        }

        if (! SnippetSanitizer::isValid($validated['custom_body_end_code'] ?? null, ['script', 'noscript', 'iframe', 'div'])) {
            $errors['custom_body_end_code'] = 'Body sonu kodlarında yalnızca izin verilen etiketler kullanılabilir.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function syncUploads(Request $request, SiteSetting $settings, array $validated): array
    {
        foreach ([
            'logo' => 'site/logo',
            'favicon' => 'site/favicon',
            'default_og_image' => 'site/og',
        ] as $field => $directory) {
            if ($request->hasFile($field)) {
                if ($settings->{$field}) {
                    Storage::disk('public')->delete($settings->{$field});
                }

                $validated[$field] = $request->file($field)->store($directory, 'public');

                continue;
            }

            $removeField = 'remove_'.$field;

            if (($validated[$removeField] ?? false) && $settings->{$field}) {
                Storage::disk('public')->delete($settings->{$field});
                $validated[$field] = null;
            } else {
                unset($validated[$field]);
            }
        }

        return $validated;
    }
}
