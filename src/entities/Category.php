<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\entities;

use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\SRProjects\entities\queries\CategoryQuery;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\Meta\Meta;
use Besnovatyj\TreeManager\Manager\entities\Node;
use yii\db\ActiveQuery;

/**
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $tree
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property integer $status
 * @property int $sort_order - Порядок сортировки корневых узлов
 *
 * @property Meta $meta
 *
 * @mixin MetaBehavior
 */
class Category extends Node
{
    /** Категория снята с публикации: не показывается на фронте и не участвует в поиске. */
    public const int STATUS_INACTIVE = 0;

    /** Категория опубликована. */
    public const int STATUS_ACTIVE = 1;

    public Meta $meta;

    /**
     * Опубликована ли категория сама по себе (без учёта предков — см. {@see CategoryQuery::visible()}).
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public static function create($name, $slug, $description, Meta $meta): self
    {
        $category = new static();
        $category->name = $name;
        $category->slug = $slug;
        $category->description = $description;
        $category->meta = $meta;
        return $category;
    }

    public function edit($name, $slug, $description, Meta $meta): void
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->meta = $meta;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    public function changeStatus(): void
    {
        $this->status = !$this->status;
    }

    public function getProjects(): ActiveQuery
    {
        return $this->hasMany(SRProject::class, ['category_id' => 'id']);
    }

    public static function tableName(): string
    {
        return '{{%sr_projects_categories}}';
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            ...parent::behaviors(),
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find(): CategoryQuery
    {
        return new CategoryQuery(static::class);
    }
}
