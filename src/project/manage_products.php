<?php

include('auth.php');
include('../common/navbar.php');
include('../common/header.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Verifica se l'utente è loggato e ha il ruolo di admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Percorso del file XML
$xmlFile = "database.xml";

// Controllo esistenza file XML
if (!file_exists($xmlFile)) {
    die("Errore: Il file XML non esiste!");
}

// Carica il file XML
$dom = new DOMDocument();
$dom->load($xmlFile);
$products = $dom->getElementsByTagName("product");

// Funzione per eliminare un prodotto
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $xpath = new DOMXPath($dom);
    $productNode = $xpath->query("//product[product_id='$delete_id']")->item(0);

    if ($productNode) {
        // Recupera e cancella l'immagine associata
        $image_path = $productNode->getElementsByTagName("image_url")->item(0)->nodeValue;
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // Rimuove il prodotto dal file XML
        $productNode->parentNode->removeChild($productNode);
        $dom->save($xmlFile);

        $_SESSION['success_message'] = "Prodotto eliminato con successo!";
    } else {
        $_SESSION['error_message'] = "Errore: Prodotto non trovato.";
    }

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Prodotti</title>
</head>
<body style="background:url('../assets/images/black_background_cafe.jpg') no-repeat center center; background-size: cover;">
<div class="container-fluid row" style="padding-top: 2vh;">
    <div class="container">    
        <h2>Gestione Prodotti</h2>

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

        <!-- Tabella dei prodotti -->
        <table class="table table-striped table-dark">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Prezzo</th>
                    <th>Descrizione</th>
                    <th>Categoria</th>
                    <th>Immagine</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product->getElementsByTagName("product_id")->item(0)->nodeValue); ?></td>
                        <td><?php echo htmlspecialchars($product->getElementsByTagName("name")->item(0)->nodeValue); ?></td>
                        <td>€ <?php echo number_format($product->getElementsByTagName("price")->item(0)->nodeValue, 2); ?></td>
                        <td><?php echo htmlspecialchars($product->getElementsByTagName("description")->item(0)->nodeValue); ?></td>
                        <td><?php echo htmlspecialchars($product->getElementsByTagName("category")->item(0)->nodeValue); ?></td>
                        <td>
                            <img src="<?php echo $product->getElementsByTagName("image_url")->item(0)->nodeValue; ?>" alt="Immagine prodotto" width="100">
                        </td>
                        <td>
                            <a href="manage_products.php?delete_id=<?php echo $product->getElementsByTagName("product_id")->item(0)->nodeValue; ?>" class="btn btn-danger btn-sm">Elimina</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-add" data-bs-toggle="modal" style="position:absolute;top:12vh;right:2vw" data-bs-target="#exampleModalCenter">
            Aggiungi Prodotto
        </button>

    </div>
</div>
</body>
</html>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Aggiungi Prodotto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Nome Prodotto</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_price" class="form-label">Prezzo</label>
                        <input type="number" step="0.01" class="form-control" id="product_price" name="product_price" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_description" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="product_description" name="product_description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="product_category" class="form-label">Categoria</label>
                        <select class="form-select" id="product_category" name="product_category" required>
                            <option value="primi">Primi</option>
                            <option value="antipasti">Antipasti</option>
                            <option value="secondi">Secondi</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product_image" class="form-label">Immagine</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" required>
                    </div>
                    <div class="d-flex justify-content-center w-100">
                        <button type="submit" class="btn btn-add">Aggiungi Prodotto</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
