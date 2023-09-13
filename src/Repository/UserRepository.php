<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly int $usersPerPage)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // @phpstan-ignore-next-line
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array{
     *     total_items: int,
     *     total_pages: int,
     *     items_per_page: int,
     *     page: int,
     *     embedded: User[]
     * }
     *
     * @throws NonUniqueResultException
     */
    public function getPaginatedUsersWithoutUser(User $user, int $page): array
    {
        /** @var User[] $users */
        $users = $this
            ->createQueryBuilder('u')
            ->where('u.id != :ulid')
            ->setParameter('ulid', $user->getId(), UlidType::NAME)
            ->orderBy('u.role', 'ASC')
            ->setFirstResult(max($this->usersPerPage * ($page - 1), 0))
            ->setMaxResults($this->usersPerPage)
            ->getQuery()
            ->getResult();

        $totalItems = (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.id != :ulid')
            ->setParameter('ulid', $user->getId(), UlidType::NAME)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total_items' => $totalItems,
            'total_pages' => (int) ceil($totalItems / $this->usersPerPage),
            'items_per_page' => $this->usersPerPage,
            'page' => $page,
            'embedded' => $users,
        ];
    }
}
