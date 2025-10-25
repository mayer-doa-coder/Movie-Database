<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['content_type']) && isset($_GET['content_id'])) {
                // Get reviews for specific content
                $contentType = $_GET['content_type'];
                $contentId = (int)$_GET['content_id'];
                
                $sql = "SELECT r.*, u.username, u.full_name 
                    FROM reviews r 
                    JOIN users u ON r.user_id = u.user_id 
                    WHERE r.content_type = ? AND r.content_id = ? 
                    ORDER BY r.created_at DESC";
                
                $reviews = $db->fetchAll($sql, [$contentType, $contentId]);
                
                // Get average rating
                $avgRating = $db->fetchOne("SELECT AVG(rating) as avg_rating 
                    FROM reviews 
                    WHERE content_type = ? AND content_id = ?", 
                    [$contentType, $contentId]);
                
                echo json_encode([
                    'success' => true,
                    'data' => $reviews,
                    'average_rating' => round($avgRating['avg_rating'], 1),
                    'total_reviews' => count($reviews)
                ]);
                
            } elseif (isset($_GET['user_id'])) {
                // Get reviews by user
                $userId = (int)$_GET['user_id'];
                
                $sql = "SELECT r.*, 
                    CASE 
                        WHEN r.content_type = 'movie' THEN (SELECT title FROM movies WHERE movie_id = r.content_id)
                        WHEN r.content_type = 'tv_series' THEN (SELECT title FROM tv_series WHERE series_id = r.content_id)
                    END as content_title
                    FROM reviews r 
                    WHERE r.user_id = ? 
                    ORDER BY r.created_at DESC";
                
                $reviews = $db->fetchAll($sql, [$userId]);
                echo json_encode(['success' => true, 'data' => $reviews]);
                
            } else {
                // Get all recent reviews
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                
                $sql = "SELECT r.*, u.username, u.full_name,
                    CASE 
                        WHEN r.content_type = 'movie' THEN (SELECT title FROM movies WHERE movie_id = r.content_id)
                        WHEN r.content_type = 'tv_series' THEN (SELECT title FROM tv_series WHERE series_id = r.content_id)
                    END as content_title
                    FROM reviews r 
                    JOIN users u ON r.user_id = u.user_id 
                    ORDER BY r.created_at DESC 
                    LIMIT $limit";
                
                $reviews = $db->fetchAll($sql);
                echo json_encode(['success' => true, 'data' => $reviews]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['user_id']) || !isset($data['content_type']) || 
                !isset($data['content_id']) || !isset($data['rating'])) {
                throw new Exception('Missing required fields');
            }
            
            $sql = "INSERT INTO reviews (user_id, content_type, content_id, rating, 
                    review_title, review_text, is_spoiler) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['user_id'],
                $data['content_type'],
                $data['content_id'],
                $data['rating'],
                $data['review_title'] ?? null,
                $data['review_text'] ?? null,
                $data['is_spoiler'] ?? false
            ];
            
            $result = $db->execute($sql, $params);
            
            // Update content rating
            updateContentRating($db, $data['content_type'], $data['content_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Review created successfully',
                'review_id' => $result['insert_id']
            ]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['review_id'])) {
                throw new Exception('Review ID is required');
            }
            
            $updates = [];
            $params = [];
            
            $allowedFields = ['rating', 'review_title', 'review_text', 'is_spoiler'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $params[] = $data['review_id'];
            $sql = "UPDATE reviews SET " . implode(', ', $updates) . " WHERE review_id = ?";
            
            $db->execute($sql, $params);
            
            // Get review to update content rating
            $review = $db->fetchOne("SELECT content_type, content_id FROM reviews WHERE review_id = ?", [$data['review_id']]);
            if ($review) {
                updateContentRating($db, $review['content_type'], $review['content_id']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Review updated successfully']);
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['review_id'])) {
                throw new Exception('Review ID is required');
            }
            
            // Get review before deleting
            $review = $db->fetchOne("SELECT content_type, content_id FROM reviews WHERE review_id = ?", [$data['review_id']]);
            
            $db->execute("DELETE FROM reviews WHERE review_id = ?", [$data['review_id']]);
            
            if ($review) {
                updateContentRating($db, $review['content_type'], $review['content_id']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function updateContentRating($db, $contentType, $contentId) {
    // Calculate average rating
    $result = $db->fetchOne(
        "SELECT AVG(rating) as avg_rating, COUNT(*) as total 
         FROM reviews 
         WHERE content_type = ? AND content_id = ?",
        [$contentType, $contentId]
    );
    
    $avgRating = $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
    $totalRatings = $result['total'];
    
    // Update the content table
    if ($contentType === 'movie') {
        $db->execute(
            "UPDATE movies SET rating = ?, total_ratings = ? WHERE movie_id = ?",
            [$avgRating, $totalRatings, $contentId]
        );
    } elseif ($contentType === 'tv_series') {
        $db->execute(
            "UPDATE tv_series SET rating = ?, total_ratings = ? WHERE series_id = ?",
            [$avgRating, $totalRatings, $contentId]
        );
    }
}
?>
