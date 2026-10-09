<?php
require_once 'config/Database.php';
require_once 'models/EmployeeModel.php';
require_once 'models/logModel.php';


class EmployeeController
{
    private $model;
    private $logModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new EmployeeModel($db);
        $this->logModel = new LogModel($db);
    }

    public function create()
    {
        $autoEmpNo = $this->model->generateNextEmployeeNo();
        include 'views/employee_form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // $data=[
            //     ':EmpNo' => $_POST['EmpNo'],
            //     ':NiNo'      => $_POST['NiNo'],
            //     ':Firstname' => $_POST['Firstname'],
            //     ':Surname'   => $_POST['Surname'],
            //     ':DeptNo'    => $_POST['DeptNo'],
            //     ':Office'    => $_POST['Office'],
            //     ':Salary'    => $_POST['Salary']
            // ];

            // if($this->model->insertEmployee($data)){
            //     header("Location: index.php?action=list");
            //     exit();
            // }

            $autoEmpNo = $_POST['EmpNo'] ?? '';
            $NiNo = strtoupper(trim($_POST['NiNo'] ?? ''));

            $formData = [
                'NiNo' => $NiNo,
                'FirstName' => $_POST['Firstname'] ?? '',
                'Surname' => $_POST['Surname'] ?? '',
                'DeptNo' => $_POST['DeptNo'] ?? '',
                'Office' => $_POST['Office'] ?? '',
                'Salary' => $_POST['Salary'] ?? '',
            ];

            // Regex: start (^), 2 allowed letters, 6 digits, 1 letter A-D, end ($), case-insensitive (/i)
            $nino_pattern = '/^[A-CEGHJ-PR-TW-Z]{2}\d{6}[A-D]$/i';

            if (!preg_match($nino_pattern, $NiNo)) {
                $error_message = "Invalid UK National Insurance Number format (e.g. QQ123456C).";
                include 'views/employee_form.php';
                return;
            }


            //ab we will do try catch to check duplicate h k nhi
            $data = [
                ':EmpNo' => $autoEmpNo,
                ':NiNo' => $NiNo,
                ':Firstname' => $_POST['Firstname'] ?? '',
                ':Surname' => $_POST['Surname'] ?? '',
                ':DeptNo' => $_POST['DeptNo'] ?? '',
                ':Office' => $_POST['Office'] ?? '',
                ':Salary' => $_POST['Salary'] ?? '',
            ];

            try {
                if ($this->model->insertEmployee($data)) {

                    //ab we need to also add the audit message jese hi employee create ho 
                    $logMessage = "Employee " . $_POST['EmpNo'] . " created";
                    $this->logModel->addLog($logMessage, 'CREATE');
                    header("Location: index.php?action=list");
                    exit();
                } else {
                    echo "Error inserting record";
                }
            } catch (PDOException $e) {
                //23000 error code is dor inetgraty constrayunt violation

                //$e->errorInfo = [ "23000", 1062, "Duplicate entry 'AB123456C' for key 'NiNo'"];
                if ($e->getCode() == 23000 || (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062)) {
                    $error_message = "Duplicate Record : The NiNo '{$NiNo}' is already registered.";
                    include 'views/employee_form.php';
                    return; // Error ke sath form reload kar dega
                }
                throw $e;
            }
        }
    }

    public function List()
    {
        $employees = $this->model->getAllEmployeeWithDepartment();
        include 'views/employee_list.php';
    }
}
