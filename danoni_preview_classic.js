/**
 * preview_classic.php 専用のスクリプト。
 *
 * loadScript_local / importCssFile_local / compareVersions / removeKeySave /
 * cancelFile / confirmCancelFile は danoni_preview_main.js 側で定義済みのため、
 * このファイルを読み込む前に danoni_preview_main.js を読み込んでおくこと。
 *
 *   <script src="<rootUrl>danoni_preview_main.js"></script>
 *   <script src="<rootUrl>danoni_preview_classic.js"></script>
 */

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

document.getElementById('d').value = dosData;
document.getElementById('dos').value = dosData;

// ▼ 楽曲ファイル
if (serverData.upload.music) {
    // 表示用は元ファイル名（タイムスタンプなし）
    document.getElementById('mf').value = serverData.post.mf || '';

    // 読み込み用はタイムスタンプ付きファイル名
    document.getElementById('dos').value +=
        `|musicUrl=../music/${serverData.upload.music}|`;
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
    document.getElementById('htmlf').value = serverData.upload.html;
}

// ▼ カスタムJS
if (serverData.upload.js.length > 0) {
    // 表示用（タイムスタンプなし）
    document.getElementById('jf1').value = serverData.post.jf1 || '';
    document.getElementById('jf2').value = serverData.post.jf2 || '';
    document.getElementById('jf3').value = serverData.post.jf3 || '';

    // 読み込み用（タイムスタンプ付き）
    const jsList = serverData.upload.js.map(f => `../music/${f}`).join(',');
    document.getElementById('jf').value = jsList;
    document.getElementById('dos').value += `|customjs=${jsList}|`;
}

// ▼ カスタムCSS
if (serverData.upload.css.length > 0) {
    document.getElementById('cf1').value = serverData.post.cf1 || '';
    document.getElementById('cf2').value = serverData.post.cf2 || '';

    const cssList = serverData.upload.css.map(f => `../music/${f}`).join(',');
    document.getElementById('cf').value = cssList;
    document.getElementById('dos').value += `|customcss=${cssList}|`;
}

// ▼ 画像
if (serverData.upload.img.length > 0) {
    // 表示用は PHP 側で作った元ファイル名リスト
    document.getElementById('imgs').value = serverData.post.imgs || '';
    // 読み込み用はタイムスタンプ付きファイル名
    document.getElementById('imgf').value = serverData.upload.img.map(f => `../music/${f}`).join(',');
}

// ▼ プリロードJS
if (serverData.upload.prejs.length > 0) {
    // 表示用（元ファイル名）
    document.getElementById('prejs').value = serverData.post.prejs || '';

    // 読み込み用（タイムスタンプ付き）
    const preList = serverData.upload.prejs.map(f => `../music/${f}`).join(',');
    document.getElementById('prejf').value = preList;
}

// ▼ 時間
document.getElementById('time').value = serverData.upload.time;

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
const dfEvent = evt => { };
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