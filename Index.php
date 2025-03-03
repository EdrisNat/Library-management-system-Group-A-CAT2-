<?php
require_once 'config.php';

$login_err = "";

$register_err = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $student_id = sanitize_input($_POST['student_id']);
    $password = sanitize_input($_POST['password']);
    
    if ($student_id == 'admin') {
        // Librarian login
        $sql = "SELECT * FROM librarian WHERE admin_id = ? AND password = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $student_id, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $_SESSION['librarian'] = true;
                $_SESSION['name'] = $row['name'];
                header("location: librarian_dashboard.php");
                exit;
            }
        }
    } else {
        // Student login
        $sql = "SELECT * FROM students WHERE student_id = ? AND password = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $student_id, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $_SESSION['student_id'] = $row['student_id'];
                $_SESSION['name'] = $row['name'];
                header("location: student_dashboard.php");
                exit;
            }
        }
    }
    $login_err = "Invalid user_id or password.";
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $student_id = sanitize_input($_POST['reg_student_id']);
    $name = sanitize_input($_POST['reg_name']);
    $password = sanitize_input($_POST['reg_password']);
    
    // Check if user exists
    $sql = "SELECT student_id FROM students WHERE student_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $register_err = "This ID is already registered.";
        } else {
            // Insert new user
            $sql = "INSERT INTO students (student_id, name, password) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sss", $student_id, $name, $password);
                
                if ($stmt->execute()) {
                    header("location: index.php?registration=success");
                    exit;
                } else {
                    $register_err = "Something went wrong. Please try again.";
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header-image {
            margin-top:2px;
            height: 320px;
            background: url('images/library_bg.png') center/cover no-repeat;
            margin-bottom: 1rem;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .text-sub {
            color: grey; 
            font-size: 23px;
            margin-left: 400px;
            margin-top: 0px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="header-image"></div>
    
    <div class="container">
        <h1 class="text-center mb-4">Library Management System</h1>
        <h3 class="text-sub">By Group A (CAT2 ASSIGNMENT) </h3>
        <div class="row justify-content-center">
            <!-- Login Form -->
            <div class="col-md-5 mb-4">
                <div class="form-container">
                    <h3 class="text-center mb-4">Login</h3>
                    <?php if(!empty($login_err)) echo "<div class='alert alert-danger'>$login_err</div>"; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label">User ID</label>
                            <input type="text" name="student_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="col-md-5 mb-4">
                <div class="form-container">
                    <h3 class="text-center mb-4">Student Registration</h3>
                    <?php if(!empty($register_err)) echo "<div class='alert alert-danger'>$register_err</div>"; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label">Student ID</label>
                            <input type="text" name="reg_student_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="reg_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="reg_password" class="form-control" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>