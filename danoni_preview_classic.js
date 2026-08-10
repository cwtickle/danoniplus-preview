/**
 * preview_classic.php 専用のスクリプト。
 *
 * loadScript_local / importCssFile_local / compareVersions / removeKeySave /
 * cancelFile / confirmCancelFile / applyServerDataToForm /
 * applyLocalStorageDefaults は danoni_preview_main.js 側で定義済みのため、
 * このファイルを読み込む前に danoni_preview_main.js を読み込んでおくこと。
 *
 *   <script src="<rootUrl>danoni_preview_main.js"></script>
 *   <script src="<rootUrl>danoni_preview_classic.js"></script>
 */

const { dosData, difData_g: initialDifData_g, urlDomain, musicData_g } = applyServerDataToForm({
    noSoundPath: `nosound.mp3`,
    uploadPrefix: {
        music: `../music/`,
        js: `../music/`,
        css: `../music/`,
        img: `../music/`,
        prejs: `../music/`,
    },
});
let difData_g = initialDifData_g;

applyPreviewDosCommonData({
    dosData,
    difDataValue: difData_g,
    musicDataValue: musicData_g,
    noSoundPath: `nosound.mp3`,
});

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

// danoni.js が baseVersion をグローバル変数として
// 直接参照するため、window に公開しておく必要がある
// (このファイルはトップレベルのスクリプトなので実質的には既にグローバルだが、
//  main.js側と同じ書き方・同じコメントにして grep で揃って見つかるようにしている)
window.baseVersion = baseVersion;

for (let j = 0; j < vElements.length; j++) {
    if (vElements[j].text.split(' ')[0] === "v" + baseVersion) {
        linkVersion = vElements[j].text.split('(').join('-').split(' ').join('-').split('-')[0];
        vElements[j].selected = true;
        versionj = j;
    }
}
if (versionj === vElements.length - 1) {
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
        if (!hasStringMarker(dosData, `custom${pattern}Use`)) {
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
    if (!hasStringMarker(dosData, '|setColor=')) {
        document.getElementById('dos').value += `
                                    |setColor=0xcccccc,0xff9999,0xffffff|`;
    }
    if (!hasStringMarker(dosData, '|frzColor=')) {
        document.getElementById('dos').value += `
                                    |frzColor=0x999999,0x999999,0x999999,0x999999,0x999999|`;
    }
    if (!hasStringMarker(dosData, '|tuning=')) {
        document.getElementById('dos').value += `|tuning=name|`;
    }
    if (!hasStringMarker(dosData, '|boost_data=')) {
        document.getElementById('dos').value += `|boost_data=200,1|`;
    }
}
// v0.43.xより前はdifDataの補完処理が無いため、初期値を入れる
if (compareVersions(baseVersion, '0.43.x') < 0 && !hasStringMarker(dosData, '|difData=') && difData_g === '') {
    document.getElementById('dos').value += `
                                |difData=7,Normal,3.5|`;
}
// v0.28.xより前は"|"区切りに対応していないため、"&"区切りで代用
if (compareVersions(baseVersion, '0.28.x') < 0) {
    document.getElementById('dos').value = document.getElementById('dos').value.split('|').join('&');
}
v.style.backgroundColor = v.options[v.selectedIndex].style.backgroundColor;

enhanceVersionSelect('v');

// 譜面エリアにフォーカスが当たっているときだけ、onkeydown, oncontextmenu の設定をリセット
setupPreviewFocusReset([`d`, `k`, `vSearchInput`]);

const { storageOrg } = applyLocalStorageDefaults({
    urlDomain,
    useCurrentFallback: false,
    writeIfMissing: false,
    bootstrapDomainDefault: false,
    roundAdjustment: (adj) => Math.round(adj),
});

// danoni_main.jsの読込
if (dosData !== null) {
    const width_g = serverData.post.w || `800px`;
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
