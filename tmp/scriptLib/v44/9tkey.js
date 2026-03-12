'use strict';
/**
 * 9tkey用カスタムJS
 * ステップゾーンの調整を行っています。
 * ノートの動きはarrowMotion_dataによるCSSアニメーションで実現(パンパネと同様)。
 */

// Reverseの表記をSpreadにする
g_lblNameObj.Reverse = 'Spread';
g_lblNameObj['u_Reverse'] = 'Spread';

/**
 * メイン画面(初期表示) [Scene: Main / Banana]
 */
g_customJsObj.main.push(() => {
  if (g_keyObj.currentKey == '9t') {
    // ステップゾーンの中心座標を設定
    document.documentElement.style.setProperty('--9t-center-x', (g_sWidth / 2) + 'px')
    document.documentElement.style.setProperty('--9t-center-y', (g_sHeight / 2) + 'px')

    for (let i = 0; i < 9; i++) {
      // ステップゾーンに9tkey用のcssクラスを適用
      document.getElementById(`stepRoot${i}`).classList.add('step9t')

      // リバース時はステップゾーンの矢印を大きくする
      if (g_stateObj.reverse == "ON") {
        document.getElementById(`stepRoot${i}`).style.transform = 'scale(2)'
      }
    }
  }
})

//------------------------------------------------

g_rootObj.motionUse = false;
g_rootObj.appearanceUse = false;
g_rootObj.maxSpeed = 20;

// 矢印モーション初期定義
g_rootObj.arrowMotion_data = `
0,20,arrow9t,arrow9t-rev
0,22,arrow9t,arrow9t-rev
`;

const customLibData = `

|chara9t=left,down,up,right,space,sleft,sdown,sup,sright$9t_0|
|color9t=0,0,0,0,2,0,0,0,0$9t_0|
|shuffle9t=0,0,0,0,1,0,0,0,0$9t_0|
|stepRtn9t=45,90,135,0,onigiri,180,-45,-90,-135$9t_0|
|div9t=9$9|
|pos9t=9A_0$9A_0|
|keyCtrl9t=103/0,104/0,105/0,100/0,101/0,102/0,97/0,98/0,99/0$55/0,56/0,57/0,85/0,73/0,79/0,74/0,75/0,76/0|
|scale9t=1$1|

`;
g_presetObj.keysDataLib.push(customLibData);

