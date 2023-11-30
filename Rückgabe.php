<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventarverwaltung</title>
    <script>
    function confirmRueckgabe() {
      if (confirm("Möchten Sie die Rückgabe wirklich durchführen?")) {
        return true; // Rückgabe wird durchgeführt
      } else {
        return false; // Rückgabe wird abgebrochen
      }
    }
  </script>
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
          <li class="nav-item ">
            <a class="nav-link " href="Ausleihe.php">Ausleihe</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link active" href="Rückgabe.php">Rückgabe</a>
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
        $username = "inventardb"; 
        $password = "test"; 
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
                if (isset($_POST['filter'])) 
                {
                    $rehanr = $_POST['rehanr'];
                    $nachname = $_POST['nachname'];
                    $kurs = $_POST['kurs'];                                                         
                    $inventarid = $_POST['inventarid'];                                             
                    
                    $sql = "SELECT ausleihe.ausleihid, ausleihe.rehanr, ausleihe.mitarbeiterid, ausleihe.Ausleihe, inventar.inventarid, inventar.kategorie, rehabilitandinnen.kurs, rehabilitandinnen.nachname          
                            FROM ausleihe 
                            INNER JOIN inventar ON ausleihe.inventarid = inventar.inventarid
                            INNER JOIN rehabilitandinnen ON ausleihe.rehanr = rehabilitandinnen.rehanr
                            WHERE ausleihe.rehanr = '$rehanr' OR rehabilitandinnen.kurs = '$kurs' OR rehabilitandinnen.nachname = '$nachname' OR inventar.inventarid = '$inventarid' AND ausleihe.zurueckgegeben = 0";
                } 
                else   
                {
                    $sql = "SELECT ausleihe.ausleihid, ausleihe.rehanr,ausleihe.mitarbeiterid, ausleihe.Ausleihe, inventar.inventarid, inventar.kategorie, rehabilitandinnen.kurs, rehabilitandinnen.nachname
                            FROM ausleihe 
                            INNER JOIN inventar ON ausleihe.inventarid = inventar.inventarid
                            INNER JOIN rehabilitandinnen ON ausleihe.rehanr = rehabilitandinnen.rehanr
                            WHERE ausleihe.zurueckgegeben = 0";
                }


                $result = mysqli_query($conn, $sql);
              ?>
               <!--- Filter beliebig erweiterbar ----->
                <form method="post" class="custom-form">
                    <label for="rehanr">Rehanr:</label>
                    <input type="text" name="rehanr" id="rehanr" class = "custom-input">
                    <label for="kurs">Kurs:</label>                                                     <!--Neu für Filter-->
                    <input type="text" name="kurs" id="kurs" class = "custom-input"> <br><br>           <!--Neu für Filter-->
                    <label for="nachname">Nachname:</label>                                             <!--Neu für Filter-->
                    <input type="text" name="nachname" id="nachname" class = "custom-input">            <!--Neu für Filter-->
                    <label for="inventarid">Inventarid:</label>                                         <!--Neu für Filter-->
                    <input type="text" name="inventarid" id="inventarid" class = "custom-input">        <!--Neu für Filter-->
                    <button type="submit" name="filter">
                    <span class="button-icon"></span>
                    Filtern
                    </button><br><br>
                    <input type="submit" name="alle_anzeigen" value="Alle anzeigen" class = "custom-button">
                </form>
                
                <!---- Tabelle Rückgabe ----->
                <div class="table-responsive">
                <?php
                    if (mysqli_num_rows($result) > 0) 
                    {
	                        echo "<table class = 'table caption-top'>";
	                        echo "<tr>";
	                        echo "<th>Rehanr</th>";
                          echo "<th>Kurs</th>";     
                          echo "<th>Nachname</th>";                           
                          echo "<th>mitarbeiterid</th>";
	                        echo "<th>Ausgeliehen (Datum/Uhrzeit)</th>";
	                        echo "<th>Inventarid</th>";
                          echo "<th>Kategorie</th>";
	                        echo "<th>Rückgabe</th>";
                          echo "</tr>";

                            $row_number = 0;
                            while($row = mysqli_fetch_assoc($result)) 
                            {
                                    echo "<tr>";
                                    echo "<td>" . $row["rehanr"]. "</td>";
                                    echo "<td>" . $row["nachname"]. "</td>";
                                    echo "<td>" . $row["kurs"]. "</td>";        
                                    echo "<td>" . $row["mitarbeiterid"]. "</td>";
                                    echo "<td>" . $row ["Ausleihe"]. "</td>";
                                    echo "<td>" . $row ["inventarid"]. "</td>";
                                    echo "<td>" . $row ["kategorie"]. "</td>";
                                    echo "<td>";
                                    if ($row) 
                                    {
                                        echo "<form action='Rückgabe.php' method='post' onsubmit='return confirmRueckgabe()'>";
                                        echo "<input type='hidden' name='inventarid' value='" . $row['inventarid']. "'>";
                                        echo "<input type='hidden' name='rehanr' value='" . $row['rehanr']. "'>";      
                                        echo "<input type='hidden' name='nachname' value='" . $row['nachname']. "'>";     
                                        echo "<input type='hidden' name='kurs' value='" . $row['kurs']. "'>";       
                                        echo "<input type='hidden' name='kategorie' value='" . $row['kategorie']. "'>";           
                                        echo "<input type='submit' name='submit_rückgabe' value='Rückgabe' class= 'custom-button";
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
                    if (isset($_POST['submit_rückgabe']))
                    { 
                            $selected_id = $_POST['inventarid'];
                            $test = $_POST['rehanr'];
                            $kurs = $_POST['kurs'];
                            $category = $_POST['kategorie'];
                            $mitarbeiterid = $_SESSION['mitarbeiterid']; // Mitarbeiter-ID aus der Session abrufen
                            $sql3 = "UPDATE ausleihe SET zurueckgegeben = 1 WHERE inventarid = '$selected_id';";
                            $sql2 = "UPDATE `inventar` SET `status` = 'verfügbar' WHERE `inventar`.`inventarid` = '$selected_id';";
                            $sql4 = "DELETE FROM `ausleihe` WHERE inventarid = '$selected_id';";
                            $sql5 = "SELECT bezeichnung AS bezeichnung FROM inventar WHERE inventarid = $selected_id;";                // Test für Log-Datei
                            $row2 = $conn->query($sql5)->fetch_array();                                                                // Test für Log-Datei
                            $lastid = $row2['bezeichnung'];
                            $result = mysqli_query($conn, $sql2);
                            $result2 = mysqli_query($conn, $sql3);
                            $result3 = mysqli_query($conn, $sql4);
                            $result4 = mysqli_query($conn, $sql5);

                            if ($result) 
                            {  
                                    echo "Rückgabe erfolgreich";
                                    echo '<meta http-equiv="refresh" content="2">';
                                    $bezeichnung = $lastid;
                            }
                            else 
                            {
                                echo "Fehler bei der Rückgabe";
                            }


                            // Code für Log-Datei

                            if (file_exists($filename) && substr(decoct(fileperms($filename)), -3, 3) != "777") 
                            {
                            chmod($filename, 0777);
                            $logdatei = fopen($filename, "a");             // eventuell Pfad ändern für Webserver
                            fputs($logdatei,
                                  "Vorgang: zurückgegeben" . 
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
