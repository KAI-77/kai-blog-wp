<?php
/*
Plugin Name: Auto Blog Demo
Description: Generates realistic-looking draft blog posts with topic-based content and multiple images.
Version: 1.5
Author: Shan
*/

if (!defined('ABSPATH'))
    exit;

// Admin Menu
function auto_post_demo_page()
{
    ob_start(); // prevents empty rectangle flash
    ?>
    <div class="wrap" style="max-width:650px;">
        <h1 style="margin-bottom:20px;">Auto Blog Demo</h1>

        <form method="post" style="background:#fff;padding:20px;border-radius:8px;border:1px solid #ddd;">
            <?php wp_nonce_field('auto_post_demo_action', 'auto_post_demo_nonce'); ?>

            <div style="margin-bottom:20px;">
                <label for="post_topic" style="font-weight:600;font-size:15px;margin-bottom:6px;display:block;">Select
                    Topic</label><br>
                <select name="post_topic" id="post_topic"
                    style="width:100%;padding:8px 10px;font-size:14px;border:1px solid #ccc;border-radius:6px;">
                    <option value="coloring">Coloring</option>
                    <option value="DIY">DIY</option>
                    <option value="art">Art & Craft</option>
                    <option value="recipes">Recipes</option>
                    <option value="fitness">Fitness</option>
                    <option value="travel">Travel</option>
                    <option value="tech">Tech Tips</option>
                </select>
            </div>

            <div style="display:flex;gap:10px;">
                <input type="submit" name="generate_post" class="button button-primary"
                    style="padding:8px 16px;font-size:14px;border-radius:6px;" value="Generate 1 Post">

                <input type="submit" name="generate_bulk" class="button"
                    style="padding:8px 16px;font-size:14px;border-radius:6px;" value="Generate 3 Posts">
            </div>
        </form>
    </div>

    <?php
    if (isset($_POST['generate_post'])) {
        auto_post_demo_handle_generation(1);
    }
    if (isset($_POST['generate_bulk'])) {
        auto_post_demo_handle_generation(3);
    }

    ob_end_flush(); // fixes empty rectangle output
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

add_action('admin_menu', function () {
    add_submenu_page(
        'auto-post-demo',
        'Auto Blog Settings',
        'Settings',
        'manage_options',
        'auto-blog-settings',
        'auto_blog_settings_page'
    );
});

add_action('admin_init', function () {

    if (
        isset($_POST['auto_blog_settings_nonce']) &&
        wp_verify_nonce($_POST['auto_blog_settings_nonce'], 'auto_blog_settings_save') &&
        current_user_can('manage_options')
    ) {

        $paragraphs = intval($_POST['auto_blog_paragraphs'] ?? 3);
        $images = isset($_POST['auto_blog_images']) ? 1 : 0;

        update_option('auto_blog_paragraphs', $paragraphs);
        update_option('auto_blog_images', $images);

        // Redirect to prevent resubmission and show notice
        wp_redirect(add_query_arg('settings-updated', 'true', menu_page_url('auto-blog-settings', false)));
        exit;
    }

});


function auto_blog_settings_page(){

    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }


    ?>
    

    <div class="wrap" style="max-width:650px;">
        <h1>Auto Blog Settings</h1>

        <form method="post">
            <?php
            wp_nonce_field('auto_blog_settings_save', 'auto_blog_settings_nonce');
            ?>

             <table class="form-table">
                <tr>
                    <th scope="row">Paragraphs per post</th>
                    <td>
                        <input type="number"
                               name="auto_blog_paragraphs"
                               value="<?php echo esc_attr(get_option('auto_blog_paragraphs', 3)); ?>"
                               min="1"
                               max="10">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Attach images</th>
                    <td>
                        <label>
                            <input type="checkbox"
                                   name="auto_blog_images"
                                   value="1"
                                   <?php checked(get_option('auto_blog_images', 1), 1); ?>>
                            Enable image attachments
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

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
            'titles' => [
                "Coloring Tips for Kids",
                "Creative Coloring Ideas for All Ages",
                "How to Make Your Own Coloring Pages",
                "Fun and Relaxing Coloring Techniques",
                "Coloring Activities to Boost Creativity"
            ],
            'paragraphs' => [
                "Coloring is not just a fun activity—it’s a way to help children develop fine motor skills and express creativity. From choosing the right coloring tools to experimenting with textures, each step teaches them to observe and plan carefully.",
                "Starting with basic shapes and simple patterns allows kids to build confidence, while gradually introducing more complex designs challenges them to think critically. Parents can join in to make it a collaborative and bonding experience.",
                "Mixing colors and experimenting with shading techniques adds depth and realism to their artwork. Encouraging children to try blending different shades fosters an understanding of color theory and visual aesthetics.",
                "Creating themed challenges, like seasonal or story-based coloring pages, keeps the activity engaging and promotes storytelling skills. Children learn to narrate stories through their art, connecting imagination with expression.",
                "Sharing colored pages with family or online communities motivates kids to continue practicing, and it exposes them to new ideas and techniques. Over time, these simple coloring activities become a gateway to broader artistic skills."
            ],
            'tags' => ['coloring', 'kids', 'creative', 'fun', 'art']
        ],
        'DIY' => [
            'titles' => [
                "Easy DIY Projects for Beginners",
                "Creative DIY Crafts for Home",
                "DIY Organization Ideas",
                "Step-by-Step DIY Tutorials",
                "Fun DIY Activities for Kids"
            ],
            'paragraphs' => [
                "DIY projects are a perfect way to combine creativity and practicality. Simple materials around the home can be transformed into useful and decorative items, giving a sense of accomplishment.",
                "Starting with beginner-friendly projects helps build confidence, and as skills improve, more intricate crafts can be attempted. Following clear step-by-step instructions ensures success and reduces frustration.",
                "Involving children in DIY projects encourages problem-solving and teamwork. It’s a great opportunity for parents to teach patience and careful planning while letting kids explore their imagination.",
                "Customizing projects with colors, patterns, and textures allows every creation to feel personal and unique. From home decor to handmade gifts, the possibilities are endless and provide a great outlet for self-expression.",
                "Sharing completed projects online or within communities creates a sense of pride and inspires others. DIY activities not only improve creative skills but also encourage sustainable practices by reusing and repurposing materials."
            ],
            'tags' => ['DIY', 'craft', 'creative', 'home', 'kids']
        ],
        'art' => [
            'titles' => [
                "Beginner-Friendly Art Projects",
                "Creative Art Ideas for Home",
                "Fun Drawing and Painting Activities",
                "Exploring Modern Art Techniques",
                "How to Start Your Art Journey"
            ],
            'paragraphs' => [
                "Art is an exploration of imagination, and it offers a powerful way to express feelings and ideas. Starting with simple drawing exercises helps beginners gain confidence and discover their unique style.",
                "Experimenting with different mediums, such as pencils, watercolors, and acrylics, allows artists to understand how materials interact and how to create distinct effects.",
                "Setting up a dedicated space for art projects makes the process enjoyable and minimizes distractions. A consistent environment encourages focus and enhances creativity.",
                "Studying both traditional and modern techniques expands artistic possibilities. Observing other artists’ work, experimenting with styles, and blending approaches leads to richer, more nuanced creations.",
                "Sharing artwork with friends, family, or online communities invites feedback and inspiration. Over time, consistent practice and exploration cultivate a deeper appreciation for art and personal growth as an artist."
            ],
            'tags' => ['art', 'creative', 'painting', 'drawing', 'inspiration']
        ],
        'recipes' => [
            'titles' => [
                "Dinner Recipes",
                "Healthy Breakfast Ideas",
                "Quick Snacks for Busy Days",
                "Delicious Desserts You Can Make at Home",
                "Comfort Food Recipes"
            ],
            'paragraphs' => [
                "Cooking at home is a delightful way to experiment with flavors and improve nutrition. Using fresh, seasonal ingredients ensures every meal is both tasty and healthy.",
                "Starting with simple recipes builds confidence, and gradually exploring more complex techniques expands culinary skills. Understanding basic cooking methods is essential for creating flavorful dishes.",
                "Planning meals ahead saves time and reduces stress during busy weekdays. Batch cooking or preparing ingredients in advance helps maintain a consistent and balanced diet.",
                "Exploring international cuisines adds variety and excitement to your meals. Trying new spices, herbs, and flavor combinations keeps the cooking process fun and educational.",
                "Sharing your culinary creations with friends or family strengthens connections and brings joy. Documenting recipes, experimenting with presentation, and learning from feedback transforms cooking into a creative and rewarding experience."
            ],
            'tags' => ['recipes', 'cooking', 'food', 'easy', 'healthy']
        ],
        'fitness' => [
            'titles' => [
                "Beginner Workout Routines",
                "Effective Home Workouts",
                "Strength Training Tips for Beginners",
                "Cardio Exercises for Fat Loss",
                "Staying Motivated in Your Fitness Journey"
            ],
            'paragraphs' => [
                "Fitness is a journey that improves both physical and mental well-being. Establishing a consistent routine helps build discipline and track progress over time.",
                "Starting with warm-ups prevents injuries and prepares the body for more intense exercises. Incorporating a mix of strength and cardio training ensures overall fitness.",
                "Setting realistic goals and monitoring progress motivates continued effort. Whether tracking weights lifted, distance run, or endurance, seeing improvement fuels commitment.",
                "Rest and recovery are as important as workouts. Giving muscles time to repair prevents fatigue and reduces the risk of overtraining, while nutrition supports energy and growth.",
                "Maintaining a positive mindset enhances performance. Celebrating small victories, staying hydrated, and mixing exercises keeps the routine enjoyable and sustainable."
            ],
            'tags' => ['fitness', 'workout', 'health', 'exercise', 'motivation']
        ],
        'travel' => [
            'titles' => [
                "Weekend Getaways",
                "Travel Tips for First-Time Travelers",
                "Budget-Friendly Travel Ideas",
                "Must-See Destinations This Year",
                "Packing Hacks for Stress-Free Travel"
            ],
            'paragraphs' => [
                "Travel opens the mind to new experiences and broadens perspectives. Planning ahead ensures you maximize your time and discover hidden gems at each destination.",
                "Researching local culture, customs, and cuisine enhances the travel experience. Engaging with locals and trying new activities creates memorable stories.",
                "Budgeting and organizing travel plans makes trips less stressful. Prioritizing destinations and creating flexible itineraries balances exploration with relaxation.",
                "Capturing moments through photography or journaling helps preserve memories. Sharing stories with friends and family allows experiences to inspire others.",
                "Traveling encourages personal growth and adaptability. Each trip teaches problem-solving, communication, and planning skills, making every journey both fun and educational."
            ],
            'tags' => ['travel', 'tips', 'adventure', 'vacation', 'explore']
        ],
        'tech' => [
            'titles' => [
                "Productivity Strategies",
                "Essential Tools for Remote Work",
                "How to Stay Safe Online",
                "Tech Hacks to Simplify Your Day",
                "Beginner’s Guide to Coding"
            ],
            'paragraphs' => [
                "Technology can simplify life and enhance productivity. Exploring apps and tools helps optimize workflows and save time on repetitive tasks.",
                "Understanding basic cybersecurity practices keeps your devices and data safe. Using strong passwords, updates, and backups ensures protection against threats.",
                "Learning basic coding improves problem-solving skills and opens opportunities to automate tasks. Small projects allow hands-on practice and gradual skill development.",
                "Adopting effective productivity strategies, like time blocking or automation, maximizes efficiency. Organizing tasks and minimizing distractions creates a focused environment.",
                "Sharing knowledge and collaborating online enhances learning and innovation. Experimenting with new tools and techniques encourages continuous improvement and creativity."
            ],
            'tags' => ['tech', 'gadgets', 'productivity', 'coding', 'tips']
        ]
    ];


    $topic_images = [
        'coloring' => ["https://images.pexels.com/photos/8014299/pexels-photo-8014299.jpeg", "https://images.pexels.com/photos/8036831/pexels-photo-8036831.jpeg", "https://images.pexels.com/photos/5274622/pexels-photo-5274622.jpeg", "https://images.pexels.com/photos/159570/crayons-coloring-book-coloring-book-159570.jpeg"],
        'DIY' => ["https://images.pexels.com/photos/35150382/pexels-photo-35150382.jpeg", "https://images.pexels.com/photos/35140769/pexels-photo-35140769.jpeg", "https://images.pexels.com/photos/982660/pexels-photo-982660.jpeg", "https://images.pexels.com/photos/1109354/pexels-photo-1109354.jpeg", "https://images.pexels.com/photos/164455/pexels-photo-164455.jpeg"],
        'art' => ["https://images.pexels.com/photos/161154/stained-glass-spiral-circle-pattern-161154.jpeg", "https://images.pexels.com/photos/20967/pexels-photo.jpg", "https://picsum.photos/seed/art3/1200/800"],
        'recipes' => ["https://images.pexels.com/photos/5737464/pexels-photo-5737464.jpeg", "https://images.pexels.com/photos/31261499/pexels-photo-31261499.jpeg", "https://images.pexels.com/photos/65170/pexels-photo-65170.jpeg", "https://images.pexels.com/photos/14935376/pexels-photo-14935376.jpeg"],
        'fitness' => ["https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg", "https://images.pexels.com/photos/221247/pexels-photo-221247.jpeg", "https://images.pexels.com/photos/669584/pexels-photo-669584.jpeg", "https://images.pexels.com/photos/897064/pexels-photo-897064.jpeg"],
        'travel' => ["https://images.pexels.com/photos/346885/pexels-photo-346885.jpeg", "https://images.pexels.com/photos/1058959/pexels-photo-1058959.jpeg", "https://images.pexels.com/photos/2104152/pexels-photo-2104152.jpeg", "https://images.pexels.com/photos/21014/pexels-photo.jpg"],
        'tech' => ["https://images.pexels.com/photos/39284/macbook-apple-imac-computer-39284.jpeg", "https://images.pexels.com/photos/7974/pexels-photo.jpg", "https://images.pexels.com/photos/943096/pexels-photo-943096.jpeg", "https://images.pexels.com/photos/32698507/pexels-photo-32698507.jpeg"]
    ];

    $selected_data = $topics_data[$topic];
    $titles = $selected_data['titles'];
    $paragraphs = $selected_data['paragraphs'];
    $tags_pool = $selected_data['tags'];

    for ($n = 0; $n < $count; $n++) {

        $title = $titles[array_rand($titles)];
        shuffle($paragraphs);
        $content = "<h2>$title</h2>";

        $paragraph_count = intval(get_option('auto_blog_paragraphs', 3));
        $paragraph_count = min($paragraph_count, count($paragraphs));

        for ($i = 0; $i < $paragraph_count; $i++) {
            $content .= "<p>{$paragraphs[$i]}</p>";
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

        $attach_images_setting = get_option('auto_blog_images', 1);

        // Attach main image + 3 extra images
        $images_for_topic = $topic_images[$topic];

        if ($attach_images_setting) { // Only attach images if enabled in Settings
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
        }

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
