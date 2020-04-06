<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
require_once  'Example_Model.php';

use Relay\Relay;

/* Seccion para conectar a la base de datos Illuminate Database ofrece
    las funciones necesarias para un crud.
*/
use Illuminate\Database\Capsule\Manager as Capsule;

//Se crea una capsula de Illuminate
$capsule = new Capsule;

//Se añaden los parametros para la conexion
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost:3308',
    'database'  => 'database_example',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$router = new Aura\Router\RouterContainer();
$map = $router->getMap();

$map -> get('get.all','/',function ($request,$capsule){
  //Ejemplo de consulta utilizando Illuminate
    $users = Capsule::table('users')->get();
  echo  $users;
});
$map -> get('other.all','/all',function ($request){
    //Ejemplo  de consulta usando la clase extendida a Model de illuminateM
    $users = ExampleModel::all();
    echo $users;
   //Algo con post
});
$map -> put('put.all','/put',function($request){
    //Algo con put
});

$map -> delete('delete.all','/put',function($request){
    //Algo con delete
});

$relay = new Relay([
    new Middlewares\AuraRouter($router),
    new Middlewares\RequestHandler()
]);

$response = $relay->handle($request);

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $response->getBody();