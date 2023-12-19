<?php

namespace Dmoldovanu\Cargo\Models;

use Dmoldovanu\Cargo\Traits\DimensionTrait;
use Dmoldovanu\Cargo\Traits\NameTrait;
use Dmoldovanu\Cargo\Traits\VolumeTrait;

class Pack
{
    use VolumeTrait,
        DimensionTrait,
        NameTrait;

    public function __construct(Package $package)
    {
        $this->setVolume($package->getPackVolume());
        $this->setWidth($package->getWidth());
        $this->setHeight($package->getHeight());
        $this->setLength($package->getLength());
        $this->setName($package->getName());
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}