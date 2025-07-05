<?php
include './php/connect.php';
include './php/auth_check.php';

$user_id = $_SESSION['user_id'];
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 30; // Default 30 minutes
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d', strtotime('+7 days'));

$available_slots = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get all tasks within the date range
    $stmt = $conn->prepare("
        SELECT start_time, duration 
        FROM tasks 
        WHERE user_id = ? 
        AND start_time BETWEEN ? AND ?
        AND status != 'Completed'
        ORDER BY start_time ASC
    ");
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $busy_slots = [];
    while ($task = $result->fetch_assoc()) {
        $start = new DateTime($task['start_time']);
        $end = clone $start;
        $end->modify('+' . $task['duration'] . ' minutes');
        $busy_slots[] = ['start' => $start, 'end' => $end];
    }

    // Define working hours (7 AM to 5 PM)
    $work_start = 7;
    $work_end = 17;

    // Find available slots
    $current_date = new DateTime($start_date);
    $end_datetime = new DateTime($end_date);

    while ($current_date <= $end_datetime) {
        if ($current_date->format('N') <= 5) { // Monday to Friday
            $day_start = clone $current_date;
            $day_start->setTime($work_start, 0);
            $day_end = clone $current_date;
            $day_end->setTime($work_end, 0);

            $slot_start = clone $day_start;
            while ($slot_start < $day_end) {
                $slot_end = clone $slot_start;
                $slot_end->modify('+' . $duration . ' minutes');

                $is_available = true;
                foreach ($busy_slots as $busy) {
                    if (
                        ($slot_start >= $busy['start'] && $slot_start < $busy['end']) ||
                        ($slot_end > $busy['start'] && $slot_end <= $busy['end']) ||
                        ($slot_start <= $busy['start'] && $slot_end >= $busy['end'])
                    ) {
                        $is_available = false;
                        break;
                    }
                }

                if ($is_available && $slot_end <= $day_end) {
                    $available_slots[] = [
                        'start' => clone $slot_start,
                        'end' => clone $slot_end
                    ];
                }

                $slot_start->modify('+30 minutes'); // Check every 30-minute interval
            }
        }
        $current_date->modify('+1 day');
    }
}
?>