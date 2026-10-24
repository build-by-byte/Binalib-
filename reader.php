<?php
require_once __DIR__ . '/config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$sid = (int) ($_SESSION['bls_ebook_student_id'] ?? 0);
if ($sid < 1) { header('Location: user/index.php'); exit; }
$id = (int) ($_GET['id'] ?? 0);
$pdo = db();
$q = $pdo->prepare("SELECT e.*, h.last_page, h.progress_percent, h.duration_seconds FROM ebooks e LEFT JOIN ebook_reading_history h ON h.ebook_id=e.id AND h.student_id=? WHERE e.id=? AND e.status='Aktif' LIMIT 1");
$q->execute([$sid,$id]);
$e = $q->fetch();
if (!$e) { http_response_code(404); exit('E-Book tidak ditemukan.'); }
$initialPage = max(1,(int)($e['last_page'] ?? 1));
$initialDuration = max(0,(int)($e['duration_seconds'] ?? 0));
$from = (string) ($_GET['from'] ?? '');
$backUrl = $from === 'ebooks' || $from === 'ebook' ? 'E-book/' : 'user/index.php';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($e['title']) ?> - BinaLib</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}body{margin:0;background:#F1F5F3;color:#183126;font-family:'Plus Jakarta Sans',system-ui,sans-serif}.reader-top{height:66px;background:#fff;border-bottom:1px solid #DDE8E1;display:flex;align-items:center;justify-content:space-between;padding:0 22px;position:sticky;top:0;z-index:5}.reader-title{min-width:0}.reader-title strong{display:block;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.reader-title small{color:#6B7A72;font-size:11px}.reader-actions{display:flex;gap:8px}.btn{border:1px solid #DDE8E1;background:#fff;color:#183126;padding:9px 12px;border-radius:9px;font:600 12px inherit;text-decoration:none;cursor:pointer}.btn-primary{background:#0B6B3A;color:#fff;border-color:#0B6B3A}.reader-main{max-width:1120px;margin:18px auto;padding:0 16px}.reader-tools{background:#fff;border:1px solid #DDE8E1;border-radius:12px;padding:12px;display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:14px}.reader-tools span{font-size:12px;color:#6B7A72}.reader-stage{background:#87938D;min-height:calc(100vh - 150px);border-radius:12px;padding:20px;display:flex;justify-content:center;overflow:auto}.page-wrap{background:#fff;box-shadow:0 5px 25px rgba(0,0,0,.18)}canvas{display:block;max-width:100%;height:auto}.progress{height:5px;background:#EAF0EC;border-radius:99px;overflow:hidden;margin:0 0 14px}.progress>div{height:100%;background:#0B6B3A;width:0}.loading{padding:60px;color:#fff;text-align:center}.reader-error{background:#FFF8E7;border:1px solid #F0E0AF;color:#7A5A10;padding:12px;border-radius:9px;margin-bottom:12px;font-size:12px}@media(max-width:700px){.reader-top{padding:0 12px}.reader-actions a:nth-child(2){display:none}.reader-stage{padding:8px}}
</style>
</head>
<body>
<header class="reader-top"><div class="reader-title"><strong><?= htmlspecialchars($e['title']) ?></strong><small><?= htmlspecialchars($e['author']) ?></small></div><div class="reader-actions"><a class="btn" href="<?= htmlspecialchars($backUrl) ?>">Kembali</a><a class="btn btn-primary" href="download.php?id=<?= $id ?>">Download PDF</a></div></header>
<main class="reader-main">
<div class="progress"><div id="progressBar"></div></div>
<div class="reader-tools"><button class="btn" id="prev">‹ Sebelumnya</button><span>Halaman <b id="pageNum">1</b> / <b id="pageCount">-</b></span><button class="btn" id="next">Berikutnya ›</button></div>
<div id="error" class="reader-error" style="display:none"></div>
<section class="reader-stage"><div class="page-wrap" id="pageWrap"><div class="loading" id="loading">Memuat E-Book...</div><canvas id="pdfCanvas"></canvas></div></section>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(() => {
const id=<?= $id ?>, initialPage=<?= $initialPage ?>, initialDuration=<?= $initialDuration ?>;
pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
let pdf=null,page=initialPage,total=0,duration=initialDuration,lastSent=0;
const startXhr=new XMLHttpRequest();startXhr.open('POST','api.php?action=ebook_start',false);startXhr.setRequestHeader('Content-Type','application/json;charset=UTF-8');startXhr.send(JSON.stringify({ebookId:id}));
try{const started=JSON.parse(startXhr.responseText||'{}');if(started.ok&&started.data){page=Math.max(1,Number(started.data.last_page||page));duration=Math.max(0,Number(started.data.duration_seconds||duration));}}catch(_){ }
const $=x=>document.getElementById(x), canvas=$('pdfCanvas'), ctx=canvas.getContext('2d');
function sendProgress(force=false){ if(!pdf)return; if(!force && duration-lastSent<10)return; lastSent=duration; const x=new XMLHttpRequest(); x.open('POST','api.php?action=ebook_progress',true); x.setRequestHeader('Content-Type','application/json;charset=UTF-8'); x.send(JSON.stringify({ebookId:id,lastPage:page,totalPages:total,durationSeconds:duration,progressPercent:total?Math.round(page/total*10000)/100:0})); }
function renderPage(n){ page=Math.max(1,Math.min(n,total)); pdf.getPage(page).then(p=>{const base=p.getViewport({scale:1.35});const scale=Math.min(1.35,(window.innerWidth-50)/base.width);const v=p.getViewport({scale});canvas.width=v.width;canvas.height=v.height;return p.render({canvasContext:ctx,viewport:v}).promise;}).then(()=>{ $('pageNum').textContent=page;$('progressBar').style.width=(total?Math.min(100,page/total*100):0)+'%'; sendProgress(); $('loading').style.display='none'; }); }
$('prev').onclick=()=>{if(page>1)renderPage(page-1)};$('next').onclick=()=>{if(page<total)renderPage(page+1)};
setInterval(()=>{duration++;sendProgress(false)},1000);window.addEventListener('beforeunload',()=>sendProgress(true));
pdfjsLib.getDocument('ebook-file.php?id='+id+'&view=1').promise.then(p=>{pdf=p;total=p.numPages;$('pageCount').textContent=total;page=Math.min(initialPage,total);renderPage(page);}).catch(e=>{ $('loading').style.display='none';$('error').style.display='block';$('error').textContent='E-Book tidak dapat dibuka. '+e.message; });
})();
</script>
</body>
</html>
