<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('auth.php');
include('../common/navbar.php'); // Includi la navbar qui
include('../common/header.php');

// Verifica se l'utente è loggato
// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    // Stampa un messaggio per vedere se entra in questo blocco
    echo "Utente non loggato, reindirizzamento...";
    exit();
}


// Verifica il ruolo dell'utente (admin o utente normale)
$is_admin = ($_SESSION['role'] === 'admin');
?>

<script>
  // Funzione per nascondere l'overlay
  function hideScrollOverlay() {
    const overlay = document.getElementById('scroll-overlay');
    overlay.classList.add('hidden'); // Applica la classe per nascondere
  }

  // Rimuove l'overlay al primo scroll
  window.addEventListener('scroll', hideScrollOverlay);

  // Rimuove l'overlay automaticamente dopo 5 secondi
  setTimeout(hideScrollOverlay, 5000);

 

</script>
   
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>
<div id="scroll-overlay" class="scroll-overlay">
    <div class="scroll-message">
        <p>Scrolla per scoprire il nostro menu!</p>
        <div class="scroll-icon">&#8595;</div> <!-- Icona della freccia -->
    </div>
</div>

<div class="d-flex flex-column align-items-center carousel-container">  
<div id="carouselExampleIndicators" class="carousel slide" data-bs-interval="5000">
        <ol class="carousel-indicators">
            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                    <div class="color-overlay"></div>    
                    <img class="d-block slide-image" src="../assets/images/prima_slide.jpg" alt="First slide">
                </div>
                <div class="carousel-caption d-none d-md-block">
                    <h5>Benvenuti al Ristorante</h5>
                    <p>Scopri i nostri piatti unici.</p>
                </div>
            </div>
            <div class="carousel-item">
                <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                    <div class="color-overlay"></div>
                    <img class="d-block slide-image" src="../assets/images/seconda_slide.png" alt="Second slide">
                </div>
                <div class="carousel-caption d-none d-md-block" >
                    <h5 style="font-weight:bold">Gusto del Mare</h5>
                    <p>Immergiti nei sapori autentici del mare con i nostri deliziosi spaghetti alle vongole, un classico intramontabile della cucina italiana.</p>
                </div>
            </div>
            <div class="carousel-item">
                <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                    <div class="color-overlay"></div>
                    <img class="d-block slide-image" src="../assets/images/terza_slide.jpg" alt="Third slide">
                </div>
                <div class="carousel-caption d-none d-md-block" >
                    <h5 style="font-weight:bold">Dolci Tentazioni</h5>
                    <p>Termina il tuo pasto con dolcezza grazie ai nostri dessert artigianali, preparati con passione e ingredienti di alta qualità.</p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </a>
    </div>
</div>

<div class="mt-5">
    <h2 class="text-center mb-4">Il Nostro Menu</h2>

    <!-- Antipasti -->
<div class="d-flex flex-row py-5 hover-effect-container" style="background-color: #072944; color: white;">
    <div class="col-md-6 d-flex justify-content-center">
        <img src="../assets/images/antipasti.png" class="img-fluid rounded menu_image " alt="Antipasti">
    </div>
    <div class="col-md-6 d-flex justify-content-center d-flex align-items-center">
        <div>
            <h3>Antipasti</h3>
            <p>Un assortimento di deliziosi antipasti per iniziare il tuo pasto in grande stile.</p>
        </div>
    </div>
    <!-- Overlay -->
     <a href="menu.php?category=antipasti">
    <div class="hover-overlay first-type-hover" >
        <div class="hover-overlay-text">Scopri gli antipasti</div>
    </div>
    </a>
</div>

  

    <!-- Primi -->
    <div class="py-5 d-flex flex-row hover-effect-container" style="background-color: white; color: #072944;">
        <div class="col-md-6 d-flex justify-content-center order-md-2">
            <img src="../assets/images/primi.png" class="img-fluid rounded menu_image" alt="Primi">
        </div>
        <div class="col-md-6 d-flex justify-content-center d-flex align-items-center order-md-1">
            <div>
                <h3>Primi</h3>
                <p>Dai classici della tradizione italiana a piatti innovativi, i nostri primi sapranno conquistarti.</p>
            </div>
        </div>
         <!-- Overlay -->
         <a href="menu.php?category=primi">
    <div class="hover-overlay second-type-hover" >
        <div class="hover-overlay-text">Scopri i nostri primi</div>
    </div>
    </a>
    </div>

    <!-- Secondi -->
    <div class="py-5 d-flex flex-row  hover-effect-container" style="background-color: #072944; color: white;">
        <div class="col-md-6 d-flex justify-content-center">
            <img src="../assets/images/secondi.jpg" class="img-fluid rounded menu_image" alt="Secondi">
        </div>
        <div class="col-md-6 d-flex align-items-center">
            <div>
                <h3>Secondi</h3>
                <p>Scopri le nostre specialità di carne e pesce, preparate con ingredienti freschi e di alta qualità.</p>
            </div>
        </div>
        <a href="menu.php?category=secondi">
    <div class="hover-overlay first-type-hover" >
        <div class="hover-overlay-text">Scopri i nostri secondi</div>
    </div>
    </a>
    </div>


    <!-- Dessert -->
    <div class="py-5 d-flex flex-row hover-effect-container" style="background-color: white; color: #072944;">
        <div class="col-md-6 d-flex justify-content-center order-md-2">
        <img src="../assets/images/dessert.jpg" class="img-fluid rounded menu_image" alt="Dessert">
    </div>
        <div class="col-md-6 d-flex justify-content-center d-flex align-items-center order-md-1">
        <div>
                <h3>Dessert</h3>
                <p>Concludi il tuo pasto con i nostri dessert irresistibili, dolci tentazioni per tutti i gusti.</p>
            </div>
        </div>
         <!-- Overlay -->
         <a href="menu.php?category=dessert">
    <div class="hover-overlay second-type-hover" >
        <div class="hover-overlay-text">Scopri i nostri dessert</div>
    </div>
    </a>
    </div>
    <?php include('footer.php');?>
</body>

</html>

