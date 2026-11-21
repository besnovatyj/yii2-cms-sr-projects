<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\readModels;

use Besnovatyj\SRProjects\entities\Category;
use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\Expression;

class SRProjectReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Category::class);
    }

    public function count(): int
    {
        return SRProject::find()->visible()->count();
    }

    public function getAllByRange(int $offset, int $limit): array
    {
        return SRProject::find()->alias('p')->visible('p')->orderBy(['created_at' => SORT_ASC])->limit($limit)->offset($offset)->all();
    }

    public function getAllIterator(): iterable
    {
        return SRProject::find()->alias('p')->visible('p')->with('mainImage')->each();
    }

    public function getAll(): DataProviderInterface
    {
        $query = SRProject::find()->alias('p')->visible('p')->with('mainImage');
        return $this->getProvider($query);
    }

    public function getAllByCategory(Category $category): DataProviderInterface
    {
        $query = SRProject::find()->alias('p')->visible('p')->with('mainImage', 'category');
        $ids = $this->treeScope->descendantIds($category, andSelf: true);
        $query->andWhere(['p.category_id' => $ids]);
        $query->groupBy('p.id');
        return $this->getProvider($query);
    }

    public function getRand($limit): array
    {
        return SRProject::find()->visible()->orderBy(new Expression('rand()'))->limit($limit)->all();
    }

    public function find(int $id): ?SRProject
    {
        /** @var $srProject SRProject */
        $srProject = SRProject::find()->visible()->andWhere(['id' => $id])->one();
        return $srProject;
    }

    private function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
//                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'created_at' => [
                        'asc' => ['p.created_at' => SORT_ASC],
                        'desc' => ['p.created_at' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['p.name' => SORT_ASC],
                        'desc' => ['p.name' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSizeLimit' => [15, 100],
                'pageSize' => 12,
            ]
        ]);
    }

    /**
     * Карта «id => заголовок» опубликованных проектов — для выбора цели URL-алиаса и пункта меню
     * в админке.
     *
     * @return array<string,string>
     */
    public function idTitleMap(): array
    {
        return SRProject::find()->visible()
            ->select(['title', 'id'])
            ->orderBy(['title' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }
}
