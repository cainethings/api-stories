<?php
header("Content-Type: application/json");

// Load necessary files
require_once 'functions/handle_story.php';
require_once 'functions/handle_user.php';

// Parse request URI
$request_uri = explode('/', trim($_SERVER["REQUEST_URI"], '/'));
$resource = $request_uri[0] ?? null; // stories or users
$action = $request_uri[1] ?? null;   // create, view, update, delete
$param = $request_uri[2] ?? null;    // Story name or user ID
$param1 = $request_uri[3] ?? null;    // Story name or user ID
$param2 = $request_uri[4] ?? null;    // Story name or user ID
$param3 = $request_uri[5] ?? null;    // Story name or user ID

$method = $_SERVER['REQUEST_METHOD'];

// Route based on resource type
if ($resource === 'stories') {
    switch ($action) {
        case 'create':
            if ($method !== 'POST') {
                echo json_encode(['error' => 'Invalid request method. Use POST.']);
                exit;
            }
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['title'], $data['author'], $data['content'])) {
                echo json_encode(['error' => 'Missing required fields: title, author, content.']);
            } else {
                echo json_encode(createStory($data['title'], $data['author'], $data['content'], $data['tags'] ?? []));
            }
            break;

        case 'view':
            if (!$param) {
                echo json_encode(['error' => 'Story name is required.']);
            } else {
                echo json_encode(getStory($param));
            }
            break;

        case 'all':
            $limit = $param !== null ? intval($param) : null;
            $offset = $param1 !== null ? intval($param1) : 0;
            echo json_encode(getAllStories($limit, $offset));
            break;

        case 'update':
            if ($method !== 'PUT') {
                echo json_encode(['error' => 'Invalid request method. Use PUT.']);
                exit;
            }
            if (!$param) {
                echo json_encode(['error' => 'Story name is required.']);
                exit;
            }
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode(updateStory($param, $data ?? ''));
            break;

        case 'delete':
            if ($method !== 'DELETE') {
                echo json_encode(['error' => 'Invalid request method. Use DELETE.']);
                exit;
            }
            if (!$param) {
                echo json_encode(['error' => 'Story name is required.']);
                exit;
            }
            echo json_encode(deleteStory($param));
            break;

        case 'add-episode':
            if ($method !== 'POST' || !$param) {
                echo json_encode(['error' => 'Story slug and POST method required']);
                exit;
            }
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode(addEpisode($param, $data));
            break;

        case 'update-episode':
            if ($method !== 'PUT' || !$param || $param1 == null) {
                echo json_encode([
                    'error' => 'Slug, episode index, and PUT method required'
                ]);
                exit;
            }
            $episode_index = intval($param1);
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode(updateEpisode($param, $episode_index, $data));
            break;  

        case 'delete-episode':
            if ($method !== 'DELETE' || !$param || $param1 == null) {
                echo json_encode(['error' => 'Slug, episode index, and DELETE method required']);
                exit;
            }
            $episode_index = intval($param1);
            echo json_encode(deleteEpisode($param, $episode_index));
            break;
        case 'get-episodes':
            if (!$param) {
                echo json_encode(['error' => 'Story slug is required']);
                exit;
            }
        
            $limit = $param1 !== null ? intval($param1) : null;
            $offset = $param2 !== null ? intval($param2) : 0;
        
            echo json_encode(getEpisodes($param, $limit, $offset));
            break;                                
        default:
            echo json_encode(['error' => 'Invalid action. Available actions: create, view, update, delete.']);
            break;
    }
} elseif ($resource === 'users') {
    switch ($action) {
        case 'create':
            if ($method !== 'POST') {
                echo json_encode(['error' => 'Invalid request method. Use POST.']);
                exit;
            }
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) {
                echo json_encode(['error' => 'Invalid JSON payload.']);
                exit;
            }
            echo json_encode(createUser($data)); // Function in `handle_user.php`
            break;

        default:
            echo json_encode(['error' => 'Invalid action. Available actions: create.']);
            break;
    }
} else {
    echo json_encode(['error' => 'Invalid endpoint. Available resources: stories, users.']);
}
?>
