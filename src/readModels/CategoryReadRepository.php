<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\readModels;

use Besnovatyj\SRProjects\entities\Category;

class CategoryReadRepository
{

    /**
     * Корневая категория дерева — только видимая: корень такая же полноценная категория, как
     * остальные, и снятая с публикации показываться не должна.
     *
     * Тип возврата стал nullable: скрытого (или отсутствующего) корня теперь может не быть, и
     * вызывающая сторона обязана это учитывать — раньше метод молча возвращал бы `null` вопреки
     * объявленному типу.
     */
    public function getRoot(): ?Category
    {
        // TODO - Что за метод? Теперь много корней деревьев
        return Category::find()->visible()->andWhere(['depth' => 0])->one();
    }

    /**
     * @return Category[]
     */
    public function getAll(): array
    {
        return Category::find()->visible()->orderBy('lft')->all();
    }

    public function find($id): ?Category
    {
        return Category::find()->visible()->andWhere(['id' => $id])->one();
    }

    /**
     * Категория по slug для фронтенда — только доступная анонимному посетителю: снятая с
     * публикации (или лежащая в скрытой ветке) не должна открываться по прямой ссылке.
     */
    public function findBySlug($slug): ?Category
    {
        return Category::find()->visible()->andWhere(['slug' => $slug])->one();
    }

}
