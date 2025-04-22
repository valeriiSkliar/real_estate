<?php

namespace app\enums;

enum BotTextKeys: string
{
    use EnumHelperTrait;

    case INVITE_USER_DOES_NOT_EXIST = 'invite-user-does-not-exist';
    case NEED_TO_BUY_PREMIUM = 'need-to-buy-premium';
    case CHOOSE_PAYMENT_METHOD = 'choose-payment-method';
    case CLICK_TO_BUY = 'click-to-buy';
    case EMAIL_REQUEST = 'email-request';
    case INVITE_LINK = 'invite-link';
    case LANGUAGE_CHANGED = 'language-changed';
    case ENTER_PROMO_CODE = 'enter-promo-code';
    case NOT_FOUND_PROMO_CODE = 'not-found-promo-code';
    case ERROR_MESSAGE = 'error-message';
    case WITHDRAW_PROCESS = 'withdraw-process';
    case WITHDRAW_REQUEST = 'withdraw-request';
    case USER_NOT_FOUND = 'user-not-found';
    case MENU = 'menu';
    case CHOOSE_CITY = 'choose-city';
    case ENTER_NAME = 'enter-name';
    case ENTER_PHONE = 'enter-phone';
    case PHONE_UNIQUE_ERROR = 'phone_unique_error';
    case EMAIL_UNIQUE_ERROR = 'email_unique_error';
}