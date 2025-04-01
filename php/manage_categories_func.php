<?php
include './php/connect.php';
include './php/auth_check.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle category operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = trim($_POST['category_name']);
                $color = $_POST['color'];
                
                if (!empty($name)) {
                    try {
                        $stmt = $conn->prepare("INSERT INTO categories (user_id, category_name, color) VALUES (?, ?, ?)");
                        $stmt->bind_param("iss", $user_id, $name, $color);
                        $stmt->execute();
                        $message = "Category added successfully!";
                    } catch (mysqli_sql_exception $e) {
                        if ($e->getCode() == 1062) { // Duplicate entry error
                            $error = "A category with this name already exists.";
                        } else {
                            $error = "Error adding category: " . $e->getMessage();
                        }
                    }
                }
                break;

            case 'update':
                $id = $_POST['category_id'];
                $name = trim($_POST['category_name']);
                $color = $_POST['color'];
                
                if (!empty($name)) {
                    try {
                        $stmt = $conn->prepare("UPDATE categories SET category_name = ?, color = ? WHERE id = ? AND user_id = ?");
                        $stmt->bind_param("ssii", $name, $color, $id, $user_id);
                        $stmt->execute();
                        $message = "Category updated successfully!";
                    } catch (mysqli_sql_exception $e) {
                        if ($e->getCode() == 1062) {
                            $error = "A category with this name already exists.";
                        } else {
                            $error = "Error updating category: " . $e->getMessage();
                        }
                    }
                }
                break;

            case 'delete':
                $id = $_POST['category_id'];
                try {
                    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
                    $stmt->bind_param("ii", $id, $user_id);
                    $stmt->execute();
                    $message = "Category deleted successfully!";
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() == 1451) { // Foreign key constraint error
                        $error = "Cannot delete category: It is being used by existing tasks.";
                    } else if ($e->getSQLState() == '45000') { // Custom error from trigger
                        $error = "Cannot delete the last remaining category.";
                    } else {
                        $error = "Error deleting category: " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Fetch all categories for the user
$stmt = $conn->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY category_name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$categories = $stmt->get_result();
?>