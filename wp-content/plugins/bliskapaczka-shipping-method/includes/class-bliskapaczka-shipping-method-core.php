<?php

/**
 * Bliskapaczka Core class
 */
class Bliskapaczka_Shipping_Method_Core
{
    /**
     * Autoloader for Bliskapaczka namespace
     *
     * @param string $class
     */
    public static function autoloader($class)
    {
        if (preg_match('#^(Bliskapaczka\\\\ApiClient)\b#', $class)) {
            $libDir = BLISKAPACZKA_ABSPATH . 'vendor/bliskapaczkapl/bliskapaczka-api-client/src/';
            $filePath = $libDir . str_replace('\\', '/', $class) . '.php';
        }

        if (preg_match('#^(IBAN)\b#', $class)) {
            $libDir = BLISKAPACZKA_ABSPATH . 'vendor/globalcitizen/php-iban/';
            $filePath = $libDir . 'oophp-iban.php';
        }

        if (preg_match('#^(Bliskapaczka_Shipping_Method_)#', $class)) {
            $filePath = BLISKAPACZKA_ABSPATH . 'includes/' . self::prepareClassFileName($class);
        }
        if ($class == 'Bliskapaczka_Map_Shipping_Method') {
            $filePath = BLISKAPACZKA_ABSPATH . 'includes/class-bliskapaczka-map-shipping-method.php';
        }
        if ($class == 'Bliskapaczka_Courier_Shipping_Method') {
            $filePath = BLISKAPACZKA_ABSPATH . 'includes/class-bliskapaczka-courier-shipping-method.php';
        }
        if (isset($filePath) && is_file($filePath)) {
            require_once($filePath);
        }
    }

    public static function prepareClassFileName($class)
    {
        $class = str_replace('_', '-', $class);
        $class = strtolower($class);
        $class = 'class-' . $class . '.php';

        return $class;
    }
}

spl_autoload_register('Bliskapaczka_Shipping_Method_Core::autoloader', true, true);
