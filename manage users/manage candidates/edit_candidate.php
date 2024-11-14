<?php
include '../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the candidate ID
    $candidate_id = intval($_POST['candidate_id']);
    
    // Get the updated form data
    $candidate_name = htmlspecialchars($_POST['candidate_name']);
    $candidate_role = htmlspecialchars($_POST['candidate_role']);
    $candidate_department = htmlspecialchars($_POST['candidate_department']);
    // Add more fields as needed
print ($candidate_name);
    // Handle the symbol upload
    $symbol = null;
    if (!empty($_FILES["symbol"]["name"])) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] .  "/EVotingSystem/uploads/symbols/"; // Set the server path
        $original_file_name = $_FILES["symbol"]["name"];
        
        // Sanitize the file name: replace spaces with underscores and remove special characters
        $clean_file_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '', str_replace(' ', '_', $original_file_name));
        $clean_file_name = strtolower($clean_file_name); // Convert to lowercase
    
        // Add a unique identifier to prevent overwriting
        $unique_file_name = uniqid() . '_' . $clean_file_name;
    
        // Set the full file path
        $symbol_path = $target_dir . $unique_file_name;
    
        // Get the file extension
        $symbol_file_type = strtolower(pathinfo($symbol_path, PATHINFO_EXTENSION));
    
        // Check file type and size
        if (in_array($symbol_file_type, ['jpg', 'png', 'jpeg']) && $_FILES["symbol"]["size"] < 5000000) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["symbol"]["tmp_name"], $symbol_path)) {
                // Set the web-accessible path for database storage
                $symbol = "/uploads/symbols/" . $unique_file_name;
            } else {
                echo "Failed to move uploaded file.";
                exit();
            }
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
