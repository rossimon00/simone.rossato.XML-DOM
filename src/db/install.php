<?php
// Attiviamo la visualizzazione degli errori per facilitare il debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Includo il file per la connessione al database
require_once 'connessione.php';

/**
 * Recupera la connessione al database.
 *
 * Questa funzione stabilisce la connessione al database utilizzando la configurazione
 * definita nel file 'connessione.php'.
 *
 * @return mysqli Oggetto di connessione al database.
 */
$conn = getDBConnection(); // Otteniamo la connessione al database

// Verifico se la connessione è riuscita
if (!$conn) {
    die("Errore di connessione al database: " . mysqli_connect_error());
}

// Creazione della tabella 'Users' se non esiste già
$sqlUsers = "CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'manager', 'admin') DEFAULT 'user',
    banned BOOLEAN DEFAULT FALSE
)";

// Verifica dell'esito della query
if ($conn->query($sqlUsers) === TRUE) {
    echo "Tabella Users creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Users: " . $conn->error . "<br>";
}

// Creazione della tabella 'Products' se non esiste già
$sqlProducts = "CREATE TABLE IF NOT EXISTS Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    category ENUM('primi', 'antipasti', 'secondi', 'dessert') NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL
)";

// Verifica dell'esito della query
if ($conn->query($sqlProducts) === TRUE) {
    echo "Tabella Products creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Products: " . $conn->error . "<br>";
}

// Dati dei piatti da inserire (Antipasti, Primi, Secondi, Dessert)
$antipasti = [
    ['Insalata di Polpo', '../assets/uploads/antipasti.png', 'Insalata di polpo con olive e capperi.', 9.50],
];

// Inserimento degli antipasti nel database
foreach ($antipasti as $antipasto) {
    // Preparo la query di inserimento per gli antipasti
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'antipasti', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $antipasto[0], $antipasto[1], $antipasto[2], $antipasto[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Inserimento dei piatti per la categoria "primi"
$primi = [
    ['Spaghetti Carbonara', '../assets/uploads/carbonara.jpg', 'Deliziosi spaghetti con crema di uovo, pancetta croccante e pepe nero.', 12.50],
    ['Risotto ai Funghi', '../assets/uploads/risotto.jpg', 'Risotto cremoso con funghi porcini.', 14.00],
    ['Penne Arrabbiata', '../assets/uploads/arrabbiata.jpg', 'Penne piccanti con salsa di pomodoro e peperoncino.', 10.00],
    ['Tagliatelle al Ragù', '../assets/uploads/tagliatelle.jpg', 'Tradizionale ragù alla bolognese con tagliatelle fresche.', 13.50],
    ['Lasagna', '../assets/uploads/lasagna.jpg', 'Lasagna ricca e cremosa, con strati di carne, besciamella e formaggio.', 15.00]
];

foreach ($primi as $primo) {
    // Preparo la query di inserimento per i piatti della categoria 'primi'
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'primi', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $primo[0], $primo[1], $primo[2], $primo[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Inserimento dei piatti per la categoria "secondi"
$secondi = [
    ['Salmone grigliato', '../assets/uploads/salmon.jpg', 'Salmone grigliato perfettamente con burro al limone.', 18.00],
    ['Bistecca alla Fiorentina', '../assets/uploads/fiorentina.jpg', 'Succulenta bistecca T-bone cotta alla perfezione, servita con contorni.', 25.00],
];

foreach ($secondi as $secondo) {
    // Preparo la query di inserimento per i piatti della categoria 'secondi'
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'secondi', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $secondo[0], $secondo[1], $secondo[2], $secondo[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Inserimento dei piatti per la categoria "dessert"
$desserts = [
    ['Tiramisu', '../assets/uploads/tiramisu.jpg', 'Classico dessert italiano con mascarpone, caffè e cacao.', 7.50],
    ['Panna Cotta', '../assets/uploads/panna_cotta.jpg', 'Panna cotta liscia e cremosa con coulis di frutti di bosco.', 6.50],
];

foreach ($desserts as $dessert) {
    // Preparo la query di inserimento per i dessert
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'dessert', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $dessert[0], $dessert[1], $dessert[2], $dessert[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Confermo che i prodotti sono stati inseriti con successo
echo "Prodotti inseriti con successo!<br>";

// Creazione dell'utente admin
$username = 'admin';  // Nome utente dell'admin
$password = 'admin_password';  // Password dell'admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Password criptata per sicurezza

// Preparo la query per inserire l'utente admin nel database
$sqlAdmin = $conn->prepare("INSERT INTO Users (username, password, role) VALUES (?, ?, ?)");
if ($sqlAdmin) {
    $role = 'admin';  // Ruolo dell'utente
    $sqlAdmin->bind_param("sss", $username, $hashed_password, $role);
    if ($sqlAdmin->execute()) {
        echo "Utente admin creato con successo!<br>";
    } else {
        echo "Errore nella creazione dell'utente admin: " . $sqlAdmin->error . "<br>";
    }
    $sqlAdmin->close();  // Chiudo la dichiarazione
} else {
    echo "Errore nella preparazione della query per l'utente admin: " . $conn->error . "<br>";
}

// Chiudiamo la connessione al database
$conn->close();
?>
