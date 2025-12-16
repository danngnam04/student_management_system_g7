
<?php
session_start();
require_once 'db.php'; 

$current_role = $_SESSION['user_role'] ?? 'student';

if (!isset($_SESSION['user_id']) || $current_role !== 'admin') {

    die("Biến đi! Chỉ Hiệu trưởng (Admin) mới được vào đây. (Quyền hiện tại của bạn là: " . $current_role . ")");
}

// 2. Xử lý khi bấm nút "Đăng thông báo"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $location = $_POST['location'];
    $start_time = $_POST['start_time'];

    $sql = "INSERT INTO announcements (title, content, location, start_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $title, $content, $location, $start_time);
    
    if ($stmt->execute()) {
        // Đăng xong quay về trang chủ
        echo "<script>alert('Đã đăng thông báo thành công!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Thông Báo Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="card shadow col-md-6 mx-auto">
        <div class="card-header bg-primary text-white">
            <h4>📢 Tạo Thông Báo Mới</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Tiêu đề:</label>
                    <input type="text" name="title" class="form-control" required placeholder="VD: Thông báo nghỉ lễ">
                </div>
                
                <div class="mb-3">
                    <label>Thời gian bắt đầu:</label>
                    <input type="datetime-local" name="start_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Địa điểm:</label>
                    <input type="text" name="location" class="form-control" placeholder="VD: Sân trường / Online">
                </div>

                <div class="mb-3">
                    <label>Nội dung (Note):</label>
                    <textarea name="content" class="form-control" rows="4" required placeholder="Chi tiết..."></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">Đăng ngay</button>
                <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Quay lại</a>
            </form>
        </div>
    </div>
</body>
</html>