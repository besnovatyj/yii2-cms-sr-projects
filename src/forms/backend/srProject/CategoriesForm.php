<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\forms\backend\SRProject;

use Besnovatyj\SRProjects\entities\Category;
use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\base\Model;

class CategoriesForm extends Model
{
    public int|null $main = null;

    public function __construct(?SRProject $srProject = null, $config = [])
    {
        if ($srProject) {
            $this->main = $srProject->category_id;
        }
        parent::__construct($config);
    }

    public function categoriesList(): array
    {
        $scope = new TreeQueryScope(Category::class);
        return $scope->dropdownTree();
    }

    public function rules(): array
    {
        return [
            ['main', 'required'],
            ['main', 'integer'],
        ];
    }
}
