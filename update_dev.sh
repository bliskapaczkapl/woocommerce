#!/bin/bash

mkdir -p ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping/
scp -r ./assets ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping/
scp -r ./includes ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping/
scp -r ./vendor ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping/
scp ./bliskapaczka-shipping.php ../vagrant_woocommerce/htdocs/woocommerce.dev/wp-content/plugins/bliskapaczka-shipping/