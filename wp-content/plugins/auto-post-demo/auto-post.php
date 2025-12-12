<?php
/*
Plugin Name: Auto Blog Demo
Description: Generates draft blog posts with AI-generated paragraphs and topic-based images.
Version: 1.5
Author: Shan
*/

if (!defined('ABSPATH'))
    exit;

// Admin Menu
function auto_post_demo_page()
{ ?>
    <div class="wrap">
        <h1>Auto Blog Demo</h1>
        <form method="post">
            <?php wp_nonce_field('auto_post_demo_action', 'auto_post_demo_nonce'); ?>

            <label for="post_topic"><strong>Select Topic:</strong></label>
            <select name="post_topic" id="post_topic">
                <option value="coloring">Coloring</option>
                <option value="DIY">DIY</option>
                <option value="art">Art & Craft</option>
                <option value="recipes">Recipes</option>
                <option value="fitness">Fitness</option>
                <option value="travel">Travel</option>
                <option value="tech">Tech Tips</option>
            </select>
            <br><br>

            <input type="submit" name="generate_post" class="button button-primary" value="Generate Blog Post">
            <input type="submit" name="generate_bulk" class="button" value="Generate 3 Posts">
        </form>
    </div>
    <?php
    if (isset($_POST['generate_post'])) {
        auto_post_demo_handle_generation(1);
    }
    if (isset($_POST['generate_bulk'])) {
        auto_post_demo_handle_generation(3);
    }
}

add_action('admin_menu', function () {
    add_menu_page(
        'Auto Blog Demo',
        'Auto Blog Demo',
        'manage_options',
        'auto-post-demo',
        'auto_post_demo_page',
        'dashicons-admin-post',
        100
    );
});

// Handle post generation
function auto_post_demo_handle_generation($count = 1)
{
    if (!isset($_POST['auto_post_demo_nonce']) || !wp_verify_nonce($_POST['auto_post_demo_nonce'], 'auto_post_demo_action')) {
        echo '<div class="notice notice-error"><p>Invalid request.</p></div>';
        return;
    }

    $topic = isset($_POST['post_topic']) ? sanitize_text_field($_POST['post_topic']) : 'coloring';

    $topics_data = [
        'coloring' => [
            'titles' => ["Top 10 Coloring Tips for Kids", "Creative Coloring Ideas for All Ages", "How to Make Your Own Coloring Pages", "Fun and Relaxing Coloring Techniques", "Coloring Activities to Boost Creativity"],
            'tags' => ['coloring', 'kids', 'creative', 'fun', 'art']
        ],
        'DIY' => [
            'titles' => ["Easy DIY Projects for Beginners", "Creative DIY Crafts for Home", "DIY Organization Ideas", "Step-by-Step DIY Tutorials", "Fun DIY Activities for Kids"],
            'tags' => ['DIY', 'craft', 'creative', 'home', 'kids']
        ],
        'art' => [
            'titles' => ["Beginner-Friendly Art Projects", "Creative Art Ideas for Home", "Fun Drawing and Painting Activities", "Exploring Modern Art Techniques", "How to Start Your Art Journey"],
            'tags' => ['art', 'creative', 'painting', 'drawing', 'inspiration']
        ],
        'recipes' => [
            'titles' => ["5 Easy Dinner Recipes", "Healthy Breakfast Ideas", "Quick Snacks for Busy Days", "Delicious Desserts You Can Make at Home", "Top 10 Comfort Food Recipes"],
            'tags' => ['recipes', 'cooking', 'food', 'easy', 'healthy']
        ],
        'fitness' => [
            'titles' => ["Beginner Workout Routines", "Quick 20-Minute Home Workouts", "Strength Training Tips for Beginners", "Cardio Exercises for Fat Loss", "Staying Motivated in Your Fitness Journey"],
            'tags' => ['fitness', 'workout', 'health', 'exercise', 'motivation']
        ],
        'travel' => [
            'titles' => ["Top 5 Weekend Getaways", "Travel Tips for First-Time Travelers", "Budget-Friendly Travel Ideas", "Must-See Destinations This Year", "Packing Hacks for Stress-Free Travel"],
            'tags' => ['travel', 'tips', 'adventure', 'vacation', 'explore']
        ],
        'tech' => [
            'titles' => ["Top 5 Productivity Apps", "Essential Tools for Remote Work", "How to Stay Safe Online", "Tech Hacks to Simplify Your Day", "Beginner’s Guide to Coding"],
            'tags' => ['tech', 'gadgets', 'productivity', 'coding', 'tips']
        ]
    ];

    $topic_images = [
        'coloring' => ["https://images.pexels.com/photos/8014299/pexels-photo-8014299.jpeg", "https://images.pexels.com/photos/8036831/pexels-photo-8036831.jpeg", "https://images.pexels.com/photos/5274622/pexels-photo-5274622.jpeg", "https://images.pexels.com/photos/159570/crayons-coloring-book-coloring-book-159570.jpeg"],
        'DIY' => ["https://images.pexels.com/photos/35150382/pexels-photo-35150382.jpeg", "https://images.pexels.com/photos/35140769/pexels-photo-35140769.jpeg", "https://images.pexels.com/photos/982660/pexels-photo-982660.jpeg", "https://images.pexels.com/photos/1109354/pexels-photo-1109354.jpeg"],
        'art' => ["https://images.pexels.com/photos/161154/stained-glass-spiral-circle-pattern-161154.jpeg", "https://images.pexels.com/photos/20967/pexels-photo.jpg", "https://picsum.photos/seed/art3/1200/800"],
        'recipes' => ["https://images.pexels.com/photos/5737464/pexels-photo-5737464.jpeg", "https://images.pexels.com/photos/31261499/pexels-photo-31261499.jpeg", "https://images.pexels.com/photos/65170/pexels-photo-65170.jpeg", "https://images.pexels.com/photos/14935376/pexels-photo-14935376.jpeg"],
        'fitness' => ["https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg", "https://images.pexels.com/photos/221247/pexels-photo-221247.jpeg", "https://images.pexels.com/photos/669584/pexels-photo-669584.jpeg", "https://images.pexels.com/photos/897064/pexels-photo-897064.jpeg"],
        'travel' => ["https://images.pexels.com/photos/346885/pexels-photo-346885.jpeg", "https://images.pexels.com/photos/1058959/pexels-photo-1058959.jpeg", "https://images.pexels.com/photos/2104152/pexels-photo-2104152.jpeg", "https://images.pexels.com/photos/21014/pexels-photo.jpg"],
        'tech' => ["https://images.pexels.com/photos/39284/macbook-apple-imac-computer-39284.jpeg", "https://images.pexels.com/photos/7974/pexels-photo.jpg", "https://images.pexels.com/photos/943096/pexels-photo-943096.jpeg", "https://images.pexels.com/photos/32698507/pexels-photo-32698507.jpeg"]
    ];

    $selected_data = $topics_data[$topic];
    $titles = $selected_data['titles'];
    $tags_pool = $selected_data['tags'];

    for ($n = 0; $n < $count; $n++) {

        $title = $titles[array_rand($titles)];

        // Generate AI paragraphs
        $ai_paragraphs = generate_ai_paragraphs($topic);
        $content = "<h2>$title</h2>";
        foreach ($ai_paragraphs as $p) {
            $content .= "<p>$p</p>";
        }

        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_content' => wpautop($content),
            'post_status' => 'draft',
            'post_author' => 1
        ]);

        if (is_wp_error($post_id))
            continue;

        wp_update_post(['ID' => $post_id, 'post_name' => sanitize_title($title)]);

        $category_id = get_cat_ID('Blog') ?: wp_create_category('Blog');
        wp_set_post_categories($post_id, [$category_id]);

        shuffle($tags_pool);
        wp_set_post_tags($post_id, array_slice($tags_pool, 0, 3));

        update_post_meta($post_id, 'summary', 'This is a blog summary for the auto-generated post.');

        // Attach main image + 3 extra images
        $images_for_topic = $topic_images[$topic];
        shuffle($images_for_topic);
        $selected_images = array_slice($images_for_topic, 0, 4);

        $content .= '<div class="auto-post-images" style="display:flex;gap:10px;flex-wrap:wrap;">';
        foreach ($selected_images as $index => $img_url) {
            $image_id = auto_post_demo_generate_image_and_attach($post_id, $img_url);
            if ($image_id) {
                if ($index === 0)
                    set_post_thumbnail($post_id, $image_id);
                $content .= wp_get_attachment_image($image_id, 'medium');
            }
        }
        $content .= '</div>';

        wp_update_post(['ID' => $post_id, 'post_content' => wpautop($content)]);

        $preview_link = get_preview_post_link($post_id);
        echo '<div class="notice notice-success">';
        echo '<p>Blog post created successfully! <a href="' . esc_url($preview_link) . '" target="_blank">Preview Post</a></p>';
        echo '</div>';
    }
}


// Image generation
function auto_post_demo_generate_image_and_attach($post_id, $image_url)
{

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $tmp = download_url($image_url);
    if (is_wp_error($tmp))
        return false;

    $file = [
        'name' => "auto-image-" . wp_rand(1000, 9999) . ".jpg",
        'type' => 'image/jpeg',
        'tmp_name' => $tmp,
        'error' => 0,
        'size' => filesize($tmp)
    ];

    $image_id = media_handle_sideload($file, $post_id);
    if (is_wp_error($image_id)) {
        @unlink($tmp);
        return false;
    }
    return $image_id;
}

// AI paragraph generation using Gemini (Google Generative AI)
function generate_ai_paragraphs($topic)
{
    $api_key = GEMINI_KEY;
    $model = 'text-bison-001';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateText";

    $prompt = "Write 3 short blog paragraphs (3-5 sentences each) about '$topic' in a friendly blog style.";

    $body = json_encode([
        "prompt" => ["text" => $prompt],
        "temperature" => 0.7,
        "candidate_count" => 1
    ]);

    $response = wp_remote_post($url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ],
        'body' => $body,
        'timeout' => 20
    ]);

    if (is_wp_error($response)) {
        return ["Failed to get AI response."];
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    // Extract AI-generated text
    if (isset($data['candidates'][0]['output'])) {
        $text = $data['candidates'][0]['output'];
        // Split into paragraphs (assuming line breaks)
        $paragraphs = array_filter(array_map('trim', explode("\n", $text)));
        if (count($paragraphs) > 0) {
            return $paragraphs;
        }
    }

    return ["No AI content generated."];
}

