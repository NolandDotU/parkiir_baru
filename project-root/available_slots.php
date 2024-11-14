<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: loginPage.php");
    exit;
}

// moethod GET vehcil type
$vehicle_type = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : '';

// Cek slot based type vehicle
$query = "SELECT * FROM ParkingSlots WHERE vehicle_type = ? AND is_occupied = 0";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $vehicle_type);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Slots</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href=".//assets//css//output.css">
    <script>
        function openFloor(evt, floorName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" bg-blue-500", " text-black");
            }
            document.getElementById(floorName).style.display = "block";
            evt.currentTarget.className += " bg-blue-500", "text-white";
        }
    </script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-blue-600 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Left side: Logo -->
            <a href="index.php">
                <img src="./assets//img//parkiir_logo.png" alt="Logo" class="mx-10 w-36">
            </a>
            <!-- Middle side: Navbar -->
            <div class="flex items-center mx-auto flex justify-center space-x-6">
                <a href="index.php" class="text-white font-semibold hover:text-blue-200">Home</a>
                <a href="book_slot.php" class="text-white font-semibold hover:text-blue-200">Book a Slot</a>
                <a href="history.php" class="text-white font-semibold hover:text-blue-200">History</a>
                <a href="contact.php" class="text-white font-semibold hover:text-blue-200">Contact</a>
            </div>
            <!-- Right side: Greeting and Logout -->
            <div class="flex items-center space-x-4">
                <?php 
                
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
    <div class="container mx-auto max-w-2xl p-4">
        <h1 class="text-2xl font-bold mb-4">Available Slots</h1>

        <!-- Floor tabs -->
        <div class="mb-4">
            <button class="tablinks text-black hover:bg-blue-500 hover:text-white px-4 py-2 rounded-lg transition" onclick="openFloor(event, 'GF')">Ground Floor</button>
            <button class="tablinks text-black hover:bg-blue-500 hover:text-white px-4 py-2 rounded-lg transition" onclick="openFloor(event, '1F')">First Floor</button>
            <button class="tablinks text-black hover:bg-blue-500 hover:text-white px-4 py-2 rounded-lg transition" onclick="openFloor(event, '2F')">Second Floor</button>
            <button class="tablinks text-black hover:bg-blue-500 hover:text-white px-4 py-2 rounded-lg transition" onclick="openFloor(event, '3F')">Third Floor</button>
        </div>

        <!-- Floor content -->
        <div id="GF" class="tabcontent" style="display: block;">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Slot Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Floor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Section
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vehicle Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($row['floor'] === 'GF') {
                                $status = $row['is_occupied'] ? 'Occupied' : 'Available';
                                echo "<tr>
                                    <td class='px-6 py-4 text-gray-700 font-medium'>{$row['slot_number']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['floor']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['section']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['vehicle_type']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$status}</td>";

                                // Show "Book" button if availbale
                                if (!$row['is_occupied']) {
                                    echo "<td class='px-6 py-4'>
                                            <a href='user_dashboard.php?slot_id={$row['id']}&vehicle_type={$vehicle_type}&slot_number={$row['slot_number']}&floor={$row['floor']}&section={$row['section']}'
                                            class='bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                                            Book
                                            </a>
                                          </td>";
                                } else {
                                    echo "<td class='px-6 py-4 text-center text-gray-500'>N/A</td>";
                                }

                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>No available slots</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="1F" class="tabcontent" style="display: none;">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Slot Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Floor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Section
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vehicle Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($row['floor'] === '1F') {
                                $status = $row['is_occupied'] ? 'Occupied' : 'Available';
                                echo "<tr>
                                    <td class='px-6 py-4 text-gray-700 font-medium'>{$row['slot_number']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['floor']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['section']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['vehicle_type']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$status}</td>";

                                // Show "Book" button only if available
                                if (!$row['is_occupied']) {
                                    echo "<td class='px-6 py-4'>
                                            <a href='user_dashboard.php?slot_id={$row['id']}&vehicle_type={$vehicle_type}&slot_number={$row['slot_number']}&floor={$row['floor']}&section={$row['section']}'
                                            class='bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                                            Book
                                            </a>
                                          </td>";
                                } else {
                                    echo "<td class='px-6 py-4 text-center text-gray-500'>N/A</td>";
                                }

                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>No available slots</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="2F" class="tabcontent" style="display: none;">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Slot Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Floor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Section
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vehicle Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($row['floor'] === '2F') {
                                $status = $row['is_occupied'] ? 'Occupied' : 'Available';
                                echo "<tr>
                                    <td class='px-6 py-4 text-gray-700 font-medium'>{$row['slot_number']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['floor']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['section']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['vehicle_type']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$status}</td>";

                                // Show "Book" button only if avaiable
                                if (!$row['is_occupied']) {
                                    echo "<td class='px-6 py-4'>
                                            <a href='user_dashboard.php?slot_id={$row['id']}&vehicle_type={$vehicle_type}&slot_number={$row['slot_number']}&floor={$row['floor']}&section={$row['section']}'
                                            class='bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                                            Book
                                            </a>
                                          </td>";
                                } else {
                                    echo "<td class='px-6 py-4 text-center text-gray-500'>N/A</td>";
                                }

                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>No available slots</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="3F" class="tabcontent" style="display: none;">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Slot Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Floor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Section
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vehicle Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($row['floor'] === '3F') {
                                $status = $row['is_occupied'] ? 'Occupied' : 'Available';
                                echo "<tr>
                                    <td class='px-6 py-4 text-gray-700 font-medium'>{$row['slot_number']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['floor']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['section']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$row['vehicle_type']}</td>
                                    <td class='px-6 py-4 text-gray-700'>{$status}</td>";

                                // Show "Book" button if available
                                if (!$row['is_occupied']) {
                                    echo "<td class='px-6 py-4'>
                                            <a href='user_dashboard.php?slot_id={$row['id']}&vehicle_type={$vehicle_type}&slot_number={$row['slot_number']}&floor={$row['floor']}&section={$row['section']}'
                                            class='bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                                            Book
                                            </a>
                                          </td>";
                                } else {
                                    echo "<td class='px-6 py-4 text-center text-gray-500'>N/A</td>";
                                }

                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>No available slots</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-600 p-4 mt-8">
        <div class="max-w-7xl mx-auto text-center text-white">
            &copy; <?php echo date("Y"); ?> Parkiir. All rights reserved.
        </div>
    </footer>
</body>
</html>
