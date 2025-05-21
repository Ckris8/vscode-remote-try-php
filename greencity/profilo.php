<?php
session_start();



if (!isset($_SESSION['session_user'])) {
    header("Location: ../auth/login.html");
    exit();
}

try {
    require_once("../db/database.php");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT nome, cognome, numero, email FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['session_user']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        // Utente non trovato, esci
        session_destroy();
        header("Location: ../auth/login.html");
        exit();
    }
} catch (PDOException $e) {
    die("Errore connessione database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Profilo</title>
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/main.js"></script>
    </head>
    <body>
        <header>
            <h1>Profilo Utente</h1>
        </header>

        <div class="dropdown">
            <button class="dropdown-button">
                <img src="../assets/icons/menu-icon.png" alt="Menu" class="menu-icon">
            </button>
            <div class="dropdown-menu">
                <a href="index.php">Home</a>
                <a href="profilo.php">Profilo</a>
                <a href="mappa.html">Mappa</a>
            </div>
        </div>

        <section class="profile-container">
            <div class="profile-card">
                <img src="../assets/icons/iconProfilo.png" alt="Immagine Profilo" class="profile-picture">
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($userData['nome'] . ' ' . $userData['cognome']); ?></h2>
                    <p><strong>Numero:</strong> <?php echo htmlspecialchars($userData['numero']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                </div>
            </div>
        </section>

        

        <!-- Sezione per le ultime segnalazioni -->
        <section class="profile-segnalazioni">
            <h2>Ultime Segnalazioni</h2>
            <table border="1" style="width: 100%; text-align: left;">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Ora</th>
                        <th>Motivo</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody id="ultimeSegnalazioni">
                    <!-- Le segnalazioni verranno aggiunte dinamicamente -->
                </tbody>
            </table>
        </section>

        <!-- Sezione per i badge di merito -->
        <section class="profile-badge">
            <h2>Badge di Merito</h2>
            <div class="badge-container">
                <p>Qui verranno mostrati i badge di merito ottenuti dall'utente.</p>
                <!-- Badge placeholder -->
                <div class="badge-placeholder">
                    <br>
                    <p>Badge in arrivo...</p>
                </div>
            </div>
        </section>

        <!-- Sezione per personalizzare le notifiche -->
        <section class="profile-notifiche">
            <h2>Personalizza Notifiche</h2>
            <div class="notifiche-container">
                <br><label>
                    <input type="checkbox" id="notificaEmail" />
                    Notifiche via Email
                </label>
                <br><label>
                    <input type="checkbox" id="notificaSMS" />
                    Notifiche via SMS
                </label>
                <br><label>
                    <input type="checkbox" id="notificaPush" />
                    Notifiche Push
                </label>
            </div>
        </section>

        <button class="button" onclick="location.href='index.php'">Torna alla Home</button>

        <!-- Pulsante Logout -->
        <form method="post" action="../auth/logout.php" style="margin-top:20px; text-align:center;">
            <button type="submit" class="button" style="background-color: #d32f2f; color: white;">Logout</button>
        </form>

        <!-- Footer -->
        <footer>
            <div>
                <img src="../assets/icons/facebook_icon.png" alt="" height="20px" width="20px"><a href="https://www.facebook.com" target="_blank">Facebook</a> 
                <br><img src="../assets/icons/ista_icon.png" alt="" height="20px" width="20px"><a href="https://www.instagram.com" target="_blank">Instagram</a>
            </div>
            <div id="orarioLocale"></div>
        </footer>

        <script>
            // Recupera le segnalazioni da localStorage
            let segnalazioni = JSON.parse(localStorage.getItem('segnalazioni')) || [];

            // Mostra le ultime 5 segnalazioni
            const ultimeSegnalazioni = document.getElementById('ultimeSegnalazioni');
            segnalazioni.slice(-5).reverse().forEach(segnalazione => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${segnalazione.data}</td>
                    <td>${segnalazione.ora}</td>
                    <td>${segnalazione.motivo}</td> <!-- Mostra il motivo -->
                    <td>${segnalazione.note || 'Nessuna nota'}</td> <!-- Mostra le note -->
                `;
                ultimeSegnalazioni.appendChild(row);
            });

            // Recupera lo stato delle notifiche da localStorage
            const notifiche = JSON.parse(localStorage.getItem('notifiche')) || {
                email: false,
                sms: false,
                push: false
            };

            // Aggiorna lo stato degli interruttori in base ai dati salvati
            document.getElementById('notificaEmail').checked = notifiche.email;
            document.getElementById('notificaSMS').checked = notifiche.sms;
            document.getElementById('notificaPush').checked = notifiche.push;

            // Aggiunge event listener per salvare lo stato delle notifiche
            document.getElementById('notificaEmail').addEventListener('change', (e) => {
                notifiche.email = e.target.checked;
                localStorage.setItem('notifiche', JSON.stringify(notifiche));
            });

            document.getElementById('notificaSMS').addEventListener('change', (e) => {
                notifiche.sms = e.target.checked;
                localStorage.setItem('notifiche', JSON.stringify(notifiche));
            });

            document.getElementById('notificaPush').addEventListener('change', (e) => {
                notifiche.push = e.target.checked;
                localStorage.setItem('notifiche', JSON.stringify(notifiche));
            });
        </script>
    </body>
</html>