<?php
require_once 'db_connect.php';
session_start();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $name=trim($_POST['customer_name']??''); $email=trim($_POST['email']??'');
    $vehicle=trim($_POST['vehicle']??''); $service=trim($_POST['service_name']??''); $date=$_POST['service_date']??'';
    if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || $vehicle==='' || $service==='' || $date==='') $message='Please complete every field with valid details.';
    else {
      $stmt=$conn->prepare("INSERT INTO appointments(customer_name,email,vehicle,service_name,service_date) VALUES(?,?,?,?,?)");
      $stmt->bind_param('sssss',$name,$email,$vehicle,$service,$date);
      $message=$stmt->execute()?'Appointment booked successfully!':'Unable to book appointment. Please try again.';
      $stmt->close();
    }
}
$appointments=$conn->query("SELECT id,customer_name,email,vehicle,service_name,service_date,status FROM appointments ORDER BY id DESC");
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Vehicle Service Management</title><link rel="stylesheet" href="style.css"></head><body>
<header><h1>Vehicle Service Management System</h1><p>Fast, reliable and affordable vehicle service booking</p></header>
<nav class="navbar"><a href="index.html">Home</a><a href="#booking">Book a service</a><a href="register.php">Customer Register</a><a href="login.php">Customer Login</a><a href="admin-login.php">Admin Login</a><?php if(!empty($_SESSION['user_id'])): ?><a href="my-appointments.php">My Appointments</a><a href="logout.php">Logout</a><?php endif; ?></nav>
<main>
<section class="panel" id="booking"><h2>Book a Service</h2><?php if($message):?><p class="notice"><?=e($message)?></p><?php endif;?>
<form method="post"><label>Customer name<input name="customer_name" required maxlength="100"></label><label>Email<input type="email" name="email" required maxlength="150"></label><label>Vehicle model / number<input name="vehicle" required maxlength="120"></label><label>Service<select name="service_name" required><option value="">Choose a service</option><option>Oil Change</option><option>Full Service</option><option>Tire Replacement</option><option>Brake Check</option><option>Engine Check</option></select></label><label>Preferred date<input type="date" name="service_date" required></label><button name="book" value="1">Book Appointment</button></form></section>
<section class="panel"><h2>Appointments</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Customer</th><th>Email</th><th>Vehicle</th><th>Service</th><th>Date</th><th>Status</th></tr></thead><tbody>
<?php if($appointments && $appointments->num_rows):while($r=$appointments->fetch_assoc()):?><tr><td><?=e($r['id'])?></td><td><?=e($r['customer_name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['vehicle'])?></td><td><?=e($r['service_name'])?></td><td><?=e($r['service_date'])?></td><td><span class="status"><?=e($r['status'])?></span></td></tr><?php endwhile;else:?><tr><td colspan="7">No appointments yet.</td></tr><?php endif;?></tbody></table></div><p class="small">Register or sign in to view your customer account. Administrators should use the separate admin login.</p></section>
</main><footer>Vehicle Service Management System · Docker + Kubernetes Micro-Project</footer></body></html>
