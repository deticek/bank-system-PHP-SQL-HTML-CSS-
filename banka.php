<?php
session_start();

// Preverimo, če datoteka 'id.json' obstaja
if (file_exists('id.json')) {
    // Preberemo vsebino datoteke 'id.json'
    $jsonContent = file_get_contents('id.json');
    
    // Dekodiramo JSON vsebino v PHP asociativno tabelo
    $data = json_decode($jsonContent, true);

    // Preverimo, če je ključ 'id' nastavljen v dekodirani tabeli
    if (isset($data['id'])) {
        $userId = $data['id'];
        echo "User ID from JSON: $userId<br>";

        // Shranimo čas zadnje aktivnosti v seji
        $_SESSION['last_activity'] = time();

        // Povezava do baze podatkov
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "banka";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Preverimo povezavo
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Pridobimo podatke o uporabniku
        $stmt = $conn->prepare("SELECT ime, priimek FROM uporabnik WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($ime, $priimek);
        $stmt->fetch();
        if ($ime === null && $priimek === null) {
            echo "No user found with ID: $userId<br>";
        }
        $stmt->close();

        // Pridobimo podatke o kartici
        $stmt = $conn->prepare("SELECT stevilka, denar FROM kartica WHERE id_up = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($stevilka, $denar);
        $stmt->fetch();
        if ($stevilka === null && $denar === null) {
            echo "No card found for user ID: $userId<br>";
        }
        $stmt->close();

        $conn->close();
    } else {
        echo "ID ni najden v datoteki.";
    }
} else {
    echo "Datoteka 'id.json' ne obstaja.";
}

// Preverimo čas zadnje aktivnosti in izvedemo odjavo po 1 minuti neaktivnosti
$session_timeout = 60; // Čas v sekundah
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    header("Location: odjava.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        #add{
    border: 2px solid black;
    border-radius: 10px;
    padding: 10px;
    width: 200px;
    margin: 20px auto;
}

    #kolko{
    border: 2px solid black;
    border-radius: 10px;
    padding: 10px;
    width: 90%;
    margin: 20px 5%;
    }

    #gumb{
    border: 2px solid black;
    border-radius: 10px;
    padding: 10px;
    width: 90%;
    margin: 20px 5%;
    }
    

    </style>

</head>
<body>
    <center>
    <div class="card relative h-[260px] w-[400px] flex flex-col justify-end px-6 py-10 text-white rounded-3xl gap-8 bg-gradient-to-r from-purple-500 to-pink-500" style="width: 500px;">
        <p class="text-2xl font-medium"><?php echo isset($stevilka) ? $stevilka : 'N/A'; ?></p>
        <div class="flex justify-between gap-10">
          <p class="text-lg font-medium"><?php echo isset($ime) && isset($priimek) ? $ime . ' ' . $priimek : 'N/A'; ?></p>
          <div class="flex-1 flex flex-col justify-end">
            <p class="self-end">Money:</p>
            <p class="self-end"><?php echo isset($denar) ? $denar . '$' : '0$'; ?></p>
          </div>
          <div class="self-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 58 36" height="36" width="58">
              <circle fill-opacity="0.62" fill="#F9CCD1" r="18" cy="18" cx="18"></circle>
              <circle fill="#424242" r="18" cy="18" cx="40" opacity="0.36"></circle>
            </svg>
          </div>
        </div>
      </div>
</center>
      <form action="addmoney.php" method="POST" id="add">
        <input type="number" name="vsota" id="kolko">
        <input type="submit" name="addmoney" id="gumb" value="Add Money">
      </form>

      <form action="takemoney.php" method="POST" id="add">
      <input type="number" name="vsota" id="kolko">
        <input type="submit" name="takemoney" id="gumb" value="Take Money">
      </form>

      <form action="odjava.php" method="POST" id="add">
        <input type="submit" name="odjava" id="gumb" value="Odjava">
      </form>

</body>
</html>
