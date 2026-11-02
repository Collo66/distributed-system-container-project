<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = "Enter a valid username and email. Password must have at least 8 characters.";
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=db;dbname=contact_db;charset=utf8mb4",
                "root",
                "secret_pass",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $username,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $success = "Registration successful! You can now log in.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "That username or email is already registered.";
            } else {
                $error = "Registration failed. Please try again.";
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
    <title>Register - Contact Management System</title>
</head>
<body>
    <h2>Create an Account</h2>

```
<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST" action="register.php">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password (at least 8 characters):</label><br>
    <input type="password" name="password" minlength="8" required><br><br>

    <button type="submit">Register</button>
</form>

<p><a href="index.php">Back to Login</a></p>
```

</body>
</html>
