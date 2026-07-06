<?php

namespace App\Controller;

use App\Service\GameService;
use App\Service\TurnService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
class TurnController extends AbstractController
{
    private TurnService $turnService;
    private GameService $gameService;
    public function __construct(TurnService $turnService, GameService $gameService)
    {
        $this->turnService = $turnService;
        $this->gameService = $gameService;
    }

    #[Route('/game/{id}/turn', name: 'create_turn', methods: ['POST'])]
    public function createTurn(string $id): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json('Unauthorized', 401);
        }

        list($game, $err) = $this->gameService->getGame($id, $user);

        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }

        list($turn, $err) = $this->turnService->createNewTurn($game);

        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        return $this->json($turn, 201, [], ['groups' => 'turn']);
    }

    #[Route('/turn/{id}', name: 'get_turn', methods: ['GET'])]
    public function getTurn(string $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        list($turn, $err) = $this->turnService->getTurn($id, $user);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 404);
        }
        return $this->json($turn, 200, [], ['groups' => 'turn']);
    }

    #[Route('/turn/{id}/wage', name: 'wage_turn', methods: ['PATCH'])]
    public function wageTurn(string $id, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->json('Invalid JSON', 400);
        }
        list($turn, $err) = $this->turnService->wageTurn($id, $user, $data);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        list($turn, $err) = $this->turnService->initializeTurn($turn);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        return $this->json($turn, 200, [], ['groups' => 'turn']);
    }

    #[Route('/turn/{id}/hit', name: 'hit_turn', methods: ['PATCH'])]
    public function hitTurn(string $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        list($turn, $err) = $this->turnService->hitTurn($id, $user);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        if ($turn->getStatus() === 'busted') {
            list($turn, $err) = $this->turnService->distributeGains($turn);

            if ($err instanceof \Exception) {
                return $this->json($err->getMessage(), $err->getCode() ?: 400);
            }
        }

        return $this->json($turn, 200, [], ['groups' => 'turn']);
    }

    #[Route('/turn/{id}/stand', name: 'stand_turn', methods: ['PATCH'])]
    public function standTurn(string $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json('Unauthorized', 401);
        }
        list($turn, $err) = $this->turnService->standTurn($id, $user);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        list($turn, $err) = $this->turnService->dealerAutoDraw($turn);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        list($turn, $err) = $this->turnService->distributeGains($turn);
        if ($err instanceof \Exception) {
            return $this->json($err->getMessage(), $err->getCode() ?: 400);
        }
        return $this->json($turn, 200, [], ['groups' => 'turn']);
    }
}
