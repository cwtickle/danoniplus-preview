<?php
$getParamPath = '';
if (isset($_GET["v"])) {
	$tempV = explode(".", $_GET["v"]);
	$testPath = "./".$tempV[0]."/_preview/danonicw-".$_GET["v"]."/js/danoni_main.js";
	$testFinalPath = "./".$tempV[0]."/_preview/danonicw-".$_GET["v"]."(final)/js/danoni_main.js";

	if (file_exists($testPath)) {
		if (compareSemanticVersions(substr($_GET["v"], 1), '19.4.0') >= 0) {
			$getParamPath = $testPath;
		} else {
			header("Location: ./".$tempV[0]."/_preview/danonicw-".$_GET["v"]."/preview/", true, 301);
			exit();
		}
	} else if (file_exists($testFinalPath)) {
		if (compareSemanticVersions(substr($_GET["v"], 1), '19.4.0') >= 0) {
			$getParamPath = $testFinalPath;
		} else {
			header("Location: ./".$tempV[0]."/_preview/danonicw-".$_GET["v"]."(final)/preview/", true, 301);
			exit();
		}
	} else {
		header("Location: /", true, 301);
		exit();
	}
}
$rootDir = '';
if ($_SERVER['HTTP_HOST'] === 'danonicw.skr.jp') {
	$rootDir = '/home/cw7/www/danonicw';
    $rootUrl = 'https://danonicw.skr.jp/';
} else if ($_SERVER['HTTP_HOST'] === 'tickle.cloudfree.jp') {
	$rootDir = '/home/tickle/tickle.cloudfree.jp/public_html';
    $rootUrl = 'https://tickle.cloudfree.jp/';
}

/**
 * 譜面データのエスケープ処理
 *
 * @param String $str
 * @return void
 */
function escapeStr($str) {
	$escList = [['`', '*bkquo*'], ["'", '*squo*']];
	$repStr = $str;
	foreach ($escList as $vals) {
		$repStr = str_ireplace($vals[0], $vals[1], $repStr);
	}
	return $repStr;
}

/**
 * 音源ファイル名のエスケープ処理
 *
 * @param String $str
 * @return void
 */
function escapeStrMusic($str) {
	$escList = [['&', '_amp_'], ["'", '_sq_']];
	$repStr = $str;
	foreach ($escList as $vals) {
		$repStr = str_ireplace($vals[0], $vals[1], $repStr);
	}
	return $repStr;
}

/**
 * ファイル名からバージョン番号を抽出
 * @param String $filename
 * @return String
 */
function extractVersionFromFilename($filename) {
    // /\/danonicw-v([\d.x]+)\//
	// [A-Za-z]*[0-9]*
    if (preg_match('/\/danonicw-v([\dx.-]+[\(A-Za-z0-9\)\s]*)\//', $filename, $matches)) {
        return $matches[1];
    } else {
        return null;
    }
}

/**
 * 所定のディレクトリパスに合致するリストを取得
 * @return String
 */
function findFilesMatchingPattern() {
    $files = [];
    $dirContents = glob('./v*/_preview/danonicw-*/js/danoni_main.js');

    foreach ($dirContents as $item) {
        $files[] = $item;
    }

    return $files;
}

/**
 * バージョン番号の比較 (x.y.z-a1 形式)
 * @param String $versionA
 * @param String $versionB
 * @return Number
 */
function compareSemanticVersions($versionA, $versionB) {
    $a = explode('.', str_replace('-', '.', $versionA));
    $b = explode('.', str_replace('-', '.', $versionB));

    $len = max(count($a), count($b));

    for ($i = 0; $i < $len; $i++) {
        $partA = isset($a[$i]) ? intval($a[$i], 36) : 0;
        $partB = isset($b[$i]) ? intval($b[$i], 36) : 0;

        if ($partA < $partB) {
            return -1;
        } elseif ($partA > $partB) {
            return 1;
        }
    }

    return 0;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="description" content="ブラウザで動作するリズムゲーム、Dancing☆Onigiri (CW Edition)のプレビューサイトです。">
    <meta property="og:title" content="Dancing☆Onigiri Preview">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $rootUrl; ?>">
    <meta property="og:image" content="<?php echo $rootUrl; ?>danoni_preview.png">
    <meta property="og:description" content="ブラウザで動作するリズムゲーム、Dancing☆Onigiri (CW Edition)のプレビューサイトです。">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fauna+One&family=Merriweather&family=Montserrat&family=Noto+Sans+JP&family=Raleway&display=swap" media="print" onload="this.media='all'" rel="stylesheet">

    <title>Dancing☆Onigiri Preview</title>

    <style type="text/css">
    a:visited {
        color: #BAB7E0;
    }

    a:active {
        color: #CCCCCC;
    }

    body {
        font-family: "Arial", "メイリオ", "MS P ゴシック", sans-serif;
        color: #cccccc;
        background-color: #000011;
        font-size: 14px;
    }

    table {
        border: 0;
        border-collapse: collapse;
        width: 80%;
        margin: auto;
    }

    .title {
        font-family: "Merriweather", "Arial", "メイリオ", "MS P ゴシック", sans-serif;
        font-size: 24px;
    }

    .title1 {
        color: #9999ff;
        font-size: 24px;
    }

    .title2 {
        color: #ff9999;
        font-size: 24px;
    }

    .title3 {
        color: #ffff99;
        font-size: 24px;
    }

    .ver {
        font-size: 16px;
    }

    th,
    td {
        padding: 2px;
    }

    tr:nth-child(2n) {
        background: #333333;
    }

    textarea {
        width: 99.5%;
        height: 200px;
        font-size: 14px;
    }

    input[type=text]:read-only {
        width: 25%;
        background-color: #aaaaaa;
    }

    a {
        text-decoration: none;
        color: #BAB7E0;
    }

    a:hover {
        color: #FF9900;
        text-decoration: underline;
        cursor: pointer;
    }

    .advanced {
        background: #000033;
        border: solid 1px #cccccc;
        text-align: center;
    }

    .warning {
        background: #330000;
        border: solid 1px #cccccc;
        text-align: center;
    }

    .select {
        border-radius: 5px;
        position: relative;
        font-size: 15px;
        padding: 5px 3px;
    }

    input[type=file] {
        width: 60%;
        height: 40px;
        border: 1px dashed #999999;
    }

    input[type=file]:hover {
        cursor: pointer;
        background: #66666699;
    }
    </style>
<?php

// ▼ アップロード結果を格納する配列
$uploaded = [
    'music' => '',
    'js'    => [],
    'css'   => [],
    'dos'   => '',
    'html'  => '',
    'img'   => [],
];

// ▼ プリロードJS
$uploadedPreJs = [];

// ▼ ランダムタイムスタンプ
if (isset($_POST['time']) && $_POST['time'] !== '') {
    $randTime = $_POST['time'];
} else {
    $randTime = date('YmdHis');
}

/*-----------------------------------
 * 音源ファイル
 *-----------------------------------*/
if (!empty($_FILES['musicFile']['tmp_name']) && $_FILES['musicFile']['error'] === UPLOAD_ERR_OK) {
    $filename = escapeStrMusic(basename($_FILES['musicFile']['name']));
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, ['mp3','mp4','m4a','ogg','oga','aac','flac','js'])) {
        $savedName = $randTime . '_' . $filename;
        $uploadPath = $rootDir . '/tmp/' . $savedName;

        if (move_uploaded_file($_FILES['musicFile']['tmp_name'], $uploadPath)) {
            $uploaded['music'] = $savedName; // serverData 用
            $_POST['mf'] = $filename;        // HTML 表示用
        }
    }
}

/*-----------------------------------
 * 譜面ファイル（dosFile1）
 *-----------------------------------*/
if (!empty($_FILES['dosFile1']['tmp_name']) && $_FILES['dosFile1']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES['dosFile1']['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, ['js','txt'])) {
        $savedName = $randTime . '_' . $filename;
        $uploadPath = $rootDir . '/tmp/' . $savedName;

        if (move_uploaded_file($_FILES['dosFile1']['tmp_name'], $uploadPath)) {
            $uploaded['dos'] = $savedName;
            $_POST['dosf1'] = $filename;
        }
    }
}

/*-----------------------------------
 * HTMLテンプレート
 *-----------------------------------*/
if (!empty($_FILES['htmlFile']['tmp_name']) && $_FILES['htmlFile']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES['htmlFile']['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, ['html','htm'])) {
        $savedName = $randTime . '_' . $filename;
        $uploadPath = $rootDir . '/tmp/' . $savedName;

        if (move_uploaded_file($_FILES['htmlFile']['tmp_name'], $uploadPath)) {
            $uploaded['html'] = $savedName;
            $_POST['htmlf1'] = $filename;
        }
    }
}

/*-----------------------------------
 * カスタムJS（3つ）
 *-----------------------------------*/
for ($i = 1; $i <= 3; $i++) {
    if (!empty($_FILES['jsFile'.$i]['tmp_name']) && $_FILES['jsFile'.$i]['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['jsFile'.$i]['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($ext === 'js') {
            $savedName = $randTime . '_' . $filename;
            $uploadPath = $rootDir . '/tmp/' . $savedName;

            if (move_uploaded_file($_FILES['jsFile'.$i]['tmp_name'], $uploadPath)) {
                $uploaded['js'][] = $savedName;
                $_POST['jf'.$i] = $filename;
            }
        }
    }
}

/*-----------------------------------
 * カスタムCSS（2つ）
 *-----------------------------------*/
for ($i = 1; $i <= 2; $i++) {
    if (!empty($_FILES['cssFile'.$i]['tmp_name']) && $_FILES['cssFile'.$i]['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['cssFile'.$i]['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($ext === 'css') {
            $savedName = $randTime . '_' . $filename;
            $uploadPath = $rootDir . '/tmp/' . $savedName;

            if (move_uploaded_file($_FILES['cssFile'.$i]['tmp_name'], $uploadPath)) {
                $uploaded['css'][] = $savedName;
                $_POST['cf'.$i] = $filename;
            }
        }
    }
}

/*-----------------------------------
 * 画像ファイル（複数）
 *-----------------------------------*/
if (!empty($_FILES['imgFiles']['name'][0])) {
    $imgList = [];

    foreach ($_FILES['imgFiles']['name'] as $idx => $name) {
        if ($_FILES['imgFiles']['error'][$idx] === UPLOAD_ERR_OK) {
            $filename = basename($name);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, ['png','jpg','jpeg','gif','svg','webp'])) {
                $savedName = $randTime . '_' . $filename;
                $uploadPath = $rootDir . '/tmp/' . $savedName;

                if (move_uploaded_file($_FILES['imgFiles']['tmp_name'][$idx], $uploadPath)) {
                    $uploaded['img'][] = $savedName;
                    $imgList[] = $filename; // 表示用
                }
            }
        }
    }

    $_POST['imgs'] = implode(',', $imgList);
}

/*-----------------------------------
 * プリロードJS（複数）
 *-----------------------------------*/
if (!empty($_FILES['prejsFiles']['name'][0])) {
    $preList = [];

    foreach ($_FILES['prejsFiles']['name'] as $idx => $name) {
        if ($_FILES['prejsFiles']['error'][$idx] === UPLOAD_ERR_OK) {
            $filename = basename($name);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if ($ext === 'js') {
                $savedName = $randTime . '_' . $filename;
                $uploadPath = $rootDir . '/tmp/' . $savedName;

                if (move_uploaded_file($_FILES['prejsFiles']['tmp_name'][$idx], $uploadPath)) {
                    $uploadedPreJs[] = $savedName;
                    $preList[] = $filename; // 表示用
                }
            }
        }
    }

    $_POST['prejs'] = implode(',', $preList);
}

?>
<script>
const serverData = <?php echo json_encode([

    // -----------------------------
    // 1. POST で送られてくる値
    // -----------------------------
    'post' => [
        'd'        => $_POST['d']        ?? '',
        'k'        => $_POST['k']        ?? '',
        'mf'       => $_POST['mf']       ?? '',
        'jf'       => $_POST['jf']       ?? '',
        'jf1'      => $_POST['jf1']      ?? '',
        'jf2'      => $_POST['jf2']      ?? '',
        'jf3'      => $_POST['jf3']      ?? '',
        'cf'       => $_POST['cf']       ?? '',
        'cf1'      => $_POST['cf1']      ?? '',
        'cf2'      => $_POST['cf2']      ?? '',
        'imgs'     => $_POST['imgs']     ?? '',
        'imgf'     => $_POST['imgf']     ?? '',
        'dosf'     => $_POST['dosf']     ?? '',
        'dosf1'    => $_POST['dosf1']    ?? '',
        'htmlf'    => $_POST['htmlf']    ?? '',
        'htmlf1'   => $_POST['htmlf1']   ?? '',
        'dosM'     => $_POST['dosM']     ?? 'UTF-8',
        'prevals'  => $_POST['prevals']  ?? '',
        'prejs'    => $_POST['prejs']    ?? '',
        'prejf'    => $_POST['prejf']    ?? '',
        'queryParams' => $_POST['queryParams'] ?? '',
        'cjd'      => $_POST['cjd']      ?? '',
        'g'        => $_POST['g']        ?? '',
        'h'        => $_POST['h']        ?? '500px',
        'time'     => $_POST['time']     ?? '',
    ],

    // -----------------------------
    // 2. バージョン情報
    // -----------------------------
    'version' => [
        'selected' => $_POST['v'] ?? '',
        'param'    => $getParamPath ?? '',
        'latest'   => $latestVerPath ?? '',
        'type'     => 'local',   // index.php はローカル版
    ],

    // -----------------------------
    // 3. サーバー環境情報
    // -----------------------------
    'env' => [
        'rootDir' => $rootDir,
        'rootUrl' => $rootUrl,
        'host'    => $_SERVER['HTTP_HOST'],
        'urlDomain' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'],
    ],

    // -----------------------------
    // 4. PHP 内部で生成される値
    // -----------------------------
    'internal' => [
        'escaped_d'       => escapeStr($_POST['d'] ?? ''),
        'escaped_k'       => escapeStr($_POST['k'] ?? ''),
        'escaped_prevals' => escapeStr($_POST['prevals'] ?? ''),
        'escaped_query'   => escapeStr($_POST['queryParams'] ?? ''),
    ],

    // -----------------------------
    // 5. アップロード結果（タイムスタンプ付き）
    // -----------------------------
    'upload' => [
        'time'  => $randTime,
        'music' => $uploaded['music'],
        'js'    => $uploaded['js'],
        'css'   => $uploaded['css'],
        'dos'   => $uploaded['dos'],
        'html'  => $uploaded['html'],
        'img'   => $uploaded['img'],
        'prejs' => $uploadedPreJs,
    ],

]);
?>;
</script>
</head>

<body>
    <table>
        <tr>
            <td>
                <p style="text-align:center;">
                    <span class="title"><span class="title1">D</span>ancing☆<span class="title2">O</span>nigiri <span class="title3">P</span>review (<a href="./" onclick="return confirm('Data will be reset. Is it OK?\nデータはリセットされます。よろしいですか？');">Index</a>)</span>
                    <span class="ver"><a id="newh" href="javascript:jumpPrev();">▲</a><span id="cver"></span><a href="javascript:jumpNext();">▼</a></span><br>
                </p>
                <hr>

                <input type="hidden" name="dos" id="dos">
                <input type="hidden" name="externalDos" id="externalDos">
                <input type="hidden" name="externalDosCharset" id="externalDosCharset">
                <div id="canvas-frame">
                    <p>ゲームを準備しています...</p>
                    <p>このメッセージがいつまでも消えない場合、<br>
                        Google ChromeやFirefox等、HTML5に対応したブラウザをご利用ください。</p>
                    <p>Preparing game...</p>
                    <p>If this message does not disappear forever, please use a browser that supports HTML5, such as Google Chrome or Firefox. </p>
                </div>
                <hr>
                <p style="text-align:center;">
                    <a href="https://superkuppabros.github.io/danoni-editor/" target="_blank">Dancing☆Onigiri エディター</a> (<a id="editorsub" onclick="window.open('https://superkuppabros.github.io/danoni-editor/', '_blank', 'width=800px,height=500px');return false;">Window</a> / <a href="./editor/" target="_blank">Mirror</a>) ／
                    <a href="https://github.com/cwtickle/danoniplus/wiki/HowToUsePreview" target="wiki">プレビューサイトの使い方</a> (<a href="https://github.com/cwtickle/danoniplus-docs/wiki/HowToUsePreview" target="wiki">How to Use</a>)
                </p>
                <hr>
                <form method="post" action="" id="formV" name="formV" style="text-align:center;" enctype="multipart/form-data">
                    <input type="submit" value="譜面読込 (Send)" style="width:50%;font-size:20px;">
                    <button type="button" id="loadButton" style="width:35%;font-size:20px;">Download As File</button>
                    <table style="width:100%;text-align:left;border:1px solid #999999;">
                        <tr>
                            <td style="width:24%;">
                                Version<br><a id="new" href="javascript:jumpPrev();">▲New</a> |<a href="javascript:jumpNext();">▼Old</a>
                            </td>
                            <td>
                                <select class="select" name="v" id="v" onchange="getVersion(this);">
                                    <?php
									$matchingFiles = findFilesMatchingPattern();

									// バージョン番号によるソート
									usort($matchingFiles, function($a, $b) {
										$versionA = extractVersionFromFilename($a);
										$versionB = extractVersionFromFilename($b);
										
										return compareSemanticVersions($versionA, $versionB) * (-1);
									});

									$latestVerPath = '';
									if (empty($matchingFiles)) {
									} else {
										$prevVer = '';
										foreach ($matchingFiles as $file) {
											$bgcolor = '#ffffff';
											$addSymbol = '';
											$versionName = extractVersionFromFilename($file);
											$versionMajor = explode('.', $versionName)[0];
											if (strpos($versionName, '-') !== false) {
												$bgcolor = '#ffbbbb';
											} else if ($prevVer !== $versionMajor) {
												if ($latestVerPath == '') {
													$latestVerPath = $file;
												}
												if (strpos($versionName, '(final)') !== false) {
													$bgcolor = '#eeaaee';
												} else if (compareSemanticVersions($versionName, '1.0.0') < 0) {
													$bgcolor = '#cccccc';
												} else {
													$bgcolor = ($prevVer == '' ? '#bbbbff' : '#dddd99');
													$addSymbol = ' *';
												}
												$prevVer = $versionMajor;
											} else if (compareSemanticVersions($versionName, '19.4.1') < 0) {
												$bgcolor = '#cccccc';
											}
											echo '<option value="'.$file.'" style="background-color:'.$bgcolor.';">v'.$versionName.$addSymbol.'</option>'."\n";
										}
									}

									?>
                                </select><select class="select" name="w" id="w" onchange="getWidth(this);">
                                    <option value="500px">W: 500px</option>
                                    <option value="550px">W: 550px</option>
                                    <option value="600px">W: 600px</option>
                                    <option value="650px">W: 650px</option>
                                    <option value="700px">W: 700px</option>
                                    <option value="750px">W: 750px</option>
                                    <option value="800px">W: 800px</option>
                                    <option value="850px">W: 850px</option>
                                    <option value="900px">W: 900px</option>
                                    <option value="950px">W: 950px</option>
                                    <option value="1000px">W: 1000px</option>
                                    <option value="1050px">W: 1050px</option>
                                    <option value="1100px">W: 1100px</option>
                                </select><select class="select" name="h" id="h" onchange="getWidth(this);" style="background: #ccffff;">
                                    <option value="450px">H: 450px</option>
                                    <option value="475px">H: 475px</option>
                                    <option value="500px">H: 500px</option>
                                    <option value="525px">H: 525px</option>
                                    <option value="550px">H: 550px</option>
                                    <option value="575px">H: 575px</option>
                                    <option value="600px">H: 600px</option>
                                    <option value="625px">H: 625px</option>
                                    <option value="650px">H: 650px</option>
                                    <option value="675px">H: 675px</option>
                                    <option value="700px">H: 700px</option>
                                </select>
                                [<a id="versionLink" target="release"> Release </a> / <a id="changelog" target="changelog"> Changelog </a> / <a id="updateInfo" target="updateInfo"> UpdateInfo </a>]
                            </td>
                        </tr>
                        <tr>
                            <td>Game mode <span id="ck">[ <a id="srcjs" target="src">js</a> <a id="srccss" target="src">/ css</a> ]</span></td>
                            <td>
                                <select class="select" name="g" id="g" onchange="getWidth(this);">
                                    <option value="">Dancing☆Onigiri</option>
                                    <option value="9tkey">Dancing☆Onigiri (9tkey)</option>
                                    <option value="kstyle">Kirizma / キリズマ</option>
                                    <option value="pstyle">Punching◇Panels (Single)</option>
                                    <option value="pstyle_dp">Punching◇Panels (Double)</option>
                                </select>
                                <a id="editorLink" target="editor">Editor</a>
                                (Valid for v31.3.1 or later)
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Input your chart data. Paste the data output from the editor or chart settings here.<br>
                                譜面データを入力 ( エディターで出力したデータや譜面ヘッダーを貼り付けてください )<br>
                                <textarea id="d" name="d"></textarea><br>
                            </td>
                        </tr>
                        <tr>
                            <td>Keymode(difData)<br>キー数情報</td>
                            <td>
                                <input type="text" name="k" id="k" title="If you specify keymode here, you can omit the chart settings information to start.&#13;&#10;ここでキー数を指定すると、譜面ヘッダー情報を省略して起動することができます。">
                                <a onclick="cancelFile(`k`);">Cancel</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Music File<br>楽曲ファイル</td>
                            <td>
                                <input type="file" id="musicFile" name="musicFile" accept=".mp3,.mp4,.m4a,.ogg,.oga,.aac,.flac,.js"> <a onclick="cancelFile(`musicFile`);">Cancel</a> (Max 64MB)<br>
                                Uploaded: <input type="text" name="mf" id="mf" style="width:75%" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>Chart File<br>譜面ファイル<br>( <a onclick="confirmCancelFile(`dosf1`);">Reset</a> )</td>
                            <td>
                                <input type="file" id="dosFile1" name="dosFile1" accept=".js,.txt"> <a onclick="cancelFile(`dosFile1`);">Cancel</a><br>
                                Charset : <select class="select" name="dosM" id="dosM">
                                    <option value="UTF-8">UTF-8</option>
                                    <option value="SHIFT_JIS">SHIFT_JIS</option>
                                </select> |
                                Uploaded: <input type="text" name="dosf1" id="dosf1" readonly><br>
                                <input type="hidden" name="dosf" id="dosf">
                            </td>
                        </tr>
                        <tr>
                            <td>URL Query Parameters<br>( <a onclick="confirmCancelFile(`queryParams`);">Reset</a> )</td>
                            <td>
                                <input type="text" name="queryParams" id="queryParams" style="width:75%" title="Specify URL query parameters. Example: scoreId=2&dos=001&#13;&#10;URLクエリパラメーターを指定します。例: scoreId=2&dos=001">
                            </td>
                        </tr>
                        <tr>
                            <td>HTML Template<br>( <a onclick="confirmCancelFile(`htmlf1`, `htmlf`);">Reset</a> )<br>( <a href="template.html" download>DL Template</a> )</td>
                            <td>
                                <input type="file" id="htmlFile" name="htmlFile" accept=".html,.htm"> <a onclick="cancelFile(`htmlFile`);">Cancel</a><br>
                                Uploaded: <input type="text" name="htmlf1" id="htmlf1" readonly><br>
                                <input type="hidden" name="htmlf" id="htmlf">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="advanced">▼ Advanced Settings / 上級者向け設定 ▼</td>
                        </tr>
                        <tr>
                            <td>Custom JS Files<br>( <a onclick="confirmCancelFile(`jf1`, `jf2`, `jf3`, `jf`);">Reset</a> )</td>
                            <td>
                                <input type="file" id="jsFile1" name="jsFile1" accept=".js" title="Upload custom JS, common configuration files, and JS files for themes.&#13;&#10;カスタムJSや共通設定ファイル、スキン用JSファイルをアップロードできます。"> <a onclick="cancelFile(`jsFile1`);">Cancel</a><br>
                                <input type="file" id="jsFile2" name="jsFile2" accept=".js"> <a onclick="cancelFile(`jsFile2`);">Cancel</a><br>
                                <input type="file" id="jsFile3" name="jsFile3" accept=".js"> <a onclick="cancelFile(`jsFile3`);">Cancel</a><br>
                                Uploaded: <input type="text" name="jf1" id="jf1" readonly><input type="text" name="jf2" id="jf2" readonly><input type="text" name="jf3" id="jf3" readonly><br>
                                <input type="hidden" name="jf" id="jf">
                                <input type="hidden" name="time" id="time">
                            </td>
                        </tr>
                        <tr>
                            <td>Custom CSS Files<br>( <a onclick="confirmCancelFile(`cf1`, `cf2`, `cf`);">Reset</a> )</td>
                            <td>
                                <input type="file" id="cssFile1" name="cssFile1" accept=".css" title="Upload custom CSS, and CSS files for themes.&#13;&#10;カスタムCSSやスキン用CSSファイルをアップロードできます。"> <a onclick="cancelFile(`cssFile1`);">Cancel</a><br>
                                <input type="file" id="cssFile2" name="cssFile2" accept=".css"> <a onclick="cancelFile(`cssFile2`);">Cancel</a><br>
                                Uploaded: <input type="text" name="cf1" id="cf1" readonly><input type="text" name="cf2" id="cf2" readonly><br>
                                <input type="hidden" name="cf" id="cf">
                            </td>
                        </tr>
                        <tr>
                            <td>Image Files<br>( <a onclick="confirmCancelFile(`imgs`, `imgf`);">Reset</a> )</td>
                            <td>
                                <input type="file" id="imgFiles" name="imgFiles[]" accept=".png,.jpg,.jpeg,.gif,.svg,.webp" title="Upload a set of image files to be specified for backgrounds, masks, etc.&#13;&#10;背景やマスク等で指定する画像ファイル一式をアップロードします。" multiple> <a onclick="cancelFile(`imgFiles`);">Cancel</a><br>
                                Uploaded: <input type="text" name="imgs" id="imgs" style="width:75%" readonly><br>
                                <input type="hidden" name="imgf" id="imgf">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="warning">▼ Experiment Settings / 注意を要する設定 ▼</td>
                        </tr>
                        <tr>
                            <td>Preload Values<br>( <a onclick="confirmCancelFile(`prevals`);">Reset</a> )</td>
                            <td>
                                <input type="text" name="prevals" id="prevals" style="width:75%" title="Specify the names to be pre-defined in the hidden attribute, separated by commas.&#13;&#10;事前にhidden属性で定義する名前をカンマ区切りで指定します。">
                            </td>
                        </tr>
                        <tr>
                            <td>Preload JS Files<br>( <a onclick="confirmCancelFile(`prejs`, `prejf`);">Reset</a> )</td>
                            <td>
                                <input type="file" id="prejsFiles" name="prejsFiles[]" accept=".js" title="Upload a batch of js files to be loaded before danoni_main.js.&#13;&#10;danoni_main.jsより前にロードするjsファイルをまとめてアップロードします。" multiple> <a onclick="cancelFile(`prejsFiles`);">Cancel</a><br>
                                Uploaded: <input type="text" name="prejs" id="prejs" style="width:75%" readonly><br>
                                <input type="hidden" name="prejf" id="prejf">
                            </td>
                        </tr>
                        <tr>
                            <td>Experiment script</td>
                            <td>
                                <select class="select" name="cjd" id="cjd" title="Enable/disable experimental scripts with new features.&#13;&#10;新しい機能を実験的に搭載したスクリプトを有効にするかどうかを設定します。">
                                    <option value="">--------</option>
                                    <?php
										$dfJsList = glob('./tmp/script/*.js');
										foreach($dfJsList as $key => $value) {
											echo '<option value="'.$value.'">'.basename($value).'</option>'."\n";
										}
									?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </form>
                <p id="commentArea">
                </p>
                <script type="text/javascript">
                // 動的にdanoni_main.jsを読み込む処理 (バージョンにより参照先が異なるため)
                const loadScript_local = (_url, _requiredFlg = true, _charset = 'UTF-8') => {
                    const baseUrl = _url.split('?')[0];

                    return new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.type = 'text/javascript';
                        script.src = _url;
                        script.charset = _charset;
                        script.onload = _ => {
                            resolve(script);
                        };
                        script.onerror = _err => {
                            if (_requiredFlg) {
                                reject(_err);
                            } else {
                                resolve(script);
                            }
                        };
                        document.querySelector(`head`).appendChild(script);
                    });
                };

                // 動的にdanoni_main.cssを読み込む処理 (v27.1.0以前のバージョンのみ)
                const importCssFile_local = (_href, {
                    crossOrigin = `anonymous`
                } = {}) => {
                    const baseUrl = _href.split(`?`)[0];

                    return new Promise(resolve => {
                        const link = document.createElement(`link`);
                        link.rel = `stylesheet`;
                        link.href = _href;
                        if (!location.href.match(/^file/)) {
                            link.crossOrigin = crossOrigin;
                        }
                        link.onload = _ => {
                            resolve(link);
                        };
                        link.onerror = _ => {
                            resolve(link);
                        };
                        document.head.appendChild(link);
                    });
                };

                // バージョン比較処理 (x.y.z-a1形式)
                const compareVersions = (versionA, versionB) => {
                    const partsA = versionA.split('-').join('.').split('.').map(j => parseInt(j, 36));
                    const partsB = versionB.split('-').join('.').split('.').map(j => parseInt(j, 36));
                    const maxLen = Math.max(partsA.length, partsB.length);

                    for (let i = 0; i < maxLen; i++) {
                        partsA[i] = partsA[i] || 0;
                        partsB[i] = partsB[i] || 0;

                        if (partsA[i] < partsB[i]) {
                            return -1; // versionA is older
                        } else if (partsA[i] > partsB[i]) {
                            return 1; // versionA is newer
                        }
                    }
                    return 0; // versionA and versionB are equal
                };

                for (const [key, value] of Object.entries(serverData.post)) {
                    const el = document.getElementById(key);
                    if (el) el.value = value;
                }

                // 譜面データ及び追加設定の初期化
                const url = new URL(window.location.href);
                const params = url.searchParams;
                const urlDomain = url.origin;
                const musicData_g = `|musicTitle=musicTitle,artistName,${urlDomain}|musicUrl=nosound.mp3|`;

                const dosData = serverData.internal.escaped_d;
                let difData_g = serverData.internal.escaped_k;

                const version_g =
                    serverData.version.selected ||
                    serverData.version.param ||
                    serverData.version.latest ||
                    document.getElementById('v').options[0].value;

                document.getElementById('d').value = dosData;
                document.getElementById('dos').value = dosData;

                // ▼ 楽曲ファイル
                if (serverData.upload.music) {
                    // 表示用は元ファイル名（タイムスタンプなし）
                    document.getElementById('mf').value = serverData.post.mf || '';

                    // 読み込み用はタイムスタンプ付きファイル名
                    document.getElementById('dos').value +=
                        `|musicUrl=(..)/tmp/${serverData.upload.music}|`;
                }

                // ▼ 譜面ファイル
                if (serverData.upload.dos) {
                    // 表示用（タイムスタンプなし）
                    document.getElementById('dosf1').value = serverData.post.dosf1 || '';
                    // 読み込み用（タイムスタンプ付き）
                    document.getElementById('dosf').value = serverData.upload.dos;
                }

                // ▼ HTMLテンプレート
                if (serverData.upload.html) {
                    document.getElementById('htmlf1').value = serverData.post.htmlf1 || '';
                    document.getElementById('htmlf').value  = serverData.upload.html;
                }

                // ▼ カスタムJS
                if (serverData.upload.js.length > 0) {
                    // 表示用（タイムスタンプなし）
                    document.getElementById('jf1').value = serverData.post.jf1 || '';
                    document.getElementById('jf2').value = serverData.post.jf2 || '';
                    document.getElementById('jf3').value = serverData.post.jf3 || '';

                    // 読み込み用（タイムスタンプ付き）
                    const jsList = serverData.upload.js.map(f => `(..)/tmp/${f}`).join(',');
                    document.getElementById('jf').value = jsList;
                    document.getElementById('dos').value += `|customjs=${jsList}|`;
                }

                // ▼ カスタムCSS
                if (serverData.upload.css.length > 0) {
                    document.getElementById('cf1').value = serverData.post.cf1 || '';
                    document.getElementById('cf2').value = serverData.post.cf2 || '';

                    const cssList = serverData.upload.css.map(f => `(..)/tmp/${f}`).join(',');
                    document.getElementById('cf').value = cssList;
                    document.getElementById('dos').value += `|customcss=${cssList}|`;
                }

                // ▼ 画像
                if (serverData.upload.img.length > 0) {
                    // 表示用は PHP 側で作った元ファイル名リスト
                    document.getElementById('imgs').value = serverData.post.imgs || '';
                    // 読み込み用はタイムスタンプ付きファイル名
                    document.getElementById('imgf').value = serverData.upload.img.join(',');
                }

                // ▼ プリロードJS
                if (serverData.upload.prejs.length > 0) {
                    // 表示用（元ファイル名）
                    document.getElementById('prejs').value = serverData.post.prejs || '';

                    // 読み込み用（タイムスタンプ付き）
                    const preList = serverData.upload.prejs.map(f => `./tmp/${f}`).join(',');
                    document.getElementById('prejf').value = preList;
                }

                // ▼ 時間
                document.getElementById('time').value = serverData.upload.time;

                let versionj = 0;

                // 選択したバージョンに関する情報取得
                const version = document.getElementById('v');
                const vElements = version.options;
                let baseVersionUrl = ``;
                let baseVersion = ``;
                let latestMajor = ``;
                for (let j = 0; j < vElements.length; j++) {
                    if (j === 0) {
                        latestMajor = vElements[j].text.slice(1).split(`.`)[0];
                    }
                    if (vElements[j].value === version_g) {
                        vElements[j].selected = true;
                        versionj = j;
                        baseVersionUrl = version_g;
                        baseVersion = vElements[j].text.slice(1);
                        document.getElementById(`cver`).innerHTML = `v${baseVersion}`;

                        const majorVersion = baseVersion.split(`.`)[0];
                        if (vElements[j].value === ``) {} else {
                            const version = vElements[j].text.split('(').join('-').split(' ').join('-').split('-')[0];
                            document.getElementById('versionLink').href = `https://github.com/cwtickle/danoniplus/releases/tag/${version}`;
                            document.getElementById('updateInfo').href = `https://github.com/cwtickle/danoniplus/wiki/UpdateInfo#-v${majorVersion}-changelog`;
                            if (latestMajor === majorVersion) {
                                document.getElementById('changelog').href = `https://github.com/cwtickle/danoniplus/wiki/Changelog-latest`;
                            } else {
                                document.getElementById('changelog').href = `https://github.com/cwtickle/danoniplus/wiki/Changelog-v${majorVersion}`;
                            }
                        }
                    }
                }
                if (versionj === 0) {
                    document.getElementById(`new`).style.color = `#999999`;
                    document.getElementById(`newh`).style.color = `#999999`;
                }
                v.style.backgroundColor = v.options[v.selectedIndex].style.backgroundColor;

                // ImgType（ノートスキン）のデフォルト設定
                // ゲームモード別のキー数設定
                const gameMode = document.getElementById('g');
                if (compareVersions(baseVersion, '23.1.0') >= 0) {
                    if (compareVersions(baseVersion, '31.3.1') >= 0) {

                        // difDataの強制キー変更
                        const matches = document.getElementById('dos').value?.match(/\|difData=(.*?)\|/);
                        const difDatas = (matches && matches.length >= 2 ? matches[1] : ``).split(`$`);
                        const replaceDifs = _key => {
                            const difDataAfter = [];
                            for (let j = 0; j < difDatas.length; j++) {
                                const difs = difDatas[j].split(`,`);
                                const rejoinDifs = difs.slice(1);
                                rejoinDifs.unshift(_key);
                                difDataAfter.push(rejoinDifs.join(`,`));
                            }
                            return `|difData=${difDataAfter.join('$')}|`;
                        };

                        if (gameMode.value === `pstyle`) {
                            // Punching◇Panels (Single)
                            document.getElementById('dos').value += `|imgType=panels,svg,true,0|`;
                            if (document.getElementById('dos').value.indexOf(`|difData=`) < 0) {
                                document.getElementById('dos').value += `|difData=18p|`;
                            } else {
                                document.getElementById('dos').value += replaceDifs(`18p`);
                            }

                            document.getElementById('editorLink').href = `https://suzme.github.io/punpane-editor/`;
                            difData_g = ``;
                            document.getElementById('k').readOnly = true;

                        } else if (gameMode.value === `pstyle_dp`) {
                            // Punching◇Panels (Double)
                            document.getElementById('dos').value += `|imgType=panels,svg,true,0|`;
                            if (document.getElementById('dos').value.indexOf(`|difData=`) < 0) {
                                document.getElementById('dos').value += `|difData=36p|`;
                            } else {
                                document.getElementById('dos').value += replaceDifs(`36p`);
                            }
                            document.getElementById('editorLink').href = `https://suzme.github.io/punpane-editor/?key=36p`;
                            difData_g = ``;
                            document.getElementById('k').readOnly = true;

                        } else if (gameMode.value === `kstyle`) {
                            // キリズマ
                            document.getElementById('dos').value += `|imgType=kirizma,svg,true,0|`;
                            if (document.getElementById('dos').value.indexOf(`|difData=`) < 0) {
                                document.getElementById('dos').value += `|difData=27k|`;
                            }
                            document.getElementById('editorLink').href = `https://suzme.github.io/kirizma-converter/`;
                            document.getElementById('editorLink').innerHTML = `Converter`;

                        } else {
                            // Dancing☆Onigiri
                            if (gameMode.value === `9tkey`) {
                                if (document.getElementById('dos').value.indexOf(`|difData=`) < 0) {
                                    document.getElementById('dos').value += `|difData=9t|`;
                                } else {
                                    document.getElementById('dos').value += replaceDifs(`9t`);
                                }
                                document.getElementById('editorLink').href = `https://suzme.github.io/punpane-editor/?key=9t`;
                                difData_g = ``;
                                document.getElementById('k').readOnly = true;
                            } else {
                                document.getElementById('editorLink').style.visibility = `hidden`;
                            }
                            document.getElementById('dos').value += `|imgType=$classic,png$classic-thin,png$note,svg,false,10$fish,svg|`;
                        }
                    } else {
                        document.getElementById('dos').value += `|imgType=$classic,png$classic-thin,png$note,svg,false,10$fish,svg|`;
                    }
                }
                if (compareVersions(baseVersion, '31.3.1') < 0) {
                    gameMode.value = ``;
                    gameMode.style.background = '#aaaaaa';
                    document.getElementById('editorLink').style.visibility = `hidden`;

                    while (gameMode.options[1] !== undefined) {
                        gameMode.remove(1);
                    }
                }

                // v33.6.0以降のみ高さの自動調整ができるため設定を追加
                document.getElementById('h').style.display = compareVersions(baseVersion, '33.6.0') >= 0 ? `inherit` : `none`;

                // v27.1.0以前は横幅が自動拡張されないため、横幅の設定を追加
                document.getElementById('w').style.display = compareVersions(baseVersion, '27.1.0') < 0 ? `inherit` : `none`;

                if (document.getElementById('dosf').value !== '') {
                    document.getElementById('externalDos').value = `${document.getElementById('dosf').value}`;
                }

                if (document.getElementById('dosM').value !== '') {
                    document.getElementById('externalDosCharset').value = `${document.getElementById('dosM').value}`;
                }

                // 楽曲名情報の設定 (musicUrlについては指定の有無によらず一旦"nosound.mp3"で上書きし、既存のデータは使わない)
                if (dosData.indexOf('|musicTitle=') !== -1) {
                    document.getElementById('dos').value += `|musicUrl=nosound.mp3|`;
                } else {
                    document.getElementById('dos').value += musicData_g;
                }

                if (difData_g !== '') {
                    document.getElementById('dos').value += '|difData=' + difData_g + '|';
                    document.getElementById('k').value = difData_g;
                }

                // 音源ファイルの設定 (ファイル名が動的に変わるためここで設定)
                if (document.getElementById('mf').value !== '') {
                    document.getElementById('dos').value += `|musicUrl=(..)/tmp/${document.getElementById('time').value + `_` + document.getElementById('mf').value}|`;
                }

                // カスタムJSファイルの設定
                const arrayCustomJs = [];
                const applyCustomJs = (_val, _subDirectory = ``) => {
                    if (_val === `pstyle_dp`) {
                        arrayCustomJs.push(`(..)tmp/scriptLib/${_subDirectory}pstyle.js`);
                        document.getElementById(`srcjs`).href = `./tmp/scriptLib/${_subDirectory}pstyle.js`;
                    } else {
                        arrayCustomJs.push(`(..)tmp/scriptLib/${_subDirectory}${_val}.js`);
                        document.getElementById(`srcjs`).href = `./tmp/scriptLib/${_subDirectory}${_val}.js`;
                    }
                };

                if (gameMode.value !== '') {
                    if (compareVersions(baseVersion, '45.0.0') >= 0) {
                        applyCustomJs(gameMode.value);
                    } else if (compareVersions(baseVersion, '39.0.0') >= 0) {
                        applyCustomJs(gameMode.value, `v44/`);
                    } else {
                        arrayCustomJs.push(`(..)tmp/scriptLib/v38/${gameMode.value}.js`);
                        document.getElementById(`srcjs`).href = `./tmp/scriptLib/v38/${gameMode.value}.js`;
                    }
                } else if (compareVersions(baseVersion, '32.0.0') >= 0) {
                    arrayCustomJs.push(`(..)tmp/scriptLib/danoni.js`);
                    document.getElementById(`srcjs`).href = `./tmp/scriptLib/danoni.js`;
                } else {
                    document.getElementById(`ck`).style.visibility = `hidden`;
                }
                if (document.getElementById('cjd').value !== '') {
                    arrayCustomJs.push(`(..)${document.getElementById('cjd').value}`);
                }
                if (document.getElementById('jf').value !== '') {
                    arrayCustomJs.push(`${document.getElementById('jf').value.replaceAll(`../music/`, `(..)/tmp/`)}`);
                }
                if (arrayCustomJs.length > 0) {
                    document.getElementById('dos').value += `|customjs=${arrayCustomJs.join(',')}|`;
                }

                // カスタムCSSファイルの設定
                const arrayCustomCss = [];
                const applyCustomCss = (_val, _subDirectory = ``) => {
                    if (_val === `pstyle_dp`) {
                        arrayCustomCss.push(`(..)tmp/scriptLib/${_subDirectory}pstyle_36p.css`);
                        document.getElementById(`srccss`).href = `./tmp/scriptLib/${_subDirectory}pstyle_36p.css`;
                    } else {
                        arrayCustomCss.push(`(..)tmp/scriptLib/${_subDirectory}${_val}.css`);
                        document.getElementById(`srccss`).href = `./tmp/scriptLib/${_subDirectory}${_val}.css`;
                    }
                };
                if (gameMode.value !== '') {
                    if (compareVersions(baseVersion, '45.0.0') >= 0) {
                        applyCustomCss(gameMode.value);
                    } else if (compareVersions(baseVersion, '39.0.0') >= 0) {
                        applyCustomCss(gameMode.value, `v44/`);
                    } else {
                        arrayCustomCss.push(`(..)tmp/scriptLib/v38/${gameMode.value}.css`);
                        document.getElementById(`srccss`).href = `./tmp/scriptLib/v38/${gameMode.value}.css`;
                    }
                } else {
                    document.getElementById(`srccss`).style.display = `none`;
                }
                if (document.getElementById('cf').value !== '') {
                    arrayCustomCss.push(`${document.getElementById('cf').value}`);
                }
                if (arrayCustomCss.length > 0) {
                    document.getElementById('dos').value += `|customcss=${arrayCustomCss.join(',')}|`;
                }

                const imgs = document.getElementById(`imgs`).value?.split(`,`);
                const imgf = document.getElementById(`imgf`).value?.split(`,`);
                imgs.filter(img => img !== ``).forEach((img, j) =>
                    document.getElementById('dos').value = document.getElementById('dos').value.replaceAll(img, `./tmp/${imgf[j]}`));

                // ダウンロードボタンの処理
                const loadButton = document.getElementById('loadButton');

                loadButton.addEventListener('click', async () => {
                    const htmlFile = `${document.getElementById('htmlf').value}` || 'template.html';
                    const response = await fetch(htmlFile);
                    if (response.ok) {
                        const text = await response.text();
                        const patternI = /\|imgType=[^\|]+\|/g;
                        const repText = document.getElementById('dos').value.replaceAll(`/tmp/${document.getElementById('time').value}_`, 'tmp/').replace(patternI, '');
                        let textAfter = text.replace(`<<DOS_DATA>>`, repText);

                        const matches = textAfter.match(/\|musicTitle=(.*?)\|/);
                        const musicTitles = (matches && matches.length >= 2 ? matches[1] : ``).split(`,`);
                        textAfter = textAfter.replace(`<<MUSIC_TITLE>>`, musicTitles[0] || `Preview`);
                        textAfter = textAfter.replace(`<<ARTIST_NAME>>`, musicTitles[1] || `---`);

                        // バージョンに応じて参照するファイルを変更
                        // v32以前はデフォルトCSSが用意されていないため、v33で置き換える
                        if (latestMajor === baseVersion.split(`.`)[0]) {
                            textAfter = textAfter.replace(`<<VERSION>>`, ``);
                        } else if (compareVersions(baseVersion, '33.0.0') >= 0) {
                            textAfter = textAfter.replace(`<<VERSION>>`, `@${baseVersion.split(`.`)[0]}`);
                        } else {
                            textAfter = textAfter.replace(`<<VERSION>>`, `@33`);
                        }

                        const file = new Blob([textAfter], {
                            type: `text/html;charset=utf-8`
                        });

                        // 見えないダウンロードリンクを作る
                        const a = document.createElement('a');
                        a.href = URL.createObjectURL(file);
                        a.download = `${(document.getElementById('mf').value || 'index').replace('.mp3', '').replace('.js', '')}.html`;
                        a.style.display = 'none';

                        // DOMツリーに存在しないとFirefox等でダウンロードできない
                        document.body.appendChild(a);

                        // ダウンロード
                        a.click();
                    }
                });

                // 譜面エリアにフォーカスが当たっているときだけ、onkeydown, oncontextmenu の設定をリセット
                let bkEvent, bkEventCxt;
                const dfEvent = evt => {};
                const dfCxt = evt => true;

                [`d`, `k`, `prevals`, `queryParams`].forEach(txt => {
                    document.getElementById(txt).addEventListener('focus', () => {
                        if (document.onkeydown !== dfEvent) {
                            bkEvent = document.onkeydown;
                            bkEventCxt = document.oncontextmenu;
                        }
                        document.onkeydown = dfEvent;
                        document.oncontextmenu = dfCxt;
                    });
                    document.getElementById(txt).addEventListener('blur', () => {
                        document.onkeydown = bkEvent;
                        document.oncontextmenu = bkEventCxt;
                        if (txt === `queryParams`) {
                            if (document.getElementById(txt)?.value !== ``) {
                                document.getElementById('formV').action = `./?` + document.getElementById(txt).value;
                            } else {
                                document.getElementById('formV').action = `./`;
                            }
                        }
                    });
                });

                // 作品別のローカルストレージデータを必要最低限に設定
                const baseUrl = new URL(location.href).toString();
                const storageOrg = JSON.parse(localStorage.getItem(`${urlDomain}/`));
                const storageCurrent = JSON.parse(localStorage.getItem(baseUrl));
                const baseStorage = storageOrg || storageCurrent;

                // ロケール、キー別、エディター関連以外のローカルストレージデータを消去
                const editorLS = [
                    `isKeyboard`, `isClick`, `isReverse`, `isHighlightedFreeze`,
                    `simultaneousThreshold`, `pageBlockNum`, `testPattern`,
                    `customKeyConfig`, `musicVolume`, `keyPhrases`, `saveData`, `orderGroupMap`,
                ];
                Object.keys(localStorage)
                    .filter(key => key !== `danoni-locale` && !key.startsWith(`danonicw-`) &&
                        key !== `${urlDomain}/` && !editorLS.includes(key))
                    .forEach(key => localStorage.removeItem(key));

                const adj = baseStorage?.adjustment || 0;
                const storageData = JSON.stringify({
                    adjustment: compareVersions(baseVersion, '23.0.0') >= 0 ? adj : Math.round(adj),
                    volume: baseStorage?.volume || 100,
                    appearance: baseStorage?.appearance || `Visible`,
                    opacity: baseStorage?.opacity || 100,
                    hitPosition: baseStorage?.hitPosition || 0,
                    colorType: baseStorage?.colorType || `Default`,
                });
                localStorage.setItem(baseUrl, storageData);
                if (!storageOrg) {
                    localStorage.setItem(`${urlDomain}/`, storageData);
                }

                // プリロードする変数群の定義
                const prevals = document.getElementById(`prevals`).value?.split(`,`);
                const makeHidden = (_name) => {
                    const val = document.createElement('input');
                    val.type = 'hidden';
                    val.name = _name;
                    document.forms[0].appendChild(val);
                };
                prevals.forEach(val => makeHidden(val));

                let queryParams = ``;
                if (document.getElementById(`queryParams`)?.value !== ``) {
                    queryParams = `?${document.getElementById(`queryParams`).value}`;
                }

                // 初期ファイルのロード処理
                const loadScripts = async () => {

                    // プリロードするjsファイルの読込
                    const prejf = document.getElementById(`prejf`).value?.split(`,`);
                    prejf.forEach(async (jsFile) => await loadScript_local(jsFile, false));

                    // danoni_main.jsの読込
                    if (dosData !== null) {
                        if (compareVersions(baseVersion, '19.4.0') < 0) {

                        } else if (compareVersions(baseVersion, '27.1.0') < 0) {
                            const width_g = `<?php echo isset($_POST["w"]) ? $_POST["w"] : ''; ?>` || `800px`;
                            const list = document.getElementById('w');
                            const elements = list.options;
                            for (let j = 0; j < elements.length; j++) {
                                if (elements[j].value === width_g) {
                                    elements[j].selected = true;
                                }
                            }

                            await importCssFile_local(baseVersionUrl.slice(0, -17) + `css/danoni_main.css`);
                            document.getElementById('canvas-frame').style.width = width_g;
                            document.getElementById('canvas-frame').style.margin = `auto`;

                        } else if (compareVersions(baseVersion, '33.6.0') >= 0) {
                            const height_g = `<?php echo isset($_POST["h"]) ? $_POST["h"] : ''; ?>` || `500px`;
                            const list = document.getElementById('h');
                            const elements = list.options;
                            for (let j = 0; j < elements.length; j++) {
                                if (elements[j].value === height_g) {
                                    elements[j].selected = true;
                                }
                            }

                            if (parseInt(height_g) >= 500) {
                                document.getElementById('canvas-frame').style.height = height_g;
                            } else {
                                document.getElementById('canvas-frame').style.height = `500px`;
                                document.getElementById(`dos`).value += `|playingHeight=${parseInt(height_g)}|`;
                            }
                        }
                        await loadScript_local(baseVersionUrl, false);

                        // フレーム数の強制表示
                        // 読み込み直後は変数が定義されていないため、定義されるまで待つ
                        const setInitialSettings = (_cnt = 0) => {
                            setTimeout(_ => {
                                if (typeof g_customJsObj === `object`) {
                                    g_customJsObj.main.push(_ => {
                                        lblframe.style.display = `inherit`;
                                    });
                                } else if (_cnt < 10) {
                                    _cnt++;
                                    setInitialSettings(_cnt);
                                }
                            }, 1000);
                        };
                        setInitialSettings();

                        const setAutoWidth = () => {
                            if (g_currentPage === `title`) {
                                if (compareVersions(baseVersion, '27.1.0') >= 0) {
                                    const val = `${Math.min(Math.ceil(g_sWidth / 50) * 50, 1100)}px`;
                                    const list = document.getElementById('w');
                                    const elements = list.options;
                                    for (let j = 0; j < elements.length; j++) {
                                        if (elements[j].value === val) {
                                            elements[j].selected = true;
                                        }
                                    }
                                }
                                document.getElementById('editorsub').onclick = () => {
                                    window.open('https://superkuppabros.github.io/danoni-editor/', '_blank', `width=${Math.max(g_keyObj[`chara${g_keyObj.currentKey}_0`].length * 30 + 300, 600)}px,height=550px`);
                                    return false;
                                };
                            } else {
                                setTimeout(_ => setAutoWidth(), 500);
                            }
                        };
                        setTimeout(_ => setAutoWidth(), 2000);
                    }
                };
                loadScripts();

                // バージョン変更時の処理
                const getVersion = obj => {
                    const idx = obj.selectedIndex;
                    const text = obj.options[idx].text;
                    const value = obj.options[idx].value;
                    const versionTxt = (value === `` || idx === -1) ? `10000.0.0` : text.slice(1);

                    let redirectUrl;
                    if (compareVersions(versionTxt, '19.4.0') < 0) {
                        redirectUrl = value.slice(1, -17) + `preview/`;
                    } else {
                        redirectUrl = `./`;
                    }
                    document.getElementById('formV').action = redirectUrl + queryParams;
                    document.getElementById('formV').submit();
                };

                // 横幅変更時の処理
                const getWidth = obj => {
                    document.getElementById('formV').action = `./` + queryParams;
                    document.getElementById('formV').submit();
                };

                // 次のバージョンへ遷移
                const jumpNext = () => {
                    versionj++;
                    document.getElementById(`v`).value = vElements[versionj].value;

                    let redirectUrl;
                    if (compareVersions(vElements[versionj].text.slice(1), '19.4.0') < 0) {
                        redirectUrl = vElements[versionj].value.slice(1, -17) + `preview/`;
                    } else {
                        redirectUrl = `./`;
                    }
                    document.getElementById('formV').action = redirectUrl + queryParams;
                    document.getElementById('formV').submit();
                };

                // 前のバージョンへ遷移
                const jumpPrev = () => {
                    if (versionj === 0) {
                        return false;
                    }
                    versionj--;
                    document.getElementById(`v`).value = vElements[versionj].value;

                    document.getElementById('formV').action = `./` + queryParams;
                    document.getElementById('formV').submit();
                };

                // キー別のローカルストレージを削除
                const removeKeySave = () => {
                    if (window.confirm("Delete local storage by keymode. Is it OK?\nキー別のローカルストレージを削除します。よろしいですか？")) {
                        Object.keys(localStorage).filter(key => key.startsWith(`danonicw-`)).forEach(key => localStorage.removeItem(key));
                    }
                };

                const cancelFile = _name => {
                    document.getElementById(_name).value = ``;
                };

                const confirmCancelFile = (...names) => {
                    if (window.confirm("Clears references to uploaded files. Is it OK?\nアップロードしたファイルの参照をクリアします。よろしいですか？")) {
                        names.forEach(name => document.getElementById(name).value = ``);
                    }
                };
                </script>
                <p style="text-align:center;">
                    <a id="removeKey" onclick="removeKeySave()">Remove local storage by keymode / キー別のローカルストレージを削除</a><br>
                </p>
                <hr>
                <p>
                    This site is a version verification and test play site for Dancing Onigiri (CW Edition). <br>
                    You can test play by specifying the version, chart information, and music data. <br>
                    Please use this site within the bounds of common sense. <br>
                    We are not responsible for any problems that may occur on this site.<br>
                </p>
                <p>
                    * Uploaded data is automatically deleted at 6:00 a.m. (GMT+9) every day.<br>
                </p>
                <hr style="border:none;border-top:dashed 1px #cccccc;height:1px;color:#FFFFFF;">
                <p>
                    このサイトは、Dancing☆Onigiri (CW Edition)のバージョン検証兼テストプレイサイトです。<br>
                    バージョンと譜面情報、楽曲データを反映することでテストプレイが可能です。<br>
                    良識の範囲でお使いください。<br>
                    このサイトで何か問題が発生したとしても、当方は免責とさせていただきます。<br>
                </p>
                <p>
                    * アップロードされたデータは毎日午前6時に自動消去される仕組みです。<br>
                </p>
                <hr>
                <p style="text-align:center;">
                    <a href="https://github.com/cwtickle/danoniplus" target="_blank">Dancing☆Onigiri (CW Edition) - Web-based Rhythm Game [GitHub]</a>
                </p>
                <hr>
            </td>
        </tr>
    </table>
</body>

</html>