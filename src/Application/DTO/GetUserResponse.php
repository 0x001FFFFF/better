<?php

namespace App\Application\DTO;

final readonly class GetUserResponse implements \JsonSerializable
{
    private function __construct(public array $data){}
    public static function combineResults(array $result, string $userId): self
    {
        $mergedData = ['id' => $userId];
        $priorities = [];
        foreach ($result as  $response) {
            $decoded = json_decode($response, true);
            $data = $decoded['data'] ?? [];
            $dataPriorities = $data['priority'] ?? [];
            unset($data['priority']);
            foreach ($data as $field => $value) {
                $fieldPriority = $dataPriorities[$field] ?? 255;
                if (!isset($priorities[$field]) || $fieldPriority < $priorities[$field]) {
                    $mergedData[$field] = $value;
                    $priorities[$field] = $fieldPriority;
                }
            }
        }


        return new self($mergedData);
    }

    public function jsonSerialize(): string
    {
        return json_encode($this->data);
    }

}