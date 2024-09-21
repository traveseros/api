<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): JsonResponse
    {
        return $this->render('health/index.html.twig', [
            'controller_name' => 'HealthController',
        ]);
    }
}
