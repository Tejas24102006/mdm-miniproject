<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

$query = "
    SELECT b.id, b.customer_id, b.provider_id, b.service_id, b.booking_date, b.status, b.created_at,
           s.service_name, s.price, s.location,
           u_cust.name as customer_name,
           u_prov.name as provider_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u_cust ON b.customer_id = u_cust.id
    JOIN users u_prov ON b.provider_id = u_prov.id
";

// Optional filters
$conditions = [];
$params = [];
$types = "";

if (isset($_GET['customer_id'])) {
    $conditions[] = "b.customer_id = ?";
    $params[] = (int)$_GET['customer_id'];
    $types .= "i";
}

if (isset($_GET['provider_id'])) {
    $conditions[] = "b.provider_id = ?";
    $params[] = (int)$_GET['provider_id'];
    $types .= "i";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode(["status" => "success", "data" => $bookings]);

$stmt->close();
?>
