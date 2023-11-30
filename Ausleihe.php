<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventarverwaltung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="stylesheet.css">
      <?php  
    // Eine Sitzung wird gestartet oder eine bestehende fortgeführt
    //Sicherstellen, dass Seite über Login Aufgerufen wird, fals nicht automatische Weiterleitung zur Startseite  
        session_start(); 

            if(!isset($_SESSION['mitarbeiterid'])) 
            {
            header("Location: Startseite.php"); 
            exit();
            } 
        ?>
  </head>
<body>
  <!-- Einbinden der Bootstrap-Java-Script-Datei -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

<?php 
$currentMonth = date("m");  // nötig für Log-Datei
$currentYear = date("Y");   // nötig für Log-Datei
$filename = "logs/". "vorgaenge/" . "logfile" . "_" . "vorgaenge" . "_" . $currentMonth . "-" . $currentYear . ".txt";

if (!is_file($filename)) 
{
  fopen($filename, "a");
}

 // Überprüfen ob ein User bereits angemeldet ist 
if (isset($_SESSION['vorname']) && isset($_SESSION['nachname'])) 
{
    // Standart-Menü für eingeloggte User
    echo '
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand logo" href="#"><img src="Logo.png" alt="Logo" class="img-fluid"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item ">
            <a class="nav-link " href="Startseite.php">Startseite</a>
          </li>
          <li class="nav-item ">
            <a class="nav-link " href="Statusübersicht.php">Statusübersicht</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link active" href="Ausleihe.php">Ausleihe</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Rückgabe.php">Rückgabe</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Inventarliste.php">Inventarliste</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
    ';
     // Anzeige wer eingeloggt ist und Logout-Formular
     $benutzername = $_SESSION['vorname'] . ' ' . $_SESSION['nachname'];
     echo '<div class = "rechts">';
     echo '<p class="angemeldet-als">Angemeldet als: '.$benutzername.'</p>';
     echo '<form action="abmelden.php" method="post">';
     echo '<button class="button" type="submit" name="logout" style="font-size: 12px;">Ausloggen</button>';
     echo '</form>';
     echo '</div>';
}
else 
{
    // Wenn ein Benutzername und Passwort eingegeben wurde, Verbindung zur Datenbank 
    if (isset($_POST['username']) && isset($_POST['password'])) 
    {
        $host = "localhost"; 
        $username = "root"; 
        $password = ""; 
        $dbname = "inventardb"; 

        $conn = mysqli_connect($host, $username, $password, $dbname);

        if (!$conn) 
        {
            die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
        }
        // Überprüfung ob Benutzername und Passwort in Datenbank vorhanden 
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM mitarbeiterinnen WHERE Benutzername='$username' AND Passwort='$password'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) 
        {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['vorname'] = $row['vorname'];
            $_SESSION['nachname'] = $row['nachname'];
            $_SESSION['mitarbeiterid'] = $row['mitarbeiterid'];  
            exit();
        } 
        // Erfolgloses Login
        else 
        {
            $error = "Benutzername oder Passwort falsch.";
        }
    }

      // Standart-Menü für nicht Eingeloggte User
    echo '
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand logo" href="#"><img src="Logo.png" alt="Logo" class="img-fluid"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item ">
            <a class="nav-link " href="Startseite.php">Startseite</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link active" href="Statusübersicht.php">Statusübersicht</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
    ';
}  
 // erneute Verbindung Datenbank und Filter
 
                if (isset($_SESSION['vorname']) && isset($_SESSION['nachname'])) 
                {
                    $benutzername = $_SESSION['vorname'] . ' ' . $_SESSION['nachname'];
                } 
                else 
                {
                    $benutzername = ' ';
                }
        
                $conn = mysqli_connect("localhost", "inventardb", "test", "inventardb");
                $selected_category = "alle";
                $sql = "SELECT * FROM inventar WHERE Status = 'verfügbar'";       
                if (isset($_POST['filter'])) 
                {
                      $inventarid = $_POST['inventarid'];                         
                      $selected_category = $_POST['kategorie'];

                      $inputFields = array('inventarid');                         
                      $isSet = false;                                             
                      foreach($inputFields as $field) {                           
                            if(empty($_POST[$field])) {                           
                                  $isSet = true;                                  
                            }                                                     
                      }                                                           

                      if ($selected_category != "alle" && $isSet)                 
                      {                                                           
                            $sql .= " AND kategorie = '$selected_category'";      
                      }                                                           
                      else {                                                      
                            $sql .= " AND inventarid = '$inventarid'";            
                      }                                                           

                }
                
                $result = mysqli_query($conn, $sql );
          ?>
           <!--- Filter beliebig erweiterbar ----->
 <form method="post" class="custom-form">
    <select name="kategorie">
        <option value="alle" <?php if ($selected_category == "alle") echo "selected"; ?>>&#x2191 Alle</option>
        <option value="Beamer" <?php if ($selected_category == "Beamer") echo "selected"; ?>>&#x2022 Beamer</option>
        <option value="Presenter" <?php if ($selected_category == "Presenter") echo "selected"; ?>>&#x2022 Presenter</option>
        <option value="Laptop" <?php if ($selected_category == "Laptop") echo "selected"; ?>>&#x2022 Laptop</option>
    </select>
    <label for="inventarid">Inventarid:</label>
    <input type="text" name="inventarid" id="inventarid" class="custom-input">
    <button type="submit" name="filter">
        <span class="button-icon"></span>
        Filtern
    </button>
</form>

<?php
if(isset($_POST['filter']) || isset($_POST['alle_anzeigen'])) {
    if(isset($_POST['kategorie'])) {
        $selected_category = $_POST['kategorie'];
    } else {
        $selected_category = "";
    }

    if(isset($_POST['inventarid'])) {
        $inventarid = $_POST['inventarid'];
    } else {
        $inventarid = "";
    }

    if($selected_category == "alle") {
        $selected_category = "'Beamer', 'Presenter', 'Laptop'";
    } else {
        $selected_category = "'$selected_category'";
    }

    $query = "SELECT * FROM inventar WHERE kategorie IN ($selected_category) AND inventarid LIKE '%$inventarid%'";
    $result = mysqli_query($conn, $query);
    // ...
}
?>

<!---- Tabelle Rückgabe ----->
      <div class="table-responsive">
      <?php 
      

    if (mysqli_num_rows($result) > 0) 
    {
          echo "<table class = 'table caption-top'>";
          echo "<tr>";
          echo "<th>Inventarid</th>";
          echo "<th>Bezeichnung</th>";
          echo "<th>Seriennr</th>";
          echo "<th>Kategorie</th>";
          echo "<th>Rehanr</th>";
          echo "<th>Ausleihe</th>";
          echo "</tr>";
          $row_number = 0;
          while ($row = mysqli_fetch_assoc($result)) 
          {
                  echo "<tr>";
                  echo "<td>" . $row["inventarid"] . "</td>";
                  echo "<td>" . $row["bezeichnung"] . "</td>";
                  echo "<td>" . $row["seriennr"] . "</td>";
                  echo "<td>" . $row["kategorie"] . "</td>";
                  echo "<td>";

                      if ($row) 
                      {
                          echo "<form action='Ausleihe.php' method='post'>";
                          echo "<input type='hidden' name='inventarid' value='" . $row['inventarid'] . "'>";
                          echo "<input type='hidden' name='bezeichnung' value='" . $row['bezeichnung']. "'>";       // nötig für Log-Datei
                          echo "<input type='hidden' name='status' value='" . $row['status']. "'>";                 // nötig für Log-Datei
                          echo "<input type='hidden' name='kategorie' value='" . $row['kategorie']. "'>";           // nötig für Log-Datei
                          echo "<input type='text' name='text' class='custom-input'>";
                          echo "</td>";
                          echo "<td>";
                          echo "<input type='submit' name='submit_ausleihe' value='Ausleihe' class='custom-button";
                              if ($row_number % 2 == 0) 
                                  {
                                      echo " dark";
                                  }
                          echo "'>";
                          echo "</form>";
                      }
                  echo "</td>";
                  echo "</tr>";
          $row_number++;
          }
  echo "</table>";
} 
else 
{
echo "Keine Ergebnisse";
}

if (isset($_POST['submit_ausleihe']) && !empty($_POST['text'])) 
{
$test = $_POST['text'];
$datetime = date("Y-m-d H:i:s");
$selected_id = $_POST['inventarid'];
$mitarbeiterid = $_SESSION['mitarbeiterid']; // Mitarbeiter-ID aus der Session abrufen
$status = $_POST['status'];                                                                                // nötig für Log-Datei
$bezeichnung = $_POST['bezeichnung'];                                                                      // nötig für Log-Datei
$category = $_POST['kategorie'];                                                                           // nötig für Log-Datei
$sql4 = "SELECT kurs AS kurs FROM rehabilitandinnen WHERE rehanr = $test;";                                // Test für Log-Datei
$row2 = $conn->query($sql4)->fetch_array();                                                                // Test für Log-Datei
$lastid = $row2['kurs'];
$sql3 = "INSERT INTO ausleihe (ausleihid, rehanr, ausleihe, inventarid, mitarbeiterid) VALUES (NULL, '$test', '$datetime', '$selected_id', '$mitarbeiterid');";
$sql2 = "UPDATE `inventar` SET `status` = 'verliehen' WHERE `inventar`.`inventarid` = '$selected_id';";
$result = mysqli_query($conn, $sql2);
$result2 = mysqli_query($conn, $sql3);
$result3 = mysqli_query($conn, $sql4);                                                                      // Test für Log-Datei

if ($result && $result2)                                                                        
{
  echo "Ausleihe erfolgreich";
  echo '<meta http-equiv="refresh" content="2">'; 
  $kurs = $lastid;                                                                                          // Test für Log-Datei
} 
else 
{
  echo "Fehler bei der Ausleihe";
}

// Code für Log-Datei
if (file_exists($filename) && substr(decoct(fileperms($filename)), -3, 3) != "777") 
{
  chmod($filename, 0777);
  $logdatei = fopen($filename, "a");             // eventuell Pfad ändern für Webserver
  fputs($logdatei,
      "Vorgang: verliehen" . 
      ", " .
      date("d.m.Y H:i:s", time()) . 
      ", " . "Reha Nummer:" .
      " " . $test . 
      ", " . "Kurs:" .
      " " . $kurs .
      ", " . "Mitarbeiter ID:" .
      " " . $mitarbeiterid .
      ", " . "Inventar ID:" .
      " " . $selected_id . 
      ", " . "Kategorie:" .
      " " . $category . 
      ", " . "Bezeichnung:" .
      " " . $bezeichnung . "\n"
      );
      fclose($logdatei);
      chmod($filename, 0100);
}


}
// Schließen der Datenbankverbindung
mysqli_close($conn);
?>
</div>




</body>

<!-- Einbinden des Footer-Elements -->
<footer>
  <div class="card mb-3">
    <img src="bfw.jpg" class="card-img-top bfw-logo" alt="logo.Berufsförderungswerk Nürnberg">
    <div class="card-body">
      <p class="card-title bold">Berufsförderungswerk Nürnberg gemeinnützige GmbH</p>
      <p class="card-text">Zentrum für berufliche Rehabilitation <br> Schleswiger Str. 101 <br> 90427 Nürnberg </p>
    </div>
  </div>  
</footer>

</html>
