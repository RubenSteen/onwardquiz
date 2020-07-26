#!/bin/bash


printf "\nRunning PHPUnit\n\n"
vendor/bin/phpunit


printf "\nRunning PHP_CodeSniffer\n\n"
vendor/bin/phpcs

printf "\nRunning PHP Copy/Paste Detector - 15 lines detection\n\n"
vendor/bin/phpcpd app --min-lines=15