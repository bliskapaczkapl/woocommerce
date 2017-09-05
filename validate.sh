#!/bin/bash

vendor/bin/phpcs --config-set installed_paths ../../wp-coding-standards/wpcs
vendor/bin/phpcs -s --colors --standard=WordPress bliskapaczka-shipping.php
vendor/bin/phpmd bliskapaczka-shipping.php text codesize
vendor/bin/phpcpd bliskapaczka-shipping.php
# vendor/bin/phpdoccheck --directory=app
vendor/bin/phploc bliskapaczka-shipping.php
vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/unit/