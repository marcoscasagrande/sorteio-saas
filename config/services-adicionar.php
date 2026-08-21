<?php

// Adicione estas duas chaves dentro do array retornado por config/services.php
// (que já existe no seu projeto Laravel).

return [

    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    ],

    'instagram' => [
        'client_id' => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
    ],

];
