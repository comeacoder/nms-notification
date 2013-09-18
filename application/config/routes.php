<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['tinmoi/add_device/android'] = "notify_tinmoi/add_device/android";
$route['tinmoi/add_device/iphone'] = "notify_tinmoi/add_device/iphone";
$route['tinmoi/list_devices/android'] = "notify_tinmoi/list_devices/android";
$route['tinmoi/list_devices/iphone'] = "notify_tinmoi/list_devices/iphone";
$route['tinmoi/remove_device/android'] = "notify_tinmoi/remove_device/android";
$route['tinmoi/remove_device/iphone'] = "notify_tinmoi/remove_device/iphone";
$route['tinmoi/push/iphone'] = "notify_tinmoi/push_iphone";
$route['tinmoi/push/android'] = "notify_tinmoi/push_android";
$route['tinmoi/test'] = "notify_tinmoi/test";
$route['tinmoi/test2'] = "notify_tinmoi/test2";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */