<?php
// student-create.php 
session_start();
// Security Check: Only Admin can perform this action
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['user_role'] !== 'admin') {
    die(" <strong>ACCESS DENIED:</strong> You do not have permission to perform this action. <a href='dashboard.php'>Back to Dashboard</a>");
}
include 'db.php';
// upload
function upload_photo($file_key, $target_dir = 'uploads/') {
    // check file existence
    if (!isset($_FILES[$file_key]) || empty($_FILES[$file_key]['name']) || $_FILES[$file_key]['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // no file upload
    }

    // check other upload status
    if ($_FILES[$file_key]['error'] != UPLOAD_ERR_OK) {
        die("File Upload Error: Có lỗi khi tải file, mã lỗi: " . $_FILES[$file_key]['error']);
    }

    $file = $_FILES[$file_key];
    $file_tmp = $file['tmp_name'];
    $file_name = $file['name'];
    $file_size = $file['size'];

    // check size, example : 2mb
    $max_size = 2 * 1024 * 1024; // 2MB
    if ($file_size > $max_size) {
        die("File Upload Error: File quá lớn. Kích thước tối đa là 2MB.");
    }

    // check file type (Exe? png?)
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        die("File Upload Error: Loại file không hợp lệ. Chỉ chấp nhận file JPG, JPEG, PNG, GIF.");
    }

    // create unique file and move
    $unique_name = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $target_dir . $unique_name;
    
    if (move_uploaded_file($file_tmp, $target_path)) {
        return $target_path;
    } else {
        die("File Upload Error: Không thể di chuyển file đã upload.");
    }
}
// upload ending

// logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //get data
    $class_id = (int)$_POST['class_id'];
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    //upload student photo
    $student_photo_path = upload_photo('student_photo', 'uploads/');

    // save into database
    $stmt = $conn->prepare("INSERT INTO students (class_id, full_name, dob, gender, phone, address, student_photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $class_id, $full_name, $dob, $gender, $phone, $address, $student_photo_path);
    
    if ($stmt->execute()) {
        // success, direct into page
        header('Location: student_list.php?class_id=' . $class_id);
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    exit; // end script
}

// get request form
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// get class_id
if (!isset($_GET['class_id'])) {
    die("Error: Class ID is missing.");
}
$class_id = (int)$_GET['class_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Student</title>
    <link rel="stylesheet" href="style_simple.css">
</head>
<body>
    <div class="form-box" style="width: 450px;">
        <h2>Add New Student</h2>
        
        <form action="" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">

            <div class="input-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="input-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob">
            </div>

            <div class="input-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" class="input-select">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="input-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone">
            </div>

            <div class="input-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address">
            </div>

            <div class="input-group">
                <label for="student_photo">Student's Photo:</label>
                <input type="file" id="student_photo" name="student_photo" accept="image/*">
            </div>

            <button type="submit" class="btn">Save Student</button>
            <a href="student_list.php?class_id=<?php echo htmlspecialchars($class_id); ?>" class="btn-back" style="display: block; text-align: center; margin-top: 1rem;">Back to Class List</a>
        </form>
    </div>
</body>
</html>
