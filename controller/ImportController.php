<?php
require_once 'config/Database.php';
require_once 'models/EmployeeModel.php';
require_once 'models/logModel.php';

class ImportController {
    private $model;
    private $logModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new EmployeeModel($db);
        $this->logModel = new LogModel($db);
    }

    public function showImportForm() {
        include 'views/import_form.php';
    }

    public function handleImport() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file']['tmp_name'])
            || !is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            $error_message = "Please upload a valid CSV file.";
            include 'views/import_form.php';
            return;
        }

        $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $importedCount = 0;
        $skippedCount  = 0;
        $skipReasons   = [];
        $seenNinos     = [];   // same file ke andar duplicate pakarne ke liye
        $lineNo        = 1;    // header = line 1
        $nino_pattern  = '/^[A-CEGHJ-PR-TW-Z]{2}\d{6}[A-D]$/i';

        fgetcsv($handle); // header skip

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $lineNo++;

            // khaali line ignore
            if ($row === [null]) { continue; }

            // 1. Columns
            if (count($row) < 6) {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: missing columns (6 required)");
                continue;
            }

            $firstname = trim($row[0]);
            $surname   = trim($row[1]);
            $deptNo    = strtoupper(trim($row[2]));
            $office    = trim($row[3]);
            $salary    = trim($row[4]);
            $nino      = strtoupper(trim($row[5]));

            // 2. Empty fields
            if ($firstname === '' || $surname === '' || $deptNo === '' || $office === '' || $salary === '' || $nino === '') {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: one or more fields are empty");
                continue;
            }

            // 3. NiNo format
            if (!preg_match($nino_pattern, $nino)) {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: invalid NiNo format '{$nino}'");
                continue;
            }

            // 4. Same file mein duplicate
            if (in_array($nino, $seenNinos)) {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: NiNo '{$nino}' repeated in this file");
                continue;
            }
            $seenNinos[] = $nino;

            // 5. DB mein duplicate
            if ($this->model->ninoExists($nino)) {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: NiNo '{$nino}' already exists in database");
                continue;
            }

            // 6. Department exist karta hai?
            if (!$this->model->deptExists($deptNo)) {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: department '{$deptNo}' does not exist");
                continue;
            }

            // 7. Salary number honi chahiye
            if (!is_numeric($salary) || $salary < 0) {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: invalid salary '{$salary}'");
                continue;
            }

            // Sab checks pass, ab insert
            $empNo = $this->model->generateNextEmployeeNo();

            $dbData = [
                ':EmpNo'     => $empNo,
                ':Firstname' => $firstname,
                ':Surname'   => $surname,
                ':DeptNo'    => $deptNo,
                ':Office'    => $office,
                ':Salary'    => $salary,
                ':NiNo'      => $nino,
            ];

            try {
                if ($this->model->insertEmployee($dbData)) {
                    $importedCount++;
                    $this->logModel->addLog("Employee {$empNo} created via batch import", 'BATCH_IMPORT');
                }
            } catch (PDOException $e) {
                $this->skip($skippedCount, $skipReasons, "Line {$lineNo}: database error - " . $e->getMessage());
            }
        }
        fclose($handle);

        if ($importedCount > 0) {
            $success_message = "{$importedCount} records imported successfully, {$skippedCount} skipped.";
        } else {
            $error_message = "No records imported. {$skippedCount} rows skipped.";
        }

        include 'views/import_form.php';
    }

    // Skip count + reason + log, teeno ek jagah
    private function skip(&$skippedCount, &$skipReasons, $reason) {
        $skippedCount++;
        $skipReasons[] = $reason;
        $this->logModel->addLog("Import skipped - " . $reason, 'BATCH_IMPORT');
    }
}