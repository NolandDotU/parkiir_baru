<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $vehicle_type = $_POST["vehicle_type"];
    $is_admin = isset($_POST["is_admin"]) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO Users (username, password, email, phone_number, vehicle_type, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $username, $password, $email, $phone_number, $vehicle_type, $is_admin);

    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: loginPage.php");
    } else {
        echo "Error: ". $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>