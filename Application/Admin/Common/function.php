<?php
function avatar_exists($member_id){
	$file_name_jpg = './Public/avatar/advtar-' . $member_id . '.jpg';
	$file_name_png = './Public/avatar/advtar-' . $member_id . '.png';
	$file_name_gif = './Public/avatar/advtar-' . $member_id . '.gif';

	//file_exists($file_name);
	if(file_exists($file_name_jpg)){
		return URL_PUB .'avatar/avatar-' . $member_id . '.jpg';
	}elseif (file_exists($file_name_png)){
		return URL_PUB .'avatar/avatar-' . $member_id . '.png';
	}elseif (file_exists($file_name_gif)){
		return URL_PUB .'avatar/avatar-' . $member_id . '.gif';
	}else{
		return URL_PUB .'avatar/avatar-default.jpg';
	}

	
}