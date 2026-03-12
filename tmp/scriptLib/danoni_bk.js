'use strict';

//------------------------------------------------

const customLibData = `

|keyCtrl1=Space|
|chara1=space|
|color1=2|
|stepRtn1=onigiri|

|keyCtrl5g=F1,F2,F3,F4,Enter/ShiftRight$D1,D2,D3,D4,Enter/ShiftRight$5_0$5_1$5_2|
|chara5g=5_0$5g_0$5_0$5_1$5_2|
|color5g=5_0$5g_0$5_0$5_1$5_2|
|stepRtn5g=0,45,135,180,giko$5g_0$5_0$5_1$5_2|
|blank5g=57.5|
|shuffle5g=5_0$5g_0$5_0$5_1$5_2|
|scroll5g=5_0$5g_0$5_0$5_1$5_2|
|transKey5g=$$5$5$5|

|keyCtrl5p=X/Z,E/W,D/F/S,T/R,V/C$B/N,Y/U,H/J/K,I/O,M/Comma$5_0$5_1$5_2|
|chara5p=5_2$5p_0$5_0$5_1$5_2|
|color5p=0,1,2,1,0$5p_0$5_0$5_1$5_2|
|stepRtn5p=-45,45,onigiri,135,-135$5p_0$5_0$5_1$5_2|
|blank5p=57.5|
|shuffle5p=0,0,0,0,0$5p_0$5_0$5_1$5_2|
|scroll5p=5_2$5p_0$5_0$5_1$5_2|
|transKey5p=$$5$5$5|

|keyCtrl6=K,O,L,P,Semicolon,Space$Space,K,O,L,P,Semicolon|
|chara6=left,leftdia,down,rightdia,right,space$space,left,leftdia,down,rightdia,right|
|color6=0,1,0,1,0,2$2,0,1,0,1,0|
|stepRtn6=0,45,-90,135,180,onigiri$onigiri,0,45,-90,135,180|
|shuffle6=1,1,1,1,1,0$0,1,1,1,1,1|

|keyCtrl7e=Tab,S,E,D,R,F,N,D6,D7$D7,D8,N,L,P,Semicolon,Ja-@,Ja-Colon,Enter|
|chara7e=oni,left,down,gor,up,right,space,sleft,sright$sright,sleft,space,right,up,gor,down,left,oni|
|color7e=2,0,1,0,1,0,2,3,3$3,3,2,0,1,0,1,0,2|
|stepRtn7e=onigiri,0,45,-90,135,180,onigiri,iyo,giko$giko,iyo,onigiri,0,45,-90,135,180,onigiri|
|blank7e=52.5|
|shuffle7e=2,0,0,0,0,0,1,0,0$0,0,1,0,0,0,0,0,2|
|scroll7e=Cross::1,-,-,-,-,-,-,1,1$Cross::1,1,-,-,-,-,-,-,1|

|keyCtrl8i=A/Z,S/X,D/C,5_1$8_0$8_1$8_2$8_3$8_4$8_5|
|chara8i=8_0$left,leftdia,down,up,rightdia,right,sleft,space$space,left,leftdia,down,up,rightdia,right,sleft$8i_3$8i_3$8i_3$8i_3|
|color8i=1,1,1,2,0,0,0,0$8_0$8_1$8_2$8_3$8_4$8_5|
|stepRtn8i=0,-90,180,5_1/giko,morara,iyo,5_1$8_0$8_1$8_2$8_3$8_4$8_5|
|shuffle8i=0,0,0,2,1,1,1,1$8_0$8_1$8_2$8_3$8_4$8_5|
|keyRetry8i=$Tab$Tab$$$$|
|transKey8i=$8$8$12$12$12$12|

|minWidth9d=650|
|keyCtrl9d=S,D,F,V,B,N,J,K,L|
|chara9d=9B_0|
|color9d=0,1,0,2,2,2,0,1,0|
|stepRtn9d=0,-45,-90,giko,onigiri,iyo,90,135,180|
|shuffle9d=0,0,0,1,1,1,2,2,2|
|scroll9d=9B_0/Cross::1,1,1,-,-,-,1,1,1|
|assist9d=AA::0,0,0,1,1,1,0,0,0|

|keyCtrl9g=F1,F2,F3,F4,F5,F6,F7,F8,Enter/ShiftRight$D1,D2,D3,D4,D5,D6,D7,D8,Enter/ShiftRight|
|chara9g=left,down,up,right,sleft,sdown,sup,sright,space$9g_0|
|color9g=0,0,0,0,1,1,1,1,2$9g_0|
|stepRtn9g=0,45,135,180,0,45,135,180,giko$9g_0|
|blank9g=52.5|
|shuffle9g=0,0,0,0,1,1,1,1,2$9g_0|
|scroll9g=Cross::1,1,-,-,-,-,1,1,1/Split::1,1,1,1,-,-,-,-,-/Alternate::1,-,1,-,1,-,1,-,1$9g_0|

|keyCtrl9j=Tab,7_0,Enter$9A_0$9A_1$9A_2|
|chara9j=9A_0$9A_0$9A_1$9A_2|
|color9j=2,7_0_0,2$9A_0$9A_1$9A_2|
|stepRtn9j=giko,7_0_0,iyo$9A_0$9A_1$9A_2|
|shuffle9j=1,7_0_0,1$9A_0$9A_1$9A_2|
|scroll9j=9A_0/Cross::1,1,1,-,-,-,1,1,1/AA-Split::1,-,-,-,1,-,-,-,1$9A_0$9A_1$9A_2|
|transKey9j=$9A$9A$9B|

|keyCtrl9v=D4,R,F,V,Space,N,J,I,D9$9B_0|
|chara9v=9B_0$9B_0|
|color9v=9B_0$9B_0|
|stepRtn9v=90,120,150,180,onigiri,0,30,60,90$9B_0|
|shuffle9v=9B_0$9B_0|
|scroll9v=---::1,1,-,-,-,-,-,1,1/flat::1,1,1,1,1,1,1,1,1$9B_0|
|transKey9v=$9B|

|keyCtrlhimsiyauz=H,I,M,S,D1,Y,A,U,Z|
|charahimsiyauz=hx,ix,mx,sx,1x,yx,ax,ux,zx|
|stepRtnhimsiyauz=-45,180,-90,135,giko,0,45,-90,-90|
|poshimsiyauz=11.5,6,13,10,0,4,8,5,9|
|colorhimsiyauz=1,3,0,1,2,3,1,3,0|
|divhimsiyauz=7,15|
|shufflehimsiyauz=0,0,0,1,1,0,1,0,1|

|minWidth10=650|
|keyCtrl10=9A_0,Enter|
|chara10=9A_0,sspace|
|color10=9A_0_0,2|
|stepRtn10=9A_0_0,onigiri|
|blank10=52.5|
|shuffle10=9A_0_0,3/9A_0_1,1|
|scroll10=Cross::9A_0,1/Split::9A_0,-/Alternate::9A_0,-/Twist::9A_0,-/Asymmetry::9A_0,1/AA-Split::-,-,-,-,1,-,-,-,-,1|
|keyTitleBack10=Escape|

|minWidth10p=650|
|keyCtrl10p=X/Z,E/W,D/F/S,T/R,V/C,B/N,Y/U,H/J/K,I/O,M/Comma$X/Z,W/Q,D/S/F,R/T,C/V,M/N,U/Y,K/J/L,O/P,Comma/Period$11i_0|
|chara10p=left,down,gor,up,right,sleft,sdown,siyo,sup,sright$10p_0$11i_0|
|color10p=0,1,2,1,0,0,1,2,1,0$10p_0$11i_0|
|stepRtn10p=-45,45,onigiri,135,-135,-45,45,onigiri,135,-135$10p_0$11i_0|
|blank10p=50|
|shuffle10p=0,0,0,0,0,0,0,0,0,0$10p_0$11i_0|
|scroll10p=Cross::1,1,1,-,-,-,-,1,1,1/Split::1,1,1,1,1,-,-,-,-,-/Alternate::1,-,1,-,1,1,-,1,-,1$10p_0$11i_0|
|transKey10p=$$11i|

|minWidth10A=700|
|keyCtrl10A=E/W/R/Q/T/D3/D4/D2/D5,I/U/O/Y/P/D8/D9/D7/D0,Up/Down/Left/Right,7_0$11_0$11L_0$11W_0$12_0|
|chara10A=sleft,sdown,sright,7_0$11_0$11L_0$11W_0$12_0|
|color10A=3,3,3,7_0_0$11_0$11L_0$11W_0$12_0|
|stepRtn10A=giko,c,iyo,7_0_0$11_0$11L_0$11W_0$12_0|
|pos10A=1.5,4.5,8,9,10,11,12,13,14,15$11_0$11L_0$11W_0$12_0|
|div10A=9$11_0$11L_0$11W_0$12_0|
|shuffle10A=2,2,2,7_0_0$11_0$11L_0$11W_0$12_0|
|scroll10A=Flat::1,1,1,-,-,-,-,-,-,-$11_0$11L_0$11W_0$12_0|
|transKey10A=$11$11L$11W$12|
|keyGroup10A=0/1,0/1,0/1,0/2,0/2,0/2,0/2,0/2,0/2,0/2|
|keyGroupOrder10A=1,2|

|minWidth11j=650|
|keyCtrl11j=Tab,9A_0,Enter$11i_0|
|chara11j=gor,9A_0,siyo$11i_0|
|color11j=2,9A_0_0,2$11i_0|
|stepRtn11j=giko,9A_0_0,iyo$11i_0|
|blank11j=50$11i_0|
|shuffle11j=3,9A_0_0,4/1,9A_0_1,1$11i_0|
|scroll11j=Cross::1,9A_0,1/Split::1,9A_0,-/Alternate::1,-,1,-,1,-,1,-,1,-,1/AA-Split::1,-,-,-,-,1,-,-,-,-,1$11i_0|
|transKey11j=$11i|
|keyRetry11j=F12$|
|keyTitleBack11j=Escape$|


|minWidth11f=650|
|keyCtrl11f=S,E,D,R,F,Space,J,I,K,O,L$X,D,C,F,V,Space,N,J,M,K,Comma$E,R,I,O,7_0$11i_0|
|chara11f=7_0,sleft,sdown,sup,sright$11f_0$leftdia,space,sleft,sup,left,down,up,rightdia,right,sdown,sright$11f_0|
|color11f=0,1,0,1,0,2,0,1,0,1,0$11f_0$11_0$11i_0|
|stepRtn11f=0,45,-90,135,180,onigiri,0,45,-90,135,180$11f_0$11_0$11i_0|
|pos11f=$$0,1,4,5,6,7,8,9,10,11,12$|
|div11f=$$6$|
|blank11f=50$11f_0$55$11f_0|
|shuffle11f=0,0,0,0,0,1,2,2,2,2,2$11f_0$11_0$11i_0|
|scroll11f=11i_0$11f_0$Split::1,1,-,-,1,1,1,-,-,-,-$11f_0|
|assist11f=Left::1,1,1,1,1,0,0,0,0,0,0/Right::0,0,0,0,0,0,1,1,1,1,1$11f_0$$11f_0|
|transKey11f=$$11F$11i|

|minWidth11g=650|
|keyCtrl11g=Z,S,X,D/F,C/V,Space,M/N,K/J,Comma,L,Period$S,E,D,R,F,Space,J,I,K,O,L$E,R,I,O,7_0$11i_0|
|chara11g=left,down,up,right,space,tspace,sleft,sdown,sup,sright,sspace$11g_0$down,right,sdown,sright,left,up,space,tspace,sleft,sup,sspace$11g_0|
|color11g=0,1,0,1,0,2,0,1,0,1,0$11g_0$11_0$11i_0|
|stepRtn11g=0,-30,-60,-90,-120,onigiri,60,90,120,150,180$0,-45,-90,-135,180,onigiri,0,45,90,135,180$11_0$11i_0|
|pos11g=$$0,1,4,5,6,7,8,9,10,11,12$|
|div11g=$$6$|
|blank11g=50$11g_0$55$11g_0|
|shuffle11g=0,0,0,0,0,1,2,2,2,2,2$11g_0$11_0$11i_0|
|scroll11g=11i_0$11g_0$Split::1,1,-,-,1,1,1,-,-,-,-$11g_0|
|assist11g=Left::1,1,1,1,1,0,0,0,0,0,0/Right::0,0,0,0,0,0,1,1,1,1,1$11g_0$$11g_0|
|transKey11g=$11f$11F$11i|

|minWidth12i=675|
|keyCtrl12i=F1,F2,F3,F4,F5,F6,F7,F8,F9,F10,F11,F12$Q,W,E,R,T,Y,U,I,O,P,Ja-@,Ja-[|
|chara12i=oni,left,leftdia,down,sleft,sdown,sup,sright,space,up,rightdia,right$12i_0|
|color12i=1,0,1,0,3,3,3,3,0,1,0,1$12i_0|
|stepRtn12i=45,0,-45,-90,giko,onigiri,iyo,c,90,135,180,225$12i_0|
|blank12i=50|
|shuffle12i=0,0,0,0,1,1,1,1,2,2,2,2$12i_0|
|scroll12i=Cross::1,1,1,1,-,-,-,-,1,1,1,1/Split::1,1,1,1,1,1,-,-,-,-,-,-$12i_0|

|minWidth14e=950|
|keyCtrl14e=Tab,X,D,C,F,V,T,Y,U,I,Comma,L,Period,Semicolon,Slash,Enter|
|chara14e=aleft,bleft,adown,bdown,aup,bup,aright,bright,cleft,dleft,cdown,ddown,cup,dup,cright,dright|
|color14e=2,0,1,0,1,0,3,3,3,3,0,1,0,1,0,2|
|stepRtn14e=onigiri,0,45,-90,135,180,giko,onigiri,iyo,c,0,45,-90,135,180,onigiri|
|pos14e=0,1,2,3,4,5,6.5,7.5,8.5,9.5,11,12,13,14,15,16|
|blank14e=50|
|shuffle14e=1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1|
|scroll14e=Cross::1,-,-,-,-,-,1,1,1,1,-,-,-,-,-,1|

|minWidth18=650|
|keyCtrl18=
W,E,D3/D4,R,Space,Left,Down,Up,Right,S,D,F,Z,X,C,J,K,L
W,E,D3/D4,R,Space,Left,Down,Up,Right,S,D,F,V,B,N,J,K,L
W,E,D3/D4,R,Space,U,I,D8/D9,O,S,D,F,Z,X,C,J,K,L
W,E,D3/D4,R,Space,U,I,D8/D9,O,S,D,F,V,B,N,J,K,L|
|chara18=sleft,sdown,sup,sright,space,aleft,adown,aup,aright,left,leftdia,down,gor,oni,iyo,up,rightdia,right$18_0$18_0$18_0|
|color18=3,3,3,3,2,4,4,4,4,0,1,0,2,2,2,0,1,0$18_0$18_0$18_0|
|div18=9$18_0$18_0$18_0|
|stepRtn18=9A_0,0,-45,-90,giko,onigiri,iyo,90,135,180$18_0$18_0$18_0|
|shuffle18=0,0,0,0,1,2,2,2,2,3,3,3,4,4,4,3,3,3$18_0$18_0$18_0|
|scroll18=Flat::1,1,1,1,1,1,1,1,1,-,-,-,-,-,-,-,-,-$18_0$18_0$18_0|

|minWidth20=850|
|keyCtrl20=W,E,D3/D4,R,U,I,D8/D9,O,Left,Down,Up,Right,8_2$20_0|
|chara20=aleft,adown,aup,aright,sleft,sdown,sup,sright,bleft,bdown,bup,bright,oni,left,leftdia,down,space,up,rightdia,right$20_0|
|color20=4,4,4,4,3,3,3,3,4,4,4,4,8_2_0$20_0|
|stepRtn20=0,-90,90,180,0,-90,90,180,0,-90,90,180,8_2_0$20_0|
|pos20=0,1,2,3,5,6,7,8,10,11,12,13,14,15,16,17,18,19,20,21$|
|div20=14,23$12,21|
|blank20=50|
|shuffle20=3,3,3,3,0,0,0,0,4,4,4,4,1,2,2,2,2,2,2,2$20_0|

|minWidth16=700|
|keyCtrl16=D1/D2,T,Space,Y,D0/Minus,S,D,F,V,G,B,H,N,J,K,L|
|chara16=sleft,sdown,space,sup,sright,aleft,adown,aup,gor,aright,oni,left,iyo,down,up,right|
|color16=0,1,2,1,0,3,2,3,4,3,4,3,4,3,2,3|
|stepRtn16=30,150,onigiri,30,150,0,-45,-90,giko,180,onigiri,0,iyo,90,135,180/30,150,onigiri,30,150,0,-45,-90,giko,180,monar,0,iyo,90,135,180/c,135,onigiri,45,morara,0,-45,-90,giko,180,monar,0,iyo,90,135,180|
|pos16=0.5,4,5,6,9.5,11,12,13,14,15,16,17,18,19,20,21|
|div16=11|
|shuffle16=0,0,3,0,0,1,1,1,2,1,2,1,2,1,1,1|

|minWidth33=1250|
|keyCtrl33=
Q,W,D2/D3,E,R,T,D5/D6,Y,U,I,D8/D9,O,P,BracketLeft,Minus/Equal,BracketRight,Z,X,S/D,C,V,B,G/H,N,M,Comma,J/K,Period,Space,Left,Down,Up,Right
Q,W,D2/D3,E,R,T,D5/D6,Y,U,I,D8/D9,O,P,BracketLeft,Minus/Equal,BracketRight,Z,X,S/D,C,V,B,G/H,N,M,Comma,J/K,Period,Left,Down,Up,Right,Space
|
|pos33=
0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,20,21,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37
0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,20,21,22,23,24,25,26,27,28,29,30,31,34,35,36,37,38
|
|div33=19,39$19,39|
|stepRtn33=
0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,onigiri,0,-90,90,180
0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,0,-90,90,180,onigiri
|
|color33=0,0,0,0,1,1,1,1,0,0,0,0,1,1,1,1,3,3,3,3,4,4,4,4,3,3,3,3,2,2,2,2,2$33_0|
|shuffle33=
0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,4,4,4,4,3,4,4,4,4
0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,4,4,4,4,4,4,4,4,3
|
|chara33=0left,0down,0up,0right,1left,1down,1up,1right,2left,2down,2up,2right,3left,3down,3up,3right,4left,4down,4up,4right,5left,5down,5up,5right,6left,6down,6up,6right,space,7left,7down,7up,7right$33_0|

`;
g_presetObj.keysDataLib.push(customLibData);

// flatMode利用のため v35.4.1以降限定で設定
// append系も安全を見てこちらに入れる
const customLibData2 = `

|minWidth33o=900|
|keyCtrl33o=49,D2,D3,D4,Q,W,E,R,A,S,D,F,Z,X,C,V,Space,N,M,Comma,Period,J,K,L,Semicolon,I,O,P,BracketLeft,D9,D0,Minus,Equal|
|pos33o=0,1,2,3,0.5,1.5,2.5,3.5,1,2,3,4,1.5,2.5,3.5,4.5,5.5,6.5,7.5,8.5,9.5,7,8,9,10,7.5,8.5,9.5,10.5,8,9,10,11|
|stepRtn33o=onigiri@:33/45,75,105,135,0,giko,iyo,180,180,c,monar,0,-45,-75,-105,-135,onigiri,-45,-75,-105,-135,180,c,monar,0,0,giko,iyo,180,45,75,105,135|
|color33o=3,3,3,3,4,4,4,4,1,1,1,1,0,0,0,0,2,0,0,0,0,1,1,1,1,4,4,4,4,3,3,3,3/3,4,4,3,4,3,3,4,1,2,2,1,0,1,1,0,2,0,1,1,0,1,2,2,1,4,3,3,4,3,4,4,3|
|shuffle33o=0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,5,5,5,5,6,6,6,6,7,7,7,7,8,8,8,8|
|chara33o=aleft,adown,aup,aright,bleft,bdown,bup,bright,cleft,cdown,cup,cright,dleft,ddown,dup,dright,space,eleft,edown,eup,eright,fleft,fdown,fup,fright,gleft,gdown,gup,gright,hleft,hdown,hup,hright|
|keyRetry=ControlRight|

|scroll33o=D-Flat::1,1,1,1,1,1,1,1,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,1,1,1,1,1,1,1,1|
|keyGroup33o=0/1,0/1,0/1,0/1,0/2,0/2,0/2,0/2,0/3,0/3,0/3,0/3,0/4,0/4,0/4,0/4,0/4,0/4,0/4,0/4,0/4,0/3,0/3,0/3,0/3,0/2,0/2,0/2,0/2,0/1,0/1,0/1,0/1|
|keyGroupOrder33o=1,2,3,4|
|flatMode33o=true|

|append5=true|
|keyCtrl5=F1,F2,F3,F4,Enter/ShiftRight$D1,D2,D3,D4,Enter/ShiftRight$X/Z,E/W,D/F/S,T/R,V/C$B/N,Y/U,H/J/K,I/O,M/Comma|
|chara5=5_0$5_(0)$5_2$5_(2)|
|color5=5_0$5_(0)$0,1,2,1,0$5_(2)|
|stepRtn5=0,45,135,180,giko$5_(0)$-45,45,onigiri,135,-135$5_(2)|
|blank5=57.5|
|shuffle5=5_0$5_(0)$0,0,0,0,0$5_(2)|
|scroll5=5_0$5_(0)$5_2$5_(2)|
|transKey5=5g$5g$5p$5p|

|append8=true|
|keyCtrl8=A/Z,S/X,D/C,5_1|
|chara8=8_0|
|color8=1,1,1,2,0,0,0,0|
|stepRtn8=0,-90,180,5_1/giko,morara,iyo,5_1|
|shuffle8=0,0,0,2,1,1,1,1|
|scroll8=8_0/Cross::1,1,-1,-1,-1,-1,1,1|
|transKey8=8i|

|append9A=true|
|keyCtrl9A=
S,D,F,V,B,N,J,K,L
F1,F2,F3,F4,F5,F6,F7,F8,Enter/ShiftRight
D1,D2,D3,D4,D5,D6,D7,D8,Enter/ShiftRight
Tab,7_0,Enter
D4,R,F,V,Space,N,J,I,D9
H,I,M,S,D1,Y,A,U,Z
|chara9A=
9A_0
left,down,up,right,sleft,sdown,sup,sright,space
left,down,up,right,sleft,sdown,sup,sright,space
9A_0
9A_0
9A_0
|color9A=
0,1,0,2,2,2,0,1,0
0,0,0,0,1,1,1,1,2
0,0,0,0,1,1,1,1,2
2,7_0_0,2
9A_0
1,3,0,1,2,3,1,3,0
|stepRtn9A=
0,-45,-90,giko,onigiri,iyo,90,135,180
0,45,135,180,0,45,135,180,giko
0,45,135,180,0,45,135,180,giko
giko,7_0_0,iyo
90,120,150,180,onigiri,0,30,60,90
-45,180,-90,135,giko,0,45,-90,-90
|shuffle9A=
0,0,0,1,1,1,2,2,2
0,0,0,0,1,1,1,1,2
0,0,0,0,1,1,1,1,2
1,7_0_0,1
9A_0
0,0,0,1,1,0,1,0,1
|scroll9A=
9A_0/Cross::1,1,1,-,-,-,1,1,1
Cross::1,1,-,-,-,-,1,1,1/Split::1,1,1,1,-,-,-,-,-/Alternate::1,-,1,-,1,-,1,-,1
Cross::1,1,-,-,-,-,1,1,1/Split::1,1,1,1,-,-,-,-,-/Alternate::1,-,1,-,1,-,1,-,1
9A_0/Cross::1,1,1,-,-,-,1,1,1/AA-Split::1,-,-,-,1,-,-,-,1
---::1,1,-,-,-,-,-,1,1/flat::1,1,1,1,1,1,1,1,1

|pos9A=$$$$$11.5,6,13,10,0,4,8,5,9|
|assist9A=AA::0,0,0,1,1,1,0,0,0$$$$$|
|div9A=$$$$$7,15|
|blank9A=52.5|
|transKey9A=9d$9g$9g$9j$9v$himsiyauz|

|append9B=true|
|keyCtrl9B=
S,D,F,V,B,N,J,K,L
F1,F2,F3,F4,F5,F6,F7,F8,Enter/ShiftRight
D1,D2,D3,D4,D5,D6,D7,D8,Enter/ShiftRight
Tab,7_0,Enter
D4,R,F,V,Space,N,J,I,D9
H,I,M,S,D1,Y,A,U,Z
|chara9B=
9B_0
left,down,up,right,sleft,sdown,sup,sright,space
left,down,up,right,sleft,sdown,sup,sright,space
9B_0
9B_0
9B_0
|color9B=
0,1,0,2,2,2,0,1,0
0,0,0,0,1,1,1,1,2
0,0,0,0,1,1,1,1,2
2,7_0_0,2
9B_0
1,3,0,1,2,3,1,3,0
|stepRtn9B=
0,-45,-90,giko,onigiri,iyo,90,135,180
0,45,135,180,0,45,135,180,giko
0,45,135,180,0,45,135,180,giko
giko,7_0_0,iyo
90,120,150,180,onigiri,0,30,60,90
-45,180,-90,135,giko,0,45,-90,-90
|shuffle9B=
0,0,0,1,1,1,2,2,2
0,0,0,0,1,1,1,1,2
0,0,0,0,1,1,1,1,2
1,7_0_0,1
9B_0
0,0,0,1,1,0,1,0,1
|scroll9B=
9B_0/Cross::1,1,1,-,-,-,1,1,1
Cross::1,1,-,-,-,-,1,1,1/Split::1,1,1,1,-,-,-,-,-/Alternate::1,-,1,-,1,-,1,-,1
Cross::1,1,-,-,-,-,1,1,1/Split::1,1,1,1,-,-,-,-,-/Alternate::1,-,1,-,1,-,1,-,1
9B_0/Cross::1,1,1,-,-,-,1,1,1/AA-Split::1,-,-,-,1,-,-,-,1
---::1,1,-,-,-,-,-,1,1/flat::1,1,1,1,1,1,1,1,1

|pos9B=$$$$$11.5,6,13,10,0,4,8,5,9|
|assist9B=AA::0,0,0,1,1,1,0,0,0$$$$$|
|div9B=$$$$$7,15|
|blank9B=52.5|
|transKey9B=9d$9g$9g$9j$9v$himsiyauz|

|minWidth12=675|
|append12=true|
|chara12=oni,left,leftdia,down,sleft,sdown,sup,sright,space,up,rightdia,right$12_(0)$12_0$12_0$12_0|
|color12=1,0,1,0,3,3,3,3,0,1,0,1$12_(0)$12_0$12_0$12_0$12_0|
|stepRtn12=45,0,-45,-90,giko,onigiri,iyo,c,90,135,180,225$12_(0)$12_0$12_0$12_0|
|keyCtrl12=
F1,F2,F3,F4,F5,F6,F7,F8,F9,F10,F11,F12
Q,W,E,R,T,Y,U,I,O,P,Ja-@,Ja-[
Left,Down,Up,Right,Space,N,J,M,K,Comma,L,Period
W,E,Up,Right,Space,N,J,M,K,Comma,L,Period
W,E,Up,Right,Space,B,H,N/M,J/K,Comma,L,Period
|shuffle12=0,0,0,0,1,1,1,1,2,2,2,2$12_(0)$12_0$12_0$12_0|
|blank12=50$50$55$55$55$55|
|pos12=$$5,6,7,8,9,10,11,12,13,14,15,16$0,1,7,8,9,10,11,12,13,14,15,16$12_(3)|
|div12=$$9$9$9$9|
|scroll12=Cross::1,1,1,1,-1,-1,-1,-1,1,1,1,1/Split::1,1,1,1,1,1,-1,-1,-1,-1,-1,-1$12_(0)$Flat::1,1,1,1,-1,-1,-1,-1,-1,-1,-1,-1$12_(2)$12_(2)|
|transKey12=12i$12i$12A$12F$12F|

|minWidth14=675|
|append14=true|
|chara14=14_0$14_0|
|color14=4,4,4,3,3,3,2,0,1,0,1,0,1,0$14_(0)|
|stepRtn14=0,-90,180,0,-90,180,onigiri,0,30,60,90,120,150,180$14_(0)|
|keyCtrl14=
W,E/D3/D4,R,Left,Down/Up,Right,Space,N,J,M,K,Comma,L,Period
W,E/D3/D4,R,Left,Down/Up,Right,Space,B,H,N/M,J/K,Comma,L,Period
|
|pos14=0,1,2,6,7,8,9,10,11,12,13,14,15,16$14_(0)|
|div14=9$9|
|scroll14=14_0$14_0|
|transKey14=14F$14F|

`;
if (compareVersions(baseVersion, '35.4.1') >= 0) {
	g_presetObj.keysDataLib.push(customLibData2);
}
