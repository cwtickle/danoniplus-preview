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