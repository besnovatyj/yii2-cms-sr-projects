<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects;

use Besnovatyj\Contracts\menu\MenuTarget;
use Besnovatyj\Contracts\menu\MenuTargetProvider;
use Besnovatyj\Contracts\module\DeclaresModule;
use Besnovatyj\Contracts\module\ProvidesDirectories;
use Besnovatyj\Contracts\module\ProvidesMigrations;
use Besnovatyj\Contracts\routing\AliasTarget;
use Besnovatyj\Contracts\routing\AliasTargetProvider;
use Besnovatyj\Kernel\module\CmsModule;
use Besnovatyj\SRProjects\entities\Category;
use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\SRProjects\readModels\SRProjectReadRepository;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

class Module extends CmsModule implements
    DeclaresModule, AliasTargetProvider,
    ProvidesDirectories, ProvidesMigrations, MenuTargetProvider
{
    public const bool EDITABLE = true;
    public const string MODULE_ID = 'SRProjects';

    public static function moduleId(): string
    {
        return self::MODULE_ID;
    }

    public static function isEditable(): bool
    {
        return self::EDITABLE;
    }

    public static function moduleConfig(): array
    {
        return require __DIR__ . '/config/config.php';
    }

    public static function migrationPath(): string
    {
        return __DIR__ . '/migrations';
    }

    public static function migrationNamespace(): ?string
    {
        return __NAMESPACE__ . '\\migrations';
    }

    public static function directories(): array
    {
        return ['@static/origin/SRProject', '@static/cache/SRProject'];
    }

    /**
     * Цели, которым можно назначить короткий URL.
     * Реализация {@see AliasTargetProvider}; вызывается только модулем алиасов, если он установлен.
     * Слага у галереи нет: красивый адрес — алиас, а не поле сущности.
     * *
     * @return AliasTarget[]
     */
    public function aliasTargets(): array
    {
        return [
            new AliasTarget('/SRProject/sr-project/sr-project', 'Проекты', 'id'),
            new AliasTarget('/SRProject/sr-project/category', 'Раздел проектов', 'slug'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     *  Для галереи ключ — `id` (слага у галереи нет), подпись — азвание гадереи; список в ручном порядке
     *  сортировки, без фильтра публикации — алиас заводят и черновику. Для разделов — карта слагов дерева,
     *  та же, что у меню.
     * @return array<string,string>
     */
    public function aliasSlugs(string $route): array
    {
        return match (ltrim($route, '/')) {
            '/SRProject/sr-project/sr-project' => $this->projectsIdMap(),
            '/SRProject/sr-project/category' => $this->categorySlugMap(),
            default => [],
        };
    }

    /**
     * Карта `id => Название` всех проектов в порядке сортировки.
     *
     * @return array<string,string>
     */
    private function projectsIdMap(): array
    {
        $map = [];
        foreach (SRProject::find()->with('mainImage', 'category')->orderBy(['id' => SORT_ASC])->all() as $srProject) {
            /* @var SRProject $srProject */
            $map[(string)$srProject->id] = $srProject->name;
        }
        return $map;
    }

    /**
     * Цели для построения пунктов меню. Реализация {@see MenuTargetProvider};
     * вызывается только модулем меню, если он установлен.
     *
     * @return MenuTarget[]
     */
    public function menuTargets(): array
    {
        return [
            new MenuTarget('/SRProject/sr-project/category', 'Категория проектов', 'slug'),
            new MenuTarget('/SRProject/sr-project/sr-project', 'Проект', 'id'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string,string>
     */
    public function menuCandidates(string $route): array
    {
        return match (ltrim($route, '/')) {
            'SRProject/sr-project/category' => $this->categorySlugMap(),
            'SRProject/sr-project/sr-project' => new SRProjectReadRepository()->idTitleMap(),
            default => [],
        };
    }

    /**
     * Карта `slug => подпись` (с отступом по глубине дерева) для категорий галереи.
     *
     * @return array<string,string>
     */
    private function categorySlugMap(): array
    {
        return new TreeQueryScope(Category::class)->dropdownTree(indent: '— ', keyAttribute: 'slug');
    }

}
