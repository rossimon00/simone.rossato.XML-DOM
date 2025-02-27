<?php

include('auth.php'); 
include('../common/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ottieni i dati dal modulo
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Controllo esistenza file XML
    if (!file_exists(XML_FILE)) {
        die("Errore: Il file XML non esiste!");
    }

    // Carica il file XML
    $dom = new DOMDocument();
    $dom->load(XML_FILE);
    
    $users = $dom->getElementsByTagName("user");
    $userFound = false;
    
    // Cerca l'utente nel database XML
    foreach ($users as $user) {
        $xmlUsername = $user->getElementsByTagName("username")->item(0)->nodeValue;
        $xmlPassword = $user->getElementsByTagName("password")->item(0)->nodeValue;
        $xmlRole = $user->getElementsByTagName("role")->item(0)->nodeValue;
        $xmlId = $user->getElementsByTagName("id")->item(0)->nodeValue;

        if ($username === $xmlUsername && $password == $xmlPassword) {
            // Login riuscito, salva i dati della sessione
            $_SESSION['user_id'] = $xmlId;
            $_SESSION['role'] = $xmlRole;
            $userFound = true;
            break;
        }
    }

    if ($userFound) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Nome utente o password errati.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<body style="overflow: hidden;">
<div class="container-fluid bg-login" ></div>
<div class="container-fluid login-container bg-white">
<div style="height: 100%; width: 35%; position: relative; overflow: hidden; border-radius: 10px 0px 0px 10px;">
    <img src="../assets/images/login_bg.jpg" alt="" style="
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px 0px 0px 10px;
    ">
</div>
        <div class="col-md-4 p-5 d-flex flex-column align-items-center justify-content-between" style="width:65%">
            <img src="../assets/images/logo.png" alt="" style="width: 200px;height: 20%;">               
                <p class="title">Accedi al tuo account</p>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="w-60">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Nome Utente</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Inserisci il tuo nome utente" required>
                </div>
                <div class="form-group mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Inserisci la tua password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn custom-button btn-lg">Accedi</button>
                </div>
            </form>

            <div class="text-center mt-4 d-flex label">
                <p class="">Non hai un account?</p><a href="register.php" class="link text-decoration-none">&nbsp;Registrati qui</a>
            </div>
        </div>
    </div>
</body>
</html>
