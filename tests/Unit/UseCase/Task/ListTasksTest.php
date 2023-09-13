<?php

declare(strict_types=1);

namespace App\Tests\Unit\UseCase\Task;

use App\DTO\ListTasksDTO;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\UseCase\Task\ListTasks;
use Doctrine\ORM\NonUniqueResultException;
use PHPUnit\Framework\TestCase;

final class ListTasksTest extends TestCase
{
    /**
     * @throws NonUniqueResultException
     */
    public function testListTasksSuccess(): void
    {
        $taskRepository = self::createMock(TaskRepository::class);

        $user = new User();
        $listTasksDTO = new ListTasksDTO(1, false, false);

        $taskRepository->expects(self::once())->method('getPaginatedTasks')->willReturn([
            'total_items' => 1,
            'total_pages' => 1,
            'items_per_page' => 1,
            'page' => 1,
            'embedded' => [new Task()],
        ]);

        $listTasks = new ListTasks($taskRepository);
        $listTasks($user, $listTasksDTO);
    }
}
