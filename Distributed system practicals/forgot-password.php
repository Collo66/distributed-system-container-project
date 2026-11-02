<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $pdo = new PDO(
                "mysql:host=db;dbname=contact_db;charset=utf8mb4",
                "root",
                "secret_pass",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare(
                    "UPDATE users SET reset_token = ? WHERE id = ?"
                );
                $stmt->execute([$token, $user["id"]]);

                $resetLink = "http://localhost:8080/reset-password.php?token=" . urlencode($token);

                $headers = "From: noreply@localhost\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                $sent = mail(
                    $email,
                    "Password Reset",
                    "Use this link to reset your password:\n\n" . $resetLink,
                    $headers
                );

                if (!$sent) {
                    $message = "The email could not be sent. Mail configuration is required.";
                } else {
                    $message = "If the account exists, a reset email has been sent.";
                }
            } else {
                $message = "If the account exists, a reset email has been sent.";
            }
        } catch (PDOException $e) {
            $message = "A database error occurred. Please try again.";
        }
    } else {
        $message = "Please enter a valid email address.";
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>

```
<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" action="forgot-password.php">
    <label>Enter your registered email:</label><br>
    <input type="email" name="email" required><br><br>
    <button type="submit">Send Reset Link</button>
</form>

<p><a href="index.php">Back to Login</a></p>
```

</body>
</html>
