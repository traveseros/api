<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HealthController extends AbstractController
{
    #[Route('/health', name: 'app_health')]
    public function index(): JsonResponse
    {
        $env = $_ENV['APP_ENV'] ?? 'unknown';

        return $this->json([
            'status' => 'ok',
            'environment' => $env,
        ], 200);
    }
}
