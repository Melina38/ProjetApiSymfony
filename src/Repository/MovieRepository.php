<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function createMovie(string $title, string $description, int $year, array $categories): Movie
    {
        $movie = new Movie();
        $movie->setTitle($title);
        $movie->setDescription($description);
        $movie->setYear($year);

        foreach ($categories as $categoryID) {
            $category = $this->getEntityManager()->getRepository('App\Entity\Category')->find($categoryID);
            if ($category) {
                $movie->addCategory($category);
            }
        }

        $this->getEntityManager()->persist($movie);
        $this->getEntityManager()->flush();
        return $movie;
    }
    public function readMovie(int $id): ?Movie
    {
        return $this->find($id);
    }

    public function deleteMovie(Movie $movie): void
    {
        $this->getEntityManager()->remove($movie);
        $this->getEntityManager()->flush();
    }

    public function updateMovie(Movie $movie): void
    {
        $this->getEntityManager()->flush();
    }

    public function paginationMovies(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('m')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countMovies(): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function searchMovies(
        ?string $title,
        ?int $year,
        int $page,
        int $limit
    ): array {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('m');

        if ($title !== null) {
            $queryBuilder
                ->andWhere('m.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        if ($year !== null) {
            $queryBuilder
                ->andWhere('m.year = :year')
                ->setParameter('year', $year);
        }

        return $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countSearchMovies(
        ?string $title,
        ?int $year
    ): int {
        $queryBuilder = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)');

        if ($title !== null) {
            $queryBuilder
                ->andWhere('m.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        if ($year !== null) {
            $queryBuilder
                ->andWhere('m.year = :year')
                ->setParameter('year', $year);
        }

        return (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

}
