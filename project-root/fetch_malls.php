<?php
include 'db_connect.php';

$city_id = isset($_GET['city_id']) ? intval($_GET['city_id']) : 0;

if ($city_id > 0) {
    $stmt = $conn->prepare("SELECT id, name FROM Malls WHERE city_id = ?");
    $stmt->bind_param("i", $city_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $malls = [];
    while ($mall = $result->fetch_assoc()) {
        $malls[] = $mall;
    }

    echo json_encode($malls);
}
?>