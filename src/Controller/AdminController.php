<?php

namespace App\Controller;

use App\Entity\Control;
use App\Entity\TravesiaType;
use App\Form\Type\TrackFileFormControlesType;
use App\Form\Type\TrackFileFormCortaType;
use App\Form\Type\TrackFileFormFamiliarType;
use App\Form\Type\TrackFileFormLargaType;
use App\Repository\ControlRepository;
use App\Repository\TrackRepository;
use App\Service\XmlTrackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class AdminController extends AbstractController
{
    public function __construct(
        private TrackRepository $trackRepository,
        private XmlTrackService $xmlService,
        private ControlRepository $controlRepository,
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
    public function tracks(Request $request): Response
    {
        $tracks = $this->trackRepository->summarizeTracks();
        $controls = $this->controlRepository->getAllSorted();

        $largaForm = $this->createForm(TrackFileFormLargaType::class,['type' => TravesiaType::LARGA]);
        $cortaForm = $this->createForm(TrackFileFormCortaType::class,['type' => TravesiaType::CORTA]);
        $familiarForm = $this->createForm(TrackFileFormFamiliarType::class,['type' => TravesiaType::FAMILIAR]);
        $controlesForm = $this->createForm(TrackFileFormControlesType::class,['type' => TravesiaType::CONTROLES]);

        $largaForm->handleRequest($request);
        $cortaForm->handleRequest($request);
        $familiarForm->handleRequest($request);
        $controlesForm->handleRequest($request);

        if ($largaForm->isSubmitted() && $largaForm->isValid()){
            $file = $largaForm->get('file')->getViewData();
            \assert($file instanceof UploadedFile);
            $xml = \file_get_contents($file->getPathname());
            if (false !== $xml){
                $this->xmlService->extractPointsFromFile(TravesiaType::LARGA, $xml);
            }
        }

        if ($cortaForm->isSubmitted() && $cortaForm->isValid()){
            $file = $cortaForm->get('file')->getViewData();
            \assert($file instanceof UploadedFile);
            $xml = \file_get_contents($file->getPathname());
            if (false !== $xml){
                $this->xmlService->extractPointsFromFile(TravesiaType::CORTA, $xml);
            }
        }

        if ($familiarForm->isSubmitted() && $familiarForm->isValid()){
            $file = $familiarForm->get('file')->getViewData();
            \assert($file instanceof UploadedFile);
            $xml = \file_get_contents($file->getPathname());
            if (false !== $xml){
                $this->xmlService->extractPointsFromFile(TravesiaType::FAMILIAR, $xml);
            }
        }

        if ($controlesForm->isSubmitted() && $controlesForm->isValid()){
            $file = $controlesForm->get('file')->getViewData();
            \assert($file instanceof UploadedFile);
            $xml = \file_get_contents($file->getPathname());
            if (false !== $xml){
                $this->xmlService->extractPointsFromFile(TravesiaType::CONTROLES, $xml);
            }
        }



        return $this->render('admin/tracks.html.twig', [
            'tracks' => $tracks,
            'controls' => $controls,
            'form' => [
                'larga' => $largaForm->createView(),
                'corta' => $cortaForm->createView(),
                'familiar' => $familiarForm->createView(),
                'controles' => $controlesForm->createView(),
            ],
        ]);
    }

    #[Route('/controls', name: 'controls', methods: ['GET'])]
    public function controls(Request $request): Response
    {
        $controls = $this->controlRepository->getAllSorted();

        return $this->render('admin/controls.html.twig',
        [
            'controls' => $controls,
        ]);
    }

    #[Route('/controls', name: 'controls_save', methods: ['POST'])]
    public function controlsPost(Request $request): Response
    {
        $controls = \json_decode($request->getContent(), true);

        foreach ($controls as $control){
            $bdControl = $this->controlRepository->findOneBy(['id' => $control['id']]);
            if(null !== $bdControl) {
                \assert($bdControl instanceof Control);
                $bdControl->setControlId(\intval($control['controlId']))
                    ->setComment($control['comment'])
                    ->setDistance(\floatval($control['dist']));
                $this->controlRepository->persist($bdControl);
            }
        }

        $this->controlRepository->flush();

        return new Response(null, 200);
    }

    #[\Symfony\Component\Routing\Annotation\Route(path: '/timetable', name: 'timetable', methods: ['GET'])]
    public function timetable(): Response
    {
        $controles = $this->controlRepository->getAllSorted();
        return $this->render('admin/timetable.html.twig', [
            'controles' => $controles,
        ]);
    }
}
