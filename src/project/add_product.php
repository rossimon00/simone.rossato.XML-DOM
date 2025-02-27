<?php
include('auth.php');
include('../common/navbar.php');
include('../common/header.php');

define('XML_FILE', __DIR__ . '/../db/database.xml');

// Verifica se l'utente è loggato e ha il ruolo di admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Funzione per caricare l'immagine
function uploadImage($image)
{
    $target_dir = "../assets/uploads/"; // Cartella di destinazione
    $target_file = $target_dir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Debug: Mostra dettagli sull'immagine
    echo '<pre>';
    var_dump($image);
    echo '</pre>';

    // Verifica se è un'immagine valida
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        return "Errore: Il file non è un'immagine.";
    }

    // Verifica dimensione (max 5MB)
    if ($image["size"] > 5000000) {
        return "Errore: L'immagine è troppo grande.";
    }

    // Verifica esistenza file
    if (file_exists($target_file)) {
        return "Errore: Il file esiste già.";
    }

    // Formati ammessi
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Errore: Formato non supportato.";
    }

    // Carica il file
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        return "Errore: Problema durante il caricamento.";
    }
}

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $description = $_POST['product_description'];
    $category = $_POST['product_category'];

    // Debug: Mostra i dati ricevuti
    echo '<pre>';
    var_dump($name, $price, $description, $category);
    echo '</pre>';

    // Gestione dell'immagine
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $image_path = uploadImage($_FILES['product_image']);

        // Debug: Mostra il percorso dell'immagine
        echo '<pre>';
        var_dump($image_path);
        echo '</pre>';

        if (strpos($image_path, 'Errore') !== false) {
            $_SESSION['error_message'] = $image_path;
        } else {
            // Carica il file XML
            if (!file_exists(XML_FILE)) {
                die("Errore: Il file XML non esiste!");
            }

            $dom = new DOMDocument();
            $dom->load(XML_FILE);

            // Trova il nodo "products"
            $productsNode = $dom->getElementsByTagName("products")->item(0);
            if (!$productsNode) {
                die("Errore: Nodo <products> non trovato!");
            }

            // Crea il nuovo elemento <product>
            $newProduct = $dom->createElement("product");

            // Genera un nuovo ID
            $newId = $productsNode->getElementsByTagName("product")->length + 1;

            // Aggiungi i dati al nuovo prodotto
            $newProduct->appendChild($dom->createElement("id", $newId));
            $newProduct->appendChild($dom->createElement("name", htmlspecialchars($name)));
            $newProduct->appendChild($dom->createElement("price", htmlspecialchars($price)));
            $newProduct->appendChild($dom->createElement("description", htmlspecialchars($description)));
            $newProduct->appendChild($dom->createElement("category", htmlspecialchars($category)));
            $newProduct->appendChild($dom->createElement("image_url", $image_path));

            // Aggiungi il prodotto al nodo <products>
            $productsNode->appendChild($newProduct);

            // Salva le modifiche nel file XML
            $dom->save(XML_FILE);

            $_SESSION['success_message'] = "Prodotto aggiunto con successo!";
        }
    } else {
        $_SESSION['error_message'] = "Errore: Nessuna immagine selezionata.";
    }

    // Reindirizza alla gestione prodotti
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Prodotto</title>
</head>
<body>
    <!-- Barra di navigazione -->
    <?php include('../common/navbar.php'); ?>

    <div class="container mt-5">
        <h2>Aggiungi Nuovo Prodotto</h2>

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

        <form method="POST" enctype="multipart/form-data">
            <label>Nome prodotto:</label>
            <input type="text" name="product_name" required>

            <label>Prezzo:</label>
            <input type="number" step="0.01" name="product_price" required>

            <label>Descrizione:</label>
            <textarea name="product_description" required></textarea>

            <label>Categoria:</label>
            <select name="product_category">
                <option value="antipasti">Antipasti</option>
                <option value="primi">Primi</option>
                <option value="secondi">Secondi</option>
                <option value="dessert">Dessert</option>
            </select>

            <label>Immagine:</label>
            <input type="file" name="product_image" required>

            <button type="submit">Aggiungi Prodotto</button>
        </form>
    </div>

</body>
</html>
