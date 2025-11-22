<?php

namespace Liki\SQL;

use Liki\SQL\SentenciasSql;


class Registrar extends SentenciasSql implements iSql {
    // Ahora recibe $parametros por referencia
    public function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        $this->parametros = [];
        if (empty($propiedades['tabla'])) {
            throw new Exception('Seleccione una tabla');
        }
        if (empty($propiedades['campos']) || empty($propiedades['valores'])) {
            throw new Exception('Campos y valores no pueden estar vacíos para la inserción');
        }
        if (count($propiedades['campos']) !== count($propiedades['valores'])) {
            throw new Exception('La cantidad de campos no coincide con la cantidad de valores');
        }

        $this->sql = "INSERT INTO ";
        $this->añadirTabla($propiedades['tabla']);
        $this->sql .= " (";
        // Añadir campos (nombres de columnas, no se parametrizan)
        $this->añadirPropiedades($propiedades, 'campos');
        $this->sql .= ") VALUES (";

        $marcadores = [];
        foreach ($propiedades['valores'] as $index => $valor) {
            $marcadores[] = $this->añadirParametro("insert_val_" . $index . "_" . uniqid(), $valor);
        }
        $this->sql .= implode(", ", $marcadores);
        $this->sql .= ")";

        // Pasa los parámetros generados por referencia
        $parametros = $this->parametros;

        return $this->sql;
    }
}

