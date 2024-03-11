<?php
// Include the database connection
require_once('connection.php');

// Specify content type
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

// Handle POST Request (create new comment)
if ($method === 'POST') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data from decoded JSON
    $product_id = $data['product_id'];
    $user_id = $data['user_id'];
    $rating = $data['rating'];
    $image = $data['image'];
    $text = $data['text'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO Comments (product, user, rating, image, text) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $product_id, $user_id, $rating, $image, $text);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Comment added successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle GET Request (retrieve existing comments)
elseif ($method === 'GET') {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM Comments");
    
    // Execute the statement
    $stmt->execute();
    
    // Bind the results
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $comments = array();
        while($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        echo json_encode($comments);
    } else {
        echo json_encode(array("message" => "No users found"));
    }

    // Close the statement
    $stmt->close();
}

// Handle PUT Request (update data for existing comment)
elseif ($method === 'PUT') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract data from decoded JSON
    $comment_id = $data['comment_id'];
    $product_id = $data['product_id'];
    $user_id = $data['user_id'];
    $rating = $data['rating'];
    $image = $data['image'];
    $text = $data['text'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Comments SET product=?, user=?, rating=?, image=?, text=? WHERE comment_id=?");
    $stmt->bind_param("iiissi", $product_id, $user_id, $rating, $image, $text, $comment_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Comment updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle DELETE Request (delete a comment)
elseif ($method === 'DELETE') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract comment_id from decoded JSON
    $comment_id = $data['comment_id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("DELETE FROM Comments WHERE comment_id=?");
    $stmt->bind_param("i", $comment_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Comment deleted successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle other requests
else {
    http_response_code(405);
    echo json_encode(array("message" => "Unsupported HTTP method"));
    exit;
}

// Close the database connection
$conn->close();
?>
