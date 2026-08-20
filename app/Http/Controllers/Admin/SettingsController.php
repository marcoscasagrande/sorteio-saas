<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = [
            'site_name' => Setting::get(Setting::SITE_NAME, 'SorteioSaaS'),
            'site_tagline' => Setting::get(Setting::SITE_TAGLINE, 'Sorteios no Instagram com transparência'),
            'site_logo_path' => Setting::get(Setting::SITE_LOGO_PATH),
            'seo_title' => Setting::get(Setting::SEO_TITLE, 'SorteioSaaS — Sorteie comentários do Instagram com auditoria pública'),
            'seo_description' => Setting::get(Setting::SEO_DESCRIPTION, 'Conecte sua conta do Instagram, cole o link do post e sorteie entre os comentários com total transparência. Grátis até 100 participantes.'),
            'mercadopago_access_token' => Setting::get(Setting::MP_ACCESS_TOKEN),
            'mercadopago_public_key' => Setting::get(Setting::MP_PUBLIC_KEY),
            'free_comment_limit' => Setting::get(Setting::FREE_COMMENT_LIMIT, '100'),
            'price_per_giveaway' => Setting::get(Setting::PRICE_PER_GIVEAWAY, '9.99'),
        ];

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:200'],
            'seo_title' => ['required', 'string', 'max:160'],
            'seo_description' => ['required', 'string', 'max:300'],
            'mercadopago_access_token' => ['nullable', 'string'],
            'mercadopago_public_key' => ['nullable', 'string'],
            'free_comment_limit' => ['required', 'integer', 'min:1'],
            'price_per_giveaway' => ['required', 'numeric', 'min:0'],
            'logo' => ['nullable', 'image', 'max:1024'], // até 1MB
        ]);

        Setting::setMany([
            Setting::SITE_NAME => $request->site_name,
            Setting::SITE_TAGLINE => $request->site_tagline,
            Setting::SEO_TITLE => $request->seo_title,
            Setting::SEO_DESCRIPTION => $request->seo_description,
            Setting::MP_ACCESS_TOKEN => $request->mercadopago_access_token,
            Setting::MP_PUBLIC_KEY => $request->mercadopago_public_key,
            Setting::FREE_COMMENT_LIMIT => $request->free_comment_limit,
            Setting::PRICE_PER_GIVEAWAY => number_format((float) $request->price_per_giveaway, 2, '.', ''),
        ]);

        if ($request->hasFile('logo')) {
            // Remove logo antiga, se existir
            $antiga = Setting::get(Setting::SITE_LOGO_PATH);
            if ($antiga) {
                Storage::disk('public')->delete($antiga);
            }

            $caminho = $request->file('logo')->store('logos', 'public');
            Setting::set(Setting::SITE_LOGO_PATH, $caminho);
        }

        return back()->with('sucesso', 'Configurações atualizadas com sucesso.');
    }
}
