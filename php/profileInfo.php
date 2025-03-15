<?php
    session_start();

    // Ensure the user is logged in
    if (!isset($_SESSION['student_id'])) {
        echo json_encode(["error" => "Not logged in"]);
        exit();
    }

    // Database connection
    // $servername = "localhost:3307";
    // $username = "root";
    // $password = "";
    // $dbname = "studentaccount";

    $servername = "localhost"; // Change if your database is hosted elsewhere
    $username = "ecom_Marc306";        // Default XAMPP username is 'root'
    $password = "OG*ED2e^2P%Atv0g";            // Default XAMPP password is empty
    $dbname = "ecom_studentaccount"; // Your database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed"]));
    }

    // Get student ID from session
    $student_id = $_SESSION['student_id'];

    // Fetch student data
    $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, student_id, school_name, email, contact_number, birthday, civil_status, image_url FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();



    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        echo json_encode($student);
    } else {
        echo json_encode(["error" => "No student found"]);
    }

    $stmt->close();
    $conn->close();
?>
