<?php

namespace budyaga\users\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use budyaga\users\models\AuthRule;

/**
 * AuthRuleSearch represents the model behind the search form about `budyaga\users\models\AuthRule`.
 */
class AuthRuleSearch extends AuthRule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = AuthRule::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
