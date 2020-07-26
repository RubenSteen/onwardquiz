#!/bin/bash


printf "\nRunning PHPUnit\n\n"
vendor/bin/phpunit


printf "\nRunning PHP_CodeSniffer\n\n"
vendor/bin/phpcs