<?php

namespace App\Controller;

use App\Repository\TrackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class AdminController extends AbstractController
{
    public function __construct(
        private TrackRepository $trackRepository,
    )
    {
        // Left intentionally blank
    }

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('admin/homepage.html.twig', []);
    }

    #[Route('/tracks', name: 'tracks')]
    public function tracks(): Response
    {
        $tracks = $this->trackRepository->findAll();
        dd($tracks);
        return $this->render('admin/tracks.html.twig', []);
    }
}
