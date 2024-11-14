<?php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit;
}

$notification = "";

// new slot
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_slot'])) {
    $slot_number = $_POST["slot_number"];
    $floor = $_POST["floor"];
    $section = $_POST["section"];
    $is_occupied = 0;

    $stmt = $conn->prepare("INSERT INTO ParkingSlots (slot_number, floor, section, is_occupied) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $slot_number, $floor, $section, $is_occupied);

    if ($stmt->execute()) {
        $notification = "Slot added successfully!";
    } else {
        $notification = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// slot edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_slot'])) {
    $slot_id = $_POST["slot_id"];
    $slot_number = $_POST["slot_number"];
    $floor = $_POST["floor"];
    $section = $_POST["section"];
    $is_occupied = isset($_POST["is_occupied"]) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE ParkingSlots SET slot_number = ?, floor = ?, section = ?, is_occupied = ? WHERE id = ?");
    $stmt->bind_param("sssii", $slot_number, $floor, $section, $is_occupied, $slot_id);

    if ($stmt->execute()) {
        $notification = "Slot updated successfully!";
    } else {
        $notification = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Slot delete
if (isset($_GET['delete_slot_id'])) {
    $slot_id = intval($_GET['delete_slot_id']);
    $stmt = $conn->prepare("DELETE FROM ParkingSlots WHERE id = ?");
    $stmt->bind_param("i", $slot_id);

    if ($stmt->execute()) {
        $notification = "Slot deleted successfully!";
    } else {
        $notification = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch slot
$slots_result = $conn->query("SELECT * FROM ParkingSlots");
$slots = [];
if ($slots_result && $slots_result->num_rows > 0) {
    while ($slot = $slots_result->fetch_assoc()) {
        $slots[$slot['floor']][] = $slot;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Parking Slots</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href=".//assets//css//output.css">
    <script>
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white py-2 px-4 rounded-lg shadow-lg';
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" bg-blue-500", " text-blue-500");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className = evt.currentTarget.className.replace(" text-blue-500", " bg-blue-500");
        }

        function showEditForm(slot) {
            document.getElementById('edit_slot_id').value = slot.id;
            document.getElementById('edit_slot_number').value = slot.slot_number;
            document.getElementById('edit_floor').value = slot.floor;
            document.getElementById('edit_section').value = slot.section;
            document.getElementById('edit_is_occupied').checked = slot.is_occupied;
            document.getElementById('edit_city').value = slot.city;
            document.getElementById('edit_mall').value = slot.mall;
            document.getElementById('editForm').style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.tablinks').click();
        });
    </script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Left side: Logo -->
            <a href="../index.php">
                <img src="../assets//img//parkiir_logo.png" alt="Logo" class="mx-10 w-36">
            </a>

            <!-- Middle side: Navbar -->
            <div class="flex items-center mx-auto flex justify-center space-x-6">
                <a href="../index.php" class="text-white font-semibold hover:text-blue-200">Home</a>
                <a href="admin_index.php" class="text-white font-semibold hover:text-blue-200">Manage Slots</a>
            </div>

            <!-- Right side: Greeting and Logout -->
            <div class="flex items-center space-x-4">
                <?php 
                $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
                ?>
                <span class="text-white font-semibold">Hello, <?php echo htmlspecialchars($username); ?>!</span>
                <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mx-auto max-w-2xl p-4">
        <h1 class="text-2xl font-bold mb-4">Manage Parking Slots</h1>

        <!-- form slot -->
        <form action="admin_index.php" method="post" class="mb-6">
            <div class="mb-4">
                <label for="slot_number" class="block text-gray-700">Slot Number</label>
                <input type="text" name="slot_number" id="slot_number" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="floor" class="block text-gray-700">Floor</label>
                <select name="floor" id="floor" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="GF">GF</option>
                    <option value="1F">1F</option>
                    <option value="2F">2F</option>
                    <option value="3F">3F</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="section" class="block text-gray-700">Section</label>
                <select name="section" id="section" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <div>
                <input type="submit" name="add_slot" value="Add Slot" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 cursor-pointer">
            </div>
        </form>

        <!-- Edit Slot Form -->
        <form id="editForm" action="update_slot.php" method="post" class="mb-6" style="display: none;">
            <input type="hidden" name="slot_id" id="edit_slot_id">
            <div class="mb-4">
                <label for="edit_slot_number" class="block text-gray-700">Slot Number</label>
                <input type="text" name="slot_number" id="edit_slot_number" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="edit_floor" class="block text-gray-700">Floor</label>
                <select name="floor" id="edit_floor" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="GF">GF</option>
                    <option value="1F">1F</option>
                    <option value="2F">2F</option>
                    <option value="3F">3F</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="edit_section" class="block text-gray-700">Section</label>
                <select name="section" id="edit_section" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="edit_is_occupied" class="block text-gray-700">Occupied</label>
                <input type="checkbox" name="is_occupied" id="edit_is_occupied" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="edit_city" class="block text-gray-700">City</label>
                <select name="city" id="edit_city" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="City1">City1</option>
                    <option value="City2">City2</option>
                    <option value="City3">City3</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="edit_mall" class="block text-gray-700">Mall</label>
                <select name="mall" id="edit_mall" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Mall1">Mall1</option>
                    <option value="Mall2">Mall2</option>
                    <option value="Mall3">Mall3</option>
                </select>
            </div>
            <div>
                <input type="submit" name="edit_slot" value="Update Slot" class="w-full bg-yellow-500 text-white py-2 rounded-lg hover:bg-yellow-600 cursor-pointer">
            </div>
        </form>

        <!-- Tabs for Floors -->
        <div class="mb-4">
            <button class="tablinks text-blue-500 py-2 px-4 rounded-lg" onclick="openTab(event, 'GF')">GF</button>
            <button class="tablinks text-blue-500 py-2 px-4 rounded-lg" onclick="openTab(event, '1F')">1F</button>
            <button class="tablinks text-blue-500 py-2 px-4 rounded-lg" onclick="openTab(event, '2F')">2F</button>
            <button class="tablinks text-blue-500 py-2 px-4 rounded-lg" onclick="openTab(event, '3F')">3F</button>
        </div>

        <?php foreach (['GF', '1F', '2F', '3F'] as $floor): ?>
            <div id="<?php echo $floor; ?>" class="tabcontent" style="display: none;">
                <h2 class="text-xl font-bold mb-4"><?php echo $floor; ?> Slots</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slot Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (isset($slots[$floor])): ?>
                            <?php foreach ($slots[$floor] as $slot): ?>
                                <?php $status = $slot['is_occupied'] ? 'Occupied' : 'Available'; ?>
                                <tr>
                                    <td class='px-6 py-4 text-gray-700 font-medium'><?php echo $slot['slot_number']; ?></td>
                                    <td class='px-6 py-4 text-gray-700'><?php echo $slot['section']; ?></td>
                                    <td class='px-6 py-4 text-gray-700'><?php echo $status; ?></td>
                                    <td class='px-6 py-4'>
                                        <button onclick='showEditForm(<?php echo json_encode($slot); ?>)' class='bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg'>Edit</button>
                                        <a href='admin_index.php?delete_slot_id=<?php echo $slot['id']; ?>' class='bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg'>Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan='4' class='px-6 py-4 text-center text-gray-500'>No slots available</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-600 p-4 mt-8">
        <div class="max-w-7xl mx-auto text-center text-white">
            &copy; <?php echo date("Y"); ?> Parkiir. All rights reserved.
        </div>
    </footer>

    <?php if (!empty($notification)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification('<?php echo $notification; ?>');
            });
        </script>
    <?php endif; ?>
</body>
</html>
