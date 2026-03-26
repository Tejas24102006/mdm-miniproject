<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"));
    
if (!empty($data->booking_id) && !empty($data->status)) {
    $booking_id = (int)$data->booking_id;
    $status = trim($data->status);
    
    $valid_statuses = ['pending', 'accepted', 'rejected', 'completed'];
    if (!in_array($status, $valid_statuses)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid status."]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Booking status updated to $status."]);
    } else {
        http_response_code(503);
        echo json_encode(["status" => "error", "message" => "Unable to update booking status."]);
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing booking_id or status."]);
}
?>
