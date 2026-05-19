<?php
require_once 'admin_auth.php';

$message = '';
$error   = '';
$action  = $_GET['action'] ?? '';
$catId   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editCat = null;

// Handle POST: add or update category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $currentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        $conn = getDBConnection();
        if ($conn) {
            if ($currentId) {
                $upd = oci_parse($conn, "UPDATE product_category SET category_type = :name WHERE product_category_id = :id");
                oci_bind_by_name($upd, ':name', $name);
                oci_bind_by_name($upd, ':id',   $currentId);
                if (oci_execute($upd)) { oci_commit($conn); $message = 'Category updated successfully.'; }
                else { $err = oci_error($upd); $error = $err['message']; }
                oci_free_statement($upd);
            } else {
                $ins = oci_parse($conn, "INSERT INTO product_category (category_type) VALUES (:name)");
                oci_bind_by_name($ins, ':name', $name);
                if (oci_execute($ins)) { oci_commit($conn); $message = 'Category added successfully.'; }
                else { $err = oci_error($ins); $error = $err['message']; }
                oci_free_statement($ins);
            }
            oci_close($conn);
        }
        if ($message) {
            header('Location: categories.php?message=' . urlencode($message));
            exit;
        }
    }
}

// Handle delete
if ($action === 'delete' && $catId) {
    $conn = getDBConnection();
    if ($conn) {
        $del = oci_parse($conn, "DELETE FROM product_category WHERE product_category_id = :id");
        oci_bind_by_name($del, ':id', $catId);
        if (oci_execute($del)) {
            oci_commit($conn);
            $msg = 'Category deleted.';
        } else {
            $err = oci_error($del);
            $msg = 'Delete failed: ' . $err['message'];
        }
        oci_free_statement($del);
        oci_close($conn);
    }
    header('Location: categories.php?message=' . urlencode($msg ?? 'Error'));
    exit;
}

// Flash message from redirect
if (isset($_GET['message'])) $message = $_GET['message'];

// Load categories with product counts
$categories = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT pc.product_category_id, pc.category_type,
                   COUNT(p.product_id) AS product_count
            FROM product_category pc
            LEFT JOIN product p ON pc.product_category_id = p.product_category_id
            GROUP BY pc.product_category_id, pc.category_type
            ORDER BY pc.category_type";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) $categories[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
}

// Load single category for edit form
if ($action === 'edit' && $catId) {
    foreach ($categories as $c) {
        if ((int)$c['PRODUCT_CATEGORY_ID'] === $catId) { $editCat = $c; break; }
    }
    if (!$editCat) { header('Location: categories.php'); exit; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('categories'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Categories</h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background:#ffebee;color:#c62828;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($error) ?></div>
            <?php endif; ?>

            <div class="page-header">
                <h2>Category Management</h2>
                <a href="categories.php?action=add" class="btn-primary"><i class="fas fa-plus"></i> Add Category</a>
            </div>

            <?php if ($action === 'add' || $action === 'edit'): ?>
            <div class="form-container" style="margin-bottom:24px;">
                <h3><?= $action === 'edit' ? 'Edit Category' : 'Add Category' ?></h3>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $editCat ? (int)$editCat['PRODUCT_CATEGORY_ID'] : '' ?>">
                    <div class="form-group">
                        <label for="name">Category Name</label>
                        <input type="text" id="name" name="name"
                               value="<?= h($editCat['CATEGORY_TYPE'] ?? '') ?>" required>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" class="btn-primary">
                            <?= $action === 'edit' ? 'Update Category' : 'Create Category' ?>
                        </button>
                        <a href="categories.php" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Products</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="3" style="text-align:center;color:#999;">No categories found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td><?= h($c['CATEGORY_TYPE']) ?></td>
                            <td><?= (int)$c['PRODUCT_COUNT'] ?></td>
                            <td class="actions">
                                <a href="categories.php?action=edit&id=<?= (int)$c['PRODUCT_CATEGORY_ID'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ((int)$c['PRODUCT_COUNT'] === 0): ?>
                                <a href="categories.php?action=delete&id=<?= (int)$c['PRODUCT_CATEGORY_ID'] ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Delete category <?= h($c['CATEGORY_TYPE']) ?>?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <?php else: ?>
                                <span style="color:#aaa;font-size:12px;">Has products</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
