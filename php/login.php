<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        die("Username and password are required.");
    }

    // Check user credentials
    $stmt = $conn->prepare("SELECT id, username, password, role, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashed_password, $role, $status);
        $stmt->fetch();

        // Check if the account is locked
        if ($status === 'locked') {
            echo "Your account is locked. Please try again with another account.";
            exit();
        }

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            
            // Set appropriate sidebar and redirect based on role
            if ($role === 'admin') {
                $_SESSION['sidebar'] = 'sidebar_admin.php';
                header("refresh:0; url=../admin_console.php");
            } else {
                $_SESSION['sidebar'] = 'sidebar.php';
                header("refresh:0; url=../dashboard.php");
            }
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No account found with this username.";
    }

    $stmt->close();
    $conn->close();
}
?>
