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
    <?php 


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
            <a class="nav-link" href="Statusübersicht.php">Statusübersicht</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Ausleihe.php">Ausleihe</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Rückgabe.php">Rückgabe</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link active" href="Inventarliste.php">Inventarliste</a>
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
          <li class="nav-item active ">
            <a class="nav-link active " href="Startseite.php">Startseite</a>
          </li>
          <li class="nav-item ">
            <a class="nav-link " href="Statusübersicht.php">Statusübersicht</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
    ';
}   
// erneute Verbindung Datenbank 
//Filter Funktionen
      $conn = mysqli_connect("localhost", "inventardb", "test", "inventardb");
      $selected_category = "alle";
      if (isset($_POST['filter'])) 
      {
      $selected_category = $_POST['kategorie'];
      }
      $sql = "SELECT * FROM inventar";
      if ($selected_category != "alle") 
      {
      $sql .= " WHERE kategorie ='$selected_category'";
      }
      $result = mysqli_query($conn, $sql);
      ?>
      <!----Hier Filter beliebig erweiterbar ---->
      <form method="post" class="custom-form">
      <select name="kategorie">
                        <option value="alle" <?php if ($selected_category == "alle") echo "selected"; ?>> &#x2191 Alle</option>
                        <option value="Beamer" <?php if ($selected_category == "Beamer") echo "selected"; ?>>&#x2022 Beamer</option>
                        <option value="Presenter" <?php if ($selected_category == "Presenter") echo "selected"; ?>>&#x2022 Presenter</option>
                        <option value="Laptop" <?php if ($selected_category == "Laptop") echo "selected"; ?>>&#x2022 Laptop</option>
              </select>

          <button type="submit" name="filter">
              Filtern
          </button>
      </form>
<!--Tabelle Statusübersicht--->
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
          echo "</tr>";

          while($row = mysqli_fetch_assoc($result)) 
          {
                    echo "<tr>";
                    echo "<td>" . $row["inventarid"]. "</td>";
                    echo "<td>" . $row["bezeichnung"]. "</td>";
                    echo "<td>" . $row ["seriennr"]. "</td>";
                    echo "<td>" . $row ["kategorie"]. "</td>";
                    echo "</tr>";
          }
            echo "</table>";
  }
  else 
  {
          echo "Keine Ergebnisse";
  }             

// Schließen der Datenbankverbindung
mysqli_close($conn);
?>
</div>

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
