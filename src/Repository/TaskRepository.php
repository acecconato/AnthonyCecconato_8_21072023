<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security,
        private readonly int $tasksPerPage
    ) {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return array{
     *     total_items: int,
     *     total_pages: int,
     *     items_per_page: int,
     *     page: int,
     *     embedded: Task[]
     * }
     *
     * @throws NonUniqueResultException
     */
    public function getPaginatedTasks(int $page, bool $completed = false, bool $anon = false): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $query = $this
            ->createQueryBuilder('task')
            ->orderBy('task.id', 'DESC')
            ->setFirstResult($this->tasksPerPage * ($page - 1))
            ->setMaxResults($this->tasksPerPage);

        $totalItemsQuery = $this
            ->createQueryBuilder('task')
            ->select('COUNT(task.id)');

        if (true === $anon) {
            $query->where('task.owner IS NULL');
            $totalItemsQuery->where('task.owner IS NULL');
        }

        if (false === $anon) {
            $query
                ->where('task.completed = :completed')
                ->setParameter('completed', $completed)
                ->andWhere('task.owner = :user')
                ->setParameter('user', $user->getId(), UlidType::NAME);
            $totalItemsQuery
                ->where('task.completed = :completed')
                ->setParameter('completed', $completed)
                ->andWhere('task.owner = :user')
                ->setParameter('user', $user->getId(), UlidType::NAME);
        }

        /** @var Task[] $tasks */
        $tasks = $query->getQuery()->getResult();
        $totalItems = (int) $totalItemsQuery->getQuery()->getSingleScalarResult();

        return [
            'total_items' => $totalItems,
            'total_pages' => (int) ceil($totalItems / $this->tasksPerPage),
            'items_per_page' => $this->tasksPerPage,
            'page' => $page,
            'embedded' => $tasks,
        ];
    }
}
