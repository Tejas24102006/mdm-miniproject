<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';

$query = "
    SELECT s.id, s.service_name, s.price, s.location, s.description, s.created_at, 
           u.id as provider_id, u.name as provider_name,
           (SELECT AVG(rating) FROM reviews r JOIN bookings b ON r.booking_id = b.id WHERE b.service_id = s.id) as avg_rating,
           (SELECT COUNT(*) FROM reviews r JOIN bookings b ON r.booking_id = b.id WHERE b.service_id = s.id) as review_count
    FROM services s
    JOIN users u ON s.provider_id = u.id
";

// Optional filters
$conditions = [];
$params = [];
$types = "";

if (isset($_GET['location']) && !empty($_GET['location'])) {
    $conditions[] = "s.location LIKE ?";
    $params[] = "%" . trim($_GET['location']) . "%";
    $types .= "s";
}

if (isset($_GET['provider_id'])) {
    $conditions[] = "s.provider_id = ?";
    $params[] = (int)$_GET['provider_id'];
    $types .= "i";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $conditions[] = "(s.service_name LIKE ? OR s.description LIKE ?)";
    $searchString = "%" . trim($_GET['search']) . "%";
    $params[] = $searchString;
    $params[] = $searchString;
    $types .= "ss";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY s.created_at DESC";

$stmt = $conn->prepare($query);

// Dynamically bind params if they exist
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$services = [];
while ($row = $result->fetch_assoc()) {
    $row['avg_rating'] = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
    $services[] = $row;
}

echo json_encode(["status" => "success", "data" => $services]);

$stmt->close();
?>
