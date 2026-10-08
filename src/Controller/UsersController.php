<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use App\Entity\User;

final class UsersController extends AbstractController
{


    #[Route('/api/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        return $this->json($user, context: ['groups' => 'user:read']);
    }

    #[Route('/api/users/{id}/follow', name: 'app_user_follow', methods: ['POST'])]
    public function follow(
        int $id,
        UserRepository $userRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        $userToFollow = $userRepository->find($id);

        if (!$userToFollow) {
            return $this->json([
                'message' => 'Utilisateur introuvable'
            ], 404);
        }

        if ($user === $userToFollow) {
            return $this->json([
                'message' => 'Vous ne pouvez pas vous suivre vous-même'
            ], 400);
        }

        if ($user->getFollowing()->contains($userToFollow)) {
            return $this->json([
                'message' => 'Vous suivez déjà cet utilisateur'
            ], 409);
        }

        $userRepository->follow($user, $userToFollow);

        return $this->json([
            'message' => 'Utilisateur suivi'
        ]);
    }

    #[Route('/api/users', name: 'app_users', methods: ['GET'])]
    public function users(UserRepository $userRepository): JsonResponse
    {
        return $this->json($userRepository->findAll(), context: ['groups' => 'user:read']);
    }

    #[Route('/api/users/{id}/follow', name: 'app_user_unfollow', methods: ['DELETE'])]
    public function unfollow(
        int $id,
        UserRepository $userRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        $userToUnfollow = $userRepository->find($id);

        if (!$userToUnfollow) {
            return $this->json([
                'message' => 'Utilisateur introuvable'
            ], 404);
        }

        $userRepository->unfollow($user, $userToUnfollow);

        return $this->json([
            'message' => 'Vous ne suivez plus cet utilisateur'
        ]);
    }
}