<?php
// db.php файлын қосу (мәліметтер базасымен байланыс үшін)
require_once 'db.php';

// GET сұранымынан мұғалім мен күн мәндерін алу
$teacher = $_GET['teacher'] ?? '';
$day = $_GET['day'] ?? '';

// Мұғалім мен күн бойынша сабақ кестесін алу үшін SQL сұранымын анықтау
$query = "SELECT group_name, subject, classroom, pairs_count FROM schedule WHERE teacher_name = :teacher AND day_of_week = :day";

// Сұранымды дайындау
$stmt = $pdo->prepare($query);

// Мұғалім мен күн параметрлерін қосу және сұранымды орындау
$stmt->execute(['teacher' => $teacher, 'day' => $day]);

// Нәтижелерді массив ретінде алу
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Жауап түрін JSON деп белгілеу
header('Content-Type: application/json');

// Сабақ кестесін JSON форматында қайтару
echo json_encode($schedule);