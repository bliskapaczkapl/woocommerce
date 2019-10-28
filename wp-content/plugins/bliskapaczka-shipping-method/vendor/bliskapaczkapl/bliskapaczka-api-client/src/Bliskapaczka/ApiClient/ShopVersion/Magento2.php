<?php

namespace Bliskapaczka\ApiClient\ShopVersion;

use Bliskapaczka\ApiClient\Exception;

/**
 * Class Magento2
 * @package Bliskapaczka\ApiClient\ShopVersion
 */
class Magento2 implements ShopVersionInterface
{
    /**
     * @return mixed|string
     * @throws Exception
     */
    public function getShopVersion()
    {
        $root = substr(getcwd(), 0, strpos(getcwd(), '/vendor'));
        $bashRes = shell_exec($root . '/bin/magento --version');
        if (!is_null($bashRes)) {
            return str_replace('Magento CLI ', '', $bashRes);
        }
        throw new Exception('It is not Magento 2');
    }
}
