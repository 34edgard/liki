
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

