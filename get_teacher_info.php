<?php
// Мәліметтер базасымен байланыс үшін db.php файлын қосу
require_once 'db.php';

// Мұғалім мен күн параметрлері тексеріледі
if (isset($_GET['teacher']) && isset($_GET['day'])) {
    // Мұғалім мен күн параметрлерін GET сұранымынан алу
    $teacher = $_GET['teacher'];
    $day = $_GET['day'];

    // Мұғалімнің аты мен күн бойынша кестені алу үшін SQL сұранымын дайындау
    $stmt = $pdo->prepare("SELECT group_name, subject, classroom, pairs_count, start_time, end_time FROM schedule WHERE teacher_name = ? AND day_of_week = ?");
    
    // Сұранымды орындатып, нәтижелерді алу
    $stmt->execute([$teacher, $day]);
    $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Егер кесте бос болса, қате хабарламасын JSON форматында қайтару
    if (!$schedule) {
        echo json_encode(['error' => 'Деректер табылмады']); // 'No data found' дегенді 'Деректер табылмады' деп аудару
        exit();
    }

    // Жауап түрін JSON деп белгілеу және сабақ кестесін JSON форматында қайтару
    header('Content-Type: application/json');
    echo json_encode($schedule);
} else {
    // Егер параметрлер жарамсыз болса, қате хабарламасын қайтару
    echo json_encode(['error' => 'Жарамсыз параметрлер']); // 'Invalid parameters' дегенді 'Жарамсыз параметрлер' деп аудару
}
?>
