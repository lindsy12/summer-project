<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Client Dashboard</title>
</head>
<body>
<h2>Welcome, <?php echo $_SESSION['name']; ?> (Client)</h2>
<p>This is your client dashboard.</p>
<a href="../logout.php">Logout</a>
</body>
</html>