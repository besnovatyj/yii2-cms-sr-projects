<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Contracts\adminMenu\AdminMenuLocation;
use Besnovatyj\Contracts\adminMenu\AdminMenuPlacement;

return [
    // Проекты
    [
        'label' => 'Проекты',
        'iconClass' => 'bi bi-images me-1',
        'url' => ['/SRProjects/backend/sr-project/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'SRProjects/backend/sr-project');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::LeftSidebar,
                    group: 'Проекты',
                    groupIcon: 'bi bi-image',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],
    // Categories
    [
        'label' => 'Категории',
        'iconClass' => 'bi bi-diagram-3 me-1',
        'url' => ['/SRProjects/backend/category/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'SRProjects/backend/category');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::LeftSidebar,
                    group: 'Проекты',
                    groupIcon: 'bi bi-image',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],
];
