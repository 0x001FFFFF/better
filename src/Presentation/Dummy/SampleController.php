<?php

declare(strict_types=1);

namespace App\Presentation\Dummy;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SampleController extends AbstractController
{
    #[Route('/api/internal/v1/source1/{user_id}', methods: ['GET'])]
    public function source1(Request $request): Response
    {
        return new JsonResponse(['data' => ['email' => 'test@test.com', 'name' => 'Bar Dor', 'priority' => ['email' => 0, 'name' => 2]]], Response::HTTP_OK);
    }

    #[Route('/api/internal/v1/source2/{user_id}', methods: ['GET'])]
    public function source2(Request $request): Response
    {
        return new JsonResponse(['data' => ['name' => 'John Foo', 'priority' => ['name' => 0]]], Response::HTTP_OK);
    }

    #[Route('/api/internal/v1/source3/{user_id}', methods: ['GET'])]
    public function source3(Request $request): Response
    {
        return new JsonResponse(['data' => ['name' => 'John Bar', 'avatar' => 'https://i.pravatar.cc/300', 'priority' => ['name' => 1, 'avatar' => 0]]], Response::HTTP_OK);
    }

    #[Route('/api/internal/v1/source4/{user_id}', methods: ['GET'])]
    public function source4(Request $request): Response
    {
        return new JsonResponse(['data' => ['unknown' => 'alien', 'priority' => ['unknown' => 0]]], Response::HTTP_OK);
    }
}
