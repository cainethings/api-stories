<?php
function sanitizeFileName($title) {
    $title = trim($title);
    $title = preg_replace('/[^a-zA-Z0-9 ]/', '', $title); // Remove special characters
    $title = preg_replace('/\s+/', '_', $title); // Replace spaces with underscores
    return strtolower($title);
}

function createStory($title, $author, $content, $tags = []) {
    $storyDir = __DIR__ . '/../data/stories/';

    if (!is_dir($storyDir)) {
        mkdir($storyDir, 0777, true); // Create directory if it doesn’t exist
    }

    $fileName = sanitizeFileName($title) . '.json';
    $filePath = $storyDir . $fileName;

    if (file_exists($filePath)) {
        return ['error' => "Story already exists!"];
    }

    $timestamp = gmdate("Y-m-d\TH:i:s\Z");

    if (!is_array($tags)) {
        $tags = [$tags]; // Convert to array if a single string is passed
    }

    $storyData = [
        "title" => $title,
        "author" => $author,
        "created_at" => $timestamp,
        "updated_at" => $timestamp,
        "content" => $content,
        "status" => "published",
        "tags" => $tags,
        "views" => 0,  // New stories start with 0 views
        "comments" => [] // No comments initially
    ];

    file_put_contents($filePath, json_encode($storyData, JSON_PRETTY_PRINT));

    return [
        'success' => "Story '{$title}' created!",
        'file' => $filePath,
        'data' => $storyData
    ];
}

function getStory($storyName) {
    $filePath = __DIR__ . "/../data/stories/" . sanitizeFileName($storyName) . ".json";

    if (!file_exists($filePath)) {
        return ['error' => "Story not found!"];
    }

    $storyData = json_decode(file_get_contents($filePath), true);
    
    // Increment view count
    $storyData['views'] += 1;
    file_put_contents($filePath, json_encode($storyData, JSON_PRETTY_PRINT));

    return [
        'success' => "Story retrieved!",
        'data' => $storyData
    ];
}

function updateStory($storyName, $updates) {
    $filePath = __DIR__ . "/../data/stories/" . sanitizeFileName($storyName) . ".json";

    if (!file_exists($filePath)) {
        return ['error' => "Story not found!"];
    }

    $storyData = json_decode(file_get_contents($filePath), true);

    // Merge updates with existing data
    $storyData = array_merge($storyData, $updates);
    $storyData['updated_at'] = gmdate("Y-m-d\TH:i:s\Z");

    file_put_contents($filePath, json_encode($storyData, JSON_PRETTY_PRINT));

    return [
        'success' => "Story '{$storyName}' updated!",
        'data' => $storyData
    ];
}

function deleteStory($storyName) {
    $filePath = __DIR__ . "/../data/stories/" . sanitizeFileName($storyName) . ".json";

    if (!file_exists($filePath)) {
        return ['error' => "Story not found!"];
    }

    unlink($filePath);

    return ['success' => "Story '{$storyName}' deleted successfully!"];
}
?>
