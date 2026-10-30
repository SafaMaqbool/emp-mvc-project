<?php
require_once 'controller/EmployeeController.php';
require_once 'controller/ImportController.php';

$controller = new EmployeeController();

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'import':
        $importController = new ImportController();
        $importController->showImportForm();
        break;
    case 'import_process':
        $importController = new ImportController();
        $importController->handleImport();
        break;
       case 'export_csv':
        $controller->exportCsv();
        break;
    case 'api_publish':
        $controller->apiPublish();
        break;
    case 'list':
    default:
        $controller->list();
        break;
}