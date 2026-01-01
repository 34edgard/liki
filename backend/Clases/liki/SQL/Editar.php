<?php
namespace Liki\SQL;
use Exception;
use Liki\SQL\SentenciasSql;

class Editar extends SentenciasSql implements iSql {
    // Ahora recibe $parametros por referencia
    public static function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        self::$parametros = [];
        if (empty($propiedades['tabla'])) {
            throw new Exception('Seleccione una tabla');
        }
        if (!isset($propiedades['campos']) || !isset($propiedades['valores']) || count($propiedades['campos']) !== count($propiedades['valores'])) {
            throw new Exception('Los valores no coinciden con los campos');
        }

        self::$sql = "UPDATE ";
        self::añadirTabla($propiedades['tabla']);
        self::$sql .= " SET ";

        $setParts = [];
        foreach ($propiedades['campos'] as $index => $campo) {
            $paramName = self::añadirParametro("update_val_" . str_replace('.', '_', $campo) . "_" . uniqid(), $propiedades['valores'][$index]);
            $setParts[] = "`$campo` = $paramName";
        }
        self::$sql .= implode(", ", $setParts);

        // Cláusula WHERE
        if (isset($propiedades['where']) && is_array($propiedades['where'])) {
            self::añadirWhere($propiedades['where']);
        }

        // Pasa los parámetros generados por referencia
        $parametros = self::$parametros;

        return self::$sql;
    }
}

