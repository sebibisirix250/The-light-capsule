<?php

//TEXT AND STRING CLEANING
function cleanString(?string $value, int $maxLength = 255): string
{
    $value = trim((string)$value);

    if ($value === '') {
        return '';
    }

    //SPACE CLEANING
    $value = preg_replace('/\s+/', ' ', $value);

    if (mb_strlen($value) > $maxLength) {
        $value = mb_substr($value, 0, $maxLength);
    }

    return $value;
}

//VALIDATE AND FORMAT EMAIL ADDRESS
function cleanEmail(?string $value): string
{
    $value = trim((string)$value);

    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return '';
    }

    //LOWER CASE FOR STORING EMAILS
    return mb_strtolower($value);
}

//FORCE VALUE INTO INTEGER RANGE
function cleanInt($value, int $min = 0, int $max = PHP_INT_MAX): int
{
    $value = (int)$value;

    if ($value < $min) return $min;
    if ($value > $max) return $max;

    return $value;
}

//FORCE VALUE INTO FLOAT RANGE - PRICES
function cleanFloat($value, float $min = 0.0, float $max = PHP_FLOAT_MAX): float
{
    $value = (float)$value;

    if ($value < $min) return $min;
    if ($value > $max) return $max;

    return $value;
}

//CLEAN LARGE TEXTS INPUTS
function cleanTextarea(?string $value, int $maxLength = 2000): string
{
    $value = trim((string)$value);

    if ($value === '') return '';

    if (mb_strlen($value) > $maxLength) {
        $value = mb_substr($value, 0, $maxLength);
    }

    return $value;
}