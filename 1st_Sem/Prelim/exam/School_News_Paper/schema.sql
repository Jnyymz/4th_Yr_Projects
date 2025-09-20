-- 1. Users table
CREATE TABLE school_publication_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Article Categories table
CREATE TABLE article_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Articles table
CREATE TABLE articles (
    article_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_path VARCHAR(255) DEFAULT NULL,
    category_id INT DEFAULT NULL,
    FOREIGN KEY (author_id) REFERENCES school_publication_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES article_categories(category_id) ON DELETE SET NULL
);

-- 4. Article Access Requests table
CREATE TABLE article_access_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    requester_id INT NOT NULL,
    owner_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(article_id) ON DELETE CASCADE,
    FOREIGN KEY (requester_id) REFERENCES school_publication_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES school_publication_users(user_id) ON DELETE CASCADE
);

-- 5. Deleted Articles table
CREATE TABLE deleted_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES school_publication_users(user_id) ON DELETE CASCADE
);

-- 6. Article-Category Mapping table
CREATE TABLE article_category_map (
    article_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (article_id, category_id),
    FOREIGN KEY (article_id) REFERENCES articles(article_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES article_categories(category_id) ON DELETE CASCADE
);
