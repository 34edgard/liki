<?php

namespace Liki\SQL;

use Liki\SQL\SentenciasSql;


class Registrar extends SentenciasSql implements iSql {
    // Ahora recibe $parametros por referencia
    public static function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        self::$parametros = [];
        if (empty($propiedades['tabla'])) {
            throw new Exception('Seleccione una tabla');
        }
        if (empty($propiedades['campos']) || empty($propiedades['valores'])) {
            throw new Exception('Campos y valores no pueden estar vacíos para la inserción');
        }
        if (count($propiedades['campos']) !== count($propiedades['valores'])) {
            throw new Exception('La cantidad de campos no coincide con la cantidad de valores');
        }

        self::$sql = "INSERT INTO ";
        self::añadirTabla($propiedades['tabla']);
        self::$sql .= " (";
        // Añadir campos (nombres de columnas, no se parametrizan)
        self::añadirPropiedades($propiedades, 'campos');
        self::$sql .= ") VALUES (";

        $marcadores = [];
        foreach ($propiedades['valores'] as $index => $valor) {
            $marcadores[] = self::añadirParametro("insert_val_" . $index . "_" . uniqid(), $valor);
        }
        self::$sql .= implode(", ", $marcadores);
        self::$sql .= ")";

        // Pasa los parámetros generados por referencia
        $parametros = self::$parametros;

        return self::$sql;
    }
}

