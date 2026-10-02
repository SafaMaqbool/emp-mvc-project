<?php
require_once 'controller/EmployeeController.php';

$controller = new EmployeeController();

$action= isset($_GET['action']) ? $_GET['action'] : 'list';

switch($action){
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store();
        break;
    case 'list':
        default:
        $controller->list();
        break;

}



?>