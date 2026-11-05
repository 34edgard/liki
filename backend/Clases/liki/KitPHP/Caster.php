<?php
declare(strict_types=1);
namespace Liki\KitPHP;



class Caster
{
    /**
     * Convierte un valor a entero
     */
    public static function toInt($value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return 0;
            }
            return (int) $trimmed;
        }

        if (is_null($value)) {
            return 0;
        }

        if (is_array($value) || is_object($value)) {
            throw new InvalidArgumentException(
                'No se puede convertir un array u objeto a entero'
            );
        }

        return (int) $value;
    }

    /**
     * Convierte un valor a float
     */
    public static function toFloat($value): float
    {
        if (is_float($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 1.0 : 0.0;
        }

        if (is_int($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return 0.0;
            }
            // Soportar coma decimal
            $trimmed = str_replace(',', '.', $trimmed);
            return (float) $trimmed;
        }

        if (is_null($value)) {
            return 0.0;
        }

        throw new InvalidArgumentException(
            'No se puede convertir a float el tipo: ' . gettype($value)
        );
    }

    /**
     * Convierte un valor a string
     */
    public static function toString($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_null($value)) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            return implode(',', array_map([self::class, 'toString'], $value));
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE) ?: '';
        }

        return (string) $value;
    }

    /**
     * Convierte un valor a booleano
     */
    public static function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_null($value)) {
            return false;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            $falseValues = ['', '0', 'false', 'no', 'off', 'null', 'nil', 'none'];
            return !in_array($normalized, $falseValues, true);
        }

        if (is_array($value)) {
            return count($value) > 0;
        }

        return (bool) $value;
    }

    /**
     * Convierte un valor a array
     */
    public static function toArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_null($value)) {
            return [];
        }

        if (is_object($value)) {
            return get_object_vars($value);
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            // Intentar JSON primero
            if ($trimmed !== '' && in_array($trimmed[0], ['{', '['], true)) {
                $decoded = json_decode($trimmed, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return is_array($decoded) ? $decoded : [$decoded];
                }
            }
            // Separar por comas
            return $trimmed === '' ? [] : array_map('trim', explode(',', $trimmed));
        }

        return [$value];
    }

    /**
     * Convierte un valor a objeto (stdClass)
     */
    public static function toObject($value): object
    {
        if (is_object($value)) {
            return $value;
        }

        if (is_array($value)) {
            return (object) $value;
        }

        return (object) ['value' => $value];
    }

    /**
     * Convierte un valor a null
     */
    public static function toNull($value)
    {
        return null;
    }

    /**
     * Conversión genérica por nombre de tipo
     */
    public static function cast($value, string $type)
    {
        return match (strtolower($type)) {
            'int', 'integer'    => self::toInt($value),
            'float', 'double'   => self::toFloat($value),
            'string', 'str'     => self::toString($value),
            'bool', 'boolean'   => self::toBool($value),
            'array', 'arr'      => self::toArray($value),
            'object', 'obj'     => self::toObject($value),
            'null'              => self::toNull($value),
            default => throw new InvalidArgumentException(
                "Tipo de conversión no soportado: {$type}"
            ),
        };
    }

    /**
     * Convierte todos los valores de un array a un tipo específico
     */
    public static function castArray(array $values, string $type): array
    {
        return array_map(
            fn($value) => self::cast($value, $type),
            $values
        );
    }
}