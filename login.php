<?php
session_start();

if (isset($_POST['email']) && isset($_POST['pin'])) {
    $email = $_POST['email'];
    $pin = $_POST['pin'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "banka";

    // Ustvarjanje povezave
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Preverjanje povezave
    if ($conn->connect_error) {
        die("Povezava ni uspela: " . $conn->connect_error);
    }

    // Uporaba pripravljenih izjav za preprečevanje SQL injekcij
    $stmt = $conn->prepare("SELECT * FROM uporabnik WHERE email = ? AND pin = ?");
    if ($stmt === false) {
        die("Priprava izjave ni uspela: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $email, $pin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['pin'] = $row['pin'];
        $_SESSION['id'] = $row['id'];

        // Shranjevanje uporabniškega ID-ja v id.json
        $idData = array("id" => $row['id']);
        file_put_contents('id.json', json_encode($idData));

        header("Location: banka.php");
        exit(); // Prepreči nadaljnje izvajanje skripte po preusmeritvi
    } else {
        echo "Napačen email ali pin.";
    }

    $stmt->close();
    $conn->close();
}
?>
