<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ceck user
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            if ($user['is_admin']) {
                header("Location: admin/admin_index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            echo "<p class='text-red-500'>username or password invalid</p>";
        }
    } else {
        echo "<p class='text-red-500'>user not foundd</p>";
    }
    
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
