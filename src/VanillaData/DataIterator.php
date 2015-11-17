<?php

namespace Rentalhost\VanillaData;

use ArrayIterator;

/**
 * Class DataIterator
 * @package Rentalhost\VanillaData
 */
class DataIterator extends ArrayIterator
{
    /**
     * Get the current value encapsulated by Data.
     *
     * @return Data,
     */
    public function current()
    {
        return new Data(parent::current());
    }
}
