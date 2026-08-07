<?php
require_once __DIR__ . '/common.php';

// このファイルは index.php / jsdelivr.php / unpkg.php から $previewConfig を設定した上で
// require されることを前提とする。単体では直接アクセスしない。
// 期待する $previewConfig の形:
// [
//     'type'                => 'local' | 'cdn',
//     'titleSuffix'          => '' や ' (jsdelivr)' など <title> に付け足す文字列,
//     'baseAction'           => タイトル部の "Index" リンク先 / <form action="..."> / JS(initDanoniPreview)に渡す遷移先ベースURL (すべて同一の値),
//     'templateFile'         => ダウンロード時のデフォルトテンプレートファイル名,
//     'noSoundPath'          => 楽曲未指定時の音源パス (JS用),
//     'supportsOldVersions'  => v19.4.0未満のバージョンをサポートするか (JS用),
//     'cdnBaseUrl'           => CDN版の場合のパッケージ配信元ベースURL (例: 'https://cdn.jsdelivr.net/npm/danoniplus')
//                               ローカル版では未使用。unpkg等、別CDN版を今後追加する際はこの値だけ変えればよい。
// ]

$updateTimestamp = '2026-08-07_190000';
$getParamPath = '';
if ($previewConfig['type'] === 'cdn') {
	if (isset($_GET["v"])) {
		// index.php ("v49.3.1"のようなv付き表記) との統一感のため、
		// CDN版では "v49.3.1" / "49.3.1" のどちらの表記でも受け付ける。
		// npm/CDN側のバージョンタグにはv接頭辞が付かないため、内部的には常に取り除く。
		$versionParam = preg_replace('/^[vV](?=\d)/', '', $_GET["v"]);
		$testPath = $previewConfig['cdnBaseUrl']."@".$versionParam."/js/danoni_main.js";
		$getParamPath = $testPath;
	}
} else {
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
}
[$rootDir, $rootUrl] = resolveRootPaths();

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

    <title>Dancing☆Onigiri Preview<?php echo $previewConfig['titleSuffix']; ?></title>

    <link rel="stylesheet" href="./danoni_preview_common.css?<?php echo $updateTimestamp; ?>">
    <style type="text/css">
    th,
    td {
        padding: 2px;
    }

    tr:nth-child(2n) {
        background: #333333;
    }
    </style>
<?php

// ▼ ランダムタイムスタンプ
if (isset($_POST['time']) && $_POST['time'] !== '') {
    $randTime = $_POST['time'];
} else {
    $randTime = date('YmdHis');
}

[$uploaded, $uploadedPreJs] = processFileUploads($rootDir, $randTime);

?>
<script>
const serverData = <?php echo json_encode(buildServerData(
    $uploaded,
    $uploadedPreJs,
    $randTime,
    $rootDir,
    $rootUrl,
    [
        // 'selected' は POST再送信時 (gameMode変更等でフォームが自動送信された場合) の
        // バージョン維持にのみ使う。GET (?v=) 経由の初回アクセス時は 'param' 側
        // ($getParamPath、上の GET パラメータ解決処理で作られる正しい形式の値) が
        // 使われるため、ここで $_GET['v'] にフォールバックしてはいけない。
        // (CDN版の <option value="..."> はURL全体であり、$_GET['v'] の生のバージョン
        //  文字列とは一致しないため、ここにフォールバックすると常に不一致になり、
        //  結果的に常に最新版へフォールバックしてしまうバグになる)
        'selected' => $_POST['v'] ?? '',
        'param'    => $getParamPath ?? '',
        'latest'   => $latestVerPath ?? '',
        'type'     => $previewConfig['type'],
    ]
));
?>;
</script>
</head>

<body>
    <table>
        <tr>
            <td>
                <form method="post" action="<?php echo $previewConfig['baseAction']; ?>" id="formV" name="formV" style="text-align:center;" enctype="multipart/form-data">
                <div class="header-bar">
                    <div class="header-title-group">
                        <span class="tt-wrap">
                            <span class="tt-icon tt-icon-ex" tabindex="0">Manual</span>
                            <span class="tt-box tt-box-title">
                                <a href="https://github.com/cwtickle/danoniplus-docs/wiki/HowToUsePreview" target="wiki">English</a>
                                <a href="https://github.com/cwtickle/danoniplus/wiki/HowToUsePreview" target="wiki">日本語</a>
                                ----
                                <a href="https://github.com/cwtickle/danoniplus-preview" target="wiki">GitHub Page</a>
                            </span>
                        </span>
                        <span class="title">
                            <a href="<?php echo $previewConfig['baseAction']; ?>" onclick="return confirm('Data will be reset. Is it OK?\nデータはリセットされます。よろしいですか？');">
                                <span class="title1">D</span>ancing☆<span class="title2">O</span>nigiri <span class="title3">P</span>review
                            </a>
                        </span>
                    </div>
                    <div class="header-controls">
                        
                        <span id="sourceLbl" class="header-label">Source</span>
                        <span id="sourcett" class="tt-wrap">
                            <span class="tt-icon" tabindex="0">?</span>
                            <span class="tt-box tt-box-mini">
                                Switch the source. jsdelivr and unpkg are supported only for version 40.4.0 and later.<br>
                                参照元を切り替えます。jsdelivr / unpkg は v40.4.0 以降のみ対応しています。
                            </span>
                        </span>
                        <select class="select" id="source" onchange="switchSource(this);">
                            <option value="local" <?php echo $previewConfig['sourceKey'] === 'local' ? 'selected' : ''; ?>>Local</option>
                            <option value="jsdelivr" <?php echo $previewConfig['sourceKey'] === 'jsdelivr' ? 'selected' : ''; ?>>jsDelivr</option>
                            <option value="unpkg" <?php echo $previewConfig['sourceKey'] === 'unpkg' ? 'selected' : ''; ?>>UNPKG</option>
                        </select>

                        <span id="modeLbl" class="header-label">Mode</span>
                        <span id="modett" class="tt-wrap">
                            <span class="tt-icon" tabindex="0">i</span>
                            <span class="tt-box tt-box-title">
                                <a id="srcjs" target="src">js</a>
                                <a id="srccss" target="src">css</a>
                            </span>
                        </span>
                        <span id="ck">
                            <select class="select" name="g" id="g" onchange="getWidth(this);">
                                <option value="">Dancing☆Onigiri</option>
                                <option value="9tkey">Dancing☆Onigiri (9tkey)</option>
                                <option value="kstyle">Kirizma / キリズマ</option>
                                <option value="pstyle">Punching◇Panels (Single)</option>
                                <option value="pstyle_dp">Punching◇Panels (Double)</option>
                            </select>
                        </span>

                        <span class="header-label">Ver</span>
                        <span class="tt-wrap">
                            <span class="tt-icon" tabindex="0">i</span>
                            <span class="tt-box tt-box-title">
                                <a id="versionLink" target="release">Release</a>
                                <a id="changelog" target="changelog">Changelog</a>
                                <a id="updateInfo" target="updateInfo">UpdateInfo</a>
                            </span>
                        </span>
                        <a id="newh" href="javascript:jumpPrev();" class="nav-arrow">▲</a>
                        <span id="cver" style="display:none;"></span>
                        <select class="select select-version" name="v" id="v" onchange="getVersion(this);">
                            <?php
                            if ($previewConfig['type'] === 'cdn') {
                                $matchingFiles = [];
                                $tmpFile = @fopen('npmversion.txt', 'r');

                                if ($tmpFile) {
                                    while (($line = fgets($tmpFile)) !== false) {
                                        $matchingFiles[] = str_replace("\n", "", $line);
                                    }
                                    fclose($tmpFile);
                                }

                                $latestVerPath = renderVersionOptions(
                                    $matchingFiles,
                                    function ($file) {
                                        return $file;
                                    },
                                    function ($file) use ($previewConfig) {
                                        return $previewConfig['cdnBaseUrl'] . '@' . $file . '/js/danoni_main.js';
                                    }
                                );
                            } else {
                                $matchingFiles = findFilesMatchingPattern();
                                $latestVerPath = renderVersionOptions(
                                    $matchingFiles,
                                    function ($file) {
                                        return extractVersionFromFilename($file);
                                    },
                                    function ($file) {
                                        return $file;
                                    }
                                );
                            }
                            ?>
                        </select>
                        <a href="javascript:jumpNext();" class="nav-arrow">▼</a>
                    </div>
                </div>

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

                <div class="chart-card">
                    <div class="chart-card-header">
                        <span class="chart-card-title">
                            Chart / 譜面データ
                            <span class="tt-wrap">
                                <span class="tt-icon" tabindex="0">?</span>
                                <span class="tt-box tt-box-wide">
                                    Input your chart data. Paste the data output from the editor or chart settings here.<br>
                                    エディターで出力した譜面データや譜面ヘッダーを貼り付けてください
                                </span>
                            </span>
                        </span>
                        <span class="chart-card-links">
                            <span id="editorDefault">
                                <a href="https://superkuppabros.github.io/danoni-editor/" target="_blank">Dancing☆Onigiri エディター</a>
                                (<a id="editorsub" onclick="window.open('https://superkuppabros.github.io/danoni-editor/', '_blank', 'width=800px,height=500px');return false;">Window</a> / 
                                <a href="./editor/" target="_blank">Mirror</a>) ↗
                            </span>
                            <a id="editorLink" target="editor">Editor</a>
                        </span>
                        <span class="chart-card-actions">
                            <input type="submit" value="譜面読込 (Load)" class="btn-primary">
                            <button type="button" id="loadButton" class="btn-secondary">Download As File</button>
                        </span>
                    </div>
                    <textarea id="d" name="d" class="chart-textarea"></textarea>
                </div>

                    <div class="settings-panes">
                        <div class="settings-pane-left">
                            <div class="settings-card">
                                <div class="settings-card-title settings-card-title-2">File Upload / ファイルアップロード</div>
                                <table class="settings-table">
                                    <tr>
                                        <td>Music File<br>楽曲ファイル
                                            <span class="tt-wrap">
                                                <span class="tt-icon" tabindex="0">?</span>
                                                <span class="tt-box tt-box-mini">
                                                    You can upload files up to 64 MB in size.<br>
                                                    64MBまでのファイルがアップロード可能です。
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="file" id="musicFile" name="musicFile" accept=".mp3,.mp4,.m4a,.ogg,.oga,.aac,.flac,.js"> <a onclick="cancelFile(`musicFile`);">Cancel</a><br>
                                            Uploaded: <input type="text" name="mf" id="mf" style="width:75%" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Chart File<br>譜面ファイル<br><a class="pill-link" onclick="confirmCancelFile(`dosf1`);">Reset</a></td>
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
                                        <td>HTML Template<br><a class="pill-link" onclick="confirmCancelFile(`htmlf1`, `htmlf`);">Reset</a> <a class="pill-link" href="<?php echo $previewConfig['templateFile']; ?>" download>DL Template</a></td>
                                        <td>
                                            <input type="file" id="htmlFile" name="htmlFile" accept=".html,.htm"> <a onclick="cancelFile(`htmlFile`);">Cancel</a><br>
                                            Uploaded: <input type="text" name="htmlf1" id="htmlf1" readonly><br>
                                            <input type="hidden" name="htmlf" id="htmlf">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Image Files
                                            <span class="tt-wrap">
                                                <span class="tt-icon" tabindex="0">?</span>
                                                <span class="tt-box tt-box-wide">
                                                    Upload a set of image files to be specified for backgrounds, masks, etc.<br>
                                                    背景やマスク等で指定する画像ファイル一式をアップロードします。
                                                </span>
                                            </span>
                                            <br><a class="pill-link" onclick="confirmCancelFile(`imgs`, `imgf`);">Reset</a></td>
                                        <td>
                                            <input type="file" id="imgFiles" name="imgFiles[]" accept=".png,.jpg,.jpeg,.gif,.svg,.webp" multiple> <a onclick="cancelFile(`imgFiles`);">Cancel</a><br>
                                            Uploaded: <input type="text" name="imgs" id="imgs" style="width:75%" readonly><br>
                                            <input type="hidden" name="imgf" id="imgf">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="settings-pane-right">
                            <div class="settings-card">
                                <div class="settings-card-title settings-card-title-1">Display Settings / 表示設定</div>
                                <table class="settings-table">
                                    <tr>
                                        <td style="width:24%;">Display Size<br>表示サイズ</td>
                                        <td>
                                            <select class="select" name="w" id="w" onchange="getWidth(this);">
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
                                            </select><select class="select select-accent" name="h" id="h" onchange="getWidth(this);">
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
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Keymode(difData)<br>キー数情報
                                            <span class="tt-wrap">
                                                <span class="tt-icon" tabindex="0">?</span>
                                                <span class="tt-box tt-box-mini">
                                                    If you specify keymode here, you can omit the chart settings information to start.<br>
                                                    ここでキーモードを指定すると、譜面ヘッダーのdifDataを省略できます。
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="text" name="k" id="k">
                                            <a onclick="cancelFile(`k`);">Cancel</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="settings-card">
                                <div class="settings-card-title settings-card-title-3">Other Settings / その他</div>
                                <table class="settings-table">
                                    <tr>
                                        <td>URL Query Parameters
                                            <span class="tt-wrap">
                                                <span class="tt-icon" tabindex="0">?</span>
                                                <span class="tt-box tt-box-wide">
                                                    Specify URL query parameters. Example: scoreId=2&dos=001<br>
                                                    URLクエリパラメーターを指定します。例: scoreId=2&dos=001
                                                </span>
                                            </span>
                                            <br><a class="pill-link" onclick="confirmCancelFile(`queryParams`);">Reset</a></td>
                                        <td>
                                            <input type="text" name="queryParams" id="queryParams" style="width:75%">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-card">
                        <button type="button" class="accordion-toggle" data-target="advancedBody" aria-expanded="false">
                            <span class="accordion-icon">▶</span>
                            Advanced Settings / 上級者向け設定
                        </button>
                        <div class="accordion-body" id="advancedBody" hidden>
                            <table class="settings-table">
                                <tr>
                                    <td>Custom JS Files
                                        <span class="tt-wrap">
                                            <span class="tt-icon" tabindex="0">?</span>
                                            <span class="tt-box tt-box-wide">
                                                Upload custom JS, common configuration files, and JS files for themes.<br>
                                                カスタムJSや共通設定ファイル、スキン用JSファイルをアップロードできます。
                                            </span>
                                        </span>
                                        <br><a class="pill-link" onclick="confirmCancelFile(`jf1`, `jf2`, `jf3`, `jf`);">Reset</a></td>
                                    <td>
                                        <input type="file" id="jsFile1" name="jsFile1" accept=".js"> <a onclick="cancelFile(`jsFile1`);">Cancel</a><br>
                                        <input type="file" id="jsFile2" name="jsFile2" accept=".js"> <a onclick="cancelFile(`jsFile2`);">Cancel</a><br>
                                        <input type="file" id="jsFile3" name="jsFile3" accept=".js"> <a onclick="cancelFile(`jsFile3`);">Cancel</a><br>
                                        Uploaded: <input type="text" name="jf1" id="jf1" readonly><input type="text" name="jf2" id="jf2" readonly><input type="text" name="jf3" id="jf3" readonly><br>
                                        <input type="hidden" name="jf" id="jf">
                                        <input type="hidden" name="time" id="time">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Custom CSS Files
                                        <span class="tt-wrap">
                                            <span class="tt-icon" tabindex="0">?</span>
                                            <span class="tt-box tt-box-wide">
                                                Upload custom CSS, and CSS files for themes.<br>
                                                カスタムCSSやスキン用CSSファイルをアップロードできます。
                                            </span>
                                        </span>
                                        <br><a class="pill-link" onclick="confirmCancelFile(`cf1`, `cf2`, `cf`);">Reset</a></td>
                                    <td>
                                        <input type="file" id="cssFile1" name="cssFile1" accept=".css"> <a onclick="cancelFile(`cssFile1`);">Cancel</a><br>
                                        <input type="file" id="cssFile2" name="cssFile2" accept=".css"> <a onclick="cancelFile(`cssFile2`);">Cancel</a><br>
                                        Uploaded: <input type="text" name="cf1" id="cf1" readonly><input type="text" name="cf2" id="cf2" readonly><br>
                                        <input type="hidden" name="cf" id="cf">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="accordion-card accordion-warning">
                        <button type="button" class="accordion-toggle" data-target="experimentBody" aria-expanded="false">
                            <span class="accordion-icon">▶</span>
                            Experiment Settings / 注意を要する設定
                        </button>
                        <div class="accordion-body" id="experimentBody" hidden>
                            <table class="settings-table">
                                <tr>
                                    <td>Preload Values
                                        <span class="tt-wrap">
                                            <span class="tt-icon" tabindex="0">?</span>
                                            <span class="tt-box tt-box-wide">
                                                Specify the names to be pre-defined in the hidden attribute, separated by commas.<br>
                                                事前にhidden属性で定義する名前をカンマ区切りで指定します。
                                            </span>
                                        </span>
                                        <br><a class="pill-link" onclick="confirmCancelFile(`prevals`);">Reset</a></td>
                                    <td>
                                        <input type="text" name="prevals" id="prevals" style="width:75%" title="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Preload JS Files
                                        <span class="tt-wrap">
                                            <span class="tt-icon" tabindex="0">?</span>
                                            <span class="tt-box tt-box-wide">
                                                Upload a batch of js files to be loaded before danoni_main.js.<br>
                                                danoni_main.jsより前にロードするjsファイルをまとめてアップロードします。
                                            </span>
                                        </span>
                                        <br><a class="pill-link" onclick="confirmCancelFile(`prejs`, `prejf`);">Reset</a></td>
                                    <td>
                                        <input type="file" id="prejsFiles" name="prejsFiles[]" accept=".js" title="" multiple> <a onclick="cancelFile(`prejsFiles`);">Cancel</a><br>
                                        Uploaded: <input type="text" name="prejs" id="prejs" style="width:75%" readonly><br>
                                        <input type="hidden" name="prejf" id="prejf">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Experiment script
                                        <span class="tt-wrap">
                                            <span class="tt-icon" tabindex="0">?</span>
                                            <span class="tt-box tt-box-wide">
                                                Enable/disable experimental scripts with new features.<br>
                                                新しい機能を実験的に搭載したスクリプトを有効にするかどうかを設定します。
                                            </span>
                                        </span>
                                    </td>
                                    <td>
                                        <select class="select" name="cjd" id="cjd" title="">
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
                        </div>
                    </div>
                </form>
                <div class="chart-card" id="commentArea"></div>
                <script src="./danoni_preview_main.js?<?php echo $updateTimestamp; ?>"></script>
                <script type="text/javascript">
                initDanoniPreview({
                    baseAction: `<?php echo $previewConfig['baseAction']; ?>`,
                    supportsOldVersions: <?php echo $previewConfig['supportsOldVersions'] ? 'true' : 'false'; ?>,
                    templateFile: `<?php echo $previewConfig['templateFile']; ?>`,
                    noSoundPath: `<?php echo $previewConfig['noSoundPath']; ?>`,
                    sourceUrls: {
                        local: `./`,
                        jsdelivr: `./jsdelivr.php`,
                        unpkg: `./unpkg.php`,
                    },
                });
                </script>
                <!--
                <p style="text-align:center;">
                    <a id="removeKey" onclick="removeKeySave()">Remove local storage by keymode / キー別のローカルストレージを削除</a><br>
                </p>
                -->
                <div class="chart-card">
                    This site is a version verification and test play site for Dancing Onigiri (CW Edition). <br>
                    You can test play by specifying the version, chart information, and music data. <br>
                    Please use this site within the bounds of common sense. <br>
                    We are not responsible for any problems that may occur on this site.<br>
                    <br>
                    * Uploaded data is automatically deleted at 6:00 a.m. (GMT+9) every day.<br>
                </div>
                <div class="chart-card">
                    このサイトは、Dancing☆Onigiri (CW Edition)のバージョン検証兼テストプレイサイトです。<br>
                    バージョンと譜面情報、楽曲データを反映することでテストプレイが可能です。<br>
                    良識の範囲でお使いください。<br>
                    このサイトで何か問題が発生したとしても、当方は免責とさせていただきます。<br>
                    <br>
                    * アップロードされたデータは毎日午前6時に自動消去される仕組みです。<br>
                </div>
                <p style="text-align:center;">
                    <a href="https://github.com/cwtickle/danoniplus" target="_blank">Dancing☆Onigiri (CW Edition) - Web-based Rhythm Game [GitHub]</a>
                </p>
                <hr>
            </td>
        </tr>
    </table>
</body>

</html>