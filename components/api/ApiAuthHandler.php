<?php

namespace app\components\api;

use app\helpers\ErrorLogHelper;
use Yii;
use yii\base\ActionFilter;
use yii\web\UnauthorizedHttpException;

class ApiAuthHandler extends ActionFilter
{
    /**
     * @return bool
     * Если запрос сайта, то проверит CSRF токен, иначе - API ключ
     * @throws UnauthorizedHttpException
     */
    public function authorize(): bool
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');

        if ($this->validateBearerToken($authHeader)){
            return true;
        }

        throw new UnauthorizedHttpException('Неверный или отсутствующий API ключ / CSRF токен');
    }

    /**
     * @param $authHeader
     * Проверит наличие и соответствие Bearer токена
     * @return bool
     */
    private function validateBearerToken($authHeader): bool
    {
        try {
            if (!str_contains($authHeader, 'Bearer'))
                return false;

            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $apiKey = $matches[1] ?? '';
                return $this->validateApiKey($apiKey);
            }

            return false;
        } catch (\Exception $e) {
            ErrorLogHelper::logApiInfo($e->getMessage(), 'Ошибка проверки наличия и соответствия Bearer токена');

            return false;
        }
    }

    /**
     * @param $bearerToken
     * Проверит соответствие API ключа
     * @return bool
     */
    private function validateApiKey($bearerToken): bool
    {
        $apiKey = getenv('API_KEY');

        return (md5(date('Y-m-d') . $apiKey) === $bearerToken);
    }
}