<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\SRProjects\repositories;

use Besnovatyj\SRProjects\entities\srProject\SRProject;
use RuntimeException;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class SRProjectRepository
{

    public function get(int $id): SRProject
    {
        if (!$srProject = SRProject::findOne($id)) {
            throw new NotFoundException('SRProject is not found.');
        }
        return $srProject;
    }

    public function existsByMainCategory($id): bool
    {
        return SRProject::find()->andWhere(['category_id' => $id])->exists();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function save(SRProject $srProject)
    {
        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                if ($srProject->save()) {
                    return true;
                }
                throw new RuntimeException('Failed to save gallery.');
            } catch (Exception $e) {
                if ($e->errorInfo[1] == 1213) { // Код ошибки дедлока
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        throw $e; // Превышено количество попыток
                    }
                    usleep(rand(100, 500) * 1000); // Задержка 100-500 мс
                    continue;
                }
                throw $e; // Другие ошибки
            }
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove(SRProject $srProject): void
    {
        if (!$srProject->delete()) {
            throw new RuntimeException('Removing error.');
        }
    }
}
