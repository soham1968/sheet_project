<?php
// echo("hello");
$con = mysqli_connect('localhost', 'root', '', 'sheet');
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
