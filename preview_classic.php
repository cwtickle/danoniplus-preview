<?php
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
    a:link {
        color: #BAB7E0;
    }

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

    .tbl tr:nth-child(2n) {
        background: #333333;
    }

    input[type=text]:read-only {
        width: 25%;
        background-color: #aaaaaa;
    }

    textarea {
        width: 99.5%;
        height: 200px;
        font-size: 14px;
    }

    a {
        color: #BAB7E0;
        text-decoration: none;
    }

    a:hover {
        color: #FF9900;
        text-decoration: underline;
        cursor: pointer;
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
</head>

<body>
    <table>
        <tr>
            <td>
                <p style="text-align:center;">
                    <span class="title"><span class="title1">D</span>ancing☆<span class="title2">O</span>nigiri <span class="title3">P</span>review (<a href="./" onclick="return confirm('データはリセットされます。よろしいですか？\nData will be reset. Is it OK?');">Reset</a> /
                        <a href="/" onclick="return confirm('データはリセットされます。よろしいですか？\nData will be reset. Is it OK?');">Index</a>)</span>
                    <span class="ver"><a href="javascript:jumpPrev();">▲</a><span id="cver"></span><a id="oldh" href="javascript:jumpNext();">▼</a></span><br>
                </p>
                <hr>

                <input type="hidden" name="dos" id="dos">
                <div id="canvas-frame">
                    <canvas id="layer0" width="800" height="500"></canvas>
                    <canvas id="layer1" width="800" height="500"></canvas>
                    <canvas id="layer2" width="800" height="500"></canvas>
                </div>
                <hr>
                <p style="text-align:center;">
                    <a href="https://superkuppabros.github.io/danoni-editor/" target="_blank">Dancing☆Onigiri エディター (CW Edition対応)</a> (<a href="./editor/" target="_blank">Mirror</a>) ／
                    <a href="https://github.com/cwtickle/danoniplus/wiki/HowToUsePreview" target="wiki">プレビューサイトの使い方</a> (<a href="https://github.com/cwtickle/danoniplus-docs/wiki/HowToUsePreview" target="wiki">How to Use</a>)
                </p>
                <hr>
                <form method="post" action="./" id="formV" name="formV" style="text-align:center;" enctype="multipart/form-data">
                    <input type="submit" value="譜面読込 (Send)" style="width:75%;font-size:20px;"><br>
                    <table class="tbl" style="width:100%;text-align:left;border:1px solid #999999;padding:2px;">
                        <tr>
                            <td style="width:24%;">Version<br><a href="javascript:jumpPrev();">▲New</a> |<a id="old" href="javascript:jumpNext();">▼Old</a></td>
                            <td><select class="select" name="v" id="v" onchange="getVersion(this);">

                                    <?php
									$matchingFiles = findFilesMatchingPattern();

									// バージョン番号によるソート
									usort($matchingFiles, function($a, $b) {
										$versionA = extractVersionFromFilename($a);
										$versionB = extractVersionFromFilename($b);
										
										return compareSemanticVersions($versionA, $versionB) * (-1);
									});

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
                                </select>
                                [<a id="versionLink" target="release"> Release </a> / <a id="changelog" target="changelog"> Changelog </a> / <a id="updateInfo" target="updateInfo"> UpdateInfo </a>]
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Input your chart data. Paste the data output from the editor or chart settings here.<br>
                                譜面データを入力 ( エディターで出力したデータや譜面ヘッダーを貼り付けてください )<br>
                                <textarea id="d" name="d" cols="110" rows="10"></textarea><br>
                            </td>
                        </tr>
                        <tr>
                            <td>Keys(difData)<br>キー数情報</td>
                            <td>
                                <input type="text" name="k" id="k">
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
                            <td>Custom JS Files<br>( <a onclick="confirmCancelFile(`jf1`, `jf2`, `jf3`, `jf`);">Reset</a> )</td>
                            <td>
                                <input type="file" id="jsFile1" name="jsFile1" accept=".js"> <a onclick="cancelFile(`jsFile1`);">Cancel</a><br>
                                <input type="file" id="jsFile2" name="jsFile2" accept=".js"> <a onclick="cancelFile(`jsFile2`);">Cancel</a><br>
                                Uploaded: <input type="text" name="jf1" id="jf1" readonly><input type="text" name="jf2" id="jf2" readonly><input type="text" name="jf3" id="jf3" readonly><br>
                                <input type="hidden" name="jf" id="jf">
                                <input type="hidden" name="time" id="time">
                            </td>
                        </tr>
                    </table>

                    <input type="hidden" name="cf" id="cf">
                    <input type="hidden" name="dosf" id="dosf">
                    <input type="hidden" name="dosf1" id="dosf1">
                    <input type="hidden" name="htmlf" id="htmlf">
                    <input type="hidden" name="htmlf1" id="htmlf1">
                    <input type="hidden" name="dosM" id="dosM">
                    <input type="hidden" name="cf1" id="cf1">
                    <input type="hidden" name="cf2" id="cf2">
                    <input type="hidden" name="cjd" id="cjd">
                    <input type="hidden" name="imgs" id="imgs">
                    <input type="hidden" name="imgf" id="imgf">
                    <input type="hidden" name="prevals" id="prevals">
                    <input type="hidden" name="prejs" id="prejs">
                    <input type="hidden" name="prejf" id="prejf">
                    <input type="hidden" name="queryParams" id="queryParams">
                    <input type="hidden" name="h" id="h">
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

                // 動的にdanoni_main.cssを読み込む処理
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

                // 譜面データ及び追加設定の初期化
                const url = new URL(window.location.href);
                const params = url.searchParams;
                const dosData = `<?php echo escapeStr(isset($_POST["d"]) ? $_POST["d"] : ''); ?>`;
                const urlDomain = url.origin;
                const musicData_g = `|musicTitle=musicTitle,artistName,${urlDomain}|musicUrl=nosound.mp3|`;
                const difData_g = `<?php echo escapeStr(isset($_POST["k"]) ? $_POST["k"] : ''); ?>`;

                document.getElementById('mf').value = `<?php echo isset($_POST["mf"]) ? $_POST["mf"] : ''; ?>`;
                document.getElementById('jf').value = `<?php echo isset($_POST["jf"]) ? $_POST["jf"] : ''; ?>`;
                document.getElementById('jf1').value = `<?php echo isset($_POST["jf1"]) ? $_POST["jf1"] : ''; ?>`;
                document.getElementById('jf2').value = `<?php echo isset($_POST["jf2"]) ? $_POST["jf2"] : ''; ?>`;
                document.getElementById('jf3').value = `<?php echo isset($_POST["jf3"]) ? $_POST["jf3"] : ''; ?>`;
                document.getElementById('d').value = dosData;
                document.getElementById('dos').value = dosData;
                document.getElementById('h').value = `<?php echo isset($_POST["h"]) ? $_POST["h"] : ''; ?>`;

                document.getElementById('cf').value = `<?php echo isset($_POST["cf"]) ? $_POST["cf"] : ''; ?>`;
                document.getElementById('dosf').value = `<?php echo isset($_POST["dosf"]) ? $_POST["dosf"] : ''; ?>`;
                document.getElementById('dosf1').value = `<?php echo isset($_POST["dosf1"]) ? $_POST["dosf1"] : ''; ?>`;
                document.getElementById('htmlf').value = `<?php echo isset($_POST["htmlf"]) ? $_POST["htmlf"] : ''; ?>`;
                document.getElementById('htmlf1').value = `<?php echo isset($_POST["htmlf1"]) ? $_POST["htmlf1"] : ''; ?>`;
                document.getElementById('dosM').value = `<?php echo isset($_POST["dosM"]) ? $_POST["dosM"] : ''; ?>` || `UTF-8`;
                document.getElementById('cf1').value = `<?php echo isset($_POST["cf1"]) ? $_POST["cf1"] : ''; ?>`;
                document.getElementById('cf2').value = `<?php echo isset($_POST["cf2"]) ? $_POST["cf2"] : ''; ?>`;
                document.getElementById('prejs').value = `<?php echo isset($_POST["prejs"]) ? $_POST["prejs"] : ''; ?>`;
                document.getElementById('prejf').value = `<?php echo isset($_POST["prejf"]) ? $_POST["prejf"] : ''; ?>`;
                document.getElementById('cjd').value = `<?php echo isset($_POST["cjd"]) ? $_POST["cjd"] : ''; ?>`;
                document.getElementById('imgs').value = `<?php echo isset($_POST["imgs"]) ? $_POST["imgs"] : ''; ?>`;
                document.getElementById('imgf').value = `<?php echo isset($_POST["imgf"]) ? $_POST["imgf"] : ''; ?>`;
                document.getElementById('prevals').value = `<?php echo isset($_POST["prevals"]) ? $_POST["prevals"] : ''; ?>`;
                document.getElementById('queryParams').value = `<?php echo isset($_POST["queryParams"]) ? $_POST["queryParams"] : ''; ?>`;
                document.getElementById('prejs').value = `<?php echo isset($_POST["prejs"]) ? $_POST["prejs"] : ''; ?>`;
                document.getElementById('prejf').value = `<?php echo isset($_POST["prejf"]) ? $_POST["prejf"] : ''; ?>`;

                <?php
					$randTime = isset($_POST["time"]) ? $_POST["time"] : '';
					if ($randTime == '') {
						$randTime = date('YmdHis');
					}
					echo "document.getElementById('time').value = `".$randTime."`;";
					
					// 音源ファイルの読込と設定
					// POSTされたデータの中に音源ファイル名が含まれている場合は、その情報を使う
					$tempfile = isset($_FILES['musicFile']['tmp_name']) ? $_FILES['musicFile']['tmp_name'] : '';
					$filename = escapeStrMusic(basename(isset($_FILES['musicFile']['name']) ? $_FILES['musicFile']['name'] : ''));
					$uploadFile = $rootDir.'/tmp/'.$randTime.'_'.$filename;
					if (is_uploaded_file($tempfile) && isset($_FILES['musicFile']['error']) && $_FILES['musicFile']['error'] == UPLOAD_ERR_OK) {
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						if (in_array($ext, array('mp3', 'ogg', 'oga', 'mp4', 'm4a', 'aac', 'flac', 'js'))) {
							if ( move_uploaded_file($tempfile , $uploadFile )) {
								echo "document.getElementById('dos').value += `|musicUrl=".$randTime.'_'.$filename."|`;";
								echo "document.getElementById('mf').value = `".$filename."`;\n";
							}
						}
					}

					// カスタムJSファイルの読込と設定
					// POSTされたデータの中にカスタムJSファイル名が含まれている場合は、その情報を使う
					$jsFlg = 0;
					$customJsList = "";
					for ($i = 1; $i <= 3; $i++) {
						$tempJsFile = isset($_FILES['jsFile'.$i]['tmp_name']) ? $_FILES['jsFile'.$i]['tmp_name'] : '';
						$fileJsName = basename(isset($_FILES['jsFile'.$i]['name']) ? $_FILES['jsFile'.$i]['name'] : '');
						$uploadJsFile = $rootDir.'/tmp/'.$randTime.'_'.$fileJsName;
						$resetFlg = 0;
						
						if (is_uploaded_file($tempJsFile) && isset($_FILES['jsFile'.$i]['error']) && $_FILES['jsFile'.$i]['error'] == UPLOAD_ERR_OK) {
							$ext = pathinfo($fileJsName, PATHINFO_EXTENSION);
						    if (in_array($ext, array('js'))) {
								if ( move_uploaded_file($tempJsFile , $uploadJsFile )) {
									if ($customJsList != '') {
										$customJsList = $customJsList.",";
									}
									$customJsList = $customJsList.'../music/'.$randTime.'_'.$fileJsName;
									echo "document.getElementById('jf".$i."').value = `".$fileJsName."`;";
									$jsFlg = 1;
								}
							}
						} else if (isset($_POST["jf".$i]) && $_POST["jf".$i] != '') {
							$customJsList = $customJsList.'../music/'.$randTime.'_'.$_POST["jf".$i];
							$jsFlg = 1;
						}
					}
					if ($jsFlg == 1) {
						echo "document.getElementById('dos').value += `|customJs=".$customJsList."|`;";
						echo "document.getElementById('jf').value = `".$customJsList."`;\n";
					}

				?>

                // 楽曲名情報の設定 (musicUrlについては指定の有無によらず一旦"nosound.mp3"で上書きし、既存のデータは使わない)
                if (dosData.indexOf('|musicTitle=') !== -1) {
                    document.getElementById('dos').value += `|musicUrl=nosound.mp3|`;
                } else {
                    document.getElementById('dos').value += musicData_g;
                }

                if (difData_g !== '') {
                    const tmpDifData = [];
                    difData_g.split(`$`).forEach(difs => tmpDifData.push(difs.split(`,`).length > 2 ? difs : `${difs},Normal,3.5`));
                    document.getElementById('dos').value += `|difData=${tmpDifData.join('$')}|`;
                    document.getElementById('k').value = difData_g;
                }

                // 音源ファイルの設定 (ファイル名が動的に変わるためここで設定)
                if (document.getElementById('mf').value !== '') {
                    document.getElementById('dos').value += `|musicUrl=${document.getElementById('time').value + `_` + document.getElementById('mf').value}|`;
                }

                // カスタムJSファイルの設定
                if (document.getElementById('jf').value !== '') {
                    document.getElementById('dos').value += `|customjs=${document.getElementById('jf').value.replaceAll(`(..)/tmp/`, `../music/`)}|`;
                }

                // 選択したバージョンに関する情報取得
                // URLにバージョン名が含まれているため、その情報を取得する
                const version = document.getElementById('v');
                const vElements = version.options;

                let baseVersion;
                let linkVersion;
                let versionj = 0;
                const matches = location.href.match(/\/danonicw-v([\dx.-]+[\(A-Za-z0-9\)\s]*)\//);
                if (matches !== null) {
                    baseVersion = matches[1];
                    document.getElementById(`cver`).innerHTML = `v${baseVersion}`;
                }
                for (let j = 0; j < vElements.length; j++) {
                    if (vElements[j].text.split(' ')[0] === "v" + baseVersion) {
                        linkVersion = vElements[j].text.split('(').join('-').split(' ').join('-').split('-')[0];
                        vElements[j].selected = true;
                        versionj = j;
                    }
                }
                if (versionj === vElements.length - 1) {
                    document.getElementById(`old`).style.color = `#999999`;
                    document.getElementById(`oldh`).style.color = `#999999`;
                }
                const majorVersion = baseVersion.split(`.`)[0];

                // v14.5.1より前はエスケープ文字表記に未対応のため、全角文字に置き換える
                if (compareVersions(baseVersion, '14.5.1') < 0) {
                    document.getElementById('dos').value = escapeStrOld(document.getElementById('dos').value);
                }

                // v3.6.0より前はカスタム設定がdanoni_main.jsで未定義のため追加
                if (compareVersions(baseVersion, '3.6.0') < 0) {
                    [`Title`, `TitleArrow`, `Back`, `BackMain`, `Ready`].forEach(pattern => {
                        if (dosData.indexOf(`custom${pattern}Use`) === -1) {
                            document.getElementById('dos').value += `|custom${pattern}Use=false|`;
                        }
                    });
                }

                // v1.0.1より前はReleaseタグが無いため、Releaseを非表示化しデフォルトの値を入れる
                if (compareVersions(baseVersion, '1.0.1') >= 0) {
                    document.getElementById('versionLink').href = `https://github.com/cwtickle/danoniplus/releases/tag/${linkVersion}`;
                    document.getElementById('updateInfo').href = `https://github.com/cwtickle/danoniplus/wiki/UpdateInfo#-v${majorVersion}-changelog`;
                } else {
                    document.getElementById('versionLink').href = `https://github.com/cwtickle/danoniplus/releases/tag/v1.0.1`;
                    document.getElementById('updateInfo').href = `https://github.com/cwtickle/danoniplus/wiki/UpdateInfo#-v1-changelog`;
                }
                document.getElementById('changelog').href = `https://github.com/cwtickle/danoniplus/wiki/Changelog-v${majorVersion}`;
                document.getElementById('versionLink').style.visibility = compareVersions(baseVersion, '1.0.1') >= 0 ? `visible` : `hidden`;

                // v0.62.xより前はカスタムキーの定義名が一部異なるため置換
                if (compareVersions(baseVersion, '0.62.x') < 0) {
                    document.getElementById('dos').value = document.getElementById('dos').value?.replaceAll(`|chara`, `|headerDat`);
                    document.getElementById('dos').value = document.getElementById('dos').value?.replaceAll(`|color`, `|arrBaseMC`);
                }
                // v0.53.xより前は各主要項目の補完処理が無いため、初期値を入れる
                if (compareVersions(baseVersion, '0.53.x') < 0) {
                    if (dosData.indexOf('|setColor=') === -1) {
                        document.getElementById('dos').value += `
								|setColor=0xcccccc,0xff9999,0xffffff|`;
                    }
                    if (dosData.indexOf('|frzColor=') === -1) {
                        document.getElementById('dos').value += `
								|frzColor=0x999999,0x999999,0x999999,0x999999,0x999999|`;
                    }
                    if (dosData.indexOf('|tuning=') === -1) {
                        document.getElementById('dos').value += `|tuning=name|`;
                    }
                    if (dosData.indexOf('|boost_data=') === -1) {
                        document.getElementById('dos').value += `|boost_data=200,1|`;
                    }
                }
                // v0.43.xより前はdifDataの補完処理が無いため、初期値を入れる
                if (compareVersions(baseVersion, '0.43.x') < 0 && dosData.indexOf('|difData=') === -1 && difData_g === '') {
                    document.getElementById('dos').value += `
							|difData=7,Normal,3.5|`;
                }
                // v0.28.xより前は"|"区切りに対応していないため、"&"区切りで代用
                if (compareVersions(baseVersion, '0.28.x') < 0) {
                    document.getElementById('dos').value = document.getElementById('dos').value.split('|').join('&');
                }
                v.style.backgroundColor = v.options[v.selectedIndex].style.backgroundColor;

                // 譜面エリアにフォーカスが当たっているときだけ、onkeydown, oncontextmenu の設定をリセット
                let bkEvent, bkEventCxt;
                const dfEvent = evt => {};
                const dfCxt = evt => true;

                [`d`, `k`].forEach(txt => {
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
                    });
                });

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

                // 作品別のローカルストレージデータを必要最低限に設定
                const baseUrl = new URL(location.href).toString();
                const storageOrg = JSON.parse(localStorage.getItem(`${urlDomain}/`));
                if (storageOrg) {
                    localStorage.setItem(baseUrl, JSON.stringify({
                        adjustment: Math.round(storageOrg.adjustment || 0),
                        volume: storageOrg.volume || 100,
                        appearance: storageOrg.appearance || `Visible`,
                        opacity: storageOrg.opacity || 100,
                        hitPosition: storageOrg.hitPosition || 0,
                        colorType: storageOrg.colorType || `Default`,
                    }));
                }

                // danoni_main.jsの読込
                if (dosData !== null) {
                    const width_g = `<?php echo $_POST["w"]; ?>` || `800px`;
                    const list = document.getElementById('w');
                    const elements = list.options;
                    for (let j = 0; j < elements.length; j++) {
                        if (elements[j].value === width_g) {
                            elements[j].selected = true;
                        }
                    }
                    document.getElementById('canvas-frame').style.width = width_g;
                    document.getElementById('canvas-frame').style.margin = `auto`;
                    document.getElementById('layer0').width = parseInt(width_g);
                    document.getElementById('layer1').width = parseInt(width_g);
                    document.getElementById('layer2').width = parseInt(width_g);

                    loadScript_local(`../js/danoni_main.js`, false);
                    importCssFile_local(`../css/danoni_main.css`);

                    if (compareVersions(baseVersion, '4.1.0') < 0) {
                        setTimeout(_ => {
                            if (typeof g_stateObj === `object`) {
                                g_stateObj.adjustment = storageOrg?.adjustment || 0;
                                g_stateObj.volume = storageOrg?.volume || 100;
                            }
                        }, 1000);
                    }

                } else {
                    document.getElementById('canvas-frame').innerHTML = 'ゲームを準備しています...<br>このメッセージがいつまでも消えない場合、<br>Google ChromeやFirefox等、HTML5に対応したブラウザをご利用ください。';
                }

                let queryParams = ``;
                if (document.getElementById(`queryParams`)?.value !== ``) {
                    queryParams = `?${document.getElementById(`queryParams`).value}`;
                }

                // バージョン変更時の処理
                function getVersion(obj) {
                    const idx = obj.selectedIndex;
                    const text = obj.options[idx].text;
                    const value = obj.options[idx].value;
                    const versionTxt = value === `` ? `10000.0.0` : text.slice(1);

                    let redirectUrl;
                    if (compareVersions(versionTxt, '19.4.0') < 0) {
                        redirectUrl = value.slice(1, -17) + `preview/`;
                    } else {
                        redirectUrl = `/`;
                    }
                    document.getElementById('formV').action = redirectUrl + queryParams;
                    document.getElementById('formV').submit();
                }

                // 横幅変更時の処理
                function getWidth(obj) {
                    document.getElementById('formV').action = `./` + queryParams;
                    document.getElementById('formV').submit();
                }

                // 次のバージョンへ遷移
                function jumpNext() {
                    if (vElements[versionj + 1]?.value === undefined) {
                        return false;
                    }
                    versionj++;
                    document.getElementById(`v`).value = vElements[versionj].value;
                    document.getElementById('formV').action = vElements[versionj].value.slice(1, -17) + `preview/` + queryParams;
                    document.getElementById('formV').submit();
                }

                // 前のバージョンへ遷移
                function jumpPrev() {
                    versionj--;
                    document.getElementById(`v`).value = vElements[versionj].value;

                    let redirectUrl;
                    if (compareVersions(vElements[versionj].text.slice(1), '19.4.0') < 0) {
                        redirectUrl = vElements[versionj].value.slice(1, -17) + `preview/`;
                    } else {
                        redirectUrl = `/`;
                    }
                    document.getElementById('formV').action = redirectUrl + queryParams;
                    document.getElementById('formV').submit();
                }

                // キー別のローカルストレージを削除
                function removeKeySave() {
                    if (window.confirm("Delete local storage by keymode. Is it OK?\nキー別のローカルストレージを削除します。よろしいですか？")) {
                        Object.keys(localStorage).filter(key => key.startsWith(`danonicw-`)).forEach(key => localStorage.removeItem(key));
                    }
                }

                function cancelFile(_name) {
                    document.getElementById(_name).value = ``;
                }

                function confirmCancelFile(...names) {
                    if (window.confirm("Clears references to uploaded files. Is it OK?\nアップロードしたファイルの参照をクリアします。よろしいですか？")) {
                        names.forEach(name => document.getElementById(name).value = ``);
                    }
                }

                // 特殊文字を全角文字に置換 (v14.5.0以前用)
                function escapeStrOld(_str) {
                    const escList = [
                        ['*bkquo*', '‘'],
                        ['*squo*', "’"],
                        ['*quot*', '”'],
                        ['*amp*', '＆']
                    ];
                    escList.forEach(rep => _str = _str.replaceAll(rep[0], rep[1]));
                    return _str;
                }
                </script>
                <hr>
                <p style="text-align:center;">
                    <a id="removeKey" onclick="removeKeySave()">Remove local storage by keymode / キー別のローカルストレージを削除</a><br>
                </p>
                <hr>
                <p>
                    This site is a version verification site for Dancing Onigiri (CW Edition). <br>
                    This page deals with an older version, so some functions are limited.<br>
                    Also, there may be unforeseen glitches that remain. Please use this site within the bounds of common sense. <br>
                    We are not responsible for any problems that may occur on this site.<br>
                </p>
                <p>
                    * Uploaded data is automatically deleted at 6:00 a.m. (GMT+9) every day.<br>
                </p>
                <hr style="border:none;border-top:dashed 1px #cccccc;height:1px;color:#FFFFFF;">
                <p>
                    このサイトは、Dancing☆Onigiri (CW Edition)のバージョン検証サイトです。<br>
                    このページでは古いバージョンを扱っているため、一部機能制限があります。<br>
                    また、予期せぬ不具合が残っていることがあります。良識の範囲内でお使いください。<br>
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