VENDOR_DIR=wp-content/plugins/bliskapaczka-shipping-method/vendor

docker run --rm -u $(id -u):$(id -g) -v $(pwd):/app -v ~/.composer:/tmp/composer -e COMPOSER_HOME=/tmp/composer composer/composer:php5 install

$VENDOR_DIR/bin/phpcs --config-set installed_paths ../../wp-coding-standards/wpcs
$VENDOR_DIR/bin/phpcs -s --colors --standard=WordPress wp-content/plugins/bliskapaczka-shipping-method/class-bliskapaczka-shipping-method.php
$VENDOR_DIR/bin/phpcpd wp-content/plugins/bliskapaczka-shipping-method/class-bliskapaczka-shipping-method.php
$VENDOR_DIR/bin/phploc wp-content/plugins/bliskapaczka-shipping-method/class-bliskapaczka-shipping-method.php
$VENDOR_DIR/bin/phpunit --bootstrap wp-content/plugins/bliskapaczka-shipping-method/tests/bootstrap.php wp-content/plugins/bliskapaczka-shipping-method/tests/unit/

docker run --rm -u $(id -u):$(id -g) -v $(pwd):/app -v ~/.composer:/tmp/composer -e COMPOSER_HOME=/tmp/composer composer install --no-dev