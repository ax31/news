<?php

use yii\helpers\Html;

$content = $model->content;
$truncateContent = $model->getTruncateContent();
$likeClass = $model->getLikeClass();
?>

<div class="panel panel-default" data-id="<?= $model->primaryKey ?>">
    <div class="panel-body">
        <?php if ($model->image): ?>
            <img src="<?= $model->image ?>">
        <?php endif; ?>
        <h2><?= Html::encode($model->title); ?></h2>
        <?php if (!empty($truncateContent)): ?>
            <p class="truncate-content"><?= nl2br(Html::encode($truncateContent)); ?></p>
            <p>
                <button class="btn btn-info show-more">Показать полностью</button>
            </p>
            <p class="content hidden"><?= nl2br(Html::encode($content)); ?></p>
        <?php else: ?>
            <p class="content"><?= nl2br(Html::encode($content)); ?></p>
        <?php endif; ?>
        <p><?= $model->created_at ?></p>
        <button class="btn btn-<?= $likeClass ?> like">Мне нравится</button>
    </div>
</div>
