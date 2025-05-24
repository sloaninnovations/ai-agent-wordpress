<?php
/*
Plugin Name: AI Agent Prompt Bridge
Description: Submit prompts to your AI agent and track status.
Version: 1.1
Author: Your Name
*/

// Admin menu for internal testing
add_action('admin_menu', function () {
    add_menu_page('AI Agent', 'AI Agent', 'manage_options', 'ai-agent', 'ai_agent_ui');
});

// Load admin scripts/styles
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_ai-agent') return;
    wp_enqueue_script('ai-agent-js', plugin_dir_url(__FILE__) . 'admin-ui.js', [], time(), true);
    wp_enqueue_style('ai-agent-css', plugin_dir_url(__FILE__) . 'admin-ui.css', [], time());
});

// Admin UI form
function ai_agent_ui() {
    ?>
    <div class="wrap">
        <h2>Submit Prompt to AI Agent</h2>
        <form id="ai-agent-form">
            <textarea id="ai-agent-prompt" rows="6" style="width:100%" placeholder="Describe your code request..."></textarea><br>
            <button class="button button-primary" type="submit">Submit</button>
        </form>
        <div id="ai-agent-status" style="margin-top: 20px;"></div>
    </div>
    <?php
}

// Register CPT for prompt history
add_action('init', function () {
    register_post_type('ai_prompt', [
        'label' => 'AI Prompts',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title'],
        'menu_icon' => 'dashicons-lightbulb',
    ]);
});

// Save each prompt submission
add_action('wp_ajax_save_ai_prompt', function () {
    if (!current_user_can('manage_options')) wp_die();

    $prompt = sanitize_text_field($_POST['prompt']);
    $project_id = sanitize_text_field($_POST['project_id']);

    $post_id = wp_insert_post([
        'post_type' => 'ai_prompt',
        'post_title' => wp_trim_words($prompt, 8),
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'meta_input' => [
            'full_prompt' => $prompt,
            'project_id' => $project_id
        ]
    ]);

    wp_send_json_success(['id' => $post_id]);
});

// Meta box for project_id and full prompt
add_action('add_meta_boxes', function () {
    add_meta_box(
        'ai_prompt_meta',
        'Prompt Details',
        'render_ai_prompt_meta_box',
        'ai_prompt',
        'normal',
        'high'
    );
});

function render_ai_prompt_meta_box($post) {
    $prompt = get_post_meta($post->ID, 'full_prompt', true);
    $project_id = get_post_meta($post->ID, 'project_id', true);

    $gh = "https://github.com/sloaninnovations/ai-agent-orchestrator/tree/main/generated/{$project_id}";
    $zip = "https://ai-agent-orchestrator.onrender.com/prompt/download/{$project_id}";

    echo "<p><strong>Project ID:</strong><br><code>{$project_id}</code></p>";
    echo "<p><strong>Full Prompt:</strong><br><textarea rows='6' style='width:100%' readonly>" . esc_textarea($prompt) . "</textarea></p>";
    echo "<p><a href='{$gh}' class='button' target='_blank'>🔗 View on GitHub</a></p>";
    echo "<p><a href='{$zip}' class='button' download>⬇️ Download ZIP</a></p>";
}

// Load shortcode frontend
require_once __DIR__ . '/frontend-ui.php';
