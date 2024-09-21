<?php

namespace App\Entity;

use App\Repository\TramoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TramoRepository::class)]
class Tramo implements \JsonSerializable
{
    const HORA_DE_CORTE = 12;
    const DEFAULT_MEDIA = 3.8;
    const DELAYED_MEDIA = 3.4;
    const LOST_MEDIA = 3.1;


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $tramo;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $entrada;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $salida;

    #[ORM\ManyToOne(targetEntity: Control::class)]
    #[ORM\JoinColumn(name: 'control_inicio_id', referencedColumnName: 'id')]
    private Control $controlInicio;

    #[ORM\ManyToOne(targetEntity: Control::class)]
    #[ORM\JoinColumn(name: 'control_fin_id', referencedColumnName: 'id')]
    private Control $controlFin;

    #[ORM\ManyToOne(targetEntity: Equipo::class, inversedBy: 'tramos')]
    #[ORM\JoinColumn(name: 'equipo_id', referencedColumnName: 'id')]
    private Equipo|null $equipo = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $media;

    #[ORM\Column(type: 'string', enumType: TramoStatus::class)]
    private TramoStatus $status;

    public function id(): int
    {
        return $this->id;
    }

    public function __construct(string $tramo, Control $controlInicio, Control $controlFin, TramoStatus $status, ?\DateTimeImmutable $entrada, ?\DateTimeImmutable $salida, Equipo $equipo, ?float $media)
    {
        $this->tramo = $tramo;
        $this->controlInicio = $controlInicio;
        $this->controlFin = $controlFin;
        $this->status = $status;
        $this->entrada = $entrada;
        $this->salida = $salida;
        $this->media = $media;
        $this->equipo = $equipo;
    }

    public function tramo(): string
    {
        return $this->tramo;
    }

    public function status(): TramoStatus
    {
        return $this->status;
    }

    public function setStatus(TramoStatus $status): void
    {
        $this->status = $status;
    }

    public function entrada(): ?\DateTimeImmutable
    {
        return $this->entrada;
    }

    public function setEntrada(?\DateTimeImmutable $entrada): void
    {
        $this->entrada = $entrada;
    }

    public function salida(): ?\DateTimeImmutable
    {
        return $this->salida;
    }

    public function setSalida(?\DateTimeImmutable $salida): void
    {
        $this->salida = $salida;
    }

    public function media(): ?float
    {
        return $this->media;
    }

    public function setMedia(?float $media): void
    {
        $this->media = $media;
    }

    public function equipo(): Equipo
    {
        return $this->equipo;
    }

    public function controlInicio(): Control
    {
        return $this->controlInicio;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'tramo' => $this->tramo,
            'status' => $this->status,
            'entrada' => $this->entrada,
            'salida' => $this->salida,
            'media' => $this->media,
            'equipo' => $this->equipo,
        ];
    }
}