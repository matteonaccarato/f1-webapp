CREATE TABLE IF NOT EXISTS cookies (
    id CHAR(255) NOT NULL,
    value CHAR(255) NOT NULL,
    expiration_date INT NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    logo_url VARCHAR(255) NULL,
    color_rgb_value CHAR(11) NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT,
    first_name VARCHAR(255) NULL,
    last_name VARCHAR(255) NULL,
    email VARCHAR(255) NOT NULL,
    password CHAR(255) NOT NULL,
    role INT NOT NULL,
    date_of_birth CHAR(10) NULL,
    cookie_id CHAR(255) NULL,
    img_url VARCHAR(255) NULL,
    newsletter INT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY email (email),
    UNIQUE KEY cookie_id (cookie_id),
    CONSTRAINT users_ibfk_1 FOREIGN KEY (cookie_id) REFERENCES cookies (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    description VARCHAR(500) NULL,
    price INT NOT NULL,
    img_url VARCHAR(700) NULL,
    team_id INT NOT NULL,
    color VARCHAR(20) NULL,
    size VARCHAR(20) NOT NULL DEFAULT 'one',
    alt VARCHAR(400) NULL,
    PRIMARY KEY (id),
    CONSTRAINT team___fk FOREIGN KEY (team_id) REFERENCES teams (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id CHAR(5) NOT NULL,
    user_id INT NOT NULL,
    date CHAR(19) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    amount DECIMAL NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders_products (
    order_id CHAR(5) NOT NULL,
    product_id INT NOT NULL,
    size VARCHAR(5) NOT NULL,
    quantity INT NOT NULL,
    unit_price INT NOT NULL,
    PRIMARY KEY (order_id, product_id, size),
    CONSTRAINT orders_products_orders_id_fk FOREIGN KEY (order_id) REFERENCES orders (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT orders_products_products_id_fk FOREIGN KEY (product_id) REFERENCES products (id)
        ON UPDATE CASCADE
);
