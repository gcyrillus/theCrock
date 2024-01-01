<?php	


# preparation fichier infos.xml et description dans la class du plugin	
	$plxPlugin->preparePlugin($_POST['title'],$_POST['version']='1.0',$_POST['date'],$_POST['author'], $plxPlugin->bone);
# Ajout variable plugin 
	# voir variable : $plxPlugin->propertie101 dans rawStuff.php . 
	# Alimente $plxPlugin->properties avant insertion
	if(isset($_POST['static'])) {
		$plxPlugin->propertie101 .= $plxPlugin->propertieStatic;
		}	

# Alimentations du tableau des hooks
	# ajout hook multilingue - toujours présent
	$plxPlugin->selectedHooks[]='AdminTopBottom';
	$plxPlugin->selectedHooks[]='ThemeEndHead';
	
	if(isset($_POST['static'])) {
	#ajout hook page statique
		foreach($plxPlugin->staticHooks as $staticK => $staticV){
			$plxPlugin->selectedHooks[]=$staticV;
		}
	}
	# un widget ?
	//$widgetThemehook = L_NONE1 ;
	$widgetThemehook ='aucun';
	if(isset($_POST['addWidget']) && !empty($_POST['addWidget'])) {	
		$plxPlugin->selectedHooks[]= $plxPlugin->className.'widget';
		$widgetThemehook ='<?php eval($plxShow->callHook(\''.$plxPlugin->className.'widget\')); ?>';// hook a copier/coller 
	}	
	if(isset($_POST['wizard'])) {	
	#ajout hook wizard
	$plxPlugin->selectedHooks[]='wizard';
	$wizarLink=' <a href="parametres_plugin.php?p=<?= basename(__DIR__) ?>&wizard" class="aWizard"><img src="<?= PLX_PLUGINS.basename(__DIR__)?>/img/wizard.png" style="height:2em;vertical-align:middle" alt="Wizard"> Wizard</a>';
	$callWizard =PHP_EOL.'
				# affichage du wizard à la demande
				if(isset($_GET[\'wizard\'])) {$_SESSION[\'justactivated\'.basename(__DIR__)] = true;}
				# fermeture session wizard
				if (isset($_SESSION[\'justactivated\'.basename(__DIR__)])) {
					unset($_SESSION[\'justactivated\'.basename(__DIR__)]);
					$this->wizard();
					}
			';
	
	}
	
	
	
	# y-a-t-il des hook ou fonction maisons?
	if(isset($_POST['hookCustom']) && !empty(trim($_POST['hookCustom']))) {
		$_POST['hookCustom']=explode(',',$_POST['hookCustom']);
		foreach($_POST['hookCustom'] as $key => $homeMadeHook) {
			$plxPlugin->selectedHooks[] = 'MyH'.$plxPlugin->cleanString($homeMadeHook);
		}
	}	
	if(isset($_POST['functionCustom']) && !empty(trim($_POST['functionCustom']))) {
		$_POST['functionCustom']=explode(',',$_POST['functionCustom']);
		foreach($_POST['functionCustom'] as $key => $homeMadeFunction) {
			$_POST['functionCustom'][$key] = $plxPlugin->cleanString($homeMadeFunction);
			$MyFunctions[] =$_POST['functionCustom'][$key];
		}
	}
	# hook choisis
	
	foreach($plxPlugin->formHooksArray as $key => $hook) {
		if(isset($_POST[$hook])) {		
			foreach($_POST[$hook] as $key => $addstring) {
				if($_POST[$hook][$key] =='') {
					unset($_POST[$hook][$key]);
					continue;
				}
				$addstring = trim($plxPlugin->cleanString($addstring));
				$plxPlugin->selectedHooks[] = $addstring;
			}
			if (str_ends_with($hook, 'Param')) continue;
			$plxPlugin->selectedHooks[] = $hook;
		}
	}

# Injection des hooks
	$plxPlugin->selectedHooks = array_unique($plxPlugin->selectedHooks);
	$plxPlugin->addPluginhooks($plxPlugin->selectedHooks);	


# Composants
	# y-a-t-il une page statique maj param var
	if(isset($_POST['static'])) {
		$setvarStatic=$setvarStaticTrue;
		$getvarStatic=$getvarStaticTrue;
		$menuConfigStatic=$menuConfigStaticTrue;
		//$tabParamsTrue
		$tabStatic=$tabStaticTrue;	
	}
	
	# y-a-til un widget?
	if(isset($_POST['addWidget']) && !empty($_POST['addWidget'])) {
	$widgetThemehook ='<?php eval($plxShow->callHook(\''.$plxPlugin->className.'widget\')); ?>';
	$widgetExample= '<?php  if(!defined(\'PLX_ROOT\')) exit; 
	// exemples :
	echo $plxPlug->getParam(\'url\');
	echo $plxPlug->getInfo(\'title\'); // object protected inaccessible en dehors de la class admin
	
	// passons par la class admin
	echo \'<?php
		$plxAdmin = plxAdmin::getInstance();\';
	echo \'	$plxPlugin = $plxAdmin->plxPlugins->getInstance(\\\'\'.basename(__DIR__).\'\\\'); 
	?>\'; 
	?>
<!-- exemples -->
<p>Widget 		<?php echo \'<?= $plxPlugin->getInfo(\\\'title\\\') ?> \'?></p>
<p>Paramètre	<?php echo \'<?= $plxPlugin->getParam(\\\'url\\\'); ?> \'?></p>	';

	$pluginfunctions .='
			/**
		* Méthode statique qui affiche le widget
		*
		**/
		public static function '.$plxPlugin->className.'widget($widget=false) {
		
		# récupération d\'une instance de plxMotor
		$plxMotor = plxMotor::getInstance();
		$plxPlug = $plxMotor->plxPlugins->getInstance(basename(__DIR__));		
		include(PLX_PLUGINS.basename(__DIR__).\'/widget.\'.basename(__DIR__).\'.php\');
		}
	'.PHP_EOL;		
	}


# Variable de la class
	# injection des propriétés	
	$plxPlugin->properties = $plxPlugin->propertie101;
	
	$plxPlugin->bone = $plxPlugin->updateBoneString('@###PROPERTIES###@',$plxPlugin->properties,$plxPlugin->bone);

# function __construct{}
	# injection accés admin	?
	if(!isset($_POST['admin'])) {$plxPlugin->adminAccess = '';$adminLink='';}
	$plxPlugin->bone = $plxPlugin->updateBoneString('@###ADMINACCESSCODE###@',$plxPlugin->adminAccess,$plxPlugin->bone);
	
	# injection config ?
	#preparation de la chaine	
	if(!isset($_POST['config'])) {$plxPlugin->configAccess = ''; $configLink='';}
	$plxPlugin->bone = $plxPlugin->updateBoneString('@###CONFIGACCESSCODE###@',$plxPlugin->configAccess,$plxPlugin->bone);
	
	# injection infos de configuration	
	if(isset($_POST['static'])) {
		$plxPlugin->getConfig[] = $plxPlugin->staticConfigUrl;
	}
	# injection infos de configuration des paramêtres
	if(isset($_POST['string'])) { //Param exemple
		$plxPlugin->getConfig[] = preg_replace('@###PARAMString###@', 'string101', $plxPlugin->paramStringConfig);
		$plxPlugin->paramString[] = 'string101';
	}
	if(isset($_POST['addstring']) && !empty($_POST['addstring'])) {
		$_POST['addstring']=explode(',',trim($_POST['addstring']));
		foreach($_POST['addstring'] as $key => $addstring) {
			if($_POST['addstring'][$key] =='') {
				unset($_POST['addstring'][$key]);
				continue;
			}
			$plxPlugin->paramString[] = $plxPlugin->cleanString($addstring);
			$_POST['addstring'][$key] = $plxPlugin->cleanString($addstring); 
			$plxPlugin->getConfig[] = preg_replace('@###PARAMString###@', $addstring, $plxPlugin->paramStringConfig);
		}
	}
	
	if(isset($_POST['cdata'])) { //Param exemple
		$plxPlugin->getConfig[] = preg_replace('@###PARAMCdata###@', 'CDATA101', $plxPlugin->paramCdataConfig);
		$plxPlugin->paramCdata[] = 'CDATA101';
	}
	if(isset($_POST['addcdata']) && !empty($_POST['addcdata'])){
		$_POST['addcdata']=explode(',',$_POST['addcdata']);
		foreach($_POST['addcdata'] as $key => $addstring) {
			if($_POST['addcdata'][$key] =='') {
				unset($_POST['addcdata'][$key]);
				continue;
			}
			$plxPlugin->paramCdata[] = $plxPlugin->cleanString($addstring); 
			$_POST['addcdata'][$key] = $plxPlugin->cleanString($addstring); 
			$plxPlugin->getConfig[] = preg_replace('@###PARAMCdata###@', $addstring, $plxPlugin->paramCdataConfig);
		}	
	}
	
	if(isset($_POST['numeric'])){ //Param exemple
		$plxPlugin->getConfig[] = preg_replace('@###PARAMNum###@','numeric101', $plxPlugin->paramNumConfig);
		$plxPlugin->paramNum[] = 'numeric101';
	}
	if(isset($_POST['addnumeric']) && !empty($_POST['addnumeric']) ) {
		$_POST['addnumeric']=explode(',',$_POST['addnumeric']);
		foreach($_POST['addnumeric'] as $key => $addstring) {
			if($_POST['addnumeric'][$key] =='') {
				unset($_POST['addnumeric'][$key]);
				continue;
			}
			$plxPlugin->paramNum[] = trim($plxPlugin->cleanString($addstring));
			$_POST['addnumeric'][$key] = trim($plxPlugin->cleanString($addstring));
			$plxPlugin->getConfig[] = preg_replace('@###PARAMNum###@', $addstring, $plxPlugin->paramNumConfig);
		}
	}
	
	if(isset($_POST['config'])) {
		$numParam=array();
		foreach($plxPlugin->paramNum as $pnum => $p) {			
			$numParam['getvar'][]='	$var[\''.$p.'\'] = $plxPlugin->getParam(\''.$p.'\')==\'\' ? 0: $plxPlugin->getParam(\''.$p.'\');';	
			$numParam['setvar'][]='	$plxPlugin->setParam(\''.$p.'\', $_POST[\''.$p.'\'], \'numeric\');';	
			$numParam['setParamNum'][]='	<p>
			<label for="'.$p.'">'.$p.'</label> 
			<?php plxUtils::printSelect(\''.$p.'\',array(\'1\'=>L_YES,\'0\'=>L_NO), $var[\''.$p.'\']);?>		
			</p>';
		}
		$stringParam=array();
		foreach($plxPlugin->paramString as $pstring => $p) {			
			$stringParam['getvar'][]='	$var[\''.$p.'\'] = $plxPlugin->getParam(\''.$p.'\')==\'\' ? \'texte simple\': $plxPlugin->getParam(\''.$p.'\');';	
			// 3types de parametres!
			$stringParam['setvar'][]='	$plxPlugin->setParam(\''.$p.'\', $_POST[\''.$p.'\'], \'string\');';	
			$stringParam['setParamString'][]='	<p>
			<label for="'.$p.'">'.$p.'</label> 
			<?php plxUtils::printInput(\''.$p.'\',$var[\''.$p.'\'],\'text\',\'20-20\') ?>		
			</p>';
		}
		$cdataParam=array();
		foreach($plxPlugin->paramCdata as $pcdata => $p) {			
			$cdataParam['getvar'][]='	$var[\''.$p.'\'] = $plxPlugin->getParam(\''.$p.'\')==\'\' ? \'<p>chaines complexes</p>\': $plxPlugin->getParam(\''.$p.'\');';	
			$cdataParam['setvar'][]='	$plxPlugin->setParam(\''.$p.'\', $_POST[\''.$p.'\'], \'cdata\');';	
			$cdataParam['setParamCdata'][]='	<p>
			<label for="'.$p.'">'.$p.'</label> 
			<?php plxUtils::printArea(\''.$p.'\', $var[\''.$p.'\'], $cols=\'\', $rows=\'\', $readonly=false, $className=\'full-width\', $extra=\'\') ?>		
			</p>';
		}
	}	
	
	# injection des variables de configurations (injectable(s)) dans admin.php, config.php et xx-wizard.php)
	$plxPlugin->addPluginConfig($plxPlugin->getConfig);	
	


	
# Les fonctions	
	
	# Option wizard	
	if(isset($_POST['wizard'])) {
		$pluginfunctions .=PHP_EOL.'		/** 
		* Méthode wizard
		* 
		* Descrition	:
		* @author		: \'.$_POST[\'title\'].\'
		* 
		**/
		# insertion du wizard
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
		
		$wiz = $plxPlugin->activateWizard;
	}	
	# fin de recolte, maj du squelette



	if(!isset($_POST['static'])) {$WizardAdminTopBottom = '';}

	## fin injection declarations des hooks	
	
	#insertion du wizard dans la fonction onactivate();
	$plxPlugin->bone = $plxPlugin->updateBoneString('@###wizardShow###@',$wiz,$plxPlugin->bone);
	$plxPlugin->bone = $plxPlugin->updateBoneString('@###ADMINTOPBOTTOMWIZARD###@',$callWizard,$plxPlugin->bone);

#injections des fonctions des hooks 
	# la page statique
	if(isset($_POST['static'])) {
		$pluginfunctions .= $plxPlugin->functionStaticHooks;
	}

	#les fonctions maisons
	if(count($MyFunctions)>0){
		foreach($MyFunctions as $key => $addstring) {
			$customFunction  = 'MyF'.trim($plxPlugin->cleanString($addstring));
			#ajout des fonctions 
			$pluginfunctions .= preg_replace('@###HOOK###@', $customFunction, $plxPlugin->selectedHook).PHP_EOL;

		}	
	}
	
	# creations des squelettes des fonctions	
	if(isset($_POST['static'])) {
		//$plxPlugin->selectedHooks = array_diff($plxPlugin->selectedHooks, $plxPlugin->staticHooks);		
		$plxPlugin->bone  = preg_replace('@###STATICCSSFILE###@',$plxPlugin->varStaticHooksThemeEndHead,$plxPlugin->bone);
		}
		else {
			$plxPlugin->bone  = preg_replace('@###STATICCSSFILE###@','',$plxPlugin->bone);
		}
	$duplicate=array('AdminTopBottom','ThemeEndHead', $plxPlugin->className.'widget', 'wizard');
	foreach($plxPlugin->selectedHooks as $functions => $function) {
		$returnCode='';
		if (str_contains($function, '*')) {	
		$function = str_replace('*', '', $function);
		$returnCode =$injectedcode;
		}

	  if (in_array($function, $duplicate)) continue;
	  if (isset($_POST['static']) && in_array($function, $plxPlugin->staticHooks)) continue;
	  if (str_ends_with($function, 'Param')) continue;
		$returnFunction   = preg_replace('@###RETURNCODE###@',$returnCode,$plxPlugin->selectedHook);
		$pluginfunctions .= preg_replace('@###HOOK###@', $function, $returnFunction).PHP_EOL;
 
	}
	
	$plxPlugin->bone = $plxPlugin->updateBoneString('@###HOOKS###@',$pluginfunctions,$plxPlugin->bone);
	
	#creation de la page config
	$getvar='';
	if(isset($numParam['getvar'])) {
		foreach($numParam['getvar'] as $k => $v) {
			$getvar.=PHP_EOL.$v;
		}
	}
	if(isset($stringParam['getvar'])) {
		foreach($stringParam['getvar'] as $k => $v) {
			$getvar.=PHP_EOL.$v;
		}
	}
	if(isset($cdataParam['getvar'])) {
		foreach($cdataParam['getvar'] as $k => $v) {
			$getvar.=PHP_EOL.$v;
		}
	}
	$setvar='';
	if(isset($numParam['setvar'])) {
		foreach($numParam['setvar'] as $k => $v) {
			$setvar.=PHP_EOL.$v;
		}
	}
	if(isset($stringParam['setvar'])) {
		foreach($stringParam['setvar'] as $k => $v) {
			$setvar.=PHP_EOL.$v;
		}
	}
	if(isset($cdataParam['setvar'])) {
		foreach($cdataParam['setvar'] as $k => $v) {
			$setvar.=PHP_EOL.$v;
		}
	}
	$setParamNum='';
	if(isset($numParam['setParamNum'])&& count($numParam['setParamNum']) >0 ) {
		foreach($numParam['setParamNum'] as $k => $v) {
			$setParamNum.=PHP_EOL.$v;
		}
		$menuConfigParams=$menuConfigParamsTrue;
	}
	$setParamString='';
	if(isset($stringParam['setParamString'])&& count($stringParam['setParamString']) >0 ) {
		foreach($stringParam['setParamString'] as $k => $v) {
			$setParamString.=PHP_EOL.$v;
		}
			$menuConfigParams=$menuConfigParamsTrue;
	}
	$setParamcdata='';
	if(isset($cdataParam['setParamCdata']) && count($cdataParam['setParamCdata'])>0) {
		foreach($cdataParam['setParamCdata'] as $k => $v) {
			$setParamcdata.=PHP_EOL.$v;
		}
			$menuConfigParams=$menuConfigParamsTrue;
	}

# les fichiers
	# page de configuration
	$configAdmin='<?php
	if(!defined(\'PLX_ROOT\')) exit;
	/**
	* Plugin 			'.$plxPlugin->className.'
	*
	* @CMS required		PluXml 
	* @page				config.php
	* @version			'.$_POST['version'].'
	* @date				'.$_POST['date'].'
	* @author 			'.$_POST['author'].'
	**/	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf[\'default_lang\']);	
	
	if(!empty($_POST)) {
	'.$setvar.'
	'.$setvarStatic.'
	$plxPlugin->saveParams();	
	header("Location: parametres_plugin.php?p=".basename(__DIR__));
	exit;
	}
	'.$getvar.'
	# initialisation des variables propres à chaque lanque
	$langs = array();
	foreach($aLangs as $lang) {
	# chargement de chaque fichier de langue
	$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.\''.$plxPlugin->className.'/lang/\'.$lang.\'.php\');
	$var[$lang][\'mnuName\'] =  $plxPlugin->getParam(\'mnuName_\'.$lang)==\'\' ? $plxPlugin->getLang(\'L_DEFAULT_MENU_NAME\') : $plxPlugin->getParam(\'mnuName_\'.$lang);
	}
	'. $getvarStatic.'
	'. $callWizard .'
	?>
	<link rel="stylesheet" href="<?php echo PLX_PLUGINS."'.$plxPlugin->className.'/css/tabs.css" ?>" media="all" />
	<p>'.strip_tags($_POST['description']).'</p>	
	<h2><?php $plxPlugin->lang("L_CONFIG") ?></h2>
	'.$adminLink.' '.$wizarLink.'
	<div id="tabContainer">
	<form action="parametres_plugin.php?p=<?= basename(__DIR__) ?>" method="post" >
	<div class="tabs">
	<ul>
	'.$menuConfigParams.'
	'.$menuConfigStatic.'
	</ul>
	</div>
	<div class="tabscontent">
	<div class="tabpage" id="tabpage_Param">	
	<fieldset><legend><?= $plxPlugin->getLang(\'L_PARAMS_NUM\') ?></legend>
	'.$setParamNum.'	
	</fieldset>
	<fieldset><legend><?= $plxPlugin->getLang(\'L_PARAMS_STRING\') ?></legend>
	'.$setParamString.'	
	</fieldset>
	<fieldset><legend><?= $plxPlugin->getLang(\'L_PARAMS_CDATA\') ?></legend>
	'.$setParamcdata.'	
	</fieldset>
	</div>
	'.$tabStatic.'
	<fieldset>
	<p class="in-action-bar">
	<?php echo plxToken::getTokenPostMethod() ?><br>
	<input type="submit" name="submit" value="<?= $plxPlugin->getLang(\'L_SAVE\') ?>"/>
	</p>
	</fieldset>
	</form>
	</div>
	<script type="text/javascript" src="<?php echo PLX_PLUGINS."'.$plxPlugin->className.'/js/tabs.js" ?>"></script>';


	
	
	
	# fichier admin.php
	$adminAdmin = '<?php
	if(!defined(\'PLX_ROOT\')) exit;
	/**
	* Plugin 			'.$plxPlugin->className.'
	*
	* @CMS required		PluXml 
	* @page				admin.php
	* @version			'.$_POST['version'].'
	* @date				'.$_POST['date'].'
	* @author 			'.$_POST['author'].'
	**/	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf[\'default_lang\']);	
	
	if(!empty($_POST)) {
	'.$setvar.'
	'.$setvarStatic.'

	$plxPlugin->saveParams();
	
	header("Location: plugin.php?p=".basename(__DIR__));
	exit;
	}
	# init vars / remove unecessary
	'.$getvar.'
	# initialisation des variables propres à chaque lanque
	$langs = array();
	foreach($aLangs as $lang) {
	# chargement de chaque fichier de langue
	$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.\''.$plxPlugin->className.'/lang/\'.$lang.\'.php\');
	$var[$lang][\'mnuName\'] =  $plxPlugin->getParam(\'mnuName_\'.$lang)==\'\' ? $plxPlugin->getLang(\'L_DEFAULT_MENU_NAME\') : $plxPlugin->getParam(\'mnuName_\'.$lang);
	}
	# init static page var
	'. $getvarStatic.'


	?>
	<link rel="stylesheet" href="<?php echo PLX_PLUGINS."'.$plxPlugin->className.'/css/tabs.css" ?>" media="all" />
	<p>'.strip_tags($_POST['description']).'</p>	
	<h2><?php $plxPlugin->lang("L_ADMIN") ?></h2>
	'.$configLink.' '.$wizarLink.'
	<div>any parameters you wish to see or treat </div>
	<form action="plugin.php?p=<?= basename(__DIR__) ?>" method="post" >
		<fieldset>
			here any parameters you wish to modify and save
		</fieldset>
		<fieldset>
			<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?><br>
			<input type="submit" name="submit" value="<?= $plxPlugin->getLang(\'L_SAVE\') ?>"/>
			</p>
		</fieldset>
	</form>
	';
	
	
	# fichier '$lang'.-wizard.php

	$wiz101='<?php
	if(!defined(\'PLX_ROOT\')) exit; 
	/**
	* Plugin 			'.$plxPlugin->className.'
	*
	* @CMS required		PluXml 
	* @page				'.$plxPlugin->lang.'-wizard.php
	* @version			'.$_POST['version'].'
	* @date				'.$_POST['date'].'
	* @author 			'.$_POST['author'].'
	**/		
	
	# pas d\'affichage dans un autre plugin !	
	if(isset($_GET[\'p\'])&& $_GET[\'p\'] !== \''.$plxPlugin->className.'\' ) {goto end;}
	
	# on charge la class du plugin pour y accéder
	$plxMotor = plxMotor::getInstance();
	$plxPlugin = $plxMotor->plxPlugins->getInstance( \''.$plxPlugin->className.'\'); 
	
	# On vide la valeur de session qui affiche le Wizard maintenant qu\'il est visible.
	if (isset($_SESSION[\'justactivated'.$plxPlugin->className.'\'])) {unset($_SESSION[\'justactivated'.$plxPlugin->className.'\']);}
	
	# initialisation des variables propres à chaque lanque 
	$langs = array();
	
	# initialisation des variables communes à chaque langue	
	$var = array();
	'.$getvar.'
	
	#affichage
	?>
	<link rel="stylesheet" href="<?= PLX_PLUGINS ?>'.$plxPlugin->className.'/css/wizard.css" media="all" />
	<input id="closeWizard" type="checkbox">
	<div class="wizard">	
	<div class="container">	
	<div class=\'title-wizard\'>
	<h2><?= $plxPlugin->aInfos[\'title\']?><br><?= $plxPlugin->aInfos[\'version\']?></h2>
	<img src="<?php echo PLX_PLUGINS. \''.$plxPlugin->className.'\'?>/icon.png">
	<div><q> Made in <?= $plxPlugin->aInfos[\'author\']?> </q></div>
	</div>
	<p></p>
	
	<div id="tab-status">
	<span class="tab active">1</span>
	</div>		
	<form action="parametres_plugin.php?p=<?php echo \''.$plxPlugin->className.'\' ?>"  method="post">
	<div role="tab-list">		
	<div role="tabpanel" id="tab1" class="tabpanel">
	<h2>Bienvenue dans l’extension <b style="font-family:cursive;color:crimson;font-variant:small-caps;font-size:2em;vertical-align:-.5rem;display:inline-block;"><?= $plxPlugin->aInfos[\'title\']?></b></h2>
	<p>Welcome text</p>
	</div>	
	<div role="tabpanel" id="tab2" class="tabpanel hidden title">
	<h2>Page 2</h2>
	<p>desc page 2</p>
	<!-- Ci-dessous , valide le passage à une autre page si d\'autre champs required existe dans le formulaire -->
	<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
	</div>		
	<div role="tabpanel" id="tabEnd" class="tabpanel hidden title">
	<h2>The End</h2>
	<p>Enregistrer ou fermer</p>
	<!-- Ci-dessous , valide le passage à une autre page si d\'autre champs required existe dans le formulaire -->
	<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
	</div>		
	<div class="pagination">
	<a class="btn hidden" id="prev"><?php $plxPlugin->lang(\'L_PREVIOUS\') ?></a>
	<a class="btn" id="next"><?php $plxPlugin->lang(\'L_NEXT\') ?></a>
	<?php echo plxToken::getTokenPostMethod().PHP_EOL ?>
	<button class="btn btn-submit hidden" id="submit"><?php $plxPlugin->lang(\'L_SAVE\') ?></button>
	</div>
	</div>		
	</form>			
	<p class="idConfig">
	<?php
	if(file_exists(PLX_PLUGINS. \''.$plxPlugin->className.'/admin.php\')) {echo \' 
	<a href="/core/admin/plugin.php?p= '.$plxPlugin->className.'">Page d\\\'administration \'. basename(__DIR__ ).\'</a>\';}
	if(file_exists(PLX_PLUGINS. \''.$plxPlugin->className.'/config.php\')) {echo \' 	<a href="/core/admin/parametres_plugin.php?p='.$plxPlugin->className.'">Page de configuration  '.$plxPlugin->className.'</a>\';}
	?>
	<label for="closeWizard"> Fermer </label>
	</p>	
	</div>	
	<script src="<?= PLX_PLUGINS ?>'.$plxPlugin->className.'/js/wizard.js"></script>
	</div>
	<?php end: // FIN! ?>				
	';

	
	# langs
	$lang='<?php
	$LANG = array(
	\'L_PAGE_TITLE\'				=> \'Plugin'.strip_tags($_POST['title']).'\',
	\'L_DESCRIPTION\'				=> \'Description\',
	
	# config multilingue 
	\'L_DEFAULT_MENU_NAME\'		=> \''.trim($_POST['title']).'\',
	\'L_MENU_DISPLAY\'			=> \'Afficher la page static au menu\',
	\'L_FORM_DISPLAY\'			=> \'Afficher le formulaire de recherche sur la page de recherche\',
	\'L_MENU_TITLE\'				=> \'Titre de la page et du menu\',
	\'L_MENU_POS\'		    	=> \'Position du menu\',
	\'L_TEMPLATE\'				=> \'Gabari\',
	\'L_PARAM_URL\'				=> \'Nom du paramètre dans l\\\'url\',
	
	#wizard
	\'L_PREVIOUS\'				=> \'Page précedente\',
	\'L_NEXT\'					=> \'Page suivante\',
	\'L_CLOSE\'					=> \'Fermer l\\\'aide\',
	
	#config
	\'L_SAVE\'					=> \'enregistrer\',
	\'L_PARAMS\'					=> \'Paramètres\',
	\'L_CONFIG\'					=> \'Configuration\',
	\'L_ADMIN\'						=> \'Administration\',
	\'L_PARAMS_NUM\'				=> \'Paramètres numériques\',
	\'L_PARAMS_STRING\'			=> \'Paramètres textes\',
	\'L_PARAMS_CDATA\'			=> \'Paramètres bloc de textes\',
	\'L_MAIN\'					=> \'Page statique\',
	# et ainsi de suite pour chaque mot(s) à traduire
	);';
	$helpLang='<div>
	<h1>Aide du plugin '.strip_tags($_POST['title']).'</h1>
	<p>aide redigé</p>
	<p>Hook du widget >'.$widgetThemehook.'</p>
	</div>';
	# creation de l'archive  xxx.zip
	$zipFile=$plxPlugin->zip.$plxPlugin->className.'.zip';
	
	$plxPlugin->makeZip($plxPlugin->className.'.php',$plxPlugin->bone,$zipFile  );
	$plxPlugin->addFiletxt('infos.xml', $infosxml, $zipFile);
	if(isset($_POST['static'])) {
		$plxPlugin->addFiletxt('static.'.$plxPlugin->className.'.php', '<?php if(!defined(\'PLX_ROOT\')) exit; ?>'.PHP_EOL.'<div>Contenu de votre page</div>'.PHP_EOL.'<?php //et/ou code php '.PHP_EOL.'?>', $zipFile);
		$plxPlugin->addFiletxt('css/'.'site.css', '/* your style here */',$zipFile);
		$plxPlugin->addFiletxt('js/'.'site.js', '/* your javascript here */',$zipFile);
	}
	if(isset($_POST['addWidget']) && !empty($_POST['addWidget'])) { // ajout du fichier widget
		$plxPlugin->addFiletxt('widget.'.$plxPlugin->className.'.php', $widgetExample, $plxPlugin->zip.$plxPlugin->className.'.zip');
	}
	if(isset($_POST['config'])) {
		$plxPlugin->addFiletxt('config.php', $configAdmin, $plxPlugin->zip.$plxPlugin->className.'.zip');
	}
	if(isset($_POST['admin'])) {
		$plxPlugin->addFiletxt('admin.php', $adminAdmin, $plxPlugin->zip.$plxPlugin->className.'.zip');
	}
	if(isset($_POST['config']) || isset($_POST['admin'])) {
		$plxPlugin->addFiles(PLX_PLUGINS.'theCrock/js/tabs.js','js/tabs.js',$zipFile);
		$plxPlugin->addFiles(PLX_PLUGINS.'theCrock/css/tabs.css','css/tabs.css',$zipFile);
	}
	if(isset($_POST['wizard'])) {
		$plxPlugin->addFiles(PLX_PLUGINS.'theCrock/css/wizard.css','css/wizard.css',$zipFile);
		$plxPlugin->addFiles(PLX_PLUGINS.'theCrock/img/wizard.png','img/wizard.png',$zipFile);
		$plxPlugin->addFiles(PLX_PLUGINS.'theCrock/js/wizard.js','js/wizard.js',$zipFile);
		$plxPlugin->addFiletxt('lang/'.$plxPlugin->default_lang.'-wizard.php', $wiz101,$zipFile);
	}
	if(isset($_FILES['icone']) && file_exists($_FILES['icone']['tmp_name'])){
		$plxPlugin->addFiles($plxPlugin->saveToPng($_FILES['icone']['tmp_name']),'icon.png',$zipFile);
		unlink(PLX_PLUGINS.'theCrock/temp/newicon.png');
	}
	//$plxPlugin->addRepertory('lang',$zipFile);
	$plxPlugin->addFiletxt('lang/'.$plxPlugin->default_lang.'.php', $lang, $zipFile);
	$plxPlugin->addFiletxt('lang/'.$plxPlugin->default_lang.'-help.php', $helpLang, $zipFile);
	$plxPlugin->addRepertory('css',$zipFile);
	$plxPlugin->addRepertory('img',$zipFile);
	$plxPlugin->addRepertory('assets',$zipFile)
