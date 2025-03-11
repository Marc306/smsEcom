<?php
// Include the database configuration to connect to the database
require_once '../config/database.php';  // Adjust the path if needed

// Query to fetch all messages from the message_chat table
$sql = "SELECT * FROM message_chat ORDER BY sent DESC"; // Order by sent timestamp
$result = $conn->query($sql);  // Execute the query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .message-card {
            background: #fff;
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .message-card h5 {
            font-size: 1.2rem;
            color: #007bff;
        }
        .message-card p {
            font-size: 1rem;
            color: #555;
        }
        .sent-time {
            font-size: 0.9rem;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Messages</h2>

    <?php if ($result->num_rows > 0): ?>
        <!-- Loop through all messages -->
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="message-card">
                <h5><?php echo htmlspecialchars($row['subject']); ?></h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                <p><strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                <p class="sent-time"><strong>Sent:</strong> <?php echo $row['sent']; ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No messages found.</p>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

