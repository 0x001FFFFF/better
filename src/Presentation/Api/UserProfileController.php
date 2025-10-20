<?php

declare(strict_types=1);

namespace App\Presentation\Api;

use App\Application\DTO\GetUserQuery;
use App\Application\User\GetProfile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;

class UserProfileController extends AbstractController
{
    #[Route('/api/v1/user-profile/{user_id}', name: 'user_profile', methods: ['GET'])]
    public function getUserProfile(Request$request, GetProfile $userProfile,  ValidatorInterface $validator): Response
    {
        $query = GetUserQuery::fromRequest($request);
        $violations = $validator->validate($query);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            return $this->json(
                data: [
                    'status' => 'error',
                    'errors' => $errors,
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $result = $userProfile->handle($query);

        return $this->json(
            data: $result->jsonSerialize(),
            status: Response::HTTP_OK,
        );
    }
}
