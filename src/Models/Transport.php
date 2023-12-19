<?php

namespace Emendis\Cargo\Models;

use Emendis\Cargo\Traits\VolumeTrait;

class Transport
{
    use VolumeTrait;

    /** @var Package[] Packages of transport */
    protected array $packages = [];
    /**
     * @var Container[] $containers Filled containers with packages
     */
    protected array $containers = [];

    /** @var Container|null Current container that is trying to charge with packages */
    protected ?Container $chargingContainer = null;

    /**
     * @return array
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @param Package[] $packages
     */
    public function setPackages(array $packages): void
    {
        $this->packages = $packages;
    }

    public function addPackage(Package $package) : void
    {
        $this->packages[] = $package;
    }

    /**
     * @return Container[]
     */
    public function getContainers(): array
    {
        return $this->containers;
    }

    public function addContainer(Container $container) : void
    {
        $this->containers[] = $container;
    }

    /**
     * @param Container|null $container
     * @return void
     */
    public function setChargingContainer(?Container $container) : void
    {
        $this->chargingContainer = !is_null($container) ? clone $container : null;
    }

    public function getChargingContainer() : Container
    {
        return $this->chargingContainer;
    }

    public function finishCharging() : void
    {
        $this->addContainer($this->getChargingContainer());
        $this->setChargingContainer(null);
    }

    /**
     * @return bool
     */
    public function hasChargingContainer() : bool
    {
        return $this->chargingContainer !== null;
    }
}
