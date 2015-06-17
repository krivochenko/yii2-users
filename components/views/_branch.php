<?php foreach ($children as $child) : ?>
    <div class="list-group">
        <div class="list-group-item list-group-item-success">
            <h4 class="list-group-item-heading"><?= $child->description?></h4>
            <?php
            $children = Yii::$app->authManager->getChildren($child->name);
            if (count($children)) {
                echo $this->context->render('_branch', ['children' => $children]);
            }
            ?>
        </div>
    </div>
<?php endforeach;?>