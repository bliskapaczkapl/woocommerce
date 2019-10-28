<?php

namespace Bliskapaczka\ApiClient\ShopVersion;

use Bliskapaczka\ApiClient\Exception;

/**
 * Class PrestaShop
 * @package Bliskapaczka\ApiClient\ShopVersion
 */
class PrestaShop implements ShopVersionInterface
{

    /**
     * PrestaShop constructor.
     */
    public function __construct()
    {
        $root = substr(getcwd(), 0, strpos(getcwd(), '/modules'));
        include_once $root . '/config/settings.inc.php';
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getShopVersion()
    {
        if (defined('_PS_VERSION_')) {
            return _PS_VERSION_;
        }
        throw new Exception('It is not Prestashop 1.6');
    }
}
