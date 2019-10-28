<?php

namespace Bliskapaczka\ApiClient\ShopVersion;

/**
 * Class Magento1
 * @package Bliskapaczka\ApiClient\ShopVersion
 */
class Magento1 implements ShopVersionInterface
{
    /**
     * @inheritDoc
     */
    public function getShopVersion()
    {
        return '1.x';
    }
}
