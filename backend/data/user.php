<?php 
	$user=json_decode(file_get_contents('php://input'));  //get user from 
	if($user->mail=='admin@filma4ok.ru' && $user->pass=='123456') 
		session_start();
		$_SESSION['uid']=uniqid('ang_');
		print $_SESSION['uid'];
?>