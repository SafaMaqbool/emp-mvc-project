<?php

//in mvc architechture model ka basically kam hota hay db queries execute krna (data insert and fetch krna)
// basically this is the fjke jo db se bat connect kr rhy frontend ko
class EmployeeModel{
    private $conn;

    public function __construct($db){
        $this->conn=$db;

    }

    public function insertEmployee($data){
        $query= "INSERT INTO employee (EmpNo, NiNo, Firstname, Surname, DeptNo, Office, Salary)
        VALUES (:EmpNo, :NiNo, :Firstname, :Surname, :DeptNo, :Office, :Salary)";

        $stmt=$this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function getAllEmployeeWithDepartment(){
        $query="SELECT e.EmpNo, e.NiNo, e.Firstname, e.Surname, d.DeptName, e.Office, e.Salary 
                  FROM employee e 
                  INNER JOIN department d ON e.DeptNo = d.DeptNo";

        $stmt=$this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


?>