<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$message = "";
$error = "";

try {
    $pdo = new PDO(
        "mysql:host=db;dbname=contact_db;charset=utf8mb4",
        "root",
        "secret_pass",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $registration_number = trim($_POST["registration_number"] ?? "");
        $mobile_number = trim($_POST["mobile_number"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $address = trim($_POST["address"] ?? "");

        if ($registration_number === "" || $mobile_number === "" ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) || $address === "") {
            $error = "Please enter valid details in all fields.";
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO contacts (registration_number, mobile_number, email, address)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                $registration_number,
                $mobile_number,
                $email,
                $address
            ]);

            $message = "Contact saved successfully!";
        }
    }

    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY id DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $error = "That registration number is already registered.";
    } else {
        $error = "A database error occurred. Please try again.";
    }

    $contacts = [];
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Contact Management System</title>
</head>
<body>
    <h2>Contact Management System</h2>

```
<p>Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>!</p>
<p><a href="logout.php">Logout</a></p>

<h3>Add a Contact</h3>

<?php if ($message): ?>
    <p style="color:green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="dashboard.php">
    <label>Registration Number:</label><br>
    <input type="text" name="registration_number" required><br><br>

    <label>Mobile Number:</label><br>
    <input type="text" name="mobile_number" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Address:</label><br>
    <textarea name="address" required></textarea><br><br>

    <button type="submit">Save Contact</button>
</form>

<h3>Saved Contacts</h3>

<p><a href="search.php">Search Contacts</a></p>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Registration Number</th>
        <th>Mobile Number</th>
        <th>Email</th>
        <th>Address</th>
    </tr>

    <?php foreach ($contacts as $contact): ?>
        <tr>
            <td><?= htmlspecialchars((string)$contact["id"]) ?></td>
            <td><?= htmlspecialchars($contact["registration_number"]) ?></td>
            <td><?= htmlspecialchars($contact["mobile_number"]) ?></td>
            <td><?= htmlspecialchars($contact["email"]) ?></td>
            <td><?= htmlspecialchars($contact["address"]) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
```

</body>
</html>
