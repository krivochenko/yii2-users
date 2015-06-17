<?php
$auth = Yii::$app->authManager;
foreach ($user->assignments as $assignment) : ?>
<div class="list-group">
    <div class="list-group-item list-group-item-info">
        <h4 class="list-group-item-heading"><?= $assignment->itemName->description?></h4>
        <?php
            $children = $auth->getChildren($assignment->itemName->name);
            if (count($children)) {
                echo $this->context->render('_userPermissionsLevel', ['children' => $children]);
            }
        ?>
    </div>
</div>
<?php endforeach;?>

<?php foreach ($defaultAssignments as $assignment) : ?>
<div class="list-group">
    <div class="list-group-item list-group-item-info">
        <h4 class="list-group-item-heading"><?= $assignment->description?></h4>
        <?php
        $children = $auth->getChildren($assignment->name);
        if (count($children)) {
            echo $this->context->render('_userPermissionsLevel', ['children' => $children]);
        }
        ?>
    </div>
</div>
<?php endforeach;?>


