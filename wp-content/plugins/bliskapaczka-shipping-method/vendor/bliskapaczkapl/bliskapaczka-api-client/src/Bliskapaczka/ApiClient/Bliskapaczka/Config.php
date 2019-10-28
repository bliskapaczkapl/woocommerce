<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka;

use Bliskapaczka\ApiClient\BliskapaczkaInterface;
use Bliskapaczka\ApiClient\AbstractBliskapaczka;

/**
 * Class Config
 * @package Bliskapaczka\ApiClient\Bliskapaczka
 * @author Paweł Karbowniczek (pkarbowniczek@divante.pl)
 */
class Config extends AbstractBliskapaczka implements BliskapaczkaInterface
{

    /** @var string  */
    const REQUEST_URL = 'config';

    /**
     * @return json
     */
    public function get()
    {
        return $this->doCall(self::REQUEST_URL);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function validate(array $data)
    {
        return true;
    }
}
