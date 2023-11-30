<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventarverwaltung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="stylesheet.css">
 
</head>
<body>
    
    <?php 
    // Eine Sitzung wird gestartet oder eine bestehende fortgeführt 
session_start();

//Variablen für erstellen Log-Datei
$currentMonth = date("m");  
$currentYear = date("Y");   
$filename = "logs/". "login/" . "logfile" . "_" . "login" . "_" . $currentMonth . "-" . $currentYear . ".txt";

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
              <li class="nav-item active ">
                <a class="nav-link active" href="Startseite.php">Startseite</a>
              </li>
              <li class="nav-item ">
                <a class="nav-link " href="Statusübersicht.php">Statusübersicht</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="Ausleihe.php">Ausleihe</a>
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
      </nav>';

      // Anzeige wer eingeloggt ist und Logout-Formular
      $benutzername = $_SESSION['vorname'] . ' ' . $_SESSION['nachname'];
      echo '<div class = "rechts">';
      echo '<p class="angemeldet-als">Angemeldet als: '.$benutzername.'</p>';
      echo '<form action="abmelden.php" method="post">';
      echo '<button class="button" type="submit" name="logout" style="font-size: 12px;">Ausloggen</button>';
      echo '</form>';
      echo '</div>';
    //Log-Datei
      $mitarbeiterid = $_SESSION['mitarbeiterid'];  
      $ipadresse = $_SERVER["192.168.9.232/invdb"]; 

if (file_exists($filename) && substr(decoct(fileperms($filename)), -3, 3) != "777") 
{
chmod($filename, 0777);
$logdatei = fopen($filename, "a");  // eventuell Pfad ändern für Webserver
fputs($logdatei,
"Vorgang: Login" . 
", " .
date("d.m.Y H:i:s", time()) . 
", " . "Mitarbeiter ID:" .
" " . $mitarbeiterid . 
", " . "Name:" . 
" " . $benutzername .
", " . "IP-Adresse:" . 
" " . $ipadresse . "\n"
);
fclose($logdatei);
chmod($filename, 0100);  // Ende Log-Datei
}
        
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
        

        //Erfolgreiches Login: Weiterleitung zur Startseite als Eingeloggter User
        if (mysqli_num_rows($result) == 1) 
        {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['vorname'] = $row['vorname'];
            $_SESSION['nachname'] = $row['nachname'];
            $_SESSION['mitarbeiterid'] = $row['mitarbeiterid'];  
            header("Location: Startseite.php"); 
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
          <li class="nav-item active">
            <a class="nav-link active" href="Startseite.php">Startseite</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Statusübersicht.php">Statusübersicht</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
    ';
}

?> 
<center>
<?php 
// erneute Verbindung Datenbank 
$host = "localhost"; 
$username = "inventardb"; 
$password = "test"; 
$dbname = "inventardb"; 

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) 
{
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}


//Anzeige auf der Seite, Willkommensnachricht für eingeloggten User
      if (isset($_SESSION['vorname']) && isset($_SESSION['nachname'])) 
      { 
        $benutzername = $_SESSION['vorname'] . ' ' . $_SESSION['nachname'];
        echo '<h2>Willkommen, ' . $benutzername . '! </h2><br><br>';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-emoji-smile-fill" viewBox="0 0 16 16">
  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zM4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z"/>
</svg><br><br>';
        
      } 
      // Wenn kein User eingeloggt dann Anzeige der Login-Form 
      else 
      {
        
        // Login-Form
        echo '<h2>Login</h2>';
        echo '<br>';
        if (isset($error)) 
        {
            echo '<p>' . $error . '</p>';
        }
        echo '
        <form method="post" class="form-inline">
  <div class="form-group mb-2">
    <label for="username" class="sr-only">Benutzername:</label>
    <input type="text" class="form-control" name="username" placeholder="Benutzername">
  </div>
  <div class="form-group mx-sm-3 mb-2">
    <label for="password" class="sr-only">Passwort:</label>
    <input type="password" class="form-control" name="password" placeholder="Passwort">
  </div>
  <button type="submit" class="btn btn-danger mb-2">Login</button>      
      ';

    echo '</form>';
      }
      


      // Schließen der Datenbankverbindung
     mysqli_close($conn);
    ?>    
</center>

<!-- Einbinden der Bootstrap-Java-Script-Datei -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

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
</body>
</html>


      