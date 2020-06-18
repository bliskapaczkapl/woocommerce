<?php
//Set custom memory limit
ini_set('memory_limit', '512M');
ini_set('error_reporting', E_ALL);

$GLOBALS['ROOT_DIR'] = dirname(__FILE__) . '/../';
define('BLISKAPACZKA_ABSPATH', $GLOBALS['ROOT_DIR']);
define('ABSPATH', $GLOBALS['ROOT_DIR']);

//Define include path for Pseudo Mocks
// ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . '/pseudo_mock');

// Load Pseudo Mocks
// require_once 'Address.php';
// require_once 'Configuration.php';
// require_once 'Customer.php';
// require_once 'Order.php';

require_once $GLOBALS['ROOT_DIR'] . '/vendor/autoload.php';

function autoloader($class)
{
    if (preg_match('#^(Bliskapaczka_Shipping_Method_)#', $class)) {
    	$class = str_replace('_', '-', $class);
        $class = strtolower($class);
        $class = 'class-' . $class . '.php';

        $filePath = $GLOBALS['ROOT_DIR'] . 'includes/' . $class;
        // @codingStandardsIgnoreStart
        require_once($filePath);
        // @codingStandardsIgnoreEnd
    }

    if (preg_match('#^(WC_(?!.*Abstract))#', $class)) {
    	$class = str_replace('_', '-', $class);
        $class = strtolower($class);
        $class =  $class . '.php';

        $filePath = $GLOBALS['ROOT_DIR'] . '../woocommerce/includes/abstracts/abstract-' . $class;

        // @codingStandardsIgnoreStart
        require_once($filePath);
        // @codingStandardsIgnoreEnd
    }

    if (preg_match('#^(WC_Abstract)#', $class)) {
        $class = str_replace('_Abstract', '', $class);
    	$class = str_replace('_', '-', $class);
        $class = strtolower($class);
        $class = 'abstract-' . $class . '.php';

        $filePath = $GLOBALS['ROOT_DIR'] . '../woocommerce/includes/abstracts/' . $class;
        // @codingStandardsIgnoreStart
        require_once($filePath);
        // @codingStandardsIgnoreEnd
    }

    if ($class == 'Bliskapaczka_Map_Shipping_Method') {
        $filePath = BLISKAPACZKA_ABSPATH . 'includes/class-bliskapaczka-map-shipping-method.php';
        // @codingStandardsIgnoreStart
        require_once($filePath);
        // @codingStandardsIgnoreEnd
    }
    if ($class == 'Bliskapaczka_Courier_Shipping_Method') {
        $filePath = BLISKAPACZKA_ABSPATH . 'includes/class-bliskapaczka-courier-shipping-method.php';
        // @codingStandardsIgnoreStart
        require_once($filePath);
        // @codingStandardsIgnoreEnd
    }

    $filePath =  $GLOBALS['ROOT_DIR'] . '../woocommerce/includes/class-wc-order.php';
    if (isset($filePath) && is_file($filePath)) {
        require_once($filePath);
    }

}

spl_autoload_register('autoloader');
        