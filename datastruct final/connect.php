<?php
session_start(); // Start the session to use session variables

// Initialize an array to hold error messages
$errors = [];

// Collect form inputs and sanitize them
$name = trim($_POST['name'] ?? '');
$amount = trim($_POST['amount'] ?? '');
$duedate = trim($_POST['duedate'] ?? '');

// Validate inputs
if (empty($name)) {
    $errors[] = "Name can't be empty";
}
if (empty($amount)) {
    $errors[] = "Amount can't be empty";
} elseif (!is_numeric($amount)) {
    $errors[] = "Amount must be a number";
}
if (empty($duedate)) {
    $errors[] = "Due date needs to be filled";
}

// If there are errors, store them in the session and redirect back to the form
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: index.php'); // Redirect to your form page
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'test');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
} else {
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO loan_table (name, amount, duedate) VALUES (?, ?, ?)");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("sis", $name, $amount, $duedate);

    if ($stmt->execute()) {
        // Success message
        $_SESSION['success'] = "Data has been successfully saved inside the database";
    } else {
        // Error message
        $_SESSION['errors'] = ["Failed to save data: " . $stmt->error];
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to the form page with success or error messages
    header('Location: index.php'); // Redirect to your form page
    exit();
}
?>