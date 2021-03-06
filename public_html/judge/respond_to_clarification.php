<?php
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/respond_to_clarification.php
#
	include_once("lib/config.inc");
	include_once("lib/judge.inc");
	include_once("lib/session.inc");

	$clarification_id = $_POST['clarification_id'];
	$response = $_POST['response'];
	$broadcast = $_POST['broadcast'];
	$problem = $_POST['problem'];

	if($clarification_id != 0) {
		$sql  = "UPDATE CLARIFICATION_REQUESTS ";
		$sql .= "SET RESPONSE='".mysqli_real_escape_string($link, $response)."', ";
		$sql .= "    REPLY_TS='".time()."', ";
		$sql .= "    BROADCAST='$broadcast' ";
		$sql .= "WHERE CLARIFICATION_ID='$clarification_id' ";
	}
	else {
		$sql = "INSERT INTO CLARIFICATION_REQUESTS ";
		$sql .= "(TEAM_ID, PROBLEM_ID, SUBMIT_TS, QUESTION, REPLY_TS, RESPONSE, BROADCAST)";
		$sql .= "VALUES ('0', '$problem', '".time()."', 'Clarification initiated by judge', '".time();
		$sql .= "', '".mysqli_real_escape_string($link, $response)."', '$broadcast');";
	}
	$result = mysqli_query($link, $sql);
	if(!$result){
		echo mysqli_error($link);
	}
	header("location: clarifications.php");
	exit(0);
?>
