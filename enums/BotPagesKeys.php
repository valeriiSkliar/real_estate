<?php

namespace app\enums;

enum BotPagesKeys: string
{
    use EnumHelperTrait;

    case MAIN_PAGE = 'main_page';
    case FAQ = 'faq';
    case AFTER_PAYMENT = 'after-payment';
    case PARTNERSHIP = 'partnership';
    case RULES = 'rules';
    case SUPPORT = 'support';
    case SUPPORT_MESSAGE = 'support-message';
    case CHECK_SUBSCRIPTION = 'check-subscription';
    case WITHDRAW = 'withdraw';
    case ACCOUNT = 'account';
    case PRE_START = 'pre-start';
    case WELCOME = 'welcome';
}