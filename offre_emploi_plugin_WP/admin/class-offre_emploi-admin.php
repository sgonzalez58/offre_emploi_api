<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/admin
 * @author     dev-iticonseil <dev@iti-conseil.com>
 */

 
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Offre_emploi_Admin {

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
		
		add_action('admin_menu', array($this, 'gestion_offre_emploi'));

		add_action('wp_ajax_get_nouvelles_offres', array($this,'get_nouvelles_offres'));
		add_action('wp_ajax_nopriv_get_nouvelles_offres', array($this,'get_nouvelles_offres'));

		add_action('wp_ajax_get_reponse_positive_offre', array($this,'get_reponse_positive_offre'));
		add_action('wp_ajax_nopriv_get_reponse_positive_offre', array($this,'get_reponse_positive_offre'));

		add_action('wp_ajax_get_reponse_negative_offre', array($this,'get_reponse_negative_offre'));
		add_action('wp_ajax_nopriv_get_reponse_negative_offre', array($this,'get_reponse_negative_offre'));

		add_action('wp_ajax_set_offre_archive', array($this,'set_offre_archive'));
		add_action('wp_ajax_nopriv_set_offre_archive', array($this,'set_offre_archive'));

		add_action('wp_ajax_importer_offres', array($this,'importer_offres'));
		add_action('wp_ajax_nopriv_importer_offres', array($this,'importer_offres'));

		add_action('init', array($this,'offre_emploi_rewrite_rules'));
		add_filter('query_vars', array($this,'offre_emploi_register_query_var' ));
		add_filter('template_include', array($this,'offre_emploi_front_end'));
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
		 * defined in Offre_emploi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Offre_emploi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

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
		 * defined in Offre_emploi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Offre_emploi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

	}

	/**
	 * Récupères les offres utilisateurs.
	 */
	function get_nouvelles_offres(){
		check_ajax_referer('liste_nouvelles_offres');
        
        $offres = $this->model->findAllOffresUser();

		$jsonData = [];
		$idx = 0;
		foreach($offres as $offre){
			$jsonData[$idx++] = ['intitule' => $offre['intitule'], 'nomVille' => $offre['ville_libelle'], 'nomEntreprise' => $offre['nom_entreprise'], 'dateDemande' => $offre['date_de_publication'], 'etat' => $offre['validation'], 'id' => $offre['id']];
		}

        wp_send_json_success($jsonData);
	}

	/**
	 * Valide une demande d'offre d'emploi et envoie un mail au demandeur
	 */
	function get_reponse_positive_offre(){
		check_ajax_referer('reponse_offre');

		$args = array(
			'id_offre' => $_POST['id_offre'],
			'commentaire' => $_POST['commentaire']
		);

		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé.");
		}
		if(!$this->model->findOneOffre($args['id_offre'])){
			wp_send_json_error("L'offre n'existe pas.");
		}

		$response = $this->model->accepterOffre($args['id_offre']);
		if( $response != 'Sql succès'){
			wp_send_json_error($response);
		}

		$this->envoi_email_utilisateur(get_userdata($this->model->findOneOffre($args['id_offre'])['user_id'])->user_email, $args['commentaire'], 'valide');
		wp_send_json_success('mail envoyé');
	}

	/**
	 * Refuse une demande d'offre d'emploi et envoie un mail au demandeur
	 */
	function get_reponse_negative_offre(){
		check_ajax_referer('reponse_offre');

		$args = array(
			'id_offre' => $_POST['id_offre'],
			'raison' => $_POST['raison']
		);

		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé.");
		}
		if(!$args['raison']){
			wp_send_json_error("Les raisons du refus n'ont pas été envoyées.");
		}
		if(!$this->model->findOneOffre($args['id_offre'])){
			wp_send_json_error("L'offre n'existe pas.");
		}

		$response = $this->model->refuserOffre($args['id_offre']);
		if( $response != 'Sql succès'){
			wp_send_json_error($response);
		}
		$this->envoi_email_utilisateur(get_userdata($this->model->findOneOffre($args['id_offre'])['user_id'])->user_email, $args['raison'], 'refus');
		wp_send_json_success('mail envoyé');
	}

	/**
	 * Archive une offre d'emploi refusée
	 */
	function set_offre_archive(){
		check_ajax_referer('reponse_offre');

		$args = array(
			'id_offre' => $_POST['id_offre']
		);
		
		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé. Erreure lors de la demande ajax.");
		}

		if(!$this->model->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->setOffreArchive($args['id_offre']);

		if($reponse != 'archivé'){
			wp_send_json_error('Erreure lors d\'archivage : '.$reponse);
		}else{
			wp_send_json_success($reponse);
		}
	}

	/**
	 * Ajoute un menu de gestion d'offre d'emploi sur la page admin.
	 */
	function gestion_offre_emploi(){
		$notification_count = $this->model->findCountPendingOffresUser();
		if($notification_count > 0){
			add_menu_page('Offre Emploi', 'Offre Emploi <span class="awaiting-mod">' . $notification_count . '</span>', 'edit_posts', 'gestion_offre_emploi', array($this, 'gestion_offre'));
		}else{
			add_menu_page('Offre Emploi', 'Offre Emploi', 'edit_posts', 'gestion_offre_emploi', array($this, 'gestion_offre'));
		}
		add_submenu_page('gestion_offre_emploi', 'Import offres', 'Import', 'edit_posts', 'import_offres_emploi', array($this, 'import_offres_emploi'));
	}

	/**
	 * Rendu visuel du mode admin.
	 * Affiche la fiche d'une offre d'emploi ou affiche le tableau de gestion des offres d'emploi.
	 */
	function gestion_offre(){
		//Affiche ici la fiche d'offre d'emploi
		if(isset($_GET['id_offre'])){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/offre_emploi_admin_display.php')) {
				wp_enqueue_style( $this->plugin_name.'.font-awesome', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'gestion_offre', plugin_dir_url( __FILE__ ) . 'css/gestion_offre_emploi.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'offre', plugin_dir_url( __FILE__ ) . 'css/offre.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'luxon', 'https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'datatable_luxon', 'https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-luxon.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'fiche_admin_offre_emploi', plugin_dir_url( __FILE__ ) . 'js/fiche_admin_offre_emploi.js', array( 'jquery' ), $this->version, true );
				$reponse_offre = wp_create_nonce( 'reponse_offre' );
				wp_localize_script(
					$this->plugin_name.'fiche_admin_offre_emploi',
					'confirmation_ajax',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => $reponse_offre,
					)
				);
				include(plugin_dir_path( __FILE__ ) .'partials/offre_emploi_admin_display.php');
				return;
			}
		}else{
			//affiche ici la gestion des offres d'emploi
			wp_enqueue_style( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'gestion_offre', plugin_dir_url( __FILE__ ) . 'css/gestion_offre_emploi.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'all', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'luxon', 'https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'datatable_luxon', 'https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-luxon.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'.popperjs', 'https://unpkg.com/@popperjs/core@2.11.6/dist/umd/popper.min.js', array( 'jquery' ), $this->version, false);
			wp_enqueue_script( $this->plugin_name.'gestion', plugin_dir_url( __FILE__ ) . 'js/gestion_offre_emploi.js', array( 'jquery' ), $this->version, true );
			$liste_offres = wp_create_nonce( 'liste_nouvelles_offres' );
			$refus_offre = wp_create_nonce( 'reponse_offre');
			wp_localize_script(
				$this->plugin_name.'gestion',
				'my_ajax_obj',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => $liste_offres,
				)
			);
			wp_localize_script(
				$this->plugin_name.'gestion',
				'confirmation_ajax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => $refus_offre,
				)
			);
			include(plugin_dir_path( __FILE__ ) .'partials/gestion_offre_emploi.php');
		}
	}

	function import_offres_emploi(){
		wp_enqueue_style( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'gestion_offre', plugin_dir_url( __FILE__ ) . 'css/gestion_offre_emploi.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'admin_import_offre', plugin_dir_url( __FILE__ ) . 'css/offre_emploi-admin-import.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'luxon', 'https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'datatable_luxon', 'https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-luxon.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'admin_import_offre', plugin_dir_url( __FILE__ ) . 'js/offre_emploi-admin-import.js', array( 'jquery' ), $this->version, true );
		$import = wp_create_nonce( 'import');
		wp_localize_script(
			$this->plugin_name.'admin_import_offre',
			'my_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $import,
			)
		);
		include(plugin_dir_path( __FILE__ ) .'partials/offre_emploi-admin-import.php');
		return;
	}

	function importer_offres(){
		check_ajax_referer('import');
		require_once plugin_dir_path( __FILE__ ) . '../library/recuperation_offre.php';
		$retour = getAnnonce();
		wp_send_json_success($retour);
		return;
	}

	/**
	 * Envoi de mail
	 */
	public function envoi_email_utilisateur($user_email, $content, $response){
		
		$mail = new PHPMailer(true);
		try {
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

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			if($response == 'valide'){
				$mail->Subject = utf8_decode("Validation de votre demande d'offre d'emploi");
				$message = "<table cellpadding=0 cellspacing=0>
						<tr>
							<td width='11px'></td>
								<td width='10px'>&nbsp;</td>
								<td width='729px'>
	
								<p>Bonjour,</p>
	
								<p>Votre offre a été validée et est visible dés maintenant sur notre site. </p>
							
								<p>".stripslashes($content)."</p>
								<p>Cordialement.</p>
							
								</td>
								<td width='10px'>&nbsp;</td>
							<td width='11px'></td>
						</tr>
						
					</table>";	
			}else{
				$mail->Subject = utf8_decode("Refus de votre demande d'offre d'emploi");
				$message = "<table cellpadding=0 cellspacing=0>
						<tr>
							<td width='11px'></td>
								<td width='10px'>&nbsp;</td>
								<td width='729px'>
	
								<p>Bonjour,</p>
	
								<p>Votre offre a été refusée pour les raisons suivante : </p>
							
								<p>".stripslashes($content)."</p>
								<p>Veuillez rectifier ces points et nous renvoyer la demande.<br>Cordialement.</p>
							
								</td>
								<td width='10px'>&nbsp;</td>
							<td width='11px'></td>
						</tr>
						
					</table>";	
			}
			
			$mail->Body    = $message;


			$mail->send();
			$filename = "/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----'.$user_email.'---'."\n");
			fputs($fp, 'Message has been sent OK !'."\n");
			
			fclose($fp);
			return 1;
		} catch (Exception $e) {
			$filename = "/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----'.$user_email.'---'."\n");
			fputs($fp, $mail->ErrorInfo."\n");
			
			fclose($fp);
			//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			return 0;
		}
	}

	function offre_emploi_rewrite_rules() {	

		add_rewrite_rule('^wp-admin/offreEmploi/postrs/?', 'index.php?postrs=1', 'top');  		
	}
	
	/**
	 * Initialisation des variables url
	 */
	function offre_emploi_register_query_var( $vars ) {
		
		$vars[] = 'postrs';

		return $vars;
	}
	
	/**
	 * affichage des pages du mode public
	*/
	function offre_emploi_front_end($template)
	{
		global $wp_query; //Load $wp_query object

		//affichage de la liste des offres
		if(array_key_exists('postrs',$wp_query->query_vars) && $wp_query->query_vars['postrs'] ==1){
			$nombre_total_offres = count($this->model->findByMotsClef());
			return;
		}
		return $template;
	}
}
