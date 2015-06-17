<?php
$auth = Yii::$app->authManager;

foreach ($permissions as $permission) : ?>
<div class="list-group">
    <div class="list-group-item list-group-item-info">
        <h4 class="list-group-item-heading"><?= $permission->description?></h4>
        <?php
        $children = $auth->getChildren($permission->name);
        if (count($children)) {
            echo $this->context->render('_branch', ['children' => $children]);
        }
        ?>
    </div>
</div>
<?php endforeach;?>