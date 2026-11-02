<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$contacts = [];
$error = "";
$search = trim($_GET["search"] ?? "");

if ($search !== "") {
    try {
        $pdo = new PDO(
            "mysql:host=db;dbname=contact_db;charset=utf8mb4",
            "root",
            "secret_pass",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare(
            "SELECT * FROM contacts
             WHERE registration_number LIKE ?
                OR mobile_number LIKE ?
                OR email LIKE ?
             ORDER BY id DESC"
        );

        $term = "%" . $search . "%";
        $stmt->execute([$term, $term, $term]);
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = "Unable to search contacts. Please try again.";
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Contacts</title>
</head>
<body>
    <h2>Search Contacts</h2>

```
<form method="GET" action="search.php">
    <input
        type="text"
        name="search"
        placeholder="Registration number, mobile or email"
        value="<?= htmlspecialchars($search) ?>"
        required
    >
    <button type="submit">Search</button>
</form>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($search !== "" && !$error): ?>
    <h3>Search Results</h3>

    <?php if (count($contacts) > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Registration Number</th>
                <th>Mobile Number</th>
                <th>Email</th>
                <th>Address</th>
            </tr>

            <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td><?= htmlspecialchars($contact["registration_number"]) ?></td>
                    <td><?= htmlspecialchars($contact["mobile_number"]) ?></td>
                    <td><?= htmlspecialchars($contact["email"]) ?></td>
                    <td><?= htmlspecialchars($contact["address"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No matching contacts found.</p>
    <?php endif; ?>
<?php endif; ?>

<p><a href="dashboard.php">Back to Dashboard</a></p>
<p><a href="logout.php">Logout</a></p>
```

</body>
</html>
