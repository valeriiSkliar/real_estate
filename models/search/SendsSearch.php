<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sends;

/**
 * SendsSearch represents the model behind the search form of `app\models\Sends`.
 */
class SendsSearch extends Sends
{
    public $date_start;
    public $date_end;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_regular', 'provider'], 'integer'],
            [['title', 'description', 'destination'], 'safe'],
            [['date_start', 'date_end'], 'safe'],
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
        $query = Sends::find();

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
            'is_regular' => $this->is_regular,
            'provider' => $this->provider,
            'status' => $this->status,
        ]);

        if($this->date_start){
            $startDate = $this->date_start;
            $query->andFilterWhere(['>=', 'date', $startDate]);
        }

        if($this->date_end){
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($this->date_end)));
            $query->andFilterWhere(['<', 'date', $endDate]);
        }

        $query->andFilterWhere(['like', 'destination', $this->destination]);

        return $dataProvider;
    }
}
