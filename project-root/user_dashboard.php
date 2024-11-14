<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT Bookings.id AS booking_id, ParkingSlots.slot_number, Bookings.booking_date, Bookings.booking_time, Bookings.status 
          FROM Bookings 
          JOIN ParkingSlots ON Bookings.slot_id = ParkingSlots.id 
          WHERE Bookings.user_id = $user_id";
$result = $conn->query($query);

$slot_id = isset($_GET['slot_id']) ? $_GET['slot_id'] : '';
$vehicle_type = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : '';
$slot_number = isset($_GET['slot_number']) ? $_GET['slot_number'] : '';
$floor = isset($_GET['floor']) ? $_GET['floor'] : '';
$section = isset($_GET['section']) ? $_GET['section'] : '';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$contact = isset($_SESSION['contact']) ? $_SESSION['contact'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href=".//assets//css//output.css">
    <?php include 'includes/header.php'; ?>
</head>
<body class="bg-gray-100">
    <h2>Your Bookings</h2>
    <table border="1">
        <tr>
            <th>Booking ID</th>
            <th>Slot Number</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['booking_id']}</td>
                <td>{$row['slot_number']}</td>
                <td>{$row['booking_date']}</td>
                <td>{$row['booking_time']}</td>
                <td>{$row['status']}</td>";
            
            if ($row['status'] == 'active') {
                echo "<td><a href='cancel_booking.php?booking_id={$row['booking_id']}'>Cancel</a></td>";
            } else {
                echo "<td>-</td>";
            }

            echo "</tr>";
        }
        ?>
    </table>

    <?php if ($slot_id && $vehicle_type && $slot_number && $floor && $section): ?>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Confirm Your Booking</h2>
                <button onclick="document.getElementById('booking-popup').style.display='none'" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div class="mb-4">
                <p><strong>Slot Number:</strong> <?php echo htmlspecialchars($slot_number); ?></p>
                <p><strong>Floor:</strong> <?php echo htmlspecialchars($floor); ?></p>
                <p><strong>Section:</strong> <?php echo htmlspecialchars($section); ?></p>
                <p><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($vehicle_type); ?></p>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700">Date</label>
                <input type="date" id="date" name="date" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="time" class="block text-gray-700">Time</label>
                <input type="time" id="time" name="time" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="duration" class="block text-gray-700">Duration (hours)</label>
                <input type="number" id="duration" name="duration" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="payment_method" class="block text-gray-700">Payment Method</label>
                <select id="payment_method" name="payment_method" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <div class="flex justify-end space-x-4">
                <button onclick="document.getElementById('booking-popup').style.display='none'" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">Cancel</button>
                <button onclick="confirmBooking()" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">Confirm Booking</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function confirmBooking() {
            alert('Booking confirmed!');
            // confirmation page
        }
    </script>
</body>
</html>
