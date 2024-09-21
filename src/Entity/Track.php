<?php

namespace App\Entity;

use App\Repository\CortaTrackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CortaTrackRepository::class)]
class Track implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'string', enumType: TravesiaType::class)]
    private TravesiaType $type;

    #[ORM\Column(type: 'float')]
    private float $lat = 0;

    #[ORM\Column(type: 'float')]
    private float $lon = 0;

    #[ORM\Column(type: 'float')]
    private float $distance = 0;

    #[ORM\Column(type: 'float')]
    private float $accumulated = 0;

    public function id(): int
    {
        return $this->id;
    }

    public function __construct(TravesiaType $type, float $lat, float $lon, float $distance, float $accumulated)
    {
        $this->type = $type;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->distance = $distance;
        $this->accumulated = $accumulated;
    }

    public function type(): TravesiaType
    {
        return $this->type;
    }

    public function lat(): float
    {
        return $this->lat;
    }

    public function lon(): float
    {
        return $this->lon;
    }

    public function distance(): float
    {
        return $this->distance;
    }

    public function accumulated(): float
    {
        return $this->accumulated;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'dist' => $this->distance,
            'accu' => $this->accumulated,
        ];
    }
}