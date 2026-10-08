<?php

namespace App\Repository;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function createRating(
        int $ratingNumber,
        Movie $movie,
        User $user
    ): Rating {
        $rating = new Rating();

        $rating->setRating($ratingNumber);
        $rating->setDate(new \DateTime());
        $rating->setMovie($movie);
        $rating->setUser($user);

        $this->getEntityManager()->persist($rating);
        $this->getEntityManager()->flush();

        return $rating;
    }

    public function updateRating(
        Rating $rating,
        int $ratingNumber
    ): Rating {

        $rating->setRating($ratingNumber);
        $rating->setDate(new \DateTime());

        $this->getEntityManager()->flush();

        return $rating;
    }

    public function deleteRating(Rating $rating): void
    {
        $this->getEntityManager()->remove($rating);
        $this->getEntityManager()->flush();
    }

    public function findUserRatings(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUserRatingForMovie(
        User $user,
        Movie $movie
    ): ?Rating {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.movie = :movie')
            ->setParameter('user', $user)
            ->setParameter('movie', $movie)
            ->getQuery()
            ->getOneOrNullResult();
    }
}