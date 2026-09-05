<?php
require_once __DIR__ . '/common.php';

$updateTimestamp = '2026-09-01_220000';

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

    <title>Dancing☆Onigiri Preview</title>

    <link rel="stylesheet" href="<?php echo $rootUrl; ?>danoni_preview_common.css?<?php echo $updateTimestamp; ?>">
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
        'selected' => $_POST['v'] ?? '',
        'param'    => '',
        'latest'   => '',
        'type'     => 'local',
    ]
));
?>;
</script>
</head>

<body>
    <table>
        <tr>
            <td>
                <form method="post" action="./" id="formV" name="formV" style="text-align:center;" enctype="multipart/form-data">
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
                            <a href="./" onclick="return confirm('データはリセットされます。よろしいですか？\nData will be reset. Is it OK?');">
                                <span class="title1">D</span>ancing☆<span class="title2">O</span>nigiri <span class="title3">P</span>review
                            </a>
                            (<a href="/" onclick="return confirm('データはリセットされます。よろしいですか？\nData will be reset. Is it OK?');">Index</a>)
                        </span>
                    </div>
                    <div class="header-controls">
                        <span class="header-label">Ver</span>
                        <span class="tt-wrap">
                            <span class="tt-icon" tabindex="0">i</span>
                            <span class="tt-box tt-box-title">
                                <a id="versionLink" target="release">Release</a>
                                <a id="changelog" target="changelog">Changelog</a>
                                <a id="updateInfo" target="updateInfo">UpdateInfo</a>
                            </span>
                        </span>
                        <a href="javascript:jumpPrev();" class="nav-arrow">▲</a>
                        <span id="cver" style="display:none;"></span>
                        <select class="select select-version" name="v" id="v" onchange="getVersion(this);">
                            <?php
                            $matchingFiles = findFilesMatchingPattern();
                            renderVersionOptions(
                                $matchingFiles,
                                function ($file) {
                                    return extractVersionFromFilename($file);
                                },
                                function ($file) {
                                    return $file;
                                }
                            );
                            ?>
                        </select>
                        <a id="oldh" href="javascript:jumpNext();" class="nav-arrow">▼</a>
                    </div>
                </div>

                <input type="hidden" name="dos" id="dos">
                <div id="canvas-frame">
                    <canvas id="layer0" width="800" height="500"></canvas>
                    <canvas id="layer1" width="800" height="500"></canvas>
                    <canvas id="layer2" width="800" height="500"></canvas>
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
                            <a id="editorsub" onclick="window.open('https://superkuppabros.github.io/danoni-editor/', '_blank', 'width=800px,height=500px');return false;">Dancing☆Onigiri Editor</a>
                            (<a href="<?php echo $rootUrl; ?>editor/" target="_blank">Mirror</a>) ↗
                        </span>
                        <span class="chart-card-actions">
                            <input type="submit" value="Load / 譜面読込" class="btn-primary">
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
                                        <td>Music File<br>楽曲ファイル<br><a class="pill-link" onclick="confirmCancelFile(`musicFile`, `mf`);">Reset</a></td>
                                        <td>
                                            <input type="file" id="musicFile" name="musicFile" accept=".mp3,.mp4,.m4a,.ogg,.oga,.aac,.flac,.js"> <a onclick="cancelFile(`musicFile`);">Cancel</a><br>
                                            Uploaded: <input type="text" name="mf" id="mf" style="width:75%" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Custom JS Files<br><a class="pill-link" onclick="confirmCancelFile(`jsFiles`, `jfs`, `jf`);">Reset</a></td>
                                        <td>
                                            <input type="file" id="jsFiles" name="jsFiles[]" accept=".js" title="" multiple> <a onclick="cancelFile(`jsFiles`);">Cancel</a><br>
                                            Uploaded: <input type="text" name="jfs" id="jfs" style="width:75%" readonly><br>
                                            <input type="hidden" name="jf" id="jf">
                                            <input type="hidden" name="time" id="time">
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
                                            <?php
                                                for ($width = 500; $width <= 1100; $width += 50) {
                                                    echo '<option value="' . $width . 'px">W: ' . $width . 'px</option>' . "\n";
                                                }
                                            ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Keys(difData)<br>キー数情報</td>
                                        <td>
                                            <input type="text" name="k" id="k">
                                            <a onclick="cancelFile(`k`);">Cancel</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>


                    <input type="hidden" name="cf" id="cf">
                    <input type="hidden" name="dosf" id="dosf">
                    <input type="hidden" name="dosf1" id="dosf1">
                    <input type="hidden" name="htmlf" id="htmlf">
                    <input type="hidden" name="htmlf1" id="htmlf1">
                    <input type="hidden" name="dosM" id="dosM">
                    <input type="hidden" name="cfs" id="cfs">
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
                <script src="<?php echo $rootUrl; ?>danoni_preview_main.js?<?php echo $updateTimestamp; ?>"></script>
                <script src="<?php echo $rootUrl; ?>danoni_preview_classic.js?<?php echo $updateTimestamp; ?>"></script>
                <script type="text/javascript">
                    initDanoniPreviewClassic();
                </script>
                <hr>
                <p style="text-align:center;">
                    <a id="removeKey" onclick="removeKeySave()">Remove local storage by keymode / キー別のローカルストレージを削除</a><br>
                </p>
                <div class="chart-card">
                    This site is a version verification site for Dancing Onigiri (CW Edition). <br>
                    This page deals with an older version, so some functions are limited.<br>
                    Also, there may be unforeseen glitches that remain. Please use this site within the bounds of common sense. <br>
                    We are not responsible for any problems that may occur on this site.<br>
                    <br>
                    * Uploaded data is automatically deleted at 6:00 a.m. (GMT+9) every day.<br>
                </div>
                <div class="chart-card">
                    このサイトは、Dancing☆Onigiri (CW Edition)のバージョン検証サイトです。<br>
                    このページでは古いバージョンを扱っているため、一部機能制限があります。<br>
                    また、予期せぬ不具合が残っていることがあります。良識の範囲内でお使いください。<br>
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