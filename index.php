<?php
$p = "./";

include './CodigoPHP/includer.php';

Peticion::metodo_get('/index.php',$inicio,[],[$pagina_html]);
Peticion::metodo_get('/saludar',$saludar);
Peticion::metodo_get('/saludar?e',$saludar,['e']);
Peticion::metodo_get('/inicio',$info,[],[$pagina_html]);