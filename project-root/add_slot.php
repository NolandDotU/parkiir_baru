<?php
include 'db_connect.php';
session_start();

// Check if the user is an admin (you need to implement this check based on your user roles)
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slot_number = $_POST["slot_number"];
    $is_occupied = 0; // New slots are not occupied by default

    // Insert the new slot into the database
    $stmt = $conn->prepare("INSERT INTO ParkingSlots (slot_number, is_occupied) VALUES (?, ?)");
    $stmt->bind_param("si", $slot_number, $is_occupied);

    if ($stmt->execute()) {
        echo "Slot added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Slot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href=".//assets//css//output.css">
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Left side: Logo -->
            <a href="index.php">
                <img src="./assets//img//parkiir_logo.png" alt="Logo" class="mx-10 w-36">
            </a>

            <!-- Middle side: Navigation Links -->
            <div class="flex items-center mx-auto flex justify-center space-x-6">
                <a href="index.php" class="text-white font-semibold hover:text-blue-200">Home</a>
                <a href="book_slot.php" class="text-white font-semibold hover:text-blue-200">Book a Slot</a>
                <a href="history.php" class="text-white font-semibold hover:text-blue-200">History</a>
                <a href="contact.php" class="text-white font-semibold hover:text-blue-200">Contact</a>
            </div>

            <!-- Right side: Greeting and Logout -->
            <div class="flex items-center space-x-4">
                <?php 
                // Fetch username from session
                $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
                ?>
                <span class="text-white font-semibold">Hello, <?php echo htmlspecialchars($username); ?>!</span>
                <?php if ($username !== 'Guest'): ?>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg">Logout</a>
                <?php else: ?>
                    <a href="loginPage.php" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Add New Slot</h1>
        <form action="add_slot.php" method="post">
            <div class="mb-4">
                <label for="slot_number" class="block text-gray-700">Slot Number</label>
                <input type="text" name="slot_number" id="slot_number" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <input type="submit" value="Add Slot" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 cursor-pointer">
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-600 p-4 mt-8">
        <div class="max-w-7xl mx-auto text-center text-white">
            &copy; <?php echo date("Y"); ?> Parkiir. All rights reserved.
        </div>
    </footer>
</body>
</html>