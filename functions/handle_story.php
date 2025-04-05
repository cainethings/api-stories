<?php

// Define the directory for story JSON files
define('STORY_DIR', 'data/stories/');

// Function to create a new story
function createStory($title, $author, $content, $tags = [], $slug = null)
{
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
function getStory($slug)
{
    $story_file = STORY_DIR . $slug . '.json'; // Use slug to locate the story file

    // Check if the story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.'];
    }

    $story = json_decode(file_get_contents($story_file), true);
    return $story;
}

// Function to get all existing story
function getAllStories($limit = null, $offset = 0) {
    $stories = [];
    $files = glob(STORY_DIR . '*.json');

    // Filter out archived stories
    $files = array_filter($files, function($file) {
        return strpos($file, 'archive/') === false;
    });

    // Sort files by creation time descending
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    // Apply pagination
    $paginated = array_slice($files, $offset, $limit);

    foreach ($paginated as $file) {
        $story = json_decode(file_get_contents($file), true);
        if ($story) {
            $stories[] = $story;
        }
    }

    return [
        'stories' => $stories,
        'total' => count($files),
        'limit' => $limit,
        'offset' => $offset
    ];
}

// Function to update an existing story
function updateStory($slug, $updated_data)
{
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
function deleteStory($slug)
{
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


//Episodes
function addEpisode($slug, $episode_data)
{
    $story_file = STORY_DIR . $slug . '.json';

    // Check if the story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.'];
    }

    // Load the story
    $story = json_decode(file_get_contents($story_file), true);

    // Ensure episodes key exists
    if (!isset($story['episodes'])) {
        $story['episodes'] = [];
    }

    // Validate required fields in episode
    if (!isset($episode_data['title']) || !isset($episode_data['content'])) {
        return ['error' => 'Episode title and content are required.'];
    }

    // Add created_at timestamp
    $episode_data['created_at'] = date(DATE_ISO8601);

    // Append the episode
    $story['episodes'][] = $episode_data;

    $story['publish_status'] = 'published';
    // Update updated_at timestamp of the story
    $story['updated_at'] = date(DATE_ISO8601);

    // Save the updated story file
    if (file_put_contents($story_file, json_encode($story, JSON_PRETTY_PRINT))) {
        return ['message' => 'Episode added successfully.', 'episode_count' => count($story['episodes'])];
    }

    return ['error' => 'Error saving episode.'];
}
function updateEpisode($slug, $episode_index, $episode_data)
{
    $story_file = STORY_DIR . $slug . '.json';

    // Check if story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.'];
    }

    // Load story data
    $story = json_decode(file_get_contents($story_file), true);

    // Check if episodes exist
    if (!isset($story['episodes']) || !is_array($story['episodes'])) {
        return ['error' => 'No episodes found for this story.'];
    }

    // Validate episode index
    if (!isset($story['episodes'][$episode_index])) {
        return ['error' => 'Invalid episode index.'];
    }

    // Update episode fields if provided
    if (isset($episode_data['title'])) {
        $story['episodes'][$episode_index]['title'] = $episode_data['title'];
    }
    if (isset($episode_data['content'])) {
        $story['episodes'][$episode_index]['content'] = $episode_data['content'];
    }

    // Add/update timestamp
    $story['episodes'][$episode_index]['updated_at'] = date(DATE_ISO8601);

    // Update story's overall updated_at timestamp
    $story['updated_at'] = date(DATE_ISO8601);

    // Save updated story
    if (file_put_contents($story_file, json_encode($story, JSON_PRETTY_PRINT))) {
        return ['message' => 'Episode updated successfully.'];
    }

    return ['error' => 'Error updating episode.'];
}
function deleteEpisode($slug, $episode_index) {
    $story_file = STORY_DIR . $slug . '.json';

    // Check if story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.'];
    }

    // Load story data
    $story = json_decode(file_get_contents($story_file), true);

    // Check if episodes exist
    if (!isset($story['episodes']) || !is_array($story['episodes'])) {
        return ['error' => 'No episodes found for this story.'];
    }

    // Validate episode index
    if (!isset($story['episodes'][$episode_index])) {
        return ['error' => 'Invalid episode index.'];
    }

    // Remove the episode
    $story['episodes'][$episode_index]['publish_status'] = 'archived';
    // array_splice($story['episodes'], $episode_index, 1);

    // Update story's updated_at
    $story['updated_at'] = date(DATE_ISO8601);

    // Save updated story
    if (file_put_contents($story_file, json_encode($story, JSON_PRETTY_PRINT))) {
        return ['message' => 'Episode deleted successfully.'];
    }

    return ['error' => 'Error deleting episode.'];
}
function getEpisodes($slug, $limit = null, $offset = 0) {
    $story_file = STORY_DIR . $slug . '.json';

    // Check if the story exists
    if (!file_exists($story_file)) {
        return ['error' => 'Story not found.', 'story' => $story_file];
    }

    // Load story data
    $story = json_decode(file_get_contents($story_file), true);

    // Check if episodes exist
    if (!isset($story['episodes']) || !is_array($story['episodes'])) {
        return ['error' => 'No episodes found for this story.'];
    }

    // Apply pagination
    $episodes = array_slice($story['episodes'], $offset, $limit);

    return [
        'episodes' => $episodes,
        'total' => count($story['episodes']),
        'limit' => $limit,
        'offset' => $offset,
    ];
}

