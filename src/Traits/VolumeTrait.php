<?php

namespace Dmoldovanu\Cargo\Traits;

trait VolumeTrait
{
    /** @var float|int */
    protected float|int $volume = 0;
    /** @var float|int  */
    protected float|int $filledVolume = 0;

    /**
     * @return float|int
     */
    public function getVolume(): float|int
    {
        return $this->volume;
    }

    /**
     * @param float|int $volume
     */
    public function setVolume(float|int $volume): void
    {
        $this->volume = $volume;
    }

    /**
     * @return float|int
     */
    public function getFilledVolume(): float|int
    {
        return $this->filledVolume;
    }

    /**
     * @param float|int $filledVolume
     */
    public function setFilledVolume(float|int $filledVolume): void
    {
        $this->filledVolume = $filledVolume;
    }

    /**
     * @return string
     */
    public function getFilledVolumePercent() : string
    {
        return number_format($this->getFilledVolume() ?
            $this->getFilledVolume() / $this->getVolume() * 100 :
            0, 2);
    }

    /**
     * @return float|int
     */
    public function getFreeVolume() : float|int
    {
        return $this->getVolume() - $this->getFilledVolume();
    }
}