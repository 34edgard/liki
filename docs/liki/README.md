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
 PHP v8.2.0 o mayor (recomendada)

## Estructura del proyecto:
Explicar brevemente frontend/ y backend/


## sistema de plantillas llamado Flow template

este sistema de plantillas posee metodos html, css, js, json y page que se pueden usar para construir
la interfas web donde o responder de formas diferentes los metodo resiven dos parametros el nombre del componente
y los datos de configuracion que segun el componente pueden ser requeridos o no, ademas de esto liki usa bootstrap, htmx, servis workers y web components para crear la interfas
todo bootstrap se usa para tener componetes ya estilosados y responsibos, htmx se usa para aser peticiones al servido, paginacion y navegacion, servis worker se usan para cache inteligente
y complementar a htmx dandole la posivilidad  de funcionar offline y los web components se usan para manejar la respuestas json pero todavia falta para que sean completamente funcionales por ahora solo 
se implemento una forma de crear web componets de forma facil usando una tempete que se define usando el metodo Flow::js('WebComponentes/newComponet',$datos) esto se define dentro de frontend/js/LoadComponets.php
ese archivo se puede usar para crear los web componets o se puede crear otros similares para usalos como un builders pero con php tambi se puede aser lo mismo con los archivos css y se les usaria de la siguente forma
/nombreDelArchivo/js
/nombreDelArchivo/css
/nombreDelArchivo/html
si el archivo esta dentro de una carpeta se usa "_" en ves de un "/"
/carpeta_archivo/js




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

# bases de datos en liki hay 



## metodo para consultar
> FlowDB::conf(user)->campos(['nombre'])->get();

## metodo para registrar
> FlowDB::conf(user)->campos(['nombre'])->post([$nombre]);



## metodo para edita
>FlowDB::conf(user)->campos(['nombre'])->valores([$nombre])->put(['id' => $id]);r


## metodo para eliminar
>FlowDB::conf(user)->delete(['id'=>$id]);




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





