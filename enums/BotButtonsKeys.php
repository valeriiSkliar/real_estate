<?php

namespace app\enums;

enum BotButtonsKeys: string
{
    use EnumHelperTrait;

    case LANGUAGE_PREFIX = 'language-';
    case TARIFF_PREFIX = 'tariff-id-';
    case CITY_PREFIX = 'city-id-';
    case TARIFF_TYPE_PREFIX = 'tariff-type-';
    case PAYMENT_METHOD_PREFIX = 'payment-method-';
    case BACK_TO_RULES = 'back-to-rules';
    case ACCEPT_RULES = 'accept-rules';
    case TARIFFS = 'tariffs';
    case FAQ = 'faq';
    case SUPPORT = 'support';
    case START = 'start';
    case ENTER_PROMO_CODE= 'enter-promo-code';
    case WITHDRAW = 'withdraw';
    case MY_REFERRALS = 'my-referrals';
    case CHOOSE_CITY = 'choose-city';
    case ENTER_NAME = 'enter-name';
    case ENTER_EMAIL = 'enter-email';
    case ENTER_PHONE = 'enter-phone';
    case ENTER_SUPPORT_MESSAGE = 'enter-support-message';
}