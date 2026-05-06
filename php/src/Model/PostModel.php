<?php

require_once __DIR__ . '/../Model/ApiClient.php';

class PostModel
{
    private ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    public function getAll(): array
    {
        return $this->api->get('/api/posts');
    }

    public function getById(int $id): array|false
    {
        return $this->api->get('/api/posts/' . $id);
    }

    public function create(array $data): int|false
    {
        $result = $this->api->post('/api/posts', $data);
        return $result['id'] ?? false;
    }

    public function update(int $id, array $data): bool
    {
        return $this->api->put('/api/posts/' . $id, $data) !== false;
    }

    public function delete(int $id): bool
    {
        return $this->api->delete('/api/posts/' . $id) !== false;
    }
}