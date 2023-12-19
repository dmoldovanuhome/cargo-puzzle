<?php

namespace Emendis\Cargo\Models;

use Emendis\Cargo\Traits\DimensionTrait;
use Emendis\Cargo\Traits\NameTrait;
use Emendis\Cargo\Traits\VolumeTrait;

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