<?php

namespace app\models;

use app\components\RouteBuilder;
use app\components\services\bot\TelegramService;
use app\enums\BotButtonsKeys;
use app\enums\BotPagesKeys;
use app\enums\BotTextKeys;
use app\enums\Tariff;
use app\helpers\ErrorLogHelper;
use app\helpers\QRcodeGenerator;
use DateTime;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "bot_users".
 *
 * @property int         $id
 * @property int         $uid
 * @property string      $username
 * @property string|null $fio
 * @property string|null $created_at
 * @property int         $status
 * @property int         $phone
 * @property string      $chat_id
 * @property boolean     $chat_status
 * @property boolean     $on_call
 * @property boolean     $notification_on
 * @property int         $priority
 * @property int|null    $role_id
 * @property int|null    $bonus
 * @property string|null $language
 * @property int         $is_paid
 * @property int         $tariff
 * @property string      $paid_until
 * @property string      $trial_until
 * @property string      $email
 * @property string      $password_hash
 * @property string      $auth_key
 * @property string      $auth_key_expired_at
 * @property int         $referral_id
 * @property string         $oauth_id
 * @property string         $image
 * @property string|null    $last_visited_at
 * @property string|null    $payment_email
 * @property bool    $has_first_deposit
 * @property float   $total_paid
 * @property float   $total_fees
 * @property int   $city_id
 */

class BotUsers extends \yii\db\ActiveRecord
{
    public const ADMIN_ROLE_ID = 1;
    public const USER_ROLE_ID = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_BLOCKED = 0;
    public const PAID = 1;
    public const NOT_PAID = 0;

    public ?string $password = null;
    public const DEFAULT_BONUS = 25;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'status', 'role_id', 'phone', 'priority', 'chat_status', 'bonus', 'is_paid', 'tariff', 'referral_id', 'city_id'], 'integer'],
            [['created_at', 'on_call', 'notification_on', 'paid_until', 'trial_until', 'oauth_id', 'password', 'auth_key_expired_at', 'last_visited_at', 'payment_email', 'has_first_deposit'], 'safe'],
            [['username', 'fio', 'chat_id', 'language', 'email', 'password_hash', 'auth_key', 'image'], 'string', 'max' => 255],
            // Правило для проверки уникальности email
            ['email', 'unique', 'targetClass' => self::class, 'message' => 'Этот email уже используется.'],

            // Правило для проверки уникальности uid
            ['uid', 'unique', 'targetClass' => self::class, 'message' => 'Этот UID уже используется.'],
            [['payment_email'], 'email'],
            [['payment_email'], 'unique', 'targetClass' => self::class, 'message' => 'Этот email уже используется.'],
            [['total_paid', 'total_fees'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'uid'             => 'Telegram ID',
            'username'        => 'Имя пользователя',
            'fio'             => 'ФИО',
            'created_at'      => 'Добавлен',
            'status'          => 'Статус',
            'role_id'         => 'Роль',
            'phone'           => 'Телефон',
            'priority'        => 'Приоритет',
            'on_call'         => 'В диалоге',
            'chat_status'     => 'Не отвечено',
            'notification_on' => 'Оповещения',
            'bonus'           => 'Баланс',
            'language'        => 'Язык',
            'paid_until'      => 'Оплачено до',
            'is_paid'         => 'Оплачено',
            'tariff'          => 'Тариф',
            'trial_until'     => 'Триал до',
            'email'           => 'Email',
            'password_hash'   => 'Пароль',
            'auth_key'        => 'Auth Key',
            'referral_id'     => 'Реферал',
            'oauth_id'        => 'OAuth ID',
            'image'           => 'Изображение',
            'auth_key_expired_at' => 'Auth Key Expired At',
            'last_visited_at' => 'Последнее посещение',
            'payment_email'   => 'Email для оплаты',
            'has_first_deposit' => 'Имеет первый депозит',
            'total_paid'      => 'Оплачено (всего)',
            'total_fees'      => 'Комиссия (всего)',
            'city_id'         => 'Город',
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->generateNewToken();

            if (!$this->email) {
                $this->email = null;
            }
        }

        if (!$this->status) {
            $this->status = 1;
        }

        if (!$this->created_at) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->logNewUser();
        } else {
            $this->logUserChange($changedAttributes);
        }
    }

    public function beforeDelete(): bool
    {
        Referrals::deleteAll(['OR', ['parent_id' => $this->id], ['referral_id' => $this->id]]);
        return parent::beforeDelete();
    }

    public function logNewUser(): void
    {
        try {
            $logExist = Logging::findOne(['user_id' => $this->id, 'type' => Logging::TYPE_NEW_USER]);

            if (!$logExist) {
                $log = new Logging();
                $log->user_id = $this->id;
                $log->type = Logging::TYPE_NEW_USER;
                $log->details = 'Добавлен новый пользователь.';
                $log->save();
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString());
        }
    }

    public function logUserChange($changedAttributes): void
    {
        try {
            if (isset($changedAttributes['is_paid']) || isset($changedAttributes['tariff'])) {
                $oldIsPaid = $this->getIsPaidLabel($changedAttributes['is_paid']);
                $oldTariff = isset($changedAttributes['tariff'])
                    ? Tariff::getTariffName($changedAttributes['tariff'])
                    : '';
                $newIsPaid = $this->getIsPaidLabel();
                $newTariff = Tariff::getTariffName($this->tariff);

                $log = new Logging();
                $log->user_id = $this->id;
                $log->type = Logging::TYPE_STATUS_CHANGE;
                $log->old = 'Статус: ' . $oldIsPaid . ', Тариф: ' . $oldTariff;
                $log->new = 'Статус: ' . $newIsPaid . ', Тариф: ' . $newTariff;
                $log->details = $this->paid_until;
                $log->save();

                if ($this->isBotPaid()){
                    $this->sendPaymentNotification();

                    if (!$this->has_first_deposit) {
                        $this->updateAttributes(['has_first_deposit' => true]);

                        $this->sendParentReward();
                    }
                }
            }

        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString());
        }
    }

    public function getIsPaidLabel($value = null): string
    {
        return $this->getIsPaid() ? 'Оплачен' : 'Не оплачен';
    }

    public function getIsPaid($value = null): bool
    {
        if ($value) {
            return $value === self::PAID;
        }

        return $this->is_paid === self::PAID;
    }

    public function isAdmin(): ?int
    {
        return $this->role_id === self::ADMIN_ROLE_ID;
    }

    public function isBotPaid(): bool
    {
        return $this->is_paid && ($this->tariff > 0) && ($this->paid_until > date('Y-m-d H:i:s'));
    }

    public function isTrialActive(): bool
    {
        return ($this->trial_until > date('Y-m-d H:i:s'));
    }

    public function isBusiness(): bool
    {
        return $this->isBotPaid() && $this->tariff === Tariff::MONTH_3->value;
    }

    public function isAuthor(): bool
    {
        return $this->isBotPaid() && $this->tariff === Tariff::MONTH_6->value;
    }

    public function isParentAuthor(): bool
    {
        return $this->parent?->isBotPaid() && $this->parent?->tariff === Tariff::MONTH_6->value;
    }

    public function isParentBusiness(): bool
    {
        return $this->parent?->isBotPaid() && $this->parent?->tariff === Tariff::MONTH_6->value;
    }

    public function isParentPartner()
    {
        return $this->parent?->isPartner();
    }

    public function isPartner(): bool
    {
        return $this->isBotPaid() && ($this->isBusiness() || $this->isAuthor());
    }

    public function isMyConsultant($id): bool
    {
        return $this->referral_id === (int) $id;
    }

    /**
     * @param $text
     *
     * @return ActiveQuery|null
     */
    public function prepareSearchQuery($text): ActiveQuery|null
    {
        $query = self::find();
        if (is_numeric($text)) {
            $query->where(['uid' => $text]);
        } else {
            $query->where(['username' => $text]);
        }

        return $query;
    }

    public function linkReferral($referralParent): bool
    {
        if ($referralParent) {
            $referral = Referrals::findOne(['referral_id' => $this->id]) ?? new Referrals();
            $referral->parent_id = $referralParent->id;
            $referral->parent_username = $referralParent->username;
            $referral->referral_username = $this->username;
            $referral->referral_id = $this->id;
            $referral->created_at = date('Y-m-d', time());
            if ($referral->save()) {
                $this->updateAttributes(['referral_id' => $referralParent->id]);

                return true;
            }

            Yii::error('Error while linking referral: '. print_r($referral->errors, true));
        }

        return false;
    }

    public static function findAvailableOperator($uid)
    {
        return self::find()
            ->where(['role_id' => self::ADMIN_ROLE_ID, 'on_call' => 0])
            ->andFilterWhere(['not', ['uid' => $uid]])
            ->orderBy('priority')
            ->limit(1)
            ->one();
    }

    public function setOrUnsetChat($chatId = null)
    {
        if ($chatId) {
            $this->chat_id = (string) $chatId;
            $this->on_call = 1;
            $this->chat_status = 0;
        } else {
            $this->chat_id = null;
            $this->on_call = 0;
            $this->chat_status = 0;
        }
        if (!$this->save()) {
            Yii::error($this->errors);
        }
    }

    public function setChatStatus($status = false)
    {
        $this->chat_status = $status;
        return $this->save();
    }

    public function saveChat($message = null, $operatorId = null)
    {
        $chat = new UserChat();
        $chat->user_id = $this->uid;
        $chat->operator_id = $operatorId;
        $chat->message = $message;
        $chat->created_at = date("Y-m-d H:i:s");
        return $chat->save();

    }

    public function deleteFinishedChat()
    {
        $chat = UserChat::findOne(['operator_id' => $this->uid]);
        if ($chat) {
            $chat->delete();
        }
    }

    public function cleanConversation($id)
    {
        UserChat::deleteAll(['OR', ['user_id' => $this->id], ['user_id' => $id]]);
    }

    public function getUserChat()
    {
        return $this->hasOne(UserChat::class, ['user_id' => 'uid']);
    }

    public function getNotAnsweredUserChat()
    {
        return UserChat::find()
            ->where(['or', ['operator_id' => ''], ['operator_id' => null]])
            ->orderBy(['created_at' => SORT_ASC])
            ->limit(1)
            ->one();
    }

    public function getReferrals()
    {
        return $this->hasMany(BotUsers::class, ['id' => 'referral_id'])->viaTable('referrals', ['parent_id' => 'id']);
    }

    public function getPayouts(): ActiveQuery
    {
        return $this->hasMany(Payouts::class, ['uid' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getLatestWithdrawal(): ActiveQuery
    {
        return $this->hasOne(Payouts::class, ['uid' => 'id'])
            ->where(['status' => Payouts::STATUS_WITHDRAWN])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function getParent()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'parent_id'])->viaTable('referrals', ['referral_id' => 'id']);
    }

    public function getInfo(): ActiveQuery
    {
        return $this->hasOne(UserInfo::class, ['user_id' => 'id']);
    }

    public function calculatePaidUntilDate(int $tariff): string
    {
        $paidUntilTimestamp = $this->paid_until ? strtotime($this->paid_until) : null;

        if (!$this->paid_until || ($paidUntilTimestamp !== false && $paidUntilTimestamp < time())) {
            $oldBotDate = time();
            ErrorLogHelper::logPaymentInfo("Дата оплаты paid_until({$this->paid_until}) не установлена или истекла. Устанавливаем от текущего времени: " . date("Y-m-d H:i:s", $oldBotDate), 'payment');
        } else {
            $oldBotDate = $paidUntilTimestamp;
            ErrorLogHelper::logPaymentInfo("Дата оплаты paid_until({$this->paid_until}) установлена и актуальна. Используем старую дату: " . date("Y-m-d H:i:s", $oldBotDate), 'payment');
        }

        if ($this->tariff !== $tariff) {
            $oldBotDate = time();
            ErrorLogHelper::logPaymentInfo("Тариф изменён. Пересчитываем от текущего времени: " . date("Y-m-d H:i:s", $oldBotDate), 'payment');
        }

        $newPaidUntil = date("Y-m-d", strtotime("+$tariff month", $oldBotDate));
        ErrorLogHelper::logPaymentInfo("Устанавливаем новую дату paid_until: " . $newPaidUntil, 'payment');

        return $newPaidUntil;
    }

    public function activateBot($tariff, $total = 0, $commission = 0): bool
    {
        $this->is_paid = 1;
        $this->paid_until = $this->calculatePaidUntilDate($tariff);
        $this->tariff = $tariff;
        $this->trial_until = null;

        if ($total > 0) {
            $this->total_paid += $total;
        }

        if ($commission > 0) {
            $this->total_fees += $commission;
        }

        if (!$this->save()){
            ErrorLogHelper::logPaymentInfo(json_encode($this->errors, JSON_THROW_ON_ERROR), 'Ошибка активации тарифа');

            return false;
        }

        ErrorLogHelper::logPaymentInfo('Успешная активации тарифа');

        return true;
    }

    public function notifyUser()
    {

    }

    public function generateBotInvite(): string
    {
        $user = $this->getAuthorizedReferralUser();

        return Yii::$app->params['bot_link'] . '?start=' . $user->uid;
    }

    public function getAuthorizedReferralUser()
    {
        if ($this->isAuthor() || $this->isBusiness()) {
            return $this;
        }

        return $this->parent ?? $this;
    }

    public function generateQr(): array
    {
        $textUrl = $this->generateBotInvite();

        $qrNane = $this->id;
        $uploadDir = Yii::getAlias('@app/web/uploads/qr');
        $fileName = 'telegram_qr_' . Yii::$app->language . '_' . $qrNane . '.png';
        $filePath = $uploadDir . '/' . $fileName;
        $fileUrl = RouteBuilder::toApi() . 'uploads/qr/' . $fileName;

        // Проверка существования файла
        if (file_exists($filePath)) {
            return [
                'url' => $textUrl,
                'fileUrl' => $fileUrl,
            ];
        }

        // Создание директории, если не существует
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $qrBinary = QRcodeGenerator::generate($textUrl);

        // Проверка корректности данных QR-кода
        if (empty($qrBinary)) {
            Yii::error('Не корректный QR-код ' . $filePath);
            Yii::$app->response->statusCode = 500;

            return [
                'url' => null,
                'fileUrl' => null,
            ];
        }

        // Сохранение файла
        file_put_contents($filePath, $qrBinary);

        // Проверка, что файл был успешно сохранен
        if (!file_exists($filePath) || !filesize($filePath)) {
            Yii::error('Не удалось сохранить файл ' . $filePath);

            return [
                'url' => null,
                'fileUrl' => null,
            ];
        }

        // Проверка, что файл является допустимым изображением
        $fileInfo = getimagesize($filePath);

        if ($fileInfo === false) {
            Yii::error('Некорректный формат файла  ' . $filePath);
            unlink($filePath); // Удаление некорректного файла

            return [
                'url' => null,
                'fileUrl' => null,
            ];
        }

        return [
            'url' => $textUrl,
            'fileUrl' => $fileUrl,
        ];
    }

    public static function calculateStatistics(): array
    {
        $connection = \Yii::$app->db;

        // Получаем текущую дату без времени
        $currentDate = date('Y-m-d');

        // Общий подсчет оплаченных и не просроченных пользователей по тарифам
        $tariffTotal = $connection->createCommand("
        SELECT tariff, COUNT(*) AS total
        FROM bot_users
        WHERE is_paid = 1
          AND paid_until >= :currentDate
        GROUP BY tariff
    ", [':currentDate' => $currentDate])->queryAll();

        // Подсчет пользователей с оставшимися днями <= 7
        $weekData = $connection->createCommand("
        SELECT tariff, COUNT(*) AS week
        FROM bot_users
        WHERE is_paid = 1
          AND paid_until >= :currentDate
          AND DATEDIFF(paid_until, :currentDate) <= 7
        GROUP BY tariff
    ", [':currentDate' => $currentDate])->queryAll();

        // Подсчет пользователей с оставшимися днями <= 30 и > 7
        $monthData = $connection->createCommand("
        SELECT tariff, COUNT(*) AS month
        FROM bot_users
        WHERE is_paid = 1
          AND paid_until >= :currentDate
          AND DATEDIFF(paid_until, :currentDate) <= 30
          AND DATEDIFF(paid_until, :currentDate) > 7
        GROUP BY tariff
    ", [':currentDate' => $currentDate])->queryAll();

        // Преобразование результатов в ассоциативные массивы
        $tariff = [
            Tariff::NONE->value     => 0,
            Tariff::MONTH_1->value  => 0,
            Tariff::MONTH_12->value  => 0,
        ];
        foreach ($tariffTotal as $row) {
            $tariff[$row['tariff']] = (int)$row['total'];
        }

        $week = [
            Tariff::NONE->value     => 0,
            Tariff::MONTH_1->value  => 0,
            Tariff::MONTH_12->value  => 0,
        ];
        foreach ($weekData as $row) {
            $week[$row['tariff']] = (int)$row['week'];
        }

        $month = [
            Tariff::NONE->value     => 0,
            Tariff::MONTH_1->value  => 0,
            Tariff::MONTH_12->value => 0,
        ];
        foreach ($monthData as $row) {
            $month[$row['tariff']] = (int)$row['month'];
        }

        return [
            'week'   => $week,
            'month'  => $month,
            'tariff' => $tariff,
        ];
    }

    /**
     * @return UserInfo|null
     */
    public function getPartnerInfo(): ?UserInfo
    {
        return UserInfo::findOne(['user_id' => $this->id, 'language' => Yii::$app->language]);
    }

    /**
     * Проверяет сколько до конца подписки для рассылки уведомлений.
     *
     * @return int
     * @throws \DateMalformedStringException
     */
    public function checkPaidUntil(): int
    {
        $currentDate = new DateTime();
        $paidUntilDate = new DateTime($this->paid_until);

        // Вычисляем разницу в днях между текущей датой и paid_until
        $interval = $currentDate->diff($paidUntilDate);
        $daysRemaining = (int)$interval->format('%a');

        // Проверяем количество оставшихся дней
        if (in_array($daysRemaining, [1, 3, 7])) {
            return $daysRemaining;
        }

        return 0;
    }


    /**
     * @throws Exception
     */
    public function generateNewToken(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
        $this->auth_key_expired_at = time() + (60 * 60 * 24 * 180);
    }

    public static function getDefaultParent(): ?BotUsers
    {
        return self::findOne(1);
    }

    public static function getDefaultPartnerInfo(): ?UserInfo
    {
        return self::getDefaultParent()?->getPartnerInfo();
    }

    public function setNewLastVisitedDate(): void
    {
        if (!$this->last_visited_at || date('Y-m-d', strtotime($this->last_visited_at)) !== date('Y-m-d')) {
            $this->updateAttributes(['last_visited_at' => date('Y-m-d H:i:s')]);
        }
    }

    private function sendPaymentNotification(): void
    {
        try {
            /** @var TelegramService $telegramService */
            $telegramService = Yii::$app->telegramService;
            $telegramService->setTelegramUserId($this->uid);
            $buttons = Yii::$app->buttonService->getInlineButtons(Buttons::POSITION_PAYMENT);
            $supportButton  = Yii::$app->buttonService->getInlineButtons(null, [BotButtonsKeys::SUPPORT->value]);

            $telegramService->sendPage(
                BotPagesKeys::AFTER_PAYMENT->value,
                false,
                array_merge($buttons, $supportButton),
            );
        } catch (Throwable $e) {

            Yii::error("Ошибка при отправке уведомления пользователю ID: {$this->id}. Ошибка: " . $e->getMessage());
        }
    }

    private function sendParentReward(): void
    {
        $parent = $this->parent;

        if ($parent) {
            ErrorLogHelper::logPayoutInfo("Начисление бонуса родительскому пользователю ID: {$parent->id}, текущий баланс: {$parent->bonus}");

            $parent->updateAttributes(['bonus' => $parent->bonus + self::DEFAULT_BONUS]);

            if (!$parent->save()) {
              ErrorLogHelper::logPayoutInfo("Ошибка при добавлении баланса родительского пользователя ID: {$parent->id}. Ошибки: ". implode(', ', $parent->errors));

              return;
            }

            Payouts::saveNewPayout($parent, self::DEFAULT_BONUS, Payouts::STATUS_ADDED);

            Payouts::sendPayoutNotification($parent->uid, self::DEFAULT_BONUS, Payouts::STATUS_ADDED);
        }
    }

    public function getPayoutAdminNotificationMessage(): string
    {
        return Texts::getLabel(BotTextKeys::WITHDRAW_REQUEST->value)
            . ' Пользователь с id: '
            . $this->uid
            . ' и username: '
            . $this->username
            . '. Сумма:' . $this->bonus;
    }

    public static function getAllAdmins(): array
    {
        return BotUsers::find()->select(['id', 'uid', 'role_id'])->where(['role_id' => BotUsers::ADMIN_ROLE_ID, 'notification_on' => 1])->all();
    }

    public function isRegistrationFinished(): bool
    {
        return $this->fio && $this->phone && $this->email && $this->city_id;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function saveEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return $this->updateAttributes(['email' => $email]);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function savePhone($original): bool
    {
        // Normalize phone number format
        $phone = str_starts_with($original, '+')
            ? '+' . preg_replace('/[^0-9]/', '', substr($original, 1))
            : preg_replace('/[^0-9]/', '', $original);

        // Validate phone number
        $isValid = str_starts_with($phone, '+')
            ? strlen($phone) === 12 && $phone[1] === '7'
            : strlen($phone) === 11 && $phone[0] === '7';

        if (!$isValid) {
            return false;
        }

        return $this->updateAttributes(['phone' => $phone]);
    }
}
