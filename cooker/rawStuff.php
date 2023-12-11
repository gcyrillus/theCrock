<?php
		# portions du squelette
		$plxPlugin->bone='<?php if(!defined(\'PLX_ROOT\')) exit;
		/**
		* Plugin 			###NOMPLUGIN###
		*
		* @CMS required			PluXml 
		*
		* @version			###VERSION###
		* @date				###DATE###
		* @author 			###AUTEUR###
		**/
		class ###CLASSPLUGIN### extends plxPlugin {
		
		
		###PROPERTIES###
		
		public function __construct($default_lang) {
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		###ADMINACCESSCODE###
		###CONFIGACCESSCODE###		
		###GETCONFIG###		
		
		# Declaration des hooks		
###DECLARATIONHOOKS###
		
		}
		
		# Activation / desactivation
		
		public function OnActivate() {
		# code à executer à l’activation du plugin
		###wizardShow###
		}
		
		public function OnDeactivate() {
		# code à executer à la désactivation du plugin
		}	


		public function ThemeEndHead() {
			#gestion multilingue
			if(defined(\'PLX_MYMULTILINGUE\')) {		
				$plxMML = is_array(PLX_MYMULTILINGUE) ? PLX_MYMULTILINGUE : unserialize(PLX_MYMULTILINGUE);
				$langues = empty($plxMML[\'langs\']) ? array() : explode(\',\', $plxMML[\'langs\']);
				$string = \'\';
				foreach($langues as $k=>$v)	{
					$url_lang="";
					if($_SESSION[\'default_lang\'] != $v) $url_lang = $v.\'/\';
					$string .= \'echo "\\\t<link rel=\\\"alternate\\\" hreflang=\\\"\'.$v.\'\\\" href=\\\"".$plxMotor->urlRewrite("?\'.$url_lang.$this->getParam(\'url\').\'")."\" />\\\n";\';
				}
				echo \'<?php if($plxMotor->mode=="\'.$this->getParam(\'url\').\'") { \'.$string.\'} ?>\';
			}
			
			###STATICCSSFILE###
			// ajouter ici vos propre codes (insertion balises link, script , ou autre)
		}
		
		/**
		 * Méthode qui affiche un message si le plugin n\'a pas la langue du site dans sa traduction
		 * Ajout gestion du wizard si inclus au plugin
		 * @return	stdio
		 * @author	Stephane F
		 **/
		public function AdminTopBottom() {

			echo \'<?php
			$file = PLX_PLUGINS."\'.$this->plug[\'name\'].\'/lang/".$plxAdmin->aConf["default_lang"].".php";
			if(!file_exists($file)) {
				echo "<p class=\\\"warning\\\">\'.basename(__DIR__).\'<br />".sprintf("\'.$this->getLang(\'L_LANG_UNAVAILABLE\').\'", $file)."</p>";
				plxMsg::Display();
			}
			?>\';###ADMINTOPBOTTOMWIZARD###
		}
		###HOOKS###					
		}';
		
#propriétès de class du plugin		
		$plxPlugin->propertie101= '
		const BEGIN_CODE = \'<?php\' . PHP_EOL;
		const END_CODE = PHP_EOL . \'?>\';
		public $lang = \'\'; 
		';
		
 		$plxPlugin->propertieStatic='		
		private $url = \'\'; # parametre de l\'url pour accèder à la page static		
		';
# initialisation variable dans  __construct		
		$plxPlugin->adminAccess='
		# droits pour accèder à la page admin.php du plugin
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);';
		$plxPlugin->configAccess ='
		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);';
		$plxPlugin->getConfig=array();

		$plxPlugin->getConfigString='';

		$plxPlugin->staticConfigUrl ='	// url Page static
		$this->url = $this->getParam(\'url\')==\'\' ? strtolower(basename(__DIR__)) : $this->getParam(\'url\');	
		';	
		
		$plxPlugin->paramNumConfig='		$###PARAMNum### = $this->getParam(\'###PARAMNum###\') ==\'\'   ?   \'0\' : $this->getParam(\'###PARAMNum###\') ;';	
		$plxPlugin->paramNum=array();
		$plxPlugin->paramStringConfig='		$###PARAMString### = $this->getParam(\'###PARAMString###\') ==\'\'   ?   \'string\' : $this->getParam(\'###PARAMString###\') ;';
		$plxPlugin->paramString=array();
		$plxPlugin->paramCdataConfig='		$###PARAMCdata### = $this->getParam(\'###PARAMCdata###\') ==\'\'   ?   \'<p>cdata</p>\' : $this->getParam(\'###PARAMCdata###\') ;';
		$plxPlugin->paramCdata=array();

# squelettes et remplissages tableaux et chaines 
		$plxPlugin->selectedconfigHooks=	array();		

		$plxPlugin->selectedconfigHook='		$this->addHook(\'###HOOK###\', \'###HOOK###\');';
		$plxPlugin->functionStaticHooks= '
		/**
		* Méthode de traitement du hook plxShowConstruct
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function plxShowConstruct() {
		
		# infos sur la page statique
		$string  = "if(\\\$this->plxMotor->mode==\'".$this->url."\') {";
		$string .= "	\\\$array = array();";
		$string .= "	\\\$array[\\\$this->plxMotor->cible] = array(
		\'name\'		=> \'".$this->getParam(\'mnuName_\'.$this->default_lang)."\',
		\'menu\'		=> \'\',
		\'url\'		=>  \'".basename(__DIR__)."\',
		\'readable\'	=> 1,
		\'active\'	=> 1,
		\'group\'		=> \'\'
		);";
		$string .= "	\\\$this->plxMotor->aStats = array_merge(\\\$this->plxMotor->aStats, \\\$array);";
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
		$template = $this->getParam(\'template\')==\'\'?\'static.php\':$this->getParam(\'template\');
		
		$string = "
		if(\\\$this->get && preg_match(\'/^".$this->url."\\\/?/\',\\\$this->get)) {
		\\\$this->mode = \'".$this->url."\';
		\\\$prefix = str_repeat(\'../\', substr_count(trim(PLX_ROOT.\\\$this->aConf[\'racine_statiques\'], \'/\'), \'/\'));
		\\\$this->cible = \\\$prefix.\\\$this->aConf[\'racine_plugins\'].\'".basename(__DIR__)."/static\';
		\\\$this->template = \'".$template."\';
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
		if($this->getParam(\'mnuDisplay\')) {
		echo "<?php \\\$status = \\\$this->plxMotor->mode==\'".$this->url."\'?\'active\':\'noactive\'; ?>";
		echo "<?php array_splice(\\\$menus, ".($this->getParam(\'mnuPos\')-1).", 0, \'<li class=\\\"static menu \'.\\\$status.\'\\\" id=\\\"static-".basename(__DIR__)."\\\"><a href=\\\"\'.\\\$this->plxMotor->urlRewrite(\'?".$this->lang.$this->url."\').\'\\\" title=\\\"".$this->getParam(\'mnuName_\'.$this->default_lang)."\\\">".$this->getParam(\'mnuName_\'.$this->default_lang)."</a></li>\'); ?>";
		}
		}
		
		/**
		* Méthode qui renseigne le titre de la page dans la balise html <title>
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function plxShowPageTitle() {
		echo \'<?php
		if($this->plxMotor->mode == "\'.$this->url.\'") {
		$this->plxMotor->plxPlugins->aPlugins["\'.basename(__DIR__).\'"]->lang("L_PAGE_TITLE");
		return true;
		}
		?>\';
		}
		
		/**
		* Méthode qui référence la page statique dans le sitemap
		*
		* @return	stdio
		* @author	Stephane F
		**/
		public function SitemapStatics() {
		echo \'<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?\'.$this->lang.$this->url.\'")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>\';
		}
		
	
		';
		$plxPlugin->varStaticHooksThemeEndHead ='echo \' 		<link href="\'.PLX_PLUGINS.basename(__DIR__).\'/css/static.css" rel="stylesheet" type="text/css" />\'."\n";';
		
		$plxPlugin->selectedHook='
		/** 
		* Méthode ###HOOK###
		* 
		* Descrition	:
		* @author		: TheCrok
		* 
		**/
		public function ###HOOK###() {
		# code à executer
		
		###RETURNCODE###	
	
		}
		';

		
	$injectedcode= '
		echo self::BEGIN_CODE;
		?>
		// here the code to inject into native function at hook\'s position
		
		// return true; // use if needed : stops native function at this point
		<?php
		echo self::END_CODE;
		';
				
		$plxPlugin->staticHooks =array(
		'plxShowConstruct',
		'plxMotorPreChauffageBegin',
		'plxShowStaticListEnd',
		'plxShowPageTitle',
		'SitemapStatics'/*,
		'ThemeEndHead'*/
		);
		
		$plxPlugin->formXML= array(
		'title',
		'author',
		'version',
		'date',
		'site',
		'description',
		'scope',
		'icone'  
		);
  		$plxPlugin->formStructure= array(
		'cssDir',
		'cssSite',
		'cssAdmin',
		'jsDir',
		'jsSite',
		'jsAdmin',
		'imgDir',
		'assetsDir',
		'varAdmin',
		'langDir',
		'langAdmin',
		'helplangAdmin'  
		);
		$plxPlugin->formOption = array(
		'config',
		'admin',
		'static',
		'addWidget',
		'wizard',
		'multilang'
		);
		
		$plxPlugin->formParam = array(
		'string',
		'addstring',
		'cdata',
		'addcdata',
		'numeric',
		'addnumeric'
		);
		$plxPlugin->formHooksCustom = array(
		'hookCustom',
		'functionCustom'
		);
		$plxPlugin->formHooks = array(
		'adminFoot',
		'adminSettingsInfos',
		'adminPrepend'
		);
		$plxPlugin->formHooksArray = array(
		'adminArticleParam',
		'adminAuthParam',
		'adminCategoryParam',
		'adminCategoriesParam',
		'adminCommentParam',
		'adminCommentsParam',  
		'adminIndexParam',
		'adminMediasParam',
		'adminSettingsDisplayParam',
		'adminSettingsAdvancedParam',
		'adminSettingsBasParam',
		'adminSettingsEdittplParam',  
		'adminSettingsPluginsParam',
		'adminThemesDisplayParam',
		'adminSettingsUsersParam',  
		'adminProfilParam',
		'adminStaticParam',
		'adminStaticsParam',
		'adminTopParam',
		'adminUserParam',
		'plxAdminParam',
		'plxFeedParam',
		'plxMotorParam',
		'plxShowParam',
		'IndexParam',
		'SitemapParam',
		'FeedParam',
		'ThemeParam'
		);
		
		$plxPlugin->activateWizard='# activation du wizard
		$_SESSION[\'justactivated\'.basename(__DIR__)] = true;';
		

# traitement formulaire
	$MyFunctions=array();
	$pluginfunctions ='';
	$topWizard='';
	$configLink=' <a href="parametres_plugin.php?p=<?= basename(__DIR__) ?>"> Config </a>';
	$adminLink=' <a href="plugin.php?p=<?= basename(__DIR__) ?>"> Admin </a> ';
	$wizarLink='';
	$callWizard='';
	$wiz='//nowizards set';
	$widgetThemehook='';
	$getvarStatic='';
	$menuConfigParams='';
	$menuConfigStatic='';
	$setvarStatic='';
	$tabParams='';
	$tabStatic='';
	$setvarStaticTrue='	
	#multilingue
	$plxPlugin->setParam(\'mnuDisplay\', $_POST[\'mnuDisplay\'], \'numeric\');
	$plxPlugin->setParam(\'mnuPos\', $_POST[\'mnuPos\'], \'numeric\');
	$plxPlugin->setParam(\'template\', $_POST[\'template\'], \'string\');
	$plxPlugin->setParam(\'url\', plxUtils::title2url($_POST[\'url\']), \'string\');
	foreach($aLangs as $lang) {
	$plxPlugin->setParam(\'mnuName_\'.$lang, $_POST[\'mnuName_\'.$lang], \'string\');
	}
	';
	$getvarStaticTrue='	# initialisation des variables page statique
	$var[\'mnuDisplay\'] =  $plxPlugin->getParam(\'mnuDisplay\')==\'\' ? 1 : $plxPlugin->getParam(\'mnuDisplay\');
	$var[\'mnuPos\'] =  $plxPlugin->getParam(\'mnuPos\')==\'\' ? 2 : $plxPlugin->getParam(\'mnuPos\');
	$var[\'template\'] = $plxPlugin->getParam(\'template\')==\'\' ? \'static.php\' : $plxPlugin->getParam(\'template\');
	$var[\'url\'] = $plxPlugin->getParam(\'url\')==\'\' ? strtolower(basename(__DIR__)) : $plxPlugin->getParam(\'url\');
	
	# On récupère les templates des pages statiques
	$glob = plxGlob::getInstance(PLX_ROOT . $plxAdmin->aConf[\'racine_themes\'] . $plxAdmin->aConf[\'style\'], false, true, \'#^^static(?:-[\\\w-]+)?\\\.php$#\');
	if (!empty($glob->aFiles)) {
	$aTemplates = array();
	foreach($glob->aFiles as $v)
	$aTemplates[$v] = basename($v, \'.php\');
	} else {
	$aTemplates = array(\'\' => L_NONE1);
	}
	/* end template */
	';
	$menuConfigParamsTrue='<li id="tabHeader_Param"><?php $plxPlugin->lang(\'L_PARAMS\') ?></li>';
	
	$menuConfigStaticTrue=$menuConfigParams.'	<li id="tabHeader_main"><?php $plxPlugin->lang(\'L_MAIN\') ?></li>
	<?php
	foreach($aLangs as $lang) {
	echo \'<li id="tabHeader_\'.$lang.\'">\'.strtoupper($lang).\'</li>\';
	} ?>';
	$tabParamsTrue='	
	<div class="tabpage" id="tabpage_main">
	<fieldset>
	<p>
	<label for="id_url"><?php $plxPlugin->lang(\'L_PARAM_URL\') ?>&nbsp;:</label>
	<?php plxUtils::printInput(\'url\',$var[\'url\'],\'text\',\'20-20\') ?>
	</p>
	<p>
	<label for="id_mnuDisplay"><?php echo $plxPlugin->lang(\'L_MENU_DISPLAY\') ?>&nbsp;:</label>
	<?php plxUtils::printSelect(\'mnuDisplay\',array(\'1\'=>L_YES,\'0\'=>L_NO),$var[\'mnuDisplay\']); ?>
	</p>
	<p>
	<label for="id_mnuPos"><?php $plxPlugin->lang(\'L_MENU_POS\') ?>&nbsp;:</label>
	<?php plxUtils::printInput(\'mnuPos\',$var[\'mnuPos\'],\'text\',\'2-5\') ?>
	</p>
	<p>
	<label for="id_template"><?php $plxPlugin->lang(\'L_TEMPLATE\') ?>&nbsp;:</label>
	<?php plxUtils::printSelect(\'template\', $aTemplates, $var[\'template\']) ?>
	</p>	
	</fieldset>
	</div>';
	$tabStaticTrue= $tabParamsTrue.'
	<?php foreach($aLangs as $lang) : ?>
	<div class="tabpage" id="tabpage_<?php echo $lang ?>">
	<?php if(!file_exists(PLX_PLUGINS.basename(__DIR__).\'/lang/\'.$lang.\'.php\')) : ?>
	<p><?php printf($plxPlugin->getLang(\'L_LANG_UNAVAILABLE\'), PLX_PLUGINS.basename(__DIR__).\'/lang/\'.$lang.\'.php\') ?></p>
	<?php else : ?>
	<fieldset>
	<p>
	<label for="id_mnuName_<?php echo $lang ?>"><?php $plxPlugin->lang(\'L_MENU_TITLE\') ?>&nbsp;:</label>
	<?php plxUtils::printInput(\'mnuName_\'.$lang,$var[$lang][\'mnuName\'],\'text\',\'20-20\') ?>
	</p>
	</fieldset>
	<?php endif; ?>
	</div>
	<?php endforeach; ?>
	</div>';
	
	
#fonctions 



	#wizard adminTopBottom
	$WizardAdminTopBottom=PHP_EOL.'				if (isset($_SESSION[\'justactivated\'.basename(__DIR__)])) {$this->wizard();}'.PHP_EOL;
	
	
	$wizardFunction=PHP_EOL.'	/** 
		* Méthode Wizard
		* 
		* Descrition	: Affiche le wizard dans l\'administration
		* @author		: G.Cyrille
		* 
		**/
		# page administration affichage O/I wizard 
		public function wizard() {
		# uniquement dans les page d\'administration du plugin.
			if(basename(
			$_SERVER[\'SCRIPT_FILENAME\']) 			==\'parametres_plugins.php\' || 
			basename($_SERVER[\'SCRIPT_FILENAME\']) 	==\'parametres_plugin.php\' || 
			basename($_SERVER[\'SCRIPT_FILENAME\']) 	==\'plugin.php\'
			) 	{	
				include(PLX_PLUGINS.__CLASS__.\'/lang/\'.$this->default_lang.\'-wizard.php\');
			}		
		}'.PHP_EOL;
		
		
		
		

	
# fichiers	

	#infos.xml
	$infosxml = '<?xml version="1.0" encoding="UTF-8"?>
	<document>
	<title><![CDATA['.strip_tags($_POST['title']).']]></title>
	<author><![CDATA['.strip_tags($_POST['author']).']]></author>
	<version>'.strip_tags($_POST['version']).'</version>
	<date>'.$_POST['date'].'</date>
	<site>'.strip_tags($_POST['site']).'</site>
	<description><![CDATA['.strip_tags($_POST['description']).']]></description>
	<scope>'.strip_tags($_POST['scope']).'</scope>
	</document>';
	
	#config.php
	// generée à partir de recipe.php
	
	#admin.php
	