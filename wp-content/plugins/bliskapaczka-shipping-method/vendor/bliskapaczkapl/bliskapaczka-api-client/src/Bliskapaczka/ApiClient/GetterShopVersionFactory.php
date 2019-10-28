<?php

namespace Bliskapaczka\ApiClient;

/**
 * Class GetterShopVersionFactory
 * @package Bliskapaczka\ApiClient
 */
class GetterShopVersionFactory
{
    /**
     * @param string $shopName
     * @return string
     * @throws Exception
     */
    public static function getByShopName($shopName)
    {
        try {
            $class = '\Bliskapaczka\ApiClient\ShopVersion' . '\\' . $shopName;
            $inst = new $class();
            return $inst->getShopVersion();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
