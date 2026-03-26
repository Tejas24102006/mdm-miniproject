<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header(
    "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With",
);
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}
include_once "../config/db.php";
$data = json_decode(file_get_contents("php://input"));
if (
    !empty($data->name) &&
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->role)
) {
    $name = trim($data->name);
    $email = trim($data->email);
    // Secure password hashing
    $password_hash = password_hash(trim($data->password), PASSWORD_DEFAULT);
    $role = trim($data->role);
    // Validate role
    $allowed_roles = ["customer", "provider", "admin"];
    if (!in_array($role, $allowed_roles)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid role."]);
        exit();
    }
    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)",
    );
    $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
    try {
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "User was registered successfully.",
            ]);
        } else {
            http_response_code(503);
            echo json_encode([
                "status" => "error",
                "message" => "Unable to register the user.",
            ]);
        }
    } catch (mysqli_sql_exception $e) {
        http_response_code(409); // Conflict
        echo json_encode([
            "status" => "error",
            "message" => "Email already exists.",
        ]);
    }

    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode([
        "status" => "error",
        "message" => "Incomplete data. Please fill all fields.",
    ]);
}
?>
