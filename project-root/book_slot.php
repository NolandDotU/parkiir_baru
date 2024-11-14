<?php
include 'db_connect.php';
session_start();

if (isset($_GET['slot_id']) && isset($_SESSION['user_id'])) {
    $slot_id = $_GET['slot_id'];
    $user_id = $_SESSION['user_id'];
    $booking_date = date('Y-m-d');
    $booking_time = date('H:i:s');
    $status = 'active';

    // Insert booking ke database Bookings table 
    $stmt = $conn->prepare("INSERT INTO Bookings (user_id, slot_id, booking_date, booking_time, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $slot_id, $booking_date, $booking_time, $status);

    if ($stmt->execute()) {
        // update occupied
        $update_stmt = $conn->prepare("UPDATE ParkingSlots SET is_occupied = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $slot_id);
        $update_stmt->execute();
        
        echo "Booking successful!";
    } else {
        echo "Booking failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: user_dashboard.php");
    exit;
} else {
    echo "Error: Missing slot ID or user session.";
}
?>
