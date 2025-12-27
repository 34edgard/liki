<?php

namespace Liki\SQL;
use Liki\SQL\SentenciasSql;


class Eliminar extends SentenciasSql implements iSql {
    // Ahora recibe $parametros por referencia
    public static function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        self::$parametros = [];
        if (empty($propiedades['tabla'])) {
            throw new Exception('Seleccione una tabla');
        }

        self::$sql = "DELETE FROM ";
        self::añadirTabla($propiedades['tabla']);

        // Cláusula WHERE (obligatoria para seguridad)
        if (isset($propiedades['where']) && is_array($propiedades['where'])) {
            self::añadirWhere($propiedades['where']);
        } else {
            throw new Exception('La cláusula WHERE es obligatoria para la eliminación.');
        }

        // Pasa los parámetros generados por referencia
        $parametros = self::$parametros;

        return self::$sql;
    }
}


