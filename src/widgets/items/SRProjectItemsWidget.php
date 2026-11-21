<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\widgets\items;

use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\SRProjects\readModels\SRProjectReadRepository;
use Yii;
use yii\base\Widget;

/**
 * Виджет возвращает массив объектов фотографий конкретного проекта без какого-либо форматирования,
 * в отличие от Besnovatyj\SRProjects\widgets\SRProjectWidget
 */
class SRProjectItemsWidget extends Widget
{
    public string $srProjectId;
    public array $images = [];
    public SRProjectReadRepository $repo;

    public function __construct(SRProjectReadRepository $repo, $config = [])
    {
        $this->repo = $repo;
        parent::__construct($config);
    }

    public function init(): void
    {
        parent::init();
        if (!Yii::$app->getModule('SRProjects')) {
            return;
        }
        $srProject = $this->repo->find($this->srProjectId);
        if ($srProject instanceof SRProject) {
            $this->images = $srProject->images;
        }
    }

}
