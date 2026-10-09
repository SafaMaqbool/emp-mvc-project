<?php
class LogModel{
    private $db;

    public function __construct($db)
    {
       $this->db=$db; 
    }

    public function addLog($logText, $category){
        $query="INSERT INTO system_log (log_text, category)
        VALUES (:log_text, :category)";
        $stmt=$this->db->prepare($query);

        return $stmt->execute(
            [
                ':log_text'=>$logText,
                ':category'=>$category
            ]
        );
    }
}

?>