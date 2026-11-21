<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\SRProjects\image;

use Besnovatyj\SRProjects\entities\srProject\SRProject;
use Besnovatyj\SRProjects\entities\srProject\Image;
use Besnovatyj\SRProjects\repositories\SRProjectRepository;
use Besnovatyj\Images\base\BaseImage;
use Besnovatyj\Images\contracts\ImageOwnerInterface;
use yii\db\Exception;

/**
 * Адаптер SRProject к ImageOwnerInterface.
 *
 * Реализует pessimistic lock через PessimisticLockBehavior SRProject,
 * чтобы исключить race condition при параллельной загрузке изображений
 * (несколько запросов одновременно видят main_image_id = null и пытаются
 * его установить, что приводит к FK constraint violation).
 */
readonly class SRProjectImageOwner implements ImageOwnerInterface
{
    public function __construct(
        private SRProject           $srProject,
        private SRProjectRepository $repository,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getOwnerId(): int
    {
        return $this->srProject->id;
    }

    /**
     * {@inheritdoc}
     *
     * @return Image[]
     */
    public function getOwnedImages(): array
    {
        return $this->srProject->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainImageId(): ?int
    {
        return $this->srProject->main_image_id ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMainImageId(?int $imageId): void
    {
        $this->srProject->setMainImage($imageId);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function saveOwner(): void
    {
        $this->repository->save($this->srProject);
    }

    /**
     * Блокирует строку галереи (SELECT FOR UPDATE) до конца транзакции.
     *
     * Исключает race condition при параллельной загрузке нескольких файлов.
     * @throws Exception
     */
    public function lockOwner(): void
    {
        $this->srProject->lock();
    }

    /**
     * Обновляет данные галереи из БД после применения блокировки.
     */
    public function refreshOwner(): void
    {
        $this->srProject->refresh();
    }
}
