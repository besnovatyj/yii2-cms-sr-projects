<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\forms\backend\srProject;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Meta\MetaForm;
use Besnovatyj\SRProjects\entities\srProject\SRProject;

/**
 * @property MetaForm $meta
 * @property CategoriesForm $categories
 */
class SRProjectForm extends CompositeForm
{
    public string $name = '';
    public string $description = '';
    public int|null $status = null;

    public function __construct(?SRProject $srProject = null, $config = [])
    {
        if ($srProject) {
            $this->name = $srProject->name;
            $this->description = $srProject->description;
            $this->status = $srProject->status;
            $this->meta = new MetaForm($srProject->meta);
            $this->categories = new CategoriesForm($srProject);
        } else {
            $this->meta = new MetaForm();
            $this->categories = new CategoriesForm();
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name', 'status'], 'required'],
            [['name'], 'string', 'max' => 255],
            ['description', 'string'],
            ['status', 'integer'],
            ['status', 'in', 'range' => [SRProject::STATUS_DRAFT, SRProject::STATUS_ACTIVE]],
        ];
    }

    protected function internalForms(): array
    {
        return ['meta', 'categories'];
    }

    public function statusList(): array
    {
        return [
            SRProject::STATUS_DRAFT => 'ВЫКЛ',
            SRProject::STATUS_ACTIVE => 'ВКЛ',
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'description' => 'Описание',
            'status' => 'Статус',
        ];
    }
}
