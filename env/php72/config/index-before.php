<?php
/**
 * Stop redirect loops for nginx-proxy over HTTPS
 * @see https://stackoverflow.com/questions/54401652/struggling-to-integrate-nginx-proxy-letsencrypt-companion-with-my-docker-compo/54452259#54452259 
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	$_SERVER['HTTPS'] = 'on';
}
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO_NEW']) && $_SERVER['HTTP_X_FORWARDED_PROTO_NEW'] == 'https') {
	$_SERVER['HTTPS'] = 'on';
}