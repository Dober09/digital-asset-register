<?php require_once __DIR__ . '/../includes/auth.php'; ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Check-In</title>
<style>body{font-family:system-ui;max-width:640px;margin:2rem auto;padding:0 1rem}label{display:block;margin:.5rem 0}input{width:100%;padding:.4rem}pre{background:#f4f4f4;padding:1rem;overflow:auto}</style>
</head><body>
<h1>Check-In Asset</h1>
<form id="f">
  <label>ID Number <input name="id_num" required value="8001015009087"></label>
  <label>Name <input name="name" required></label>
  <label>Surname <input name="surname" required></label>
  <label>Email <input name="email" type="email" required></label>
  <label>Asset Identifier <input name="asset_identifier" required></label>
  <label>Asset Descriptor <input name="asset_descriptor" required></label>
  <button type="submit">Check In</button>
</form>
<pre id="out">—</pre>
<p><a href="/index.php">← Back</a></p>
<script>
document.getElementById('f').addEventListener('submit', async e => {
  e.preventDefault();
  const r = await fetch('/api/checkin.php', { method:'POST', body: new FormData(e.target) });
  document.getElementById('out').textContent = JSON.stringify(await r.json(), null, 2);
});
</script>
</body></html>