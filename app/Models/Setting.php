<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // Chaves conhecidas usadas pelo sistema — mantidas aqui como referência
    // única, pra evitar strings soltas espalhadas pelo código.
    const SITE_NAME = 'site_name';
    const SITE_TAGLINE = 'site_tagline';
    const SITE_LOGO_PATH = 'site_logo_path';
    const SEO_TITLE = 'seo_title';
    const SEO_DESCRIPTION = 'seo_description';
    const MP_ACCESS_TOKEN = 'mercadopago_access_token';
    const MP_PUBLIC_KEY = 'mercadopago_public_key';

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            static::set($key, $value);
        }
    }
}
