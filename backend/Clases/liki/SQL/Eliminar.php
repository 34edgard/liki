<?php

namespace Liki\SQL;
use Liki\SQL\SentenciasSql;


class Eliminar extends SentenciasSql implements iSql {
    // Ahora recibe $parametros por referencia
    public function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        $this->parametros = [];
        if (empty($propiedades['tabla'])) {
            throw new Exception('Seleccione una tabla');
        }

        $this->sql = "DELETE FROM ";
        $this->añadirTabla($propiedades['tabla']);

        // Cláusula WHERE (obligatoria para seguridad)
        if (isset($propiedades['where']) && is_array($propiedades['where'])) {
            $this->añadirWhere($propiedades['where']);
        } else {
            throw new Exception('La cláusula WHERE es obligatoria para la eliminación.');
        }

        // Pasa los parámetros generados por referencia
        $parametros = $this->parametros;

        return $this->sql;
    }
}


