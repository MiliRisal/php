<?php
// Include the database connection
require_once('connection.php');

// Specify content type
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

// Handle POST Request (create new product)
if ($method === 'POST') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data from decoded JSON
    $description = $data['description'];
    $image = $data['image'];
    $price = $data['price'];
    $shipping_cost = $data['shipping_cost'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO Product (description, image, price, shipping_cost) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdd", $description, $image, $price, $shipping_cost);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Product added successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle GET Request (retrieve existing products)
elseif ($method === 'GET') {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM Product");
    
    // Execute the statement
    $stmt->execute();
    
    // Bind the results
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $products = array();
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        echo json_encode(array());
    }

    // Close the statement
    $stmt->close();
}

// Handle PUT Request (update data for existing product)
elseif ($method === 'PUT') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract data from decoded JSON
    $product_id = $data['product_id'];
    $description = $data['description'];
    $image = $data['image'];
    $price = $data['price'];
    $shipping_cost = $data['shipping_cost'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Product SET description=?, image=?, price=?, shipping_cost=? WHERE product_id=?");
    $stmt->bind_param("ssddi", $description, $image, $price, $shipping_cost, $product_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Product updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Close the statement
    $stmt->close();
}

// Handle DELETE Request (delete a product)
elseif ($method === 'DELETE') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract product_id from decoded JSON
    $product_id = $data['product_id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("DELETE FROM Product WHERE product_id=?");
    $stmt->bind_param("i", $product_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Product deleted successfully"));
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
