<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/db.php';

// Allow an admin_id parameter to verify basic access (simplistic)
if (!isset($_GET['admin_id'])) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Admin access required."]);
    exit();
}

$admin_id = (int)$_GET['admin_id'];
$check = $conn->prepare("SELECT role FROM users WHERE id = ?");
$check->bind_param("i", $admin_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0 || $res->fetch_assoc()['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}
$check->close();

$stats = [];

// Total users
$res = $conn->query("SELECT role, COUNT(id) as count FROM users GROUP BY role");
$users = [];
while ($row = $res->fetch_assoc()) {
    $users[$row['role']] = $row['count'];
}
$stats['users'] = $users;

// Bookings pipeline
$res = $conn->query("SELECT status, COUNT(id) as count FROM bookings GROUP BY status");
$bookings = [];
while ($row = $res->fetch_assoc()) {
    $bookings[$row['status']] = $row['count'];
}
$stats['bookings_pipeline'] = $bookings;

// Revenue estimate (10% of completed bookings price)
$res = $conn->query("
    SELECT SUM(s.price) as total_value 
    FROM bookings b 
    JOIN services s ON b.service_id = s.id 
    WHERE b.status = 'completed'
");
$row = $res->fetch_assoc();
$total_value = $row['total_value'] ? (float)$row['total_value'] : 0;

$stats['revenue'] = [
    "total_transaction_value" => $total_value,
    "platform_revenue_10_percent" => $total_value * 0.10
];

echo json_encode(["status" => "success", "data" => $stats]);
?>
