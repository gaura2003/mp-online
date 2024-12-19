<?php
// upload_documents.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if files are uploaded
    if (isset($_FILES['documents'])) {
        $uploadedFiles = [];
        $uploadDir = 'documents/';
        
        // Create the uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['documents']['name'] as $key => $fileName) {
            $fileTmpName = $_FILES['documents']['tmp_name'][$key];
            $fileDestination = $uploadDir . basename($fileName);
            
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $uploadedFiles[] = $fileDestination;
            }
        }

        // Return the uploaded file paths as JSON
        echo json_encode(['status' => 'success', 'files' => $uploadedFiles]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No files uploaded']);
    }
}
?>
