<?php
/**
 * index.php / index2.php / preview_classic.php で共有するユーティリティ関数群
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

/**
 * ホスト名から配置先のルートディレクトリ・ルートURLを判定する
 * (index.php / index2.php / preview_classic.php で共通)
 *
 * @return array{0: string, 1: string} [$rootDir, $rootUrl]
 */
function resolveRootPaths() {
    $rootDir = '';
    $rootUrl = '';
    if ($_SERVER['HTTP_HOST'] === 'danonicw.skr.jp') {
        $rootDir = '/home/cw7/www/danonicw';
        $rootUrl = 'https://danonicw.skr.jp/';
    } else if ($_SERVER['HTTP_HOST'] === 'tickle.cloudfree.jp') {
        $rootDir = '/home/tickle/tickle.cloudfree.jp/public_html';
        $rootUrl = 'https://tickle.cloudfree.jp/';
    }
    return [$rootDir, $rootUrl];
}

/**
 * バージョン選択用の<option>一覧を出力する (index.php / index2.php で共通)
 *
 * $matchingFiles の並べ替え・背景色判定ロジックは完全に共通だが、
 * バージョン文字列の取り出し方・<option value="...">の組み立て方が
 * ローカル版(index.php)とCDN版(index2.php)とで異なるため、
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
            $bgcolor = '#ffffff';
            $addSymbol = '';
            $versionName = $extractVersion($file);
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
            echo '<option value="' . $buildValue($file) . '" style="background-color:' . $bgcolor . ';">v' . $versionName . $addSymbol . '</option>' . "\n";
        }
    }

    return $latestVerPath;
}