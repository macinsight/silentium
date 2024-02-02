<?php

require_once 'conf.php';

// Query to fetch mod data
$sql = "SELECT * FROM modlist ORDER BY id ASC";
$result = $conn->query($sql);

$totalFileSize = 0;

// Generate HTML dynamically
if ($result->num_rows > 0) {
    echo '<table class="table table-hover">';
    echo '<thead><tr><th>Mod Name</th><th>Image</th></tr></thead>';
    echo '<tbody>';
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        $link = $row['link'];
        $description = $row['description'];
        $image = $row['image'];
        echo "<td><a class='link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover' href='$link'>$description</a></td>";
        echo "<td><img src='$image' alt='$description' style='max-width: 100px;'></td>";
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
