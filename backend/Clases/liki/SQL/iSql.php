<?php
namespace Liki\SQL;

interface iSql {
    // La firma de generar_sql ahora incluye una referencia a $parametros para que la clase lo modifique.
    public function generar_sql(array $propiedades, array &$parametros): string;
}
