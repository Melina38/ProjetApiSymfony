<?php

namespace App\Controller;

use App\Model\QueryDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

final class UsersController extends AbstractController
{


    #[Route('/user', name: 'app_user')]
    public function index(
        Request $request,
        #[MapQueryString] QueryDTO $queryDTO
    ): JsonResponse
    {
        return $this->json($queryDTO);
    }
}