<?php

namespace App\Entity;

use App\Repository\CortaTrackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ControlRepository::class)]
class Control implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $controlId;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $comment;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $groupping;

    #[ORM\Column(type: 'float')]
    private float $lat = 0;

    #[ORM\Column(type: 'float')]
    private float $lon = 0;

    #[ORM\Column(type: 'float')]
    private float $distance = 0;

    public function id(): int
    {
        return $this->id;
    }

    public function __construct(float $lat, float $lon, int $controlId, string $comment, string $groupping, float $distance)
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->controlId = $controlId;
        $this->comment = $comment;
        $this->groupping = $groupping;
        $this->distance = $distance;
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

    public function controlId(): int
    {
        return $this->controlId;
    }

    public function comment(): string
    {
        return $this->comment;
    }

    public function groupping(): string
    {
        return $this->groupping;
    }

    public function setControlId(int $controlId): self
    {
        $this->controlId = $controlId;

        return $this;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
          'id' => $this->id,
          'lat' => $this->lat,
          'lon' => $this->lon,
          'dist' => $this->distance,
          'controlId' => $this->controlId,
          'comment' => $this->comment,
          'groupping' => $this->groupping,
        ];
    }
}