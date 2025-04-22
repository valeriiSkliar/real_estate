<?php

/**
 * URL Rules Configuration
 * 
 * This file contains all URL routing rules for the application.
 * Each rule maps a URL pattern to a controller/action.
 */

return [
    // Site routes
    '/' => 'frontend/site/index',
    'test' => 'frontend/site/test',
    'error' => 'frontend/site/error',
    'favorites' => 'frontend/favorites/index',
    'favorites/add' => 'frontend/favorites/add',
    'favorites/remove' => 'frontend/favorites/remove',

    // my properties routes
    'my-properties' => 'frontend/my-properties/index',
    'my-properties/create' => 'frontend/my-properties/create',
    'my-properties/view/<id:\d+>' => 'frontend/my-properties/view',
    'my-properties/update/<id:\d+>' => 'frontend/my-properties/update',
    'my-properties/delete/<id:\d+>' => 'frontend/my-properties/delete',

    // AJAX routes for sidebar filters
    'site/get-district-selector' => 'frontend/site/get-district-selector',
    'site/get-complex-search' => 'frontend/site/get-complex-search',
    'site/search-complexes' => 'frontend/site/search-complexes',

    //Telegram webhook routes
    'telegram/process' => 'webhook/telegram/process',

    // Authentication admin routes
    'admin/login' => 'admin/site/login',
    'admin/logout' => 'admin/site/logout',
    'admin/site/logout' => 'admin/site/logout',

    // Admin routes
    'admin' => 'admin/site/index',
    'image-tiny-uploader/upload' => 'admin/image-tiny-uploader/upload',

    // Payment routes
    'payment/meleton' => 'webhook/payment/meleton',
    'payment/lava' => 'webhook/payment/lava',
    'payment/paypal' => 'webhook/payment/paypal',
    'payment/stripe' => 'webhook/payment/stripe',
];
