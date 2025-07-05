<?php
include 'connect.php';

// Function to fetch all users
function fetch_users($conn) {
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    return $stmt->get_result();
}

// Function to add a new user
function add_user($conn, $username, $password, $role) {
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, password_hash($password, PASSWORD_DEFAULT), $role);
    return $stmt->execute();
}

// Function to edit a user
function edit_user($conn, $id, $username, $role) {
    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $role, $id);
    return $stmt->execute();
}

// Function to delete a user
function delete_user($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Function to lock/unlock a user account
function toggle_user_status($conn, $id, $status) {
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

// Handle form submissions for adding, editing, deleting, and locking users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        // Add user logic
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        add_user($conn, $username, $password, $role);
    } elseif (isset($_POST['edit_user'])) {
        // Edit user logic
        $id = $_POST['id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        edit_user($conn, $id, $username, $role);
    }
}

// Handle delete and toggle actions
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'delete') {
        delete_user($conn, $id);
        // Redirect back to admin_console.php with a success message
        header("Location: ../admin_console.php?message=User deleted successfully");
        exit();
    } elseif ($_GET['action'] === 'toggle') {
        $current_status = $_GET['current_status'];
        $new_status = $current_status === 'active' ? 'locked' : 'active';
        toggle_user_status($conn, $id, $new_status);
        header("Location: ../admin_console.php?message=User deleted successfully");
        exit();
    }
}

$users = fetch_users($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['status']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>">Edit</a>
                        <a href="php/manage_users.php?id=<?= $user['id'] ?>&action=delete" onclick="return confirm('Are you sure?')">Delete</a>
                        <a href="php/manage_users.php?id=<?= $user['id'] ?>&action=toggle&current_status=<?= $user['status'] ?>"><?= $user['status'] === 'active' ? 'Lock' : 'Unlock' ?></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
