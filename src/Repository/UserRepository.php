<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Bundle\SecurityBundle\Security;
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
    public function __construct(ManagerRegistry $registry, private readonly Security $security, private readonly int $users_per_page)
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
     * @return User[]
     */
    public function getPaginatedUsersWithoutMe(User $user, int $page): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $perPage = $this->users_per_page;

        /** @var User[] $users */
        $users = $this
            ->createQueryBuilder('u')
            ->where('u.id != :ulid')
            ->setParameter('ulid', $user->getId(), UlidType::NAME)
            ->orderBy('u.role', 'ASC')
            ->setFirstResult(max($perPage * ($page - 1), 0))
            ->setMaxResults($perPage)
            ->getQuery()
            ->execute();

        return $users;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function countUsersWithoutMe(): int
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return (int) $this
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.id != :ulid')
            ->setParameter('ulid', $user->getId(), UlidType::NAME)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
