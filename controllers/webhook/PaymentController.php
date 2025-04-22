<?php

namespace app\controllers\webhook;

use app\components\payments\invoices\LavaInvoice;
use app\components\payments\invoices\MeletonInvoice;
use app\components\payments\invoices\PaypalInvoice;
use app\components\payments\invoices\StripeInvoice;
use app\helpers\ErrorLogHelper;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use yii\web\Controller;

class PaymentController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @throws Exception
     */
    public function actionMeleton(): ?string
    {
        try {
            return (new MeletonInvoice())->process();
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo($e->getMessage() . $e->getTraceAsString(), 'Ошибка оплаты Meleton');

            throw $e;
        }
    }

    /**
     * @throws JsonException
     */
    public function actionLava(): ?string
    {
        try {
            return (new LavaInvoice())->process();
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo($e->getMessage() . $e->getTraceAsString(), 'Ошибка оплаты Lava');

            throw $e;
        }
    }

    /**
     * @throws JsonException|Exception|GuzzleException
     */
    public function actionPaypal(): ?string
    {
        try {
            return (new PaypalInvoice())->process();
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo($e->getMessage() . $e->getTraceAsString(), 'Ошибка оплаты PayPal');
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function actionStripe(): ?string
    {
        try {
            return (new StripeInvoice())->process();
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo($e->getMessage() . $e->getTraceAsString(), 'Ошибка оплаты Stripe');
            throw $e;
        }
    }
}