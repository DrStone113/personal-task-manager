<?php
include './php/manage_categories_func.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planex - Manage Categories</title>
    <link rel="icon" type="image/x-icon" href="./img/logo.png">
    <script src="./js/categories.js?v=<?= time() ?>" defer></script>
    <script src="./js/dashboard.js?v=<?= time() ?>" defer></script>
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/categories.css?v=<?php echo time(); ?>"> <!-- Include new CSS file -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<?php 
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        include './php/sidebar_admin.php';
    } else {
        include './php/sidebar.php';
    }
    ?>

    <section class="home">
        <div class="text">Manage Categories</div>
        
        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="controls">
            <div class="action-buttons">
                <button onclick="document.getElementById('addCategoryForm').style.display = 'block'" class="btn add-category">+ Add New Category</button>
            </div>
        </div>

        <div id="addCategoryForm" class="add-category" style="display: none;">
            <h3>Add New Category</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_name">Category Name:</label>
                        <input type="text" name="category_name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="color">Color:</label>
                        <input type="color" name="color" value="#808080" class="form-control">
                    </div>
                    <div class="form-group" style="flex: 0 0 auto; align-self: flex-end;">
                        <button type="submit" class="btn btn-primary">Add Category</button>
                        <button type="button" onclick="document.getElementById('addCategoryForm').style.display = 'none'" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="category-grid">
            <?php while ($category = $categories->fetch_assoc()): ?>
                <div class="category-card">
                    <div class="category-header">
                        <span class="category-name">
                            <span class="color-preview" style="background: <?= htmlspecialchars($category['color']) ?>"></span>
                            <?= htmlspecialchars($category['category_name']) ?>
                        </span>
                        <div class="category-actions">
                            <button onclick="editCategory(<?= htmlspecialchars(json_encode($category)) ?>)" 
                                    class="btn-icon btn-edit" title="Edit">
                                <i class='bx bx-edit'></i>
                            </button>
                            <form method="POST" action="" style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete this category?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                <button type="submit" class="btn-icon btn-delete" title="Delete">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Edit Category Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Category</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="form-group">
                    <label for="edit_category_name">Category Name:</label>
                    <input type="text" name="category_name" id="edit_category_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="edit_color">Color:</label>
                    <input type="color" name="color" id="edit_color" class="form-control">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
