<?php
namespace Liki\SQL;
use Exception;


abstract class SentenciasSql {
    protected static $sql = '';
    protected static array $parametros = []; // Nuevo array para almacenar los parámetros

    protected static function añadirCampo(string $campo): void {
        self::$sql .= " $campo ";
    }

    // Este método ya no es necesario para construir SQL directamente
    // Se usará para añadir los valores a los parámetros.
    protected static function añadirParametro(string $nombre, mixed $valor): string {
        // Puedes usar marcadores de posición con nombre (preferido) o posicionales.
        // Usaremos ":nombre" para mayor legibilidad y flexibilidad.
        $paramName = ":" . str_replace('.', '_', $nombre); // Reemplazar puntos si vienen en el nombre del campo
        self::$parametros[$paramName] = $valor;
        return $paramName; // Retorna el marcador de posición
    }

    protected static function añadirPropiedades(array $propiedades, string $nombrePropiedad): void {
        if (!isset($propiedades[$nombrePropiedad]) || !is_array($propiedades[$nombrePropiedad])) {
            return;
        }

        $longitud = count($propiedades[$nombrePropiedad]);
        foreach ($propiedades[$nombrePropiedad] as $indice => $contenido) {
            if ($nombrePropiedad === 'campos') {
                self::añadirCampo((string) $contenido);
            } elseif ($nombrePropiedad === 'valores_para_insertar') { // Nuevo nombre para distinguir de campos
                self::$sql .= self::añadirParametro("val_" . $indice, $contenido); // Marcador de posición
            } elseif ($nombrePropiedad === 'orderBy') { // Si necesitas parámetros en ORDER BY (raro)
                 self::$sql .= "`$contenido`"; // ORDER BY generalmente no necesita parámetros para los nombres de columnas
            }
            // Agrega más lógica si otras propiedades necesitan manejo especial de parámetros

            if ($indice < $longitud - 1) {
                self::$sql .= ', ';
            }
        }
    }

    protected static function añadirWhere(array $condiciones): void {
        if (empty($condiciones)) {
            return;
        }

        self::$sql .= " WHERE ";
        self::añadirCondiciones($condiciones, 'AND');
    }

    protected static function añadirCondiciones(array $condiciones, string $operadorLogico): void {
        $partes = [];
        foreach ($condiciones as $condicion) {
            if (is_array($condicion) && isset($condicion['operador'])) {
                $partes[] = self::generarCondicionEspecial($condicion);
            } elseif (is_array($condicion) && count($condicion) === 1 && isset($condicion['OR'])) {
                $orPartes = [];
                foreach ($condicion['OR'] as $orCondicion) {
                    // Si es una condición compleja dentro de OR
                    if (is_array($orCondicion) && isset($orCondicion['operador'])) {
                        $orPartes[] = self::generarCondicionEspecial($orCondicion);
                    } else {
                        // Asume formato 'campo' => 'valor' para OR simple
                        foreach ($orCondicion as $campo => $valor) {
                            $paramName = self::añadirParametro("where_" . str_replace('.', '_', $campo) . "_" . uniqid(), $valor);
                            $orPartes[] = "`$campo` = $paramName";
                        }
                    }
                }
                $partes[] = "(" . implode(" OR ", $orPartes) . ")";
            } else {
                // Condición simple 'campo' => 'valor'
                foreach ($condicion as $campo => $valor) {
                    $paramName = self::añadirParametro("where_" . str_replace('.', '_', $campo) . "_" . uniqid(), $valor);
                    $partes[] = "`$campo` = $paramName";
                }
            }
        }
        self::$sql .= implode(" $operadorLogico ", array_filter($partes));
    }

    protected static function generarCondicionEspecial(array $condicion): string {
        switch (strtoupper($condicion['operador'])) {
            case 'BETWEEN':
                if (!isset($condicion['campo'], $condicion['valor1'], $condicion['valor2'])) {
                    throw new Exception("Condición BETWEEN incompleta.");
                }
                $campo = $condicion['campo'];
                $paramName1 = self::añadirParametro("between_" . str_replace('.', '_', $campo) . "_1_" . uniqid(), $condicion['valor1']);
                $paramName2 = self::añadirParametro("between_" . str_replace('.', '_', $campo) . "_2_" . uniqid(), $condicion['valor2']);
                return "`$campo` BETWEEN $paramName1 AND $paramName2";
            case 'IN':
                if (!isset($condicion['campo'], $condicion['valores']) || !is_array($condicion['valores'])) {
                    throw new Exception("Condición IN incompleta o valores no es un arreglo.");
                }
                $campo = $condicion['campo'];
                $inParams = [];
                foreach ($condicion['valores'] as $idx => $val) {
                    $inParams[] = self::añadirParametro("in_" . str_replace('.', '_', $campo) . "_" . $idx . "_" . uniqid(), $val);
                }
                return "`$campo` IN (" . implode(", ", $inParams) . ")";
            case 'LIKE':
                if (!isset($condicion['campo'], $condicion['valor'])) {
                    throw new Exception("Condición LIKE incompleta.");
                }
                $campo = $condicion['campo'];
                // Para LIKE, los comodines (%) deben ser parte del valor del parámetro, no del SQL
                $paramName = self::añadirParametro("like_" . str_replace('.', '_', $campo) . "_" . uniqid(), '%' . $condicion['valor'] . '%');
                return "`$campo` LIKE $paramName";
            case 'IN_SUBQUERY':
                // Para subconsultas, la subconsulta ya debería ser segura (generada por otra iSql)
                if (!isset($condicion['campo'], $condicion['subquery'])) {
                    throw new Exception("Condición IN_SUBQUERY incompleta.");
                }
                $campo = $condicion['campo'];
                // Las subconsultas no reciben parámetros directamente de la consulta padre
                // Asegúrate de que la subconsulta maneje sus propios parámetros internamente.
                $subquerySql = self::obtenerSqlSubconsulta($condicion['subquery']);
                return "`$campo` IN ($subquerySql)";
            case 'EXISTS':
                // Similar a IN_SUBQUERY, la subconsulta maneja sus propios parámetros.
                if (!isset($condicion['subquery'])) {
                    throw new Exception("Condición EXISTS incompleta.");
                }
                $subquerySql = self::obtenerSqlSubconsulta($condicion['subquery']);
                return "EXISTS ($subquerySql)";
            default:
                // Operadores estándar
                if (!isset($condicion['campo'], $condicion['valor'])) {
                    throw new Exception("Condición de operador estándar incompleta.");
                }
                $campo = $condicion['campo'];
                $paramName = self::añadirParametro("op_" . str_replace('.', '_', $campo) . "_" . uniqid(), $condicion['valor']);
                return "`$campo` " . $condicion['operador'] . " $paramName";
        }
    }

    protected static function obtenerSqlSubconsulta(mixed $subquery): string {
        if ($subquery instanceof iSql) {
            // Si es una instancia de una sentencia SQL (ej. Consultar), generar su SQL
            // ¡IMPORTANTE! Las subconsultas deben manejar sus propios parámetros internamente.
            // Esto significa que $subquery->generar_sql() DEBE agregar sus parámetros
            // al array de parámetros de la propia instancia de subquery, NO a $this->parametros.
            // Si la subconsulta es compleja, esto podría requerir un manejo más avanzado
            // o que la subconsulta sea un objeto Consultar completamente independiente.
            $subqueryParams = []; // La subconsulta tendrá sus propios parámetros
            return $subquery->generar_sql([], $subqueryParams); // Genera SQL de la subconsulta
        } elseif (is_string($subquery)) {
            // Si es una cadena, asumimos que es SQL ya formado (¡asegúrate de que sea seguro!)
            return $subquery;
        } else {
            throw new Exception("Subconsulta no válida. Debe ser una instancia iSql o una cadena SQL.");
        }
    }

    protected static function añadirTabla(string $tabla): void {
        self::$sql .= " `$tabla` ";
    }

    // Nuevo método para pasar los parámetros generados
    public static function obtenerParametros(): array {
        return self::$parametros;
    }
}

