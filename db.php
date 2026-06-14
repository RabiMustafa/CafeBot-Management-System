<?php
session_start(); // Turns on login memory globally
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cafe_db';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}