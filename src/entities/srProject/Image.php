<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\SRProjects\entities\srProject;

use Besnovatyj\Images\base\BaseImage;

/**
 * Изображение галереи.
 *
 * @property int $id
 * @property int $srProject_id
 * @property string $file
 * @property int $sort
 */
class Image extends BaseImage
{
    /**
     * {@inheritdoc}
     */
    protected static function getParentAttribute(): string
    {
        return 'sr_project_id';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getStorageName(): string
    {
        return 'SRProjects';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getThumbProfiles(): array
    {
        return [
            'admin'          => ['width' => 70,   'height' => 100], // /backend/gallery/index
            'thumb'          => ['width' => 640,  'height' => 480], // /backend/gallery/view
            'gallery_widget' => ['width' => 1200, 'height' => 600], // /frontend/gallery/widget
            'frontend_list'  => ['width' => 1200, 'height' => 600], // /frontend/gallery/index
            'frontend_item'  => ['width' => 1200, 'height' => 600], // /frontend/gallery/view
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%sr_projects_images}}';
    }

}
