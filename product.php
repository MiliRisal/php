<?php
// Included the database connection
require_once('connection.php');

// Specified content type
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

// Handled POST Request (create new product)
if ($method === 'POST') {
    // Decoded JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Extracted data from decoded JSON
    $description = $data['description'];
    $image = $data['image'];
    $price = $data['price'];
    $shipping_cost = $data['shipping_cost'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("INSERT INTO Product (description, image, price, shipping_cost) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdd", $description, $image, $price, $shipping_cost);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Product added successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Closed the statement
    $stmt->close();
}

// Handled GET Request (retrieve existing products)
elseif ($method === 'GET') {
    // Prepared the SQL statement
    $stmt = $conn->prepare("SELECT * FROM Product");
    
    // Executed the statement
    $stmt->execute();
    
    // Binded the results
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

    // Closing the statement
    $stmt->close();
}

// Handled PUT Request (update data for existing product)
elseif ($method === 'PUT') {
    // Decoded JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extracted data from decoded JSON
    $product_id = $data['product_id'];
    $description = $data['description'];
    $image = $data['image'];
    $price = $data['price'];
    $shipping_cost = $data['shipping_cost'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("UPDATE Product SET description=?, image=?, price=?, shipping_cost=? WHERE product_id=?");
    $stmt->bind_param("ssddi", $description, $image, $price, $shipping_cost, $product_id);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Product updated successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

    // Closing the statement
    $stmt->close();
}

// Handled DELETE Request (delete a product)
elseif ($method === 'DELETE') {
    // Decode JSON data from request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extracted product_id from decoded JSON
    $product_id = $data['product_id'];

    // Prepared the SQL statement
    $stmt = $conn->prepare("DELETE FROM Product WHERE product_id=?");
    $stmt->bind_param("i", $product_id);

    // Executed the statement
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Product deleted successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $stmt->error));
    }

   // Closing the statement
    $stmt->close();
}

// Handled other requests
else {
    http_response_code(405);
    echo json_encode(array("message" => "Unsupported HTTP method"));
    exit;
}

// Closing the database connection
$conn->close();
?>
