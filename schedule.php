<?php
// Барлық қателіктерді көрсету үшін қателерді баптау
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Сессияны бастау
session_start();

// Егер қолданушы тіркелмеген болса, оны басты бетке бағыттау
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Мәліметтер базасымен байланыс орнату
require_once 'db.php';

// Барлық мұғалімдердің тізімін алу, бір реттен ғана көрсетіледі
$stmt = $pdo->query("SELECT DISTINCT teacher_name FROM schedule ORDER BY teacher_name");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Белл уақыттарын алу
$stmt = $pdo->query("SELECT time_range FROM bell_times");
$bellTimes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <title>Мұғалімдер</title>
    <style>
        /* Беттің негізгі стилі */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: space-between;
            padding: 20px;
            margin: 0;
        }
        /* Жанама тақта стилі */
        .sidebar {
            width: 25%;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-height: 80vh;
            overflow-y: auto;
        }
        /* Мазмұн стилі */
        .content {
            width: 70%;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            min-height: 80vh;
        }
        /* Қолданылатын тақырыптар стилі */
        h1, h2 {
            color: #333;
            margin-bottom: 10px;
        }
        /* Мұғалім батырмаларының стилі */
        .teacher-button {
            width: 100%;
            display: block;
            margin: 5px 0;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #2196F3;
            color: white;
            cursor: pointer;
            text-align: left;
            transition: background-color 0.3s;
        }
        .teacher-button:hover {
            background-color: #1976D2;
        }
        /* Күн батырмаларының стилі */
        #daysButtons button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #2196F3;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        #daysButtons button:hover {
            background-color: #1976D2;
        }
        /* Уақыт батырмасының стилі */
        #timeButton {
            margin: 5px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #2196F3;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
            float: right; /* Батырманы оң жаққа орналастыру */
        }
        #timeButton:hover {
            background-color: #1976D2;
        }
        /* Уақыт ақпаратының стилі */
        #timeInfo {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Жанама тақта, мұнда мұғалімдердің тізімі батырма түрінде көрсетіледі -->
    <div class="sidebar">
        <h1>Мұғалімдер</h1>
        <?php foreach ($teachers as $teacher): ?>
            <button class="teacher-button" onclick="showDays('<?= htmlspecialchars($teacher['teacher_name'], ENT_QUOTES) ?>')">
                <?= htmlspecialchars($teacher['teacher_name'], ENT_QUOTES) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Мазмұн бөлімі, мұнда мұғалімдердің кестесі көрсетіледі -->
    <div class="content" id="scheduleInfo" style="display: none;">
        <h2 id="teacherName"></h2>
        <div id="daysButtons" style="margin-bottom: 20px;">
            <!-- Әр күн үшін батырмалар -->
            <button onclick="showScheduleByDay('Дүйсенбі')">Дүйсенбі</button>
            <button onclick="showScheduleByDay('Сейсенбі')">Сейсенбі</button>
            <button onclick="showScheduleByDay('Сәрсенбі')">Сәрсенбі</button>
            <button onclick="showScheduleByDay('Бейсенбі')">Бейсенбі</button>
            <button onclick="showScheduleByDay('Жұма')">Жұма</button>
            <button onclick="showScheduleByDay('Сенбі')">Сенбі</button> 
            <button onclick="showScheduleByDay('Жексенбі')">Жексенбі</button> 
            <button id="timeButton" onclick="toggleTimes()">Уақыты</button>
        </div>

        <div id="scheduleInfoContent"></div>
        <div id="timeInfo"></div>
    </div>

    <script>
    // JavaScript функциялары
    document.addEventListener('DOMContentLoaded', function() {
        let selectedTeacher = '';
        let shownDays = {}; 

        // Мұғалімнің кестесін көрсету функциясы
        function showDays(teacher) {
            selectedTeacher = teacher;
            document.getElementById('daysButtons').style.display = 'block';
            document.getElementById('teacherName').innerText = teacher;
            document.getElementById('scheduleInfo').style.display = 'block';
            document.getElementById('scheduleInfoContent').innerHTML = '';
            document.getElementById('timeInfo').style.display = 'none'; 
            shownDays = {}; 
        }

        window.showDays = showDays;

        // Күн бойынша кестені көрсету функциясы
        function showScheduleByDay(day) {
            if (shownDays[day]) {
                document.getElementById('scheduleInfoContent').innerHTML = '';
                shownDays[day] = false;
            } else {
                console.log(`Кесте алу үшін қолданылады ${selectedTeacher} на ${day}`);
                fetch(`get_schedule_info.php?teacher=${encodeURIComponent(selectedTeacher)}&day=${encodeURIComponent(day)}`)
                    .then(response => response.json())
                    .then(data => {
                        let scheduleHtml = `<h2>${selectedTeacher} - ${day}</h2>`;
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(item => {
                                scheduleHtml += `
                                    <div style="margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #ddd;">
                                        <p><strong>Топ:</strong> ${item.group_name}</p>
                                        <p><strong>Сабақ:</strong> ${item.subject}</p>
                                        <p><strong>Кабинет:</strong> ${item.classroom}</p>
                                        <p><strong>Сабақтар саны:</strong> ${item.pairs_count}</p>
                                    </div>`;
                            });
                        } else {
                            scheduleHtml += '<p>Бұл күнде сабақ жоқ.</p>';
                        }
                        document.getElementById('scheduleInfoContent').innerHTML = scheduleHtml;
                        shownDays[day] = true;
                    })
                    .catch(error => {
                        console.error('Қате:', error);
                        document.getElementById('scheduleInfoContent').innerHTML = '<p>Деректерді жүктеу кезінде қате пайда болды.</p>';
                    });
            }
        }

        window.showScheduleByDay = showScheduleByDay;
        
        // Уақытты көрсету батырмасын басу
        window.toggleTimes = function() {
            const timeInfoDiv = document.getElementById('timeInfo');
            if (timeInfoDiv.style.display === 'block') {
                timeInfoDiv.style.display = 'none'; 
            } else {
                let timeInfo = '<h3>Уақыттары</h3><ul>';
                <?php foreach ($bellTimes as $time): ?>
                    timeInfo += `<li><?= htmlspecialchars($time['time_range'], ENT_QUOTES) ?></li>`;
                <?php endforeach; ?>
                timeInfo += '</ul>';
                timeInfoDiv.innerHTML = timeInfo;
                timeInfoDiv.style.display = 'block'; 
            }
        }
    });

