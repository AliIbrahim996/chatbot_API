<?php


class Reference
{
    private $conn;

    /**
     * Course constructor.
     * @param $conn
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function add($rName)
    {
        $q = "INSERT INTO `reference` (`ref_name`) VALUES (?)";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $rName);
        if ($stmt->execute()) {
            http_response_code(201);
            return json_encode(
                array(
                    "message" => "reference added successfully"
                )
            );
        } else {
            http_response_code(403);
            return json_encode(
                array(
                    "message" => "something went wrong!"
                )
            );
        }
    }

    public function get()
    {
        $q = "select * from reference";
        $stmt = $this->conn->prepare($q);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $refData = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $refItem = array("reference_name" => $row['ref_name'], "id" => $row['id']);
                array_push($refData, $refItem);
            }
            http_response_code(200);
            return json_encode(
                array("References" => $refData)
            );
        } else {
            http_response_code(404);
            return json_encode(
                array("References" => "no data found!")
            );
        }
    }
}
