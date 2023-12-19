<?php

namespace Dmoldovanu\Cargo\Models;

use Dmoldovanu\Cargo\Traits\CountTrait;
use Dmoldovanu\Cargo\Traits\DimensionTrait;
use Dmoldovanu\Cargo\Traits\VolumeTrait;

class Tile
{
    protected int $filledCount = 0;
    /** @var int number of packs can be set by width */
    protected int $rowCount;
    /** @var int  number of packs can be set by height */
    protected int $colCount;
    /** @var float|int */
    protected float|int $fillableVolume;

    protected array $packs = [];

    use DimensionTrait,
        VolumeTrait,
        CountTrait;

    public function __construct(Container $container, Package $package)
    {
        $this->setRowCount((int) floor($container->getWidth() / $package->getWidth()));
        $this->setColCount((int) floor($container->getHeight() / $package->getHeight()));
        $this->setCount($this->rowCount *  $this->colCount);
        $this->setWidth($container->getWidth());
        $this->setHeight($container->getHeight());
        $this->setLength($package->getLength());
        $this->setVolume($container->getWidth() * $container->getHeight() * $package->getLength());
        $this->fillableVolume = $package->getPackVolume() * $this->count;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * @return int
     */
    public function getColCount(): int
    {
        return $this->colCount;
    }

    /**
     * @return float|int
     */
    public function getFillableVolume(): float|int
    {
        return $this->fillableVolume;
    }

    /**
     * @param float|int $fillableVolume
     */
    public function setFillableVolume(float|int $fillableVolume): void
    {
        $this->fillableVolume = $fillableVolume;
    }

    /**
     * @param Package $package
     * @param int|null $col
     * @param int|null $row
     * @return void
     */
    public function fillPack(Package $package, int $col = null, int $row = null) : void
    {
        $packs = [
            1 => [ //X=1
                1 => 'A', //Y=1
                2 => 'A', //Y=2
                3 => 'A', //Y=3
            ],
            2 => [  //X=2
                1 => 'A', //Y=1
                2 => 'A', //Y=2
                3 => 'A', //Y=3
            ],
            3 => [  //X=3
                1 => 'A', //Y=1
                2 => 'A', //Y=2
            ],
        ];
        $x = array_key_last($this->getPacks()) ?? 1;
        if (!is_null($row)) {
            $x = $row;
        }

        if (!array_key_exists($x, $this->getPacks())) {
            $this->packs[$x] = [];
        }

        $y = array_key_last($this->getPacks()[$x]) ?? 1;
        if (!is_null($col)) {
            $y = $col;
        }

//        if (count($this->getPacks()[$x]) < $this->getColCount()) {
        if ($package->getHeight() < $this->getColFreeHeight($x)) {
            $y++;
        } else {
            $y = 1;
            $x++;
        }

        if (isset($this->packs[$x][$y]) && $this->packs[$x][$y] instanceof Pack) return; //position is already filled

        $package->charge(1);
        $this->packs[$x][$y] = new Pack($package);

        $this->setFilledVolume($this->getFilledVolume() + $package->getPackVolume());
        $this->filledCount++;
    }

    public function getColFreeHeight($col): float|int
    {
        $height = $this->getHeight();
        $height -= array_reduce($this->getPacks()[$col], function ($sum, $pack) {
           return $sum + $pack->getHeight();
        });
        return $height;
    }

    public function getFilledWidth() : float|int
    {
        $width = 0;
        foreach ($this->getPacks() as $col) {
           $width +=  $col[array_key_first($col)]->getWidth();
        }
        return $width;
    }

    /**
     * @return array
     */
    public function getPacks(): array
    {
        return $this->packs;
    }

    /**
     * @return int
     */
    public function getFilledCount(): int
    {
        return $this->filledCount;
    }

    /**
     * @param int $colCount
     */
    public function setColCount(int $colCount): void
    {
        $this->colCount = $colCount;
    }

    /**
     * @param int $rowCount
     */
    public function setRowCount(int $rowCount): void
    {
        $this->rowCount = $rowCount;
    }
}
