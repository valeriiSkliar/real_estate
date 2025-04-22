<?php

namespace app\components\services\api;

use app\enums\Tariff;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\UserInfo;
use JsonException;
use TgWebValid\Exceptions\BotException;
use TgWebValid\TgWebValid;
use Yii;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UploadedFile;

class ApiSupportService
{
    public array $post;
    public ?BotUsers $user = null;
    public ?BotUsers $consultant = null;
    public string $apiUrl;


    public function __construct(?BotUsers $user, ?BotUsers $consultant, string $apiUrl, array $post = [])
    {
        $this->user = $user;
        $this->consultant = $consultant;
        $this->apiUrl = $apiUrl;
        $this->post = $post;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionGenerateInvite()
    {
        $user = $this->getUser();

        if (is_string($user)) {
            return $user;
        }

        return $user->generateQr();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function validateAuthor(): string|BotUsers
    {
        $user = $this->getUser();

        if (!$user->isAuthor()) {
            throw new NotFoundHttpException('User is not Author');
        }

        return $user;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function getDescription()
    {
        if (!isset($this->post['description'])) {
            throw new NotFoundHttpException('Description is required');
        }

        return $this->post['description'];
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws JsonException
     */
    public function saveEntity($entity, $id, $user, $field): bool|string
    {
        try {
            if ($entity->isNewRecord) {
                $entity->user_id = $user->id;
                $entity->{$field} = $id;
            }

            $entity->description_parts = $this->getDescription();

            if (!$entity->save()) {
                ErrorLogHelper::logApiInfo($entity->getErrors());
                throw new HttpException(400, json_encode($entity->getErrors(), JSON_THROW_ON_ERROR));
            }

            return true;
        } catch (UnprocessableEntityHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            ErrorLogHelper::logApiInfo($e->getMessage());
            throw new HttpException(500, json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR));
        }

    }

    /**
     * @throws NotFoundHttpException
     */
    public function getUser(): string|BotUsers
    {
        $user = $this->user;

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $user;
    }

    public function getUserInfo($user): UserInfo
    {
        $userInfo = UserInfo::findOne(['user_id' => $user->id, 'language' => Yii::$app->language]);

        if (!$userInfo) {
            $userInfo = new UserInfo();
            $userInfo->user_id = $user->id;
            $userInfo->language = Yii::$app->language;
        }

        return $userInfo;
    }

    /**
     * @throws HttpException
     */
    public function validateDescriptionLength($description): bool|string
    {
        if (mb_strlen($description) > 1000) {
            throw new HttpException(400, 'descriptionIsTooLong');
        }

        return true;
    }

    public function handleImageUpload($userInfo): void
    {
        $imageFile = UploadedFile::getInstanceByName('photo_url');

        if ($imageFile !== null) {
            $imagePath = '/uploads/user-info/' . $userInfo->user_id . '_' . Yii::$app->language . '.jpg';
            $savePath = \Yii::getAlias("@app/web") . $imagePath;

            if ($imageFile->saveAs($savePath)) {
                $userInfo->photo_url = $imagePath;
            }
        }
    }

    public function handleDomainUpdate(BotUsers $user): bool|string
    {
        if (isset($this->post['domain']) && $this->post['domain']) {

            if (!$user->validateDomain($this->post['domain'])) {
                throw new HttpException(400, 'Domain is not valid');
            }

            if (!$user->isAvailableSubdomain($this->post['domain'])) {
                throw new HttpException(400, 'Domain is not available');
            }

            $user->domain = mb_strtolower($this->post['domain']);
            $user->save(false, ['domain']);
        }

        return true;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function getConsultant(): BotUsers|null
    {
        if (!$this->consultant) {
            throw new NotFoundHttpException('Consultant is not found');
        }

        return $this->consultant;
    }

    public function validateOwner(): bool
    {
        return $this->user && $this->user?->id === $this->consultant?->id;
    }

    public function assignConsultant(): bool
    {
        if (!$this->user?->id) {
            return false;
        }

        if (!$this->consultant){
            return false;
        }

        if ($this->user->referral_id && $this->consultant->id === $this->user->referral_id) {
            return true;
        }

        if (!$this->consultant->isPartner()) {
            ErrorLogHelper::logApiInfo('Реферал: '. $this->user->id . ' не добавлен консультанту: '. $this->consultant->id . ' т.к. консультант не партнер');

            return false;
        }

        if (!$this->user->linkReferral($this->consultant)) {
            ErrorLogHelper::logApiInfo('Ошибка добавление реферала через сайт, пользователь: '. $this->user->id . ' консультант: '. $this->consultant->id);

            return false;
        }

        return true;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function checkIfUserExistById(): array|BotUsers|null
    {
        $userId = $this->post['id'] ?? null;

        if (!$userId || strlen($userId) < 2) {
            throw new NotFoundHttpException('UserId is required');
        }

        return $this->getUserByParam(['id' => $userId]);
    }

    /**
     * @throws HttpException
     */
    public function checkIfUserExistByEmail(): array|BotUsers|null
    {
        $email = $this->post['email'] ?? null;

        if (!$this->validateEmail($email)) {
            throw new NotFoundHttpException('Valid email is required');
        }

        return $this->getUserByParam(['email' => $email]);
    }

    /**
     * @throws \yii\base\Exception
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function checkIfUserExistByUid(): array|BotUsers|null
    {
        $uid = $this->getTelegramAppId();

        if (!is_numeric($uid)) {
            throw new NotFoundHttpException('Valid uid is required');
        }

        return $this->getUserByParam(['uid' => $uid]);
    }

    public function validateEmail($email): bool
    {
        return $email && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function checkOauthId($user): bool
    {
        $id = $this->post['oauth_id'] ?? null;
        $oauthId = (string)$user->oauth_id;

        if (!$id || !$oauthId || $oauthId !== (string) $id) {
            throw new NotFoundHttpException('Valid oauth_id is required');
        }

        return true;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function checkPassword($user): bool
    {
        $password = $this->post['password'] ?? null;

        if (!$password || !$user->password_hash || !Yii::$app->security->validatePassword((string)$password, $user->password_hash)) {
            throw new NotFoundHttpException('Valid password is required');
        }

        return true;
    }

    public function getUserByParam($array): ?BotUsers
    {
        return BotUsers::findOne($array);
    }

    public function saveNewUser(BotUsers $user): array
    {
        $assigned = false;

        if (Yii::$app->globalParams->domain) {
            $this->user = $user;
            $assigned = $this->assignConsultant();
        }

        if ($this->consultant && (!$this->consultant->isPartner() || !$assigned)) {
            $trialDays = 14;
            $user->trial_until = date('Y-m-d H:i:s', time() + ($trialDays * 24 * 3600));
        }

        $user->status = BotUsers::STATUS_ACTIVE;
        $user->language = Yii::$app->language;
        $user->created_at = date('Y-m-d H:i:s');

        if (!$user->save()) {
            ErrorLogHelper::logApiInfo($user->getErrors());
            Yii::$app->response->statusCode = 400;

            return [
                'auth_key' => null,
                'expired_at' => null,
                'domain'   => null,
                'error'    => $user->getErrors(),
            ];
        }

        return [
            'auth_key' => $user->auth_key,
            'expired_at' => $user->auth_key_expired_at,
            'domain'   => Yii::$app->globalParams->domain,
            'error'    => null,
        ];
    }

    public function prepareNewUser(): BotUsers
    {
        $user = new BotUsers();
        $user->fio = $this->post['fio'] ?? null;
        $user->email = $this->post['email'] ?? null;
        $user->image = $this->post['image'] ?? null;
        $user->oauth_id = $this->post['oauth_id'] ?? null;
        $user->password_hash = Yii::$app->security->generatePasswordHash(
            $this->post['password'] ??  Yii::$app->security->generateRandomString()
        );

        return $user;
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function login(BotUsers $user): array
    {
        $user->language = Yii::$app->language;

        if ($user->auth_key_expired_at < time()) {
            $user->generateNewToken();
        }

        $userProfile = $this->getUserProfile($user);

        if ($user->save()){
            return [
                'auth_key' => $user->auth_key,
                'expired_at' => $user->auth_key_expired_at,
                'domain'   => $this->getDomain($user),
                'error'    => null,
                'profile' => $userProfile,
            ];
        }

        Yii::$app->response->statusCode = 400;

        return [
            'auth_key' => null,
            'expired_at' => null,
            'domain'   => null,
            'error'    => $user->getErrors(),
        ];
    }

    public function getDomain(BotUsers $user): ?string
    {
        if (Yii::$app->globalParams->domain) {
            return Yii::$app->globalParams->domain;
        }

        return $user->parent?->domain ?? $user->domain;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function getUserProfile(?BotUsers $user = null): array
    {
        $nullInfo = [
            'id'          => null,
            'user_id'     => null,
            'name'        => null,
            'description' => null,
            'photo_url'   => null,
            'phone'       => null,
            'telegram'    => null,
            'email'       => null,
            'whatsapp'    => null,
        ];

        if (!$user) {
            $user = $this->getUser();
        }

        $userInfo = $user
            ->getInfo()
            ->select([
                'id', 'user_id', 'name', 'description', "CONCAT('$this->apiUrl', photo_url) AS photo_url",
                'phone', 'telegram', 'email', 'whatsapp',
            ])
            ->andFilterWhere(['language' => Yii::$app->language])
            ->asArray()
            ->one() ?? $nullInfo;

        $userInfo['domain'] = $user->domain;
        $userInfo['personal_info'] = [
            'id'          => $user->id,
            'image'       => $user->image,
            'name'        => $user->fio,
            'username'    => $user->username,
            'email'       => $user->email,
            'paid_until'  => $user->paid_until,
            'trial_until' => $user->trial_until,
            'tariff'      => Tariff::getTariffSlug($user->tariff),
        ];

        if (!$user->isBotPaid()) {
            $userInfo['personal_info']['paid_until'] = null;
            $userInfo['personal_info']['tariff'] = Tariff::getTariffSlug(0);
        }

        if (!$user->isTrialActive()) {
            $userInfo['personal_info']['trial_until'] = null;
        }

        $userInfo['telegram_link'] = $user->generateBotInvite();
        $userInfo['web_link'] = $user->generateQr();
        $userInfo['edit_allowed'] = $user->isPartner();
        $userInfo['web_owner'] = !empty($user->domain);
        $userInfo['bot_owner'] = !empty($user->uid);

        return $userInfo;
    }

    /**
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function parseTelegramAppData($initData): array
    {
        $botToken = Yii::$app->params['bot_token'];

        if (empty($initData)) {
            throw new BadRequestHttpException('Query is empty.');
        }

        if (!$this->isSafe($botToken, $initData)) {
            throw new UnauthorizedHttpException('Telegram app validation failed.');
        }

        parse_str($initData, $params);

        // Проверка наличия параметра 'user'
        if (!isset($params['user'])) {
            throw new BadRequestHttpException('User data is missing.');
        }

        // Декодирование JSON строки пользователя
        $userDataJson = $params['user'];
        $userData = json_decode($userDataJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid user data.');
        }

        return $userData;
    }

    /**
     * @throws \yii\base\Exception
     * @throws BadRequestHttpException
     */
    public function getTelegramAppId(): string
    {
        $userData = $this->parseTelegramAppData(Yii::$app->request->queryString);

        // Извлечение данных пользователя
        $telegramUserId = $userData['id'] ?? null;

        if (!$telegramUserId) {
            throw new BadRequestHttpException('User ID is missing.');
        }

        return $telegramUserId;
    }


    public function isSafe(string $botToken, string $initData): bool
    {
        [$checksum, $sortedInitData] = $this->convertInitData($initData);
        $secretKey                   = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $hash                        = bin2hex(hash_hmac('sha256', $sortedInitData, $secretKey, true));

        return 0 === strcmp($hash, $checksum);
    }

    private function convertInitData(string $initData): array
    {
        $initDataArray = explode('&', rawurldecode($initData));
        $needle        = 'hash=';
        $hash          = '';

        foreach ($initDataArray as &$data) {
            if (substr($data, 0, \strlen($needle)) === $needle) {
                $hash = substr_replace($data, '', 0, \strlen($needle));
                $data = null;
            }
        }
        $initDataArray = array_filter($initDataArray);
        sort($initDataArray);

        return [$hash, implode("\n", $initDataArray)];
    }
}