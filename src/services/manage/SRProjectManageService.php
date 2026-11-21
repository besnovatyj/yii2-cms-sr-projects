<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\SRProjects\services\manage;

use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\SRProjects\forms\backend\srProject\SRProjectForm;
use Besnovatyj\SRProjects\repositories\CategoryRepository;
use Besnovatyj\SRProjects\repositories\SRProjectRepository;
use Besnovatyj\Meta\Meta;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * Сервис управления проектами.
 *
 * Отвечает за CRUD проектов.
 * Логика загрузки/удаления изображений вынесена в standalone actions
 * через пакет besnovatyj/yii2-cms-images + SRProjectImageOwner.
 */
class SRProjectManageService
{
    private SRProjectRepository $projects;
    private CategoryRepository $categories;

    public function __construct(
        SRProjectRepository  $projects,
        CategoryRepository $categories,
    )
    {
        $this->projects = $projects;
        $this->categories = $categories;
    }

    /**
     * @param SRProjectForm $form
     * @return SRProject
     * @throws Throwable
     */
    public function create(SRProjectForm $form): SRProject
    {
        $category = $this->categories->get($form->categories->main);

        $srProject = SRProject::create(
            $form->name,
            $form->description,
            $category->id,
            $form->status,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->projects->save($srProject);
            $transaction->commit();
            return $srProject;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     * @param SRProjectForm $form
     * @throws Throwable
     */
    public function edit(int $id, SRProjectForm $form): void
    {
        $srProject = $this->projects->get($id);
        $category = $this->categories->get($form->categories->main);

        $srProject->edit(
            $form->name,
            $form->description,
            $form->status,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        $srProject->changeMainCategory($category->id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->projects->save($srProject);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function remove(int $id): void
    {
        $srProject = $this->projects->get($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->removeImages($srProject);
            $this->projects->remove($srProject);
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function activate(int $id): void
    {
        $srProject = $this->projects->get($id);
        $srProject->activate();
        $this->projects->save($srProject);
    }

    /**
     * @throws Exception
     */
    public function draft(int $id): void
    {
        $srProject = $this->projects->get($id);
        $srProject->draft();
        $this->projects->save($srProject);
    }

    // ==================== Private methods ====================

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    private function removeImages(SRProject $srProject): void
    {
        foreach ($srProject->images as $image) {
            $image->delete();
        }
    }

}
