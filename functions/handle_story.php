<?php

// Define the directory for story JSON files
define('STORY_DIR', 'data/stories/');

// Function to create a new story
function createStory($title, $author, $content, $tags = [], $slug = null) {
    // Use provided slug or generate one from the title if not provided
    $slug = $slug ?: strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', $title))); // Generate slug
    $story_file = STORY_DIR . $slug . '.json'; // Use slug as the filename

    // Check if the story already exists
    if (file_exists($story_file)) {
        return ['error' => 'Story already exists.'];
    }

    // Prepare story data
    $story = [
        'title' => $title,
        'author' => $author,
        'slug' => $slug, // Include slug
        'created_at' => date(DATE_ISO8601), // Current timestamp
        'updated_at' => date(DATE_ISO8601), // Current timestamp
        'content' => $content,
        'status' => 'published', // Default status
        'tags' => $tags,
        'views' => 0, // Initial view count
        'comments' => [] // Empty comments array
    ];

    // Save story as JSON file
    if (file_put_contents($story_file, json_encode($story, JSON_PRETTY_PRINT))) {
        return ['message' => 'Story created successfully.', 'slug' => $slug];
    }

    return ['error' => 'Error creating story.'];
}

// Function to retrieve a specific story
function getStory($slug) {
    $story_file = STORY_DIR . $slug . '.json'; // Use slug to locate the story file

    // Check if the story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.'];
    }

    $story = json_decode(file_get_contents($story_file), true);
    return $story;
}

// Function to update an existing story
function updateStory($slug, $updated_data) {
    $story_file = STORY_DIR . $slug . '.json'; // Use slug to locate the story file

    // Check if the story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.'];
    }

    // Get existing story
    $story = json_decode(file_get_contents($story_file), true);
    
    // Update story fields with provided data, if exists
    if (isset($updated_data['title'])) {
        $story['title'] = $updated_data['title'];
        // Update slug based on new title if no custom slug is provided
        // Note: This won't update the filename unless you handle that in your routing
        $story['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', $story['title'])));
    }
    if (isset($updated_data['content'])) {
        $story['content'] = $updated_data['content'];
    }
    if (isset($updated_data['status'])) {
        $story['status'] = $updated_data['status'];
    }
    if (isset($updated_data['tags'])) {
        $story['tags'] = $updated_data['tags'];
    }
    if (isset($updated_data['views'])) {
        $story['views'] = $updated_data['views'];
    }

    // Update the updated_at timestamp
    $story['updated_at'] = date(DATE_ISO8601);

    // Save updated story    
    if (file_put_contents($story_file, json_encode($story, JSON_PRETTY_PRINT))) {
        return ['message' => 'Story updated successfully.', 'file' => $story_file, 'data' => $updated_data];
    }

    return ['error' => 'Error updating story.'];
}

// Function to delete a story
function deleteStory($slug) {
    $story_file = STORY_DIR . $slug . '.json';
    $archive_dir = STORY_DIR . 'archive/';

    // Check if the story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.'];
    }

    // Ensure archive folder exists
    if (!is_dir($archive_dir)) {
        mkdir($archive_dir, 0755, true);
    }

    // Target file path in archive folder
    $archived_file = $archive_dir . $slug . '_' . date('Ymd_His') . '.json';

    // Move the file instead of deleting
    if (rename($story_file, $archived_file)) {
        return ['message' => 'Story moved to archive successfully.'];
    }

    return ['error' => 'Error archiving story.'];
}
