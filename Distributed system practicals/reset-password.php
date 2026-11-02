<?php
$message = "";
$token = $_GET["token"] ?? $_POST["token"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? "";

    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=db;dbname=contact_db;charset=utf8mb4",
                "root",
                "secret_pass",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $pdo->prepare(
                "SELECT id FROM users WHERE reset_token = ?"
            );
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $token !== "") {
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare(
                    "UPDATE users
                     SET password = ?, reset_token = NULL
                     WHERE id = ?"
                );

                $stmt->execute([$hashedPassword, $user["id"]]);

                $message = "Password reset successful! You can now log in.";
            } else {
                $message = "Invalid or expired reset link.";
            }
        } catch (PDOException $e) {
            $message = "A database error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="reset-password.php">
        <input
            type="hidden"
            name="token"
            value="<?= htmlspecialchars($token) ?>"
        >

        <label>New Password:</label><br>
        <input type="password" name="password" minlength="8" required>
        <br><br>

        <button type="submit">Reset Password</button>
    </form>

    <p><a href="index.php">Back to Login</a></p>
</body>
</html>