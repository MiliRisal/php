<?php
// Included the database connection
require_once('connection.php');

// Specifed content type
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

// Handled POST Request (create new cart)
if ($method === 'POST') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extracted data from decoded JSON
    $user_id = $data['user_id'];
    $products = $data['products'];
    $quantities = $data['quantities'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("INSERT INTO Cart (user, products, quantities) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $products, $quantities);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Cart added successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // closing the statement
    $stmt->close();
}

// Handled GET Request (retrieve existing carts)
elseif ($method === 'GET') {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM Cart");
    
    // Executed the statement
    $stmt->execute();
    
    // Binded the results
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $carts = array();
        while($row = $result->fetch_assoc()) {
            $carts[] = $row;
        }
        echo json_encode($carts);
    } else {
        echo json_encode(array("message" => "No cart found"));
    }

    // closing the statement
    $stmt->close();
}

// Handled PUT Request (update data for existing cart)
elseif ($method === 'PUT') {
    // Decoded JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extracted data from decoded JSON
    $cart_id = $data['cart_id'];
    $user_id = $data['user_id'];
    $products = $data['products'];
    $quantities = $data['quantities'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("UPDATE Cart SET user=?, products=?, quantities=? WHERE cart_id=?");
    $stmt->bind_param("iiii", $user_id, $products, $quantities, $cart_id);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Cart updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // closing the statement
    $stmt->close();
}

// Handled DELETE Request (delete a cart)
elseif ($method === 'DELETE') {
    // Decoded JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extracted cart_id from decoded JSON
    $cart_id = $data['cart_id'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("DELETE FROM Cart WHERE cart_id=?");
    $stmt->bind_param("i", $cart_id);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Cart deleted successfully"));
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
