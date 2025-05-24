<?php
// v1.0 - Frontend prompt submission + history + refine

add_shortcode('ai_agent_frontend', function () {
    if (!is_user_logged_in()) return "<p>You must be logged in to use this.</p>";

    ob_start(); ?>
    <div id="ai-agent-box">
        <h2>Submit a Prompt</h2>
        <form id="ai-agent-form">
            <textarea id="ai-agent-prompt" rows="6" style="width:100%" placeholder="Enter your prompt..."></textarea><br>
            <button class="button" type="submit">Submit</button>
        </form>
        <div id="ai-agent-status" style="margin-top: 20px;"></div>

        <h3>Your Previous Prompts</h3>
        <div id="ai-agent-history">
            <?php
            $args = [
                'post_type' => 'ai_prompt',
                'author' => get_current_user_id(),
                'posts_per_page' => 10,
                'orderby' => 'date',
                'order' => 'DESC'
            ];
            $prompts = get_posts($args);
            foreach ($prompts as $p) {
                $prompt = get_post_meta($p->ID, 'full_prompt', true);
                $id = get_post_meta($p->ID, 'project_id', true);
                $gh = "https://github.com/sloaninnovations/ai-agent-orchestrator/tree/main/generated/{$id}";
                $zip = "https://ai-agent-orchestrator.onrender.com/prompt/download/{$id}";
                echo "<div class='ai-history'>
                    <p><strong>{$p->post_title}</strong></p>
                    <a href='{$gh}' target='_blank'>🔗 GitHub</a> |
                    <a href='{$zip}' download>⬇️ ZIP</a> |
                    <a href='#' onclick='refinePrompt(`" . esc_js($prompt) . "`); return false;'>↩️ Refine</a>
                </div>";
            }
            ?>
        </div>
    </div>
    <?php return ob_get_clean();
});
