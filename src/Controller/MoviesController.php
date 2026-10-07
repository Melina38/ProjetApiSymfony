<?php

namespace App\Controller;

use App\Model\QueryDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MovieRepository;
use App\Model\PaginationDTO;
use App\Model\Pagination;

/* modifier les hhtp pour mettre DELETE pour la route pour delete PATCH pour update et GET pour read et POST pour create*/
// php bin/console debug:router pour voir toute les routes

final class MoviesController extends AbstractController
{
    #[Route('/movies', name: 'app_movies')]
    public function index(
        Request $request,
        #[MapQueryString] QueryDTO $queryDTO
    ): JsonResponse
    {
        return $this->json($queryDTO);
    }

    #[Route('/movies/list', name: 'app_movies_list')]
    public function list(
        MovieRepository $movieRepository,
        #[MapQueryString] PaginationDTO $paginationDTO
    ): JsonResponse {
        $movies = $movieRepository->paginationMovies(
            $paginationDTO->page,
            $paginationDTO->limit
        );

        $total = $movieRepository->countMovies();

        $pagination = new Pagination(
            $movies,
            $total,
            $paginationDTO->page,
            $paginationDTO->limit
        );

        return $this->json($pagination);
    }

    /*#[Route('/movies/{id}', name: 'app_movies_id')]
    public function movie1(MovieRepository $movieRepository, int $id): JsonResponse
    {
        $movies = $movieRepository->find($id);

        return $this->json($movies, context: ['groups' => 'movie:read']);
    }*/

    #[Route('/movies/create', name: 'app_movies_create', methods: ['POST'])]
    public function create(MovieRepository $movieRepository, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        //dd($data);
        $movie = $movieRepository->createMovie($data['titre'], $data['description'], $data['year'], $data['categories']);
        return $this->json($movie);
    }

    #[Route('/movies/read', name: 'app_movies_read', methods: ['POST'])]
    public function read(MovieRepository $movieRepository, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $movie = $movieRepository->readMovie($data['id']);
        return $this->json($movie);
    }

    #[Route('/movies/delete', name: 'app_movies_delete', methods: ['POST'])]
    public function delete(MovieRepository $movieRepository, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $movie = $movieRepository->readMovie($data['id']);
        if ($movie) {
            $movieRepository->deleteMovie($movie);
            return $this->json(['message' => 'Movie deleted successfully.']);
        } else {
            return $this->json(['message' => 'Movie not found.'], 404);
        }
    }

    #[Route('/movies/update', name: 'app_movies_update', methods: ['POST'])]
    public function update(MovieRepository $movieRepository, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $movie = $movieRepository->readMovie($data['id']);
        if ($movie) {
            $movie->setTitre($data['titre']);
            $movie->setDescription($data['description']);
            $movie->setYear($data['year']);
            $movieRepository->updateMovie($movie);
            return $this->json(['message' => 'Movie updated successfully.']);
        } else {
            return $this->json(['message' => 'Movie not found.'], 404);

        }
    }
}

//pour prendre une valeur d'une clé spécifique
//return $this->json(json_decode($request->getContent(), true)['title']);