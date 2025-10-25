-- Cinema Paradiso Database Schema
-- Drop existing database if exists
DROP DATABASE IF EXISTS cinema_paradiso;
CREATE DATABASE cinema_paradiso;
USE cinema_paradiso;

-- Users Table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    bio TEXT,
    avatar_url VARCHAR(255),
    date_of_birth DATE,
    country VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Celebrities Table
CREATE TABLE celebrities (
    celebrity_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    birth_date DATE,
    biography TEXT,
    profile_image VARCHAR(255),
    nationality VARCHAR(50),
    profession VARCHAR(50), -- Actor, Director, Writer, Producer, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- Movies Table
CREATE TABLE movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    release_date DATE,
    duration INT, -- in minutes
    plot_summary TEXT,
    poster_url VARCHAR(255),
    trailer_url VARCHAR(255),
    genre VARCHAR(100), -- Could be normalized into separate table
    language VARCHAR(50),
    country VARCHAR(50),
    rating DECIMAL(3,1), -- Average rating (0.0 to 10.0)
    total_ratings INT DEFAULT 0,
    director_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (director_id) REFERENCES celebrities(celebrity_id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_release_date (release_date),
    INDEX idx_rating (rating)
);

-- TV Series Table
CREATE TABLE tv_series (
    series_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    first_air_date DATE,
    last_air_date DATE,
    number_of_seasons INT,
    number_of_episodes INT,
    plot_summary TEXT,
    poster_url VARCHAR(255),
    trailer_url VARCHAR(255),
    genre VARCHAR(100),
    language VARCHAR(50),
    country VARCHAR(50),
    rating DECIMAL(3,1),
    total_ratings INT DEFAULT 0,
    status VARCHAR(20), -- Ongoing, Ended, Cancelled
    creator_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES celebrities(celebrity_id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_first_air_date (first_air_date),
    INDEX idx_rating (rating)
);

-- Movie Cast (Links movies with celebrities)
CREATE TABLE movie_cast (
    cast_id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT NOT NULL,
    celebrity_id INT NOT NULL,
    role VARCHAR(100), -- Character name
    cast_type VARCHAR(20), -- Actor, Director, Writer, Producer
    cast_order INT, -- For ordering in credits
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (celebrity_id) REFERENCES celebrities(celebrity_id) ON DELETE CASCADE,
    INDEX idx_movie (movie_id),
    INDEX idx_celebrity (celebrity_id)
);

-- TV Series Cast (Links TV series with celebrities)
CREATE TABLE series_cast (
    cast_id INT PRIMARY KEY AUTO_INCREMENT,
    series_id INT NOT NULL,
    celebrity_id INT NOT NULL,
    role VARCHAR(100),
    cast_type VARCHAR(20),
    cast_order INT,
    FOREIGN KEY (series_id) REFERENCES tv_series(series_id) ON DELETE CASCADE,
    FOREIGN KEY (celebrity_id) REFERENCES celebrities(celebrity_id) ON DELETE CASCADE,
    INDEX idx_series (series_id),
    INDEX idx_celebrity (celebrity_id)
);

-- User Watchlist
CREATE TABLE watchlist (
    watchlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content_type ENUM('movie', 'tv_series') NOT NULL,
    content_id INT NOT NULL, -- movie_id or series_id
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_watchlist (user_id, content_type, content_id),
    INDEX idx_user (user_id),
    INDEX idx_content (content_type, content_id)
);

-- User Favorites
CREATE TABLE favorites (
    favorite_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content_type ENUM('movie', 'tv_series') NOT NULL,
    content_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, content_type, content_id),
    INDEX idx_user (user_id),
    INDEX idx_content (content_type, content_id)
);

-- Reviews Table
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content_type ENUM('movie', 'tv_series') NOT NULL,
    content_id INT NOT NULL,
    rating DECIMAL(3,1) NOT NULL, -- User's rating (0.0 to 10.0)
    review_title VARCHAR(255),
    review_text TEXT,
    is_spoiler BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (user_id, content_type, content_id),
    INDEX idx_user (user_id),
    INDEX idx_content (content_type, content_id),
    INDEX idx_rating (rating)
);

-- Review Likes (Users can like reviews)
CREATE TABLE review_likes (
    like_id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT NOT NULL,
    user_id INT NOT NULL,
    liked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (review_id, user_id),
    INDEX idx_review (review_id),
    INDEX idx_user (user_id)
);

-- User Lists (Custom movie/series lists)
CREATE TABLE user_lists (
    list_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    list_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
);

-- List Items (Movies/Series in user lists)
CREATE TABLE list_items (
    list_item_id INT PRIMARY KEY AUTO_INCREMENT,
    list_id INT NOT NULL,
    content_type ENUM('movie', 'tv_series') NOT NULL,
    content_id INT NOT NULL,
    item_order INT, -- For custom ordering
    notes TEXT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (list_id) REFERENCES user_lists(list_id) ON DELETE CASCADE,
    UNIQUE KEY unique_list_item (list_id, content_type, content_id),
    INDEX idx_list (list_id),
    INDEX idx_content (content_type, content_id)
);

-- Followers/Following Relationships
CREATE TABLE user_follows (
    follow_id INT PRIMARY KEY AUTO_INCREMENT,
    follower_id INT NOT NULL, -- User who follows
    following_id INT NOT NULL, -- User being followed
    followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_follow (follower_id, following_id),
    INDEX idx_follower (follower_id),
    INDEX idx_following (following_id),
    CHECK (follower_id != following_id)
);

-- Insert sample data
INSERT INTO users (username, email, password_hash, full_name, country) VALUES
('john_doe', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'John Doe', 'USA'),
('jane_smith', 'jane@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Jane Smith', 'UK'),
('movie_buff', 'buff@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Mike Johnson', 'Canada');

INSERT INTO celebrities (name, birth_date, nationality, profession) VALUES
('Christopher Nolan', '1970-07-30', 'British', 'Director'),
('Leonardo DiCaprio', '1974-11-11', 'American', 'Actor'),
('Quentin Tarantino', '1963-03-27', 'American', 'Director'),
('Margot Robbie', '1990-07-02', 'Australian', 'Actor');

INSERT INTO movies (title, release_date, duration, genre, language, country, rating, total_ratings, director_id) VALUES
('Inception', '2010-07-16', 148, 'Sci-Fi, Thriller', 'English', 'USA', 8.8, 2500, 1),
('The Dark Knight', '2008-07-18', 152, 'Action, Crime', 'English', 'USA', 9.0, 3000, 1),
('Pulp Fiction', '1994-10-14', 154, 'Crime, Drama', 'English', 'USA', 8.9, 2800, 3);

INSERT INTO tv_series (title, first_air_date, number_of_seasons, number_of_episodes, genre, language, status, rating, total_ratings) VALUES
('Breaking Bad', '2008-01-20', 5, 62, 'Crime, Drama', 'English', 'Ended', 9.5, 5000),
('Game of Thrones', '2011-04-17', 8, 73, 'Fantasy, Drama', 'English', 'Ended', 9.2, 4500);
