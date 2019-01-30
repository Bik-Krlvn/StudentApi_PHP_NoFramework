<?php
include_once '../../interfaces/IDataAccess.php';

class Users  implements IDataAccess
{
    private $conn;
    private $table_name = 'users';

    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public $errors;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function Create(){
        $query = "INSERT 
                    INTO
                        $this->table_name
                    SET
                        firstname = :firstname,
                        lastname  = :lastname,
                        email     = :email,
                        password  = :password";
        $stmt = $this->conn->prepare($query);

        $data = json_decode(file_get_contents('php://input'));
        $this->firstname    = $data->firstname;
        $this->lastname     = $data->lastname;
        $this->email        = $data->email;
        $this->password     = $data->password;
        
        $this->firstname     = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname      = htmlspecialchars(strip_tags($this->lastname));
        $this->email         = htmlspecialchars(strip_tags($this->email));
        $this->password      = htmlspecialchars(strip_tags($this->password));
        $password_hash       = password_hash($this->password,PASSWORD_BCRYPT);

        if($stmt->execute([':firstname'=>$this->firstname,':lastname'=>$this->lastname,
        ':email'=>$this->email,':password'=>$password_hash])){
            $query = "SELECT LAST_INSERT_ID() FROM $this->table_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $id = $stmt->fetch();
            $this->id = $id[0];
            http_response_code(201);
            return true;
        }
        http_response_code(400);
        printf('Error: %s.\n',$stmt->error);
        $this->errors = $stmt->error;
        return false;
    }
    public function Get(){
        $query = "SELECT
                        u.id,
                        u.firstname,
                        u.lastname,
                        u.email,
                        u.created_at
                    FROM
                        $this->table_name u
                    ORDER BY
                        u.id
                    DESC";

        $stmt = $this->conn->prepare($query);
        return $stmt;
    }
    public function GetById(){
        $query = "SELECT
                        u.id,
                        u.firstname,
                        u.lastname,
                        u.email,
                        u.created_at
                    FROM
                        $this->table_name u
                    WHERE
                        u.id = :id";

        $stmt = $this->conn->prepare($query);
        return $stmt;
    }
   
    public function EmailExist()
    {   
        # code...
        $query = "SELECT 
                        u.id,
                        u.firstname,
                        u.lastname,
                        u.password
                    FROM
                        $this->table_name u
                    WHERE
                        u.email = :email";
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        if($stmt->execute([':email'=>$this->email])){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $this->id = $id;
                $this->firstname = $firstname;
                $this->lastname = $lastname;
                $this->password = $password;
            }
            http_response_code(200);
            return true;
        }
        http_response_code(400);
        printf('Error: %s.\n',$stmt->error);
        return false;
    }
    public function Update(){

        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname  = htmlspecialchars(strip_tags($this->lastname));
        $this->email     = htmlspecialchars(strip_tags($this->email));
        $this->id        = htmlspecialchars(strip_tags($this->id));
        $this->password = htmlspecialchars(strip_tags($this->password));

        $password_check = !empty($this->password) ? ',password = :password' : '';
        $this->errors = array();
        $query = "UPDATE 
                        $this->table_name
                    SET
                        firstname = :firstname,
                        lastname  = :lastname,
                        email     = :email
                        {$password_check}  
                    WHERE
                        id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id',$this->id);

        if (!empty($this->firstname)){
            $stmt->bindParam(':firstname',$this->firstname);
        }else{$this->errors['Firstname'] = "Can't Be Null";}

        if (!empty($this->lastname)){
            $stmt->bindParam(':lastname',$this->lastname);
        }else{$this->errors['Lastname'] = "Can't Be Null";}

        if (!empty($this->email)){
            $stmt->bindParam(':email',$this->email);
        }else{$this->errors['Email'] = "Can't Be Null";}
        
        if(!empty($this->password)){
            $password_hash = password_hash($this->password,PASSWORD_BCRYPT);
            $stmt->bindParam(':password',$password_hash);
        }
        return $stmt;
    }
    public function Delete(){

    }
}
