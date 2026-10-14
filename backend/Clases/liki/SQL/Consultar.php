<?php

namespace Liki\SQL;
use Liki\SQL\SentenciasSql;

class Consultar extends SentenciasSql implements iSql {
   
    protected static ?array $unionClause = null;


    public static function setUnion(string $type, mixed $query): self {
        if (!in_array(strtoupper($type), ['UNION', 'UNION ALL'])) {
            throw new \Exception("Tipo de UNION no válido. Use 'UNION' o 'UNION ALL'.");
        }
        if (!($query instanceof iSql) && !is_string($query)) {
            throw new \Exception("La consulta de UNION debe ser una instancia de iSql o una cadena SQL.");
        }
        self::$unionClause = ['type' => strtoupper($type), 'query' => $query];
        return self::class;
    }

    // Ahora recibe $parametros por referencia
    public static function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        self::$parametros = [];
        self::$sql = "SELECT ";

        if (isset($propiedades['campos']) && is_array($propiedades['campos']) && !empty($propiedades['campos'])) {
            self::añadirPropiedades($propiedades, 'campos');
        } else {
            self::$sql .= " * ";
        }

        if (empty($propiedades['tabla'])) {
            throw new \Exception('Seleccione una tabla');
        }

        self::$sql .= " FROM ";
        self::añadirTabla($propiedades['tabla']);

        // Añadir JOINS
        if(isset($propiedades['join'])){
            foreach ($propiedades['join'] as $join) {
                self::$sql .= " " . $join['type'] . " JOIN `" . $join['table'] . "` ON " . $join['on'];
            }
        }
        
        
        

        // Cláusula WHERE
        if (isset($propiedades['where']) && is_array($propiedades['where'])) {
            self::añadirWhere($propiedades['where']);
        }

        // Añadir GROUP BY
        if (isset($propiedades['groupBy']) && is_array($propiedades['groupBy']) && count($propiedades['groupBy']) > 0 ) {
            self::$sql .= " GROUP BY " . implode(", ", array_map(fn($col) => "$col", $propiedades['groupBy']));
        }

        // Añadir HAVING
        if (isset($propiedades['having']) && is_array($propiedades['having']) && count($propiedades['having']) >0 ) {
            self::$sql .= " HAVING ";
            self::añadirCondiciones($propiedades['having'], 'AND'); // Reutiliza la lógica de condiciones
        }

        // Cláusula ORDER BY
        if (isset($propiedades['orderBy']) && is_array($propiedades['orderBy']) ) {
            self::$sql .= " ORDER BY ";
            // Aquí se asume que 'campo' y 'direccion' son nombres de columnas y no necesitan parámetros.
            // Si 'campo' o 'direccion' pudieran ser datos de usuario, necesitarías validación estricta (whitelist).
            $campo = $propiedades['orderBy']['campo'];
            $direccion = isset($propiedades['orderBy']['direccion']) ? strtoupper($propiedades['orderBy']['direccion']) : 'ASC';
            self::$sql .= "`$campo` $direccion";
        }
        // Cláusula LIMIT
        if (isset($propiedades['limit'])) {
            $limit = (int)$propiedades['limit'];
            $offset = isset($propiedades['offset']) ? (int)$propiedades['offset'] : 0;
            // LIMIT y OFFSET son generalmente enteros y no necesitan ser parametrizados,
            // pero siempre es buena práctica castearlos a int.
            self::$sql .= " LIMIT $limit";
            if ($offset > 0) {
                self::$sql .= " OFFSET $offset";
            }
        }
        // Añadir UNION
        if (self::$unionClause !== null) {
            $unionQuerySql = self::obtenerSqlSubconsulta(self::$unionClause['query']);
            self::$sql .= " " . self::$unionClause['type'] . " ($unionQuerySql)";
        }
        // Pasa los parámetros generados por referencia al array de parámetros global
        $parametros = self::$parametros;
           //echo self::$sql;
        
        return trim(self::$sql);
    }
    
}