<?php
// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "itenenary_report");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to retrieve data from the database
$sql = "SELECT id, upload_at FROM full_report ORDER BY upload_at ASC";
$result = mysqli_query($conn, $sql);

// Create an array to store the grouped data
$grouped_data = array();

// Loop through the result set
while ($row = mysqli_fetch_assoc($result)) {
    // Get the upload_at time and convert it to a timestamp
    $upload_at = strtotime($row['upload_at']);
    $project = $row['project'];

    // Calculate the group ID based on the upload_at time
    $group_id = date('Ymd', $upload_at);

    // Add the ID to the corresponding group
    if (!isset($grouped_data[$group_id])) {
        $grouped_data[$group_id] = array();
    }
    $grouped_data[$group_id][] = $row['id'];
}

// Close the database connection
mysqli_close($conn);

// Display the grouped data on the website
foreach ($grouped_data as $group_id => $ids) {
    echo "<h2>Group $group_id</h2>";
    echo $project;
    echo "<ul>";
    foreach ($ids as $id) {
        echo "<li>ID $id</li>";
    }
    echo "</ul>";
}
?>