<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the candidate ID
    $candidate_id = intval($_POST['candidate_id']);
    
    // Get the updated form data
    $candidate_name = htmlspecialchars($_POST['candidate_name']);
    $candidate_role = htmlspecialchars($_POST['candidate_role']);
    $candidate_department = htmlspecialchars($_POST['candidate_department']);
    // Add more fields as needed

    // Handle the symbol upload
    $symbol = "";
    if (isset($_FILES['symbol']) && $_FILES['symbol']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/symbols/";
        $symbol = $target_dir . basename($_FILES["symbol"]["name"]);
        $symbol_file_type = strtolower(pathinfo($symbol, PATHINFO_EXTENSION));

        // Check file type and size (e.g., 2MB limit)
        if (in_array($symbol_file_type, ['jpg', 'png', 'jpeg']) && $_FILES["symbol"]["size"] < 2000000) {
            move_uploaded_file($_FILES["symbol"]["tmp_name"], $symbol);
        } else {
            echo "Invalid symbol file type or size.";
            exit();
        }
    }

    // Prepare the update query
    if ($symbol) {
        $sql = "UPDATE candidates SET candidate_name = ?, candidate_role = ?, department = ?, symbol = ? WHERE candidate_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $candidate_name, $candidate_role, $candidate_department, $symbol, $candidate_id);
    } else {
        $sql = "UPDATE candidates SET candidate_name = ?, candidate_role = ?, department = ? WHERE candidate_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $candidate_name, $candidate_role, $candidate_department, $candidate_id);
    }

    if ($stmt->execute()) {
        echo "Candidate updated successfully";
        // Redirect or refresh the page after update
        header("Location: manage_candidates.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
