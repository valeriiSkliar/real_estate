<?php

namespace app\components\payments\invoices;

use app\enums\PaymentStatuses;
use app\helpers\ErrorLogHelper;
use app\models\Payments;
use app\models\Tariffs;
use app\models\BotUsers;
use Stripe\Checkout\Session;
use Stripe\Invoice;
use Stripe\Subscription;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\Exception\SignatureVerificationException;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use Exception;
use RuntimeException;

class StripeInvoice
{
    private string $webhookSecret;

    public function __construct()
    {
        $this->webhookSecret = getenv('STRIPE_WEBHOOK_SECRET');

        if (!$this->webhookSecret) {
            throw new RuntimeException('Stripe webhook secret not set');
        }

        Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
    }

    /**
     * Обработка вебхука Stripe.
     *
     * @return string
     */
    public function process(): string
    {
        $payload = Yii::$app->request->getRawBody();
        $sigHeader = Yii::$app->request->headers->get('Stripe-Signature');
        $endpointSecret = $this->webhookSecret;
        ErrorLogHelper::logPaymentInfo(json_encode($payload), 'Stripe webhook data');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (SignatureVerificationException $e) {
            ErrorLogHelper::logPaymentInfo('Stripe webhook signature verification failed: ' . $e->getMessage(), 'Stripe error');
            return 'error';
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo('Stripe webhook error: ' . $e->getMessage(), 'Stripe error');
            return 'error';
        }

        // Обработка различных типов событий
        switch ($event->type) {
            case 'invoice.paid':
                return $this->handleInvoicePaid($event->data->object);
            case 'checkout.session.completed':
//                return $this->handleSessionCompleted($event->data->object);
                return 'ok';
            default:
                ErrorLogHelper::logPaymentInfo('Unhandled Stripe event type: ' . $event->type, 'Stripe error');
                return 'error';
        }
    }

    /**
     * Обработка успешной оплаты счета (автоматическое продление).
     *
     * @param Invoice $object
     * @return string
     */
    private function handleInvoicePaid(Invoice $object): string
    {
        try {
            $subscriptionId = $object->subscription;
            if (!$subscriptionId) {
                throw new Exception('No subscription ID in Invoice');
            }

            // Получаем объект Subscription с Stripe
            $subscription = Subscription::retrieve($subscriptionId);
            $paymentId = $subscription->metadata->payment_id ?? null;

            if (!$paymentId) {
                throw new Exception('No payment_id in Stripe object metadata');
            }

            $total = $object->amount_paid / 100;
            $fee = $total * 0.015 + 0.3;

            return $this->handleOrder($paymentId, $total, $fee);
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo('Stripe webhook processing error: ' . $e->getMessage(), 'Stripe error');
            return 'error';
        }
    }

    private function handleSessionCompleted(Session $object): string
    {
        try {
            $paymentId = $object->metadata->payment_id ?? null;

            if (!$paymentId) {
                throw new Exception('No payment_id in Stripe object metadata');
            }

            $total = $object->amount_total / 100;
            $fee = $total * 0.015 + 0.3;

            return $this->handleOrder($paymentId, $total, $fee);
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo('Stripe webhook processing error: ' . $e->getMessage(), 'Stripe error');
            return 'error';
        }
    }

    /**
     * @throws \yii\db\Exception
     * @throws \JsonException
     * @throws Exception
     */
    private function handleOrder($paymentId, $total = 0.0, $commission = 0.0): string
    {
        /** @var Payments $payment */
        $payment = Payments::findOne(['id' => $paymentId]);
        if (!$payment) {
            throw new Exception("No payment found with id: {$paymentId}");
        }

        $user = $payment->user;
        $tariff = Tariffs::findOne($payment->tariff_id);

        if (!$user || !$tariff) {
            throw new Exception("No user or tariff found for payment id: {$paymentId}");
        }

        $newStatus = PaymentStatuses::SUCCESS->value;

        if ($tariff->subscription_id){
            $newStatus = $payment->status !== PaymentStatuses::SUBSCRIPTION_ACTIVATED->value
                ? PaymentStatuses::SUBSCRIPTION_ACTIVATED->value
                : PaymentStatuses::SUBSCRIPTION_RENEWED->value;
        }

        $payment->status = $newStatus;
        $payment->acknowledged = true;

        if ($payment->save()) {
            $user->activateBot($tariff->type, $total, $commission);
            $user->notifyUser();
            $payment->incrementPromoCodeCount();

            return 'ok';
        } else {
            ErrorLogHelper::logPaymentInfo(json_encode($payment->errors, JSON_THROW_ON_ERROR), 'Ошибка сохранения платежа');
            return 'error';
        }
    }
}