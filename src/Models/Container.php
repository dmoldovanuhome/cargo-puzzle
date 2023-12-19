<?php

namespace Emendis\Cargo\Models;

use Emendis\Cargo\Traits\DimensionTrait;
use Emendis\Cargo\Traits\NameTrait;
use Emendis\Cargo\Traits\VolumeTrait;

class Container
{
    /** @var Tile[] */
    protected array $tiles = [];
    /** @var float|int */
    protected float|int $filledLength = 0;
    /**
     * @var Tile|null
     */
    protected ?Tile $chargingTile = null;

    use DimensionTrait,
        VolumeTrait,
        NameTrait;

    public function __construct($name, $width, $height, $length)
    {
        $this->setName($name);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setLength($length);
        $this->setVolume($width * $height * $length);
    }

    /**
     * @param Package $package
     * @return bool
     */
    public function isOutRange(Package $package) : bool
    {
        return $this->width < $package->getWidth() &&
            $this->height < $package->getHeight() &&
            $this->length < $package->getLength();
    }

    /**
     * @return float
     */
    public function getFilledLength(): float
    {
        return $this->filledLength;
    }

    /**
     * @return float
     */
    public function getFreeLength(): float
    {
        return $this->getLength() - $this->getFilledLength();
    }

    /**
     * @return void
     */
    public function completeChargeTile(): void
    {
        $tile = $this->getChargingTile();
        $this->setFilledVolume($this->getFilledVolume() + $tile->getFilledVolume());
        $this->setTile($tile);
        $this->setFilledLength($this->getFilledLength() + $tile->getLength());

        $this->setChargingTile(null);
    }

    public function updateInfoByTiles() : void
    {
        $this->setFilledVolume(0);
        $this->setFilledLength(0);

        foreach ($this->getTiles() as $tile) {
            $this->setFilledVolume($this->getFilledVolume() + $tile->getFilledVolume());
            $this->setFilledLength($this->getFilledLength() + $tile->getLength());
        }
    }

    /**
     * @return Tile[]
     */
    public function getTiles(): array
    {
        return $this->tiles;
    }

    public function setTile(Tile $tile): void
    {
        $this->tiles[] = $tile;
    }

    /**
     * @return int
     */
    public function getPackTotalCount() : int
    {
        $count = 0;
        foreach ($this->getTiles() as $tile) {
            $count += $tile->getFilledCount();
        }

        return $count;
    }

    /**
     * @param float|int $filledLength
     */
    public function setFilledLength(float|int $filledLength): void
    {
        $this->filledLength = $filledLength;
    }

    /**
     * @return Tile|null
     */
    public function getChargingTile(): ?Tile
    {
        return $this->chargingTile;
    }

    /**
     * @param Tile|null $chargingTile
     */
    public function setChargingTile(?Tile $chargingTile): void
    {
        $this->chargingTile = !is_null($chargingTile) ? clone $chargingTile : null;
    }

    /**
     * @return bool
     */
    public function hasChargingTile(): bool
    {
        return $this->chargingTile !== null;
    }
}
