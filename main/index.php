<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$email = $telefoonnummer = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $host = "localhost";
        $gebruiker = "root";
        $password = "root";
        $database = "gegevensverzameling";

        // Collect POST data
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $telefoonnummer = preg_replace('/[^0-9]/', '', $_POST['telefoonnummer']); // Strips non-numeric chars

        // Create database connection
        $connectie = new mysqli($host, $gebruiker, $password, $database);

        if ($connectie->connect_error) {
            throw new exception($connectie->error);
        }

        // Prepare and execute the SQL statement
        $query = "INSERT INTO users (email, telefoonnummer) VALUES (?, ?)";
        $statement = $connectie->prepare($query);
        $statement->bind_param("ss", $email, $telefoonnummer);

        if ($statement->execute()) {
            // Redirect to the same page with a success parameter
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } else {
            echo "Error: " . $statement->error;
        }
    } catch (Exception $e) {
        echo "Oepsie: " . $e->getMessage();
    } finally {
        if (isset($connectie)) {
            $connectie->close();
        }
        if (isset($statement)) {
            $statement->close();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="../css/basic.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/popup.css">
</head>
<body>

<header>
    <h2 id="title">MetaTech</h2>

    <nav class="nav-menu" id="nav-menu">
        <div class="nav-item"><a href="index.php">Home</a></div>
        <div class="nav-item"><a href="overzicht.php">Administratie</a></div>
    </nav>
</header>

<main>
    <div class="container">
        <h1>Persoonlijke gegevens</h1>
        <form action="" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Vul je e-mail in " value="<?php echo htmlspecialchars($email); ?>" required>
            
            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="tel" id="telefoonnummer" name="telefoonnummer" placeholder="Vul je telefoonummmer in" value="<?php echo htmlspecialchars($telefoonnummer); ?>" required>
            
            <input id="button" type="submit" value="Toevoegen">
        </form>
    </div>
    <div>
        <img src="../images/Logo.svg" alt="Image">
    </div>
</main>

<footer>
</footer>


<script src="../css/javascript.js"></script>
<script src="../css/popup.js"></script>
</body>
</html>
