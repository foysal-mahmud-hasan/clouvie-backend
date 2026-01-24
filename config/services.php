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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'zoho_mail' => [
        'account_id' => env('ZOHO_MAIL_ACCOUNT_ID'),
        'from' => env('ZOHO_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')),
        'token' => env('ZOHO_MAIL_TOKEN'),
        'base_url' => env('ZOHO_MAIL_BASE_URL', 'https://mail.zoho.com/api'),
        'timeout' => env('ZOHO_MAIL_TIMEOUT', 5),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
