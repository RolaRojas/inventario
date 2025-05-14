<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/


/* ------------quitar comentario por si falla-----------------
$route['default_controller'] = 'inventario';
$route['recuperar_clave'] = 'inventario/recuperar_clave';
$route['inicio'] = 'inventario/inicio';
$route['visitas'] = 'inventario/visitas';
$route['login'] = 'inventario/login';
$route['ingresar'] = 'inventario/ingresar';
$route['retirados'] = 'inventario/retirados';
$route['olvide_clave'] = 'olvide_clave';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE; 
------------------------------------------------------------------*/



// Ruta predeterminada
$route['default_controller'] = 'login';

// Rutas específicas
$route['login'] = 'login/index';
$route['login/validar_login'] = 'login/validar_login';
$route['login/salir'] = 'login/cerrar_sesion';
$route['inicio'] = 'inventario/inicio';
$route['ingresar'] = 'inventario/ingresar';
$route['retirados'] = 'inventario/retirados';
$route['crear_usuario'] = 'inventario/crear_usuario';
$route['ubicaciones'] = 'inventario/ubicaciones';
$route['verificar'] = 'verificar/index';
$route['login/cerrar_sesion'] = 'login/cerrar_sesion';
$route['inicio_visita'] = 'inventario/inicio_visita';
$route['crear_info'] = 'inventario/crear_info';



// Rutas para acciones AJAX
$route['inventario/agregar'] = 'inventario/agregar';
$route['inventario/modificar'] = 'inventario/modificar';
$route['inventario/obtener_articulo/(:num)'] = 'inventario/obtener_articulo/$1';
$route['inventario/detalles_retirado/(:num)'] = 'inventario/detalles_retirado/$1';
$route['inventario/exportar/(:any)'] = 'inventario/exportar/$1';
$route['inventario/exportar_retirados/(:any)'] = 'inventario/exportar_retirados/$1';
$route['inventario/importar'] = 'inventario/importar';
$route['inventario/importar_excel'] = 'inventario/importar_excel';
$route['descargar_plantilla'] = 'inventario/descargar_plantilla';
$route['inventario/procesar_importacion'] = 'inventario/procesar_importacion';


// Rutas para importación/exportación de Excel
$route['importar_excel'] = 'inventario/importar_excel';
$route['descargar_plantilla'] = 'inventario/descargar_plantilla';
$route['procesar_importacion'] = 'inventario/procesar_importacion';


// Ruta para cualquier otra solicitud
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

