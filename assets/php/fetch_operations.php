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

date_default_timezone_set('Etc/UTC');
$currentDateTime = date('Y-m-d H:i:s');
// Query to fetch upcoming operations
$sql = "SELECT * FROM operations WHERE  CONCAT(date, ' ', time) > '$currentDateTime' ORDER BY date ASC";
$result = $conn->query($sql);

// Generate HTML dynamically
if ($result->num_rows > 0) {
    // Initialize an array to hold operation details for the webhook
    $operations_for_webhook = [];

    echo '<table class="table table-hover">';
    echo '<thead><tr><th>Operation Name</th><th><i class="bi bi-calendar2-event"></i> Date</th><th><i class="bi bi-stopwatch"></i> Time (Local)</th><th><i class="bi bi-geo-alt"></i> Location</th><th><i class="bi bi-card-text"></i> Description</th></tr></thead>';
    echo '<tbody>';

    while ($row = $result->fetch_assoc()) {
        // Fetch operation details
        $operation_name = $row['operation_name'];
        $date = $row['date'];
        $time = new DateTime($row['time'], new DateTimeZone('UTC'));
        $time->setTimezone(new DateTimeZone('UTC'));
        $time_formatted = $time->format('H:i e');
        $time_unix = $time->format('U');
        $location = $row['location'];
        $description = $row['description'];

        // Add operation details to the array for the webhook
        $operations_for_webhook[] = [
            'name' => $operation_name,
            'date' => $date,
            'time' => $time_unix,
            'location' => $location,
            'description' => $description
        ];

        // Output operation details in the HTML table
        echo "<tr>";
        echo "<td>$operation_name</td>";
        echo "<td>$date</td>";
        echo "<td>$time_formatted</td>";
        echo "<td>$location</td>";
        echo "<td>$description</td>";
        echo "</tr>";
    }

    echo '</tbody>';
    echo '</table>';

    // Close the database connection
    $conn->close();

    // Trigger the Discord webhook
    send_webhook_to_discord($operations_for_webhook);
} else {
    echo "<p>No upcoming operations found.</p>";

    // Close the database connection
    $conn->close();
}

// Function to send the webhook to Discord
// Function to send the webhook to Discord
function send_webhook_to_discord($operations) {
    // Discord webhook URL
    $webhook_url = $_ENV['DISCORD_WEBHOOK_URL'];

    // Create the payload to send to Discord
    $payload = [
        'content' => 'New operation added, <@&1203045365410963486> ',
        'embeds' => []
    ];

    foreach ($operations as $operation) {
        // Format the time to display in the user's local timezone
        $local_time = date('Y-m-d H:i', $operation['time']);

        $embed = [
            'title' => $operation['name'],
            'fields' => [
                ['name' => 'Date', 'value' => $operation['date'], 'inline' => true],
                ['name' => 'Time (Local)', 'value' => $local_time, 'inline' => true],
                ['name' => 'Location', 'value' => $operation['location']],
                ['name' => 'Description', 'value' => $operation['description']],
                ['name' => 'Unix Timestamp', 'value' => '<' . $operation['time'] . '>', 'inline' => true] // Display Unix timestamp
            ]
        ];
        $payload['embeds'][] = $embed;
    }

    // Send the POST request to Discord webhook
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Check if the request was successful
    if (!$response) {
        echo "Error sending webhook: " . curl_error($ch);
    } else {
        echo "Webhook sent successfully";
    }
}
?>

