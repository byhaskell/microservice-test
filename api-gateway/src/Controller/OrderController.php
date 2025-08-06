<?php

namespace App\Controller;

use App\Rpc\OrderRpcClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderController extends AbstractController
{
    #[Route('/orders', name: 'api_orders', methods: ['POST', 'OPTIONS'])]
    public function orders(Request $request, OrderRpcClient $orderRpcClient)
    {
        $data = $request->getPayload()->all();

        $response = $orderRpcClient->call($data);

        return new JsonResponse($response, isset($response['errors']) ? 400 : 200);
    }
}
