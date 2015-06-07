<?php

namespace budyaga\users\controllers;

use budyaga\users\models\AuthItemSearch;
use budyaga\users\models\AuthRuleSearch;
use budyaga\users\models\forms\AssignmentForm;
use Yii;
use yii\rbac\Item;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class RbacController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'delete', 'children'],
                        'roles' => ['rbacManage'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $rolesSearchModel = new AuthItemSearch();
        $rolesSearchModel->formName = 'RolesSearch';

        $permissionsSearchModel = new AuthItemSearch();
        $permissionsSearchModel->formName = 'PermissionsSearch';

        $rulesSearchModel = new AuthRuleSearch();

        return $this->render('index', [
            'rolesSearchModel' => $rolesSearchModel,
            'permissionsSearchModel' => $permissionsSearchModel,
            'rulesSearchModel' => $rulesSearchModel
        ]);
    }

    public function actionCreate($type = null)
    {
        $className = 'budyaga\\users\\models\\' . $this->getModelName($type);
        $model = new $className;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $auth = Yii::$app->authManager;

            if ($type != null) {
                $entity = ($type == Item::TYPE_ROLE) ? $auth->createRole($model->name) : $auth->createPermission($model->name);
                $entity->description = $model->description;
                $entity->data = $model->data;
                $entity->ruleName = ($model->rule_name != '') ? $model->rule_name : null;
            } else {
                $entity = new $model->name;
            }

            if ($auth->add($entity)) {
                Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_ADD', ['type' => $this->getModelTypeTitle($type), 'name' => $entity->name]));
            } else {
                Yii::$app->session->setFlash('success', Yii::t('users', 'ERROR_ADD', ['type' => $this->getModelTypeTitle($type)]));
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'type' => $type
            ]);
        }
    }

    public function actionUpdate($id, $type)
    {
        $className = 'budyaga\\users\\models\\' . $this->getModelName($type);
        $model = $this->findModel($id, $className);
        $auth = Yii::$app->authManager;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $entity = $this->findAuthEntity($id, $type);
            $entity->name = $model->name;
            $entity->description = $model->description;
            $entity->data = $model->data;
            $entity->ruleName = ($model->rule_name != '') ? $model->rule_name : null;

            if ($auth->update($id, $entity)) {
                Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_UPDATE', ['type' => $this->getModelTypeTitle($type), 'name' => $entity->name]));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('users', 'ERROR_UPDATE', ['type' => $this->getModelTypeTitle($type), 'name' => $entity->name]));
            }
            return $this->redirect(['index']);
        } else {
            if ($type == null) {
                $data = unserialize($model->data);
                $model->name = $data::className();
            }
            return $this->render('update', [
                'model' => $model,
                'type' => $type
            ]);
        }
    }

    public function actionDelete($id, $type = NULL)
    {
        $entity = $this->findAuthEntity($id, $type);

        if (Yii::$app->authManager->remove($entity)) {
            Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_DELETE', ['type' => $this->getModelTypeTitle($type), 'name' => $entity->name]));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('users', 'ERROR_DELETE', ['type' => $this->getModelTypeTitle($type), 'name' => $entity->name]));
        }

        return $this->redirect(['index']);
    }

    public function actionChildren($id, $type)
    {
        $modelForm = new AssignmentForm;
        $modelForm->model = $this->findModel($id, 'budyaga\\users\\models\\AuthItem');
        $modelForm->target = $this->findAuthEntity($id, $type);

        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            return $this->redirect(Yii::$app->request->url);
        }

        return $this->render('children', [
            'modelForm' => $modelForm
        ]);
    }

    protected function findModel($id, $modelName)
    {
        if (($model = $modelName::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function getModelName($type)
    {
        return ($type === null) ? 'AuthRule' : 'AuthItem';
    }

    public function getModelTypeTitle($type = null)
    {
        if ($type == null) {
            return Yii::t('users', 'RULE');
        } else {
            return Yii::t('users', ($this->actionParams['type'] == Item::TYPE_PERMISSION) ? 'PERMISSION' : 'ROLE');
        }
    }

    protected function findAuthEntity($id, $type)
    {
        $auth = Yii::$app->authManager;
        if ($type == null) {
            $entity = $auth->getRule($id);
        } else {
            $entity = ($type == Item::TYPE_ROLE) ? $auth->getRole($id) : $auth->getPermission($id);
        }

        if ($entity) {
            return $entity;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
