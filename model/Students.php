<?php
include_once '../../interfaces/IDataAccess.php';
class Students  implements IDataAccess
{
    private $conn;
    private $table_name = 'students';

    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $errors;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function Create()
    {
        # code...
        $query = "INSERT INTO 
                        $this->table_name
                    SET
                        firstname = :firstname,
                        lastname  = :lastname,
                        email     = :email
                        ";
        $stmt = $this->conn->prepare($query);

        $data = json_decode(file_get_contents('php://input'));
        $this->firstname = $data->firstname;
        $this->lastname  = $data->lastname;
        $this->email     = $data->email;

        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname  = htmlspecialchars(strip_tags($this->lastname));
        $this->email     = htmlspecialchars(strip_tags($this->email));

        if($stmt->execute([':firstname'=>$this->firstname,':lastname'=>$this->lastname,':email'=>$this->email])){
            # code...
            $query = "SELECT 
                            LAST_INSERT_ID()
                        FROM 
                            $this->table_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $id = $stmt->fetch();
            $this->id = $id[0];
            return true;
        }
        printf('Error: %s.\n',$stmt->error);
        $this->errors = $stmt->error;
        return false;
    }

    public function Get()
    {
        # code...
        $query = "SELECT 
                        s.id,
                        s.firstname,
                        s.lastname,
                        s.email,
                        s.created_at
                    FROM
                        $this->table_name s
                    ORDER BY
                        s.created_at
                    DESC";
        $stmt = $this->conn->prepare($query);
        return $stmt;
    }

    public function GetById()
    {
        # code...
        $query = "SELECT 
                        s.id,
                        s.firstname,
                        s.lastname,
                        s.email,
                        s.created_at
                    FROM
                        $this->table_name s
                    WHERE
                        s.id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt;
    }

    public function Update()
    {
        # code...
        $query = "UPDATE 
                        $this->table_name
                    SET
                        firstname = :firstname,
                        lastname  = :lastname,
                        email     = :email
                    WHERE
                        id = :id";
        $stmt = $this->conn->prepare($query);

        $data = json_decode(file_get_contents('php://input'));
        $this->firstname = $data->firstname;
        $this->lastname  = $data->lastname;
        $this->email     = $data->email;
        $this->id        = $data->id;

        $this->firstname = htmlspecialchars_decode(strip_tags($this->firstname));
        $this->lastname  = htmlspecialchars_decode(strip_tags($this->lastname));
        $this->email     = htmlspecialchars_decode(strip_tags($this->email));
        $this->id        = htmlspecialchars_decode(strip_tags($this->id));

        $this->errors = array();

        if(!empty($this->id)){
            $stmt->bindParam(':id',$this->id);
        }else{$this->errors['ID'] = "Required";}
        
        if(!empty($this->firstname)){
           $stmt->bindParam(':firstname',$this->firstname);
        }else{$this->errors['Firstname'] = "Can't Be Null";}

        if(!empty($this->lastname)){
            $stmt->bindParam(':lastname',$this->lastname);
        }else{$this->errors['Lastname'] = "Can't Be Null";}

        if(!empty($this->email)){
            $stmt->bindParam(':email',$this->email);
        }else{$this->errors['Email'] = "Can't Be Null";}

        return $stmt;
    }

    public function Delete()
    {
        # code...
        $this->id = htmlspecialchars(strip_tags($this->id));

        $query = "SELECT 
                        s.id
                    FROM
                        $this->table_name s
                    WHERE
                        s.id = :id";
        $stmt = $this->conn->prepare($query);

        if($stmt->execute([':id'=>$this->id])){
            if($stmt->rowCount() == 0) {
                 $this->errors = "Student ID Does Not Exist";
                 http_response_code(400);
                 return false;
            }

            $query = "DELETE FROM
                            $this->table_name
                        WHERE
                            id = :id";
            $stmt =$this->conn->prepare($query);

            if($stmt->execute([':id'=>$this->id])) return true;
            printf('Error: %s.\n',$stmt->error);
            return false;
        }
        http_response_code(400);
        printf('Error: %s.\n',$stmt->error);
        $this->errors = $stmt->error;
        return false;
    }
}
