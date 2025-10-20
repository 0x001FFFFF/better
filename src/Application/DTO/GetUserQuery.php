<?php

namespace App\Application\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetUserQuery
{
    private function __construct(
        #[Assert\NotBlank(message: 'ID is required')]
        #[Assert\Type('string')]
        #[Assert\Uuid(message: 'ID must be a valid UUID')]
        public ?string $id,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id: $request->attributes->get('user_id')
                ? (string) $request->attributes->get('user_id')
                : null
        );
    }

    public function getId(): ?string
    {
      return $this->id;
    }
}