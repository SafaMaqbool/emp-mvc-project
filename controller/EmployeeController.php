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

    public function getPostData()
    {
        $EmpNo = $_POST['EmpNo'] ?? '';
        $NiNo  = strtoupper(trim($_POST['NiNo'] ?? ''));

        $formData = [
            'EmpNo' => $EmpNo,
            'NiNo' => $NiNo,
            'Firstname' => $_POST['Firstname'] ?? '',
            'Surname' => $_POST['Surname'] ?? '',
            'DeptNo' => $_POST['DeptNo'] ?? '',
            'Office' => $_POST['Office'] ?? '',
            'Salary' => $_POST['Salary'] ?? '',
        ];

        //for db me data pass we need to replace placeholder walay se 
        $dbData = [];
        foreach ($formData as $key => $value) {
            $dbData[":$key"] = $value;
        }
        return [
            'form' => $formData,
            'db' => $dbData,
            'nino' => $NiNo
        ];
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $extracted = $this->getPostData();
            $formData = $extracted['form'];
            $dbData = $extracted['db'];
            $NiNo = $extracted['nino'];
            $autoEmpNo = $formData['EmpNo'];

            // $autoEmpNo = $_POST['EmpNo'] ?? '';
            // $NiNo = strtoupper(trim($_POST['NiNo'] ?? ''));

            // $formData = [
            //     'NiNo' => $NiNo,
            //     'Firstname' => $_POST['Firstname'] ?? '',
            //     'Surname' => $_POST['Surname'] ?? '',
            //     'DeptNo' => $_POST['DeptNo'] ?? '',
            //     'Office' => $_POST['Office'] ?? '',
            //     'Salary' => $_POST['Salary'] ?? '',
            // ];

            // Regex: start (^), 2 allowed letters, 6 digits, 1 letter A-D, end ($), case-insensitive (/i)
            $nino_pattern = '/^[A-CEGHJ-PR-TW-Z]{2}\d{6}[A-D]$/i';

            if (!preg_match($nino_pattern, $NiNo)) {
                $error_message = "Invalid UK National Insurance Number format (e.g. QQ123456C).";
                include 'views/employee_form.php';
                return;
            }


            //ab we will do try catch to check duplicate h k nhi


            try {
                // Model ko direct placeholder wala array de diya
                if ($this->model->insertEmployee($dbData)) {
                    $this->logModel->addLog("Employee " . $autoEmpNo . " created", 'CREATE');
                    header("Location: index.php?action=list");
                    exit();
                }
            } catch (PDOException $e) {
                //23000 error code is dor inetgraty constrayunt violation

                //$e->errorInfo = [ "23000", 1062, "Duplicate entry 'AB123456C' for key 'NiNo'"];
                if ($e->getCode() == 23000 || (isset($e->errorInfo) && $e->errorInfo == 1062)) {
                    $error_message = "Duplicate Record : The NiNo '{$NiNo}' is already registered.";
                    include 'views/employee_form.php';
                    return;
                }
                throw $e;
            }
        }
    }

    public function exportCsv()
    {
        // Model se active employees leingt
        $employees = $this->model->getPaginatedEmployees();

        // Browser ko batao: ye page nahi, CSV file hai, download karo
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="employees_' . date('Y-m-d') . '.csv"');

        // Seedha browser ki taraf likhne ke liye output stream kholo
        $output = fopen('php://output', 'w');

        // Pehli line: column names
        fputcsv($output, ['EmpNo', 'NiNo', 'Firstname', 'Surname', 'DeptNo', 'Office', 'Salary', 'Created At']);

        //Har employee ki ek line
        foreach ($employees as $emp) {
            fputcsv($output, $emp);
        }

        fclose($output);

        // Audit log
        $this->logModel->addLog("CSV export of " . count($employees) . " employees", 'EXPORT');
        exit();
    }

    public function apiPublish()
    {
        // 1. Model se active employees lao
        $employees = $this->model->getPaginatedEmployees();

        // 2. Browser ko batao: ye HTML nahi, JSON data hai
        header('Content-Type: application/json; charset=utf-8');

        // 3. Response ka structure banao
        $response = [
            'status'      => 200,
            'total_count' => count($employees),
            'timestamp'   => date('Y-m-d H:i:s'),
            'data'        => $employees
        ];

        // 4. PHP array ko JSON mein badal ke bhejo
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }

    public function edit()
    {

        $empNo = $_GET['id'] ?? null;

        if ($empNo) {
            $formData = $this->model->getEmployeeById($empNo);
            include 'views/employee_form.php';
        } else {
            // Agar id na mile to shoulkd go on list hi 
            header("Location: index.php?action=list");
            exit();
        }
    }
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=list");
            exit();
        }

        $extracted = $this->getPostData();
        $formData  = $extracted['form'];   // form refill ke liye (EmpNo bhi isme hai)
        $dbData    = $extracted['db'];
        $NiNo      = $extracted['nino'];

        // Same validation as store
        $nino_pattern = '/^[A-CEGHJ-PR-TW-Z]{2}\d{6}[A-D]$/i';
        if (!preg_match($nino_pattern, $NiNo)) {
            $error_message = "Invalid UK National Insurance Number format (e.g. QQ123456C).";
            include 'views/employee_form.php';
            return;
        }

        try {
            if ($this->model->updateEmployee($dbData)) {
                $this->logModel->addLog("Employee " . $formData['EmpNo'] . " updated", 'UPDATE');
                header("Location: index.php?action=list");
                exit();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 || ($e->errorInfo[1] ?? null) == 1062) {
                $error_message = "Duplicate Record: The NiNo '{$NiNo}' is already registered.";
                include 'views/employee_form.php';
                return;
            }
            throw $e;
        }
    }

    public function delete()
    {
        $empNo = $_GET['id'] ?? null;
        if ($empNo && $this->model->softDeleteEmployee($empNo)) {
            $this->logModel->addLog("Employee " . $empNo . " soft deleted", "SOFT_DELETE");
        }
        header("Location: index.php?action=list");
        exit();
    }
    public function List()
    {
        $employees = $this->model->getAllEmployeeWithDepartment();
        include 'views/employee_list.php';
    }
}
