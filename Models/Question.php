<?php


class Question
{
    private $conn;

    /**
     * Question constructor.
     * @param $conn
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }


    public function createQuestion($question)
    {

        $q = "INSERT INTO `question` (`question`) VALUES (?)";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $question);
        if ($stmt->execute()) {
            http_response_code(201);
            return json_encode(array(
                "message" => "question added successfully"
            ));
        } else {
            http_response_code(401);
            return json_encode(array(
                "message" => "something went wrong!"
            ));
        }
    }

    public function ask($query)
    {
        if (strlen($query) > 20) {
            $q = "select answer  from question where question like '%$query%'";
            $stmt = $this->conn->prepare($q);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $answer = $stmt->fetch(PDO::FETCH_ASSOC)['answer'];
                http_response_code(200);
                return json_encode(
                    array(
                        "answer" => $answer
                    )
                );
            } else {
                $this->createQuestion($query);
            }
        } else {
            $this->createQuestion($query);
        }
    }

    public function answer($questionId, $answer)
    {
        $q = "update question set answer = ? where id = ? ";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(1, $answer);
        $stmt->bindParam(2, $questionId);
        if ($stmt->execute()) {
            http_response_code(201);
            return json_encode(
                array(
                    "message" => "question answered done!",
                    "flag" => 1
                )
            );
        } else {
            http_response_code(401);
            return json_encode(
                array(
                    "message" => "something went wrong!",
                    "flag" => -1
                )
            );
        }
    }

    public function getQuestions()
    {
        $q = "select id,question from question where answer is null";
        $stmt = $this->conn->prepare($q);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $qData = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $qItem = array("id" => $row['id'], "question" => $row['question']);
                array_push($qData, $qItem);
            }
            http_response_code(200);
            return json_encode(array(
                "Questions" => $qData,
                "flag" => 1
            ));
        } else {
            http_response_code(404);
            return json_encode(array(
                "Questions" => "no data found",
                "flag" => 0
            ));
        }
    }
}
