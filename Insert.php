<?php
$check_in_date = date("y-m-d", strtotime($_POST['check_in_date']));
$check_out_date = date("y-m-d", strtotime($_POST['check_out_date']));
$no_of_adults = $_POST['no-of-adults'];
$no_of_children = $_POST['no-of-children'];

$hostname = "localhost";
$username = "root";
$password = "";
$database = "booking";
$conn = mysqli_connect($hostname, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection Failed:" . mysqli_connect_errno());
}

$insert_query = "INSERT INTO `booking` (`check_in_date`,`check_out_date`,`no_of_adults`,`no_of_children`) 
    VALUES (?,?,?,?)";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $insert_query)) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "iiii", $check_in_date, $check_out_date, $no_of_adults, $no_of_children);
mysqli_stmt_execute($stmt);