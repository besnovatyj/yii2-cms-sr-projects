<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<n>' */
class m250226_130210_create_sr_projects_images_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%sr_projects_images}}';

    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'sr_project_id' => $this->integer()->notNull()
                ->comment('ID проекта.'),
            'file' => $this->string(255)->notNull()
                ->comment('Файл фотографии'),
            'sort' => $this->integer()->notNull()
                ->comment('Сортировка фотографии в конкретном проекте'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Изображения для проекта');

        $this->createIndexes(static::TABLE_NAME, 'sr_project_id');

        parent::safeUp();
    }

}
