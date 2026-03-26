<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';

$query = "
    SELECT r.id, r.booking_id, r.customer_id, r.provider_id, r.rating, r.comment, r.created_at,
           u_cust.name as customer_name,
           u_prov.name as provider_name,
           s.service_name
    FROM reviews r
    JOIN users u_cust ON r.customer_id = u_cust.id
    JOIN users u_prov ON r.provider_id = u_prov.id
    JOIN bookings b ON r.booking_id = b.id
    JOIN services s ON b.service_id = s.id
";

// Optional filters
$conditions = [];
$params = [];
$types = "";

if (isset($_GET['provider_id'])) {
    $conditions[] = "r.provider_id = ?";
    $params[] = (int)$_GET['provider_id'];
    $types .= "i";
}

if (isset($_GET['service_id'])) {
    $conditions[] = "b.service_id = ?";
    $params[] = (int)$_GET['service_id'];
    $types .= "i";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Optionally sort by latest or highest rating
$sort = "r.created_at DESC";
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'rating_desc') {
        $sort = "r.rating DESC";
    } elseif ($_GET['sort'] === 'rating_asc') {
        $sort = "r.rating ASC";
    }
}

$query .= " ORDER BY " . $sort;

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode(["status" => "success", "data" => $reviews]);

$stmt->close();
?>
