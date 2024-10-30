<?php
session_start(); // Жаңа немесе бар сессияны бастаймыз
require_once 'db.php'; // Дерекқорға қосылу үшін db.php файлды қосамыз
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Егер сұраныс әдісі POST болса, яғни форма жіберілген болса
    // Логин мен құпиясөзді POST сұранысынан аламыз
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Пайдаланушы аты бойынша дерекқордан пайдаланушы мәліметтерін іздейміз
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?"); 
    $stmt->execute([$username]); // Сұранысты орындап, логинді параметр ретінде жібереміз
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Қайтарылған жолды ассоциативті массив ретінде аламыз

    // Пайдаланушы бар және енгізілген құпиясөз сақталған құпиясөзбен сәйкес келсе
    if ($user && password_verify($password, $user['password'])) {
        
        $_SESSION['username'] = $user['username']; // Сессияға пайдаланушы атын сақтаймыз
        header("Location: schedule.php"); // Кесте бетіне қайта бағыттаймыз
        exit(); // Сценарийді аяқтаймыз
    } else {
        // Қате хабарламаны орнатамыз, егер логин немесе құпиясөз дұрыс емес болса
        $error_message = "Кіру есімі немесе құпиясөз дұрыс емес.";
    }
}
?>

<!DOCTYPE html> 
<html lang="kk"> <!-- Беттің тілі қазақша екенін көрсетеді -->
<head> 
    <meta charset="UTF-8"> <!-- Мәтіннің UTF-8 кодтауын пайдаланады, бұл барлық символдарды дұрыс көрсетуге мүмкіндік береді -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Мобильдік құрылғыларда дұрыс масштабтау үшін -->
    <title>Ақтөбе құрылыс-техникалық колледжі</title> <!-- Браузер қойындысындағы беттің атауы -->
    <style>
        /* CSS стилдері */
        body {
            display: flex; /* Орталыққа қою үшін flex */
            justify-content: center; /* Горизонталь бойынша ортасына қою */
            align-items: center; /* Вертикаль бойынша ортасына қою */
            height: 100vh; /* Беттің биіктігін экранның биіктігіне тең қою */
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff; 
        }
        .container {
            text-align: center; /* Мәтінді орталыққа қою */
            background-color: white; /* Контейнер фоны */
            padding: 30px 20px; /* Контейнердің ішкі шеттері */
            border-radius: 10px; /* Контейнердің дөңгелектелген шеттері */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Контейнердің көлеңкесі */
            width: 320px; /* Контейнер ені */
        }
        .logo {
            margin-bottom: 15px; /* Логотиптің төменгі шетінен шегініс */
        }
        h1, p {
            margin: 0; /* Шеткі мәндерді жоямыз */
            font-size: 24px; /* Шрифт өлшемі */
            color: #007bff; /* Мәтін түсі */
            font-weight: bold; /* Қалың шрифт */
            line-height: 1.2; /* Жоларалық интервал */
        }
        p {
            margin-top: 10px; /* Жоғарғы шеті */
        }
        form {
            display: flex; 
            flex-direction: column; /* Баған бойынша қою */
            align-items: stretch; /* Ені контейнер еніне тең */
        }
        input[type="text"], input[type="password"] {
            margin: 10px 0;
            padding: 12px; 
            border: 1px solid #ced4da; /* Шекара түсі */
            border-radius: 6px; /* Дөңгелектелген шеттер */
            transition: border-color 0.3s; /* Шекара түсінің ауысуы */
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007bff; /* Шекара түсі өзгеруі */
            outline: none; /* Сыртқы шекараны жою */
        }
        button {
            padding: 12px 0; 
            border: none;
            background-color: #007bff; /* Түйме түсі */
            color: white;
            border-radius: 6px; 
            cursor: pointer;
            font-weight: bold; 
            transition: background-color 0.3s; 
        }
        button:hover {
            background-color: #0056b3; /* Түймені шертуде түсі өзгеруі */
        }
        .register-link {
            margin-top: 15px;
            font-size: 14px; 
            color: #007bff; 
        }
        .register-link a {
            text-decoration: none; /* Асты сызылған сызықты алып тастайды */
        }
        .error-message {
            color: red; /* Қате туралы хабарламаның түсі */
            margin-bottom: 10px; 
        }
        input[type="text"]:first-child, input[type="password"]:first-child {
            margin-top: 20px; 
        }
    </style>
</head>
<body>
<div class="container">  
    <img src="1.png" alt="Логотип" class="logo" width="150">  <!-- Логотип суреті -->
    <h1>Ақтөбе құрылыс техникалық колледжі</h1> <!-- Бет тақырыбы -->
    <p>Қош келдіңіз!</p> <!-- Қош келдіңіз мәтіні -->
    <?php if (isset($error_message)): ?> <!-- Егер қате бар болса -->
        <p class="error-message"><?= $error_message; ?></p> <!-- Қате туралы хабарламаны көрсету -->
    <?php endif; ?> 
    <form action="index.php" method="post">
        <input type="text" name="username" placeholder="Логин" required> <!-- Логин енгізу алаңы -->
        <input type="password" name="password" placeholder="Құпиясөз" required> <!-- Құпиясөз енгізу алаңы -->
        <button type="submit">Кіру</button> <!-- Жіберу түймесі -->
    </form>
    <div class="register-link">
        <a href="register.php">Тіркелу</a> <!-- Тіркелу бетіне сілтеме -->
    </div>
</div>
</body>
</html>