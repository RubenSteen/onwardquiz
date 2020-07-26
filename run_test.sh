#!/bin/bash


printf "\nRunning PHPUnit\n\n"
vendor/bin/phpunit


printf "\n\n\n\n\n\n\n\nRunning PHP_CodeSniffer\n\n"
vendor/bin/phpcs

printf "\n\n\n\n\n\n\n\nRunning PHP Copy/Paste Detector - 15 lines detection\n\n"
vendor/bin/phpcpd app --min-lines=15

printf "\n\n\n\n\n\n\n\nRunning SensioLabs Security Checker\n\n"
vendor/bin/security-checker security:check composer.lock