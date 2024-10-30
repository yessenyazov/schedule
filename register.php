<?php
require_once 'db.php'; // Дерекқор қосылымын орнату үшін db.php файлды қосамыз.

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Егер форма жіберілсе, осы блок іске қосылады.
    $full_name = $_POST['full_name']; // Пайдаланушының толық аты-жөнін аламыз.
    $username = $_POST['username']; // Пайдаланушының логинін аламыз.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Құпиясөзді хештеу арқылы аламыз.

    // Логин бойынша дерекқордан тексеру.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?"); 
    $stmt->execute([$username]); 
    if ($stmt->rowCount() > 0) { // Егер мұндай логин бар болса, қате хабарламасын көрсетеміз.
        $error_message = "Пользователь с таким именем уже существует!";
    } else {
        // Егер логин бос болса, дерекқорға жаңа пайдаланушыны енгіземіз.
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$full_name, $username, $password])) {
            header("Location: index.php"); // Тіркеу сәтті болса, басты бетке бағыттаймыз.
            exit();
        } else {
            $error_message = "Ошибка регистрации!"; // Егер тіркелуде қате болса, хабарламаны көрсетеміз.
        }
    }
}
?>

<!DOCTYPE html>
<html lang="kk"> <!-- Қазақ тілі ретінде көрсетеміз -->
<head>
    <meta charset="UTF-8"> <!-- UTF-8 кодтауын қолданамыз -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Мобильдік құрылғыларда дұрыс көрсету -->
    <title>Регистрация</title> <!-- Беттің атауы -->
    <style>
        /* CSS стильдері */
        body {
            display: flex; /* Контейнерді орталыққа қою үшін flex қолданамыз */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #e9ecef; /* Бет фоны */
        }
        .container {
            background-color: white; /* Контейнердің фоны */
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Көлеңке */
            width: 100%;
            max-width: 400px; /* Максималды ені */
            text-align: center; 
        }
        h1 {
            margin: 0 0 20px; 
            font-size: 24px; 
            color: #007bff; 
            font-weight: 600; 
        }
        .logo {
            margin-bottom: 20px; 
        }
        input[type="text"], input[type="password"] {
            margin: 10px 0; 
            padding: 12px; 
            border: 1px solid #ced4da; 
            border-radius: 6px; 
            transition: border-color 0.3s; 
            width: calc(100% - 24px); /* Input алаң ені */
            font-size: 16px; 
            box-sizing: border-box; 
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007bff; 
            outline: none; 
        }
        button {
            padding: 12px; 
            border: none;
            background-color: #007bff; 
            color: white;
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600; 
            transition: background-color 0.3s; 
            width: calc(100% - 24px); 
            font-size: 16px; 
            margin-top: 20px; 
        }
        button:hover {
            background-color: #0056b3; 
        }
        .error-message {
            color: red; /* Қате хабарламаның түсі */
            margin-bottom: 15px; 
            font-weight: bold; 
        }
    </style>
</head>
<body>

<div class="container">
    <img src="1.png" alt="Логотип" class="logo" width="150"> <!-- Логотипті көрсетеміз -->
    <h1>Ақтөбе құрылыс техникалық колледжі</h1> <!-- Беттің тақырыбы -->
    <?php if (isset($error_message)): ?> <!-- Қате хабарламаны көрсету -->
        <p class="error-message"><?= $error_message; ?></p>
    <?php endif; ?>
    <form action="register.php" method="post">
        <input type="text" name="full_name" placeholder="Пайдаланушының толық аты жөні" required> <!-- Толық аты-жөн алаңы -->
        <input type="text" name="username" placeholder="Логин" required> <!-- Логин алаңы -->
        <input type="password" name="password" placeholder="Құпиясөз" required> <!-- Құпиясөз алаңы -->
        <input type="password" name="confirm_password" placeholder="Құпиясөзді растау" required> <!-- Құпиясөзді растау алаңы -->
        <button type="submit">Тіркелу</button> <!-- Жіберу түймесі -->
    </form>
</div>

</body>
</html>