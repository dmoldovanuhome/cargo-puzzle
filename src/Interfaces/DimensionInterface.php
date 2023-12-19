<?php

namespace Dmoldovanu\Cargo\Interfaces;

/**
 * @property float|int $width
 * @property float|int $height
 * @property float|int $length
 */
interface DimensionInterface
{
    public function getWidth(): float|int;
    public function getHeight(): float|int;
    public function getLength(): float|int;
}