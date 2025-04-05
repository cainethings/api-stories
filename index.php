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
