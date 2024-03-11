<?php
// Include the database connection
require_once('connection.php');

// Specify content type
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

// Handle POST Request (create new order)
if ($method === 'POST') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data from decoded JSON
    $user_id = $data['user_id'];
    $products = $data['products'];
    $quantities = $data['quantities'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO `Order` (user, products, quantities) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $products, $quantities);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Order placed successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle GET Request (retrieve existing orders)
elseif ($method === 'GET') {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM `Order`");
    
    // Execute the statement
    $stmt->execute();
    
    // Bind the results
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $orders = array();
        while($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        echo json_encode($orders);
    } else {
        echo json_encode(array("message" => "No order found"));
    }

    // Close the statement
    $stmt->close();
}

// Handle PUT Request (update data for existing order)
elseif ($method === 'PUT') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract data from decoded JSON
    $order_id = $data['order_id'];
    $user_id = $data['user_id'];
    $products = $data['products'];
    $quantities = $data['quantities'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE `Order` SET user=?, products=?, quantities=? WHERE order_id=?");
    $stmt->bind_param("iiii", $user_id, $products, $quantities, $order_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Order updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle DELETE Request (delete an order)
elseif ($method === 'DELETE') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract order_id from decoded JSON
    $order_id = $data['order_id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("DELETE FROM `Order` WHERE order_id=?");
    $stmt->bind_param("i", $order_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Order deleted successfully"));
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
