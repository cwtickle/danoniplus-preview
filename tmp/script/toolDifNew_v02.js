'use strict';

// 難易度計算
const makeToolDif = () => {

    g_detailObj.toolDifNew = [];

    const calcLevelNew = _scoreObj => {

        // 譜面データの分解処理 (ここからget_frameinfoの前までは既存処理と同じ)
        const frzStartData = [];
        const frzEndData = [];

        _scoreObj.frzData.forEach((frzs, j) => {
            if (frzs.length > 1) {
                for (let k = 0; k < frzs.length; k += 2) {
                    _scoreObj.arrowData[j].push(frzs[k]);
                    frzStartData.push(frzs[k]);
                    frzEndData.push(frzs[k + 1]);
                }
            }
            _scoreObj.arrowData[j] = _scoreObj.arrowData[j].sort((a, b) => a - b)
                .filter((x, i, self) => self.indexOf(x) === i && !isNaN(parseFloat(x)));
        })

        frzStartData.sort((a, b) => a - b);
        frzEndData.sort((a, b) => a - b);

        let allScorebook = [];
        _scoreObj.arrowData.forEach(data => allScorebook = allScorebook.concat(data));

        allScorebook.sort((a, b) => a - b);
        allScorebook.unshift(allScorebook[0] - 100);
        allScorebook.push(allScorebook.at(-1) + 100);
        const allCnt = allScorebook.length;

        frzEndData.push(allScorebook.at(-1));
        let freezenum = 0; // フリーズアロー数

        //---- get_frameinfo
        // 既存ツールではフリーズアロー位置を検索しながら同時押し補正をしているが、
        // ここではフリーズアロー位置の検索と、各ノーツの位置関係のみを格納
        let framePrev = -1;
        const frameInfo = [];

        for (let i = 1; i < allCnt - 1; i++) {
            // フリーズ始点の検索
            while (frzStartData[0] < allScorebook[i]) {
                // 現フレームに存在するフリーズ数を1増やす
                frzStartData.shift();
                freezenum++;
            }

            // フリーズ終点の検索
            while (frzEndData[0] < allScorebook[i] + 3) {
                // 現フレームに存在するフリーズ数を1減らす
                frzEndData.shift();
                freezenum--;
            }

            // isFrz: ノーツがフリーズアローかどうか判断
            const isFrz = allScorebook[i] === frzStartData[0] && allScorebook[i] > framePrev;

            // inFrz: ノーツがフリーズアロー押下中かどうか判断
            const inFrz = freezenum > 0;
            frameInfo.push({ frame: allScorebook[i], isFrz: isFrz, inFrz: inFrz });

            framePrev = allScorebook[i];
        }

        //---- get_diffscore
        if (frameInfo.length === 0) {
            return {
                cnt: 0,
                apm: 0,
                scoreAll: 0,
                scoreVol: 0,
                scoreFrz: 0,
                scoreAll2: 0,
                currentScoreFrz: 0,
            };
        }
        let first = frameInfo[0].frame;
        let last = frameInfo[frameInfo.length - 1].frame;
        let idxNext = 0, idxPrev = 0;
        let scoreMax = 0, scoreFrz = 0;

        const frameTable = frameInfo.map(x => x.frame);
        frameTable.push(last + g_fps * 6);

        frameInfo.forEach((x, j) => {
            // 全てのノートに対して、前後3秒のノート数を計算（⇒計算値）
            while (frameTable[idxNext] <= x.frame + g_fps * 3) {
                idxNext++;
            }
            while (frameTable[idxPrev] < x.frame - g_fps * 3) {
                idxPrev++;
            }

            const scoreNote = idxNext - idxPrev;
            x.scoreNote = scoreNote;
            x.scoreNote2 = scoreNote - 1;

            // フリーズアローの補正: フリーズアロー中のノート⇒2, フリーズアロー本体⇒1
            if (x.inFrz) {
                scoreFrz += scoreNote * 2;
            } else if (x.isFrz) {
                scoreFrz += scoreNote;
            }

            // 前後3秒のノート数の最密値を算出（VOL算出で使用）
            scoreMax = Math.max(scoreNote, scoreMax);
        });

        const count = frameInfo.length;

        // 各ノートの計算値の平均を算出
        const scoreAll = sumData(frameInfo.filter(x => x.scoreNote !== undefined)
            .map(x => x.scoreNote)) / count;
        const scoreList = frameInfo.filter(x => x.scoreNote2 !== undefined)
            .map(x => x.scoreNote2);
        scoreFrz /= count;

        const toDecimal2 = num => (Math.round(num * 100) / 100).toFixed(2);
        const calcList = {
            cnt: count,
            apm: toDecimal2(count * 3600 / (last - first)),

            // ALL: 各ノートの計算値の平均を2で除算した値
            scoreAll: toDecimal2(scoreAll / 2),
            // VOL: 最密値 / ALL - 1
            scoreVol: toDecimal2(scoreMax / scoreAll - 1),
            // FRZ: フリーズアロー補正値 / ALL
            scoreFrz: toDecimal2(scoreFrz / scoreAll),

            scoreAll2: toDecimal2(calc_diffscore(scoreList)),

            scoreAll_100: toDecimal2(calc_diffscore(scoreList, 100)),
            scoreAll_95: toDecimal2(calc_diffscore(scoreList, 95)),
            scoreAll_90: toDecimal2(calc_diffscore(scoreList, 90)),
            scoreAll_85: toDecimal2(calc_diffscore(scoreList, 85)),
            scoreAll_80: toDecimal2(calc_diffscore(scoreList, 80)),
            scoreAll_75: toDecimal2(calc_diffscore(scoreList, 75)),
            scoreAll_70: toDecimal2(calc_diffscore(scoreList, 70)),
            scoreAll_65: toDecimal2(calc_diffscore(scoreList, 65)),
            scoreAll_60: toDecimal2(calc_diffscore(scoreList, 60)),
            scoreAll_55: toDecimal2(calc_diffscore(scoreList, 55)),
            scoreAll_50: toDecimal2(calc_diffscore(scoreList, 50)),
            scoreAll_45: toDecimal2(calc_diffscore(scoreList, 45)),
            scoreAll_40: toDecimal2(calc_diffscore(scoreList, 40)),
            scoreAll_35: toDecimal2(calc_diffscore(scoreList, 35)),
            scoreAll_30: toDecimal2(calc_diffscore(scoreList, 30)),
            scoreAll_25: toDecimal2(calc_diffscore(scoreList, 25)),
            scoreAll_20: toDecimal2(calc_diffscore(scoreList, 20)),
        };
        return calcList;
    };

    for (let j = 0; j < g_headerObj.keyLabels.length; j++) {
        const keyCtrlPtn = `${g_headerObj.keyLabels[j]}_0`;
        g_detailObj.toolDifNew[j] = calcLevelNew(scoreConvert(g_rootObj, j, 0, ``, keyCtrlPtn, true));
    }

}

const calc_diffscore = (_listScore, _percent = 50) => {
    let diffTotal = 0;
    const revList = _listScore.sort((a, b) => b - a);
    const listScoreCalc = revList.slice(0, Math.floor(revList.length * _percent / 100 + 1));

    listScoreCalc.forEach(score => {
        diffTotal += calc_scorenote(score);
    });
    return diffTotal / listScoreCalc.length;
};

const calc_scorenote = (_note) => {
    let calc;
    if (_note < 10) {
        calc = 0 + (_note - 0) * (2 - 0) / 10;
    } else if (_note < 20) {
        calc = 2 + (_note - 10) * (6 - 2) / 10;
    } else if (_note < 40) {
        calc = 6 + (_note - 20) * (18 - 6) / 20;
    } else if (_note < 80) {
        calc = 18 + (_note - 40) * (54 - 18) / 40;
    } else if (_note < 160) {
        calc = 54 + (_note - 80) * (162 - 54) / 80;
    } else {
        calc = 162 + (_note - 160) * (486 - 162) / 160;
    }

    /**
         #APM      old v01 v02
        #-100  ..   5   1   2
        #-200  ..  10   4   6
        #-400  ..  20  16  18
        #-800  ..  40  64  54
        #-1600 ..  80 256 162
        #1600- ..  --1024 486

        if score < 10:
            calc =   0 + (score -  0) * (2 - 0) / 10
        elif score < 20: 
            calc =   2 + (score - 10) * (6 - 2) / 10
        elif score < 40: 
            calc =   6 + (score - 20) * (18 - 6) / 20
        elif score < 80: 
            calc =  18 + (score - 40) * (54 - 18) / 40
        elif score < 160: 
            calc =  54 + (score - 80) * (162 - 54) / 80
        else: 
            calc = 162 + (score - 160) * (486 - 162) / 160
     */
    return calc;
};
g_customJsObj.preTitle.push(makeToolDif);

// 難易度変更時のカスタマイズ（譜面選択オンマウスへ表示）
const updateToolDifNew = () => {
    const sId = g_detailObj.toolDifNew[g_stateObj.scoreId];
    lnkDifficulty.title = `Level ) ALL: ${g_detailObj.toolDif[g_stateObj.scoreId].tool} / Chords: ${(g_detailObj.toolDif[g_stateObj.scoreId].douji).toFixed(2)} / Jack: ${(g_detailObj.toolDif[g_stateObj.scoreId].tate).toFixed(2)}\n`;
    lnkDifficulty.title += `LevelN) ALL: ${g_detailObj.toolDifNew[g_stateObj.scoreId].scoreAll} / VOL: ${g_detailObj.toolDifNew[g_stateObj.scoreId].scoreVol} / FRZ: ${g_detailObj.toolDifNew[g_stateObj.scoreId].scoreFrz} / APM: ${g_detailObj.toolDifNew[g_stateObj.scoreId].apm}\n`;
    lnkDifficulty.title += `LevelN v02) : ${g_detailObj.toolDifNew[g_stateObj.scoreId].scoreAll2}`
        + `(100%: ${sId.scoreAll_100} | 90%: ${sId.scoreAll_90} | 80%: ${sId.scoreAll_80} | 70%: ${sId.scoreAll_70} | 60%: ${sId.scoreAll_60} | 50%: ${sId.scoreAll_50} | 40%: ${sId.scoreAll_40} | 30%: ${sId.scoreAll_30})`;
    console.log(g_detailObj.toolDifNew[g_stateObj.scoreId]);
}
g_customJsObj.difficulty.push(updateToolDifNew);

