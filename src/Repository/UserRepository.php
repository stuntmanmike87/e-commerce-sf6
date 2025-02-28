<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    #[\Override]
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $values = $user::class;
        $values = strval($values);

        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $values));
        }

        $user->setPassword($newHashedPassword);
        $em = $this->getEntityManager();
        $em->persist($user); // $this->_em->persist($user);
        $em->flush(); // $this->_em->flush();
    }

    public function findOneByEmail(string $email): ?User// mixed
    {
        /* return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ; */
        /** @var User|null $userFoundByEmail */
        $userFoundByEmail = $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $userFoundByEmail;
    }

    public function findOneByResetToken(TokenInterface $resetToken): ?User// mixed
    {
        /* return $this->createQueryBuilder('u')
            ->andWhere('u.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ; */

        /** @var User|null $userFoundByResetToken */
        $userFoundByResetToken = $this->createQueryBuilder('u')
            ->andWhere('u.resetToken = :resetToken')
            ->setParameter('resetToken', $resetToken)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $userFoundByResetToken;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
