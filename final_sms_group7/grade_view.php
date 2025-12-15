<?php
// grade_view.php - UPDATED: Teachers can now input grades
session_start();
include 'db.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_role = $_SESSION['user_role'] ?? 'user';

// 2. Lấy ID học sinh
if (!isset($_GET['student_id'])) { die("Error: Student ID missing."); }
$student_id = (int)$_GET['student_id'];

// 3. Lấy thông tin học sinh
$stmt = $conn->prepare("SELECT s.*, c.class_name FROM students s JOIN classes c ON s.class_id = c.id WHERE s.id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) die("Student not found");

// 4. Lấy bộ lọc (Năm học, Học kỳ)
$current_term = $_GET['term'] ?? 'Semester 1';
$current_year = $_GET['year'] ?? '2024-2025';

// 5. XỬ LÝ LƯU ĐIỂM (Cập nhật: Admin HOẶC Teacher đều được)
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra quyền: Chỉ Admin hoặc Teacher mới được lưu
    if ($user_role == 'admin' || $user_role == 'teacher') {
        $term = $_POST['term'];
        $year = $_POST['year'];
        $subjects = ['math', 'english', 'physics', 'chemistry', 'literature', 'history', 'geography'];
        $scores = [];
        
        // Lấy điểm từ form (nếu để trống thì coi như null hoặc 0 tuỳ logic, ở đây lấy giá trị post)
        foreach($subjects as $sub) $scores[] = $_POST[$sub];

        // Check xem đã có dòng điểm của kỳ này chưa
        $check = $conn->query("SELECT id FROM grades WHERE student_id=$student_id AND term='$term' AND school_year='$year'");
        
        if ($check->num_rows > 0) {
            // Update
            $sql = "UPDATE grades SET math=?, english=?, physics=?, chemistry=?, literature=?, history=?, geography=? WHERE student_id=? AND term=? AND school_year=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("dddddddiss", ...array_merge($scores, [$student_id, $term, $year]));
        } else {
            // Insert
            $sql = "INSERT INTO grades (student_id, term, school_year, math, english, physics, chemistry, literature, history, geography) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issddddddd", $student_id, $term, $year, ...$scores);
        }
        
        if ($stmt->execute()) {
            $msg = "<div class='success' style='background:#dcfce7; color:#166534; padding:10px; border-radius:5px; margin-bottom:15px;'>Grades saved successfully!</div>";
        } else {
            $msg = "<div class='error' style='background:#fee2e2; color:#991b1b; padding:10px; border-radius:5px; margin-bottom:15px;'>Error saving grades.</div>";
        }
    } else {
        $msg = "<div class='error'>You do not have permission to save grades.</div>";
    }
}

// 6. LẤY ĐIỂM HIỆN TẠI ĐỂ HIỂN THỊ
$g_sql = "SELECT * FROM grades WHERE student_id = ? AND term = ? AND school_year = ?";
$stmt_g = $conn->prepare($g_sql);
$stmt_g->bind_param("iss", $student_id, $current_term, $current_year);
$stmt_g->execute();
$g = $stmt_g->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Book - <?php echo htmlspecialchars($student['full_name']); ?></title>
    <link rel="stylesheet" href="style_simple.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Thêm CSS inline nhỏ để đảm bảo hiển thị đẹp nếu thiếu file css */
        .grade-input { width: 80px; text-align: center; padding: 5px; border: 1px solid #cbd5e1; border-radius: 4px; font-weight: bold; }
        .table-data th, .table-data td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
        .btn-back { display:inline-block; margin-bottom:15px; text-decoration:none; color:#3b82f6; font-weight:bold;}
    </style>
</head>
<body>
    <div class="dashboard-container" style="max-width: 900px; margin: 20px auto; font-family: 'Segoe UI', sans-serif;">
        <a href="search_student.php?btn_search=1" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Search
        </a>
        
        <div style="display:flex; gap:20px; align-items:center; margin-bottom:30px; padding:20px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
            <img src="<?php echo htmlspecialchars($student['student_photo'] ?? 'images/default_avatar.png'); ?>" 
                 style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div>
                <h2 style="margin:0 0 8px 0; font-size:1.75rem; color:#1e293b;">
                    <?php echo htmlspecialchars($student['full_name']); ?>
                </h2>
                <p style="margin:0; color:#64748b; font-size:1rem;">
                    <i class="fas fa-school" style="margin-right:6px;"></i>
                    Class: <strong><?php echo htmlspecialchars($student['class_name']); ?></strong>
                </p>
            </div>
        </div>

        <?php echo $msg; ?>

        <form action="" method="GET" class="filter-bar" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:24px; display:flex; gap:20px;">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            <div>
                <label style="font-weight:bold; color:#64748b;"><i class="fas fa-calendar-alt"></i> School Year:</label>
                <select name="year" onchange="this.form.submit()" class="form-control" style="padding:5px; border-radius:4px;">
                    <option value="2023-2024" <?php if($current_year=='2023-2024') echo 'selected';?>>2023-2024</option>
                    <option value="2024-2025" <?php if($current_year=='2024-2025') echo 'selected';?>>2024-2025</option>
                    <option value="2025-2026" <?php if($current_year=='2025-2026') echo 'selected';?>>2025-2026</option>
                </select>
            </div>
            <div>
                <label style="font-weight:bold; color:#64748b;"><i class="fas fa-bookmark"></i> Term:</label>
                <select name="term" onchange="this.form.submit()" class="form-control" style="padding:5px; border-radius:4px;">
                    <option value="Semester 1" <?php if($current_term=='Semester 1') echo 'selected';?>>Semester 1</option>
                    <option value="Semester 2" <?php if($current_term=='Semester 2') echo 'selected';?>>Semester 2</option>
                </select>
            </div>
        </form>

        <form action="" method="POST">
            <input type="hidden" name="term" value="<?php echo $current_term; ?>">
            <input type="hidden" name="year" value="<?php echo $current_year; ?>">
            
            <div style="background:#fff; border-radius:8px; border:1px solid #e2e8f0; overflow:hidden;">
                <table class="table-data grade-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="width:60%; text-align:left;"><i class="fas fa-book"></i> Subject</th>
                            <th style="width:40%; text-align:center;">Score (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subs = [
                            'math'=>'Mathematics', 
                            'english'=>'English', 
                            'physics'=>'Physics', 
                            'chemistry'=>'Chemistry', 
                            'literature'=>'Literature', 
                            'history'=>'History', 
                            'geography'=>'Geography'
                        ];
                        foreach($subs as $key => $label): 
                            $val = $g[$key] ?? '';
                        ?>
                        <tr>
                            <td style="font-weight:600; color:#334155;"><?php echo $label; ?></td>
                            <td style="text-align:center;">
                                <?php 
                                // QUAN TRỌNG: Cho phép Admin HOẶC Teacher sửa điểm
                                if ($user_role == 'admin' || $user_role == 'teacher'): 
                                ?>
                                    <input type="number" 
                                           step="0.1" 
                                           min="0" 
                                           max="10" 
                                           name="<?php echo $key; ?>" 
                                           value="<?php echo $val; ?>" 
                                           class="grade-input" 
                                           data-score="<?php echo $val; ?>"
                                           required>
                                <?php else: ?>
                                    <strong class="grade-display" 
                                            data-score="<?php echo $val; ?>"
                                            style="font-size:1.1rem; color:#1e293b;">
                                            <?php echo $val !== '' && $val != 0 ? $val : '-'; ?>
                                    </strong>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="result-box" style="margin-top:24px; padding:15px; background:#fff; border-radius:8px; border:1px solid #e2e8f0; display:flex; gap:20px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-chart-line" style="color:#2563eb; font-size:1.3rem;"></i>
                    <span style="color:#64748b;">GPA:</span> 
                    <span id="gpa" style="color:#2563eb; font-size:1.5rem; font-weight:700;">0.0</span>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-medal" style="color:#f59e0b; font-size:1.3rem;"></i>
                    <span style="color:#64748b;">Rank:</span> 
                    <span id="rank" style="font-size:1.3rem; font-weight:700;">---</span>
                </div>
            </div>

            <?php 
            // Hiện nút Save cho cả Admin và Teacher
            if ($user_role == 'admin' || $user_role == 'teacher'): 
            ?>
                <div style="text-align:right; margin-top:24px;">
                    <button type="submit" class="btn" style="padding:12px 30px; font-size:1rem; background:#3b82f6; color:#fff; border:none; border-radius:6px; cursor:pointer;">
                        <i class="fas fa-save"></i> Save Grades
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        // JS Tính GPA tự động
        function calculateGPA() {
            let total = 0, count = 0;
            const scoreElements = document.querySelectorAll('[data-score]');
            
            scoreElements.forEach(el => {
                let score = parseFloat(el.dataset.score);
                if (!isNaN(score) && score >= 0) {
                    total += score;
                    count++;
                }
            });
            
            const gpaEl = document.getElementById('gpa');
            const rankEl = document.getElementById('rank');
            
            if (count === 0) {
                gpaEl.innerText = "0.0";
                rankEl.innerText = "No Data";
                rankEl.style.color = "#94a3b8";
                return;
            }
            
            let avg = total / count;
            gpaEl.innerText = avg.toFixed(1);
            
            let rank = "Weak";
            let color = "#dc2626";
            
            if (avg >= 8.0) { rank = "Excellent"; color = "#16a34a"; } 
            else if (avg >= 6.5) { rank = "Good"; color = "#2563eb"; } 
            else if (avg >= 5.0) { rank = "Average"; color = "#f59e0b"; }
            
            rankEl.innerText = rank;
            rankEl.style.color = color;
        }
        
        // Chạy lần đầu
        calculateGPA();
        
        // Cập nhật khi nhập liệu
        const inputs = document.querySelectorAll('.grade-input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.dataset.score = this.value;
                calculateGPA();
            });
        });
    </script>
</body>
</html>