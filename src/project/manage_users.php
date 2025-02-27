<?php
session_start(); // Assicurati che la sessione sia avviata

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('XML_FILE', __DIR__ . '/../db/database.xml');

// Funzione per caricare il file XML
function loadXML() {
    global $xmlFile;
    if (!file_exists(filename: XML_FILE)) {
        die("File XML non trovato.");
    }
    $doc = new DOMDocument();
    $doc->load(XML_FILE);
    return $doc;
}

// Funzione per ottenere tutti gli utenti, escludendo l'utente loggato
function getUsers() {
    $doc = loadXML();
    $users = [];
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    foreach ($doc->getElementsByTagName('user') as $user) {
        $userId = $user->getElementsByTagName('user_id')->item(0)->nodeValue;

        // Escludi l'utente loggato dalla lista
        if ($userId == $currentUserId) {
            continue;
        }

        $users[] = [
            'id' => $userId,
            'username' => $user->getElementsByTagName('username')->item(0)->nodeValue,
            'role' => $user->getElementsByTagName('role')->item(0)->nodeValue,
            'banned' => $user->getElementsByTagName('banned')->item(0)->nodeValue
        ];
    }
    return $users;
}

// Funzione per bannare un utente
function banUser($userId) {
    global $xmlFile;
    $doc = loadXML();

    foreach ($doc->getElementsByTagName('user') as $user) {
        if ($user->getElementsByTagName('user_id')->item(0)->nodeValue == $userId) {
            $user->getElementsByTagName('banned')->item(0)->nodeValue = 'true';
            $doc->save($xmlFile);
            return true;
        }
    }
    return false;
}

// Funzione per eliminare un utente
function deleteUser($userId) {
    global $xmlFile;
    $doc = loadXML();
    $users = $doc->getElementsByTagName('users')->item(0);

    foreach ($doc->getElementsByTagName('user') as $user) {
        if ($user->getElementsByTagName('user_id')->item(0)->nodeValue == $userId) {
            $users->removeChild($user);
            $doc->save($xmlFile);
            return true;
        }
    }
    return false;
}

// Gestione richieste
if (isset($_GET['ban_id'])) {
    if (banUser($_GET['ban_id'])) {
        $_SESSION['success_message'] = "Utente bannato con successo!";
    } else {
        $_SESSION['error_message'] = "Errore nel ban dell'utente.";
    }
    header("Location: manage_users.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    if (deleteUser($_GET['delete_id'])) {
        $_SESSION['success_message'] = "Utente eliminato con successo!";
    } else {
        $_SESSION['error_message'] = "Errore nell'eliminazione dell'utente.";
    }
    header("Location: manage_users.php");
    exit();
}

// Recupera utenti per la visualizzazione (escludendo l'utente loggato)
$users = getUsers();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestione Utenti</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Utente</th>
                    <th>Ruolo</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="align-middle"><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <?php echo ($user['banned'] == "true") ? 'Bannato' : 'Attivo'; ?>
                        </td>
                        <td>
                            <?php if ($user['banned'] == "false"): ?>
                                <a href="manage_users.php?ban_id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Banna</a>
                            <?php else: ?>
                                <span class="text-muted">Bannato</span>
                            <?php endif; ?>
                            <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Elimina</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
