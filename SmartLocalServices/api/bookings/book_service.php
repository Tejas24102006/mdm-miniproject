<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->customer_id) && !empty($data->service_id) && !empty($data->booking_date)) {
    $customer_id = (int)$data->customer_id;
    $service_id = (int)$data->service_id;
    $booking_date = trim($data->booking_date);

    // Get the provider_id for the service
    $service_stmt = $conn->prepare("SELECT provider_id FROM services WHERE id = ?");
    $service_stmt->bind_param("i", $service_id);
    $service_stmt->execute();
    $service_result = $service_stmt->get_result();

    if ($service_result->num_rows > 0) {
        $service_row = $service_result->fetch_assoc();
        $provider_id = $service_row['provider_id'];

        $stmt = $conn->prepare("INSERT INTO bookings (customer_id, service_id, provider_id, booking_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $customer_id, $service_id, $provider_id, $booking_date);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Service booked successfully."]);
        } else {
            http_response_code(503);
            echo json_encode(["status" => "error", "message" => "Unable to book the service."]);
        }

        $stmt->close();
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Service not found."]);
    }
    $service_stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Incomplete data. Please provide all required fields."]);
}
?>
