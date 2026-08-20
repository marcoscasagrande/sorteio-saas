<?php

namespace App\Http\Controllers;

use App\Models\InstagramToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramOAuthController extends Controller
{
    public function redirect()
    {
        $params = http_build_query([
            'client_id' => config('services.instagram.client_id'),
            'redirect_uri' => route('instagram.callback'),
            'response_type' => 'code',
            'scope' => 'instagram_business_basic,instagram_business_manage_comments',
        ]);

        return redirect("https://www.instagram.com/oauth/authorize?{$params}");
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('dashboard')
                ->with('erro', 'Você recusou a conexão com o Instagram.');
        }

        // Troca o code por um token de curta duração
        $shortLived = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
            'client_id' => config('services.instagram.client_id'),
            'client_secret' => config('services.instagram.client_secret'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('instagram.callback'),
            'code' => $request->code,
        ])->json();

        // Troca pelo token de longa duração (~60 dias)
        $longLived = Http::get('https://graph.instagram.com/access_token', [
            'grant_type' => 'ig_exchange_token',
            'client_secret' => config('services.instagram.client_secret'),
            'access_token' => $shortLived['access_token'],
        ])->json();

        InstagramToken::updateOrCreate(
            ['user_id' => $request->user()->id, 'instagram_user_id' => $shortLived['user_id']],
            [
                'access_token' => $longLived['access_token'],
                'expires_at' => now()->addSeconds($longLived['expires_in'] ?? 5184000),
            ]
        );

        return redirect()->route('dashboard')->with('sucesso', 'Instagram conectado com sucesso!');
    }
}
