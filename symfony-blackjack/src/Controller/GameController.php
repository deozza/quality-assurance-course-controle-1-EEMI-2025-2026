<?php

namespace App\Controller;

use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
class GameController extends AbstractController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    #[Route('/game', name: 'get_list_of_games', methods: ['GET'])]
    public function getListOfGames(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        $userId = null;
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $userId = $user->getId();
        }
        $limit = $request->query->get('limit', 12);
        $page = $request->query->get('page', 0);
        list($games, $err) = $this->gameService->getPaginatedGameList($limit, $page, $userId);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        return $this->json($games, 200, [], ['groups' => 'game']);
    }

    #[Route('/game', name: 'create_game', methods: ['POST'])]
    public function createGame(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        list($game, $err) = $this->gameService->createGame($user);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        list($game, $err) = $this->gameService->initializeGame($game);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        return $this->json($game, 201, [], ['groups' => 'game']);
    }

    #[Route('/game/{id}', name: 'get_game', methods: ['GET'])]
    public function getGame(string $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        list($game, $err) = $this->gameService->getGame($id, $user);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 404);
        }
        return $this->json($game, 200, [], ['groups' => 'game']);
    }

    #[Route('/game/{id}', name: 'delete_game', methods: ['DELETE'])]
    public function deleteGame(string $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        list($_, $err) = $this->gameService->deleteGame($id, $user);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        return $this->json(null, 204);
    }
}