<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['takemoney'])) {
    $amount = $_POST['vsota'];

    if (file_exists('id.json')) {
        $jsonContent = file_get_contents('id.json');
        $data = json_decode($jsonContent, true);

        if (isset($data['id'])) {
            $userId = $data['id'];

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "banka";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Preverimo trenutno stanje denarja
            $stmt = $conn->prepare("SELECT denar FROM kartica WHERE id_up = ?");
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($currentMoney);
            $stmt->fetch();
            $stmt->close();

            if ($currentMoney >= $amount) {
                // Posodobitev denarja
                $stmt = $conn->prepare("UPDATE kartica SET denar = denar - ? WHERE id_up = ?");
                if ($stmt === false) {
                    die("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("di", $amount, $userId);
                if ($stmt->execute()) {
                    echo "Money taken successfully.";
                    header('Location: banka.php');
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Insufficient funds.";
            }

            $conn->close();
        } else {
            echo "User ID not found in id.json.";
        }
    } else {
        echo "File id.json does not exist.";
    }
} else {
    echo "Invalid request.";
}
?>
