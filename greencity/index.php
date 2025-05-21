<?php
session_start();
$isLogged = isset($_SESSION['username']);

// Connessione al database
require_once("../db/database.php");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Preleva le ultime 3 segnalazioni con latitudine e longitudine
$stmt = $pdo->query("SELECT data, ora, motivo, note, latitudine, longitudine FROM segnalazioni ORDER BY id DESC LIMIT 3");
$ultimeSegnalazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Conta il totale delle segnalazioni
$stmtCount = $pdo->query("SELECT COUNT(*) as totale FROM segnalazioni");
$totaleSegnalazioni = $stmtCount->fetch(PDO::FETCH_ASSOC)['totale'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>indice</title>
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/main.js"></script>
    </head>
    <body>
        <header>
            <h1>GreenHub - Connettiamoci per un mondo più sostenibile!</h1>
        </header>

        <div class="dropdown">
            <button class="dropdown-button" onclick="menu()">
                <img src="../assets/icons/menu-icon.png" alt="Menu" class="menu-icon">
            </button>
            <div class="dropdown-menu">
                <a href="index.php">Home</a>
                <a href="profilo.php">Profilo</a>
                <a href="mappa.html">Mappa</a>
            </div>
        </div>

        <a href="profilo.php" class="profile-button">
            <img src="../assets/icons/iconProfilo.png" alt="Profilo" class="profile-icon">
        </a>

        <h1>Presentazione del Progetto: GreenHub - Il Portale per un Futuro Sostenibile</h1>
        <p>GreenHub è un portale innovativo dedicato alla sostenibilità ambientale.
            La piattaforma fornisce informazioni, strumenti e risorse per aziende, cittadini e istituzioni che desiderano ridurre 
            il proprio impatto ambientale e promuovere pratiche eco-friendly.
        </p>
        <h3>Obbiettivi progetto</h3>
        <ul>
            <li>Creare un punto di riferimento digitale per la sostenibilità</li>
            <li>Promuovere l'educazione ambientale e le buone pratiche green</li>
            <li>Offrire strumenti per il calcolo dell’impronta ecologica</li>
            <li>Supportare aziende e cittadini nella transizione verso modelli sostenibili</li>
        </ul>

        <section class="infographic">
            <div class="stat">
                <h2>70%</h2>
                <p>Delle emissioni globali di CO2 provengono da città</p>
            </div>
            <div class="stat">
                <h2>50%</h2>
                <p>Delle risorse naturali sono già state consumate</p>
            </div>
            <div class="stat">
                <h2>30%</h2>
                <p>Di energia può essere risparmiata con pratiche green</p>
            </div>
        </section>
        <h3>Mappa segnalazioni</h3>
        <br><img src="../assets/icons/mappaMin.png" alt="20" style="margin-left: 15%;">
        <p>Agevoliamo l'aiuto a sviluppare spazi green o la risoluzione di problemi inerenti, grazie ad una mappa interrattiva dove potete inserire segnalazioni o rinnovamenti specificandone luogo e data</p>
        <section align="center">
            <button class="button" onclick="location.href='mappa.html'">Vai alla Mappa</button>
            <button class="button" onclick="location.href='segnala.html'">Nuova Segnalazione</button>
        </section>
        <p style="text-align: center;">Grazie per il contributo!!!</p>

        <section>
            <h3>Ultime Segnalazioni</h3>
            <table border="1" style="width: 100%; text-align: left;">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Ora</th>
                        <th>Motivo</th>
                        <th>Note</th>
                        <th>Latitudine</th>
                        <th>Longitudine</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimeSegnalazioni as $segnalazione): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($segnalazione['data']); ?></td>
                            <td><?php echo htmlspecialchars($segnalazione['ora']); ?></td>
                            <td><?php echo htmlspecialchars($segnalazione['motivo']); ?></td>
                            <td><?php echo htmlspecialchars($segnalazione['note']); ?></td>
                            <td><?php echo htmlspecialchars($segnalazione['latitudine']); ?></td>
                            <td><?php echo htmlspecialchars($segnalazione['longitudine']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Numero totale di segnalazioni:</strong> <?php echo $totaleSegnalazioni; ?></p>
        </section>

        <section>
            <h3>Link Utili per la Sostenibilità</h3>
            <ul>
                <li><a href="https://www.un.org/sustainabledevelopment/" target="_blank">Obiettivi di Sviluppo Sostenibile - ONU</a></li>
                <li><a href="https://www.wwf.it/" target="_blank">WWF Italia</a></li>
                <li><a href="https://www.greenpeace.org/italy/" target="_blank">Greenpeace Italia</a></li>
                <li><a href="https://www.legambiente.it/" target="_blank">Legambiente</a></li>
                <li><a href="https://www.ipcc.ch/" target="_blank">IPCC - Cambiamenti Climatici</a></li>
            </ul>
        </section>

        <!-- Footer -->
        <footer>
            <div>
                <img src="../assets/icons/facebook_icon.png" alt="" height="20px" width="20px"><a href="https://www.facebook.com" target="_blank">Facebook</a> 
                <br><img src="../assets/icons/ista_icon.png" alt="" height="20px" width="20px"><a href="https://www.instagram.com" target="_blank">Instagram</a>
            </div>
            <div id="orarioLocale"></div>
        </footer>
    </body>
</html>
