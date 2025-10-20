<?php

namespace App\Application\User;

use App\Application\DTO\GetUserQuery;
use App\Application\DTO\GetUserResponse;
use App\Infrastructure\Client\ApiClient;

final readonly class GetProfile
{
    public function __construct(public ApiClient $apiClient) {}
    public function handle(GetUserQuery $query): GetUserResponse
    {
        $result = $this->apiClient->fetchUserProfileData($query->getId());
        return GetUserResponse::combineResults($result, $query->getId());
    }
}