<?php

namespace app\components\telegram\handlers;

use app\components\services\bot\ButtonService;
use app\components\services\bot\TelegramService;
use app\components\services\PaymentProviderService;
use app\enums\BotButtonsKeys;
use app\enums\BotPagesKeys;
use app\enums\BotTextKeys;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Buttons;
use app\models\Cities;
use app\models\dto\TelegramUserDto;
use app\models\Languages;
use app\models\Pages;
use app\models\Payouts;
use app\models\Promocodes;
use app\models\Referrals;
use app\models\SupportMessages;
use app\models\Tariffs;
use app\models\Texts;
use Exception;
use JsonException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Throwable;
use Yii;
use yii\helpers\Inflector;

class TelegramFlowHandler
{
    private ?BotUsers $user;
    private TelegramUserDto $telegramUserDto;

    public function __construct(
        private readonly ButtonService $buttonService,
        private readonly TelegramService $telegramService,
    ) {
        $this->telegramUserDto = $this->telegramService->getTelegramUserDto();
        $this->user = $this->telegramService->getUserFromDb($this->telegramUserDto->getUserId());
        Yii::$app->language = $this->telegramUserDto->getLanguage();
    }

    public function handle(): void
    {
        try {
            if ($this->user) {
                Yii::$app->language = $this->user->language ?? $this->telegramUserDto->getLanguage();
                $this->user->setNewLastVisitedDate();
                $this->updateUsername();
            }

            $this->checkAuth($this->telegramUserDto->getText());

            return;
        } catch (Exception $e) {
            ErrorLogHelper::logBotInfo($e->getMessage(). $e->getTraceAsString(), 'telegram-error');

            if ($this->telegramUserDto->getUserId()){
                ErrorLogHelper::logBotInfo("user id = {$this->telegramUserDto->getUserId()}");
            }
        }
    }

    /**
     * Проверяем есть ли пользователь в базе c таким telegram ID,
     * если есть, то отправляем на проверку команды, которую он прислал.
     * Если же нет, то проверяем на наличие инвайт-ссылки и сохраняем в базу.
     */
    private function checkAuth($text): void
    {
        // Получаем параметры из строки
        $params = str_contains($text,'start')
            ? $this->telegramService->getParametersFromString($text)
            : null;

        if (!$params){
            $this->defaultFlow($text);

            return;
        }

        $this->parametrizedFlow($params, $text);

        return;
    }
    /**
     * Путь для пользователя без специальных параметров в строке запроса
     */
    private function defaultFlow($text): void
    {
        try {
            if ($this->user) {
                $this->router($text);

                return;
            }

            $this->newGuestFlow();
            $this->start();

            return;
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo($e->getMessage(). $e->getTraceAsString(), 'Telegram defaultFlow error: ');
        }
    }

    /**
     * Путь для пользователя со специальными параметрами в строке запроса
     */
    private function parametrizedFlow($params, $text): void
    {
        try {
            $id = $params['id'] ?? null;
            $param = $params['param'] ?? null;

            if ($id && $this->handleInvite($id)) {
                return;
            }

            if ($this->user) {
                $this->router($text);
            }
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo($e->getMessage() . $e->getTraceAsString(), 'Telegram parametrizedFlow error: ');
        }
    }

    /**
     * @throws Throwable
     */
    private function handleInvite(?int $id): bool
    {
        return $this->checkInvite($id);
    }

    /**
     * @param $text
     * Маршрутизатор переключает на необходимый обработчик команд
     *
     * @return void
     * @throws TelegramSDKException|JsonException
     * @throws Exception
     * @throws Throwable
     */
    private function router($text): void
    {
        //События после определенной кнопки
        if ($this->buttonService->getLastButton($this->telegramUserDto->getUserId())) {
            $this->lastButtonHandler($text);

            return;
        }

        //Ответ от кнопки (inline-keyboard)
        if ($this->telegramUserDto->getData()) {
            $this->callbackHandler($this->telegramUserDto->getData());

            return;
        }

        //События запускаются если кнопка в базе
        $button = $this->buttonService->searchButton($text);

        if ($button) {
            $response = $this->callMethod($button->slug);

            if (!$response) {
                $this->telegramService->sendPage($button->slug, true);

                return;
            }
        }

        //События запускаются если пришла команда(типа /start, /help)
        if (str_starts_with($text, '/')) {
            $command = str_replace('/', '', $text);

            $response = $this->callMethod($command);

            if ($response){
                return;
            }

            $this->telegramService->sendPage($command, true);

            return;
        }
    }

    /**
     * @param $name
     * Вызываем функцию контроллера
     *
     * @return bool
     */
    private function callMethod($name): bool
    {
        $methodName = Inflector::camelize($name);

        if (method_exists($this, $methodName)) {
            call_user_func([$this, $methodName]);

            return true;
        }

        return false;
    }

    /**
     * @param $inviteId
     * Проверяем инвайт и сохраняем, либо отказываем в виде сообщения.
     *
     * @return bool
     * @throws Throwable
     */
    private function checkInvite($inviteId): bool
    {
        try {
            if ($inviteId && is_numeric($inviteId)) {
                // Проверяем есть ли у нас пользователь с айди как в инвайте
                $referralParent = $this->telegramService->getUserFromDb($inviteId);

                if ($referralParent) {
                    $user = $this->user ?? $this->saveNewUser();

                    if ($user) {
                        if (!$user->linkReferral($referralParent)){
                            ErrorLogHelper::logBotInfo('Ошибка добавление реферала через бот, пользователь: '. $user->id . ' консультант: '. $referralParent->id);
                        }
                    }

                    $this->start();

                    return true;
                } else {
                    $textResponse = Texts::getLabel(BotTextKeys::INVITE_USER_DOES_NOT_EXIST->value);
                    $this->telegramService->sendMessageByMarkup($textResponse);

                    return false;
                }
            } else {
                if (!$this->user) {
                    $this->newGuestFlow();
                }

                $this->start();

                return true;
            }
        } catch (Exception $e) {
            ErrorLogHelper::logBotInfo($e->getMessage(). $e->getTraceAsString(), 'Ошибка при проверке инвайта ' . $inviteId);

            return false;
        }
    }

    /**
     * @throws Throwable
     */
    private function newGuestFlow(): void
    {
        $this->saveNewUser();
    }

    /**
     * @param $text
     * Ответ от кнопки (inline-keyboard)
     *
     * @return void
     * @throws TelegramSDKException
     * @throws Exception
     * @throws Throwable
     */
    private function callbackHandler($text): void
    {
        //Логируем клик по кнопки, за исключением чата
        $this->telegramService->incrementButtonClickCount($text);

        if (str_contains($text, BotButtonsKeys::LANGUAGE_PREFIX->value)){
            $this->setLanguage($text);

            return;
        }

        if (str_contains($text, BotButtonsKeys::TARIFF_PREFIX->value)){
            $tariffId = str_replace(BotButtonsKeys::TARIFF_PREFIX->value, '',$text);
            $this->sendPaymentUrl($tariffId);

            return;
        }

        if (str_contains($text, BotButtonsKeys::TARIFF_TYPE_PREFIX->value)){
            $tariffTypeId = str_replace(BotButtonsKeys::TARIFF_TYPE_PREFIX->value, '',$text);
            $this->choosePaymentMethod($tariffTypeId);

            return;
        }

        if (str_contains($text, BotButtonsKeys::CITY_PREFIX->value)){
            $cityId = str_replace(BotButtonsKeys::CITY_PREFIX->value, '',$text);
            $this->saveCity($cityId);

            return;
        }

        if ($text == BotButtonsKeys::BACK_TO_RULES->value || $text == BotButtonsKeys::ACCEPT_RULES->value) {
            $this->afterPayment();

            return;
        }

        $response = $this->callMethod($text);

        if (!$response) {
            $this->telegramService->sendPage($text, true);
        }

        return;
    }

    /**
     * @param $text
     *  Действия после нажатия кнопки
     *
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    private function lastButtonHandler($text): void
    {
        $lastButton = $this->buttonService->getLastButton($this->telegramUserDto->getUserId());

        if (!$lastButton) {
            return;
        }

        if ($lastButton == BotButtonsKeys::ENTER_PROMO_CODE->value) {
            $this->usePromoCode($text);

            return;
        }

        if (str_contains($lastButton, BotButtonsKeys::TARIFF_TYPE_PREFIX->value)){
            $this->chooseTariff(
                str_replace(BotButtonsKeys::TARIFF_TYPE_PREFIX->value, '', $lastButton),
                str_replace(BotButtonsKeys::PAYMENT_METHOD_PREFIX->value, '', $this->telegramUserDto->getData())
            );

            return;
        }

        if ($lastButton == BotButtonsKeys::ENTER_NAME->value) {
            $this->saveName($text);

            return;
        }
        if ($lastButton == BotButtonsKeys::ENTER_PHONE->value) {
            $this->savePhone($text);

            return;
        }

        if (str_contains($lastButton, BotButtonsKeys::ENTER_EMAIL->value)){
            $this->saveEmail($text);

            return;
        }

        if ($lastButton === BotButtonsKeys::ENTER_SUPPORT_MESSAGE->value){
            $this->saveSupportMessage($text);

            return;
        }

        return;
    }

    /**
     * @return void
     * Начальный экран с кнопками
     * @throws TelegramSDKException|JsonException
     * @throws Throwable
     */
    private function start(): void
    {
        if (!$this->user?->isRegistrationFinished()) {
            $this->preStart();

            return;
        }

        $this->telegramService->sendPage(BotPagesKeys::MAIN_PAGE->value, true);

        return;
    }

    /**
     * @throws Throwable
     * @throws TelegramSDKException
     * @throws JsonException
     */
    private function preStart(): void
    {
        if ($this->user->city_id) {
            $this->enterName();

            return;
        }

        $buttons = $this->buttonService->getInlineButtons(null, [
            BotButtonsKeys::CHOOSE_CITY->value,
        ]);

        $this->telegramService->sendPage(
            BotPagesKeys::PRE_START->value,
            false,
            $buttons,
        );

        return;
    }

    /**
     * @throws Throwable
     * @throws TelegramSDKException
     * @throws JsonException
     */
    private function chooseCity(): void
    {
        $textResponse = Texts::getLabel(BotTextKeys::CHOOSE_CITY->value);
        $buttons = $this->buttonService->getCityButtons(2);

        if ($buttons) {
            $this->telegramService->sendMessageWithInlineKeyboardByMarkup($textResponse, $buttons);
        }

        return;
    }

    /**
     * @throws Throwable
     */
    private function saveCity(int $cityId): void
    {
        $city = Cities::findOne($cityId);

        if (!$city) {
            $this->telegramService->sendErrorMessage();

            return;
        }

        $this->user->updateAttributes(['city_id' => $cityId]);

        $this->enterName();

        return;
    }

    /**
     * @throws Throwable
     * @throws TelegramSDKException
     * @throws JsonException
     */
    private function enterName(): void
    {
        if ($this->user->fio) {
            $this->sendEmailRequest();

            return;
        }

        $textResponse = Texts::getLabel(BotTextKeys::ENTER_NAME->value);
        $this->buttonService->setLastButton(BotTextKeys::ENTER_NAME->value, $this->telegramUserDto->getUserId());
        
        $this->telegramService->sendMessageByMarkup($textResponse);

        return;
    }

    /**
     * @throws Throwable
     */
    private function saveName(string $name): void
    {
        $this->buttonService->clearLastButton($this->telegramUserDto->getUserId());
        
        if (!$name) {
            $this->telegramService->sendErrorMessage();

            return;
        }

        $this->user->updateAttributes(['fio' => $name]);

        $this->sendEmailRequest();

        return;
    }

    /**
     * @throws TelegramSDKException
     * @throws Throwable
     */
    private function sendEmailRequest(): void
    {
        if ($this->user->email) {
            $this->enterPhone();

            return;
        }

        $textResponse = Texts::getLabel(BotTextKeys::EMAIL_REQUEST->value);

        $this->buttonService->setLastButton(
            BotButtonsKeys::ENTER_EMAIL->value,
            $this->telegramUserDto->getUserId()
        );

        $this->telegramService->sendMessageByMarkup($textResponse);


        return;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function saveEmail(string $email): void
    {
        $this->buttonService->clearLastButton($this->telegramUserDto->getUserId());

        if (BotUsers::findOne(['email' => $email]) !== null || !$this->user->saveEmail($email)) {
            $textResponse = Texts::getLabel(BotTextKeys::EMAIL_UNIQUE_ERROR->value);

            $this->telegramService->sendMessageByMarkup($textResponse);

            $this->sendEmailRequest();

            return;
        }

        $this->enterPhone();

        return;
    }

    /**
     * @throws Throwable
     * @throws TelegramSDKException
     */
    private function enterPhone(): void
    {
        if ($this->user->phone) {
            $this->start();

            return;
        }

        $textResponse = Texts::getLabel(BotTextKeys::ENTER_PHONE->value);
        
        $this->buttonService->setLastButton(BotButtonsKeys::ENTER_PHONE->value,$this->telegramUserDto->getUserId());
        
        $this->telegramService->sendMessageByMarkup($textResponse);

        return;
    }

    /**
     * @throws Throwable
     */
    private function savePhone(string $phone): void
    {
        $this->buttonService->clearLastButton($this->telegramUserDto->getUserId());
        
        if (!$phone) {
            $this->telegramService->sendErrorMessage();

            return;
        }

        if (BotUsers::findOne(['phone' => $phone]) !== null || !$this->user->savePhone($phone)) {
            $textResponse = Texts::getLabel(BotTextKeys::PHONE_UNIQUE_ERROR->value);

            $this->telegramService->sendMessageByMarkup($textResponse);

            $this->enterPhone();

            return;
        }

        $this->start();

        return;
    }

    /**
     * Выводит список тарифов кнопками
     * @throws Throwable
     * @throws TelegramSDKException
     * @throws JsonException
     */
    private function tariffs(): void
    {
        $textResponse = Texts::getLabel(BotTextKeys::NEED_TO_BUY_PREMIUM->value);
        $buttons = $this->buttonService->getInlineButtons(Buttons::POSITION_TARIFFS);

        if ($buttons) {
            $this->telegramService->sendMessageWithInlineKeyboardByMarkup($textResponse, $buttons);
        }

        return;
    }

    /**
     * Выводит страницу faq
     *
     * @throws TelegramSDKException
     * @throws Throwable
     * @throws JsonException
     */
    private function faq(): void
    {
        $buttons = $this->buttonService->getInlineButtons(null, [
            BotButtonsKeys::TARIFFS->value,
            BotButtonsKeys::SUPPORT->value,
            BotButtonsKeys::START->value,
        ]);

        $this->telegramService->sendPage(
            BotPagesKeys::FAQ->value,
            false,
            $buttons,
        );

        return;
    }
    /**
     * Выводит страницу support
     *
     * @throws TelegramSDKException
     * @throws Throwable
     * @throws JsonException
     */
    private function support(): void
    {
        $this->buttonService->setLastButton(BotButtonsKeys::ENTER_SUPPORT_MESSAGE->value, $this->telegramUserDto->getUserId());

        $this->telegramService->sendPage(BotPagesKeys::SUPPORT->value, false);

        return;
    }

    /**
     * @throws Throwable
     */
    private function saveSupportMessage(string $text): void
    {
        $this->buttonService->clearLastButton($this->telegramUserDto->getUserId());

        if (!$text) {
            $this->telegramService->sendErrorMessage();

            return;
        }

        SupportMessages::saveMessage($text, $this->user->id);

        $this->telegramService->sendPage(
            BotPagesKeys::SUPPORT_MESSAGE->value,
            false,
        );

        return;
    }


    /**
     * Выводит страницу партнерского кабинета
     *
     * @throws TelegramSDKException
     * @throws Throwable
     * @throws JsonException
     */
    private function partnership(): void
    {
        $buttons = $this->buttonService->getInlineButtons(Buttons::POSITION_REFERRAL);
        $startButton  = $this->buttonService->getInlineButtons(null, [BotButtonsKeys::START->value]);

        $this->telegramService->sendPage(
            BotPagesKeys::PARTNERSHIP->value,
            false,
            array_merge($buttons, $startButton),
        );

        return;
    }

    /**
     * Выводит страницу после оплаты
     *
     * @throws TelegramSDKException
     * @throws Throwable
     * @throws JsonException
     */
    private function afterPayment(): void
    {
        $buttons = $this->buttonService->getInlineButtons(Buttons::POSITION_PAYMENT);
        $supportButton  = $this->buttonService->getInlineButtons(null, [BotButtonsKeys::SUPPORT->value]);

        $this->telegramService->sendPage(
            BotPagesKeys::AFTER_PAYMENT->value,
            false,
            array_merge($buttons, $supportButton),
        );

        return;
    }

    /**
     * Выводит страницу правил
     *
     * @throws TelegramSDKException
     * @throws Throwable
     * @throws JsonException
     */
    private function rules(): void
    {
        $buttons = $this->buttonService->getInlineButtons(null, [BotButtonsKeys::ACCEPT_RULES->value]);

        $this->telegramService->sendPage(
            BotPagesKeys::RULES->value,
            false,
            $buttons,
        );

        return;
    }

    /**
     * Выводит страницу проверки подписки
     *
     * @throws TelegramSDKException
     * @throws Throwable
     * @throws JsonException
     */
    private function checkSubscription(): void
    {
        $page = Pages::findOne([
            'command' => BotPagesKeys::CHECK_SUBSCRIPTION->value,
            'language' => Yii::$app->language
        ]);

        if ($page) {
            $page->h1 = str_replace('{expireDate}', $this->user->paid_until, $page->h1);
            $page->text = str_replace('{expireDate}', $this->user->paid_until, $page->text);
        }


        $this->telegramService->sendPage(
            command: BotPagesKeys::CHECK_SUBSCRIPTION->value,
            page: $page,
        );

        return;
    }

    /**
     * @param $language
     *
     * @return void
     * @throws TelegramSDKException
     * @throws JsonException|Throwable
     */
    private function setLanguage($language): void
    {
        $languageModel = Languages::findOne([
            'slug' => str_replace(BotButtonsKeys::LANGUAGE_PREFIX->value, '', $language),
            'is_active' => Languages::STATUS_ACTIVE,
        ]);

        if ($languageModel) {
            $this->setLanguageParameters($languageModel->slug);
        }

        $text = Texts::findOne([
            'slug' => BotTextKeys::LANGUAGE_CHANGED->value,
            'language' => $languageModel?->slug ?? 'en'
        ]);

        $message = $text?->name ?? "Language changed";

        $this->telegramService->sendMessageByMarkup($message);
        $this->telegramService->sendMenu();

        return;
    }

    private function setLanguageParameters($language): void
    {
        $this->user->updateAttributes(['language' => $language]);
        Yii::$app->language = $language;
        $this->telegramService->labels = Texts::getLabels($language);

        return;
    }

    /**
     * @throws Exception|Throwable
     */
    private function sendPaymentUrl(int $tariffId): void
    {
        $this->buttonService->clearLastButton($this->telegramUserDto->getUserId());

        $tariff = Tariffs::findOne($tariffId);

        $textResponse = Texts::getLabel(BotTextKeys::CLICK_TO_BUY->value);

        if ($tariff) {
            $cacheKey = Promocodes::buildCacheKey($this->telegramUserDto->getUserId());

            $promoCode = $this->buttonService->getCache($cacheKey);

            $link =  (new PaymentProviderService())->getPaymentUrl($this->user, $tariff, $promoCode);

            $textResponse = str_replace('{name}', $tariff->name, $textResponse);
            $textResponse .= "\n\n <a href='{$link}'>{$tariff->name}</a>";

            $this->buttonService->deleteCache($cacheKey);
        }

        $this->telegramService->sendMessageByMarkup($textResponse);

        return;
    }

    /**
     * @throws Throwable
     * @throws TelegramSDKException
     * @throws JsonException
     */
    private function choosePaymentMethod(int $tariffTypeId): void
    {
        $this->buttonService->setLastButton(
            BotButtonsKeys::TARIFF_TYPE_PREFIX->value . $tariffTypeId,
            $this->telegramUserDto->getUserId()
        );

        $textResponse = Texts::getLabel(BotTextKeys::CHOOSE_PAYMENT_METHOD->value);
        $buttons = $this->buttonService->getPaymentMethodButtons();;

        if ($buttons) {
            $this->telegramService->sendMessageWithInlineKeyboardByMarkup($textResponse, $buttons);
        }

        return;
    }

    /**
     * @throws Throwable
     */
    private function chooseTariff(int $tariffType, string $paymentMethod): void
    {
        $this->buttonService->clearLastButton($this->telegramUserDto->getUserId());

        $query = Tariffs::find()
            ->where([
                'type' => $tariffType,
                'provider' => $paymentMethod,
            ]);

        $cacheKey = Promocodes::buildCacheKey($this->telegramUserDto->getUserId());
        $promoCode = $this->buttonService->getCache($cacheKey);
        $promoCodeModel = $promoCode ? Promocodes::findOne(['code' => $promoCode]) : null;
        $promoCodeModel ? $query->andWhere(['>', 'discount', 0]) : $query->andWhere(['<=', 'discount', 0]);

        /** @var  Tariffs  $tariff */
        $tariff = $query->one();

        if ($tariff) {
            $this->sendPaymentUrl($tariff->id);
        }

        return;
    }

    /**
     * @return void
     * Отсылает инвайт ссылку
     * @throws TelegramSDKException
     * @throws Throwable
     */
    private function share(): void
    {
        $textResponse = Texts::getLabel(BotTextKeys::INVITE_LINK->value);
        $inviteData = $this->user->generateQr();
        $textUrl = $inviteData['url'];
        $qrUrl = $inviteData['fileUrl'];

        if ($textUrl){
            $this->telegramService->sendMessageByMarkup($textResponse);
        }

        $this->telegramService->sendPhoto($qrUrl, false);

        $this->telegramService->sendMenu($textUrl);

        return;
    }

    /**
     * @throws TelegramSDKException
     * @throws Throwable
     */
    private function enterPromoCode(): void
    {
        $textResponse = Texts::getLabel(BotTextKeys::ENTER_PROMO_CODE->value);
        $this->telegramService->sendMessageByMarkup($textResponse);
        $this->buttonService->setLastButton(BotButtonsKeys::ENTER_PROMO_CODE->value, $this->telegramUserDto->getUserId());

        return;
    }

    /**
     * @throws Exception|Throwable
     */
    private function usePromoCode($text): void
    {
        $this->buttonService->clearLastButton($this->telegramUserDto->getUserId());
        $promoCode = Promocodes::findOne(['code' => $text]);
        $tariffType = $promoCode?->tariff_id;

        if (!$promoCode || !$promoCode->isValid() || !$tariffType){
            $textResponse = Texts::getLabel(BotTextKeys::NOT_FOUND_PROMO_CODE->value);
            $this->telegramService->sendMenu($textResponse);

            return;
        }

        $this->buttonService->setCache(
            Promocodes::PROMO_CODE_CACHE_PREFIX . $this->telegramUserDto->getUserId(),
            $promoCode->code
        );

        $this->choosePaymentMethod($tariffType);

        return;
    }

    /**
     * @return BotUsers|false
     * Сохраняет нового пользователя
     * @throws Throwable
     */
    private function saveNewUser(): bool|BotUsers
    {
        try {
            $userDto = $this->telegramUserDto;

            if (!BotUsers::findOne(['uid' => $userDto->getUserId()])) {
                $user = new BotUsers();
                $user->uid = $userDto->getUserId();
                $user->username = $userDto->getUsername();
                $user->role_id = 0;
                $user->status = 0;
                $image = $this->telegramService->saveUserPhoto();

                if ($image) {
                    $user->image = $image;
                }

                if (!$user->save()) {
                    ErrorLogHelper::logBotInfo(json_encode($user->errors, JSON_THROW_ON_ERROR), 'Ошибка при сохранении пользователя ');
                    $this->telegramService->sendErrorMessage();

                    return false;
                }

                $this->user = $user;

                return $user;
            }
        } catch (Exception $e) {
            ErrorLogHelper::logBotInfo($e->getMessage(). $e->getTraceAsString(), 'Ошибка при сохранении пользователя ');
            $this->telegramService->sendErrorMessage();
        }

        return false;
    }

    /**
     * @return void
     * Выводит баланс на экран
     */
    private function account(): void
    {
        try {
            $page = Pages::findOne([
                'command' => BotPagesKeys::ACCOUNT->value,
                'language' => Yii::$app->language
            ]);

            $page?->replaceReferralData($this->user);

            $buttons = $this->buttonService->getTariffTypeButtons();

            $this->telegramService->sendPage(
                command: BotPagesKeys::ACCOUNT->value,
                buttons: $buttons,
                page: $page,
            );
        } catch (Throwable $e) {
            ErrorLogHelper::logBotInfo($e->getMessage(). $e->getTraceAsString(), 'Ошибка при выводе баланса');
        }

        return;
    }

    /**
     * @return void
     * Заявка на вывод средств и оповещение админов
     * @throws \yii\db\Exception
     * @throws Throwable
     */
    private function withdraw(): void
    {
        $label = Texts::getLabel(BotTextKeys::ERROR_MESSAGE->value);

        if ($this->user?->bonus > 0){
            try {
                $withdraw = new Payouts([
                    'telegram_id' => $this->user->uid,
                    'username' => $this->user->username,
                    'uid' => $this->user->id,
                    'amount' => $this->user->bonus,
                    'status' => Payouts::STATUS_DRAFT
                ]);

                if($withdraw->save()){
                    $label = Texts::getLabel(BotTextKeys::WITHDRAW_PROCESS->value);
                    $admins = BotUsers::getAllAdmins();

                    /** @var BotUsers $admin */
                    foreach ($admins as $admin){
                        $text = $admin->getPayoutAdminNotificationMessage();

                        $this->telegramService->sendMessageByMarkup($text, $admin->uid);
                    }
                }
            } catch (Throwable $e) {
                ErrorLogHelper::logBotInfo($e->getMessage(). $e->getTraceAsString(), 'Ошибка при выводе средств');
            }

        }

        $this->telegramService->sendMenu($label);

        return;
    }

    /**
     * @return void
     * Выводит всех рефералов конкретного пользователя в файл
     * @throws JsonException
     * @throws Throwable
     */
    private function myReferrals(): void
    {
        $users = $this->user?->getReferrals()->asArray()->all();

        if($users) {
            $document = InputFile::create(Referrals::generateCsv($users, $this->telegramUserDto->getUserId()), 'my-referrals.csv');
            $this->telegramService->sendCsv($document);

            return;
        }

        $textResponse = Texts::getLabel(BotTextKeys::USER_NOT_FOUND->value);
        $this->telegramService->sendMenu($textResponse);

        return;
    }

    private function updateUsername(): void
    {
        try {
            if (!$this->user) {
                return;
            }

            if ($this->user->username === $this->telegramUserDto->getUsername()) {
                return;
            }

            $this->user->username = $this->telegramUserDto->getUsername();
            $this->user->save();
        } catch (Exception $e) {
            ErrorLogHelper::logBotInfo($e->getMessage() . $e->getTraceAsString(), 'Ошибка при изменении имени пользователя');

            return;
        }
    }
}