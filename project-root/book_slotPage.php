<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if slot_id is provided in the URL
if (isset($_GET['slot_id']) && !empty($_GET['slot_id'])) {
    $slot_id = $_GET['slot_id'];
    $vehicle_type = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : '';

    // Prepare a query to fetch the slot details
    $slot_query = $conn->prepare("SELECT * FROM ParkingSlots WHERE id = ?");
    $slot_query->bind_param("i", $slot_id);
    $slot_query->execute();
    $slot_result = $slot_query->get_result();

    // Check if a slot was found
    if ($slot_result && $slot_result->num_rows > 0) {
        $slot = $slot_result->fetch_assoc();
    } else {
        // Redirect to available_slots.php with an error if the slot is not found
        header("Location: available_slots.php?error=SlotNotFound&vehicle_type=" . urlencode($vehicle_type));
        exit;
    }

    $slot_query->close();
} else {
    // Redirect to available_slots.php with an error if slot_id is missing
    header("Location: available_slots.php?error=MissingSlotID");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Parking Slot</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Container -->
    <div class="min-h-screen flex items-center justify-center py-10">
        <div class="bg-white shadow-lg rounded-lg p-8 max-w-lg w-full">
            
            <!-- Booking Confirmation -->
            <h2 class="text-2xl font-bold text-blue-600 text-center mb-6">Confirm Your Booking</h2>
            
            <!-- Slot Details -->
            <div class="mb-6">
                <p class="text-lg"><strong>Slot Number:</strong> <?php echo htmlspecialchars($slot['slot_number']); ?></p>
                <p class="text-lg"><strong>Vehicle Type:</strong> <?php echo ucfirst(htmlspecialchars($vehicle_type)); ?></p>
                <p class="text-lg"><strong>Status:</strong> 
                    <?php echo $slot['is_occupied'] ? '<span class="text-red-500">Occupied</span>' : '<span class="text-green-500">Available</span>'; ?>
                </p>
            </div>

            <?php if (!$slot['is_occupied']): ?>
                <form method="GET" action="book_slot.php" class="space-y-4">
                    <!-- Hidden Inputs to Pass Slot ID -->
                    <input type="hidden" name="slot_id" value="<?php echo htmlspecialchars($slot_id); ?>">

                    <!-- Booking Button -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-300">
                        Confirm Booking
                    </button>
                </form>
            <?php else: ?>
                <div class="bg-red-500 text-white text-center py-3 rounded-lg">
                    This slot is already occupied.
                </div>
            <?php endif; ?>

            <!-- Back to Slots Button -->
            <div class="mt-6">
                <a href="available_slots.php?vehicle_type=<?php echo urlencode($vehicle_type); ?>" 
                   class="block text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg transition duration-300">
                    Back to Slots
                </a>
            </div>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>
