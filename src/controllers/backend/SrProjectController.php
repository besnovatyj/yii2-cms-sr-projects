<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\SRProjects\controllers\backend;

use Besnovatyj\SRProjects\forms\backend\srProject\SRProjectForm;
use Besnovatyj\SRProjects\forms\backend\search\SRProjectSearch;
use Besnovatyj\SRProjects\image\SRProjectImageOwner;
use Besnovatyj\SRProjects\repositories\SRProjectRepository;
use Besnovatyj\SRProjects\services\manage\SRProjectManageService;
use Besnovatyj\Images\helpers\ImageActionsMap;
use Besnovatyj\SRProjects\entities\srProject\Image;
use Besnovatyj\Kernel\controller\ControllerTrait;
use Besnovatyj\Kernel\urlmanager\UrlManagerHelperTrait;
use DomainException;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;

class SrProjectController extends Controller
{
    use ControllerTrait;
    use UrlManagerHelperTrait;

    private SRProjectManageService $service;
    private SRProjectRepository $srProjectRepo;

    public function __construct(
        $id,
        $module,
        SRProjectManageService $service,
        SRProjectRepository $srProjectRepo,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
        $this->service     = $service;
        $this->srProjectRepo = $srProjectRepo;
    }

    /**
     * Регистрирует standalone image-actions через ImageActionsMap.
     *
     * SRProject передаёт SRProjectImageOwner, который реализует pessimistic lock
     * (SELECT FOR UPDATE) для исключения race condition при параллельной загрузке.
     *
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return ImageActionsMap::get(
            Image::class,
            fn(int $id) => new SRProjectImageOwner($this->srProjectRepo->get($id), $this->srProjectRepo),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'activate'       => ['POST'],
                    'draft'          => ['POST'],
                    'delete'         => ['POST'],
                    'add-image'      => ['POST'],
                    'delete-image'   => ['POST'],
                    'set-main-image' => ['POST'],
                    'get-images'     => ['POST'],
                    'set-new-sort'   => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel  = new SRProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws InvalidConfigException
     */
    public function actionView(int $id): Response|string
    {
        try {
            $absoluteFrontendUrl = $this->getAbsoluteFrontendRoute('/SRProjects/sr-project/sr-project/', ['id' => $id]);
            return $this->render('view', [
                'srProject'             => $this->srProjectRepo->get($id),
                'absoluteFrontendUrl' => $absoluteFrontendUrl,
            ]);
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->goHome();
    }

    /**
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $form = new SRProjectForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $srProject = $this->service->create($form);
                return $this->redirect(['view', 'id' => $srProject->id]);
            } catch (Throwable $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     */
    public function actionUpdate(int $id): Response|string
    {
        $srProject = $this->srProjectRepo->get($id);
        $form    = new SRProjectForm($srProject);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($srProject->id, $form);
                return $this->redirect(['view', 'id' => $srProject->id]);
            } catch (Throwable $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }

        return $this->render('update', [
            'model'   => $form,
            'srProject' => $srProject,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->service->remove($id);
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionActivate(int $id): Response
    {
        try {
            $this->service->activate($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->goReferer();
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDraft(int $id): Response
    {
        try {
            $this->service->draft($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->goReferer();
    }

}
