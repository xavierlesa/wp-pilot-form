<?php
/**
 * @package Pilot Form
 * @version 1.0
 */

/*
Plugin Name: Pilot form
Plugin URI: http://
Description: Plugin para integrar Pilot CRM forms. 
Author: Pilot Solutions
Version: 1.0
Author URI: http://
*/

require_once('autoload.php');

define("CONST_APP_KEY", "E3EF1626-F456-42B0-9FD0-BCDEC6B55F39");
define("CONST_RECAPTCHA_SITEKEY", '6LemwAcUAAAAAHwA5wSls2uO1cyTyPaUGwkokevh');
define("CONST_RECAPTCHA_SECRET", '6LemwAcUAAAAAL9hnUFB0c106hMb6gnsnr51QBQA');



/**
 * Register style sheet.
 */
function pilot_form_styles() {
	try {
		wp_register_style( 'pilot-form', plugins_url( 'wp-pilot-form/css/plt_style.css' ) );
		wp_enqueue_style( 'pilot-form' );
	} catch (Exception $e) {
		echo "pilot_form_styles()->:".$e->getMessage();
		die();	
	}
}
//SCRIPTS
function pilot_form_scripts(){
	try {
    	//wp_register_script('pilot-form', plugins_url('wp-pilot-form/js/app.wpajaxsend.js'));
	    wp_enqueue_script('pilot-form', plugins_url('wp-pilot-form/js/app.wpajaxsend.js'), array('jquery'));
	    wp_enqueue_script('pilot-form-action', plugins_url('wp-pilot-form/js/app.wpajaxsend_action.js'), array(), null, true);
	} catch (Exception $e) {	
		echo "pilot_form_scripts()->:".$e->getMessage();
		die();		
	}
}

// [plt-form appkey="1"]

function pilot_contact_form_tag_func( $atts ) {
    
	try {
		$html = '';
		
		$a = shortcode_atts( array(
			'appkey' => CONST_APP_KEY,
            'origincode' => 'default_origincode',
            'sitekey' => CONST_RECAPTCHA_SITEKEY
		), $atts );

        $r = rand() * 123;

        $html = ''
        .'<form method="post" class="wpcf7-form" id="wpajaxsend-' . $r . '" action="" data-wpajaxsend>'
        .'    <div style="display: none;">'
        .'        <input type="hidden" name="_plt_appkey" value="' . $a['appkey'] . '">'
        .'        <input type="hidden" name="_plt_origincode" value="' . $a['origincode'] . '">'
        .'        <input type="hidden" name="_plt_contact_type_id" value="1">'
        .'        <input type="hidden" name="_plt_business_type_id" value="1">'
        .'        <input type="hidden" name="_plt_is_post_call" value="1">'
        .'        <input type="hidden" name="_plt_form_type" value="plt_form_gl">'
        .'    </div>'
        .'    <h6>Queremos Asesorarte</h6>'
        .'    <h2>Consultanos</h2>'
        .'    <ul>'
        .'        <li>'
        .'            <label>Nombre y Apellido'
        .'                <span class="wpcf7-form-control-wrap nombre_apellido">'
        .'                    <input type="text" name="_plt_firstname" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false">'
        .'                </span>'
        .'            </label>'
        .'        </li>'
        .'        <li>'
        .'            <label>Teléfono'
        .'                <span class="wpcf7-form-control-wrap telefono">'
        .'                    <input type="text" name="_plt_phone" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false">'
        .'                </span>'
        .'            </label>'
        .'        </li>'
        .'        <li>'
        .'            <label>Email'
        .'                <span class="wpcf7-form-control-wrap email">'
        .'                    <input type="text" name="_plt_email" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false">'
        .'                </span>'
        .'            </label>'
        .'        </li>'
        .'        <li>'
        .'            <label>Comentarios'
        .'                <span class="wpcf7-form-control-wrap comentario">'
        .'                    <textarea name="_plt_notes" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"></textarea>'
        .'                </span><br>'
        .'            </label>'
        .'        </li>'
        .'        <li>'
        .'            <div class="g-recaptcha" data-sitekey="'.$a['sitekey'].'"></div>'
        .'        </li>'
        .'        <li>'
        .'          <input type="submit" value="Consultar" class="wpcf7-form-control wpcf7-submit">'
        .'        </li>'
        .'    </ul>'
        .'</form>'
        .'<script>var dynaForm = jQuery("form#wpajaxsend-' . $r . '");</script>';

	} catch (Exception $e) {
		echo "pilot_contact_form_gl_tag_func()->:".$e->getMessage();
		die();
	}	
	
	return $html;
}

function plt_control_init() {
	try {
		
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			if ( isset( $_POST['_plt_is_post_call'] ) ) {
				if (checkRecaptcha()){
					pilot_do_post();
				}else {
				    die("Please re-enter your reCAPTCHA 1.");
				}
		
			}
		}
		
	} catch (Exception $e) {
		
		echo "plt_control_init()->:".$e->getMessage();
		die();
		
	}	
}


function checkRecaptcha(){

    $secret=CONST_RECAPTCHA_SECRET; 
    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
   
    if ($resp->isSuccess()) {
        return true;
    }

    return false;

}

function pilot_do_post() {
	
	try {
		$PARAMETRO_REQUERIDO = true; 
		$PARAMETRO_NO_REQUERIDO = false; 

		// recupera variables del form 
		$data = array (
			"debug" 				=> "0",
			"appkey" 				=> request("_plt_appkey"),
			"pilot_suborigin_id" 	=> request("_plt_origincode"),
			"pilot_contact_type_id" => request("_plt_contact_type_id"),
			"pilot_business_type_id"=> request("_plt_business_type_id"),
			"pilot_form_type"		=> request("_plt_form_type"),
			"pilot_firstname" 		=> request("_plt_firstname"),
			"pilot_lastname" 		=> request("_plt_lastname",$PARAMETRO_NO_REQUERIDO, ""),
			"pilot_phone" 			=> request("_plt_phone",$PARAMETRO_NO_REQUERIDO, ""),
			"pilot_email" 			=> request("_plt_email",$PARAMETRO_NO_REQUERIDO, ""),
			"pilot_notes" 			=> request("_plt_notes",$PARAMETRO_NO_REQUERIDO, ""),
			"motivo_consulta" 		=> request("_plt_motivo_consulta",$PARAMETRO_NO_REQUERIDO, "sin motivo de consulta"),
			"cuando_operacion" 		=> request("_plt_cuando_operacion",$PARAMETRO_NO_REQUERIDO, "sin plazo de confirmación"),
			"tipo_operacion" 		=> request("_plt_tipo_operacion",$PARAMETRO_NO_REQUERIDO, "sin tipo de operación"),
			"tipo_departamento" 	=> request("_plt_tipo_departamento",$PARAMETRO_NO_REQUERIDO, "sin tipo de departamento"),
			"modelo_version" 		=> request("_plt_modelo_version",$PARAMETRO_NO_REQUERIDO, "sin modelo"),
			"kilometros" 			=> request("_plt_kilometros",$PARAMETRO_NO_REQUERIDO, "sin kilometros"),
			"donde_taller" 			=> request("_plt_donde_taller",$PARAMETRO_NO_REQUERIDO, "sin taller"),
			"tipo_bici" 			=> request("_plt_tipo_bici",$PARAMETRO_NO_REQUERIDO, "sin tipo"),
			"page_url_link" 		=> $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
		
		);
		
		
		sendToAPI($data);
		
    } catch (Exception $e) {
        http_response_code(400);
        echo '{"status": "error", "code": 400, "errors": [' . $e->getMessage() . ']}';
		//echo "pilot_do_post()->:".$e->getMessage();
		die();
	}	
}



function sendToAPI($data=null){
	
	try {
		
		// *****************  PARAMETROS A MODIFICAR  ****************************************************//
		$serviceURL = "http://www.pilotsolution.com.ar/api/webhooks/welcome.php";
		// ***********************************************************************************************//
		
		## default
		$pilot_business_type_id = $data["pilot_business_type_id"];
		$pilot_suborigin_id 	= $data["pilot_suborigin_id"];
		$notes	  				= urlencode($data["pilot_notes"]);
		
		//CAPTURA DE PARAMETROS que pueden venir de un formulario
		$payload = "";
		$payload .= 'action=create';
		$payload .= '&debug='.$data["debug"];
		$payload .= '&appkey='.$data["appkey"];
		$payload .= '&pilot_contact_type_id='.$data["pilot_contact_type_id"];
		$payload .= '&pilot_firstname='.urlencode($data["pilot_firstname"]);
		$payload .= '&pilot_lastname='.urlencode($data["pilot_lastname"]);
		//************************************************************************************************************
		//AL MENOS UNO DE ESTOS PARAMETROS DEBER SER INFORMADO PARA QUE EL DATO INGRESE CORRETAMENTE Y NO SE RECHAZADO
		//************************************************************************************************************ 
		$payload .= '&pilot_phone='.urlencode($data["pilot_phone"]);
		$payload .= '&pilot_cellphone='."";
		$payload .= '&pilot_email='.urlencode($data["pilot_email"]);
		//************************************************************************************************************
						
		
        /*
		switch ($data["pilot_form_type"]) {
			
			case "plt_form_gl":
				
					switch ($data["tipo_departamento"]){
						
						case "1":
							//Ventas 0km
							$pilot_business_type_id = "1";
							$pilot_suborigin_id = "308B73FB";		
							break;
						
						case "2":
							//Ventas usados
							$pilot_business_type_id = "2";
							$pilot_suborigin_id = "1F0CB4EF";		
							break;			

						case "3":
							//Ventas autoplan
							$pilot_business_type_id = "3";
							$pilot_suborigin_id = "D40D93ED";		
							break;
							
						case "4":
							//Administración
							$pilot_business_type_id = "1";
							$pilot_suborigin_id = "C0CAE97C";		
							break;
							
						case "5":
							//Taller
							$pilot_business_type_id = "1";
							$pilot_suborigin_id = "0A996223";		
							break;	

						case "6":
							//Repuestos y accesorios
							$pilot_business_type_id = "1";
							$pilot_suborigin_id = "AE95344C";		
							break;			

						case "7":
							//RRHH
							$pilot_business_type_id = "1";
							$pilot_suborigin_id = "28839A54";		
							break;		

						case "8":
							//Departamento de calidad
							$pilot_business_type_id = "1";
							$pilot_suborigin_id = "54312F5D";		
							break;

						case "9":
							//Peugeot Professional
							$pilot_business_type_id = "1";
							$pilot_suborigin_id = "3C8F4FC9";		
							break;
						
							
						default:
							throw new Exception("no se definio el tipo de departamento");
						
					}
					
				
				break;
				
			case "plt_form_pa":
				$notes .= ' / MOTIVO CONSULTA:'.urlencode($data["motivo_consulta"]);
				$notes .= ' / INTENCION DE CIERRE:'.urlencode($data["cuando_operacion"]);
		
				break;
				
			case "plt_form_pv":
				$notes .= ' / MODELO Y VERSION:'.urlencode($data["modelo_version"]);
				$notes .= ' / KILOMETROS:'.urlencode($data["kilometros"]);
				$notes .= ' / TALLER:'.urlencode($data["donde_taller"]);
				
			
				break;
				
			case "plt_form_vn":
			
				$notes .= ' / TIPO OPERACION:'.urlencode($data["tipo_operacion"]);		
				$notes .= ' / INTENCION DE CIERRE:'.urlencode($data["cuando_operacion"]);
				break;
				
				
			case "plt_form_bc":
			
				$notes .= ' / TIPO BICICLETA:'.urlencode($data["tipo_bici"]);		
				break;
			
			case "plt_form_vo":
			
				$notes .= ' / MODELO Y VERSION:'.urlencode($data["modelo_version"]);
				
		}	
        */
		
		
		$payload .= '&pilot_business_type_id='.$pilot_business_type_id;
		$payload .= '&pilot_suborigin_id='.$pilot_suborigin_id;
		$payload .= '&pilot_notes='.$notes;
		
		$payload .= '&pilot_provider_url='.urlencode($data["page_url_link"]);	
	
		$output = posturl($serviceURL, $payload);       
		
		$response = json_decode($output, true);
		
		//IMPLEMENTAR METODO DE CAPTURA DE ERROR 
		if ($response["success"] == false){
			
			echo "No se pudo cargar el dato por : ".$response["data"]; 
			die();
		} else {
			
            header('Location:/gracias-por-consultar-por-nuestros-planes/');
		}
		
	
	// echo "<br> DEBUG (eliminar en produccion) :<br>"; 
	// echo "<pre>"; 
	// echo $output;
	// echo "</pre>"; 


	} catch (Exception $e) {
			
		echo "sendToAPI()".$e->getMessage();
		die();
			
	}
}



function posturl($url, $payload){

	(function_exists('curl_init')) ? '' : die('cURL Must be installed for geturl function to work. Ask your host to enable it or uncomment extension=php_curl.dll in php.ini');

	$curl = curl_init();
	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[] = "Cache-Control: max-age=0";
	$header[] = "Connection: keep-alive";
	$header[] = "Keep-Alive: 300";
	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[] = "Accept-Language: en-us,en;q=0.5";
	$header[] = "Pragma: ";
	
	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:5.0) Gecko/20100101 Firefox/5.0 Firefox/5.0');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_REFERER, $url);
	curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($curl, CURLOPT_AUTOREFERER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS,  $payload);
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); //CURLOPT_FOLLOWLOCATION Disabled...
	curl_setopt($curl, CURLOPT_TIMEOUT, 60);

	$html = curl_exec($curl);

	$status = curl_getinfo($curl);
	
	curl_close($curl);

	if($status['http_code']!=200){
		if($status['http_code'] == 301 || $status['http_code'] == 302) {
			list($header) = explode("\r\n\r\n", $html, 2);
			$matches = array();
			preg_match("/(Location:|URI:)[^(\n)]*/", $header, $matches);
			$url = trim(str_replace($matches[1],"",$matches[0]));
			$url_parsed = parse_url($url);
			return (isset($url_parsed))? geturl($url):'';
		}
		$oline='';
		foreach($status as $key=>$eline){$oline.='['.$key.']'.$eline.' ';}
		$line =$oline." \r\n ".$url."\r\n-----------------\r\n";
		$handle = @fopen('./curl.error.log', 'a');
		fwrite($handle, $line);
		return FALSE;
	}
	return $html;
}       
 
// Levanta los parámetros por post o get
function request($param, $required=true, $default="")
{
	$result = $default;
	
	//veo si esta seteado el parametro POST
	if (isset($_POST[$param])) {
		
		if($_POST[$param]!="")
		{
			$result = $_POST[$param];
		} else {
			if ($required)
			{
				throw new Exception("El parametro requerido ".$param." no fue seteado");
				
			}
		
		}
	}
	else if(isset($_GET[$param]))
	{
		if($_GET[$param]!="")
		{
			$result = $_GET[$param];
		} else {
			if ($required)
			{
				throw new Exception("El parametro requerido ".$param." no fue seteado");
			}
		
		}
	}
	else 
	{
		if ($required)
		{
			throw new Exception("El parametro requerido ".$param." no fue seteado");
			
		} 
	}
	
	return $result;
}	

function getCurlData($url){
    $selfpath = dirname(__FILE__);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 120);
	curl_setopt($curl, CURLOPT_CAINFO, $selfpath . 'cacert.pem');
	$curlData = curl_exec($curl);
	curl_close($curl);
	return $curlData;
}


function pilot_before_send_mail($contact_form) {
    echo(var_export($contact_form, 1));
}

// Register style sheet.
add_action('wp_loaded', 'plt_control_init');
//add_action('wp_enqueue_scripts', 'pilot_form_styles');
add_action('wp_enqueue_scripts', 'pilot_form_scripts');

add_shortcode('plt-form', 'pilot_contact_form_tag_func');
