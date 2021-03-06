<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\Models\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'glome' => [
        'server' => env('GLOME_API_SERVER'),
        'key' => env('GLOME_API_KEY'),
        'uid' => env('GLOME_API_UID'),
    ],

    'twitter' => [
    'consumer_key'    => env('TWITTER_API_KEY'),
    'consumer_secret' => env('TWITTER_API_SECRET'),
    'access_token'    => env('TWITTER_ACCESS_TOKEN'),
    'access_secret'   => env('TWITTER_ACCESS_SECRET'),
  ],

];
