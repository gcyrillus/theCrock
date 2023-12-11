<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf['default_lang']);
	
	# fermeture du wizard
	if (isset($_SESSION['justactivated'.basename(__DIR__)])) {unset($_SESSION['justactivated'.basename(__DIR__)]);}
	# affichage du wizard à la demande
	if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
	/* end wizard */
	
	/* multilingue part */
	# Si le plugin plxMyMultiLingue est installé on filtre sur les langues utilisées
	# On garde par défaut le fr si aucune langue sélectionnée dans plxMyMultiLingue
	if(defined('PLX_MYMULTILINGUE')) {
		$langs = plxMyMultiLingue::_Langs();
		$multiLangs = empty($langs) ? array() : explode(',', $langs);
		$aLangs = $multiLangs;
	}
	/* en multilingue part*/
	
	/* saving config */
	if(!empty($_POST)) {
		$plxPlugin->setParam('frmDisplay', $_POST['frmDisplay'], 'numeric');
		$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
		$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
		$plxPlugin->setParam('template', $_POST['template'], 'string');
		$plxPlugin->setParam('url', plxUtils::title2url($_POST['url']), 'string');
		foreach($aLangs as $lang) {
			$plxPlugin->setParam('mnuName_'.$lang, $_POST['mnuName_'.$lang], 'string');
		}
		$plxPlugin->saveParams();
		if(is_file(PLX_ROOT.'.htaccess')) {
			$f = file_get_contents(PLX_ROOT.'.htaccess');
			$f = str_replace('[L]', '[QSA,L]', $f);
			plxUtils::write($f, PLX_ROOT.'.htaccess');
		}
		header('Location: parametres_plugin.php?p='. basename(__DIR__));
		exit;
	}
	/* end saving config */
	$var = array();
	# initialisation des variables propres à chaque lanque
	$langs = array();
	foreach($aLangs as $lang) {
		# chargement de chaque fichier de langue
		$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php');
		$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName_'.$lang);
	}
	# initialisation des variables communes à chaque langue
	$var['url'] = $plxPlugin->getParam('url')=='' ? strtolower(basename(__DIR__)) : $plxPlugin->getParam('url');
	$var['mnuDisplay'] =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
	$var['mnuPos'] =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
	$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
	
	/* end init var */
	
	# On récupère les templates des pages statiques
	$glob = plxGlob::getInstance(PLX_ROOT . $plxAdmin->aConf['racine_themes'] . $plxAdmin->aConf['style'], false, true, '#^^static(?:-[\w-]+)?\.php$#');
	if (!empty($glob->aFiles)) {
		$aTemplates = array();
		foreach($glob->aFiles as $v)
		$aTemplates[$v] = basename($v, '.php');
		} else {
		$aTemplates = array('' => L_NONE1);
	}
	/* end template */
?>
<link rel="stylesheet" href="<?= PLX_PLUGINS.basename(__DIR__ )?>/css/tabs.css" media="all" />
<style>
	form.inline-form label {
	width: 300px !important;
	}
</style>
<div id="tabContainer">
	<form class="inline-form" id="form_pluginMaker" action="parametres_plugin.php?p=<?= basename(__DIR__) ?>" method="post">
		<?php if(!file_exists(PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php')) : ?>
			<p><?php printf($plxPlugin->getLang('L_LANG_UNAVAILABLE'), PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php') ?></p>
		<?php else : ?>
			<fieldset>
				<p>
					<label><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;(<?= $lang ?>):</label>
					<?php plxUtils::printInput('mnuName_default_'.$lang,$var[$lang]['mnuName'],'text" disabled="disabled','20-20') ?>
				</p>
			</fieldset>
		<?php endif; ?>	
		<h2><?php $plxPlugin->lang('L_CONFIG') ?></h2>
		<div class="tabs">
			<ul>
				<li id="tabHeader_main"><?php $plxPlugin->lang('L_MAIN') ?></li>
				<?php
					foreach($aLangs as $lang) {
						echo '<li id="tabHeader_'.$lang.'">'.strtoupper($lang).'</li>';
					}
				?>
			</ul>
		</div>
		<div class="tabscontent">
		

					
			<div class="tabpage" id="tabpage_main">
				<fieldset>
					<p>
						<label for="id_url"><?php $plxPlugin->lang('L_PARAM_URL') ?>&nbsp;:</label>
						<?php plxUtils::printInput('url',$var['url'],'text','20-20') ?>
					</p>

					
					
					
					<p>
						<label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label>
						<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$var['mnuDisplay']); ?>
					</p>
					<p>
						<label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label>
						<?php plxUtils::printInput('mnuPos',$var['mnuPos'],'text','2-5') ?>
					</p>
					<p>
						<label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ?>&nbsp;:</label>
						<?php plxUtils::printSelect('template', $aTemplates, $var['template']) ?>
					</p>
				</fieldset>
			</div>
			<?php foreach($aLangs as $lang) : ?>
			<div class="tabpage" id="tabpage_<?php echo $lang ?>">
				<?php if(!file_exists(PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php')) : ?>
				<p><?php printf($plxPlugin->getLang('L_LANG_UNAVAILABLE'), PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php') ?></p>
				<?php else : ?>
				<fieldset>
					<p>
						<label for="id_mnuName_<?php echo $lang ?>"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;:</label>
						<?php plxUtils::printInput('mnuName_'.$lang,$var[$lang]['mnuName'],'text','20-20') ?>
					</p>
				</fieldset>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
		<fieldset>
			<p class="in-action-bar">
				<?php echo plxToken::getTokenPostMethod() ?>
				<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
			</p>
		</fieldset>
	</form>
</div>
<script type="text/javascript" src="<?php echo PLX_PLUGINS.basename(__DIR__)."/js/tabs.js" ?>"></script>