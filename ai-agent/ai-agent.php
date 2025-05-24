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
    wp_enqueue_script('ai-agent-js', plugin_dir_url(__FILE__) . 'admin-ui.js', [], false, true);
    wp_enqueue_style('ai-agent-css', plugin_dir_url(__FILE__) . 'admin-ui.css');
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
