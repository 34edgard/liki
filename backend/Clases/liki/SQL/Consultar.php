<?php

namespace Liki\SQL;
use Liki\SQL\SentenciasSql;

class Consultar extends SentenciasSql implements iSql {
    protected array $joins = [];
    protected array $groupBy = [];
    protected array $having = [];
    protected ?array $unionClause = null;

    public function addJoin(string $type, string $table, string $onCondition): self {
        // Los JOINS generalmente no necesitan parámetros en la condición ON si los campos son fijos.
        // Si la condición ON puede variar por datos del usuario, necesitarías marcadores de posición aquí también.
        $this->joins[] = [
            'type' => strtoupper($type),
            'table' => $table,
            'on' => $onCondition
        ];
        return $this;
    }

    public function setGroupBy(array $columns): self {
        $this->groupBy = $columns;
        return $this;
    }

    public function setHaving(array $conditions): self {
        $this->having = $conditions;
        return $this;
    }

    public function setUnion(string $type, mixed $query): self {
        if (!in_array(strtoupper($type), ['UNION', 'UNION ALL'])) {
            throw new Exception("Tipo de UNION no válido. Use 'UNION' o 'UNION ALL'.");
        }
        if (!($query instanceof iSql) && !is_string($query)) {
            throw new Exception("La consulta de UNION debe ser una instancia de iSql o una cadena SQL.");
        }
        $this->unionClause = ['type' => strtoupper($type), 'query' => $query];
        return $this;
    }

    // Ahora recibe $parametros por referencia
    public function generar_sql(array $propiedades, array &$parametros): string {
        // Reinicia los parámetros para esta consulta específica
        $this->parametros = [];
        $this->sql = "SELECT ";

        if (isset($propiedades['campos']) && is_array($propiedades['campos']) && !empty($propiedades['campos'])) {
            $this->añadirPropiedades($propiedades, 'campos');
        } else {
            $this->sql .= " * ";
        }

        if (empty($propiedades['tabla'])) {
            throw new Exception('Seleccione una tabla');
        }

        $this->sql .= " FROM ";
        $this->añadirTabla($propiedades['tabla']);

        // Añadir JOINS
        foreach ($this->joins as $join) {
            $this->sql .= " " . $join['type'] . " JOIN `" . $join['table'] . "` ON " . $join['on'];
        }

        // Cláusula WHERE
        if (isset($propiedades['where']) && is_array($propiedades['where'])) {
            $this->añadirWhere($propiedades['where']);
        }

        // Añadir GROUP BY
        if (!empty($this->groupBy)) {
            $this->sql .= " GROUP BY " . implode(", ", array_map(fn($col) => "`$col`", $this->groupBy));
        }

        // Añadir HAVING
        if (!empty($this->having)) {
            $this->sql .= " HAVING ";
            $this->añadirCondiciones($this->having, 'AND'); // Reutiliza la lógica de condiciones
        }

        // Cláusula ORDER BY
        if (isset($propiedades['orderBy']) && is_array($propiedades['orderBy'])) {
            $this->sql .= " ORDER BY ";
            // Aquí se asume que 'campo' y 'direccion' son nombres de columnas y no necesitan parámetros.
            // Si 'campo' o 'direccion' pudieran ser datos de usuario, necesitarías validación estricta (whitelist).
            $campo = $propiedades['orderBy']['campo'];
            $direccion = isset($propiedades['orderBy']['direccion']) ? strtoupper($propiedades['orderBy']['direccion']) : 'ASC';
            $this->sql .= "`$campo` $direccion";
        }

        // Cláusula LIMIT
        if (isset($propiedades['limit'])) {
            $limit = (int)$propiedades['limit'];
            $offset = isset($propiedades['offset']) ? (int)$propiedades['offset'] : 0;
            // LIMIT y OFFSET son generalmente enteros y no necesitan ser parametrizados,
            // pero siempre es buena práctica castearlos a int.
            $this->sql .= " LIMIT $limit";
            if ($offset > 0) {
                $this->sql .= " OFFSET $offset";
            }
        }

        // Añadir UNION
        if ($this->unionClause !== null) {
            $unionQuerySql = $this->obtenerSqlSubconsulta($this->unionClause['query']);
            $this->sql .= " " . $this->unionClause['type'] . " ($unionQuerySql)";
        }

        // Pasa los parámetros generados por referencia al array de parámetros global
        $parametros = $this->parametros;
           //echo $this->sql;
        return trim($this->sql);
    }
}

