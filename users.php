<?php
// Include the database connection
require_once('connection.php');

// Specify content type
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

// Handle POST Request (create new user)
if ($method === 'POST') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data from decoded JSON
    $email = $data['email'];
    $password = $data['password'];
    $username = $data['username'];
    $purchase_history = $data['purchase_history'];
    $shipping_address = $data['shipping_address'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO Users (email, password, username, purchase_history, shipping_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $password, $username, $purchase_history, $shipping_address);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "User added successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle GET Request (retrieve existing users)
elseif ($method === 'GET') {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM Users");
    
    // Execute the statement
    $stmt->execute();
    
    // Bind the results
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $users = array();
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
    } else {
        echo json_encode(array());
    }

    // Close the statement
    $stmt->close();
}

// Handle PUT Request (update data for existing user)
elseif ($method === 'PUT') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract data from decoded JSON
    $user_id = $data['user_id'];
    $email = $data['email'];
    $password = $data['password'];
    $username = $data['username'];
    $purchase_history = $data['purchase_history'];
    $shipping_address = $data['shipping_address'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Users SET email=?, password=?, username=?, purchase_history=?, shipping_address=? WHERE user_id=?");
    $stmt->bind_param("sssss", $email, $password, $username, $purchase_history, $shipping_address, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "User updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle DELETE Request (delete a user)
elseif ($method === 'DELETE') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract user_id from decoded JSON
    $user_id = $data['user_id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("DELETE FROM Users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "User deleted successfully"));
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
