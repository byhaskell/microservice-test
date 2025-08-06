<?php

namespace App\Controller;

use App\Model\User;
use App\Security\ApiChecker;
use App\Service\CreateUserService;
use App\Service\GetUserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/api/v1/user/create', name: 'app_user_create', methods: ['POST', 'OPTIONS'])]
    public function create(CreateUserService $createUserService, Request $request, ApiChecker $apiChecker): Response
    {
        $apiChecker->check($request->headers->get(ApiChecker::KEY));

        $response = $createUserService->create($request->getPayload()->all());

        return new JsonResponse(
            $response instanceof User ? $response->toArray() : $response,
            is_array($response) && isset($response['errors']) ? 400 : 200
        );
    }

    #[Route('/api/v1/user/{id}', name: 'app_user', methods: ['GET'])]
    public function getUserInfo($id, GetUserService $getUserService, Request $request, ApiChecker $apiChecker): Response
    {
        $apiChecker->check($request->headers->get(ApiChecker::KEY));

        // Завантажуємо список користувачів для подальшого считування
        $getUserService->writeDownUser();

        $response = $getUserService->getById($id);
        if (empty($response)) {
            return new JsonResponse(['errors' => ['Користувач не знайдено']], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($response->toArray(), 200);
    }
}
