<?php

namespace Bliskapaczka\ApiClient\ShopVersion;

use Bliskapaczka\ApiClient\Exception;

/**
 * Class Woocommerce
 * @package Bliskapaczka\ApiClient\ShopVersion
 */
class Woocommerce implements ShopVersionInterface
{

    /**
     * Woocommerce constructor.
     */
    public function __construct()
    {
        $GLOBALS['ROOT_DIR'] = dirname(__FILE__) . '/../';
        if (!defined('ABSPATH')) {
            define('ABSPATH', $GLOBALS['ROOT_DIR']);
        }
        
        $root = substr(getcwd(), 0, strpos(getcwd(), '/wp-content'));
        if (empty($root)) {
            $root = substr(getcwd(), 0, strpos(__DIR__, '/wp-content'));
        }
        include_once $root . '/wp-content/plugins/woocommerce/includes/class-woocommerce.php';
    }

    /**
     * @return string
     * @throws Exception
     * @throws \ReflectionException
     */
    public function getShopVersion()
    {
        try {
            $obj = new \ReflectionClass('WooCommerce');
            $inst = $obj->newInstanceWithoutConstructor();
            return $inst->version;
        } catch (Exception $exception) {
            throw new Exception(sprintf('%s %s', 'It is not Woocommerce.', $exception->getMessage()));
        }
    }
}
