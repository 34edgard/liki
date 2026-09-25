<?php

namespace Liki\KitPHP;

use Stringable;
use InvalidArgumentException;

class Num implements Stringable
{
    private int|float $value;

    public function __construct(int|float|string $value)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException("Num requiere un valor numérico, se recibió: " . gettype($value));
        }
        $this->value = $value + 0; // castea a int|float
    }

    /* ---------- Constructor estático ---------- */

    public static function of(int|float|string $value): self
    {
        return new self($value);
    }

    /* ---------- Operaciones aritméticas (mutan y devuelven $this) ---------- */

    public function add(int|float|string $n): self
    {
        $this->value += $n;
        return $this;
    }

    public function subtract(int|float|string $n): self
    {
        $this->value -= $n;
        return $this;
    }

    public function multiply(int|float|string $n): self
    {
        $this->value *= $n;
        return $this;
    }

    public function divide(int|float|string $n): self
    {
        if ((float) $n === 0.0) {
            throw new InvalidArgumentException('División por cero.');
        }
        $this->value /= $n;
        return $this;
    }

    public function mod(int|float $n): self
    {
        if ((float) $n === 0.0) {
            throw new InvalidArgumentException('Módulo por cero.');
        }
        $this->value = $this->value % $n;
        return $this;
    }

    public function pow(int|float $exponent): self
    {
        $this->value = $this->value ** $exponent;
        return $this;
    }

    public function sqrt(): self
    {
        if ($this->value < 0) {
            throw new InvalidArgumentException('sqrt de un número negativo.');
        }
        $this->value = sqrt($this->value);
        return $this;
    }

    public function abs(): self
    {
        $this->value = abs($this->value);
        return $this;
    }

    public function negate(): self
    {
        $this->value = -$this->value;
        return $this;
    }

    /* ---------- Redondeo ---------- */

    public function round(int $precision = 0): self
    {
        $this->value = round($this->value, $precision);
        return $this;
    }

    public function floor(): self
    {
        $this->value = (int) floor($this->value);
        return $this;
    }

    public function ceil(): self
    {
        $this->value = (int) ceil($this->value);
        return $this;
    }

    public function truncate(int $precision = 0): self
    {
        $factor = 10 ** $precision;
        $this->value = (int) ($this->value * $factor) / $factor;
        return $this;
    }

    /* ---------- Formato / presentación ---------- */

    public function format(int $decimals = 2, string $decimalSep = '.', string $thousandsSep = ','): self
    {
        // OJO: esto convierte a string. Lo guardamos como float original
        // y ofrecemos un método aparte para formatear la salida.
        $this->value = (float) number_format($this->value, $decimals, $decimalSep, $thousandsSep);
        return $this;
    }

    /**
     * Devuelve el número formateado SIN mutar la instancia.
     */
    public function toFormatted(int $decimals = 2, string $decimalSep = '.', string $thousandsSep = ','): string
    {
        return number_format($this->value, $decimals, $decimalSep, $thousandsSep);
    }

    /**
     * Interpreta $this->value como un porcentaje de $total.
     * Ej: Num::of(25)->percentageOf(200) => 12.5
     */
    public function percentageOf(int|float $total): float
    {
        if ((float) $total === 0.0) {
            throw new InvalidArgumentException('El total no puede ser cero.');
        }
        return ($this->value / $total) * 100;
    }

    /**
     * Aplica un porcentaje al número actual.
     * Ej: Num::of(200)->applyPercentage(15) => 30
     */
    public function applyPercentage(int|float $percent): self
    {
        $this->value = $this->value * ($percent / 100);
        return $this;
    }

    /* ---------- Comparaciones / consultas ---------- */

    public function isZero(): bool
    {
        return $this->value == 0;
    }

    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    public function isNegative(): bool
    {
        return $this->value < 0;
    }

    public function isEven(): bool
    {
        return $this->value % 2 === 0;
    }

    public function isOdd(): bool
    {
        return $this->value % 2 !== 0;
    }

    public function greaterThan(int|float $n): bool
    {
        return $this->value > $n;
    }

    public function lessThan(int|float $n): bool
    {
        return $this->value < $n;
    }

    public function equals(int|float $n): bool
    {
        return $this->value == $n;
    }

    public function between(int|float $min, int|float $max): bool
    {
        return $this->value >= $min && $this->value <= $max;
    }

    public function clamp(int|float $min, int|float $max): self
    {
        $this->value = max($min, min($max, $this->value));
        return $this;
    }

    /* ---------- Salidas ---------- */

    public function toInt(): int
    {
        return (int) $this->value;
    }

    public function toFloat(): float
    {
        return (float) $this->value;
    }

    public function toNum(): int|float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}


/*
//use Liki\KitPHP\Num;

// Aritmética encadenada
echo Num::of(10)->add(5)->multiply(2)->subtract(3)->divide(9);
// 3

// Redondeo
echo Num::of(3.14159)->round(2);          // 3.14
echo Num::of(3.9)->floor();               // 3
echo Num::of(3.1)->ceil();                // 4

// Potencia y raíz
echo Num::of(2)->pow(10);                 // 1024
echo Num::of(144)->sqrt();                // 12

// Porcentajes
echo Num::of(25)->percentageOf(200);      // 12.5
echo Num::of(200)->applyPercentage(15);   // 30

// Comparaciones
Num::of(4)->isEven();                     // true
Num::of(7)->isOdd();                      // true
Num::of(5)->between(1, 10);               // true
Num::of(50)->clamp(1, 10);                // 10

// Formato (sin mutar)
echo Num::of(1234567.891)->toFormatted(2, '.', ','); // "1,234,567.89"

// Validación
//Num::of("abc"); // lanza InvalidArgumentException
//Num::of(5)->divide(0); // lanza InvalidArgumentException

*/