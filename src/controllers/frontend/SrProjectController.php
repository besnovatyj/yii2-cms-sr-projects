<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\controllers\frontend;

use Besnovatyj\SRProjects\readModels\CategoryReadRepository;
use Besnovatyj\SRProjects\readModels\SRProjectReadRepository;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SrProjectController extends Controller
{
    private SRProjectReadRepository $projects;
    private CategoryReadRepository $categories;

    public function __construct(
        $id,
        $module,
        SRProjectReadRepository $projects,
        CategoryReadRepository $categories,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->projects = $projects;
        $this->categories = $categories;
    }

    public function actionIndex(): string
    { // TODO - Что за метод? Теперь много корней деревьев
        $dataProvider = $this->projects->getAll();
        $category = $this->categories->getRoot();

        return $this->render('index', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory(string $slug): string
    {
        if (!$category = $this->categories->findBySlug($slug)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $dataProvider = $this->projects->getAllByCategory($category);

        return $this->render('category', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSrProject(int $id): string
    {
        if (!$srProject = $this->projects->find($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('sr-project', [
            'srProject' => $srProject,
        ]);
    }
}
