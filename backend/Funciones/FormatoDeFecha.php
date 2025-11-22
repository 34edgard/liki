<?php
function isValidDateFormat(string $dateString, string $dateFormat= "Y-m-d"): bool
{
    $dateTime = DateTime::createFromFormat($dateFormat, $dateString);

    // DateTime::createFromFormat devuelve FALSE si el formato no coincide.
    // También devuelve FALSE si la fecha es inválida (ej: 30 de febrero).
    // Para verificar solo el formato, usamos la siguiente comprobación:
    return $dateTime !== false && $dateTime->format($dateFormat) === $dateString;
}