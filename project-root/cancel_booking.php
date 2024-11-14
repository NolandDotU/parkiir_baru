<?php
include 'db_connect.php';

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // Update booking status to cancelled
    $stmt = $conn->prepare("UPDATE Bookings SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        // Make the slot available again
        $query = "SELECT slot_id FROM Bookings WHERE id = $booking_id";
        $slot_result = $conn->query($query);
        $slot = $slot_result->fetch_assoc();

        $update_slot = $conn->prepare("UPDATE ParkingSlots SET is_occupied = 0 WHERE id = ?");
        $update_slot->bind_param("i", $slot['slot_id']);
        $update_slot->execute();
        
        echo "Booking cancelled successfully.";
    } else {
        echo "Error cancelling booking.";
    }

    header("Location: user_dashboard.php");
    exit;
}
?>
