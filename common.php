<?php
/**
 * index.php / jsdelivr.php / unpkg.php / preview_classic.php で共有するユーティリティ関数群
 *
 * 各ファイルからは先頭で以下のように読み込む想定:
 *   require_once __DIR__ . '/common.php';
 */

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
    if (preg_match('/\/danoni(?:cw|plus)-v([\dx.-]+[\(A-Za-z0-9\)\s]*)\//', $filename, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * 所定のディレクトリパスに合致するリストを取得
 * @return String
 */
function findFilesMatchingPattern() {
    $files = [];
    $dirContents = glob('./v*/_preview/danoni{cw,plus}-*/js/danoni_main.js', GLOB_BRACE) ?: [];

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

/**
 * ホスト名から配置先のルートディレクトリ・ルートURLを判定する
 * (index.php / jsdelivr.php / unpkg.php / preview_classic.php で共通)
 *
 * @return array{0: string, 1: string} [$rootDir, $rootUrl]
 */
function resolveRootPaths() {
    // 1. 物理ルートディレクトリの判定
    // common.php が置かれているディレクトリ（あるいはこのプロジェクトのルート）を基準にする
    // ※ common.php と同一階層がルートの場合は __DIR__ でOK
    $rootDir = realpath(__DIR__); 

    // Windows環境等のパス区切り文字（\）をスラッシュ（/）に統一
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
    $normalizedRootDir = str_replace('\\', '/', $rootDir);

    // 2. ドキュメントルートからの相対パス（サブディレクトリのパス）を算出
    // 例: ドキュメントルートが /var/www/html、$rootDir が /var/www/html/danoni/ の場合 -> /danoni
    $subDir = '';
    if ($docRoot !== '' && strpos($normalizedRootDir, $docRoot) === 0) {
        $subDir = substr($normalizedRootDir, strlen($docRoot));
    }
    $subDir = rtrim($subDir, '/') . '/';

    // 3. ルートURLの生成
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $rootUrl = $scheme . '://' . $host . $subDir;

    return [$rootDir, $rootUrl];
}

/**
 * バージョン選択用の<option>一覧を出力する (index.php / jsdelivr.php / unpkg.php で共通)
 *
 * $matchingFiles の並べ替え・背景色判定ロジックは完全に共通だが、
 * バージョン文字列の取り出し方・<option value="...">の組み立て方が
 * ローカル版(index.php)とCDN版(jsdelivr.php/unpkg.php)とで異なるため、
 * それぞれをコールバックとして受け取る。
 *
 * @param array $matchingFiles ソート前のファイル一覧 (ローカルパス、またはCDN版はバージョン文字列そのもの)
 * @param callable $extractVersion function(string $file): string  $fileからバージョン文字列を取り出す
 * @param callable $buildValue     function(string $file): string  <option value="...">に入れる値を組み立てる
 * @return string 最新メジャーバージョンのファイル (基準バージョンのパス)
 */
function renderVersionOptions(array $matchingFiles, callable $extractVersion, callable $buildValue) {
    usort($matchingFiles, function ($a, $b) use ($extractVersion) {
        $versionA = $extractVersion($a);
        $versionB = $extractVersion($b);

        return compareSemanticVersions($versionA, $versionB) * (-1);
    });

    $latestVerPath = '';
    if (!empty($matchingFiles)) {
        $prevVer = '';
        foreach ($matchingFiles as $file) {
            $bgcolor = '#1a1a2e';
            $fgcolor = '#cccccc';
            $addSymbol = '';
            $versionName = $extractVersion($file);
            $versionMajor = explode('.', $versionName)[0];
            if (strpos($versionName, '-') !== false) {
                $bgcolor = '#4a2020';
                $fgcolor = '#ffaaaa';
            } else if ($prevVer !== $versionMajor) {
                if ($latestVerPath == '') {
                    $latestVerPath = $file;
                }
                if (strpos($versionName, '(final)') !== false) {
                    $bgcolor = '#3a2040';
                    $fgcolor = '#eeaaee';
                } else if (compareSemanticVersions($versionName, '1.0.0') < 0) {
                    $bgcolor = '#2a2a30';
                    $fgcolor = '#999999';
                } else {
                    $bgcolor = ($prevVer == '' ? '#20204a' : '#4a4a20');
                    $fgcolor = ($prevVer == '' ? '#bbbbff' : '#dddd99');
                    $addSymbol = ' *';
                }
                $prevVer = $versionMajor;
            } else if (compareSemanticVersions($versionName, '19.4.1') < 0) {
                $bgcolor = '#2a2a30';
                $fgcolor = '#999999';
            }
            echo '<option value="' . $buildValue($file) . '" style="background-color:' . $bgcolor . ';color:' . $fgcolor . ';">v' . $versionName . $addSymbol . '</option>' . "\n";
        }
    }

    return $latestVerPath;
}

/**
 * アップロードされたファイルを処理し、$rootDir/tmp/ 以下へ保存する。
 * (index.php / jsdelivr.php / unpkg.php / preview_classic.php で共通)
 *
 * $_POST への表示用ファイル名 (mf, dosf1, htmlf1, jf1〜3, cf1〜2, imgs, prejs) の
 * 反映も行う (副作用として $_POST を直接書き換える。既存の呼び出し元の挙動を踏襲)。
 *
 * @param string $rootDir 保存先のルートディレクトリ
 * @param string $randTime タイムスタンプ文字列 (保存ファイル名の接頭辞に使う)
 * @return array{0: array, 1: array} [$uploaded, $uploadedPreJs]
 */
function processFileUploads($rootDir, $randTime) {
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

    /*-----------------------------------
     * 音源ファイル
     *-----------------------------------*/
    if (!empty($_FILES['musicFile']['tmp_name']) && $_FILES['musicFile']['error'] === UPLOAD_ERR_OK) {
        $filename = escapeStrMusic(basename($_FILES['musicFile']['name']));
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, ['mp3','mp4','m4a','ogg','oga','aac','flac','wav','js'])) {
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
     * カスタムJS（複数）
     *-----------------------------------*/
    if (!empty($_FILES['jsFiles']['name'][0])) {
        $jsList = [];

        foreach ($_FILES['jsFiles']['name'] as $idx => $name) {
            if ($_FILES['jsFiles']['error'][$idx] === UPLOAD_ERR_OK) {
                $filename = basename($name);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if ($ext === 'js') {
                    $savedName = $randTime . '_' . $filename;
                    $uploadPath = $rootDir . '/tmp/' . $savedName;

                    if (move_uploaded_file($_FILES['jsFiles']['tmp_name'][$idx], $uploadPath)) {
                        $uploaded['js'][] = $savedName;
                        $jsList[] = $filename; // 表示用
                    }
                }
            }
        }

        $_POST['jfs'] = implode(',', $jsList);
    }

    /*-----------------------------------
     * カスタムCSS（複数）
     *-----------------------------------*/
    if (!empty($_FILES['cssFiles']['name'][0])) {
        $cssList = [];

        foreach ($_FILES['cssFiles']['name'] as $idx => $name) {
            if ($_FILES['cssFiles']['error'][$idx] === UPLOAD_ERR_OK) {
                $filename = basename($name);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if ($ext === 'css') {
                    $savedName = $randTime . '_' . $filename;
                    $uploadPath = $rootDir . '/tmp/' . $savedName;

                    if (move_uploaded_file($_FILES['cssFiles']['tmp_name'][$idx], $uploadPath)) {
                        $uploaded['css'][] = $savedName;
                        $cssList[] = $filename; // 表示用
                    }
                }
            }
        }

        $_POST['cfs'] = implode(',', $cssList);
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

    return [$uploaded, $uploadedPreJs];
}

/**
 * serverData用の配列を構築する (post/env/internal/upload セクションは共通、
 * version セクションのみ呼び出し元から渡してもらう)
 *
 * @param array $uploaded processFileUploads() の戻り値の1つ目
 * @param array $uploadedPreJs processFileUploads() の戻り値の2つ目
 * @param string $randTime タイムスタンプ文字列
 * @param string $rootDir
 * @param string $rootUrl
 * @param array $versionInfo ['selected'=>.., 'param'=>.., 'latest'=>.., 'type'=>..]
 * @return array json_encode に渡す連想配列
 */
function buildServerData($uploaded, $uploadedPreJs, $randTime, $rootDir, $rootUrl, $versionInfo) {
    return [

        // -----------------------------
        // 1. POST で送られてくる値
        // -----------------------------
        'post' => [
            'd'        => $_POST['d']        ?? '',
            'k'        => $_POST['k']        ?? '',
            'mf'       => $_POST['mf']       ?? '',
            'jf'       => $_POST['jf']       ?? '',
            'jfs'      => $_POST['jfs']      ?? '',
            'cf'       => $_POST['cf']       ?? '',
            'cfs'      => $_POST['cfs']      ?? '',
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
            'w'        => $_POST['w']        ?? '',
            'h'        => $_POST['h']        ?? '500px',
            'time'     => $_POST['time']     ?? '',
        ],

        // -----------------------------
        // 2. バージョン情報
        // -----------------------------
        'version' => $versionInfo,

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
    ];
}