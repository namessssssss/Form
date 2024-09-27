
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Overzicht</title>
    <link rel="stylesheet" href="../css/basic.css">
    <link rel="stylesheet" href="../css/overzicht.css">
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
        <h1>Overzicht van gegevens</h1>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        try {
            $host = "localhost";
            $user = "root";
            $pass = "root";
            $db = "gegevensverzameling";

            $connection = new mysqli($host, $user, $pass, $db); // Maak verbinding met de database

            if ($connection->connect_error) { // Controleer op verbindingsfouten
                throw new Exception($connection->connect_error); // Gooi een uitzondering als de verbinding mislukt
            }

            $query = "SELECT id, email, telefoonnummer FROM users"; // SQL-query om gegevens uit de database te selecteren
            $statement = $connection->prepare($query); // Bereid de SQL-statement voor

            if (!$statement->execute()) { // Voer de voorbereide statement uit
                throw new Exception($statement->error); // Gooi een uitzondering als de uitvoering mislukt
            }

            
            if (isset($_POST['backup'])) {
                $backupDir = 'backups/';
                
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0777, true);  // Create the directory if it doesn't exist
                }

                // Name for the backup file (with timestamp)
                $backupFile = $backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';

                // Command to execute the backup, using the existing database connection variables
                $command = "C:\\MAMP\\bin\\mysql\\bin\\mysqldump.exe --user=$user --password=$pass --host=$host $db > $backupFile";

                // Execute the command
                system($command, $output);

                // Check if the file was created
                if (file_exists($backupFile)) {
                    echo "Backup successful. <a href='$backupFile'>Download backup</a>";
                } else {
                    echo "Backup failed.";
                }
            }


            $statement->bind_result($id, $email, $telefoonnummer); // Bind resultaatvariabelen

            echo "<table>
                    <tr>
                        <th>Email</th>
                        <th>Telefoonnummer</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>";

            while ($statement->fetch()) { // Haal gegevens op uit de uitgevoerde statement
                echo "<tr>
                    <td>" . ($email) . "</td>
                    <td>" . ($telefoonnummer) . "</td>
                    <td><a href='update.php?id=$id'>Update</a></td>
                    <td><a href='delete.php?id=$id'>Delete</a></td>
                    </tr>"; // Toon de opgehaalde gegevens in een tabel
            }
            echo "</table>";
        } catch (Exception $e) { // Vang eventuele uitzonderingen op
            echo "Er is iets misgegaan: " . $e->getMessage(); // Toon foutmelding
        } finally { // Voer deze code uit ongeacht of een uitzondering is opgevangen of niet
            if ($statement) {
                $statement->close(); // Sluit de voorbereide statement
            }
            if ($connection) {
                $connection->close(); // Sluit de databaseverbinding
            }
        }
        ?>

    <form action="" method="post">
        <button type="submit" name="backup" value="backup">Backup</button>
    </form>
        
    </div>
    <img src="../images/Logo.svg" alt="Image">

</main>

<footer>
</footer>

<script src="../css/javascript.js"></script>
</body>
</html>