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

        // Cache des catégories déjà récupérées
        $categories = [];

        // Compteurs
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

            // Récupération des données du film
            $title = $movieData['title'] ?? '';

            $year = isset($movieData['year']['$numberInt'])
                ? (int) $movieData['year']['$numberInt']
                : null;

            $description = $movieData['plot'] ?? '';

            // Vérifie si le film existe déjà uniquement avec son titre
            $existingMovie = $this->entityManager
                ->getRepository(Movie::class)
                ->findOneBy([
                    'titre' => $title,
                ]);

            // Si le film existe déjà, on passe au suivant
            if ($existingMovie) {
                $duplicates++;
                continue;
            }

            // Création du film
            $movie = new Movie();

            $movie
                ->setTitre($title)
                ->setDescription($description)
                ->setYear($year);

            // Gestion des catégories
            foreach ($movieData['genres'] ?? [] as $genreName) {

                // Vérifie si la catégorie est déjà dans le cache
                if (!isset($categories[$genreName])) {

                    // Recherche la catégorie en base
                    $category = $this->entityManager
                        ->getRepository(Category::class)
                        ->findOneBy([
                            'name' => $genreName,
                        ]);

                    // Si elle n'existe pas, on la crée
                    if (!$category) {
                        $category = new Category();
                        $category->setName($genreName);

                        $this->entityManager->persist($category);
                    }

                    // Ajoute la catégorie au cache
                    $categories[$genreName] = $category;
                }

                // Associe la catégorie au film
                $movie->addCategory(
                    $categories[$genreName]
                );
            }

            // Prépare le film pour l'enregistrement
            $this->entityManager->persist($movie);

            $imported++;

            // On s'arrête après 150 nouveaux films
            if ($imported >= 150) {
                break;
            }
        }

        fclose($file);

        // Enregistre les films, catégories et relations en base
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