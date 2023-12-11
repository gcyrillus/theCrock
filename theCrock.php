<?php
	/**
		* Plugin 	theCrock
		* @author	Cyrille G.  @ re7net.com
		* barebone pour plugin
	**/
	class theCrock extends plxPlugin {
        const BEGIN_CODE = '<?php' . PHP_EOL;
        const END_CODE = PHP_EOL . '?>';
		
		private $url = ''; 					# parametre de l'url pour accèder à la page static
		public $lang ;
		public $className; 					# CLASS du nouveau plugin
		public $bone;						# squelette du plugin
		
		#propriétès de class du plugin
		public $properties ; 				# declaration des variables		
		public $propertie101;				# variable communes	
 		public $propertieStatic;			# variable url page statique 		
		
		# initialisation variable dans  __construct
		public $adminAccess='';				# droits accés admin
		public $configAccess='';			# droits accés config
		public $getConfig=array();			# tableau des variables du nouveau plugin
		public $getConfigString='';			# chaine. variable à insere dans le squelette
		public $staticConfigUrl;			# url page statique du plugin			
		public $paramNumConfig;				# squelette variable numeriques
		public $paramNum=array();			# tableau des variables numériques
		public $paramStringConfig;			# squelette variable chaine texte
		public $paramString=array();		# tableau des variables chaine texte
		public $paramCdataConfig;			# squelette des variables chaine complexes	
		public $paramCdata=array();			# tableau des variables chaine complexes
		public $selectedconfigHook;			# squelette de déclarations des hooks		
		public $selectedconfigHooks=array();# tableaux hooks et fonctions selectionné				
		public $functionStaticHooks;		# chaine, listes des fonction pour gerer la page statique du plugin	
		public $varStaticHooksThemeEndHead;	# lien CSS injecté dans la fonction ThemeEndHead
		public $selectedHook;				# squelette des fonctions		
		public $staticHooks =array();		# tableau des hooks pour gerer la page statique		
		public $selectedHooks =array();		# tableau des hooks selectionnés		
		public $formXML= array();			# tableau infos du fichier infos.xml
  		public $formStructure= array();		# tableau des option de structure et fichier 
		public $formOption = array();		# tableau des options de fonctionnalités		
		public $formParam = array();		# tableau des paramètres
		public $formHooksCustom = array();	# tableau des hooks et fonctions non natives
		public $formHooks = array();		# tableaux des hooks natifs
		public $formHooksArray = array();	# tableaux par familles de hooks natifs
		public $formDatas =array();			# tableau des données du formulaire		
		public $activateWizard;				# chaine affichage wizard (inserer dans la fonction onActivate()

		public $zip='plugins/theCrock/bowls/'; // stockage fichier generer
		public $zipTakeAway='plugins/theCrock/bowls/rawdish'; // stockage fichier basiques
		
		
		
		/**
			* Constructeur de la classe
			*
			* @param	default_lang	langue par défaut
			* @return	stdio
			* @author	Stephane F
		**/
		public function __construct($default_lang) {
			
		# gestion du multilingue plxMyMultiLingue
		$this->lang='';
		if(defined('PLX_MYMULTILINGUE')) {
			$lang = plxMyMultiLingue::_Lang();
			if(!empty($lang)) {
				if(isset($_SESSION['default_lang']) AND $_SESSION['default_lang']!=$lang) {
					$this->lang = $lang.'/';
				}
			}
		}		
			
			# appel du constructeur de la classe plxPlugin (obligatoire)
			parent::__construct($default_lang);	
			
			# droits pour accèder à la page config.php du plugin
			$this->setConfigProfil(PROFIL_ADMIN);
			
			# déclaration des hooks
			$this->addHook('IndexBegin','IndexBegin');		
			$this->addHook('AdminTopBottom','AdminTopBottom');
			$this->addHook('plxShowConstruct','plxShowConstruct');
			$this->addHook('plxMotorPreChauffageBegin','plxMotorPreChauffageBegin');
			$this->addHook('plxShowStaticListEnd','plxShowStaticListEnd');
			$this->addHook('plxShowPageTitle','plxShowPageTitle');
			$this->addHook('SitemapStatics','SitemapStatics');
			$this->addHook('theCrockWidget', 'widget');	
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('ThemeEndBody', 'ThemeEndBody');
		
			$this->url = $this->getParam('url')=='' ? strtolower(basename(__DIR__)) : $this->getParam('url');	
		

		
		}
		
		# Activation / desactivation
		public function OnActivate() {
		# activation du wizard
		$_SESSION['justactivated'.basename(__DIR__)] = true;			
		}
		
		# page administration affichage O/I wizard
		public function AdminTopBottom() {		
		if (isset($_SESSION['justactivated'.basename(__DIR__)])) {$this->wizard();}
		}
		
		# insertion du wizard
		public function wizard() {
		# uniquement dans les page d'administration du plugin.
		if(basename(
		$_SERVER['SCRIPT_FILENAME']) 			=='parametres_plugins.php' || 
		basename($_SERVER['SCRIPT_FILENAME']) 	=='parametres_plugin.php' || 
		basename($_SERVER['SCRIPT_FILENAME']) 	=='plugin.php'
		) {	
		include(PLX_PLUGINS.__CLASS__.'/lang/'.$this->default_lang.'-wizard.php');
		}
		}
		
		/**
		* Méthode de traitement du hook plxShowConstruct
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function plxShowConstruct() {
		
		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='".$this->url."') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
		'name'		=> '".$this->getParam('mnuName_'.$this->default_lang)."',
		'menu'		=> '',
		'url'		=> 'theCrock',
		'readable'	=> 1,
		'active'	=> 1,
		'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
		}
		
		/**
		* Méthode de traitement du hook plxMotorPreChauffageBegin
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function plxMotorPreChauffageBegin() {		
		$template = $this->getParam('template')==''?'static.php':$this->getParam('template');
		
		$string = "
		if(\$this->get && preg_match('/^".$this->url."\/?/',\$this->get)) {
		\$this->mode = '".$this->url."';
		\$prefix = str_repeat('../', substr_count(trim(PLX_ROOT.\$this->aConf['racine_statiques'], '/'), '/'));
		\$this->cible = \$prefix.\$this->aConf['racine_plugins'].'theCrock/static';
		\$this->template = '".$template."';
		return true;
		}
		";
		
		echo "<?php ".$string." ?>";
		}
		
		/**
		* Méthode de traitement du hook plxShowStaticListEnd
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function plxShowStaticListEnd() {
		
		# ajout du menu pour accèder à la page de recherche
		if($this->getParam('mnuDisplay')) {
		echo "<?php \$status = \$this->plxMotor->mode=='".$this->url."'?'active':'noactive'; ?>";
		echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li class=\"static menu '.\$status.'\" id=\"static-theCrock\"><a href=\"'.\$this->plxMotor->urlRewrite('?".$this->lang.$this->url."').'\" title=\"".$this->getParam('mnuName_'.$this->default_lang)."\">".$this->getParam('mnuName_'.$this->default_lang)."</a></li>'); ?>";
		}
		}
		
		/**
		* Méthode qui renseigne le titre de la page dans la balise html <title>
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function plxShowPageTitle() {
		echo '<?php
		if($this->plxMotor->mode == "'.$this->url.'") {
		$this->plxMotor->plxPlugins->aPlugins["theCrock"]->lang("L_PAGE_TITLE");
		return true;
		}
		?>';
		}
		
		/**
		* Méthode qui référence la page de recherche dans le sitemap
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function SitemapStatics() {
		echo '<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?'.$this->lang.$this->url.'")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>';
		}
		
		/**
		* Méthode statique qui affiche le widget
		*
		**/
		public static function widget($widget=false) {
		
		# récupération d'une instance de plxMotor
		$plxMotor = plxMotor::getInstance();
		$plxPlug = $plxMotor->plxPlugins->getInstance(basename(__DIR__));		
		include(PLX_PLUGINS.basename(__DIR__).'/widget.'.basename(__DIR__).'.php');
		}
		
		/** 
		*
		* Méthode d'ajout des <link rel="alternate"... sur les pages des plugins qui gèrent le multilingue
		*
		* @return	stdio
		* @author	WorldBot
		* 
		**/
		public function ThemeEndHead() {
		
		if(defined('PLX_MYMULTILINGUE')) {		
		$plxMML = is_array(PLX_MYMULTILINGUE) ? PLX_MYMULTILINGUE : unserialize(PLX_MYMULTILINGUE);
		$langues = empty($plxMML['langs']) ? array() : explode(',', $plxMML['langs']);
		$string = '';
		foreach($langues as $k=>$v)	{
		$url_lang="";
		if($_SESSION['default_lang'] != $v) $url_lang = $v.'/';
		$string .= 'echo "\t<link rel=\"alternate\" hreflang=\"'.$v.'\" href=\"".$plxMotor->urlRewrite("?'.$url_lang.$this->getParam('url').'")."\" />\n";';
		}
		echo '<?php if($plxMotor->mode=="'.$this->getParam('url').'") { '.$string.'} ?>';
		}
		echo '<link href="'.PLX_PLUGINS.basename(__DIR__).'/css/site.css" rel="stylesheet" type="text/css" />'."\n";
		
		}		
		
		/** methode insertion HTML avant fermeture body
		*
		**/
		public function ThemeEndBody() {
		echo self::BEGIN_CODE;
		?>
		$output = str_replace('</body>', ob_get_clean().'<script src="'.PLX_ROOT.'plugins/<?= __CLASS__ ?>/js/site.js"></script>'.PHP_EOL.'</body>', $output);
		<?php
		echo self::END_CODE;
		}
		
		/** 
		================== plugin functions ============ **
		**/
		public static function cleanString($str) {
		$str = plxUtils::removeAccents($str,PLX_CHARSET);
		$str = str_replace(' ', "", $str);
		return $str;
		}
		
		/**
		* Mise à jour du squelette
		* @param : array()
		**/
		public function updateBoneArray($array, $boneFile) {
		$this->bone = preg_replace(array_keys($array), array_values($array), $boneFile);
		}
		
		/**
		* Mise à jour du squelette
		* @param $string : string to replace
		* @param $value  : string to insert
		**/
		public function updateBoneString($string,$value,$boneFile) {
		$this->bone = preg_replace($string, $value, $boneFile);
		return $this->bone;
		}
		

		/**
		* ajoute au squelette : infos et nom de class
		*/
		public function preparePlugin($name,$version,$date,$auteur,$boneFile) {
		$version= $version=='' ? '1.0' :$version;
		$this->bone = $boneFile;
		
		#on créer le nom de class du plugin sans trous ni accents.
		$this->className = $this->cleanString($name);

		# tableau de remplacements
		$infos=array(
		'@###NOMPLUGIN###@' 	=>$name,
		'@###CLASSPLUGIN###@' 	=> $this->className,
		'@###VERSION###@'		=>$version,
		'@###DATE###@'			=>$date,
		'@###AUTEUR###@'		=>$auteur
		);
		
		# maj du squelette
		$this->updateBoneArray($infos,$this->bone);		
		}
		
		/**
		* ajoute au squelette : proprietés
		* $properties : array()
		**/
		public function addPluginProperties($properties) {
		###PROPERTIES###
		$this->updateBoneString('@###PROPERTIES###@',$properties);	
		}
		
		/**
		* ajoute au squelette : accés admin 
		* $properties : array()
		**/
		public function addPluginAdminAccess($adminAccess) {
		###ADMINACCESSCODE###
		$this->updateBoneString('@###ADMINACCESSCODE###@',$adminAccess);	
		}
		
		/**
		* ajoute au squelette : accés config 
		* $properties : array()
		**/
		public function addPluginConfigAccess($configAccess) {
		###CONFIGACCESSCODE###
		$this->updateBoneString('@###CONFIGACCESSCODE###@',$configAccess);	
		}
		
		/**
		* ajoute au squelette : configuration 
		* $properties : array()
		**/
		public function addPluginConfig($getConfig) {
		###GETCONFIG###
		foreach($getConfig as $key => $value) {
		$this->getConfigString .= $value.PHP_EOL;
		}
		$this->updateBoneString('@###GETCONFIG###@',$this->getConfigString, $this->bone);	
		}		
		
		/**
		* ajoute au squelette : hooks
		* $hooks : array()
		**/
		public function addPluginhooks($hooks) {
		###HOOK###
		$getHooks='';
		foreach($hooks as $key => $name) {
		if (str_contains($name, '*')) 	$name = str_replace('*', '', $name);
		$getHooks .= preg_replace('@###HOOK###@', $name, $this->selectedconfigHook).PHP_EOL;
		}
		$this->updateBoneString('@###DECLARATIONHOOKS###@',$getHooks, $this->bone);
		}
		
		/**
		* ajoute au squelette : myfunctions
		* $myfunction : array()
		**/
		public function addPluginMyFunctions($myfunctions) {
		###MYFUNCTIONS###
		$getMyFunctions='';
		foreach($hooks as $key => $name) {
		
		$getMyFunctions .= preg_replace('###HOOK###', $name, $selectedHook).PHP_EOL;
		}
		$this->updateBoneString('###MYFUNCTIONS###',$getMyFunctions);
		}
		
		/**
			* fait une copie de l'image au format png
			*
			* @author Cyrille G.
		**/
		public function saveToPng($filename) {
			$fileInfo = pathinfo($filename);
			$dirInfo = PLX_PLUGINS.__CLASS__.'/temp/';	
			if (!file_exists($dirInfo)) {mkdir($dirInfo, 0777, true);}				
			$imgNewsVersion= $dirInfo.'/newicon.png';				
			imagepng(imagecreatefromstring(file_get_contents($filename)),$imgNewsVersion );
			return $imgNewsVersion;			
		}		
		/**
		=================== ZIP ==================== 
		**/
		
		/**
			* Méthode de création du .ZIP
			*
			* Créer l'archive de base avec le squelette de la class
			* $file : nom du fichier de class
			* $content : Code de la class du plugin
			* @author Cyrille G.
		**/
		public function makeZip($file,$content,$zipFile) {
		$zip = new ZipArchive;
		$res = $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		if ($res === TRUE) {
		$zip->addFromString($file, $content);
		$zip->close();
		//echo  'creation archive '. $zipFile .'<br>';
		} else {
		echo 'échec création container du zip '. $file .PHP_EOL ;
		}
		}
		
		/**
			* Méthode de création repertoire racine dans l'archive zip
			*
			* @author Cyrille G.
		**/
		public function addRepertory($repertory,$zipFile){
		$zip = new ZipArchive;
		if ($zip->open($zipFile) === TRUE) {
		if($zip->addEmptyDir($repertory)) {
		//echo'okay<br>';
		} else {
		echo 'Impossible de créer un nouveau dossier'.$repertory.'<br>';
		}
		$zip->close();
		} else {
		echo 'Échec';
		}
		}
		
		/**
			* Méthode d'ajout de fichier dans l'archive à partir d'une chaine
			*
			* @author Cyrille G.
		**/
		public function addFiletxt( $file,$content,$zipFile) {
		$zip = new ZipArchive;
		$res = $zip->open($zipFile, ZipArchive::CREATE);
		if ($res === TRUE) {
		$zip->addFromString($file, $content);
		$zip->close();
		} else {
		echo 'échec ajout ficher:'. $file .' dans '. $zipFile .'<br>';
		}
		}
		
		/**
			* Méthode de televersement dans un fichier dans l'archive
			*
			* @author Cyrille G.
		**/
		public function addFiles( $path, $file, $zipFile) {
		$zip = new ZipArchive;
		if ($zip->open($zipFile) === TRUE) {
		$zip->addFile($path, $file);
		$zip->close();
		//echo 'ok ajout ficher:'. $file .' depuis '.$path.' dans '. $zipFile .'<br>';
		} else {
		//echo  'échec ajout ficher:'. $file .' depuis '.$path.'<br>';
		}
		
		
		}
		/**
			* Methode de destruction des zip de X jours
			*
			* @author Cyrille G.
		**/
		public function clearOldFiles($path,$days){
			$filenames = glob($path);
			foreach ($filenames as $filename) {	
				$dateFile = date('Y-m-d',filemtime($filename) );
				$today= date('Y-m-d');				
				$Filedate= date_create($dateFile);
				$dayto= date_create($today);	
				$diff=date_diff($Filedate,$dayto);
				if($diff->format("%a")>$days) unlink($filename);
			}
		}
		
		}		




