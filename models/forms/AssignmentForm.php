<?php
namespace budyaga\users\models\forms;

use Yii;
use yii\base\Model;
use yii\rbac\Item;

class AssignmentForm extends Model
{
    public $model;
    public $assigned;
    public $unassigned;
    public $target;
    public $action;

    public function rules()
    {
        return [
            [['assigned', 'unassigned', 'action'], 'safe'],
            [['action'], 'required'],
            [['unassigned'], 'noEmpty', 'skipOnEmpty' => false, 'params' => ['action' => 'assign']],
            [['assigned'], 'noEmpty', 'skipOnEmpty' => false, 'params' => ['action' => 'revoke']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'assigned' => Yii::t('users', 'RBAC_ASSIGNED'),
            'unassigned' => Yii::t('users', 'RBAC_UNASSIGNED'),
        ];
    }

    public function noEmpty($attribute, $parameters)
    {
        if ($this->action == $parameters['action']) {
            $items = $this->{$attribute};
            if (!is_array($items) || !count($items)) {
                $this->addError($attribute, Yii::t('users', 'CHOOSE_ITEMS'));
            }
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->target) {
            if ($this->action == 'assign') {
                return $this->saveChildren($this->unassigned, 'addChild');
            } else {
                return $this->saveChildren($this->assigned, 'removeChild');
            }
        } else {
            if ($this->action == 'assign') {
                return $this->saveAssignments($this->unassigned, 'assign');
            } else {
                return $this->saveAssignments($this->assigned, 'revoke');
            }
        }

        return true;
    }

    protected function saveChildren($items, $method)
    {
        $allSuccess = true;
        $auth = Yii::$app->authManager;

        foreach ($items as $item) {
            $allSuccess = $allSuccess && $auth->$method($this->target, $this->getItem($item));
        }

        return $allSuccess;
    }

    protected function saveAssignments($items, $method)
    {
        $allSuccess = true;
        $auth = Yii::$app->authManager;

        foreach ($items as $item) {
            $allSuccess = $allSuccess && $auth->$method($this->getItem($item), $this->model->id);
        }

        return $allSuccess;
    }

    protected function getItem($serialize)
    {
        $item = unserialize($serialize);
        $auth = Yii::$app->authManager;
        return ($item[1] == Item::TYPE_ROLE) ? $auth->getRole($item[0]) : $auth->getPermission($item[0]);
    }
}
