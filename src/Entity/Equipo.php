<?php

namespace App\Entity;

use App\Repository\EquipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipoRepository::class)]
class Equipo implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $equipoId;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name;

    #[ORM\Column(type: 'integer', nullable: false)]
    private string $travesiaId;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $media;

    #[ORM\OneToMany(mappedBy: 'equipo', targetEntity: Tramo::class)]
    private Collection $tramos;




    public function __construct(int $equipoId, ?string $name, string $travesiaId, float $media = Tramo::DEFAULT_MEDIA)
    {
        $this->equipoId = $equipoId;
        $this->name = $name;
        $this->travesiaId = $travesiaId;
        $this->media = $media;
        $this->tramos = new ArrayCollection();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function equipoId(): int
    {
        return $this->equipoId;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function tramos(): Collection
    {
        return $this->tramos;
    }

    public function addTramo(Tramo $tramo): void
    {
        $this->tramos->add($tramo);
    }


    public function travesiaId(): string
    {
        return $this->travesiaId;
    }

    public function media(): float
    {
        return $this->media;
    }

    public function setMedia(float $media): void
    {
        $this->media = $media;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'equipoId' => $this->equipoId,
            'name' => $this->name,
            'travesiaId' => $this->travesiaId,
            'media' => $this->media,
        ];
    }
}