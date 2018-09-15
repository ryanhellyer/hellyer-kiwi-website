<?php

$season_id = '2130';

$start = 0;
while ( $start < 4500 ) {

	$end = $start + 25;

	$url = 'http://members.iracing.com/memberstats/member/GetSeasonStandings?seasonid=' . $season_id . '&carclassid=-1&division=-1&raceweek=-1&start=' . $start . '&end=' . $end;

	$start = $start + 25;

	echo '<a href="' . $url . '">' . $start . ' | ' . $end . '</a>';
	echo '<br />';
}

