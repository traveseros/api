<?php

namespace App\Command;

use App\Entity\Equipo;
use App\Entity\Tramo;
use App\Entity\TramoStatus;
use App\Repository\ControlRepository;
use App\Repository\EquipoRepository;
use App\Repository\TramoRepository;
use App\Service\SpreadsheetService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fetch-data')]
class FetchDataFromSpreadsheetCommand extends Command
{
    private array $controles = [];

    public function __construct(
        private SpreadsheetService $spreadsheetService,
        private EquipoRepository $equipoRepository,
        private ControlRepository $controlRepository,
        private TramoRepository $tramoRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        foreach ($this->controlRepository->findAll() as $control){
            $this->controles[$control->controlId()] = $control;
        }

        //$this->tramoRepository->deleteAll();

        $output->writeln('Fetching data from excel at ' . (new \DateTimeImmutable())->format('Y/m/d H:i:s'));
        $output->writeln('DATA FROM LARGA ...');
        $this->decodeForLarga($this->spreadsheetService->get(1));
        $output->writeln('Importados todos los datos de la Larga');


        $output->writeln('DATA FROM CORTA');
        $this->decodeForCorta($this->spreadsheetService->get(2));
        $output->writeln('Importados todos los datos de la corta');


        $output->writeln('DATA FROM FAMILIAR');
        $this->decodeForFamiliar($this->spreadsheetService->get(3));
        $output->writeln('Importados todos los datos de la corta');
        $this->tramoRepository->flush();
        $output->writeln('-------------------FIN-------------------');



        return Command::SUCCESS;
    }

    private function decodeForLarga(array $data)
    {
        foreach ($data as $key => $row){
            if ($key < 3){
                continue;
            }

            $equipo = $this->equipoRepository->findOneBy(['equipoId' => $row[0]]);
            if(null === $equipo){
                $equipo = new Equipo(
                    $row[0],
                    $row[1],
                    1,
                );
                $this->equipoRepository->persistAndFlush($equipo);
            }else{
                if($equipo->name() !== $row[1]){
                    $equipo->setName($row[1]);
                    $this->equipoRepository->persistAndFlush($equipo);
                }
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '10-11']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[2] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[4] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 2, 4, 10, 11);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '11-12']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[5] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[6] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 5, 6, 11, 12);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '12-13']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[7] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[10] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 7, 10, 12, 13);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '13-14']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[11] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[12] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 11, 12, 13, 14);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '14-15']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[13] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[14] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 13, 14, 14, 15);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '15-16']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[15] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[17] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 15, 17, 15, 16);
            }
            $this->tramoRepository->flush();
            $tramos = $this->tramoRepository->findBy(['equipo' => $equipo]);

            $tramosAcabados = array_filter($tramos, fn(Tramo $tramo) => $tramo->status() === TramoStatus::FINISH);
            $mediaEquipo = array_reduce(
                $tramosAcabados,
                fn($carry, Tramo $tramo) => $carry + $tramo->media(),
                0
            );
            if(\count($tramosAcabados) > 0 || $mediaEquipo > 0){
                $mediaEquipo = $mediaEquipo / \count($tramosAcabados);
            }else{
                $mediaEquipo = Tramo::DEFAULT_MEDIA;
            }

            $equipo->setMedia($mediaEquipo);
            $this->equipoRepository->persistAndFlush($equipo);

            $tramos = $this->tramoRepository->findBy(['equipo' => $equipo]);
            foreach (array_filter($tramos, fn(Tramo $tramo) => $tramo->status() !== TramoStatus::FINISH) as $tramo){
                $tramo->setMedia($mediaEquipo);
                $this->tramoRepository->persist($tramo);
            }
            $this->tramoRepository->flush();
        }
    }

    private function decodeForCorta(array $data)
    {
        foreach ($data as $key => $row){
            if ($key < 3){
                continue;
            }
            $equipo = $this->equipoRepository->findOneBy(['equipoId' => $row[0]]);
            if(null === $equipo){
                $equipo = new Equipo(
                    $row[0],
                    $row[1],
                    2,
                );
                $this->equipoRepository->persistAndFlush($equipo);
            }else{
                if($equipo->name() !== $row[1]){
                    $equipo->setName($row[1]);
                    $this->equipoRepository->persistAndFlush($equipo);
                }
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '20-21']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[2] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[3] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 2, 3, 20, 21);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '21-22']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[4] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[5] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 4, 5, 21, 22);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '22-23']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[6] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[7] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 6, 7, 22, 23);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '23-24']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[8] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[9] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 8, 9, 23, 24);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '24-25']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[10] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[11] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 10, 11, 24, 25);
            }

            $this->tramoRepository->flush();
            $tramos = $this->tramoRepository->findBy(['equipo' => $equipo]);

            $tramosAcabados = array_filter($tramos, fn(Tramo $tramo) => $tramo->status() === TramoStatus::FINISH);
            $mediaEquipo = array_reduce(
                $tramosAcabados,
                fn($carry, Tramo $tramo) => $carry + $tramo->media(),
                0
            );
            if(\count($tramosAcabados) > 0 || $mediaEquipo > 0){
                $mediaEquipo = $mediaEquipo / \count($tramosAcabados);
            }else{
                $mediaEquipo = Tramo::DEFAULT_MEDIA;
            }

            $equipo->setMedia($mediaEquipo);
            $this->equipoRepository->persistAndFlush($equipo);

            $tramos = $this->tramoRepository->findBy(['equipo' => $equipo]);
            foreach (array_filter($tramos, fn(Tramo $tramo) => $tramo->status() !== TramoStatus::FINISH) as $tramo){
                $tramo->setMedia($mediaEquipo);
                $this->tramoRepository->persist($tramo);
            }
            $this->tramoRepository->flush();
        }
    }

    private function decodeForFamiliar(array $data)
    {
        foreach ($data as $key => $row){
            if ($key < 3){
                continue;
            }
            $equipo = $this->equipoRepository->findOneBy(['equipoId' => $row[0]]);
            if(null === $equipo){
                $equipo = new Equipo(
                    $row[0],
                    $row[1],
                    3,
                );
                $this->equipoRepository->persistAndFlush($equipo);
            }else{
                if($equipo->name() !== $row[1]){
                    $equipo->setName($row[1]);
                    $this->equipoRepository->persistAndFlush($equipo);
                }
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '30-31']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[2] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[3] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 2, 3, 30, 31);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '31-32']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[4] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[5] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 4, 5, 31, 32);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '32-33']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[6] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[7] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 6, 7, 32, 33);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '33-34']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[8] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[9] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 8, 9, 33, 34);
            }

            $oldTramo = $this->tramoRepository->findOneBy(['equipo' => $equipo, 'tramo' => '34-35']);
            if(null === $oldTramo
                || $oldTramo->entrada() !== $this->parseDate($row[10] ?? null)
                || $oldTramo->salida() !== $this->parseDate($row[11] ?? null)){
                $this->createOrUpdateTramo($oldTramo, $equipo, $row, 10, 11, 34, 35);
            }

            $this->tramoRepository->flush();
            $tramos = $this->tramoRepository->findBy(['equipo' => $equipo]);

            $tramosAcabados = array_filter($tramos, fn(Tramo $tramo) => $tramo->status() === TramoStatus::FINISH);
            $mediaEquipo = array_reduce(
                $tramosAcabados,
                fn($carry, Tramo $tramo) => $carry + $tramo->media(),
                0
            );
            if(\count($tramosAcabados) > 0 || $mediaEquipo > 0){
                $mediaEquipo = $mediaEquipo / \count($tramosAcabados);
            }else{
                $mediaEquipo = Tramo::DEFAULT_MEDIA;
            }

            $equipo->setMedia($mediaEquipo);
            $this->equipoRepository->persistAndFlush($equipo);

            $tramos = $this->tramoRepository->findBy(['equipo' => $equipo]);
            foreach (array_filter($tramos, fn(Tramo $tramo) => $tramo->status() !== TramoStatus::FINISH) as $tramo){
                $tramo->setMedia($mediaEquipo);
                $this->tramoRepository->persist($tramo);
            }
            $this->tramoRepository->flush();
        }
    }

    private function parseDate(?string $date): ?\DateTimeImmutable
    {
        if('' === $date || null === $date){
            return null;
        }
//dump($date);
        $parts = explode(':', $date);
        $addDays = 0;
        if(intval($parts[0] > Tramo::HORA_DE_CORTE)){
            $addDays = -1;
        }
//dump($addDays);
        return (new \DateTimeImmutable($date))->modify($addDays. ' days');
    }

    private function calculteMean(?\DateTimeImmutable $inicio, ?\DateTimeImmutable $fin, $distancia): float
    {
        if(null === $inicio && null === $fin){
            return Tramo::DEFAULT_MEDIA;
        }

        if(null === $inicio || $inicio > new \DateTimeImmutable()){
            return Tramo::DEFAULT_MEDIA;
        }

        if(null === $fin){
            $fin = new \DateTimeImmutable();
        }
        return ($distancia/1000)/(($fin->getTimestamp() - $inicio->getTimestamp())/3600);
    }

    private function createOrUpdateTramo(
        ?Tramo $tramo,
        Equipo $equipo,
        mixed $row,
        int $indexStart,
        int $indexFinish,
        int $controlIdInicio,
        int $controlIdFin): void
    {
        $tramoName = sprintf('%s-%s', $controlIdInicio, $controlIdFin);

        $inicioTramo = $this->parseDate($row[$indexStart] ?? null);
        $finTramo = $this->parseDate($row[$indexFinish] ?? null);

        $distancia = $this->controles[$controlIdInicio]->distance();
        $media = $this->calculteMean($inicioTramo, $finTramo, $distancia);

        if(null === $finTramo){
            if(null === $inicioTramo || $inicioTramo > new \DateTimeImmutable()){
                $status = TramoStatus::NOT_STARTED;
            }else{
                if($media <= Tramo::LOST_MEDIA){
                    $status = TramoStatus::LOST;
                }elseif ($media > Tramo::LOST_MEDIA && $media <= Tramo::DELAYED_MEDIA){
                    $status = TramoStatus::DELAYED;
                }else{
                    $media = $equipo->media();
                    $status = TramoStatus::IN_TRANSIT;
                }
            }
        }else{
            $status = TramoStatus::FINISH;
        }

        if(null === $tramo) {
            $tramo = new Tramo(
                $tramoName,
                $this->controles[$controlIdInicio],
                $this->controles[$controlIdFin],
                $status,
                $inicioTramo,
                $finTramo,
                $equipo,
                $media,
            );
        }else{
            $tramo->setEntrada($inicioTramo);
            $tramo->setSalida($finTramo);
            $tramo->setStatus($status);
            $tramo->setMedia($this->calculteMean($inicioTramo, $finTramo, $distancia));
        }

        $this->tramoRepository->persist($tramo);
    }
}