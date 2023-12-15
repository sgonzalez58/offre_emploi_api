<?php

/**
 * The automation-specific functionality of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/public
 */
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
/**
 * The automation-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the automation-specific stylesheet and JavaScript.
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/automation
 * @author     dev-iticonseil <dev@iti-conseil.com>
 */
class Offre_Emploi_Automation {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	private $model;
	private $facebookApi;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		require_once plugin_dir_path( __FILE__ ) . '../model/model-offre_emploi.php';
		$this->model = new Offre_Emploi_Model();
		
		add_shortcode('automate_emploi', array($this, 'sc_automate_emploi'));

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Klub_Koikispass_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Klub_Koikispass_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/klub-koikispass-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Klub_Koikispass_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Klub_Koikispass_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/klub-koikispass-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Effectue certaines actions en fonction de l'heure actuelle
	 *
	 */
	public function sc_automate_emploi($atts) {
		
		date_default_timezone_set('Europe/Paris');

		if( $_GET['send_fb_post'] == 'houhihouhaha'){
			ob_start();
			//$this->send_new_agenda();	
			$this->get_stats();
			return ob_get_clean();
		}

		if( $_GET['send_fb_post'] == 'v2bDj4qrhNMG4d06iP0jh1BvfdPWpmtLRX21nSv8WaBglleNWWUQBKdnHSvFWRoo'){
			ob_start();

			$minutes = date('i');
			$heure   = date('H');

			if($heure < 17 && $heure >= 9)	{

			 	if(($minutes >= 5 && $minutes < 15))
				{ 
					$this->send_mail_confirmation();
					$this->archiver_offre();
			 	}
			}

			return ob_get_clean();
		}elseif( $_GET['send_fb_post'] == 'FWfmI7dlKB7fBZ40wsmjpkhzLyjVsLQzP7kk1Wl0iCxCnMunRa5o4Y0CngmwIz4E' ){
			ob_start();
			$this->get_stats();
			return ob_get_clean();
		}else{
			echo "nope";
		}
		
	}
	
	public function send_mail_confirmation(){
		
		$offre_a_verifier = $this->model->getOffreAVerifier();
				
		foreach( $offre_a_verifier as $offre){

			if( $offre['clef'] == NULL ){
			
				$mail = $offre['email_notification'];
				
				$heure  = date('H');
				$minute = date('i');

				$clef = $this->model->genererClefMail($offre['id']);

				if($clef == 'error'){

					$this->envoi_mail_admin('mail_confirmation', $offre['id']);

				}

				$this->envoi_mail_confirmation_offre_emploi($offre['intitule'], $mail, 'https://koikispass.com/offres-emploi/'.$offre['id'].'/', 'https://koikispass.com/offres-emploi/'.$offre['id'].'/nonPourvu?key='.urlencode($clef), 'https://koikispass.com/offres-emploi/'.$offre['id'].'/pourvu?key='.urlencode($clef));

			}
		}
	}

	public function archiver_offre(){
		
		$offres_depassees = $this->model->getOffredepassee();
				
		foreach( $offres_depassees as $offre){

			$this->model->archiverOffreAuto($offre['id']);

		}
	}
	
	public function get_stats(){

		$token_auth = '0bcd2d953c851085a33bf0c0e58d1d51';

		$date_hier = new Datetime('yesterday');

        $mois = $date_hier->format('Y-m');

		$offres_stats = $this->model->getOffresStats();

		foreach($offres_stats as $offre){
		
			$url = "https://matomo.iticonseil.com/";
			$url .= "?module=API&method=Events.getName";
			// $url .= "&idSite=2&period=month&date=".(new Datetime('yesterday'))->format('Y-m-d')."&expanded=1";
			$url .= "&idSite=2&period=month&date=".$date_hier->format('Y-m-d')."&expanded=1";
			$url .= "&segment=eventName==".$offre['id']."&format=JSON&filter_limit=1";
			$url .= "&token_auth=$token_auth";

			$curl = curl_init();
	
			curl_setopt_array($curl, [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			]);
	
			$response = json_decode(curl_exec($curl));
			$err = curl_error($curl);
			
			if (!$err && $response) {
			
				$offre_emploi_historique = $this->model->getHistorique($offre['id'], $mois);

				$clics = 0;
				$vues = 0;
				$vues_liste = 0;
				$postuler = 0;

				foreach($response[0]->subtable as $table){
					if($table->label == "Affichage"){
						$vues = $table->nb_events;
					}
					if($table->label == 'Clic'){
						$clics = $table->nb_events;
					}
					if($table->label == 'Postuler'){
						$postuler = $table->nb_events;
					}
					if($table->label == 'AffichageListe'){
						$vues_liste = $table->nb_events;
					}
				}

				if(!$offre_emploi_historique){
					$this->model->ajouterHistorique($offre['id'], $mois, $clics, $vues, $vues_liste, $postuler);
				}else{
					$this->model->modifierHistorique($offre_emploi_historique['id'], $clics, $vues, $vues_liste, $postuler);
				}
			}
		}
	}

	
    public function converthtml($string){

        $c_html = ['&quot;', '&amp;', '&apos;', '&lt;', '&gt;', '&nbsp;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '&OElig;', '&oelig;', '&Scaron;', '&scaron;', '&Yuml;', '&fnof;', '&circ;', '&tilde;', '&Alpha;', '&Beta;', '&Gamma;', '&Delta;', '&Epsilon;', '&Zeta;', '&Eta;', '&Theta;', '&Iota;', '&Kappa;', '&Lambda;', '&Mu;', '&Nu;', '&Xi;', '&Omicron;', '&Pi;', '&Rho;', '&Sigma;', '&Tau;', '&Upsilon;', '&Phi;', '&Chi;', '&Psi;', '&Omega;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigmaf;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&thetasym;', '&upsih;', '&piv;', '&ensp;', '&emsp;', '&thinsp;', '&zwnj;', '&zwj;', '&lrm;', '&rlm;', '&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;', '&dagger;', '&Dagger;', '&bull;', '&hellip;', '&permil;', '&prime;', '&Prime;', '&lsaquo;', '&rsaquo;', '&oline;', '&frasl;', '&euro;', '&image;', '&weierp;', '&real;', '&trade;', '&alefsym;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&crarr;', '&lArr;', '&uArr;', '&rArr;', '&dArr;', '&hArr;', '&forall;', '&part;', '&exist;', '&empty;', '&nabla;', '&isin;', '&notin;', '&ni;', '&prod;', '&sum;', '&minus;', '&lowast;', '&radic;', '&prop;', '&infin;', '&ang;', '&and;', '&or;', '&cap;', '&cup;', '&int;', '&there4;', '&sim;', '&cong;', '&asymp;', '&ne;', '&equiv;', '&le;', '&ge;', '&sub;', '&sup;', '&nsub;', '&sube;', '&supe;', '&oplus;', '&otimes;', '&perp;', '&sdot;', '&lceil;', '&rceil;', '&lfloor;', '&rfloor;', '&loz;', '&spades;', '&clubs;', '&hearts;', '&diams;', '&lang;', '&rang;'];
        $c_dec = ['&#34;', '&#38;', '&#39;', '&#60;', '&#62;', '&#160;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;', '&#338;', '&#339;', '&#352;', '&#353;', '&#376;', '&#402;', '&#710;', '&#732;', '&#913;', '&#914;', '&#915;', '&#916;', '&#917;', '&#918;', '&#919;', '&#920;', '&#921;', '&#922;', '&#923;', '&#924;', '&#925;', '&#926;', '&#927;', '&#928;', '&#929;', '&#931;', '&#932;', '&#933;', '&#934;', '&#935;', '&#936;', '&#937;', '&#945;', '&#946;', '&#947;', '&#948;', '&#949;', '&#950;', '&#951;', '&#952;', '&#953;', '&#954;', '&#955;', '&#956;', '&#957;', '&#958;', '&#959;', '&#960;', '&#961;', '&#962;', '&#963;', '&#964;', '&#965;', '&#966;', '&#967;', '&#968;', '&#969;', '&#977;', '&#978;', '&#982;', '&#8194;', '&#8195;', '&#8201;', '&#8204;', '&#8205;', '&#8206;', '&#8207;', '&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8218;', '&#8220;', '&#8221;', '&#8222;', '&#8224;', '&#8225;', '&#8226;', '&#8230;', '&#8240;', '&#8242;', '&#8243;', '&#8249;', '&#8250;', '&#8254;', '&#8260;', '&#8364;', '&#8465;', '&#8472;', '&#8476;', '&#8482;', '&#8501;', '&#8592;', '&#8593;', '&#8594;', '&#8595;', '&#8596;', '&#8629;', '&#8656;', '&#8657;', '&#8658;', '&#8659;', '&#8660;', '&#8704;', '&#8706;', '&#8707;', '&#8709;', '&#8711;', '&#8712;', '&#8713;', '&#8715;', '&#8719;', '&#8721;', '&#8722;', '&#8727;', '&#8730;', '&#8733;', '&#8734;', '&#8736;', '&#8743;', '&#8744;', '&#8745;', '&#8746;', '&#8747;', '&#8756;', '&#8764;', '&#8773;', '&#8776;', '&#8800;', '&#8801;', '&#8804;', '&#8805;', '&#8834;', '&#8835;', '&#8836;', '&#8838;', '&#8839;', '&#8853;', '&#8855;', '&#8869;', '&#8901;', '&#8968;', '&#8969;', '&#8970;', '&#8971;', '&#9674;', '&#9824;', '&#9827;', '&#9829;', '&#9830;', '&#10216;', '&#10217;'];
        $c_hex = ['&#x22;', '&#x26;', '&#x27;', '&#x3c;', '&#x3e;', '&#xa0;', '&#xa1;', '&#xa2;', '&#xa3;', '&#xa4;', '&#xa5;', '&#xa6;', '&#xa7;', '&#xa8;', '&#xa9;', '&#xaa;', '&#xab;', '&#xac;', '&#xad;', '&#xae;', '&#xaf;', '&#xb0;', '&#xb1;', '&#xb2;', '&#xb3;', '&#xb4;', '&#xb5;', '&#xb6;', '&#xb7;', '&#xb8;', '&#xb9;', '&#xba;', '&#xbb;', '&#xbc;', '&#xbd;', '&#xbe;', '&#xbf;', '&#xc0;', '&#xc1;', '&#xc2;', '&#xc3;', '&#xc4;', '&#xc5;', '&#xc6;', '&#xc7;', '&#xc8;', '&#xc9;', '&#xca;', '&#xcb;', '&#xcc;', '&#xcd;', '&#xce;', '&#xcf;', '&#xd0;', '&#xd1;', '&#xd2;', '&#xd3;', '&#xd4;', '&#xd5;', '&#xd6;', '&#xd7;', '&#xd8;', '&#xd9;', '&#xda;', '&#xdb;', '&#xdc;', '&#xdd;', '&#xde;', '&#xdf;', '&#xe0;', '&#xe1;', '&#xe2;', '&#xe3;', '&#xe4;', '&#xe5;', '&#xe6;', '&#xe7;', '&#xe8;', '&#xe9;', '&#xea;', '&#xeb;', '&#xec;', '&#xed;', '&#xee;', '&#xef;', '&#xf0;', '&#xf1;', '&#xf2;', '&#xf3;', '&#xf4;', '&#xf5;', '&#xf6;', '&#xf7;', '&#xf8;', '&#xf9;', '&#xfa;', '&#xfb;', '&#xfc;', '&#xfd;', '&#xfe;', '&#xff;', '&#x152;', '&#x153;', '&#x160;', '&#x161;', '&#x178;', '&#x192;', '&#x2c6;', '&#x2dc;', '&#x391;', '&#x392;', '&#x393;', '&#x394;', '&#x395;', '&#x396;', '&#x397;', '&#x398;', '&#x399;', '&#x39a;', '&#x39b;', '&#x39c;', '&#x39d;', '&#x39e;', '&#x39f;', '&#x3a0;', '&#x3a1;', '&#x3a3;', '&#x3a4;', '&#x3a5;', '&#x3a6;', '&#x3a7;', '&#x3a8;', '&#x3a9;', '&#x3b1;', '&#x3b2;', '&#x3b3;', '&#x3b4;', '&#x3b5;', '&#x3b6;', '&#x3b7;', '&#x3b8;', '&#x3b9;', '&#x3ba;', '&#x3bb;', '&#x3bc;', '&#x3bd;', '&#x3be;', '&#x3bf;', '&#x3c0;', '&#x3c1;', '&#x3c2;', '&#x3c3;', '&#x3c4;', '&#x3c5;', '&#x3c6;', '&#x3c7;', '&#x3c8;', '&#x3c9;', '&#x3d1;', '&#x3d2;', '&#x3d6;', '&#x2002;', '&#x2003;', '&#x2009;', '&#x200c;', '&#x200d;', '&#x200e;', '&#x200f;', '&#x2013;', '&#x2014;', '&#x2018;', '&#x2019;', '&#x201a;', '&#x201c;', '&#x201d;', '&#x201e;', '&#x2020;', '&#x2021;', '&#x2022;', '&#x2026;', '&#x2030;', '&#x2032;', '&#x2033;', '&#x2039;', '&#x203a;', '&#x203e;', '&#x2044;', '&#x20ac;', '&#x2111;', '&#x2118;', '&#x211c;', '&#x2122;', '&#x2135;', '&#x2190;', '&#x2191;', '&#x2192;', '&#x2193;', '&#x2194;', '&#x21b5;', '&#x21d0;', '&#x21d1;', '&#x21d2;', '&#x21d3;', '&#x21d4;', '&#x2200;', '&#x2202;', '&#x2203;', '&#x2205;', '&#x2207;', '&#x2208;', '&#x2209;', '&#x220b;', '&#x220f;', '&#x2211;', '&#x2212;', '&#x2217;', '&#x221a;', '&#x221d;', '&#x221e;', '&#x2220;', '&#x2227;', '&#x2228;', '&#x2229;', '&#x222a;', '&#x222b;', '&#x2234;', '&#x223c;', '&#x2245;', '&#x2248;', '&#x2260;', '&#x2261;', '&#x2264;', '&#x2265;', '&#x2282;', '&#x2283;', '&#x2284;', '&#x2286;', '&#x2287;', '&#x2295;', '&#x2297;', '&#x22a5;', '&#x22c5;', '&#x2308;', '&#x2309;', '&#x230a;', '&#x230b;', '&#x25ca;', '&#x2660;', '&#x2663;', '&#x2665;', '&#x2666;', '&#x27e8;', '&#x27e9;'];
        $c_car = ['"', '&', '\'', '<', '>', ' ', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨', '©', 'ª', '«', '¬', '­', '®', '¯', '°', '±', '²', '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½', '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ', 'Œ', 'œ', 'Š', 'š', 'Ÿ', 'ƒ', 'ˆ', '˜', 'Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω', 'α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'ς', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω', 'ϑ', 'ϒ', 'ϖ', ' ', ' ', ' ', '‌', '‍', '‎', '‏', '–', '—', '‘', '’', '‚', '“', '”', '„', '†', '‡', '•', '…', '‰', '′', '″', '‹', '›', '‾', '⁄', '€', 'ℑ', '℘', 'ℜ', '™', 'ℵ', '←', '↑', '→', '↓', '↔', '↵', '⇐', '⇑', '⇒', '⇓', '⇔', '∀', '∂', '∃', '∅', '∇', '∈', '∉', '∋', '∏', '∑', '−', '∗', '√', '∝', '∞', '∠', '∧', '∨', '∩', '∪', '∫', '∴', '∼', '≅', '≈', '≠', '≡', '≤', '≥', '⊂', '⊃', '⊄', '⊆', '⊇', '⊕', '⊗', '⊥', '⋅', '⌈', '⌉', '⌊', '⌋', '◊', '♠', '♣', '♥', '♦', '⟨', '⟩'];
        $string = str_replace($c_html, $c_car, $string);
        $string = str_replace($c_dec, $c_car, $string);
        $string = str_replace($c_hex, $c_car, $string);

        return $string;

    }
	
	public function envoi_mail_confirmation_offre_emploi($titre, $user_email, $lien_publication, $lien_non_pourvu, $lien_pourvu){
		
		$mail = new PHPMailer(true);
		try {
			//Server settings
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			//$mail->SMTPDebug = 2;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp-out.iti-conseil.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = false;                                   //Enable SMTP authentication
			$mail->Username   = '';                     //SMTP username
			$mail->Password   = '';                               //SMTP password
			$mail->SMTPSecure = 'tls';         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->SMTPAutoTLS = false;
			$mail->SMTPOptions = array(
					'ssl' => array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signed' => true
					));
			//Recipients
			$mail->CharSet = 'utf-8';
			$mail->setFrom('no-reply@koikispass.com', 'Koikispass.com');
			$mail->addReplyTo('no-reply@koikispass.com', 'Koikispass');		
			$mail->addAddress($user_email);
			//$mail->addBcc('cb@iti-conseil.com');			

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = utf8_decode( 'Votre offre '.$titre.' sur Koikispass');
			$message = '<table width="540" border="0" cellspacing="0" cellpadding="0" class="mobile-view" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">

				<tr>
					<td width="729px">
					
						<p>Bonjour,</p>
						<p>Votre offre d\'emploi est aujourd\'hui toujours visible sur le site Koikispass.</p>
						<p>Cette offre est visible à l’adresse suivante : </p>
						<p><a href="'.$lien_publication.'">Voir l\'offre</a></p>
						<p>Pour améliorer l’impact de nos offres et rendre la votre encore plus visible, nous avons besoin de confirmer sa disponibilité.</p>
						
						<p>Si votre offre est encore disponible, veuillez cliquer sur le lien suivant :</p>
						<p><a href="'.$lien_non_pourvu.'">Mon offre est encore disponible</a></p>

						<p>Dans le cas contraire, veuillez cliquer sur ce lien :</p>
						<p><a href="'.$lien_pourvu.'">Mon offre n\'est plus disponible</a></p>
				
					</td>
				</tr>
				<p>Cordialement,<br/>L\'équipe de Koikispass</p>
						
				</table>


				<table width="540" border="0" cellspacing="0" cellpadding="0" class="mobile-view" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					<tbody>
						<tr>
							<td valign="top" align="left" class="clump" style="padding:0 0 10px;" width="60">
								<!-- Add the Mark of your Company Logo -->
								<img src="https://www.iti-conseil.com/signatures/koikispass/images/logo-kkp-vertical-13.png" alt="Logo" border="0" width="60" style="padding:10px 0 0 0; display:block; border:0; outline:none;">
							</td>
							<td valign="top" align="left" class="clump" style="padding:10px 0 0 10px;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
									<tbody>                        
										<tr>
											<td style="padding:0 0 0 10px;">
												<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
													<tbody>
														<tr>
															<td width="100%" class="clump">
																<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
																	<tbody>
																		<tr>
																			<td width="25" height="30" valign="top"><img src="https://www.iti-conseil.com/signatures/koikispass/images/icon_red_phone.png" alt="Phone" border="0" width="18" style="padding:3px 0 0 0; display:block; border:0; outline:none;"></td>
																			<!-- Edit your Phone number -->
																			<td width="" height="30" valign="top" align="left" style="font-family:\'Raleway\', sans-serif, Arial; font-size:12px; line-height:24px; font-weight:400; color:#2f3542;"> 03 86 61 56 52</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										
										<tr>
											<td style="padding:0 0 0 10px;">
												<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
													<tbody>
														<tr>
															<td width="33%" class="clump">
																<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
																	<tbody>
																		<tr>
																			<td width="25" height="30" valign="top"><img src="https://www.iti-conseil.com/signatures/koikispass/images/icon_red_address.png" alt="Address" border="0" width="18" style="padding:3px 0 0 0; display:block; border:0; outline:none;"></td>
																			<!-- Edit your Address -->
																			<td width="" height="30" valign="top" align="left" style="font-family:\'Raleway\', sans-serif, Arial; font-size:12px; line-height:24px; font-weight:400; color:#2f3542;"> 12 Avenue Marceau - 58000 Nevers</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										
										<tr>
											<td style="padding:0 0 0 10px;">
												<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
													<tbody>
														<tr>
															<td width="33%" class="clump">
																<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
																	<tbody>
																		<tr>
																			<td width="25" height="30" valign="top"><img src="https://www.iti-conseil.com/signatures/koikispass/images/icon_red_website.png" alt="Web" border="0" width="18" style="padding:3px 0 0 0; display:block; border:0; outline:none;"></td>
																			<!-- Edit your website URL -->
																			<td width="" height="30" valign="top" align="left" style="font-family:\'Raleway\', sans-serif, Arial; font-size:12px; line-height:24px; font-weight:400; color:#2f3542;"><a href="https://www.koikispass.com" style="text-decoration:none; color:#2f3542;">www.koikispass.com</a></td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										
										<tr>
											<td style="padding:10px 0 10px 10px;">
												<img src="https://www.iti-conseil.com/signatures/koikispass/images/divider.png" alt="Logo" border="0" width="100%" height="1" style="display:block; border:0; outline:none;">
											</td>
										</tr>
										
										<tr>
											<!-- Edit your social network URLs -->
											<td>
												<a href="https://www.facebook.com/koikispass"><img src="https://www.iti-conseil.com/signatures/koikispass/images/social_icon_dark_facebook.png" alt="Facebook" width="35" border="0" style="display:inline-block; border:0; outline:none;"></a>
												<!-- <a href="#"><img src="https://www.iti-conseil.com/signatures/koikispass/images/social_icon_dark_twitter.png" alt="Twitter" width="35" border="0" style="display:inline-block; border:0; outline:none;" /></a> -->
												<a href="#"><img src="https://www.iti-conseil.com/signatures/koikispass/images/social_icon_dark_linkedin.png" alt="LinkedIN" width="35" border="0" style="display:inline-block; border:0; outline:none;"></a>
												<a href="https://www.instagram.com/koikispass/"><img src="https://www.iti-conseil.com/signatures/koikispass/images/social_icon_dark_instagram.png" alt="Instagram" width="35" border="0" style="display:inline-block; border:0; outline:none;"></a>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>';


			$mail->Body    = $message;


			$mail->send();
			//echo 'Message has been sent';
			$filename = "/home/www/www.koikispass.com/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----'.$user_email.' -- '.$lien_publication.'---'."\n");
			fputs($fp, 'Message has been sent OK !'."\n");
			
			fclose($fp);
			return 1;
		} catch (Exception $e) {
			$filename = "/home/www/www.koikispass.com/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----'.$user_email.' -- '.$lien_publication.'---'."\n");
			fputs($fp, $mail->ErrorInfo."\n");
			
			fclose($fp);
			//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			return 0;
		}
	}	

	public function envoi_mail_admin($subject, $attr){
		
		$mail = new PHPMailer(true);
		try {
			//Server settings
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			//$mail->SMTPDebug = 2;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp-out.iti-conseil.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = false;                                   //Enable SMTP authentication
			$mail->Username   = '';                     //SMTP username
			$mail->Password   = '';                               //SMTP password
			$mail->SMTPSecure = 'tls';         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->SMTPAutoTLS = false;
			$mail->SMTPOptions = array(
					'ssl' => array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signed' => true
					));
			//Recipients
			$mail->CharSet = 'utf-8';
			$mail->setFrom('no-reply@koikispass.com', 'Koikispass.com');
			$mail->addReplyTo('no-reply@koikispass.com', 'Koikispass');		
			$mail->addAddress('cb@iti-conseil.com');
			//$mail->addBcc('cb@iti-conseil.com');			

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			if($subject == 'mail_confirmation'){
				
				$mail->Subject = utf8_decode( 'Erreur lors de l\'envoi du mail de rafraichissement d\'une offre d\'envoi');
				$message = '<table cellpadding=0 cellspacing=0>
					<tr>
						<td width="11px"></td>
							<td width="10px">&nbsp;</td>
							<td width="729px">
							
								<p>Bonjour,</p>
								<p>Une erreur est survenue lors de la création de la clef d\'identification de l\'offre d\'id .'.$attr.'</p>
								<p>Merci de vérifier si le code fonctionne toujours.</p>
								<p>Si c\'est le cas, veuillez réessayer d\'envoyer le mail.</p>

								<p>Cordialement,</p>
						
							</td>
							<td width="10px">&nbsp;</td>
						<td width="11px"></td>
					</tr>
					<p>Cordialement,<br/>L\'équipe de Koikispass</p>';
			}
			$mail->Body    = $message;


			$mail->send();
			//echo 'Message has been sent';
			$filename = "/home/www/www.koikispass.com/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----cb@iti-conseil.com -- erreur clef offre '.$attr.'---'."\n");
			fputs($fp, 'Message has been sent OK !'."\n");
			
			fclose($fp);
			return 1;
		} catch (Exception $e) {
			$filename = "/home/www/www.koikispass.com/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----cb@iti-conseil.com -- erreur clef offre '.$attr.'---'."\n");
			fputs($fp, $mail->ErrorInfo."\n");
			
			fclose($fp);
			//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			return 0;
		}
	}	
}


