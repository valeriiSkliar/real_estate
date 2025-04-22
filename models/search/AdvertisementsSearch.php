<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Advertisements;

/**
 * AdvertisementsSearch represents the model behind the search form of `app\models\Advertisements`.
 */
class AdvertisementsSearch extends Advertisements
{
    public $updated_at_start;
    public $updated_at_end;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'room_quantity', 'property_area', 'land_area', 'price'], 'integer'],
            [['property_type', 'trade_type', 'source', 'realtor_phone', 'address', 'clean_description', 'raw_description', 'property_name', 'locality', 'district', 'condition', 'created_at', 'updated_at', 'updated_at_start', 'updated_at_end'], 'safe'],
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
        $query = Advertisements::find();

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
            'room_quantity' => $this->room_quantity,
            'property_area' => $this->property_area,
            'land_area' => $this->land_area,
            'price' => $this->price,
        ]);

        $query->andFilterWhere(['like', 'property_type', $this->property_type])
            ->andFilterWhere(['like', 'trade_type', $this->trade_type])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'realtor_phone', $this->realtor_phone])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'clean_description', $this->clean_description])
            ->andFilterWhere(['like', 'raw_description', $this->raw_description])
            ->andFilterWhere(['like', 'property_name', $this->property_name])
            ->andFilterWhere(['like', 'locality', $this->locality])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'condition', $this->condition]);

        if($this->updated_at_start){
            $startDate = $this->updated_at_start;
            $query->andFilterWhere(['>=', 'updated_at', $startDate]);
        }

        if($this->updated_at_end){
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($this->updated_at_end)));
            $query->andFilterWhere(['<', 'updated_at', $endDate]);
        }

        return $dataProvider;
    }
}
