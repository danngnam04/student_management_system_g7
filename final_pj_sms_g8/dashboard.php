<?php
// dashboard.php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_name = $_SESSION['user_name'];

$sql = "SELECT 
            classes.*, 
            COUNT(students.id) AS student_count
        FROM 
            classes
        LEFT JOIN 
            students ON classes.id = students.class_id
        GROUP BY 
            classes.id
        ORDER BY 
            classes.grade_block, classes.class_name";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Management</title>
    <link rel="stylesheet" href="style_simple.css">
</head>
<body>
    
    <div class="dashboard-container">
        
        <div class="header-bar">
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>

        <h3>Class Management</h3>
        
        <a href="class_create.php" class="btn btn-create">Add New Class</a>

        <table class="table-data">
            <thead>
                <tr>
                    <th>No.</th> <th>Class Name</th>
                    <th>Grade Block</th>
                    <th>Head Teacher</th>
                    <th>Student Count</th>
                    <th>Actions</th>
            
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    // --- BẮT ĐẦU BIẾN ĐẾM ---
                    $i = 1; 
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        // --- HIỂN THỊ BIẾN ĐẾM ---
                        echo "<td>" . $i . "</td>"; 
                        
                        echo "<td><a href='student_list.php?class_id=" . $row['id'] . "' class='link-primary'>" 
                             . htmlspecialchars($row['class_name']) 
                             . "</a></td>";
                        echo "<td>" . htmlspecialchars($row['grade_block']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['teacher_name']) . "</td>";
                        echo "<td>" . $row['student_count'] . "</td>";
                        echo "<td class='actions'>";
                        echo "<a href='edit_class.php?id=" . $row['id'] . "' class='btn btn-edit'>Edit</a>";
                        echo "<a href='delete-class.php?id=" . $row['id'] . "' class='btn btn-delete' onclick=\"return confirm('Are you sure you want to delete this class? This will also delete ALL students in it!');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                        
                        // --- TĂNG BIẾN ĐẾM ---
                        $i++; 
                    }
                } else {
                    // --- CẬP NHẬT COLSPAN ---
                    echo "<tr><td colspan='6'>No classes found.</td></tr>"; 
                }
                $conn->close(); 
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>