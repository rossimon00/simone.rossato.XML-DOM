<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$xmlFile ="database.xml";

function login($username, $password) {
    global $xmlFile;

    if (!file_exists($xmlFile)) {
        die("Errore: Il file XML non esiste!");
    }

    $dom = new DOMDocument();
    $dom->load($xmlFile);
    $users = $dom->getElementsByTagName("user");

    foreach ($users as $user) {
        $storedUsername = $user->getElementsByTagName("username")->item(0)->nodeValue;
        $storedPassword = $user->getElementsByTagName("password")->item(0)->nodeValue;
        $role = $user->getElementsByTagName("role")->item(0)->nodeValue;
        $id = $user->getElementsByTagName("user_id")->item(0)->nodeValue;

        // Verifica username e password hashata
        if ($storedUsername === $username && password_verify($password, $storedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php"); // Reindirizza alla dashboard
            return true;
        }
    }
    return false;
}

function checkRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}

// ---- USO DELLO SCRIPT ----
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        echo "Login riuscito! Benvenuto, $username.";
    } else {
        echo "Errore: Username o password errati.";
    }
}
?>
