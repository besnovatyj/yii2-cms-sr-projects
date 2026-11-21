<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\entities\srProject\queries;

use Besnovatyj\SRProjects\entities\Category;
use Besnovatyj\SRProjects\entities\srProject\SRProject;
use yii\db\ActiveQuery;

class SRProjectQuery extends ActiveQuery
{
    /**
     * @param null|string $alias
     * @return $this
     */
    public function active(?string $alias = null): static
    {
        return $this->andWhere([
            ($alias ? $alias . '.' : '') . 'status' => SRProject::STATUS_ACTIVE,
        ]);
    }

    /**
     * Галерея доступна анонимному посетителю: опубликована сама И лежит в видимой категории.
     *
     * Одной публикации мало: скрытая категория не должна «протекать» на фронт своими галереями —
     * категория проверяется целиком, вместе с предками
     * (см. {@see \Besnovatyj\SRProjects\entities\queries\CategoryQuery::visible()}).
     * Галерея без категории (`category_id` NULL) видна: скрывать её не за что.
     *
     * @param string|null $alias алиас таблицы галерей, если запрос строится с `alias()`
     */
    public function visible(?string $alias = null): static
    {
        $column = ($alias ? $alias . '.' : '') . 'category_id';

        return $this->active($alias)->andWhere([
            'or',
            [$column => null],
            [$column => Category::find()->visible()->select('id')],
        ]);
    }
}
