# PHP Blog Project

A glassmorphism-themed blogging platform built with vanilla PHP and MySQL. It supports user registration, authentication, writing posts with a Markdown editor, liking posts, and browsing a curated "Top Liked" carousel.

## Features
- **User accounts**: registration, login, logout, and profile management.
- **Markdown editing**: SimpleMDE integration for composing posts with live preview.
- **Image uploads**: optional cover images per post.
- **Like system**: visitors can like posts; the most popular content appears in a dedicated carousel.
- **Glass UI**: cohesive styling for navigation, auth forms, post cards, and profile pages.

## Tech Stack
- PHP 8+
- MySQL 5.7+/MariaDB (tested via XAMPP)
- HTML5, CSS3
- JavaScript (Showdown + SimpleMDE)

## Project Structure
```
php_blog_project/
├── assets/            # CSS assets shared across the site
├── components/        # Reusable PHP partials (navbar, footer, like button, etc.)
├── pages/             # Auth, CRUD, and profile pages
├── uploads/           # Uploaded post images and profile avatars
├── config.php         # Database connection details
├── index.php          # Home page
└── README.md
```

## Getting Started

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/) or a local LAMP/WAMP stack with PHP 8+
- MySQL/MariaDB server

### 2. Clone or Copy the Project
Place the project inside your web root (for XAMPP on Windows this is typically `C:/xampp/htdocs/`).

```bash
cd C:/xampp/htdocs
git clone https://github.com/Chamodi-Karunarathne/php_blog_project.git
```

### 3. Configure the Database
1. Start Apache and MySQL from the XAMPP control panel.
2. Create the database (default name: `php_blog`). You can do this in phpMyAdmin:
   ```sql
   CREATE DATABASE php_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Create the required tables. The following schema reflects the current application code:

   ```sql
   USE php_blog;

   CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(60) NOT NULL UNIQUE,
     display_name VARCHAR(100) NOT NULL,
     email VARCHAR(120) NOT NULL UNIQUE,
     password VARCHAR(255) NOT NULL,
     role ENUM('admin','user') DEFAULT 'user',
     profile_image VARCHAR(255) DEFAULT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE posts (
     id INT AUTO_INCREMENT PRIMARY KEY,
     user_id INT NOT NULL,
     title VARCHAR(180) NOT NULL,
     content MEDIUMTEXT NOT NULL,
     image VARCHAR(255) DEFAULT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
   );

   CREATE TABLE likes (
     id INT AUTO_INCREMENT PRIMARY KEY,
     user_id INT NOT NULL,
     post_id INT NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     UNIQUE KEY uniq_like (user_id, post_id),
     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
     FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
   );
   ```

4. Update `config.php` if your MySQL credentials differ:

   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $dbname = "php_blog";
   ```

### 4. Access the Application
- Visit `http://localhost/php_blog_project/` in your browser.
- Register a new account or log in if you already created one.

## Optional Setup Tips
- **Uploads directory**: ensure `uploads/` is writable by the web server for image uploads (`chmod 775 uploads` on Unix-like systems).
- **Sample data**: add a few posts and likes to see the carousel populate.
- **Environment separation**: for production deployments, move database credentials to environment variables or an `.env` file and update `config.php` accordingly.

## Troubleshooting
- **Database errors**: check that the database and tables exist and that `config.php` contains the correct credentials.
- **Uploads failing**: confirm that `uploads/` has proper write permissions and that `file_uploads` is enabled in `php.ini`.
- **Missing assets**: clear your browser cache or ensure the `assets/` folder is accessible via the correct relative paths.

## License
This project is distributed under the MIT License. Feel free to fork, extend, and adapt it for your own blogging experiments.
