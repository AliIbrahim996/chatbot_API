<?php


class Chat
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function chat($data)
    {
        $q = "INSERT INTO `chat` (`senderId`, `reciverId`, `message`) VALUES ( ?, ?,?)";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $data->sId);
        $stmt->bindParam(2, $data->rId);
        $stmt->bindParam(3, $data->msg);
        if ($stmt->execute()) {
            http_response_code(201);
            return json_encode(array("message" => "new message added!"));
        } else {
            http_response_code(400);
            return json_encode(array("message" => "something went wrong"));
        }
    }

    public function getMsg($rId)
    {
        $q = "select  id,senderId,message from chat where reciverId = ?";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $rId);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            include_once "User.php";
            $chatData = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $u = new User($this->conn);
                $userName = $u->getName($row['senderId']);
                $chatItem = array(
                    "id" => $row['id'],
                    "senderId" => $row['senderId'],
                    "senderName" => $userName,
                    "message" => $row['message']
                );
                array_push($chatData, $chatItem);
            }
            http_response_code(200);
            return json_encode(array("Chat" => $chatData));
        } else {
            http_response_code(404);
            return json_encode(array("Chat" => "no data found!"));
        }
    }

    public function getMsg2($u1, $u2)
    {
        $q = "select  id,senderId,reciverId,message from chat where (senderId = ? and  reciverId = ? ) 
                                                   or (senderId = ? and  reciverId = ? )";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $u1);
        $stmt->bindParam(2, $u2);
        $stmt->bindParam(3, $u2);
        $stmt->bindParam(4, $u1);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            include_once "User.php";
            $chatData = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $u = new User($this->conn);
                $userName = $u->getName($row['senderId']);
                $secondUserName = $u->getName($row['reciverId']);
                $chatItem = array(
                    "id" => $row['id'],
                    "senderId" => $row['senderId'],
                    "senderName" => $userName,
                    "reciverId" => $row['reciverId'],
                    "reciverName" => $secondUserName,
                    "message" => $row['message']
                );
                array_push($chatData, $chatItem);
            }
            http_response_code(200);
            return json_encode(array("Chat" => $chatData));
        } else {
            http_response_code(404);
            return json_encode(array("Chat" => "no data found!"));
        }
    }
}
