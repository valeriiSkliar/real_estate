<?php

namespace app\models\search;

use app\models\BotUsers;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Referrals;

/**
 * ReferralsSearch represents the model behind the search form of `app\models\Referrals`.
 */
class ReferralsSearch extends Referrals
{
    public $created_at_start;
    public $created_at_end;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'referral_id'], 'integer'],
            [['created_at', 'parent_username', 'referral_username'], 'safe'],
            [['created_at_start', 'created_at_end'], 'safe'],
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
        $query = Referrals::find();

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
            'parent_username' => $this->parent_username,
            'referral_username' => $this->referral_username,
            'parent_id' => $this->parent_id,
           'referral_id' => $this->referral_id,
        ]);

        if($this->created_at_start){
            $startDate = $this->created_at_start;
            $query->andFilterWhere(['>=', 'created_at', $startDate]);
        }

        if($this->created_at_end){
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($this->created_at_end)));
            $query->andFilterWhere(['<', 'created_at', $endDate]);
        }

        return $dataProvider;
    }
}
