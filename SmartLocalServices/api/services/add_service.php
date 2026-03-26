<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->provider_id) && !empty($data->service_name) && !empty($data->price) && !empty($data->location) && isset($data->description)) {
    $provider_id = (int) $data->provider_id;
    $service_name = trim($data->service_name);
    $price = (float) $data->price;
    $location = trim($data->location);
    $description = trim($data->description);

    // Basic verification that provider exists and is a provider
    $check_stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $check_stmt->bind_param("i", $provider_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['role'] !== 'provider') {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Only providers can add services."]);
            exit();
        }
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Provider not found."]);
        exit();
    }
    $check_stmt->close();

    $stmt = $conn->prepare("INSERT INTO services (provider_id, service_name, price, location, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $provider_id, $service_name, $price, $location, $description);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Service created successfully."]);
    } else {
        http_response_code(503);
        echo json_encode(["status" => "error", "message" => "Unable to create service."]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Incomplete data. Please provide all fields."]);
}
?>
