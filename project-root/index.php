<?php
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Vehicle Type</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="./assets/css/output.css">
</head>

<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-blue-700 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo and Navigation -->
            <a href="index.php" class="flex items-center">
                <img src="./assets/img/parkiir_logo.png" alt="Logo" class="w-32 h-auto">
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-8">
                <a href="index.php" class="text-white font-medium hover:text-blue-200">Home</a>
                <a href="book_slotPage.php" class="text-white font-medium hover:text-blue-200">Book a Slot</a>
                <a href="history.php" class="text-white font-medium hover:text-blue-200">History</a>
                <a href="contact.php" class="text-white font-medium hover:text-blue-200">Contact</a>
            </div>

            <!-- Right side: Greeting and Logout -->
            <div class="flex items-center space-x-4">
                <?php
                // Fetch username
                $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
                ?>
                <span class="text-white font-semibold">Hello, <?php echo htmlspecialchars($username); ?>!</span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow w-96 mx-auto bg-white shadow-lg rounded-lg mt-10 mb-10 p-8">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Select Vehicle</h2>
        <form method="GET" action="available_slots.php">
            <div class="mb-4">
                <label for="vehicle_type" class="block text-gray-700 font-medium mb-2">Vehicle Type</label>
                <select name="vehicle_type" id="vehicle_type" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled selected>Select a vehicle type</option>
                    <option value="Car">Car</option>
                    <option value="Motorcycle">Motorcycle</option>
                    <option value="EV">EV</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-200">Submit</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-700 p-4 mt-auto text-center text-white">
        &copy; <?php echo date("Y"); ?> Parkiir. All rights reserved.
    </footer>
</body>

</html>