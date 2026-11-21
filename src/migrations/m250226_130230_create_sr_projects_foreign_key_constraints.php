<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use Yii;
use yii\db\Exception;

class m250226_130230_create_sr_projects_foreign_key_constraints extends BaseMigration
{

    /**
     * @throws Exception
     */
    public function safeUp(): void
    {
        parent::safeUp();

        Yii::$app->getDb()->createCommand("SET foreign_key_checks = 0")->execute();

        // Изображения
        $this->createFKs(
            m250226_130210_create_sr_projects_images_table::TABLE_NAME,
            'sr_project_id',
            m250226_130215_create_sr_projects_projects_table::TABLE_NAME,
            'id',
            'CASCADE',
            'CASCADE',
        );

        // Галереи
        $this->createFKs(
            m250226_130215_create_sr_projects_projects_table::TABLE_NAME,
            'category_id',
            m250226_130200_create_sr_projects_categories_table::TABLE_NAME,
            'id',
        );
        $this->createFKs(
            m250226_130215_create_sr_projects_projects_table::TABLE_NAME,
            'main_image_id',
            m250226_130210_create_sr_projects_images_table::TABLE_NAME,
            'id',
            'SET NULL',
        );

        Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

    }

    public function safeDown(): void
    {
        // Отменяем действия по умолчанию,
        // так как \Besnovatyj\Kernel\migration\BaseMigration::safeDown() вызывает static::TABLE_NAME,
        // которого в данной миграции не существует.
        // Так же, \Besnovatyj\Kernel\migration\BaseMigration::safeDown() при удалении таблиц сам удалит у них все индексы и внешние ключи.

        // parent::safeDown();
    }

}
