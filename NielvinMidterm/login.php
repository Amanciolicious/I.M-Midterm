<?php
session_start();
include('connection.php');

$connection = new Connection();
$pdo = $connection->OpenConnection();

$error = '';

// Registration Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $username = $_POST['reg_username'];
    $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT); // Securely hash the password

    // Insert the new user into the `register` table
    $query = "INSERT INTO register (first_name, last_name, address, birthdate, gender, username, password)
              VALUES (:first_name, :last_name, :address, :birthdate, :gender, :username, :password)";
    $stmt = $pdo->prepare($query);

    try {
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':address' => $address,
            ':birthdate' => $birthdate,
            ':gender' => $gender,
            ':username' => $username,
            ':password' => $password
        ]);
        $_SESSION['success'] = "Account registered successfully! You can now log in.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    }

    header("Location: login.php");
    exit;
}

// Login Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check admin login in the `users` table
    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_OBJ);

    if ($admin && password_verify($password, $admin->password)) {
        $_SESSION['username'] = $admin->username;
        $_SESSION['role'] = 'admin';
        header("Location: index.php");
        exit;
    }

    // Check user login in the `register` table
    $query = "SELECT * FROM register WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user && password_verify($password, $user->password)) {
        $_SESSION['username'] = $user->username;
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['role'] = 'user';
        header("Location: landing.php");
        exit;
    } else {
        $error = "Incorrect username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f6d365, #fda085);
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .container {
            margin-top: 50px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h2 {
            text-align: center;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }
        .alert { 
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
        }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
        .btn {
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(120deg, #6a11cb, #2575fc);
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(120deg, #2575fc, #6a11cb);
            transform: scale(1.05);
        }
        .modal-header {
            background-color: #f5f5f5;
            border-bottom: 2px solid #ddd;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid #ddd;
        }
        .btn-secondary {
            background: #28a745;
            color: white;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Login</h2>
    <?php if ($error) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" id="success-alert"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <h4>Login</h4>
        <div class="mb-3">
            <label for="username" class="form-label">Enter Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Enter Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>

    <button type="button" class="btn btn-secondary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#registrationModal">
        Register
    </button>

    <!-- Registration Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationModalLabel">Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-error" id="error-alert"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" name="birthdate" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reg_username" class="form-label">Username</label>
                            <input type="text" name="reg_username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="reg_password" class="form-label">Password</label>
                            <input type="password" name="reg_password" class="form-control" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if (isset($_SESSION['error'])): ?>
        var registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
        registrationModal.show();
    <?php endif; ?>

    setTimeout(function() {
        const errorAlert = document.getElementById("error-alert");
        const successAlert = document.getElementById("success-alert");
        if (errorAlert) errorAlert.style.display = "none";
        if (successAlert) successAlert.style.display = "none";
    }, 3000);
</script>
</body>
</html>
