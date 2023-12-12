<?php

class User
{
    private $conn;
    private $table = 'user';
    //User Prop

    /**
     * `id` int(11) NOT NULL,
     * `fullName` varchar(255) NOT NULL,
     * `password` varchar(255) NOT NULL,
     * `email` varchar(255) NOT NULL,
     * `role` int(11) NOT NULL
     */

    private $id;
    private $email;
    private $fullName;
    private $password;
    private $role;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    function registerUser($data)
    {
        // insert query
        $query = "
                INSERT INTO user (`fullName`, `email`, `password`, `role`) VALUES (?,?,?,?)
               ";
        $stmt = $this->conn->prepare($query);
        // bind the values
        $password = password_hash($data->password, PASSWORD_BCRYPT);
        $stmt->bindParam(1, $data->fullName);
        $stmt->bindParam(2, $data->email);
        $stmt->bindParam(3, $password);
        $stmt->bindParam(4, $data->userRole);
        // execute the query, also check if query was successful
        try {
            $stmt->execute();
            $q = "Select  id  FROM $this->table where email = ? ";
            $stmt2 = $this->conn->prepare($q);
            $stmt2->bindParam(1, $data->email);
            $stmt2->execute();
            $id = $stmt2->fetch(PDO::FETCH_ASSOC)['id'];
            //201 created
            http_response_code(201);
            return json_encode(array(
                "message" => "User registered successful",
                "flag" => 1,
                "id" => $id
            ));
        } catch (Exception $e) {
            http_response_code(400);
            return json_encode(array(
                "message" => "error: " . $e->getMessage()
            ));
        }
    }

    public function getName($id)
    {
        $q = "select  fullName  from $this->table where id = ?";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fullName'];
        } else {
            return "no user found!";
        }
    }

    public function userLogIn($email, $password)
    {
        if ($this->userExists($email)) {
            //check for password
            if (password_verify($password, $this->password)) {
                //
                http_response_code(200);
                return json_encode(array(
                    "message" => "successfully logged in",
                    "user_info" => array(
                        "userRole" => $this->role,
                        "email" => $email,
                        "fullName" => $this->fullName,
                        "id" => $this->id
                    ),
                    "flag" => 1
                ));
            } else {
                http_response_code(401);
                return json_encode(
                    array(
                        "message" => "Unauthorized! password error",
                        "flag" => -1
                    )
                );
            }
        } else {
            http_response_code(404);
            return json_encode(
                array(
                    "message" => "User not found! check your email",
                    "flag" => -2
                )
            );
        }
    }

    private function userExists($email): bool
    {

        // query to check if email exists
        $query = "SELECT id,password,fullName,role
            FROM " . $this->table . "
            WHERE email = ?
            LIMIT 0,1";

        // prepare the query
        $stmt = $this->conn->prepare($query);
        // bind value
        $stmt->bindParam(1, $email);
        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();
        if ($num > 0) {
            //set password
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->password = $row['password'];
            $this->fullName = $row['fullName'];
            $this->role = $row['role'];
            $this->id = $row['id'];
            // return true because email exists in the database
            return true;
        }
        // return false if email does not exist in the database
        return false;
    }

    public function deleteUser($user_id)
    {
        $q = "Delete from user where id = ? ";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $user_id);
        try {
            $stmt->execute();
            http_response_code(200);
            return json_encode(array(
                "message" => "user deleted successfully",
                "flag" => 1
            ));
        } catch (Exception $e) {
            http_response_code(401);
            return json_encode(array(
                "message" => "something went wrong! " . $e->getMessage(),
                "flag" => -1
            ));
        }
    }

    public function getAll($role, $sId = "")
    {

        if ($role == "-1") {
            $q = "select * from user where role = 1 ";
            $stmt = $this->conn->prepare($q);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $userArr = array();
                $userArr['data'] = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $userItem = array(
                        "id" => $row['id'],
                        "fullName" => $row['fullName'],
                        "email" => $row['email'],
                        "role" => $row['role']
                    );

                    array_push($userArr['data'], $userItem);
                }
                http_response_code(200);
                return json_encode(
                    array("users" => $userArr)
                );
            } else {
                http_response_code(404);
                return json_encode(
                    array("users" => "no data found")
                );
            }
        } elseif ($role == "1") {
            $q = "select distinct reciverId from chat where senderId = ? ";
            $stmt = $this->conn->prepare($q);
            $stmt->bindParam(1, $sId);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $userArr = array();
                $userArr['data'] = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $q = "select * from user where id = ? ";
                    $stmt2 = $this->conn->prepare($q);
                    $stmt2->bindParam(1, $row['reciverId']);
                    $stmt2->execute();
                    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    $userItem = array(
                        "id" => $row2['id'],
                        "fullName" => $row2['fullName'],
                        "email" => $row2['email'],
                        "role" => $row2['role']
                    );
                    array_push($userArr['data'], $userItem);
                }
                http_response_code(200);
                return json_encode(
                    array("users" => $userArr)
                );
            } else {
                $q = "select distinct senderId from chat where reciverId = ? ";
                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(1, $sId);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $userArr = array();
                    $userArr['data'] = array();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $q = "select * from user where id = ? ";
                        $stmt2 = $this->conn->prepare($q);
                        $stmt2->bindParam(1, $row['senderId']);
                        $stmt2->execute();
                        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                        $userItem = array(
                            "id" => $row2['id'],
                            "fullName" => $row2['fullName'],
                            "email" => $row2['email'],
                            "role" => $row2['role']
                        );
                        array_push($userArr['data'], $userItem);
                    }
                    http_response_code(200);
                    return json_encode(
                        array("users" => $userArr)
                    );
                } else {
                    http_response_code(404);
                    return json_encode(
                        array("users" => "no data found")
                    );
                }
            }
        }
    }
}
