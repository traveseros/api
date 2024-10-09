<?php

namespace App\Service;

use App\Entity\Control;
use App\Entity\CortaTrack;
use App\Entity\Track;
use App\Entity\TravesiaType;
use App\Repository\ControlRepository;
use App\Repository\TrackRepository;

class XmlTrackService
{
    public function __construct(
        private TrackRepository $trackRepository,
        private ControlRepository $controlRepository,
    ){

    }

    public function extractPointsFromFile(TravesiaType $type, string $xml): void
    {
        $this->trackRepository->deleteAll($type);
        $trackData = simplexml_load_string($xml);

        if (TravesiaType::CONTROLES !== $type) {
            foreach (($trackData->trk->trkseg) as $trackPoint) {
                $previousLat = null;
                $previousLon = null;
                $previousDist = 0;

                $accu_distance = 0;

                foreach ($trackPoint->trkpt as $trkpt) {
                    $attributes = ((array)($trkpt))['@attributes'];
                    $lat = (float)$attributes['lat'];
                    $lon = (float)$attributes['lon'];

                    if ($previousLat === null) {
                        $distance = 0;
                    } else {
                        $distance = $this->distance($previousLat, $previousLon, $lat, $lon, 'K') * 1000;
                    }

                    if (!is_nan($distance)) {
                        $distance = round($distance, 4);
                        $accu_distance += $distance;
                        $previousDist = $distance;
                    } else {
                        $distance = $previousDist;
                    }

                    $previousLat = $lat;
                    $previousLon = $lon;
                    $trackPointModel = new Track($type, $lat, $lon, $distance, round($accu_distance, 2));
                    $this->trackRepository->persist($trackPointModel);


                }
            }
            $this->trackRepository->flush();
        } else {
            foreach ($trackData->wpt as $trackPoint) {
                $comentario = (string)$trackPoint->cmt ?? '';
                $attributes = ((array)($trackPoint))['@attributes'];
                $lat = (float)$attributes['lat'];
                $lon = (float)$attributes['lon'];
                $controls_raw_ids = (string)$trackPoint->name;
                $controls_id = explode('-', $controls_raw_ids);
                if(is_array($controls_id)){
                    foreach ($controls_id as $controlId){
                        $control = new Control(
                            $lat,
                            $lon,
                            intval($controlId),
                            $comentario,
                            $controls_raw_ids,
                            1000,
                        );
                        $this->controlRepository->persist($control);
                    }
                }else{
                    $control = new Control(
                        $lat,
                        $lon,
                        intval($controls_raw_ids),
                        $comentario,
                        $controls_raw_ids,
                        1000,
                    );
                    $this->controlRepository->persist($control);
                }
            }

            $this->controlRepository->flush();
        }
    }

    private function distance($lat1, $lon1, $lat2, $lon2, $unit): float|int
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "m") {
            return ($miles * 1.609344) * 1000;
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}