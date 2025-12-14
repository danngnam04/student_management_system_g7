<?php
// edit-student.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if ($_SESSION['user_role'] !== 'admin') {
    die(" <strong>ACCESS DENIED:</strong> You do not have permission to perform this action. <a href='dashboard.php'>Back to Dashboard</a>");
}
include 'db.php';
include 'functions.php'; // file helper upload

// if not log in, back to page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // get data from FORM Post
    $student_id = (int)$_POST['student_id'];
    $class_id = (int)$_POST['class_id']; // get class id
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $old_photo_path = $_POST['old_photo_path'];
    
    $student_photo_path = $old_photo_path; // keep old photo by default

    // check new photo
    if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] == UPLOAD_ERR_OK) {
        
        $new_photo_path = upload_photo('student_photo', 'uploads/');
        
        if ($new_photo_path) {
            $student_photo_path = $new_photo_path; // use new photo
            
            // delete old photo 
            if ($old_photo_path && file_exists($old_photo_path)) {
                unlink($old_photo_path);
            }
        }
    }

    // update into database
    $stmt = $conn->prepare("UPDATE students SET full_name = ?, dob = ?, gender = ?, phone = ?, address = ?, student_photo = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $full_name, $dob, $gender, $phone, $address, $student_photo_path, $student_id);
    
    if ($stmt->execute()) {
        //after update, back to student list
        header('Location: student_list.php?class_id=' . $class_id);
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    exit; //end script
}

// show form

// get student id
if (!isset($_GET['id'])) {
    die("Error: Student ID is missing.");
}
$student_id = (int)$_GET['id'];

// get student info
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
$conn->close(); //disconnect connection

if (!$student) {
    die("Error: Student not found.");
}
// keep class id
$class_id = $student['class_id']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style_simple.css">
</head>
<body>
    <div class="form-box" style="width: 450px;">
        <h2>Edit Student: <?php echo htmlspecialchars($student['full_name']); ?></h2>
        
        <form action="" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>"> <div class="input-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            
            <div class="input-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" 
                       value="<?php echo htmlspecialchars($student['dob']); ?>">
            </div>

            <div class="input-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" class="input-select">
                    <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="input-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" 
                       value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>

            <div class="input-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" 
                       value="<?php echo htmlspecialchars($student['address']); ?>">
            </div>

            <div class="input-group">
                <label>Current Photo:</label>
                <img src="<?php echo htmlspecialchars($student['student_photo'] ?? 'images/default_avatar.png'); ?>" 
                     alt="Current Photo" class="profile-photo-small" style="margin-bottom: 10px;">
            </div>

            <div class="input-group">
                <label for="student_photo">Upload New Photo (Optional):</label>
                <input type="file" id="student_photo" name="student_photo" accept="image/*">
                <input type="hidden" name="old_photo_path" value="<?php echo htmlspecialchars($student['student_photo']); ?>">
            </div>

            <button type="submit" class="btn">Update Student</button>
            <a href="search_student.php?class_id=<?php echo $class_id; ?>" class="btn-back" style="display: block; text-align: center; margin-top: 1rem;">Cancel</a>
        </form>
    </div>
</body>
</html>