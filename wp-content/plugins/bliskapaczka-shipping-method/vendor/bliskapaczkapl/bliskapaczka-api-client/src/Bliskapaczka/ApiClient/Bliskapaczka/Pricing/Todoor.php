<?php

namespace Bliskapaczka\ApiClient\Bliskapaczka\Pricing;

use Bliskapaczka\ApiClient\Bliskapaczka\Pricing;
use Bliskapaczka\ApiClient\BliskapaczkaInterface;

/**
 * Bliskapaczka class
 *
 * @author  Mateusz Koszutowski (mkoszutowski@divante.pl)
 * @version 0.1.0
 */
class Todoor extends Pricing implements BliskapaczkaInterface
{
    const REQUEST_URL = 'pricing/todoor';
}
