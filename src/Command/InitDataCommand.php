<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-data',
    description: 'Importe les 150 premiers films depuis movies.json',
)]
class InitDataCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
    ): int {
        $file = fopen(__DIR__ . '/../../movies.json', 'r');

        if (!$file) {
            $io->error('Impossible d\'ouvrir le fichier movies.json');

            return Command::FAILURE;
        }

        ini_set('memory_limit', '2048M');

        $categories = [];

        $imported = 0;
        $duplicates = 0;

        while (!feof($file)) {
            $line = fgets($file);

            if (!$line) {
                continue;
            }

            $movieData = json_decode($line, true);

            if (!$movieData) {
                continue;
            }

            $title = $movieData['title'] ?? '';

            $year = isset($movieData['year']['$numberInt'])
                ? (int) $movieData['year']['$numberInt']
                : null;

            $description = $movieData['plot'] ?? '';

            $existingMovie = $this->entityManager
                ->getRepository(Movie::class)
                ->findOneBy([
                    'title' => $title,
                ]);

            if ($existingMovie) {
                $duplicates++;
                continue;
            }

            $movie = new Movie();

            $movie
                ->setTitle($title)
                ->setDescription($description)
                ->setYear($year);

            foreach ($movieData['genres'] ?? [] as $genreName) {

                if (!isset($categories[$genreName])) {

                    $category = $this->entityManager
                        ->getRepository(Category::class)
                        ->findOneBy([
                            'name' => $genreName,
                        ]);

                    if (!$category) {
                        $category = new Category();
                        $category->setName($genreName);

                        $this->entityManager->persist($category);
                    }

                    $categories[$genreName] = $category;
                }

                $movie->addCategory(
                    $categories[$genreName]
                );
            }

            $this->entityManager->persist($movie);

            $imported++;

            if ($imported >= 150) {
                break;
            }
        }

        fclose($file);

        $this->entityManager->flush();

        $io->success(
            sprintf(
                '%d films importés, %d doublons ignorés.',
                $imported,
                $duplicates
            )
        );

        return Command::SUCCESS;
    }
}