<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->booking_id) && !empty($data->customer_id) && !empty($data->rating)) {
    $booking_id = (int)$data->booking_id;
    $customer_id = (int)$data->customer_id;
    $rating = (int)$data->rating;
    $comment = isset($data->comment) ? trim($data->comment) : "";

    if ($rating < 1 || $rating > 5) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Rating must be between 1 and 5."]);
        exit();
    }

    // Verify booking belongs to customer and is completed
    $check_stmt = $conn->prepare("SELECT provider_id, status FROM bookings WHERE id = ? AND customer_id = ?");
    $check_stmt->bind_param("ii", $booking_id, $customer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['status'] !== 'completed') {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "You can only review completed services."]);
            exit();
        }
        
        $provider_id = $row['provider_id'];

        // Uses UNIQUE constraint on booking_id in schema to prevent duplicates automatically
        $stmt = $conn->prepare("INSERT INTO reviews (booking_id, customer_id, provider_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $booking_id, $customer_id, $provider_id, $rating, $comment);

        try {
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Review submitted successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["status" => "error", "message" => "Unable to submit review."]);
            }
        } catch (mysqli_sql_exception $e) {
            http_response_code(409);
            echo json_encode(["status" => "error", "message" => "You have already reviewed this booking."]);
        }
        $stmt->close();
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Booking not found or does not belong to you."]);
    }
    $check_stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Incomplete data. Please provide booking_id, customer_id, and rating."]);
}
?>
