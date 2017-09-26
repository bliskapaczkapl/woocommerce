#!/bin/bash

MODULE_DIR=./wp-content/plugins/bliskapaczka-shipping-method

mkdir -p ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping-method
cp -r $MODULE_DIR/assets ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping-method/
cp -r $MODULE_DIR/includes ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping-method/
cp -r $MODULE_DIR/tests ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping-method/
cp -r $MODULE_DIR/vendor ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping-method/
cp $MODULE_DIR/class-bliskapaczka-shipping-method.php ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping-method/