<?php

?>
<?php

interface iSql {
    public function generar_sql(array $propiedades, array $datos);
}

abstract class SentenciasSql {
    protected $sql = '';

    protected function añadirCampo($campo) {
        $this->sql .= " `$campo` ";
    }

    protected function añadirValor($valor) {
        $this->sql .= $this->determinarTipo($valor);
    }

    protected function determinarTipo($valor): string {
        return is_numeric($valor) ? " $valor" : " '" . addslashes($valor) . "'";
    }

    protected function añadirPropiedades(array $propiedades, string $nombrePropiedad) {
        $longitud = count($propiedades[$nombrePropiedad]);
        foreach ($propiedades[$nombrePropiedad] as $indice => $contenido) {
            $this->añadirCampoORNumericORString($nombrePropiedad, $contenido);
            if ($indice < $longitud - 1) {
                $this->sql .= ', ';
            }
        }
    }

    protected function añadirCampoORNumericORString($tipo, $contenido) {
        if ($tipo === 'campos') {
            $this->añadirCampo($contenido);
        } elseif ($tipo === 'valores') {
            $this->añadirValor($contenido);
        }
    }

    protected function añadirTabla($tabla) {
        $this->sql .= " `$tabla` ";
    }

    public function getSql(): string {
        return $this->sql;
    }
}

class Consultar extends SentenciasSql implements iSql {
    public function generar_sql(array $propiedades = ['tabla' => '', 'campos' => ['*']], array $datos = []) {
        if (empty($propiedades['tabla'])) {
          throw new \Exception('Seleccione una tabla');
        }
        $this->sql = 'SELECT';
        $this->añadirPropiedades($propiedades, 'campos');
        $this->sql .= " FROM ";
        $this->añadirTabla($propiedades['tabla']);
        return $this->sql;
    }
}

class Registrar extends SentenciasSql implements iSql {
    public function generar_sql(array $propiedades, array $datos = []) {
        if (empty($propiedades['tabla'])) {
            throw new \Exception('Seleccione una tabla');
        } else if (count($propiedades['campos']) !== count($propiedades['valores'])) {
             throw new \Exception('Los valores no son suficientes para los campos');
        }

        $this->sql = "INSERT INTO ";
        $this->añadirTabla($propiedades['tabla']);
        $this->sql .= " (";
        $this->añadirPropiedades($propiedades, 'campos');
        $this->sql .= ") VALUES (";
        $this->añadirPropiedades($propiedades, 'valores');
        $this->sql .= ") ";
        return $this->sql;
    }
}

class Editar extends SentenciasSql implements iSql {
    public function generar_sql(array $propiedades, array $datos = []) {
        if (empty($propiedades['tabla'])) {
            throw new \Exception( 'Seleccione una tabla');
        }
        $this->sql = "UPDATE ";
        $this->añadirTabla($propiedades['tabla']);
        $this->sql .= " SET ";

    //    $this->añadirPropiedades($propiedades, 'campos');
        foreach ($propiedades['campos'] as $index => $campo) {
          $this->añadirCampo($propiedades['campos'][$index]);
            $this->sql .= " = ";
            $this->añadirValor($propiedades['valores'][$index]);
            if ($index < count($propiedades['campos']) - 1) {
                $this->sql .= ", ";
            }
        }

        $this->sql .= " WHERE ";
        return $this->sql;
    }
}

class Eliminar extends SentenciasSql implements iSql {
    public function generar_sql(array $propiedades, array $datos = []) {
        if (empty($propiedades['tabla'])) {
           throw new \Exception('Seleccione una tabla');
        }
        $this->sql = "DELETE FROM ";
        $this->añadirTabla($propiedades['tabla']);
        $this->sql .= " WHERE ";
        return $this->sql;
    }
}

