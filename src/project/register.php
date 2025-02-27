<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../common/header.php');

session_start();
define('XML_FILE', 'database.xml');

// Funzione per controllare se un utente esiste già
function userExists($username) {
    if (!file_exists(XML_FILE)) {
        return false;
    }
    
    $doc = new DOMDocument();
    $doc->load(XML_FILE);
    
    foreach ($doc->getElementsByTagName('user') as $user) {
        if ($user->getElementsByTagName('username')->item(0)->nodeValue === $username) {
            return true;
        }
    }
    return false;
}

// Funzione per registrare un utente
function registerUser($username, $password, $role) {
    $doc = new DOMDocument("1.0", "UTF-8");
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    
    if (file_exists(XML_FILE)) {
        $doc->load(XML_FILE);
        $root = $doc->documentElement;

        // Trova il nodo <users>
        $usersNode = $doc->getElementsByTagName("users")->item(0);
        if (!$usersNode) {
            $usersNode = $doc->createElement("users");
            $root->appendChild($usersNode);
        }
    } else {
        $root = $doc->createElement("database");
        $doc->appendChild($root);

        $usersNode = $doc->createElement("users");
        $root->appendChild($usersNode);
    }

    // Crea un nuovo nodo <user>
    $user = $doc->createElement("user");

    // Creazione ID univoco basato sul tempo
    $userIdNode = $doc->createElement("user_id", time());
    $usernameNode = $doc->createElement("username", htmlspecialchars($username));
    $passwordNode = $doc->createElement("password", password_hash($password, PASSWORD_DEFAULT)); // Hash della password
    $roleNode = $doc->createElement("role", htmlspecialchars($role));
    $bannedNode = $doc->createElement("banned", "false");

    // Aggiunge i nodi all'utente
    $user->appendChild($userIdNode);
    $user->appendChild($usernameNode);
    $user->appendChild($passwordNode);
    $user->appendChild($roleNode);
    $user->appendChild($bannedNode);

    // Aggiunge l'utente sotto <users>
    $usersNode->appendChild($user);
    $doc->save(XML_FILE);
}

// Controllo invio form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    $role = trim($_POST['role']);

    if (empty($username) || empty($password) || empty($password_confirm)) {
        $error_message = "Tutti i campi sono obbligatori!";
    } elseif ($password !== $password_confirm) {
        $error_message = "Le password non coincidono!";
    } elseif (userExists($username)) {
        $error_message = "Nome utente già esistente!";
    } else {
        registerUser($username, $password, $role);
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh; background: url('../assets/images/register_bg.jpg') no-repeat center center;background-size:cover">
        <div class="register-container col-md-4 bg_container">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Lido Azzurro</h2>
                <p>Compila il modulo per registrarti</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Nome Utente</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Inserisci il tuo nome utente" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Inserisci la tua password" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password_confirm" class="form-label">Conferma Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Conferma la tua password" required>
                </div>

                <!-- Dropdown per il ruolo -->
                <div class="mb-4">
                    <label for="role" class="form-label">Ruolo</label>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Seleziona Ruolo
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#" data-role="user">Utente</a></li>
                            <li><a class="dropdown-item" href="#" data-role="admin">Amministratore</a></li>
                        </ul>
                    </div>
                    <!-- Campo nascosto per passare il valore del ruolo -->
                    <input type="hidden" id="role" name="role" value="user">
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-custom btn-lg">Registrati</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p>Hai già un account? <a href="login.php" class="link text-decoration-none">Accedi</a></p>
            </div>
        </div>
    </div>

    <script>
        // Gestione della dropdown per selezionare il ruolo
        document.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                var role = this.getAttribute('data-role'); // Ottieni il valore del ruolo
                document.getElementById('role').value = role; // Imposta il valore nel campo nascosto
                document.getElementById('dropdownMenuButton').textContent = this.textContent; // Cambia il testo del bottone
            });
        });
    </script>
</body>
</html>
