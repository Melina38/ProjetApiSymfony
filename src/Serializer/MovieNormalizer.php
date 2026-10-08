<?php

namespace App\Serializer;

use App\Entity\Movie;
use App\Repository\RatingRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MovieNormalizer implements NormalizerInterface
{
    public function __construct(
        private RatingRepository $ratingRepository,
        private Security $security
    ) {
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = []
    ): array {
        $data = [
            'id' => $object->getId(),
            'titre' => $object->getTitre(),
            'description' => $object->getDescription(),
            'year' => $object->getYear(),
        ];

        $user = $this->security->getUser();

        if ($user) {
            $rating = $this->ratingRepository->findUserRatingForMovie(
                $user,
                $object
            );

            $data['myRating'] = $rating?->getRating();
        }

        return $data;
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): bool {
        return $data instanceof Movie;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Movie::class => true,
        ];
    }
}