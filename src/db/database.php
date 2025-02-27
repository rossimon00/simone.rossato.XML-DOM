<?php
class XMLDatabase {
    private $xmlFile;
    private $dom;

    public function __construct($file) {
        $this->xmlFile = $file;
        if (!file_exists($file)) {
            die("Errore: Il file XML non esiste!");
        }

        $this->dom = new DOMDocument();
        $this->dom->load($this->xmlFile);
    }

    // Mostra tutti gli utenti
    public function getUsers() {
        $users = $this->dom->getElementsByTagName("user");
        foreach ($users as $user) {
            echo "ID: " . $user->getElementsByTagName("id")->item(0)->nodeValue . "<br>";
            echo "Username: " . $user->getElementsByTagName("username")->item(0)->nodeValue . "<br>";
            echo "Ruolo: " . $user->getElementsByTagName("role")->item(0)->nodeValue . "<br>";
            echo "Bannato: " . $user->getElementsByTagName("banned")->item(0)->nodeValue . "<br><br>";
        }
    }

    // Mostra tutti gli utenti per ruolo
    public function getUsersByRole($role) {
        $users = $this->dom->getElementsByTagName("user");
        echo "<h3>Utenti con ruolo: $role</h3>";
        foreach ($users as $user) {
            if ($user->getElementsByTagName("role")->item(0)->nodeValue === $role) {
                echo "ID: " . $user->getElementsByTagName("id")->item(0)->nodeValue . "<br>";
                echo "Username: " . $user->getElementsByTagName("username")->item(0)->nodeValue . "<br>";
                echo "Bannato: " . $user->getElementsByTagName("banned")->item(0)->nodeValue . "<br><br>";
            }
        }
    }

    // Mostra tutti i prodotti
    public function getProducts() {
        $products = $this->dom->getElementsByTagName("product");
        foreach ($products as $product) {
            echo "ID: " . $product->getElementsByTagName("id")->item(0)->nodeValue . "<br>";
            echo "Nome: " . $product->getElementsByTagName("name")->item(0)->nodeValue . "<br>";
            echo "Categoria: " . $product->getElementsByTagName("category")->item(0)->nodeValue . "<br>";
            echo "Prezzo: " . $product->getElementsByTagName("price")->item(0)->nodeValue . " €<br><br>";
        }
    }

    // Mostra i prodotti per categoria
    public function getProductsByCategory($category) {
        $products = $this->dom->getElementsByTagName("product");
        echo "<h3>Prodotti nella categoria: $category</h3>";
        foreach ($products as $product) {
            if ($product->getElementsByTagName("category")->item(0)->nodeValue === $category) {
                echo "ID: " . $product->getElementsByTagName("id")->item(0)->nodeValue . "<br>";
                echo "Nome: " . $product->getElementsByTagName("name")->item(0)->nodeValue . "<br>";
                echo "Prezzo: " . $product->getElementsByTagName("price")->item(0)->nodeValue . " €<br><br>";
            }
        }
    }

    // Aggiunge un nuovo utente
    public function addUser($id, $username, $password, $role, $banned) {
        $usersNode = $this->dom->getElementsByTagName("users")->item(0);

        $userNode = $this->dom->createElement("user");

        $userNode->appendChild($this->createTextNode("id", $id));
        $userNode->appendChild($this->createTextNode("username", $username));
        $userNode->appendChild($this->createTextNode("password", password_hash($password, PASSWORD_DEFAULT)));
        $userNode->appendChild($this->createTextNode("role", $role));
        $userNode->appendChild($this->createTextNode("banned", $banned ? "true" : "false"));

        $usersNode->appendChild($userNode);
        $this->saveXML();
        echo "Utente aggiunto con successo!<br>";
    }

    // Aggiunge un nuovo prodotto
    public function addProduct($id, $name, $image, $category, $description, $price) {
        $productsNode = $this->dom->getElementsByTagName("products")->item(0);

        $productNode = $this->dom->createElement("product");

        $productNode->appendChild($this->createTextNode("id", $id));
        $productNode->appendChild($this->createTextNode("name", $name));
        $productNode->appendChild($this->createTextNode("image_url", $image));
        $productNode->appendChild($this->createTextNode("category", $category));
        $productNode->appendChild($this->createTextNode("description", $description));
        $productNode->appendChild($this->createTextNode("price", number_format($price, 2)));

        $productsNode->appendChild($productNode);
        $this->saveXML();
        echo "Prodotto aggiunto con successo!<br>";
    }

    // Funzione per creare un nodo con testo
    private function createTextNode($tag, $value) {
        $element = $this->dom->createElement($tag);
        $element->appendChild($this->dom->createTextNode($value));
        return $element;
    }

    // Salva il file XML
    private function saveXML() {
        $this->dom->formatOutput = true;
        $this->dom->save($this->xmlFile);
    }
}

// ---- USO DELLA CLASSE ----
$xmlDB = new XMLDatabase("database.xml");

// Mostra utenti per ruolo
echo "<h2>Utenti per ruolo</h2>";
$xmlDB->getUsersByRole("admin");
$xmlDB->getUsersByRole("manager");
$xmlDB->getUsersByRole("user");

// Mostra prodotti per categoria
echo "<h2>Prodotti per categoria</h2>";
$xmlDB->getProductsByCategory("antipasti");
$xmlDB->getProductsByCategory("primi");
$xmlDB->getProductsByCategory("secondi");
$xmlDB->getProductsByCategory("dessert");

// Aggiunta di un nuovo utente
$xmlDB->addUser(3, "giulia", "securePass!", "manager", false);

// Aggiunta di un nuovo prodotto
$xmlDB->addProduct(12, "Zuppa Inglese", "../assets/uploads/zuppa.jpg", "dessert", "Un dolce al cucchiaio con crema e liquore Alchermes.", 6.50);
?>
