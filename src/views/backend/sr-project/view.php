<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\SRProjects\helpers\SRProjectHelper;
use Besnovatyj\Images\widgets\upload\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $srProject SRProject */
/* @var $absoluteFrontendUrl string */

$this->title = $srProject->name;
$this->params['breadcrumbs'][] = ['label' => 'Проекты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php if ($srProject->isActive()): ?>
        <?= Html::a('Выключить', ['draft', 'id' => $srProject->id], ['class' => 'btn  btn-warning', 'data-method' => 'post']) ?>
    <?php else: ?>
        <?= Html::a('Включить', ['activate', 'id' => $srProject->id], ['class' => 'btn  btn-success', 'data-method' => 'post']) ?>
    <?php endif; ?>
    <?= Html::a('Редактировать', ['update', 'id' => $srProject->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Удалить', ['delete', 'id' => $srProject->id], [
        'class' => 'btn  btn-danger',
        'data'  => [
            'confirm' => 'Вы уверены?',
            'method'  => 'post',
        ],
    ]) ?>

    <a class="btn  btn-secondary" target="_blank"
       href="<?= $absoluteFrontendUrl; ?>">
        <i class="bi bi-eye"></i>
    </a>
</p>

<div class="container">
    <div class="row">
        <div class="col-sm">
            <!--COMMON-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Общее</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-common" role="button"
                       aria-expanded="true" aria-controls="collapseCommon">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-common">
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model'      => $srProject,
                            'attributes' => [
                                'id',
                                [
                                    'attribute' => 'status',
                                    'value'     => SRProjectHelper::statusLabel($srProject),
                                    'format'    => 'raw',
                                ],
                                'name',
                                [
                                    'attribute' => 'category_id',
                                    'value'     => ArrayHelper::getValue($srProject, 'category.name'),
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <!--SEO-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">SEO</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-SEO" role="button"
                       aria-expanded="true" aria-controls="collapseSEO">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-SEO">
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model'      => $srProject,
                            'attributes' => [
                                [
                                    'attribute' => 'meta.title',
                                    'value'     => $srProject->meta->title,
                                ],
                                [
                                    'attribute' => 'meta.description',
                                    'value'     => $srProject->meta->description,
                                ],
                                [
                                    'attribute' => 'meta.keywords',
                                    'value'     => $srProject->meta->keywords,
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            <!--DESCRIPTION-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Описание</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-description" role="button"
                       aria-expanded="true" aria-controls="collapseDescription">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-description">
                    <div class="card-body">
                        <?= Yii::$app->formatter->asHtml($srProject->description, [
                            'Attr.AllowedRel'        => array('nofollow'),
                            'HTML.SafeObject'        => true,
                            'Output.FlashCompat'     => true,
                            'HTML.SafeIframe'        => true,
                            'URI.SafeIframeRegexp'   => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <!--IMAGES-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Изображения</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-images" role="button"
                       aria-expanded="true" aria-controls="collapseImages">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-images">
                    <div class="card-body">
                        <?= Widget::widget([
                            'ownerId'   => $srProject->id,
                            'endpoints' => [
                                'getImages'    => Url::to(['/SRProjects/backend/sr-project/get-images'], true),
                                'setNewSort'   => Url::to(['/SRProjects/backend/sr-project/set-new-sort'], true),
                                'upload'       => Url::to(['/SRProjects/backend/sr-project/add-image'], true),
                                'deleteImage'  => Url::to(['/SRProjects/backend/sr-project/delete-image'], true),
                                'setMainImage' => '/SRProjects/backend/sr-project/set-main-image',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
