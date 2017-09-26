#!/bin/bash

MODULE_DIR=wp-content/plugins/bliskapaczka-shipping-method

$MODULE_DIR/vendor/bin/phpcs --config-set installed_paths ../../wp-coding-standards/wpcs
$MODULE_DIR/vendor/bin/phpcs -s --colors --standard=WordPress $MODULE_DIR/class-bliskapaczka-shipping-method.php
# $MODULE_DIR/vendor/bin/phpmd $MODULE_DIR/class-bliskapaczka-shipping-method.php text codesize
$MODULE_DIR/vendor/bin/phpcpd $MODULE_DIR/class-bliskapaczka-shipping-method.php
# vendor/bin/phpdoccheck --directory=app
$MODULE_DIR/vendor/bin/phploc $MODULE_DIR/class-bliskapaczka-shipping-method.php
$MODULE_DIR/vendor/bin/phpunit --bootstrap $MODULE_DIR/tests/bootstrap.php $MODULE_DIR/tests/unit/