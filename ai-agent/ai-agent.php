<?php
/*
Plugin Name: AI Agent Prompt Bridge
Description: Submit prompts to your AI agent and track status.
Version: 1.0
Author: Your Name
*/

add_action('admin_menu', function () {
    add_menu_page('AI Agent', 'AI Agent', 'manage_options', 'ai-agent', 'ai_agent_ui');
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_ai-agent') return;
	wp_enqueue_script('ai-agent-js', plugin_dir_url(__FILE__) . 'admin-ui.js', [], time(), true);
    wp_enqueue_style('ai-agent-css', plugin_dir_url(__FILE__) . 'admin-ui.css', [], time());
});

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
// v1.0 - Register Custom Post Type: ai_prompt
add_action('init', function () {
    register_post_type('ai_prompt', [
        'label' => 'AI Prompts',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title'],
        'menu_icon' => 'dashicons-lightbulb',
    ]);
});

// v1.1 - Save prompt to history
add_action('wp_ajax_save_ai_prompt', function () {
    if (!current_user_can('manage_options')) wp_die();

    $prompt = sanitize_text_field($_POST['prompt']);
    $project_id = sanitize_text_field($_POST['project_id']);

    $post_id = wp_insert_post([
        'post_type' => 'ai_prompt',
        'post_title' => wp_trim_words($prompt, 8),
        'post_status' => 'publish',
        'meta_input' => [
            'full_prompt' => $prompt,
            'project_id' => $project_id
        ]
    ]);

    wp_send_json_success(['id' => $post_id]);
});
