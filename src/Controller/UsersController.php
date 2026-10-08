<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UsersController extends AbstractController
{


    #[Route('/api/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non connecté'
            ], 401);
        }

        return $this->json($user, context: ['groups' => 'user:read']);
    }
}