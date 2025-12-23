DROP DATABASE IF EXISTS froglabs_shop;
CREATE DATABASE froglabs_shop;
USE froglabs_shop;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2),
    sale BOOLEAN DEFAULT FALSE,
    reviews INT DEFAULT 0,
    action_text VARCHAR(50)
);

INSERT INTO products (name, image, price, old_price, sale, reviews, action_text) VALUES
('Desert Rain Frog', 'desert-rain-frog.jpg', 7110.00, 8888.00, FALSE, 0, NULL),
('Wallace''s Flying Frog', 'wallaces-flying-frog.jpg', 10666.00, 118513.00, TRUE, 5, NULL),
('Giant Bullfrog', 'giant-african-bullfrog.jpg', 4444.00, 5925.00, TRUE, 0, NULL),
('Giant Monkey Frog', 'giant-monkey-frog.jpg', 7703.00, NULL, FALSE, 5, NULL),
('Red-Eyed Tree Frog', 'red-eyed-tree-frog.jpg', 3259.00, 4147.00, TRUE, 0, NULL),
('Amazon Milk Frog', 'amazon-milk-frog.jpg', 1481.00, 3555.00, FALSE, 0, NULL),
('Strawberry Poison Dart Frog', 'strawberry-poison-dart-frog.jpg', 4088.00, 5866.00, TRUE, 5, NULL),
('Tomato Frog', 'tomato-frog.jpg', 1481.00, NULL, FALSE, 5, NULL),
('Golden Poison Dart Frog', 'golden-dart-frog.jpg', 5328.00, NULL, FALSE, 0, NULL),
('Budgett''s Frog', 'budgett-frog.jpg', 2368.00, NULL, FALSE, 0, NULL);
