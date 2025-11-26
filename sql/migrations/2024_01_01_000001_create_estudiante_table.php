<?php

use Liki\Database\SchemaBuilder;  
use Liki\Database\ConexionesBD;  
  
return new class {  
public function up() {  
    $schema = new SchemaBuilder();  
      
    $sql = $schema->createTable('usuariog2', function($table) {  
        $table->integer('id_usuario',true)->primary('id_usuario');  
        $table->integer('cedula')->notNull();  
        $table->string('nombres', 30)->notNull();  
        $table->string('apellidos',30)->notNull();  
        $table->integer('id_rol')->notNull();  
        $table->string('usuario',30)->notNull();  
        $table->integer('id_correo')->notNull();  
        $table->string('contrasena',166)->notNull();  
        $table->enum('estado', ['activo', 'inactivo'])->notNull();  
        
   
    });
        
 }
};