<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\SRProjects\forms\backend\srProject\SRProjectForm;
use yii\web\View;

/* @var $this View */
/* @var $model SRProjectForm */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Проекты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_form', ['model' => $model]);
