<?php require_once __DIR__ . '/../includes/auth.php'; ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Log</title>
<style>body{font-family:system-ui;max-width:800px;margin:2rem auto;padding:0 1rem}input{padding:.4rem}pre{background:#f4f4f4;padding:1rem;overflow:auto}</style>
</head><body>
<h1>Movement Log</h1>
<form id="f">
  <input name="asset_id" placeholder="Asset ID (numeric)" required>
  <button type="submit">Load</button>
</form>
<pre id="out">—</pre>
<p><a href="/index.php">← Back</a></p>
<script>
document.getElementById('f').addEventListener('submit', async e => {
  e.preventDefault();
  const id = new FormData(e.target).get('asset_id');
  const r = await fetch('/api/log.php?asset_id=' + encodeURIComponent(id));
  document.getElementById('out').textContent = JSON.stringify(await r.json(), null, 2);
});
</script>
</body></html>