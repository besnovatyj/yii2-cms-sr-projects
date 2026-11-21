<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\SRProjects\forms\backend\srProject\SRProjectForm;
use yii\web\View;

/* @var $this View */
/* @var $srProject SRProject */
/* @var $model SRProjectForm */

$this->title = 'Редактировать проект: ' . $srProject->name;
$this->params['breadcrumbs'][] = ['label' => 'Проекты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $srProject->name, 'url' => ['view', 'id' => $srProject->id]];
$this->params['breadcrumbs'][] = 'Редактировать';

echo $this->render('_form', [
    'model' => $model,
    'srProject' => $srProject,
]);
