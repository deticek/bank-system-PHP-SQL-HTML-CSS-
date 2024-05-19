<?php
if (isset($_POST['register'])) {
    $ime = $_POST['ime'];
    $priimek = $_POST['priimek'];
    $email = $_POST['email'];
    $pin = $_POST['pin'];

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "banka";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Funkcija za generiranje 16-mestne številke
    function generate16DigitNumber() {
        $number = '';
        for ($i = 0; $i < 16; $i++) {
            $number .= rand(0, 9);
        }
        return $number;
    }

    // Funkcija za generiranje 3-mestne številke
    function generate3DigitNumber() {
        return rand(100, 999);
    }

    // Generiramo številke
    $sixteenDigitNumber = generate16DigitNumber();
    $threeDigitNumber = generate3DigitNumber();

    // Ustvarimo novega uporabnika
    $sql = "INSERT INTO uporabnik (ime, priimek, email, pin) VALUES ('$ime', '$priimek', '$email', '$pin')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";

        // Pridobimo ID novo ustvarjenega uporabnika
        $userId = $conn->insert_id;

        // Shranimo podatke v tabelo kartica
        $money = "INSERT INTO kartica (stevilka, trimestna, denar, id_up) VALUES ('$sixteenDigitNumber', '$threeDigitNumber', 0, '$userId')";
        if ($conn->query($money) === TRUE) {
            echo "Card record created successfully";
        } else {
            echo "Error: " . $money . "<br>" . $conn->error;
        }

        header("Location: index.html");
        exit(); // Prepreči nadaljnje izvajanje skripte po preusmeritvi
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>
