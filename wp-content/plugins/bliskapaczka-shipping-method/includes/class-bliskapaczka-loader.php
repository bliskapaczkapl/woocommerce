<?php

/**
 * Bliskapaczka autoloader
 */
class Bliskapaczka_Loader
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
        } else if ($class == 'IBAN' || preg_match('#^(IBAN)\b#', $class)) {
        	$filePath = BLISKAPACZKA_ABSPATH . 'vendor/globalcitizen/php-iban/oophp-iban.php';
        } else if (preg_match('#^(Bliskapaczka_Admin_)#', $class)) {
        	$filePath = BLISKAPACZKA_ABSPATH . 'includes/admin/' . self::prepareClassFileName($class) ;
        } else if (preg_match('#^(Bliskapaczka_)#', $class)) {
            $filePath = BLISKAPACZKA_ABSPATH . 'includes/' . self::prepareClassFileName($class);
        }

        if (isset($filePath) && is_file($filePath)) {
            require_once($filePath);
        }
    }
	
    /**
     * Prepare Bliskapaczka class source file name.
     * 
     * @param string $class Class name
     * @return string The file name for given class
     */
    public static function prepareClassFileName($class)
    {
    	return 'class-' . strtr( strtolower( $class ), '_', '-' ) . '.php';
    }
}

spl_autoload_register('Bliskapaczka_Loader::autoloader', true, true);
