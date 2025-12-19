<?php


use App\DatosExtra\Correo;
use Liki\SQL\LikiQueryBuilder;
return new class {
public static function run($id_correo){


$sql = LikiQueryBuilder::parse("
campos: id_correo , email;
where: {
    campo: id_correo , operador: = , valor: $id_correo
}");

$email = (new Correo())->consultar($sql);
    return $email[0]['email'];
    
}
};