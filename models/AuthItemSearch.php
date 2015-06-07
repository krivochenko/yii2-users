<?php

namespace budyaga\users\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\rbac\Item;

/**
 * AuthItemSearch represents the model behind the search form about `budyaga\users\models\AuthItem`.
 */
class AuthItemSearch extends AuthItem
{
    private $_formName;

    public function setFormName($value)
    {
        $this->_formName = $value;
    }

    public function formName()
    {
        return $this->_formName;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'name'], 'safe'],
            [['type'], 'integer'],
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
        $query = AuthItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $this->_formName);
        if ($this->_formName != 'AuthItemSearch') {
            $this->type = ($this->_formName == 'RolesSearch') ? Item::TYPE_ROLE : Item::TYPE_PERMISSION;
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['type' => $this->type])->andFilterWhere(['like', 'description', $this->description])->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
