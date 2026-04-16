<?php

set_time_limit(90);

ob_start();

// Debug

error_reporting(0);

// error_reporting(E_ALL);

// Time

date_default_timezone_set('America/Sao_Paulo');

// Url

$httprotocol = "https://";

if( !$_SERVER['HTTPS'] && $_SERVER['HTTP_X_FORWARDED_PROTO'] != 'https' ) {
	$fixprotocol = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("Location: ".$fixprotocol);
	exit;
}

$simple_url = "pedeue.com";

$suport_url = $httprotocol."conheca.".$simple_url."/";
$system_url = $httprotocol.$simple_url."/administracao";
$panel_url = $httprotocol.$simple_url."/painel";
$admin_url = $httprotocol.$simple_url."/administracao";
$just_url = $httprotocol.$simple_url;
$app_url = $httprotocol.$simple_url."/app";
$afiliado_url = $httprotocol.$simple_url."/afiliado";

// Comissão Afiliados
$comissao_afiliados = "10";

// Title

$seo_title = "Plw Design";
$seo_description = "Compre sem sair de casa!";
//$titulo_topo = "Velox Imports<strong>.</strong>"; //TITULO DA LOGO PARA USAR TITULO INVES DE IMAGEM TIRAR OS // DO COMEÇO E COLOCAR NO DE BAIXO 
$titulo_topo = '<img src="/_core/_cdn/img/logo.png">'; //US4R LOGO INVES DE TITUL5
$titulo_rodape ="Plw Desgign";
$sub_titulo_rodape ="O CATÁLOGO VIRTUAL DESCOMPLICADO!"; //Endereço ou Slogan
$titulo_rodape_marketplace ="Plw Design, Compre sem sair de casa!"; //Endereço ou Slogan


// Redes/Whatsapp/Email
$whatsapp = "11998608485";
$usrtelefone = "11998608485";
$email ="#";
$youtube ="https://www.youtube.com/channel/UCgyNLdajZqtzsiJxg-IK9sg";
$instagram="https://www.instagram.com/plwdesign/";
$facebook ="https://www.facebook.com/produtosmktplw";

// Db

$db_host = "pedeue_catal251_banco2catalogo";
$db_user = "catal251_admin3";
$db_pass = "Admin@2302";
$db_name = "catal251_banco2catalogo";

// SMTP

$smtp_name = "Plw Design";
$smtp_user = "contato@catalogoplwdesign.com";
$smtp_pass = "(-eFF%.Ci}J8";

// Manunten

$manutencao = false;

if( $manutencao ) {

	include("manutencao.php");
	die;

}

// Includes

include("functions.php");

// Tokens


// Recaptcha
// Gerar em: https://www.google.com/recaptcha/admin/
$recaptcha_sitekey = "6LdoiAgiAAAAAFkD_vNiuI5eYrNdVOUKVuMX6OSI";
$recaptcha_secretkey = "6LdoiAgiAAAAAG5Kw_qDsskTKlnneCFmov1rK4QH";

//External token Utilizado para receber os callbacks do mercado pago pro sistema, pode manter padr
$external_token = "6LfBMLcUAAAAALxKYfylrPMhMMg35IskTG4R7jYw";

// Mercado pago
// Gerar em: https://www.mercadopago.com.br/developers/panel/credentials
$mp_sandbox = false;

if ($mp_sandbox == true) {
	$mp_public_key = "TEST-313b6034-90dd-4507-98b0-471a6b214669";
	$mp_acess_token = "TEST-1951298084039693-082705-95e836ceaf682953862bbf26b739b2fa-50209960";
} else {
	$mp_public_key = "APP_USR-9dd18cec-ddbe-4277-9fc5-1d4ce520c00e";
	$mp_acess_token = "APP_USR-1951298084039693-082705-2daed8fa84f0817886f4c6c047dd4fe9-50209960";
	$mp_client_id = "1951298084039693";
	$mp_client_secret = "q2IvAV5vZufWGHNxVzR6A0B5TW3Meo2y";
}

// Plano padr (id)

$plano_default = "5";

// Root path

$rootpath = $_SERVER["DOCUMENT_ROOT"];

// Images

$image_max_width = 1000;
$image_max_height = 1000;
$gallery_max_files  = 10;

// Global header and footer

$system_header = "";
$system_footer = "";

// Keep Alive

if( $_SESSION['user']['logged'] == "1" && strlen( $_SESSION['user']['keepalive'] ) >= 10 && $_SESSION['user']['keepalive'] != $_COOKIE['keepalive'] ) {
	setcookie( 'keepalive', "kill", time() - 3600 );
	if( strlen( $_SESSION['user']['keepalive'] ) >= 10 ) {
		setcookie( 'keepalive', $_SESSION['user']['keepalive'], (time() + (120 * 24 * 3600)) );
	}
}

$keepalive = $_COOKIE['keepalive'];

if( $_SESSION['user']['logged'] != "1" && strlen( $keepalive ) >= 10 ) {

	make_login($keepalive,"","keepalive","2");

}

?>