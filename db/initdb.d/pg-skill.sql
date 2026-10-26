DROP TABLE IF EXISTS issues_tags;
DROP TABLE IF EXISTS issues;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS users;

-- users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE
);

-- issues table
CREATE TABLE issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT,

    CONSTRAINT fk_issues_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

-- tags table
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

-- issues_tags table
CREATE TABLE issues_tags (
    issue_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (issue_id, tag_id),

    CONSTRAINT fk_issue
        FOREIGN KEY (issue_id)
        REFERENCES issues(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_tag
        FOREIGN KEY (tag_id)
        REFERENCES tags(id)
        ON DELETE CASCADE
);

-- users data
INSERT INTO users (email) VALUES
('test1@example.com'),
('test2@example.com');

-- tags data
INSERT INTO tags (title) VALUES
('tag A'),
('tag B'),
('tag C');
