<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Database connection
$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$database = $_ENV['DB_DATABASE'];

$conn = new mysqli($host . ':' . $port, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Query to fetch mod data
$sql = "SELECT * FROM modlist ORDER BY id ASC";
$result = $conn->query($sql);

// Cache duration in seconds (1 day)
$cacheDuration = 86400;

// Variable to store the total file size
$totalFileSize = 0;

// Generate HTML dynamically
if ($result->num_rows > 0) {
    echo '<table class="table table-hover">';
    echo '<thead><tr><th>Mod Name</th><th>File Size (MB)</th><th>Required?</th></tr></thead>';
    echo '<tbody>';
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        $link = $row['link'];
        echo "<td>";
        $description = $row['description'];
        echo "</td>";
        echo "<td>";
        $image = $row['image'];
        echo "</td>";
        echo "</tr>";
    }
    echo '</tbody>';
    echo '</table>';

} else {
    echo "<p>No Mods found.</p>";
}

// Close the database connection
$conn->close();
?>
