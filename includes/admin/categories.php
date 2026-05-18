<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Create uploads directory if it doesn't exist
$uploads_dir = dirname(__DIR__) . '/assets/images/categories';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

if (!isset($_SESSION['admin_categories'])) {
    $_SESSION['admin_categories'] = [
        ['id' => 1, 'name' => 'Butcher', 'products' => 12, 'image' => ''],
        ['id' => 2, 'name' => 'Greengrocer', 'products' => 28, 'image' => ''],
        ['id' => 3, 'name' => 'Delicatessen', 'products' => 15, 'image' => ''],
        ['id' => 4, 'name' => 'Fishmonger', 'products' => 18, 'image' => ''],
        ['id' => 5, 'name' => 'Bakery', 'products' => 16, 'image' => ''],
    ];
}

$categories = &$_SESSION['admin_categories'];
$message = '';
$error = '';
$action = $_GET['action'] ?? '';
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = null;

if ($action === 'edit') {
    foreach ($categories as $cat) {
        if ($cat['id'] === $category_id) {
            $category = $cat;
            break;
        }
    }
    if (!$category) {
        header('Location: categories.php?message=' . urlencode('Category not found.'));
        exit;
    }
}

if ($action === 'delete' && $category_id) {
    foreach ($categories as $index => $cat) {
        if ($cat['id'] === $category_id) {
            unset($categories[$index]);
            $_SESSION['admin_categories'] = array_values($categories);
            header('Location: categories.php?message=' . urlencode('Category deleted successfully.'));
            exit;
        }
    }
    header('Location: categories.php?message=' . urlencode('Category not found.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $current_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $image_name = '';

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        foreach ($categories as $cat) {
            if (strcasecmp($cat['name'], $name) === 0 && $cat['id'] !== $current_id) {
                $error = 'A category with that name already exists.';
                break;
            }
        }
    }

    // Handle image upload
    if (!$error && isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Error uploading file.';
        } else {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowed_types)) {
                $error = 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.';
            } elseif ($file['size'] > $max_size) {
                $error = 'File size exceeds 5MB limit.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $image_name = 'category_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = $uploads_dir . '/' . $image_name;
                
                if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $error = 'Failed to save image file.';
                }
            }
        }
    }

    if (!$error) {
        if ($current_id) {
            foreach ($categories as &$cat) {
                if ($cat['id'] === $current_id) {
                    $cat['name'] = $name;
                    // Only update image if a new one was uploaded
                    if ($image_name) {
                        // Delete old image if it exists
                        if (!empty($cat['image'])) {
                            $old_path = $uploads_dir . '/' . $cat['image'];
                            if (file_exists($old_path)) {
                                unlink($old_path);
                            }
                        }
                        $cat['image'] = $image_name;
                    }
                    break;
                }
            }
            unset($cat);
            $message = 'Category updated successfully.';
        } else {
            $new_id = 1;
            foreach ($categories as $cat) {
                if ($cat['id'] >= $new_id) {
                    $new_id = $cat['id'] + 1;
                }
            }
            $categories[] = ['id' => $new_id, 'name' => $name, 'products' => 0, 'image' => $image_name];
            $message = 'Category added successfully.';
        }

        $_SESSION['admin_categories'] = $categories;
        header('Location: categories.php?message=' . urlencode($message));
        exit;
    }
}

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo"><a href="index.php" style="text-decoration:none; color: inherit;"><img src="../assets/images/logo.png" alt="CleckBasket" style="height: 40px; width: auto;"></a></div>
            <nav class="nav-menu">
                <a href="index.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
                <a href="products.php" class="nav-item"><i class="fas fa-box"></i><span>Products</span></a>
                <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
                <a href="customers.php" class="nav-item"><i class="fas fa-users"></i><span>Customers</span></a>
                <a href="categories.php" class="nav-item active"><i class="fas fa-tags"></i><span>Categories</span></a>
                <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
                <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
                <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Categories</h1>
            </header>

            <div class="content">
                <?php if ($message): ?>
                    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="page-header">
                    <h2>Category Management</h2>
                    <a href="categories.php?action=add" class="btn-primary"><i class="fas fa-plus"></i> Add Category</a>
                </div>

                <?php if ($action === 'add' || $action === 'edit'): ?>
                    <div class="form-container">
                        <h3><?php echo $action === 'edit' ? 'Edit Category' : 'Add Category'; ?></h3>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id'] ?? ''); ?>">
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Category Image</label>
                                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                                <small>Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB</small>
                                <?php if ($action === 'edit' && !empty($category['image'])): ?>
                                    <div class="current-image">
                                        <p>Current Image:</p>
                                        <img src="<?php echo htmlspecialchars('/../assets/images/categories/' . $category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-buttons">
                                <button type="submit" class="btn-primary">
                                    <?php echo $action === 'edit' ? 'Update Category' : 'Create Category'; ?>
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
                                <th>Image</th>
                                <th>Category Name</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="image-cell">
                                    <?php if (!empty($cat['image'])): ?>
                                        <img src="<?php echo htmlspecialchars('/../assets/images/categories/' . $cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" style="max-width: 50px; max-height: 50px; border-radius: 4px;">
                                    <?php else: ?>
                                        <span style="color: #999;">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td><?php echo $cat['products']; ?></td>
                                <td class="actions">
                                    <a href="categories.php?action=edit&id=<?php echo $cat['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="categories.php?action=delete&id=<?php echo $cat['id']; ?>" class="btn-delete" onclick="return confirm('Delete this category?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
