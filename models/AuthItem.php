<?php

namespace budyaga\users\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 */
class AuthItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type'], 'integer'],
            [['name'], 'unique'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('users', 'RBAC_NAME'),
            'description' => Yii::t('users', 'RBAC_DESCRIPTION'),
            'rule_name' => Yii::t('users', 'RBAC_RULE_NAME'),
            'data' => Yii::t('users', 'RBAC_DATA'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->via('authItemChildren');
    }

    public function getNotChildren()
    {
        $query = AuthItem::find()->where(['not in', 'name', ArrayHelper::getColumn($this->children, 'name')])->andWhere(['<>', 'name', $this->name]);
        if ($this->type == Item::TYPE_PERMISSION) {
            $query->andWhere(['type' => Item::TYPE_PERMISSION]);
        }

        return $query->all();
    }
}
