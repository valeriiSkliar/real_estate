<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "referrals".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int|null $referral_id
 * @property string|null $created_at
 * @property string|null $parent_username
 * @property string|null $referral_username
 */
class Referrals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'referrals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'referral_id'], 'required'],
            [['parent_id', 'referral_id'], 'integer'],
            [['created_at', 'parent_username', 'referral'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'referral_id' => 'Referral ID',
            'created_at' => 'Добавлен',
            'parent_username' => 'Parent username',
            'referral_username' => 'Referral username',
        ];
    }
    public function getReferralParent()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'parent_id']);
    }

    public function getReferral()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'referral_id']);
    }

    /**
     * @param array $users
     * @param int $userId
     * @param bool $parent
     * Генерирует файл с рефералами
     * @return string
     */
    public static function generateCsv(array $users, int $userId, bool $parent = false): string
    {
        $directory = \Yii::getAlias('@webRoot/uploads/csv/');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true); // Create the directory if it doesn't exist
        }

        $filename = $directory . $userId . 'users.csv';
        $file = fopen($filename, 'w');
        if($parent){
            foreach ($users as $user){
                if(isset($user['referrals']) && count($user['referrals']) > 0){
                    fputcsv($file, ['---', '---']);
                    fputcsv($file, [$user['uid'], $user['username']]);
                    fputcsv($file, ['Id', 'Username', 'Name']);
                    foreach ($user['referrals'] as $referral) {
                        fputcsv($file, [$referral['uid'], $referral['username'], $referral['fio']]);
                    }
                }
            }
        } else {
            fputcsv($file, ['Id', 'Username', 'Name']);
            foreach ($users as $user) {
                fputcsv($file, [$user['uid'], $user['username'], $user['fio']]);
            }
        }

        fclose($file);

        return $filename;
    }
}
