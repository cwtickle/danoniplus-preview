/**
 * index.php (ローカル版) と index2.php (CDN/jsdelivr版) で共有するメイン処理。
 *
 * 呼び出し側は先に serverData (各PHPが出力する <script> ブロック) を
 * 定義した上で、このファイル読込後に以下のように呼び出す。
 *
 *   initDanoniPreview({
 *       baseAction: './',                 // フォームの送信先 (index2.php では './index2.php')
 *       supportsOldVersions: true,         // v19.4.0 より前のバージョンをローカルに保持しているか
 *       templateFile: 'template.html',     // ダウンロード時のデフォルトテンプレート
 *       noSoundPath: 'nosound.mp3',        // 楽曲未指定時の音源パス
 *   });
 *
 * config の各項目が、index.php / index2.php 間で唯一異なっていた挙動に対応します。
 * それ以外のロジックはバージョン番号による分岐（compareVersions）で
 * 両サイトに対して自然に正しい結果になるよう、共通コードとして統合しています。
 */

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

const initDanoniPreview = (config) => {

    for (const [key, value] of Object.entries(serverData.post)) {
        const el = document.getElementById(key);
        if (el) el.value = value;
    }

    // 譜面データ及び追加設定の初期化
    const url = new URL(window.location.href);
    const params = url.searchParams;
    const urlDomain = url.origin;
    const musicData_g = `|musicTitle=musicTitle,artistName,${urlDomain}|musicUrl=${config.noSoundPath}|`;

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
        document.getElementById('htmlf').value = serverData.upload.html;
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

    // バージョンに応じた各種リンク (Release, UpdateInfo, Changelog) の設定
    const applyVersionLinks = (optionElement) => {
        const versionText = optionElement.text.slice(1);
        const majorVersion = versionText.split(`.`)[0];
        const versionTag = optionElement.text.split('(').join('-').split(' ').join('-').split('-')[0];
        document.getElementById('versionLink').href = `https://github.com/cwtickle/danoniplus/releases/tag/${versionTag}`;
        document.getElementById('updateInfo').href = `https://github.com/cwtickle/danoniplus/wiki/UpdateInfo#-v${majorVersion}-changelog`;
        document.getElementById('changelog').href = (latestMajor === majorVersion) ?
            `https://github.com/cwtickle/danoniplus/wiki/Changelog-latest` :
            `https://github.com/cwtickle/danoniplus/wiki/Changelog-v${majorVersion}`;
    };

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

            if (vElements[j].value !== ``) {
                applyVersionLinks(vElements[j]);
            }
        }
    }
    // 該当するバージョンが見つからない場合 (CDN側で value_g が一覧に無い場合など) は先頭を初期選択
    if (baseVersion === ``) {
        vElements[0].selected = true;
        versionj = 0;
        baseVersionUrl = vElements[0].value;
        baseVersion = vElements[0].text.slice(1);
        document.getElementById(`cver`).innerHTML = `v${baseVersion}`;

        applyVersionLinks(vElements[0]);
    }

    if (versionj === 0) {
        document.getElementById(`new`).style.color = `#999999`;
        document.getElementById(`newh`).style.color = `#999999`;
    }
    v.style.backgroundColor = v.options[v.selectedIndex].style.backgroundColor;

    // ImgType（ノートスキン）のデフォルト設定
    // ゲームモード別のキー数設定
    const gameMode = document.getElementById('g');
    const CLASSIC_IMG_TYPE = `$classic,png$classic-thin,png$note,svg,false,10$fish,svg`;

    // gameMode別の設定テーブル (v23.1.0 & v31.3.1以降にのみ適用)
    const gameModeConfig = {
        pstyle: {
            imgType: `panels,svg,true,0`,
            applyDifKey: `18p`,
            editorLink: `https://suzme.github.io/punpane-editor/`,
            resetDifData: true,
            readOnlyDif: true,
        },
        pstyle_dp: {
            imgType: `panels,svg,true,0`,
            applyDifKey: `36p`,
            editorLink: `https://suzme.github.io/punpane-editor/?key=36p`,
            resetDifData: true,
            readOnlyDif: true,
        },
        kstyle: {
            // 既存のdifDataがあればそのまま維持する (force置換はしない)
            defaultDifKey: `27k`,
            editorLink: `https://suzme.github.io/kirizma-converter/`,
            editorLinkLabel: `Converter`,
        },
        '9tkey': {
            imgType: CLASSIC_IMG_TYPE,
            applyDifKey: `9t`,
            editorLink: `https://suzme.github.io/punpane-editor/?key=9t`,
            resetDifData: true,
            readOnlyDif: true,
        },
    };

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

            // difDataが未設定なら既定値を、既に設定済みならキー部分だけ置き換える
            // (pstyle / pstyle_dp / 9tkey で共通のパターン。kstyleは既存データがあれば
            //  そのまま維持する仕様のため、defaultDifKeyという別扱いにしている)
            const applyDifDataKey = _key => {
                if (document.getElementById('dos').value.indexOf(`|difData=`) < 0) {
                    document.getElementById('dos').value += `|difData=${_key}|`;
                } else {
                    document.getElementById('dos').value += replaceDifs(_key);
                }
            };

            const cfg = gameModeConfig[gameMode.value];
            if (cfg) {
                if (cfg.imgType) {
                    document.getElementById('dos').value += `|imgType=${cfg.imgType}|`;
                }
                if (cfg.applyDifKey) {
                    applyDifDataKey(cfg.applyDifKey);
                } else if (cfg.defaultDifKey && document.getElementById('dos').value.indexOf(`|difData=`) < 0) {
                    document.getElementById('dos').value += `|difData=${cfg.defaultDifKey}|`;
                }

                document.getElementById('editorLink').href = cfg.editorLink;
                if (cfg.editorLinkLabel) {
                    document.getElementById('editorLink').innerHTML = cfg.editorLinkLabel;
                }
                if (cfg.resetDifData) {
                    difData_g = ``;
                }
                if (cfg.readOnlyDif) {
                    document.getElementById('k').readOnly = true;
                }
            } else {
                // Dancing☆Onigiri (gameMode未選択)
                document.getElementById('editorLink').style.visibility = `hidden`;
                document.getElementById('dos').value += `|imgType=${CLASSIC_IMG_TYPE}|`;
            }
        } else {
            document.getElementById('dos').value += `|imgType=${CLASSIC_IMG_TYPE}|`;
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

    // 楽曲名情報の設定 (musicUrlについては指定の有無によらず一旦上書きし、既存のデータは使わない)
    if (dosData.indexOf('|musicTitle=') !== -1) {
        document.getElementById('dos').value += `|musicUrl=${config.noSoundPath}|`;
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

    // v45.0.0以降、CDNから参照するゲームモード別の追加ライブラリ設定
    // (kstyle, pstyle, pstyle_dp 以外は従来通りローカルの scriptLib を参照する)
    const cdnLibConfig = {
        kstyle: { repo: `cwtickle/kirizma-cw@v3`, file: `kstyle` },
        pstyle: { repo: `cwtickle/punching-panels@v2`, file: `pstyle` },
        pstyle_dp: { repo: `cwtickle/punching-panels@v2`, file: `pstyle` },
    };
    const resolveCdnLibUrl = (_type) => {
        const cfg = cdnLibConfig[gameMode.value];
        return cfg ? `https://cdn.jsdelivr.net/gh/${cfg.repo}/${_type}/${cfg.file}.${_type}` : null;
    };

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
            const cdnUrl = resolveCdnLibUrl(`js`);
            if (cdnUrl) {
                arrayCustomJs.push(cdnUrl);
                document.getElementById(`srcjs`).href = cdnUrl;
            } else {
                applyCustomJs(gameMode.value);
            }
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
            const cdnUrl = resolveCdnLibUrl(`css`);
            if (cdnUrl) {
                arrayCustomCss.push(cdnUrl);
                document.getElementById(`srccss`).href = cdnUrl;
            } else {
                applyCustomCss(gameMode.value);
            }
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
        const htmlFile = `${document.getElementById('htmlf').value}` || config.templateFile;
        const response = await fetch(htmlFile);
        if (response.ok) {
            const text = await response.text();
            const patternI = /\|imgType=[^\|]+\|/g;
            const repText = document.getElementById('dos').value.replaceAll(`/tmp/${document.getElementById('time').value}_`, 'tmp/').replace(patternI, '');
            let textAfter = text.replace(`<<DOS_DATA>>`, repText);

            const matches = textAfter.match(/\|musicTitle=(.*?)\|/);
            const musicTitles = (matches && matches.length >= 2 ? matches[1] : ``).split(`,`);
            textAfter = textAfter.replace(`<<MUSIC_TITLE>>`, escapeHtml(musicTitles[0] || `Preview`));
            textAfter = textAfter.replace(`<<ARTIST_NAME>>`, escapeHtml(musicTitles[1] || `---`));

            if (config.supportsOldVersions) {
                // バージョンに応じて参照するファイルを変更
                // v32以前はデフォルトCSSが用意されていないため、v33で置き換える
                if (latestMajor === baseVersion.split(`.`)[0]) {
                    textAfter = textAfter.replace(`<<VERSION>>`, ``);
                } else if (compareVersions(baseVersion, '33.0.0') >= 0) {
                    textAfter = textAfter.replace(`<<VERSION>>`, `@${baseVersion.split(`.`)[0]}`);
                } else {
                    textAfter = textAfter.replace(`<<VERSION>>`, `@33`);
                }
            } else {
                textAfter = textAfter.replace(`<<VERSION>>`, baseVersion);
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
    const dfEvent = evt => { };
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
                    document.getElementById('formV').action = `${config.baseAction}?` + document.getElementById(txt).value;
                } else {
                    document.getElementById('formV').action = config.baseAction;
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
                const width_g = serverData.post.w || `800px`;
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
                const height_g = serverData.post.h || `500px`;
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

    // 旧バージョン (v19.4.0未満) の場合のみ専用の遷移先URLを組み立てる
    const computeRedirectUrl = (versionText, versionValue) => {
        if (config.supportsOldVersions && compareVersions(versionText, '19.4.0') < 0) {
            return versionValue.slice(1, -17) + `preview/`;
        }
        return config.baseAction;
    };

    // バージョン変更時の処理
    const getVersion = obj => {
        const idx = obj.selectedIndex;
        const text = obj.options[idx].text;
        const value = obj.options[idx].value;
        const versionTxt = (value === `` || idx === -1) ? `10000.0.0` : text.slice(1);

        const redirectUrl = computeRedirectUrl(versionTxt, value);
        document.getElementById('formV').action = redirectUrl + queryParams;
        document.getElementById('formV').submit();
    };

    // 横幅変更時の処理
    const getWidth = obj => {
        document.getElementById('formV').action = config.baseAction + queryParams;
        document.getElementById('formV').submit();
    };

    // 次のバージョンへ遷移
    const jumpNext = () => {
        versionj++;
        document.getElementById(`v`).value = vElements[versionj].value;

        const redirectUrl = computeRedirectUrl(vElements[versionj].text.slice(1), vElements[versionj].value);
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

        document.getElementById('formV').action = config.baseAction + queryParams;
        document.getElementById('formV').submit();
    };

    // グローバルから呼べるようにする (onclick="jumpNext();" 等のインラインハンドラ用)
    window.getVersion = getVersion;
    window.getWidth = getWidth;
    window.jumpNext = jumpNext;
    window.jumpPrev = jumpPrev;
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