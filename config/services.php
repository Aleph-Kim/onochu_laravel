<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'kakao' => [
        'client_id'          => env('KAKAO_CLIENT_ID'),
        'client_secret'      => env('KAKAO_CLIENT_SECRET'),
        'redirect_uri'       => env('KAKAO_REDIRECT_URI'),
        'share_template_id'  => env('KAKAO_SHARE_TEMPLATE_ID'),
        'share_secret'       => env('KAKAO_SHARE_SECRET'),
    ],

    'img_server' => [
        'username' => env('IMG_SERVER_USERNAME'),
        'secret'   => env('IMG_SERVER_SECRET'),
    ],

];
