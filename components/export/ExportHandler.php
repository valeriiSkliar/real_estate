<?php

namespace app\components\export;

use app\enums\PaymentStatuses;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Referrals;
use app\models\Tariffs;
use yii\base\Exception;

class ExportHandler
{
    private CsvExportService $csvExportService;

    public function __construct(CsvExportService $csvExportService)
    {
        $this->csvExportService = $csvExportService;
    }

    /**
     * Универсальный метод для подготовки данных и экспорта CSV.
     *
     * @param string $type Тип экспорта ('common' или 'referral')
     * @param string $filename Имя файла
     * @param bool $toString Определяет, экспортировать ли как строку или в файл
     * @return string Путь к файлу или CSV строка
     * @throws Exception
     */
    public function export(string $type, string $filename, bool $toString = false, array $filters = []): string
    {
        $dataProvider = match ($type) {
            'common' => $this->processCommonExport($filters),
            'referral' => $this->processReferralExport($filters),
            default => throw new Exception("Неизвестный тип экспорта: {$type}"),
        };

        return $this->csvExportService->prepareAndExport($filename, $dataProvider[0], $dataProvider[1], $toString);
    }

    /**
     * Возвращает CSV строку для общих данных.
     *
     * @param string $filename Имя файла
     * @return string CSV контент
     * @throws Exception
     */
    public function exportCommonToString(string $filename = 'export.csv', array $filters = []): string
    {
        return $this->export('common', $filename, true, $filters);
    }

    /**
     * Возвращает CSV строку для реферальных данных.
     *
     * @param string $filename Имя файла
     * @return string CSV контент
     * @throws Exception
     */
    public function exportReferralToString(string $filename = 'referral.csv', array $filters = []): string
    {
        return $this->export('referral', $filename, true, $filters);
    }

    /**
     * Экспортирует общие данные в CSV файл.
     *
     * @param string $filename Имя файла
     * @return string Путь к сохранённому файлу
     * @throws Exception
     */
    public function exportCommonToFile(string $filename = 'export.csv', array $filters = []): string
    {
        return $this->export('common', $filename, false, $filters);
    }

    /**
     * Экспортирует реферальные данные в CSV файл.
     *
     * @param string $filename Имя файла
     * @return string Путь к сохранённому файлу
     * @throws Exception
     */
    public function exportReferralToFile(string $filename = 'referral.csv', array $filters = []): string
    {
        return $this->export('referral', $filename, false, $filters);
    }

    private function processCommonExport(array $filters): array
    {
        $headers = [
            'e-mail',
            'ТГ id',
            'ТГ линк',
            'дата оплаты',
            'сервис оплаты',
            'Статус',
            'сумма',
            'получено',
            'продукт',
            'источник',
            'промокод',
        ];

        $query = Payments::find()
            ->joinWith(['user', 'tariff']);

        foreach ($filters as $value) {
            if ($value) {
                $query->andWhere($value);
            }
        }

        $payments = $query->all();
        $dataRows = [];

        /** @var Payments $payment */
        foreach ($payments as $payment) {
            /** @var BotUsers $user */
            $user = $payment->user;
            /** @var Tariffs $tariff */
            $tariff = $payment->tariff;

            $dataRows[] = [
                $user->payment_email,
                $user->uid,
                $user->username ?: $user->fio,
                $payment->created_at,
                $tariff->provider,
                PaymentStatuses::getPaymentName($payment->status),
                $payment->amount,
                $payment->amount - $tariff->fee,
                $tariff->name,
                'Telegram',
                $payment->promo_code,
            ];
        }

        return [$headers, $dataRows];
    }

    private function processReferralExport(): array
    {
        $headers = [
            'e-mail',
            'ТГ линк',
            'количество оплативших по ссылке за все время',
            'Дата последней выплаты',
            'количество оплативших за невыплаченный период',
            'сумма прихода (сколько оплатил клиент), $',
            'комиссия сервису, $',
            'сумма к выплате для реферала, $',
            'чистый доход (сумма прихода - комиссия - сумма к выплате), $',
        ];

        $users = BotUsers::find()
            ->where(['referral_id' => null])
            ->with(['latestWithdrawal'])
            ->all();

        $dataRows = [];

        /** @var BotUsers $user */
        foreach ($users as $user) {
            $latestWithdrawal = $user->latestWithdrawal;
            $latestWithdrawalDate = $latestWithdrawal?->created_at;
            $totalPaidReferralsInCurrentPeriod = 0;
            $totalPaidReferrals = 0;
            $totalPaid = 0;
            $totalFees = 0;

            $paidReferrals = Referrals::find()
                ->innerJoinWith(['referral r'])
                ->where(['parent_id' => $user->id])
                ->andWhere(['r.has_first_deposit' => true])
                ->all();

            foreach ($paidReferrals as $paidReferral) {
                $totalPaidReferrals++;

                if ($latestWithdrawalDate && $paidReferral->created_at >= $latestWithdrawalDate) {
                    $totalPaidReferralsInCurrentPeriod++;
                }

                $totalPaid += $paidReferral->referral->total_paid;
                $totalFees += $paidReferral->referral->total_fees;
            }

            $dataRows[] = [
                $user->payment_email,
                $user->username,
                $totalPaidReferrals,
                $latestWithdrawalDate,
                $totalPaidReferralsInCurrentPeriod,
                $totalPaid,
                $totalFees,
                $user->bonus,
                $totalPaid - $totalFees - $user->bonus,
            ];
        }

        return [$headers, $dataRows];
    }
}