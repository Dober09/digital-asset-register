<?php require_once __DIR__ . '/../includes/auth.php'; ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Transfer</title>
<style>body{font-family:system-ui;max-width:640px;margin:2rem auto;padding:0 1rem}label{display:block;margin:.5rem 0}input{width:100%;padding:.4rem}pre{background:#f4f4f4;padding:1rem;overflow:auto}</style>
</head><body>
<h1>Transfer Ownership</h1>
<form id="f">
  <label>Asset ID <input name="asset_id" required></label>
  <label>New Owner ID Number <input name="new_owner_id" required></label>
  <label>New Owner Name <input name="new_owner_name" required></label>
  <label>New Owner Surname <input name="new_owner_surname" required></label>
  <label>New Owner Email <input name="new_owner_email" type="email" required></label>
  <button type="submit">Transfer</button>
</form>
<pre id="out">—</pre>
<p><a href="/index.php">← Back</a></p>
<script>
document.getElementById('f').addEventListener('submit', async e => {
  e.preventDefault();
  const r = await fetch('/api/transfer.php', { method:'POST', body: new FormData(e.target) });
  document.getElementById('out').textContent = JSON.stringify(await r.json(), null, 2);
});
</script>
</body></html>