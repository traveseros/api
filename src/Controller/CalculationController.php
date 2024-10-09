<?php

namespace App\Controller;

use App\Entity\Equipo;
use App\Entity\Tramo;
use App\Entity\TramoStatus;
use App\Entity\TravesiaType;
use App\Repository\ControlRepository;
use App\Repository\CortaTrackRepository;
use App\Repository\EquipoRepository;
use App\Repository\FamiliarTrackRepository;
use App\Repository\LargaTrackRepository;
use App\Repository\TrackRepository;
use App\Repository\TramoRepository;
use App\Service\SpreadsheetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculationController extends AbstractController
{
    public function __construct(
        private TrackRepository $trackRepository,
        private EquipoRepository $equipoRepository,
        private TramoRepository $tramoRepository,
        private ControlRepository $controlRepository,
    ){}
    #[Route(path: '/async/current/track', name: 'current', methods: ['GET'])]
    public function home(): Response
    {
        $results = [];
        $distanciasControles = [
            '1' => [],
            '2' => [],
            '3' => [],
        ];
        $controles = $this->controlRepository->getAllSorted();
        $carry1 = 0;
        $carry2 = 0;
        $carry3 = 0;
        foreach ($controles as $control){
            if($control->controlId() < 20){
                $distanciasControles['1'][$control->controlId()] = $carry1;
                $carry1 += $control->distance();
            }elseif ($control->controlId() < 30){
                $distanciasControles['2'][$control->controlId()] = $carry2;
                $carry2 += $control->distance();
            }else{
                $distanciasControles['3'][$control->controlId()] = $carry3;
                $carry3 += $control->distance();
            }
        }

        $equipos = $this->equipoRepository->findAll();
        foreach ($equipos as $equipo){
            /* @var Equipo $equipo */
            $started = array_filter($equipo->tramos()->toArray(), fn(Tramo $tramo) => $tramo->status() === TramoStatus::IN_TRANSIT || $tramo->status() === TramoStatus::DELAYED || $tramo->status() === TramoStatus::LOST);
            if(\count($started) === 0){
                continue;
            }
            $last = array_pop($started);
            /** @var Tramo $last */
            if($last->status() === TramoStatus::IN_TRANSIT) {
                $secondsNow = (new \DateTimeImmutable())->getTimestamp();
                $distancia = round($distanciasControles[intval($equipo->travesiaId())][$last->controlInicio()->controlId()] + (($last->media() * 1000) / 3600) * ($secondsNow - $last->entrada()->getTimestamp()));
                if($distancia <= $distanciasControles[intval($equipo->travesiaId())][$last->controlInicio()->controlId() +1]) {
                    $trackPoint = null;
                    switch ($equipo->travesiaId()) {
                        case 1:
                            $trackPoint = $this->trackRepository->findOnePoint($distancia, TravesiaType::LARGA);
                            break;
                        case 2:
                            $trackPoint = $this->trackRepository->findOnePoint($distancia, TravesiaType::CORTA);
                            break;
                        case 3:
                            $trackPoint = $this->trackRepository->findOnePoint($distancia, TravesiaType::FAMILIAR);
                            break;
                    }
                    $results[] = [
                        'dorsal' => $equipo->equipoId(),
                        'tramo' => $last->tramo(),
                        'status' => $last->status(),
                        'lat' => $trackPoint?->lat(),
                        'lon' => $trackPoint?->lon(),
                    ];
                }else{
                    $results[] = [
                        'dorsal' => $equipo->equipoId(),
                        'tramo' => $last->tramo(),
                        'status' => TramoStatus::DELAYED,
                        'lat' => null,
                        'lon' => null,
                    ];
                }
            }else{
                $results[] = [
                    'dorsal' => $equipo->equipoId(),
                    'tramo' => $last->tramo(),
                    'status' => $last->status(),
                    'lat' => null,
                    'lon' => null,
                ];
            }
        }

        return new JsonResponse($results);
    }

    #[Route(path: '/async/current/table', name: 'current-table', methods: ['GET'])]
    public function table(): Response
    {
        $salida = [1 => [], 2 => [], 3 => []];
        $equipos = $this->equipoRepository->findAll();
        foreach ($equipos as $equipo) {
            $salida[$equipo->travesiaId()][$equipo->equipoId()] = [
                'dorsal' => $equipo->equipoId(),
                'equipo' => $equipo->name(),
                'tramos' => [],
            ];
            foreach($equipo->tramos() as $tramo){
                /** @var Tramo $tramo */
                $previsto = null;
                $lastValidTime = null;
                if(null !== $tramo->entrada() && ($tramo->status() === TramoStatus::IN_TRANSIT ||$tramo->status() === TramoStatus::DELAYED || $tramo->status() === TramoStatus::LOST)){
                    $calculo = $tramo->entrada()->getTimestamp() + ((1000/3600) * $tramo->media() * $tramo->controlInicio()->distance());
                    $previsto = ((new \DateTimeImmutable())->setTimestamp($calculo));
                    $lastValidTime = $previsto;
                }

                if($tramo->status() === TramoStatus::NOT_STARTED && $tramo->controlInicio()->controlId() !== 10 && $tramo->controlInicio()->controlId() !== 20 && $tramo->controlInicio()->controlId() !== 30 ) {
                    $validKey = \count($salida[$equipo->travesiaId()][$equipo->equipoId()]['tramos']) - 1;
                    $entrada = $salida[$equipo->travesiaId()][$equipo->equipoId()]['tramos'][$validKey]['lastValidTime'];

                    if ($entrada !== null){
                        $calculo = $entrada->getTimestamp() + ((1000 / 3600) * $tramo->media() * $tramo->controlInicio()->distance());
                        $previsto = ((new \DateTimeImmutable())->setTimestamp($calculo));
                        $lastValidTime = $previsto;
                    }
                }

                if($tramo->status() === TramoStatus::NOT_STARTED && ($tramo->controlInicio()->controlId() === 10 || $tramo->controlInicio()->controlId() === 20 || $tramo->controlInicio()->controlId() === 30)){
                    if ($tramo->entrada() !== null) {
                        $calculo = $tramo->entrada()->getTimestamp() + ((1000 / 3600) * $tramo->media() * $tramo->controlInicio()->distance());
                        $previsto = ((new \DateTimeImmutable())->setTimestamp($calculo));
                        $lastValidTime = $previsto;
                    }
                }

                $salida[$equipo->travesiaId()][$equipo->equipoId()]['tramos'][] = [
                    'tramo' => $tramo->tramo(),
                    'media' => round($tramo->media(),2),
                    'hora' => $tramo->entrada(),
                    'hora-timestamp' => $tramo->entrada()?->getTimestamp(),
                    'status' => $tramo->status()->value,
                    'llegada' => $previsto,
                    'llegada-timestamp' => $previsto?->getTimestamp(),
                    'lastValidTime' => $lastValidTime,
                ];
            }
        }
        return new JsonResponse($salida);
    }
}