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
		const gh = `https://github.com/sloaninnovations/ai-agent-orchestrator/tree/main/generated/${id}`;
		status.innerHTML = `
		<strong>Tracking Project ID:</strong> ${id}<br>
		<a href="${gh}" target="_blank">🔗 View on GitHub</a><br>
		<pre>${JSON.stringify(json, null, 2)}</pre>
		`;

        const poll = setInterval(async () => {
            const check = await fetch(`https://ai-agent-orchestrator.onrender.com/prompt/status/${id}`);
            const json = await check.json();
            status.innerHTML = `<strong>Tracking Project ID:</strong> ${id}<br><pre>${JSON.stringify(json, null, 2)}</pre>`;
            if (json.stage === "committed" || json.stage === "error") clearInterval(poll);
        }, 5000);
    });
});
