<?php

//in mvc architechture model ka basically kam hota hay db queries execute krna (data insert and fetch krna)
// basically this is the fjke jo db se bat connect kr rhy frontend ko
class EmployeeModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function generateNextEmployeeNo()
    {
        $query = "Select EmpNo FROM employee
                  ORDER BY CAST(SUBSTRING(EmpNo, 2) As UNSIGNED) DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['EmpNo'])) {
            $lastNum = (int)substr($row['EmpNo'], 1);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }
        return 'E' . $nextNum;
    }

    public function insertEmployee($data)
    {
        $query = "INSERT INTO employee (EmpNo, NiNo, Firstname, Surname, DeptNo, Office, Salary, is_active)
        VALUES (:EmpNo, :NiNo, :Firstname, :Surname, :DeptNo, :Office, :Salary, 1)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function getAllEmployeeWithDepartment()
    {
        $query = "SELECT e.EmpNo, e.NiNo, e.Firstname, e.Surname, d.DeptName, e.Office, e.Salary 
                  FROM employee e 
                  INNER JOIN department d ON e.DeptNo = d.DeptNo
                  WHERE e.is_active =1 ";



        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeById($empNo)
    {
        $query = "SELECT * FROM employee WHERE EmpNo = :empNo";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(['empNo' => $empNo]);

        // Single row fetch karne ke liye fetch(PDO::FETCH_ASSOC) use hota hai
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function softDeleteEmployee($empNo)
    {
        $stmt = $this->conn->prepare("UPDATE employee SET is_active = 0 WHERE EmpNo = :EmpNo");
        return $stmt->execute([':EmpNo' => $empNo]);
    }


    public function updateEmployee($data)
    {
        $query = "UPDATE employee
              SET NiNo = :NiNo,
                  Firstname = :Firstname,
                  Surname = :Surname,
                  DeptNo = :DeptNo,
                  Office = :Office,
                  Salary = :Salary
              WHERE EmpNo = :EmpNo";

        $stmt = $this->conn->prepare($query);


        return $stmt->execute($data);
    }
    public function ninoExists($nino)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM employee WHERE NiNo = :NiNo");
        $stmt->execute([':NiNo' => $nino]);
        return $stmt->fetchColumn() > 0;
    }

    public function deptExists($deptNo)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM department WHERE DeptNo = :DeptNo");
        $stmt->execute([':DeptNo' => $deptNo]);
        return $stmt->fetchColumn() > 0;
    }
}
