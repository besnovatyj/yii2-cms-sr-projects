<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\SRProjects\entities\Category;
use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\base\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $srProject SRProject */

$this->title = $srProject->name;

$this->params['og:title'] = $this->title;

$category = $srProject->category;
$this->params['breadcrumbs'] = new TreeQueryScope(Category::class)->breadcrumbs($category, urlCallback: function ($item) use ($category) {
    if ($item->id !== $category->id) {
        return Url::to(['category', 'slug' => $item->slug]);
    }
    return false;
});

$this->registerMetaTag(['name' => 'title', 'content' => $srProject->getSeoTitle()]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $srProject->meta->keywords]);
$this->registerMetaTag(['name' => 'description', 'content' => $srProject->meta->description]);

if (Yii::$app->getModule('Config') instanceof Module) {
    $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->getModule('Config')->params['frontend']['app']['name']]);
}

?>

<section class="container mt-3 mb-5">
    <h1 class="h2 mb-4"><?= Html::encode($srProject->name) ?></h1>

    <div class="row g-4">
        <div class="col-12 col-md-5">
            <?php foreach ($srProject->images as $i => $image): ?>
                <?php if ($i === 0): ?>
                    <a href="<?= $image->getUploadUrl('file') ?>"
                       class="d-block rounded overflow-hidden shadow-sm" target="_blank" rel="noopener">
                        <img src="<?= $image->getThumbUrl('file', 'frontend_list') ?>"
                             class="img-fluid w-100" alt="<?= Html::encode($srProject->name) ?>"/>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="col-12 col-md-7">
            <?php if (trim($srProject->description)): ?>
                <div class="mb-3">
                    <?= Yii::$app->formatter->asHtml($srProject->description) ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mt-1">
        <?php foreach ($srProject->images as $i => $image): ?>
            <?php if ($i !== 0): ?>
                <div class="col">
                    <a href="<?= $image->getUploadUrl('file') ?>"
                       class="d-block ratio ratio-1x1 rounded overflow-hidden shadow-sm"
                       target="_blank" rel="noopener">
                        <img src="<?= $image->getThumbUrl('file', 'frontend_item') ?>"
                             class="object-fit-cover" alt="<?= Html::encode($srProject->name) ?>"/>
                    </a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
