CREATE TABLE Product (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255),
    image VARCHAR(255),
    price DECIMAL(10, 2),
    shipping_cost DECIMAL(10, 2)
);

CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    username VARCHAR(255),
    purchase_history TEXT,
    shipping_address TEXT
);

CREATE TABLE Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    product INT,
    user INT,
    rating INT,
    image VARCHAR(255),
    text TEXT,
    FOREIGN KEY (product) REFERENCES Product(product_id),
    FOREIGN KEY (user) REFERENCES Users(user_id)
);

CREATE TABLE Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user INT,
    products INT,
    quantities INT,
    FOREIGN KEY (user) REFERENCES Users(user_id),
    FOREIGN KEY (products) REFERENCES Product(product_id)
);

CREATE TABLE `Order` (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user INT,
    products INT,
    quantities INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user) REFERENCES Users(user_id),
    FOREIGN KEY (products) REFERENCES Product(product_id)
);