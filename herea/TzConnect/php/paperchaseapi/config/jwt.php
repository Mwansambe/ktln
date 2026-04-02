<?php

/*
|--------------------------------------------------------------------------
| JWT Configuration
|--------------------------------------------------------------------------
|
| This file contains the configuration for JWT token generation.
| You can customize these settings as needed.
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used to sign your JWT tokens. Generate one using:
    | php artisan jwt:secret
    |
    */
    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Time to Live (TTL)
    |--------------------------------------------------------------------------
    |
    | This determines how long a token will be valid in minutes.
    |
    */
    'ttl' => env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | JWT Refresh Token TTL
    |--------------------------------------------------------------------------
    |
    | This determines how long a refresh token will be valid in minutes.
    |
    */
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | This is the algorithm used to sign the token.
    | Supported: HS256, HS384, HS512, RS256, RS384, RS512
    |
    */
    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | JWT Required Claims
    |--------------------------------------------------------------------------
    |
    | These claims must be present in the token.
    |
    */
    'required_claims' => ['iss', 'iat', 'exp', 'nbf', 'sub', 'jti'],

    /*
    |--------------------------------------------------------------------------
    | JWT Persistent Claims
    |--------------------------------------------------------------------------
    |
    | These claims will be persisted in the refresh token.
    |
    */
    'persistent_claims' => [],

    /*
    |--------------------------------------------------------------------------
    | JWT Lock Subject
    |--------------------------------------------------------------------------
    |
    | This determines if the 'sub' claim should be locked to the user ID.
    |
    */
    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | JWT Leeway
    |--------------------------------------------------------------------------
    |
    | This option controls the JWT leeway in seconds. This allows you to
    | account for clock skew when validating token expiration times.
    |
    */
    'leeway' => env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | JWT Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | This determines if the blacklist is enabled. When enabled, invalidated
    | tokens will be added to the blacklist.
    |
    */
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | JWT Blacklist Grace Period
    |--------------------------------------------------------------------------
    |
    | The grace period in seconds. When a token is invalidated, it will
    | remain valid for this many seconds.
    |
    */
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | JWT Decrypt Key
    |--------------------------------------------------------------------------
    |
    | This is the key used to decrypt the token. This is only used when
    | the algorithm is set to something like RS256.
    |
    */
    'decrypt_key' => env('JWT_DECRYPT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | JWT Encrypt Key
    |--------------------------------------------------------------------------
    |
    | This is the key used to encrypt the token. This is only used when
    | the algorithm is set to something like RS256.
    |
    */
    'encrypt_key' => env('JWT_ENCRYPT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | JWT Provider
    |--------------------------------------------------------------------------
    |
    | This is the class that is used to handle the JWT token storage.
    |
    */
    'provider' => \PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci::class,

    /*
    |--------------------------------------------------------------------------
    | JWT Storage Provider
    |--------------------------------------------------------------------------
    |
    | This is the class that is used to handle the blacklist storage.
    |
    */
    'storage' => \PHPOpenSourceSaver\JWTAuth\Providers\Storage\Illuminate::class,
];

