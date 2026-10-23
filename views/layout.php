<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD PHP + PDO</title>
    <style>
        :root { --bg:#f5f6fa; --card:#fff; --text:#1f2430; --muted:#6b7280; --primary:#4f46e5; --danger:#dc2626; --border:#d9dce3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, sans-serif; background:var(--bg); color:var(--text); }
        main { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        h1 { margin-top:0; }
        .card { background:var(--card); border:1px solid var(--border); border-radius:10px; padding:1.25rem; margin-bottom:1rem; }
        .btn { display:inline-block; padding:.5rem .9rem; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--text); cursor:pointer; text-decoration:none; font-size:.9rem; }
        .btn-primary { background:var(--primary); color:#fff; border-color:var(--primary); }
        .btn-danger  { background:var(--danger);  color:#fff; border-color:var(--danger); }
        .flash { background:#ecfdf5; border:1px solid #6ee7b7; padding:.7rem 1rem; border-radius:6px; margin-bottom:1rem; }
        table { width:100%; border-collapse:collapse; font-size:.9rem; }
        th, td { padding:.55rem .6rem; border-bottom:1px solid var(--border); text-align:left; vertical-align:middle; }
        .swatch { display:inline-block; width:18px; height:18px; border-radius:4px; border:1px solid var(--border); vertical-align:middle; }
        form.inline { display:inline; }
        fieldset { border:1px solid var(--border); border-radius:8px; margin:0 0 1rem; padding:1rem; }
        legend { padding:0 .4rem; font-weight:600; }
        .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:1rem; }
        label.field { display:block; font-size:.85rem; font-weight:600; }
        label.field input:not([type=checkbox]):not([type=radio]):not([type=range]):not([type=color]),
        label.field select, label.field textarea { display:block; width:100%; margin-top:.25rem; padding:.5rem; border:1px solid var(--border); border-radius:6px; font:inherit; }
        label.field input[type=color], label.field input[type=range] { display:block; margin-top:.35rem; width:100%; }
        .inline-opts label { font-weight:400; margin-right:1rem; }
        .error { color:var(--danger); font-size:.8rem; font-weight:400; display:block; margin-top:.2rem; }
        .actions { display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
    </style>
</head>
<body>
<main>
    <?= $content ?>
</main>
</body>
</html>
