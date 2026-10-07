<?php
class Database{
    private $host ="localhost";
    private $db_Name="COMP1841";
    private $username="root";
    private $password="";
    public $conn;

    public function getConnection(){
        $this->conn=null; //making the connection null coz pehlay se kuch ho tw wo empty ho jaye
        try{
            // mysql:host=localhost;dbname=COMP1841
         $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_Name, $this->username, $this->password);
         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
         echo "Connection failed: " . $e->getMessage();
        }
    return $this->conn;
    }
}
?>