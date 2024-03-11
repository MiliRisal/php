<?php
// Included the database connection
require_once('connection.php');

// Specified content type
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

// Handled POST Request (create new comment)
if ($method === 'POST') {
    // Decoded JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extracted data from decoded JSON
    $product_id = $data['product_id'];
    $user_id = $data['user_id'];
    $rating = $data['rating'];
    $image = $data['image'];
    $text = $data['text'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("INSERT INTO Comments (product, user, rating, image, text) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $product_id, $user_id, $rating, $image, $text);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Comment added successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // closing the statement
    $stmt->close();
}

// Handled GET Request (retrieve existing comments)
elseif ($method === 'GET') {
    // Prepared the SQL statement
    $stmt = $conn->prepare("SELECT * FROM Comments");
    
    // Executed the statement
    $stmt->execute();
    
    // Binded the results
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

    // closing the statement
    $stmt->close();
}

// Handled PUT Request (update data for existing comment)
elseif ($method === 'PUT') {
    // Decoded JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extracted data from decoded JSON
    $comment_id = $data['comment_id'];
    $product_id = $data['product_id'];
    $user_id = $data['user_id'];
    $rating = $data['rating'];
    $image = $data['image'];
    $text = $data['text'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("UPDATE Comments SET product=?, user=?, rating=?, image=?, text=? WHERE comment_id=?");
    $stmt->bind_param("iiissi", $product_id, $user_id, $rating, $image, $text, $comment_id);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Comment updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // closing the statement
    $stmt->close();
}

// Handled DELETE Request (delete a comment)
elseif ($method === 'DELETE') {
    // Decoded JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extracted comment_id from decoded JSON
    $comment_id = $data['comment_id'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("DELETE FROM Comments WHERE comment_id=?");
    $stmt->bind_param("i", $comment_id);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Comment deleted successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // closing the statement
    $stmt->close();
}

// Handled other requests
else {
    http_response_code(405);
    echo json_encode(array("message" => "Unsupported HTTP method"));
    exit;
}

// closing the database connection
$conn->close();
?>
