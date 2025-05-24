document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("ai-agent-form");
    const prompt = document.getElementById("ai-agent-prompt");
    const status = document.getElementById("ai-agent-status");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        status.innerHTML = "Submitting...";

        const res = await fetch("https://ai-agent-orchestrator.onrender.com/prompt", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ prompt: prompt.value })
        });

        const data = await res.json();
        const id = data.project_id;
        status.innerHTML = `<strong>Tracking Project ID:</strong> ${id}<br>Status: Generating...`;

const poll = setInterval(async () => {
    const check = await fetch(`https://ai-agent-orchestrator.onrender.com/prompt/status/${id}`);
    const json = await check.json();

    const gh = `https://github.com/sloaninnovations/ai-agent-orchestrator/tree/main/generated/${id}`;
    const rawUrl = `https://raw.githubusercontent.com/sloaninnovations/ai-agent-orchestrator/main/generated/${id}`;

    let preview = "";
    if (json.data && json.data["app.py"]) {
        const code = await fetch(`${rawUrl}/app.py`).then(r => r.text());
        preview = `<h3>app.py</h3><pre><code>${code}</code></pre>`;
    } else if (json.data && json.data["main.py"]) {
        const code = await fetch(`${rawUrl}/main.py`).then(r => r.text());
        preview = `<h3>main.py</h3><pre><code>${code}</code></pre>`;
    }

    status.innerHTML = `
        <strong>Tracking Project ID:</strong> ${id}<br>
        <a href="${gh}" target="_blank">🔗 View on GitHub</a><br>
        <pre>${JSON.stringify(json, null, 2)}</pre>
        ${preview}
    `;

    if (json.stage === "committed" || json.stage === "error") clearInterval(poll);
}, 5000);

    });
});
