<?php require_once __DIR__ . '/../includes/auth.php'; ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Search</title>
<style>body{font-family:system-ui;max-width:800px;margin:2rem auto;padding:0 1rem}input{padding:.4rem;width:60%}pre{background:#f4f4f4;padding:1rem;overflow:auto}</style>
</head><body>
<h1>Search Assets</h1>
<form id="f">
  <input name="q" placeholder="Identifier, descriptor, name, surname" required>
  <button type="submit">Search</button>
</form>
<pre id="out">—</pre>
<p><a href="/index.php">← Back</a></p>
<script>
document.getElementById('f').addEventListener('submit', async e => {
  e.preventDefault();
  const q = new FormData(e.target).get('q');
  const r = await fetch('/api/search.php?q=' + encodeURIComponent(q));
  document.getElementById('out').textContent = JSON.stringify(await r.json(), null, 2);
});
</script>
</body></html>