<?php

namespace App\Controller;

use App\Message\CreateOrder;
use App\Service\OrderRpcClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderController extends AbstractController
{
    #[Route('/orders', name: 'api_orders', methods: ['POST', 'OPTIONS'])]
    public function index(Request $request, OrderRpcClient $orderRpcClient)
    {
        $data = $request->getPayload()->all();

        $queueId = uniqid();

        $message = new CreateOrder(
            $data,
            queueId: $queueId
        );

        $response = $orderRpcClient->send($message);

        return new JsonResponse($response);
    }
}
