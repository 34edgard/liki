# Liki

## ¿Que es LIKI?

liki es un Framework de php, minimalista y ligero, creado para desarrollar aplicaciones web, crear api-rest, 
proyectos academicos, desarrollo de NVP (producto minimo viable).

## Enfoque de Liki

Liki esta enfocado en personas que recien empiezan en el desarrollo web, como estudiantes, desarrolladores que trabajan en movil,
gente que quiera desarrollar rapido o provar conceptos para su aplicacion web sin tener que usar framework mas complejos y pesados como
laravel o que esten trabajando en un entorno con recursos limitados como servidores compartidos.


## Lo que ofrece Liki 

liki ofrece un marco de trabajo basicas para el desarrollo rapido de pequeñas web, mvp o api-rest, muy similar a framework como 
laravel, pero en un tamaño mas reducido y sin dependecias externas


## Características principales:

- Sistema de plantillas basado en componentes
- pagina.php:36-40
- Manejo de errores integrado ErrorHandler.php:8-26
- Sistema de sesiones Sesion.php:61-103
- ORM simple para base de datos Usuario.php:6-9

## Requisitos:
PHP v7.4 (mínima) o PHP v8.2.0 o mayor (recomendada)

## Estructura del proyecto:
Explicar brevemente frontend/ y backend/

## Ejemplo de uso básico: 
Un "Hello World" o componente simple

 para hacer un "hello world" simple en el index.php se debe crear una ruta

>Ruta::get('/HelloWorld',
function(){
  echo 'Hello World';
});


después se debe abrir en el navegador la ruta 'http/localhost/HelloWorld'

La clase rutas posee metodos de agrupacion como gruop o prefix para agrupar rutas de formas diferentes



## group 

group nos permite agrupar rutas pero en un archivo separado dentro de la carpeta funciones/rutas pertiendonos
trabajar con un grupo de rutas como si fuera un modulo aparte ademos que podemlos desactivar un grupo de rutas 
pasando como segundo parametro una condicion o valor booleano que si es true desactiva las rutas, esto reduce el tamaño del index
ademas de reducir los conflictos en el control de versiones a la hora de trabajar con barios programadores 

>Ruta::group('nombre',condicion);


## prefix

este permite agrupar las rutas por prefijos 


> Ruta::prefix('/bdSQLWeb',function(){
  Ruta::get('/tablas',[BdSQLWeb::class,'bdSQLWeb']);          
                  
},[lista de midelware],
[lista De Funciones que se agregaran a las rutas ]);    

# como liki interactua con la base de datos

en la carpeta app se definen los controladores
que extienden de la clase tabla la cual posee operaciones crud
basicas a las cuales se les pasa un arry con la siguiente estructura 
el indice tabla se define en el constructor del controlador y no es nesesario definir a la hora de aser las consultas


>[ 
    "tabla"=>'nombre',
    "campos"=>['campo1','campo2'],
    "valores"=>['valor1','valor2'],
    "where"=>[
        ["campo"=>'nombre','operador'=>'=',"valor"=>$valor]
    ]
]

la ventaja de esta sintaxis es que si se identa bien facilita 
entender como funciona una consulta con solo verla ademas de enseñar 
que es importante identar bien el codigo

apesar de ser una sintaxis muy verbosa se puede usar una alternatiba pero solo para operaciones simples
y para operaciones mas complejas se usaria la ya mensionada 

sintaxis alternativa

> use Liki\SQL\LikiQueryBuilder;

$sqlArray = LikiQueryBuilder::parse("
campos: campo1 , campo2 ;
valores: valor1 , valor2 ;
"where": {
    campo: nombre , operador: = , valor: $valor 
} ";

esta sintaxis es mas limpia pero no cuenta con todas las caracter de la otra sintaxis
la eleccion de cual usar queda a conveniencia del programador 

ambas syntaxis solo se recomienda usarse para aprender como funcionan las clase Tabla o para
practicar la logica de sql, la syntaxis que se recomienda usar para ser mas productivos 
es la siguiente donde se usa el metodo estatico conf de la clase Tabla pasandole una clase 
para configurarla, despues se usan metodos de encadenamiento con nombres similares a los 
de las otras syntaxis 

## metodo para consultar
> Tabla::conf(user::class)->campos(['nombre'])->get();

## metodo para registrar
> Tabla::conf(user::class)->campos(['nombre'])->post([$nombre]);



## metodo para edita
>Tabla::conf(user::class)->campos(['nombre'])->valores([$nombre])->put(['id' => $id]);r


## metodo para eliminar
>Tabla::conf(user::class)->delete(['id'=>$id]);




# liki y su editor integrado


pheditor.php es un editor de codigo el cual es un fork 
del repocitorio 

> https://github.com/pheditor/pheditor.git

y fue desarrollado por: Laurent, fjavierc, Jidbo
en futuras versiones de liki este editor sera integrado de mejor manera con la arquitectura de liki
con el fin de permitir ase cambios rapidos y ajustes de codigo etc...


# futuras mejoras

- se mejorara la sintaxis de la linea de comandos quedando de la siguiente forma

[tipo:accion] -[opcionales] [nombre] [extras]

el tipo son abrebiasiones del tipo de comando

gn: generacion de codigo

bd: base de datos

ts: testeo

ai: inteligencia artificial

la accion es lo que debe hacer el comando

bd:export

bd:import

los opcionales pueden cambiar el comportamiento del comando 

bd:export -h 

da informacion del comando

bd:export -dir:name

cambia el directorio a donde se exporta el comando


el nombre es un dato que puede variar 

los extras son datos extra que pueden pedir algunos comandos



- se añadiran mas comandos a la terminal como:
api-grup: para agregar varios endpoints
plant-grup: para generar plantillas en grupo
entre otras funciones en grupo similares





