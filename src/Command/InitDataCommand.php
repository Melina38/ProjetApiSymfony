<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:init-data',
    description: 'Add a short description for your command',
)]
class InitDataCommand{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }


    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Argument description')] string $arg = '',
        #[Option('Option description')] bool $enable = false,
    ): int {
        $io->note(sprintf('The value of $arg is: %s', var_export($arg, true)));
        $io->note(sprintf('The value of $enable is: %s', var_export($enable, true)));

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $movie = (new Movie())
            ->setTitre(titre: 'Inception')
            ->setDescription(description: 'Un film de science-fiction réalisé par Christopher Nolan.')
            ->setYear(year: 2010);

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
