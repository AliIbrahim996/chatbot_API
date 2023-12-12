<?php


class Course
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

    public function add($cName)
    {
        $q = "INSERT INTO `course` (`c_name`) VALUES (?)";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $cName);
        if ($stmt->execute()) {
            http_response_code(201);
            return json_encode(
                array(
                    "message" => "course added successfully"
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
        $q = "select * from course";
        $stmt = $this->conn->prepare($q);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $cData = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cItem = array("course_name" => $row['c_name'], "id" => $row['id']);
                array_push($cData, $cItem);
            }
            http_response_code(200);
            return json_encode(
                array("Courses" => $cData)
            );
        } else {
            http_response_code(404);
            return json_encode(
                array("Courses" => "no data found!")
            );
        }
    }
}
