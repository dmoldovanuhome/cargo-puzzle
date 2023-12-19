<?php

namespace Emendis\Cargo\Traits;

trait DimensionTrait
{
    /** @var float|int */
    protected float|int $width;
    /** @var float|int */
    protected float|int $height;
    /** @var float|int */
    protected float|int $length;

    /**
     * @return float|int
     */
    public function getWidth(): float|int
    {
        return $this->width;
    }

    /**
     * @return float|int
     */
    public function getHeight(): float|int
    {
        return $this->height;
    }

    /**
     * @return float|int
     */
    public function getLength(): float|int
    {
        return $this->length;
    }

    /**
     * @param float|int $width
     */
    public function setWidth(float|int $width): void
    {
        $this->width = $width;
    }

    /**
     * @param float|int $height
     */
    public function setHeight(float|int $height): void
    {
        $this->height = $height;
    }

    /**
     * @param float|int $length
     */
    public function setLength(float|int $length): void
    {
        $this->length = $length;
    }
}