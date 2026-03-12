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
		};
		return calcList;
	};

	for (let j = 0; j < g_headerObj.keyLabels.length; j++) {
		const keyCtrlPtn = `${g_headerObj.keyLabels[j]}_0`;
		g_detailObj.toolDifNew[j] = calcLevelNew(scoreConvert(g_rootObj, j, 0, ``, keyCtrlPtn, true));
	}

}
g_customJsObj.preTitle.push(makeToolDif);

// 難易度変更時のカスタマイズ（譜面選択オンマウスへ表示）
const updateToolDifNew = () => {
	lnkDifficulty.title = `Level ) ALL: ${g_detailObj.toolDif[g_stateObj.scoreId].tool} / Chords: ${(g_detailObj.toolDif[g_stateObj.scoreId].douji).toFixed(2)} / Jack: ${(g_detailObj.toolDif[g_stateObj.scoreId].tate).toFixed(2)}\n`;
	lnkDifficulty.title += `LevelN) ALL: ${g_detailObj.toolDifNew[g_stateObj.scoreId].scoreAll} / VOL: ${g_detailObj.toolDifNew[g_stateObj.scoreId].scoreVol} / FRZ: ${g_detailObj.toolDifNew[g_stateObj.scoreId].scoreFrz} / APM: ${g_detailObj.toolDifNew[g_stateObj.scoreId].apm}`;
	console.log(g_detailObj.toolDifNew[g_stateObj.scoreId]);
}
g_customJsObj.difficulty.push(updateToolDifNew);

