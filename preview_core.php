<?php
require_once __DIR__ . '/common.php';

// このファイルは index.php / index2.php から $previewConfig を設定した上で
// require されることを前提とする。単体では直接アクセスしない。
// 期待する $previewConfig の形:
// [
//     'type'                => 'local' | 'cdn',
//     'titleSuffix'          => '' や ' (jsdelivr)' など <title> に付け足す文字列,
//     'indexLinkHref'        => タイトル部の "Index" リンク先 ('./' 等),
//     'formAction'           => <form action="..."> に入れる値,
//     'baseAction'           => JS(initDanoniPreview)に渡す遷移先ベースURL,
//     'templateFile'         => ダウンロード時のデフォルトテンプレートファイル名,
//     'noSoundPath'          => 楽曲未指定時の音源パス (JS用),
//     'supportsOldVersions'  => v19.4.0未満のバージョンをサポートするか (JS用),
// ]

$getParamPath = '';
if ($previewConfig['type'] === 'cdn') {
	if (isset($_GET["v"])) {
		$testPath = "https://cdn.jsdelivr.net/npm/danoniplus@".$_GET["v"]."/js/danoni_main.js";
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

    <link rel="stylesheet" href="./danoni_preview_common.css">
    <style type="text/css">
    th,
    td {
        padding: 2px;
    }

    tr:nth-child(2n) {
        background: #333333;
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
        'selected' => $previewConfig['type'] === 'cdn'
            ? (($_POST['v'] ?? '') !== '' ? $_POST['v'] : ($_GET['v'] ?? ''))
            : ($_POST['v'] ?? ''),
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
                <p style="text-align:center;">
                    <span class="title"><span class="title1">D</span>ancing☆<span class="title2">O</span>nigiri <span class="title3">P</span>review (<a href="<?php echo $previewConfig['indexLinkHref']; ?>" onclick="return confirm('Data will be reset. Is it OK?\nデータはリセットされます。よろしいですか？');">Index</a>)</span>
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
                <form method="post" action="<?php echo $previewConfig['formAction']; ?>" id="formV" name="formV" style="text-align:center;" enctype="multipart/form-data">
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
                                        function ($file) {
                                            return 'https://cdn.jsdelivr.net/npm/danoniplus@' . $file . '/js/danoni_main.js';
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
                            <td>HTML Template<br>( <a onclick="confirmCancelFile(`htmlf1`, `htmlf`);">Reset</a> )<br>( <a href="<?php echo $previewConfig['templateFile']; ?>" download>DL Template</a> )</td>
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
                <script src="./danoni_preview_main.js"></script>
                <script type="text/javascript">
                initDanoniPreview({
                    baseAction: `<?php echo $previewConfig['baseAction']; ?>`,
                    supportsOldVersions: <?php echo $previewConfig['supportsOldVersions'] ? 'true' : 'false'; ?>,
                    templateFile: `<?php echo $previewConfig['templateFile']; ?>`,
                    noSoundPath: `<?php echo $previewConfig['noSoundPath']; ?>`,
                });
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