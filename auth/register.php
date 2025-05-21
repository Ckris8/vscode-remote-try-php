<?php // creo un utente con i dati ricevuti 
session_start(); //ripristino sessione

require_once("../db/database.php");

if (isset($_POST['register'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $email = $_POST['email'] ?? '';
    $numero = $_POST['numero'] ?? '';

    $isUsernameValid = filter_var(
        $username,
        FILTER_VALIDATE_REGEXP, [
            "options" => [
                "regexp" => "/^[a-z\d_]{3,20}$/i"
            ]
        ]
    );

    $pwdLenght = mb_strlen($password);

    if (
        empty($username) || empty($password) || empty($nome) ||
        empty($cognome) || empty($email) || empty($numero)
    ) {
        $msg = 'Compila tutti i campi %s';
    } elseif (false === $isUsernameValid) {
        $msg = 'Lo username non è valido. Sono ammessi solamente caratteri 
                alfanumerici e l\'underscore. Lunghezza minina 3 caratteri.
                Lunghezza massima 20 caratteri';
    } elseif ($pwdLenght < 8 || $pwdLenght > 20) {
        $msg = 'Lunghezza minima password 8 caratteri.
                Lunghezza massima 20 caratteri';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Email non valida %s';
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $query = "
            SELECT id
            FROM users
            WHERE username = :username
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':username', $username, PDO::PARAM_STR);
        $check->execute();

        $user = $check->fetchAll(PDO::FETCH_ASSOC);

        if (count($user) > 0) {
            $msg = 'Username già in uso %s';
        } else {
            $query = "
                INSERT INTO users(id, username, password, nome, cognome, email, numero)
                VALUES (0, :username, :password, :nome, :cognome, :email, :numero)
            ";

            $check = $pdo->prepare($query);
            $check->bindParam(':username', $username, PDO::PARAM_STR);
            $check->bindParam(':password', $password_hash, PDO::PARAM_STR);
            $check->bindParam(':nome', $nome, PDO::PARAM_STR);
            $check->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $check->bindParam(':email', $email, PDO::PARAM_STR);
            $check->bindParam(':numero', $numero, PDO::PARAM_STR);
            $check->execute();

            if ($check->rowCount() > 0) {
                $msg = 'Registrazione eseguita con successo %s';
            } else {
                $msg = 'Problemi con l\'inserimento dei dati %s';
            }
        }
    }
    printf($msg, '<br><a href="../greencity/index.php">torna indietro</a>');
}
?>