<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BotUsers;

/**
 * BotUsersSearch represents the model behind the search form of `app\models\BotUsers`.
 */
class BotUsersSearch extends BotUsers
{
    public $paid_until_start;
    public $trial_until_start;
    public $paid_until_end;
    public $trial_until_end;
    public $last_visited_at_start;
    public $last_visited_at_end;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'status', 'role_id', 'bonus', 'is_paid', 'tariff'], 'integer'],
            [['username', 'fio', 'created_at', 'phone', 'notification_on', 'email', 'language', 'payment_email'], 'safe'],
            [['paid_until_start', 'paid_until_end'], 'safe'],
            [['trial_until_start', 'trial_until_end'], 'safe'],
            [['last_visited_at_start', 'last_visited_at_end'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BotUsers::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'uid' => $this->uid,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'role_id' => $this->role_id,
            'notification_on' => $this->notification_on,
            'bonus' => $this->bonus,
            'is_paid' => $this->is_paid,
            'tariff' => $this->tariff,
        ]);

        if($this->paid_until_start){
            $startDate = $this->paid_until_start;
            $query->andFilterWhere(['>=', 'paid_until', $startDate]);
        }

        if($this->paid_until_end){
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($this->paid_until_end)));
            $query->andFilterWhere(['<', 'paid_until', $endDate]);
        }

        if($this->trial_until_start){
            $startDate = $this->trial_until_start;
            $query->andFilterWhere(['>=', 'trial_until', $startDate]);
        }

        if($this->trial_until_end){
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($this->trial_until_end)));
            $query->andFilterWhere(['<', 'trial_until', $endDate]);
        }

        if($this->last_visited_at_start){
            $startDate = $this->last_visited_at_start;
            $query->andFilterWhere(['>=', 'last_visited_at', $startDate]);
        }

        if($this->last_visited_at_end){
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($this->last_visited_at_end)));
            $query->andFilterWhere(['<', 'last_visited_at', $endDate]);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'payment_email', $this->payment_email])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
