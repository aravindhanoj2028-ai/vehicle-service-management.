```php
<?php
session_start();
require_once 'db_connect.php';

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['user_role'] ?? '') !== 'customer'
) {
    header('Location: login.php');
    exit;
}

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

try {
    $st = $conn->prepare(
        'SELECT id, vehicle, service_name, service_date, status
         FROM appointments
         WHERE email = (
             SELECT email FROM users WHERE id = :user_id
         )
         ORDER BY id DESC'
    );

    $st->execute([':user_id' => $_SESSION['user_id']]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    error_log('My appointments query failed: ' . $ex->getMessage());
    $rows = [];
    $error = 'Unable to load your appointments right now.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Appointments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>My Appointments</h1>
    <p>Welcome, <?= e($_SESSION['user_name'] ?? 'Customer') ?></p>
</header>

<nav class="navbar">
    <a href="booking.php">Book a Service</a>
    <a href="index.php">Home</a>
    <a href="logout.php">Logout</a>
</nav>

<main>
    <section class="panel">
        <h2>Your bookings</h2>

        <?php if (!empty($error)): ?>
            <p class="notice"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= e($r['id']) ?></td>
                        <td><?= e($r['vehicle']) ?></td>
                        <td><?= e($r['service_name']) ?></td>
                        <td><?= e($r['service_date']) ?></td>
                        <td><?= e($r['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No appointments found. Book a service to get started.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
```
