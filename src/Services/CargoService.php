<?php

namespace Dmoldovanu\Cargo\Services;

use Dmoldovanu\Cargo\Enum\ContainerSet;
use Dmoldovanu\Cargo\Enum\Rotate;
use Dmoldovanu\Cargo\Enum\Sort;
use Dmoldovanu\Cargo\Enum\TransportSet;
use Dmoldovanu\Cargo\Models\Container;
use Dmoldovanu\Cargo\Models\Pack;
use Dmoldovanu\Cargo\Models\Package;
use Dmoldovanu\Cargo\Models\Tile;
use Dmoldovanu\Cargo\Models\Transport;
use Dmoldovanu\Cargo\Traits\DimensionTrait;
use Dmoldovanu\Cargo\Traits\VolumeTrait;

class CargoService
{
    /** @var Container[]  */
    private array $containers;

    public function __construct()
    {
        $containers = $this->getContainers();
        $this->sortByVolume($containers, Sort::ASC);
        $this->containers = $containers;
    }

    /**
     * @return array
     */
    public function getContainerSet() : array
    {
        return [
            '10ft Standard Dry Container' => ContainerSet::FT10,
            '40ft Standard Dry Container' => ContainerSet::FT40,
        ];
    }

    /**
     * @return Container[]
     */
    public function getContainers() : array
    {
        $res  = [];
        foreach ($this->getContainerSet() as $name => $item)
        {
            $res[] = new Container($name, $item['width'], $item['height'], $item['length']);
        }

        return $res;
    }

    public function getTransportSet() : array
    {
        return [
            TransportSet::One,
            TransportSet::Two,
            TransportSet::Three,
        ];
    }

    public function getRotationSet(): array
    {
        return [
            TransportSet::Four,
        ];
    }

    public function calculateLinear(...$args) : array
    {
        $transportSet = [];
        foreach ($this->getTransportSet() as $k => $data) {
            $transport = $this->createTransport($data);
            if (array_key_exists('sort', $args)) {
                $packages = $transport->getPackages();
                $this->sortByVolume($packages, $args['sort']);
                $transport->setPackages($packages);
            }

            $transportSet[] = $this->linear($transport);
        }

        return $transportSet;
    }

    public function linear(Transport $transport) : Transport
    {
        foreach ($transport->getPackages() as $package) {
            $selectedContainer = $this->selectContainerForTransport($transport);
            $tile = new Tile($selectedContainer, $package);

            $this->fillPackageByTile($transport, $package, $selectedContainer, $tile);
        }

        $transport->finishCharging();

        return $transport;
    }

    public function calculateMerged(...$args) : array
    {
        $transportSet = [];
        $set = $this->getTransportSet();
        if (array_key_exists('rotation', $args)) {
            $set = $this->getRotationSet();
        }

        foreach ($set as $data) {
            $transport = $this->createTransport($data);

            if (array_key_exists('rotation', $args)) {
                $this->rotatePackages($transport, $args['rotation']);
            }

            $order = Sort::DESC;
            if (array_key_exists('sort', $args)) {
                $order = $args['sort'];
            }
            $packages = $transport->getPackages();
            $this->sortByLength($packages, $order);
            $transport->setPackages($packages);

            $transportSet[] = $this->merged($transport);
        }

        return $transportSet;
    }

    public function calculateNotRotated(...$args) : array
    {
        $transportSet = [];
        foreach ($this->getRotationSet() as $data) {
            $transport = $this->createTransport($data);

            $order = Sort::DESC;
            if (array_key_exists('sort', $args)) {
                $order = $args['sort'];
            }
            $packages = $transport->getPackages();
            $this->sortByLength($packages, $order);
            $transport->setPackages($packages);

            $transportSet[] = $this->merged($transport);
        }

        return $transportSet;
    }

    public function merged(Transport $transport) : Transport
    {
        foreach ($transport->getPackages() as $package) {
            $selectedContainer = $this->selectContainerForTransport($transport);
            $tile = new Tile($selectedContainer, $package);

            // we have already charged
            if (count($transport->getContainers())) {
                foreach ($transport->getContainers() as $container) {
                    $this->tryAddPackageInContainer($container, $package);
                }
            }

            // we have a charging container
            if ($transport->hasChargingContainer()) {
                $this->tryAddPackageInContainer($transport->getChargingContainer(), $package);
            }

            $this->fillPackageByTile($transport, $package, $selectedContainer, $tile);
        }

        $transport->finishCharging();

        return  $transport;
    }

    public function createTransport(array $data) : Transport
    {
        $transport = new Transport();

        foreach ($data as $packageData) {
            $package = new Package(
                $packageData['sizes']['width'],
                $packageData['sizes']['height'],
                $packageData['sizes']['length'],
                $packageData['count']
            );

            $transport->addPackage($package);
            $transport->setVolume($transport->getVolume() + $package->getVolume());
        }

        return $transport;
    }

    /**
     * Sort array of entities that have volume
     * @param array $set
     * @param int $order
     * @return void
     */
    public function sortByVolume(array &$set, int $order) : void
    {
        usort($set, function ($a, $b) use ($order) {
            /** @var VolumeTrait $a */
            /** @var VolumeTrait $b */
            if ($a->getVolume() === $b->getVolume()) return 0;

            if ($order === Sort::DESC)
                return $a->getVolume() < $b->getVolume() ? 1 : -1;
            return $a->getVolume() < $b->getVolume() ? -1 : 1;
        });
    }

    public function sortByLength(array &$set, int $order) : void
    {
        usort($set, function ($a, $b) use ($order) {
            /** @var DimensionTrait $a */
            /** @var DimensionTrait $b */
            if ($a->getLength() === $b->getLength()) return 0;

            if ($order === Sort::DESC)
                return $a->getLength() < $b->getLength() ? 1 : -1;
            return $a->getLength() < $b->getLength() ? -1 : 1;
        });
    }

    private function selectContainerForTransport(Transport $transport) : Container
    {
        $selectedContainer = null;

        foreach ($this->containers as $container) {
            //todo this code should be refactored and not check just volume
            if ($transport->getFreeVolume() < $container->getVolume()) {
                $selectedContainer = $container;
                break;
            }
        }

        if ($selectedContainer === null) {
            //then set max volume container
            $selectedContainer = $this->containers[array_key_last($this->containers)];
        }

        return $selectedContainer;
    }

    private function fillPackageByTile(Transport $transport, Package $package, Container $selectedContainer, Tile $tile) : void
    {
        while (true) {
            if ($package->getUnchargedCount() === 0) {
                //here we special finish tile, even is not fulfilled
                if ($transport->getChargingContainer()->hasChargingTile()) {
                    $transport->getChargingContainer()->completeChargeTile();
                }
                break;
            }

            if (!$transport->hasChargingContainer()) {
                $transport->setChargingContainer($selectedContainer);
            }

            //start next container
            if ($transport->getChargingContainer()->getFreeLength() < $package->getLength()) {
                //fill in Transport charged volume
                $transport->setFilledVolume(
                    $transport->getFilledVolume() + $transport->getChargingContainer()->getFilledVolume()
                );
                $selectedContainer = $this->selectContainerForTransport($transport);
                $tile = new Tile($selectedContainer, $package);


                $transport->finishCharging();
                $transport->setChargingContainer($selectedContainer);
            }

            //start new tile
            if (!$transport->getChargingContainer()->hasChargingTile()) {
                $fillingTile = clone $tile;
                $transport->getChargingContainer()->setChargingTile($fillingTile);
            }

            $transport->getChargingContainer()->getChargingTile()->fillPack($package);

            //if is complete filled, then charge into container
            if ($transport->getChargingContainer()->getChargingTile()->getFilledCount() === $tile->getCount()) {
                $transport->getChargingContainer()->completeChargeTile();
            }
        }
    }

    private function tryAddPackageInContainer(Container $container, Package $package) : void
    {
        if ($container->getFreeVolume() < $package->getPackVolume()) { // the container is fulfilled
            return;
        }

        foreach ($container->getTiles() as $tile) {
            $this->tryAddPackageInTile($tile, $package);
        }

        $container->updateInfoByTiles();
    }

    private function tryAddPackageInTile(Tile $tile, Package $package) : void
    {
        if ($tile->getFreeVolume() < $package->getPackVolume()) return; //the tile is fulfilled

        if ($tile->getLength() < $package->getLength()) return; //to big

        foreach ($tile->getPacks() as $row => $col) { //add pack in existent columns
            $filledHeight = array_reduce($col, function ($counted, $pack)  {
                return $counted + $pack->getHeight();
            });

            /** @var Pack $basePack */
            $basePack = $col[array_key_first($col)];
            if (($tile->getHeight() - $filledHeight) > $package->getHeight() && $basePack->getWidth() >= $package->getWidth()) {
                //add pack into existent cols
                $tile->fillPack($package, count($col));
            }
        }


        if (($tile->getWidth() - $tile->getFilledWidth()) > $package->getWidth()) { // enough width for new cols.
            $newCols = (int) floor(($tile->getWidth() - $tile->getFilledWidth()) / $package->getWidth());
            $newRows = (int) floor($tile->getHeight() / $package->getHeight()); // how many packs can be put

            for ($i = 1; $i <= $newRows; $i++) {
                for ($j=1; $j <= $newCols; $j ++) {
                    $tile->fillPack($package, $tile->getRowCount() + $i, $tile->getColCount() + $j);
                }
            }

            $tile->setColCount($tile->getColCount() + $newCols);
            $tile->setRowCount($tile->getRowCount() + $newRows);
        }
    }

    /**
     * @param Transport $transport
     * @param int $rotation
     * @return void
     */
    public function rotatePackages(Transport$transport, int $rotation) : void
    {
        foreach ($transport->getPackages() as $package) {
            switch ($rotation) {
                case Rotate::WL:
                    if ($package->getWidth() > $package->getLength()) {
                        $tmp = $package->getWidth();
                        $package->setWidth($package->getLength());
                        $package->setLength($tmp);
                    }
                    break;
            }
        }
    }
}
