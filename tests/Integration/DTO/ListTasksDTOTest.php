<?php

declare(strict_types=1);

namespace App\Tests\Integration\DTO;

use App\DTO\ListTasksDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ListTasksDTOTest extends KernelTestCase
{
    public function testListTasksDTOValid(): void
    {
        $listTasksDTO = new ListTasksDTO();

        $listTasksDTO->setAnon(true);
        $listTasksDTO->setCompleted(true);
        $listTasksDTO->setPage(1);

        self::assertTrue($listTasksDTO->isAnon());
        self::assertTrue($listTasksDTO->isCompleted());
        self::assertEquals(1, $listTasksDTO->getPage());
    }
}
