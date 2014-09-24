<?php
/*
  Template helper functions
*/

//this function sorts two people by last name, except for some manually ordered people
function last_name_sort($a, $b) {
	$name_priority = get_option('people_priority_people', array()); //put names you want at the top of the teams here
	$first = $a->post_title;
	$second = $b->post_title;
	$first_index = array_search($first, $name_priority);
	$second_index = array_search($second, $name_priority);
	if ($first_index) {
		if ($second_index) {
			return strcmp($first_index, $second_index);
		}
		else {
			return -1;
		}
	}
	elseif ($second_index) {
         return 1;
    }
	$first = explode(' ', $first);
	$second = explode(' ', $second);
	return strcmp($first[sizeof($first) - 1], $second[sizeof($second) - 1]);
}

//this function groups people by team.  The teams are in the order they come up except for when manually ordered
function group_by_team($people) {
	$priority_team = get_option('people_priority_team');	//this is the name of a team you want to float to the top
	$team_groups = array($priority_team => array());
	$team_no_team = array();
	foreach ($people as $person) {
		$team = get_the_terms($person->ID, 'teams');
		$assigned_team = null;
		if (!$team) {
			array_push($team_no_team, $person);
		}
		else {
			$teams = array_values($team);
			foreach ($teams as $team) {
				$teamname = $team->name;
				if ($teamname == $priority_team) {
					$assigned_team = $teamname;
				}
			}
			if (empty($assigned_team)) {
				$assigned_team = $teamname;
			}
			if (!array_key_exists($assigned_team, $team_groups)) {
				$team_groups[$assigned_team] = array();
			}
			array_push($team_groups[$assigned_team], $person);
		}
	}
	$team_groups[''] = $team_no_team;
	return $team_groups;
}
?>
