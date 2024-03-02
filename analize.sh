#!/bin/bash
vendor/bin/phpcs --standard=PSR12 app/ --runtime-set ignore_warnings_on_exit 1
vendor/bin/phpmd app/ text phpmd_rules.xml
php -d memory_limit=2G vendor/bin/phpstan analyse -c phpstan.neon