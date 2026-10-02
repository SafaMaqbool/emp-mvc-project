<?php
require_once 'config/Database.php';
require_once 'models/EmployeeModel.php';


class EmployeeController{
    private $model;

    public function __construct(){
        $database= new Database();
        $db=$database->getConnection();
        $this->model = new EmployeeModel($db);
    }

    public function create(){
        include 'views/employee_form.php';

    }

    public function store(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data=[
                ':EmpNo' => $_POST['EmpNo'],
                ':NiNo'      => $_POST['NiNo'],
                ':Firstname' => $_POST['Firstname'],
                ':Surname'   => $_POST['Surname'],
                ':DeptNo'    => $_POST['DeptNo'],
                ':Office'    => $_POST['Office'],
                ':Salary'    => $_POST['Salary']
            ];

            if($this->model->insertEmployee($data)){
                header("Location: index.php?action=list");
                exit();
            }
        }
    }

    public function List(){
        $employees=$this->model->getAllEmployeeWithDepartment();
        include 'views/employee_list.php';
    }
}




?>