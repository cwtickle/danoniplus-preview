'use strict';
/**
 * Dancing☆Onigiri カスタム用jsファイル
 * [for Punching◇Panels 36panels version]
 * 
 * Original Source by tickle
 * Created: 2022/01/28
 * Revised: 2023/08/06
 * Source Version: Ver 1.4.0+
 * 
 * https://github.com/cwtickle/punching-panels
 */

// 位置の設定、ゲーム名の変更
const jstyleX = [
  100, 200, 300, 400, 500,  600, 700, 800, 900, 1000,
    150, 250, 350, 450,       650, 750, 850, 950,
    150, 250, 350, 450,       650, 750, 850, 950,
  100, 200, 300, 400, 500,  600, 700, 800, 900, 1000
];
const jstyleY = [
  110, 110, 110, 110, 110,  110, 110, 110, 110, 110,
    170, 170, 170, 170,       170, 170, 170, 170,
    230, 230, 230, 230,       230, 230, 230, 230,
  290, 290, 290, 290, 290,  290, 290, 290, 290, 290
];

g_lblNameObj.dancing = `PUNCHING`;
g_lblNameObj.star = `◇`;
g_lblNameObj.onigiri = `PANELS`;
g_lblNameObj[`u_key`] = `panel`;
g_lblNameObj[`u_k-`] = `p-`;
g_lblNameObj.Reverse = `Dynamic`;
g_lblNameObj[`u_Reverse`] = `Dynamic`;
g_lang_msgObj.Ja.reverse = `パネルの移動パターンを変更します。`;
g_lang_msgObj.En.reverse = `Change the panel movement pattern.`;
g_rootObj.arrowEffectUse = `false,ON`;

// カスタムキー定義
g_keyObj.keyName36p = `36`;
g_keyObj.chara36p_0 = [
  `aa`, `ab`, `ac`, `ad`, `ae`,  `af`, `ag`, `ah`, `ai`, `aj`,
    `ba`, `bb`, `bc`, `bd`,         `bf`, `bg`, `bh`, `bi`,
    `ca`, `cb`, `cc`, `cd`,         `cf`, `cg`, `ch`, `ci`,
  `da`, `db`, `dc`, `dd`, `de`,   `df`, `dg`, `dh`, `di`, `dj`
];
g_keyObj.chara36p_1 = g_keyObj.chara36p_0.concat()
g_keyObj.chara36p_2 = g_keyObj.chara36p_0.concat()

g_keyObj.color36p_0_0 = [
  0, 1, 2, 3, 4,  0, 1, 2, 3, 4,
    0, 1, 3, 4,     0, 1, 3, 4,
    0, 1, 3, 4,     0, 1, 3, 4,
  0, 1, 2, 3, 4,  0, 1, 2, 3, 4
];
g_keyObj.color36p_1_0 = g_keyObj.color36p_0_0.concat()
g_keyObj.color36p_0 = g_keyObj.color36p_0_0.concat()
g_keyObj.color36p_1 = g_keyObj.color36p_0_0.concat()

g_keyObj.shuffle36p_0_0 = [
  0, 0, 0, 0, 0,  1, 1, 1, 1, 1,
    0, 0, 0, 0,     1, 1, 1, 1,
    0, 0, 0, 0,     1, 1, 1, 1,
  0, 0, 0, 0, 0,  1, 1, 1, 1, 1
];
g_keyObj.shuffle36p_1_0 = g_keyObj.shuffle36p_0_0.concat()
g_keyObj.shuffle36p_0 = g_keyObj.shuffle36p_0_0.concat()
g_keyObj.shuffle36p_1 = g_keyObj.shuffle36p_0_0.concat()

g_keyObj.stepRtn36p_0_0 = 'c'.repeat(36).split('');
g_keyObj.stepRtn36p_1_0 = g_keyObj.stepRtn36p_0_0.concat()
g_keyObj.stepRtn36p_0 = g_keyObj.stepRtn36p_0_0.concat()
g_keyObj.stepRtn36p_1 = g_keyObj.stepRtn36p_0_0.concat()

g_keyObj.pos36p_0 = new Array(36).fill(0).map((val, i) => i)
g_keyObj.pos36p_1 = g_keyObj.pos36p_0.concat()

g_keyObj.keyCtrl36p_0 = [
  [50, 0], [51, 0], [52, 0], [53, 0], [54, 0],  [ 55, 0], [ 56, 0], [ 57, 0], [ 48, 0], [189, 0],
      [87, 0], [69, 0], [82, 0], [84, 0],           [ 85, 0], [ 73, 0], [ 79, 0], [ 80, 0],
      [83, 0], [68, 0], [70, 0], [71, 0],           [ 74, 0], [ 75, 0], [ 76, 0], [187, 0],
  [90, 0], [88, 0], [67, 0], [86, 0], [66, 0],  [ 78, 0], [ 77, 0], [188, 0], [190, 0], [191, 0],
];
g_keyObj.keyCtrl36p_1 = [
  [49, 0], [50, 0], [51, 0], [52, 0], [53, 0],  [ 55, 0], [ 56, 0], [ 57, 0], [ 48, 0], [189, 0],
    [81, 0], [87, 0], [69, 0], [82, 0],           [ 85, 0], [ 73, 0], [ 79, 0], [ 80, 0],
    [65, 0], [83, 0], [68, 0], [70, 0],           [ 74, 0], [ 75, 0], [ 76, 0], [187, 0],
  [16, 0], [90, 0], [88, 0], [67, 0], [86, 0],  [ 78, 0], [ 77, 0], [188, 0], [190, 0], [191, 0],
];

g_keyObj.div36p_0 = 36;
g_keyObj.div36p_1 = 36;
g_keyObj.minWidth36p = 1200;

g_rootObj.imgType = `panels,svg,true,0`;
g_rootObj.arrowJdgY = -160;

// デフォルト配列のコピー (g_keyObj.aaa_X から g_keyObj.aaa_Xd を作成)
const keyCtrlNameP = Object.keys(g_keyObj).filter(val => val.startsWith(`keyCtrl36p`));
keyCtrlNameP.forEach(property => g_keyObj[`${property}d`] = copyArray2d(g_keyObj[property]));

[`color36p`, `shuffle36p`].forEach(type => {
  const tmpName = Object.keys(g_keyObj).filter(val => val.startsWith(type) && val.endsWith(`_0`));
  tmpName.forEach(property => g_keyObj[`${property.slice(0, -2)}`] = g_keyObj[property].concat());
});

// 矢印モーション初期定義
g_rootObj.arrowMotion_data = `
0,0,j11_org,j11
0,1,j12_org,j12
0,2,j13_org,j13
0,3,j14_org,j14
0,4,j15_org,j15
0,5,j16_org,j16
0,6,j17_org,j17
0,7,j18_org,j18
0,8,j19_org,j19
0,9,j1a_org,j1a
0,10,j21_org,j21
0,11,j22_org,j22
0,12,j23_org,j23
0,13,j24_org,j24
0,14,j26_org,j26
0,15,j27_org,j27
0,16,j28_org,j28
0,17,j29_org,j29
0,18,j31_org,j31
0,19,j32_org,j32
0,1020,j33_org,j33
0,1021,j34_org,j34
0,1022,j36_org,j36
0,1023,j37_org,j37
0,1024,j38_org,j38
0,1025,j39_org,j39
0,1026,j41_org,j41
0,1027,j42_org,j42
0,1028,j43_org,j43
0,1029,j44_org,j44
0,1030,j45_org,j45
0,1031,j46_org,j46
0,1032,j47_org,j47
0,1033,j48_org,j48
0,1034,j49_org,j49
0,1035,j4a_org,j4a
`;

// ステップゾーンの位置変更 (ノーツはCSS側で制御)
function pstyleMainInit() {
  if ([`36p`].includes(g_keyObj.currentKey)) {
    for (let i = 0; i < 36; i++) {
      if (document.getElementById(`stepRoot${i}`)) {
        document.getElementById(`stepRoot${i}`).style.left = `${jstyleX[i]}px`;
        document.getElementById(`stepRoot${i}`).style.top = `${jstyleY[i]}px`;
      }
    }
  }
}
g_customJsObj.main.push(pstyleMainInit);

//------------------------------------------------

const customLibData = `
|keyName36p=36|
|chara36p=aa,ab,ac,ad,ae,af,ag,ah,ai,aj,ba,bb,bc,bd,bf,bg,bh,bi,ca,cb,cc,cd,cf,cg,ch,ci,da,db,dc,dd,de,df,dg,dh,di,dj$aa,ab,ac,ad,ae,af,ag,ah,ai,aj,ba,bb,bc,bd,bf,bg,bh,bi,ca,cb,cc,cd,cf,cg,ch,ci,da,db,dc,dd,de,df,dg,dh,di,dj|
|color36p=0,1,2,3,4,0,1,2,3,4,0,1,3,4,0,1,3,4,0,1,3,4,0,1,3,4,0,1,2,3,4,0,1,2,3,4$0,1,2,3,4,0,1,2,3,4,0,1,3,4,0,1,3,4,0,1,3,4,0,1,3,4,0,1,2,3,4,0,1,2,3,4|
|shuffle36p=0,0,0,0,0,1,1,1,1,1,0,0,0,0,1,1,1,1,0,0,0,0,1,1,1,1,0,0,0,0,0,1,1,1,1,1$0,0,0,0,0,1,1,1,1,1,0,0,0,0,1,1,1,1,0,0,0,0,1,1,1,1,0,0,0,0,0,1,1,1,1,1|
|stepRtn36p=c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c$c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c,c|
|pos36p=0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35$0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35|
|keyCtrl36p=50/0,51/0,52/0,53/0,54/0,55/0,56/0,57/0,48/0,189/0,87/0,69/0,82/0,84/0,85/0,73/0,79/0,80/0,83/0,68/0,70/0,71/0,74/0,75/0,76/0,187/0,90/0,88/0,67/0,86/0,66/0,78/0,77/0,188/0,190/0,191/0$49/0,50/0,51/0,52/0,53/0,55/0,56/0,57/0,48/0,189/0,81/0,87/0,69/0,82/0,85/0,73/0,79/0,80/0,65/0,83/0,68/0,70/0,74/0,75/0,76/0,187/0,16/0,90/0,88/0,67/0,86/0,78/0,77/0,188/0,190/0,191/0|
|div36p=36$36|
|minWidth36p=1200|
`;
g_presetObj.keysDataLib.push(customLibData);

// ライセンス原文、以下は削除しないでください
/*-----------------------------------------------------------*/
/*
MIT License

Copyright (c) 2022 tickle

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
/*-----------------------------------------------------------*/