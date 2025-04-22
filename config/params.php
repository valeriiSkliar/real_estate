<?php

return [
    'bsVersion' => '5.x',
    'bot_token' => getenv('BOT_TOKEN'),
    'bot_link' => getenv('BOT_LINK'),
    'url' => getenv('URL'),
    'google_translation_api_key' => getenv('GOOGLE_TRANSLATION_API_KEY'),
    'deepl_translation_api_key' => getenv('DEEPL_TRANSLATION_API_KEY'),
    'deepl_free_translation_api_key' => getenv('DEEPL_FREE_TRANSLATION_API_KEY'),
    'translationOn' => true,
    'translator' => getenv('TRANSLATOR'), //'google','deepl' or 'deepl-free' translation
    'partner_oil_image_active' => getenv('PARTNER_OIL_IMAGE_ACTIVE'),
];
