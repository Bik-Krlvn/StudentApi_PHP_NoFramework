<?php

class Database  
{
    private $db_host = 'localhost';
    private $db_name = 'school_db';
    private $db_user = 'root';
    private $db_pass = '';
    private $conn;

    public function Connect()
    {
        # code...
        $this->conn = null;
        try {
            //code...
            $this->conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name;",$this->db_user,$this->db_pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
