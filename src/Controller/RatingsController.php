<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\RatingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class RatingsController extends AbstractController
{
    #[Route('/api/ratings', name: 'app_rating_create', methods: ['POST'])]
    public function create(
        Request $request,
        RatingRepository $ratingRepository,
        MovieRepository $movieRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['movieId'], $data['rating'])) {
            return $this->json([
                'message' => 'movieId et rating sont obligatoires'
            ], 400);
        }

        if ($data['rating'] < 1 || $data['rating'] > 10) {
            return $this->json([
                'message' => 'La note doit être comprise entre 1 et 10'
            ], 400);
        }

        $movie = $movieRepository->find($data['movieId']);

        if (!$movie) {
            return $this->json([
                'message' => 'Film introuvable'
            ], 404);
        }

        $existingRating = $ratingRepository->findUserRatingForMovie(
            $user,
            $movie
        );

        if ($existingRating) {
            return $this->json([
                'message' => 'Vous avez déjà noté ce film'
            ], 409);
        }

        $rating = $ratingRepository->createRating(
            $data['rating'],
            $movie,
            $user
        );

        return $this->json($rating, 201, context: ['groups' => 'rating:read']);
    }

    #[Route('/api/ratings/{id}', name: 'app_rating_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        RatingRepository $ratingRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        $rating = $ratingRepository->find($id);

        if (!$rating) {
            return $this->json([
                'message' => 'Note introuvable'
            ], 404);
        }

        if ($rating->getUser() !== $user) {
            return $this->json([
                'message' => 'Vous ne pouvez pas modifier cette note'
            ], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['rating'])) {
            return $this->json([
                'message' => 'La note est obligatoire'
            ], 400);
        }

        if ($data['rating'] < 1 || $data['rating'] > 10) {
            return $this->json([
                'message' => 'La note doit être comprise entre 1 et 10'
            ], 400);
        }

        $ratingRepository->updateRating(
            $rating,
            $data['rating']
        );

        return $this->json($rating, 200, context: ['groups' => 'rating:read']);
    }

    #[Route('/api/ratings/{id}', name: 'app_rating_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        RatingRepository $ratingRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        $rating = $ratingRepository->find($id);

        if (!$rating) {
            return $this->json([
                'message' => 'Note introuvable'
            ], 404);
        }

        if ($rating->getUser() !== $user) {
            return $this->json([
                'message' => 'Vous ne pouvez pas supprimer cette note'
            ], 403);
        }

        $ratingRepository->deleteRating($rating);

        return $this->json([
            'message' => 'Note supprimée'
        ]);
    }

    #[Route('/api/ratings', name: 'app_rating_list', methods: ['GET'])]
    public function list(
        RatingRepository $ratingRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        $ratings = $ratingRepository->findUserRatings($user);

        return $this->json($ratings, 200, context: ['groups' => 'rating:read']);
    }
}