<?php

namespace Dmoldovanu\Cargo\Models;

use Dmoldovanu\Cargo\Interfaces\DimensionInterface;
use Dmoldovanu\Cargo\Traits\CountTrait;
use Dmoldovanu\Cargo\Traits\DimensionTrait;
use Dmoldovanu\Cargo\Traits\NameTrait;
use Dmoldovanu\Cargo\Traits\VolumeTrait;

class Package implements DimensionInterface
{
    /** @var float|int */
    protected float|int $packVolume;
    protected int $unchargedCount;

    use DimensionTrait,
        VolumeTrait,
        CountTrait,
        NameTrait;

    public function __construct($width, $height, $length, $count)
    {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setLength($length);
        $this->setPackVolume($width * $height * $length);
        $this->setVolume($this->packVolume * $count);
        $this->setCount($count);
        $this->setUnchargedCount($count);
        $this->setName($this->getWidth() . 'x' . $this->getHeight() . 'x' . $this->getLength());
    }

    /**
     * @return int
     */
    public function getUnchargedCount() : int
    {
        return $this->unchargedCount;
    }

    /**
     * @param int $unchargedCount
     */
    public function setUnchargedCount(int $unchargedCount): void
    {
        $this->unchargedCount = $unchargedCount;
    }

    /**
     * @return float|int
     */
    public function getPackVolume(): float|int
    {
        return $this->packVolume;
    }

    /**
     * @param float|int $packVolume
     */
    public function setPackVolume(float|int $packVolume): void
    {
        $this->packVolume = $packVolume;
    }

    /**
     * @param int $count
     * @return void
     */
    public function charge(int $count) : void
    {
        $this->setUnchargedCount($this->getUnchargedCount() - $count);
    }
}
