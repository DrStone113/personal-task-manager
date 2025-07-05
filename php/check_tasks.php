<?php
include './connect.php';
require '../vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

$now = new DateTime();
$in_30_minutes = clone $now;
$in_30_minutes->add(new DateInterval('PT30M'));

// Lấy các task sắp bắt đầu trong 30 phút
$stmt_start = $conn->prepare("
    SELECT t.*, u.fcm_token 
    FROM tasks t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.start_time BETWEEN ? AND ? 
    AND t.status != 'Completed'
");
$now_str = $now->format('Y-m-d H:i:s');
$in_30_minutes_str = $in_30_minutes->format('Y-m-d H:i:s');
$stmt_start->bind_param("ss", $now_str, $in_30_minutes_str);
$stmt_start->execute();
$tasks_start = $stmt_start->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_start->close();

// Lấy các task sắp kết thúc trong 30 phút
$stmt_end = $conn->prepare("
    SELECT t.*, u.fcm_token 
    FROM tasks t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.end_time BETWEEN ? AND ? 
    AND t.status != 'Completed'
");
$now_str = $now->format('Y-m-d H:i:s');
$in_30_minutes_str = $in_30_minutes->format('Y-m-d H:i:s');
$stmt_end->bind_param("ss", $now_str, $in_30_minutes_str);
$stmt_end->execute();
$tasks_end = $stmt_end->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_end->close();

// Hàm gửi thông báo
function sendNotification($token, $title, $body) {
    $credential = new ServiceAccountCredentials(
        "https://www.googleapis.com/auth/firebase.messaging",
        json_decode(file_get_contents("./pvKey.json"), true)
    );

    $authToken = $credential->fetchAuthToken(HttpHandlerFactory::build());
    if (isset($authToken['access_token'])) {
        $ch = curl_init("https://fcm.googleapis.com/v1/projects/task-manager-c5c37/messages:send");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $authToken['access_token']
        ]);

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'webpush' => [
                    'fcm_options' => [
                        'link' => 'http://localhost/personal-task-manager/dashboard.php'
                    ]
                ]
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    } else {
        return "Failed to get access token";
    }
}

// Hàm lưu thông báo vào cơ sở dữ liệu
function saveNotification($user_id, $title, $body) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, body) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $body);
    $stmt->execute();
    $stmt->close();
}

// Gửi thông báo cho các task sắp bắt đầu
foreach ($tasks_start as $task) {
    if ($task['fcm_token']) {
        $title = "Planex";
        $body = "Task '" . htmlspecialchars($task['title']) . "' will start in 30 minutes.";
        $response = sendNotification($task['fcm_token'], $title, $body); // Hàm gửi FCM
        if ($response) {
            saveNotification($task['user_id'], $title, $body);
        }
    }
}

// Gửi thông báo cho các task sắp kết thúc
foreach ($tasks_end as $task) {
    if ($task['fcm_token']) {
        $title = "Planex";
        $body = "Task '" . htmlspecialchars($task['title']) . "' will end in 30 minutes.";
        $response = sendNotification($task['fcm_token'], $title, $body); // Hàm gửi FCM
        if ($response) {
            saveNotification($task['user_id'], $title, $body);
        }
    }
}

$conn->close();
?>