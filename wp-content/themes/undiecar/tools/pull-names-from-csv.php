<?php

/* GET SESSION IDEAS VIA JSON FROM http://members.iracing.com/memberstats/member/GetSeriesRaceResults
$bla = '{"m":{"1":"start_time","2":"carclassid","3":"trackid","4":"sessionid","5":"subsessionid","6":"officialsession","7":"sizeoffield","8":"strengthoffield"},"d":[{"1":1529341200000,"2":15,"3":107,"4":95855819,"5":23503089,"6":1,"7":21,"8":1022},{"1":1529341200000,"2":15,"3":107,"4":95855819,"5":23503087,"6":1,"7":21,"8":2901},{"1":1529341200000,"2":15,"3":107,"4":95855819,"5":23503088,"6":1,"7":21,"8":1673},{"1":1529326800000,"2":15,"3":107,"4":95847950,"5":23501376,"6":1,"7":12,"8":981},{"1":1529326800000,"2":15,"3":107,"4":95847950,"5":23501375,"6":1,"7":13,"8":2316},{"1":1529348400000,"2":15,"3":107,"4":95859795,"5":23504062,"6":1,"7":21,"8":1449},{"1":1529348400000,"2":15,"3":107,"4":95859795,"5":23504063,"6":1,"7":20,"8":1043},{"1":1529348400000,"2":15,"3":107,"4":95859795,"5":23504061,"6":1,"7":21,"8":2350},{"1":1529355600000,"2":15,"3":107,"4":95863780,"5":23505000,"6":1,"7":20,"8":950},{"1":1529355600000,"2":15,"3":107,"4":95863780,"5":23504998,"6":1,"7":21,"8":2606},{"1":1529355600000,"2":15,"3":107,"4":95863780,"5":23504999,"6":1,"7":20,"8":1492},{"1":1529362800000,"2":15,"3":107,"4":95867762,"5":23505993,"6":1,"7":21,"8":987},{"1":1529334000000,"2":15,"3":107,"4":95851885,"5":23502224,"6":1,"7":22,"8":1099},{"1":1529334000000,"2":15,"3":107,"4":95851885,"5":23502223,"6":1,"7":23,"8":2564},{"1":1529362800000,"2":15,"3":107,"4":95867762,"5":23505991,"6":1,"7":22,"8":2536},{"1":1529362800000,"2":15,"3":107,"4":95867762,"5":23505992,"6":1,"7":21,"8":1443},{"1":1528772400000,"2":15,"3":107,"4":95559176,"5":23433447,"6":1,"7":18,"8":1495},{"1":1528772400000,"2":15,"3":107,"4":95559176,"5":23433446,"6":1,"7":18,"8":2558},{"1":1528772400000,"2":15,"3":107,"4":95559176,"5":23433448,"6":1,"7":17,"8":1070},{"1":1528779600000,"2":15,"3":107,"4":95562635,"5":23434378,"6":1,"7":12,"8":1170},{"1":1528779600000,"2":15,"3":107,"4":95562635,"5":23434377,"6":1,"7":13,"8":2363},{"1":1528786800000,"2":15,"3":107,"4":95566059,"5":23435206,"6":1,"7":17,"8":1768},{"1":1528794000000,"2":15,"3":107,"4":95569481,"5":23436018,"6":1,"7":17,"8":1403},{"1":1528794000000,"2":15,"3":107,"4":95569481,"5":23436017,"6":1,"7":18,"8":2193},{"1":1528808400000,"2":15,"3":107,"4":95576302,"5":23437673,"6":1,"7":18,"8":1269},{"1":1528808400000,"2":15,"3":107,"4":95576302,"5":23437672,"6":1,"7":19,"8":2393},{"1":1528815600000,"2":15,"3":107,"4":95579717,"5":23438502,"6":1,"7":21,"8":1278},{"1":1528815600000,"2":15,"3":107,"4":95579717,"5":23438501,"6":1,"7":21,"8":2675},{"1":1528880400000,"2":15,"3":107,"4":95610724,"5":23446904,"6":1,"7":12,"8":1309},{"1":1528880400000,"2":15,"3":107,"4":95610724,"5":23446903,"6":1,"7":13,"8":2442},{"1":1528765200000,"2":15,"3":107,"4":95555486,"5":23432422,"6":1,"7":17,"8":2718},{"1":1528765200000,"2":15,"3":107,"4":95555486,"5":23432423,"6":1,"7":17,"8":1204},{"1":1529060400000,"2":15,"3":107,"4":95701011,"5":23468570,"6":1,"7":14,"8":1265},{"1":1529060400000,"2":15,"3":107,"4":95701011,"5":23468569,"6":1,"7":15,"8":2610},{"1":1529017200000,"2":15,"3":107,"4":95677097,"5":23463257,"6":1,"7":21,"8":1050},{"1":1529017200000,"2":15,"3":107,"4":95677097,"5":23463256,"6":1,"7":21,"8":2340},{"1":1528801200000,"2":15,"3":107,"4":95572887,"5":23436852,"6":1,"7":17,"8":1290},{"1":1528801200000,"2":15,"3":107,"4":95572887,"5":23436851,"6":1,"7":17,"8":1765},{"1":1529031600000,"2":15,"3":107,"4":95685141,"5":23465292,"6":1,"7":16,"8":1017},{"1":1529031600000,"2":15,"3":107,"4":95685141,"5":23465291,"6":1,"7":16,"8":1573},{"1":1528801200000,"2":15,"3":107,"4":95572887,"5":23436850,"6":1,"7":18,"8":2891},{"1":1529031600000,"2":15,"3":107,"4":95685141,"5":23465290,"6":1,"7":17,"8":2768},{"1":1528959600000,"2":15,"3":107,"4":95648512,"5":23456765,"6":1,"7":12,"8":1170},{"1":1528959600000,"2":15,"3":107,"4":95648512,"5":23456764,"6":1,"7":13,"8":2068},{"1":1528995600000,"2":15,"3":107,"4":95665267,"5":23460336,"6":1,"7":20,"8":1140},{"1":1528995600000,"2":15,"3":107,"4":95665267,"5":23460335,"6":1,"7":20,"8":1634},{"1":1528995600000,"2":15,"3":107,"4":95665267,"5":23460334,"6":1,"7":21,"8":2658},{"1":1528966800000,"2":15,"3":107,"4":95651917,"5":23457552,"6":1,"7":14,"8":1224},{"1":1528966800000,"2":15,"3":107,"4":95651917,"5":23457551,"6":1,"7":15,"8":2061},{"1":1528974000000,"2":15,"3":107,"4":95655320,"5":23458328,"6":1,"7":19,"8":1811},{"1":1528822800000,"2":15,"3":107,"4":95583130,"5":23439402,"6":1,"7":16,"8":1210},{"1":1528822800000,"2":15,"3":107,"4":95583130,"5":23439401,"6":1,"7":16,"8":1876},{"1":1528822800000,"2":15,"3":107,"4":95583130,"5":23439400,"6":1,"7":17,"8":3213},{"1":1528873200000,"2":15,"3":107,"4":95607332,"5":23446111,"6":1,"7":16,"8":1682},{"1":1528902000000,"2":15,"3":107,"4":95620946,"5":23449322,"6":1,"7":20,"8":2769},{"1":1528902000000,"2":15,"3":107,"4":95620946,"5":23449323,"6":1,"7":20,"8":1306},{"1":1528923600000,"2":15,"3":107,"4":95631223,"5":23452084,"6":1,"7":22,"8":1059},{"1":1528923600000,"2":15,"3":107,"4":95631223,"5":23452082,"6":1,"7":23,"8":3442},{"1":1528923600000,"2":15,"3":107,"4":95631223,"5":23452083,"6":1,"7":22,"8":1725},{"1":1528930800000,"2":15,"3":107,"4":95634660,"5":23453073,"6":1,"7":20,"8":1057},{"1":1528930800000,"2":15,"3":107,"4":95634660,"5":23453071,"6":1,"7":21,"8":3021},{"1":1528909200000,"2":15,"3":107,"4":95624349,"5":23450158,"6":1,"7":16,"8":945},{"1":1528909200000,"2":15,"3":107,"4":95624349,"5":23450156,"6":1,"7":17,"8":2667},{"1":1528909200000,"2":15,"3":107,"4":95624349,"5":23450157,"6":1,"7":16,"8":1569},{"1":1528930800000,"2":15,"3":107,"4":95634660,"5":23453072,"6":1,"7":20,"8":1682},{"1":1528945200000,"2":15,"3":107,"4":95641627,"5":23455084,"6":1,"7":21,"8":1051},{"1":1528945200000,"2":15,"3":107,"4":95641627,"5":23455083,"6":1,"7":22,"8":2171},{"1":1529204400000,"2":15,"3":107,"4":95780512,"5":23486456,"6":1,"7":18,"8":881},{"1":1529204400000,"2":15,"3":107,"4":95780512,"5":23486454,"6":1,"7":19,"8":2679},{"1":1529204400000,"2":15,"3":107,"4":95780512,"5":23486455,"6":1,"7":18,"8":1496},{"1":1529089200000,"2":15,"3":107,"4":95716822,"5":23471813,"6":1,"7":21,"8":1036},{"1":1529089200000,"2":15,"3":107,"4":95716822,"5":23471811,"6":1,"7":22,"8":2609},{"1":1529089200000,"2":15,"3":107,"4":95716822,"5":23471812,"6":1,"7":22,"8":1553},{"1":1529010000000,"2":15,"3":107,"4":95673098,"5":23462309,"6":1,"7":23,"8":1085},{"1":1529010000000,"2":15,"3":107,"4":95673098,"5":23462307,"6":1,"7":24,"8":3075},{"1":1529010000000,"2":15,"3":107,"4":95673098,"5":23462308,"6":1,"7":24,"8":1644},{"1":1529082000000,"2":15,"3":107,"4":95712845,"5":23470894,"6":1,"7":16,"8":1130},{"1":1529082000000,"2":15,"3":107,"4":95712845,"5":23470892,"6":1,"7":17,"8":2321},{"1":1529082000000,"2":15,"3":107,"4":95712845,"5":23470893,"6":1,"7":16,"8":1485},{"1":1529197200000,"2":15,"3":107,"4":95776518,"5":23485545,"6":1,"7":18,"8":1043},{"1":1529168400000,"2":15,"3":107,"4":95760585,"5":23481721,"6":1,"7":16,"8":933},{"1":1529197200000,"2":15,"3":107,"4":95776518,"5":23485544,"6":1,"7":19,"8":2852},{"1":1529168400000,"2":15,"3":107,"4":95760585,"5":23481719,"6":1,"7":17,"8":2560},{"1":1529168400000,"2":15,"3":107,"4":95760585,"5":23481720,"6":1,"7":17,"8":1434},{"1":1528887600000,"2":15,"3":107,"4":95614139,"5":23447694,"6":1,"7":16,"8":1375},{"1":1528887600000,"2":15,"3":107,"4":95614139,"5":23447693,"6":1,"7":17,"8":2645},{"1":1528844400000,"2":15,"3":107,"4":95593457,"5":23442382,"6":1,"7":22,"8":1245},{"1":1528844400000,"2":15,"3":107,"4":95593457,"5":23442381,"6":1,"7":22,"8":2416},{"1":1528916400000,"2":15,"3":107,"4":95627771,"5":23451136,"6":1,"7":22,"8":980},{"1":1528916400000,"2":15,"3":107,"4":95627771,"5":23451134,"6":1,"7":23,"8":2725},{"1":1528916400000,"2":15,"3":107,"4":95627771,"5":23451135,"6":1,"7":22,"8":1617},{"1":1529154000000,"2":15,"3":107,"4":95752654,"5":23479883,"6":1,"7":20,"8":1836},{"1":1529154000000,"2":15,"3":107,"4":95752654,"5":23479884,"6":1,"7":20,"8":1062},{"1":1528851600000,"2":15,"3":107,"4":95596931,"5":23443453,"6":1,"7":20,"8":1077},{"1":1528851600000,"2":15,"3":107,"4":95596931,"5":23443452,"6":1,"7":21,"8":2192},{"1":1528952400000,"2":15,"3":107,"4":95645088,"5":23455975,"6":1,"7":16,"8":2692},{"1":1528952400000,"2":15,"3":107,"4":95645088,"5":23455976,"6":1,"7":15,"8":1150},{"1":1528837200000,"2":15,"3":107,"4":95590013,"5":23441386,"6":1,"7":18,"8":1015},{"1":1528837200000,"2":15,"3":107,"4":95590013,"5":23441385,"6":1,"7":18,"8":1630},{"1":1528837200000,"2":15,"3":107,"4":95590013,"5":23441384,"6":1,"7":19,"8":3300},{"1":1529161200000,"2":15,"3":107,"4":95756621,"5":23480772,"6":1,"7":20,"8":910},{"1":1529161200000,"2":15,"3":107,"4":95756621,"5":23480770,"6":1,"7":21,"8":2657},{"1":1529161200000,"2":15,"3":107,"4":95756621,"5":23480771,"6":1,"7":20,"8":1493},{"1":1529146800000,"2":15,"3":107,"4":95748694,"5":23479024,"6":1,"7":13,"8":1186},{"1":1529146800000,"2":15,"3":107,"4":95748694,"5":23479023,"6":1,"7":13,"8":2415},{"1":1528866000000,"2":15,"3":107,"4":95603917,"5":23445316,"6":1,"7":16,"8":1105},{"1":1528866000000,"2":15,"3":107,"4":95603917,"5":23445315,"6":1,"7":17,"8":2532},{"1":1528894800000,"2":15,"3":107,"4":95617545,"5":23448489,"6":1,"7":14,"8":1399},{"1":1528894800000,"2":15,"3":107,"4":95617545,"5":23448488,"6":1,"7":15,"8":2812},{"1":1529110800000,"2":15,"3":107,"4":95728777,"5":23474663,"6":1,"7":16,"8":919},{"1":1529110800000,"2":15,"3":107,"4":95728777,"5":23474662,"6":1,"7":17,"8":1564},{"1":1529110800000,"2":15,"3":107,"4":95728777,"5":23474661,"6":1,"7":17,"8":3052},{"1":1529190000000,"2":15,"3":107,"4":95772532,"5":23484606,"6":1,"7":20,"8":2701},{"1":1529190000000,"2":15,"3":107,"4":95772532,"5":23484607,"6":1,"7":19,"8":1479},{"1":1529190000000,"2":15,"3":107,"4":95772532,"5":23484608,"6":1,"7":19,"8":952},{"1":1529233200000,"2":15,"3":107,"4":95796359,"5":23489826,"6":1,"7":22,"8":1196},{"1":1529233200000,"2":15,"3":107,"4":95796359,"5":23489825,"6":1,"7":22,"8":2262},{"1":1529139600000,"2":15,"3":107,"4":95744746,"5":23478208,"6":1,"7":17,"8":1174},{"1":1529139600000,"2":15,"3":107,"4":95744746,"5":23478207,"6":1,"7":17,"8":1988},{"1":1529218800000,"2":15,"3":107,"4":95788461,"5":23488157,"6":1,"7":16,"8":1250},{"1":1529218800000,"2":15,"3":107,"4":95788461,"5":23488156,"6":1,"7":16,"8":2659},{"1":1529240400000,"2":15,"3":107,"4":95800312,"5":23490660,"6":1,"7":20,"8":1217},{"1":1529240400000,"2":15,"3":107,"4":95800312,"5":23490659,"6":1,"7":21,"8":2227},{"1":1529118000000,"2":15,"3":107,"4":95732801,"5":23475669,"6":1,"7":17,"8":933},{"1":1529118000000,"2":15,"3":107,"4":95732801,"5":23475667,"6":1,"7":18,"8":2711},{"1":1529118000000,"2":15,"3":107,"4":95732801,"5":23475668,"6":1,"7":18,"8":1367},{"1":1529175600000,"2":15,"3":107,"4":95764569,"5":23482676,"6":1,"7":19,"8":1029},{"1":1529175600000,"2":15,"3":107,"4":95764569,"5":23482674,"6":1,"7":20,"8":2637},{"1":1529175600000,"2":15,"3":107,"4":95764569,"5":23482675,"6":1,"7":20,"8":1486},{"1":1529211600000,"2":15,"3":107,"4":95784494,"5":23487351,"6":1,"7":13,"8":1034},{"1":1529211600000,"2":15,"3":107,"4":95784494,"5":23487350,"6":1,"7":14,"8":2158},{"1":1528938000000,"2":15,"3":107,"4":95638116,"5":23454116,"6":1,"7":21,"8":1672},{"1":1528938000000,"2":15,"3":107,"4":95638116,"5":23454115,"6":1,"7":22,"8":3089},{"1":1529002800000,"2":15,"3":107,"4":95668981,"5":23461332,"6":1,"7":22,"8":1013},{"1":1529002800000,"2":15,"3":107,"4":95668981,"5":23461330,"6":1,"7":23,"8":2985},{"1":1529002800000,"2":15,"3":107,"4":95668981,"5":23461331,"6":1,"7":23,"8":1698},{"1":1528938000000,"2":15,"3":107,"4":95638116,"5":23454117,"6":1,"7":21,"8":1105},{"1":1529024400000,"2":15,"3":107,"4":95681101,"5":23464303,"6":1,"7":20,"8":1093},{"1":1529038800000,"2":15,"3":107,"4":95689136,"5":23466174,"6":1,"7":17,"8":1785},{"1":1529024400000,"2":15,"3":107,"4":95681101,"5":23464302,"6":1,"7":20,"8":2423},{"1":1528830000000,"2":15,"3":107,"4":95586566,"5":23440373,"6":1,"7":18,"8":968},{"1":1528830000000,"2":15,"3":107,"4":95586566,"5":23440371,"6":1,"7":18,"8":2181},{"1":1528830000000,"2":15,"3":107,"4":95586566,"5":23440372,"6":1,"7":18,"8":1560},{"1":1528830000000,"2":15,"3":107,"4":95586566,"5":23440370,"6":1,"7":19,"8":3826},{"1":1529046000000,"2":15,"3":107,"4":95693103,"5":23466976,"6":1,"7":16,"8":1517},{"1":1529096400000,"2":15,"3":107,"4":95720795,"5":23472750,"6":1,"7":21,"8":1046},{"1":1529096400000,"2":15,"3":107,"4":95720795,"5":23472748,"6":1,"7":22,"8":3027},{"1":1529096400000,"2":15,"3":107,"4":95720795,"5":23472749,"6":1,"7":21,"8":1614},{"1":1529053200000,"2":15,"3":107,"4":95697052,"5":23467769,"6":1,"7":16,"8":1265},{"1":1529132400000,"2":15,"3":107,"4":95740791,"5":23477401,"6":1,"7":19,"8":902},{"1":1529132400000,"2":15,"3":107,"4":95740791,"5":23477400,"6":1,"7":19,"8":1980},{"1":1529053200000,"2":15,"3":107,"4":95697052,"5":23467768,"6":1,"7":17,"8":2759},{"1":1528858800000,"2":15,"3":107,"4":95600461,"5":23444445,"6":1,"7":24,"8":2296},{"1":1528858800000,"2":15,"3":107,"4":95600461,"5":23444446,"6":1,"7":23,"8":1199},{"1":1529125200000,"2":15,"3":107,"4":95736804,"5":23476564,"6":1,"7":18,"8":1322},{"1":1529103600000,"2":15,"3":107,"4":95724784,"5":23473719,"6":1,"7":21,"8":1106},{"1":1529103600000,"2":15,"3":107,"4":95724784,"5":23473718,"6":1,"7":22,"8":2849},{"1":1529125200000,"2":15,"3":107,"4":95736804,"5":23476563,"6":1,"7":18,"8":2701},{"1":1529269200000,"2":15,"3":107,"4":95816205,"5":23494465,"6":1,"7":21,"8":986},{"1":1529269200000,"2":15,"3":107,"4":95816205,"5":23494464,"6":1,"7":21,"8":1657},{"1":1529269200000,"2":15,"3":107,"4":95816205,"5":23494463,"6":1,"7":21,"8":2820},{"1":1529247600000,"2":15,"3":107,"4":95804265,"5":23491566,"6":1,"7":21,"8":2368},{"1":1529247600000,"2":15,"3":107,"4":95804265,"5":23491567,"6":1,"7":21,"8":1539},{"1":1529247600000,"2":15,"3":107,"4":95804265,"5":23491568,"6":1,"7":21,"8":1025},{"1":1529254800000,"2":15,"3":107,"4":95808232,"5":23492492,"6":1,"7":23,"8":1006},{"1":1529254800000,"2":15,"3":107,"4":95808232,"5":23492490,"6":1,"7":24,"8":2576},{"1":1529254800000,"2":15,"3":107,"4":95808232,"5":23492491,"6":1,"7":24,"8":1568},{"1":1529305200000,"2":15,"3":107,"4":95836136,"5":23498993,"6":1,"7":12,"8":1819},{"1":1529283600000,"2":15,"3":107,"4":95824193,"5":23496402,"6":1,"7":22,"8":1006},{"1":1529283600000,"2":15,"3":107,"4":95824193,"5":23496401,"6":1,"7":23,"8":2025},{"1":1529312400000,"2":15,"3":107,"4":95840076,"5":23499786,"6":1,"7":21,"8":1780},{"1":1529319600000,"2":15,"3":107,"4":95844014,"5":23500597,"6":1,"7":23,"8":1698},{"1":1529290800000,"2":15,"3":107,"4":95828223,"5":23497370,"6":1,"7":21,"8":1040},{"1":1529290800000,"2":15,"3":107,"4":95828223,"5":23497369,"6":1,"7":21,"8":2031},{"1":1529298000000,"2":15,"3":107,"4":95832199,"5":23498226,"6":1,"7":21,"8":1671},{"1":1529262000000,"2":15,"3":107,"4":95812209,"5":23493470,"6":1,"7":22,"8":2960},{"1":1529262000000,"2":15,"3":107,"4":95812209,"5":23493471,"6":1,"7":22,"8":1918},{"1":1529262000000,"2":15,"3":107,"4":95812209,"5":23493472,"6":1,"7":22,"8":1458},{"1":1529262000000,"2":15,"3":107,"4":95812209,"5":23493473,"6":1,"7":21,"8":954},{"1":1529226000000,"2":15,"3":107,"4":95792406,"5":23488974,"6":1,"7":22,"8":1144},{"1":1529226000000,"2":15,"3":107,"4":95792406,"5":23488973,"6":1,"7":23,"8":2147},{"1":1529276400000,"2":15,"3":107,"4":95820190,"5":23495434,"6":1,"7":17,"8":1016},{"1":1529276400000,"2":15,"3":107,"4":95820190,"5":23495432,"6":1,"7":18,"8":2577},{"1":1529276400000,"2":15,"3":107,"4":95820190,"5":23495433,"6":1,"7":18,"8":1436},{"1":1529182800000,"2":15,"3":107,"4":95768546,"5":23483669,"6":1,"7":23,"8":1026},{"1":1529182800000,"2":15,"3":107,"4":95768546,"5":23483667,"6":1,"7":24,"8":2914},{"1":1529182800000,"2":15,"3":107,"4":95768546,"5":23483668,"6":1,"7":23,"8":1641}]}';

$bla = json_decode( $bla );
$bla = $bla->d;
$count = 0;
foreach ( $bla as $key => $b ) {

	// get time
	$time = $b->{1} / 1000;
	$hours = date( 'H', $time );

	if (
		'19' == $hours
		||
		'20' == $hours
		||
		'21' == $hours
		||
		'22' == $hours
		||
		'23' == $hours
	) {
		echo $b->{5} . ",";
		$count++;
	}

}
echo "\n\nTotal: $count";
die;
*/


if ( ! isset( $_GET['pull_names'] ) ) {
	return;
}


//define( 'ALLOW_ROOKIES', true );
define( 'ALLOW_B_LICENSES', true );
//define( 'ALLOW_C_LICENSES', true );
//define( 'ALLOW_D_LICENSES', true );

//define( 'MIN_OVAL_IRATING', 3000 );
//define( 'MIN_ROAD_IRATING', 2000 );
define( 'MIN_OVAL_IRATING', 1200 );
define( 'MIN_ROAD_IRATING', 100 );



/*****
 ****** IMPORTANT:
 ****** use GET_USERS() to CHECK FOR EXISTING USERS - needs new code for this
 ******/



require( 'contacted.php' );

$contacts = '';
foreach ( array_merge(
	explode( ',', $contacts ),\
	explode( ',', $contacted )
) as $x => $driver_name ) {
	$personal_contacts[$driver_name] = true;
}



$events = array(
	'2019-s2-c-fixed-trucks' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
/*
	'roval-s1-2019' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'porsche-gt3-cup-2018-s4' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'radicals-s4-2018' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'nascar-b' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'nascar-a' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'daytona-trucks' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'global-mazda-s4' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'radicals-s3' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'global-mazda-s3-w5' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'mazda-mx5-cup-s2-w6-10-2018' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'advanced-mx5-s2-2018' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'ruf-s2-wks-1-10-2018' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'skippies-2018-s1-tuesday-nights' => array( // might be worth inviting B-license holders too
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'fixed-indy-iowa-s1' => array( // might be worth inviting B-license holders too
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indy-fixed-s1-w1-w9' => array( // might be worth inviting B-license holders too
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'dallara-dash-9' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'dallara-dash-6' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r2' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r3' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r4' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-s1-2018-r1' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indy-road-s1-2018' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'pro-mazda-s4-2017' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indycar-s4-2017' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indycar2' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-2' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indycar-spa' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'skip-barber' => array(
		'incident_ratio_1' => 1,
		'incident_ratio_2' => 1,
		'incident_ratio_3' => 1,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'indycar' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'laguna-seca' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 75,
		'time_2'           => 76,
		'time_3'           => 77,
	),
	'phoenix' => array(
		'incident_ratio_1' => 0.6,
		'incident_ratio_2' => 0.8,
		'incident_ratio_3' => 0.9,
		'time_1'           => 20.65, // Times largely irrelevant as qual set to 0
		'time_2'           => 20.7, // Times largely irrelevant as qual set to 0
		'time_3'           => 20.75, // Times largely irrelevant as qual set to 0
	),
	'sebring-promazda' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'bathurst-promazda' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'spa-promazda' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	'formula-renault-last-2' => array(
		'incident_ratio_1' => 0.1,
		'incident_ratio_2' => 0.2,
		'incident_ratio_3' => 0.3,
		'time_1'           => 999,
		'time_2'           => 999,
		'time_3'           => 999,
	),
	*/
);

foreach ( $events as $event => $vars ) {

	$incident_ratio_1 = $vars['incident_ratio_1'];
	$incident_ratio_2 = $vars['incident_ratio_2'];
	$incident_ratio_3 = $vars['incident_ratio_3'];
	$time_1 = $vars['time_1'];
	$time_2 = $vars['time_2'];
	$time_3 = $vars['time_3'];

	$directory = get_template_directory() . '/tools/results/' . $event . '/';


	// Get iRacing stats
	$dir = wp_upload_dir();
	$stats = file_get_contents( $dir['basedir'] . '/iracing-members.json' );
	$stats = json_decode( $stats, true );

	$csv_files = scandir( $directory, 1 );

	foreach ( $csv_files as $key => $csv_file_name ) {

		if ( '.csv' !== substr( $csv_file_name, -4 ) ) {
			continue;
		}

		// Get CSV data
		$csv_file_path = $directory . $csv_file_name;
		$csv_file_content = file_get_contents( $csv_file_path );
		$csv_file_content = str_replace( '"', '', $csv_file_content );
		$csv_file_rows = explode( "\n", $csv_file_content );

		// Stripping description out
		unset( $csv_file_rows[0] );
		unset( $csv_file_rows[1] );
		unset( $csv_file_rows[2] );
		unset( $csv_file_rows[3] );

		foreach ( $csv_file_rows as $key => $row ) {
			$cells = explode( ',', $row );

			// Get name
			if ( ! isset( $cells[7] ) ) {
				continue;
			}
			$driver_name = $cells[7];
			$driver_name = utf8_encode( $driver_name );

			// this removes 'Bill Eberhardt' who for some reason won't remove based on his name. appears to be an oddball character encoding problem with his name.
			if ( '215126' === $cells[5] ) {
				continue;
			}

			if (
				'Iberia' === $cells[24]
				||
				'Brazil' === $cells[24]
			) {
				continue;
			}

			// Ignore personal contacts
			if ( isset( $personal_contacts[$driver_name] ) ) {
				$drivers[$driver_name] = 'personal';
				continue;
			}

			// If no iRating, then set it to 0
			if ( ! isset( $stats[$driver_name]['oval_irating'] ) ) {
				$stats[$driver_name]['oval_irating'] = 0;
			}
			if ( ! isset( $stats[$driver_name]['road_irating'] ) ) {
				$stats[$driver_name]['road_irating'] = 0;
			}

			// Bail out if they don't meet either oval or road iRating minimums
			if (
				$stats[$driver_name]['oval_irating'] > MIN_OVAL_IRATING
				||
				$stats[$driver_name]['road_irating'] > MIN_ROAD_IRATING
			) {
				// non problemo with iRating
			} else {
				continue;
			}

			if ( isset( $stats[$driver_name]['road_license'] ) ) {

				// Allow A B C drivers
				if (
					'A' === $stats[$driver_name]['road_license']
					||
					(
						'B' === $stats[$driver_name]['road_license']
						&& defined( 'ALLOW_B_LICENSES' )
					)
					||
					(
						'C' === $stats[$driver_name]['road_license']
						&&
						defined( 'ALLOW_C_LICENSES' )
					)
				) {
					$drivers[$driver_name] = $event;
					continue;
				}

			}


		}

	}

}


$listed_drivers = $drivers;

/**
 * Remove existing members.
 */
$users = get_users();
foreach ( $users as $key => $data ) {
	$driver_name = $data->display_name;

	if ( isset( $listed_drivers[$driver_name] ) ) {
		unset( $listed_drivers[$driver_name] );
	}

}

// Strip out personal ones
foreach ( $listed_drivers as $driver_name => $track ) {

	if ( 'personal' === $track ) {
		unset( $listed_drivers[$driver_name] );
	}
}

/**
 * Finally, output names.
 */
$listed_drivers2 = 0;
if ( 'csv' === $_GET['pull_names'] ) {

	foreach ( $listed_drivers as $driver_name => $track ) {
		echo $driver_name . ',';
		$listed_drivers2++;
	}

	echo "\n\ncount: " . $listed_drivers2;


} else if ( 'details' === $_GET['pull_names'] ) {

	foreach ( $listed_drivers as $driver_name => $track ) {

		echo $driver_name . ":\n";

		if ( isset( $stats[$driver_name]['road_irating'] ) ) {
			echo "\troad iRating: " . $stats[$driver_name]['road_irating'] . "\n";
		}
		if ( isset( $stats[$driver_name]['oval_irating'] ) ) {
			echo "\toval iRating: " . $stats[$driver_name]['oval_irating'] . "\n";
		}
		if ( isset( $stats[$driver_name]['road_license'] ) ) {
			echo "\troad license: " . $stats[$driver_name]['road_license'] . "\n";
		}
		if ( isset( $stats[$driver_name]['oval_license'] ) ) {
			echo "\toval license: " . $stats[$driver_name]['oval_license'] . "\n";
		}

		$listed_drivers2++;
	}

	echo "\n\ncount: " . $listed_drivers2;

} else {

	print_r( $listed_drivers );
	echo "\n\ncount: " . count( $listed_drivers );

}


die;
