<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Access Token de fallback do Mercado Pago — a chave cadastrada em
    // Admin > Configurações tem prioridade sobre esta (ver MercadoPagoService).
    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    ],

    // Credenciais do app da Meta usadas no OAuth "Instagram API with
    // Instagram Login" (ver InstagramOAuthController).
    'instagram' => [
        'client_id' => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
    ],

];
