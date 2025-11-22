<?php
namespace Liki\SQL;

use Liki\SQL\SentenciasSql;

class Editar extends SentenciasSql implements iSql {
    // Ahora recibe $parametros por referencia
    public function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        $this->parametros = [];
        if (empty($propiedades['tabla'])) {
            throw new Exception('Seleccione una tabla');
        }
        if (!isset($propiedades['campos']) || !isset($propiedades['valores']) || count($propiedades['campos']) !== count($propiedades['valores'])) {
            throw new Exception('Los valores no coinciden con los campos');
        }

        $this->sql = "UPDATE ";
        $this->añadirTabla($propiedades['tabla']);
        $this->sql .= " SET ";

        $setParts = [];
        foreach ($propiedades['campos'] as $index => $campo) {
            $paramName = $this->añadirParametro("update_val_" . str_replace('.', '_', $campo) . "_" . uniqid(), $propiedades['valores'][$index]);
            $setParts[] = "`$campo` = $paramName";
        }
        $this->sql .= implode(", ", $setParts);

        // Cláusula WHERE
        if (isset($propiedades['where']) && is_array($propiedades['where'])) {
            $this->añadirWhere($propiedades['where']);
        }

        // Pasa los parámetros generados por referencia
        $parametros = $this->parametros;

        return $this->sql;
    }
}

