<?php

require_once __DIR__ . '/../Model/PostModel.php';

class PostController
{
    private PostModel $model;

    public function __construct(PostModel $model)
    {
        $this->model = $model;
    }

    public function index(): array
    {
        return $this->model->getAll();
    }

    public function show(int $id): array|false
    {
        return $this->model->getById($id);
    }

    public function store(array $post): int|false
    {
        if (empty($post['title']) || empty($post['content']) || empty($post['category'])) {
            return false;
        }
        return $this->model->create($post);
    }

    public function update(int $id, array $post): bool
    {
        return $this->model->update($id, $post);
    }

    public function destroy(int $id): bool
    {
        return $this->model->delete($id);
    }
}