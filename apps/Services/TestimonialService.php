<?php

namespace App\Services;

use App\Models\Testimonial;
use PDOException;

class TestimonialService
{
    private Testimonial $model;

    public function __construct()
    {
        $this->model = new Testimonial();
    }

    public function getActive(): array
    {
        try {
            return $this->model->allActive();
        } catch (PDOException) {
            return $this->fallbackTestimonials();
        }
    }

    public function getAll(): array
    {
        return $this->model->all();
    }

    public function find(int $id): ?array
    {
        return $this->model->find($id);
    }

    public function getForClient(int $clientId): array
    {
        return $this->model->findByClient($clientId);
    }

    public function create(array $data): int
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    private function fallbackTestimonials(): array
    {
        return [
            [
                'author_name' => 'Sarah Jenkins',
                'author_location' => 'United Kingdom',
                'quote' => "The most authentic safari experience I've ever had. Pentagon Quest's attention to detail and knowledge of the land is unparalleled.",
                'accent_color' => 'gold',
            ],
            [
                'author_name' => 'Mark Thompson',
                'author_location' => 'USA',
                'quote' => 'From the moment we landed in Nairobi, everything was seamless. The 4x4 expedition was rugged yet incredibly comfortable.',
                'accent_color' => 'green',
            ],
        ];
    }
}
