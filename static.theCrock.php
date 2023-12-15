<?php 

	$row="";	
	$color = 'orange';
	$plxPlugin = $this->plxMotor->plxPlugins->getInstance(basename(__DIR__));
		$aLangs = array($this->plxMotor->aConf['default_lang']);
	if(defined('PLX_MYMULTILINGUE')) {
		$langs = plxMyMultiLingue::_Langs();
		$multiLangs = empty($langs) ? array() : explode(',', $langs);
		$aLangs = $multiLangs;
	}	
	$langs = array();
	foreach($aLangs as $lang) {
		# chargement de chaque fichier de langue
		$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.basename(__DIR__).'/lang/'.$lang.'.php');
		$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName_'.$lang);
	}	
	

	# nettoyage des vieux zips
	$plxPlugin->clearOldFiles(PLX_PLUGINS.basename(__DIR__).'/bowls/*.zip','1');	
	
	if(isset($_POST['title'])){
		# message
		$row ='<p id="com_message" class="#com_class"><strong>#com_message</strong></p>'; 		

		if($_SESSION['capcha'] == sha1($_POST['rep'])) {
			# injection infos
			include(PLX_PLUGINS.basename(__DIR__).'/cooker/rawStuff.php');			
			include(PLX_PLUGINS.basename(__DIR__).'/cooker/recipe.php');	
			$color = 'green';
			$_SESSION['msgcom'] = sprintf($plxPlugin->getLang('L_DOWNLOAD_ZIP'), $plxPlugin->zip.$plxPlugin->className.'.zip') ;
			
		}
		else {
			$_SESSION['msgcom'] =  L_NEWCOMMENT_ERR_ANTISPAM;
		}

		header("Location: " . "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		die;
		
	}
	
	if (!empty($_SESSION['msgcom'])) {	
		# message
		$row ='<p id="com_message" class="#com_class"><strong>#com_message</strong></p>'; 		
		$message=$_SESSION['msgcom'];
		if($message !=L_NEWCOMMENT_ERR_ANTISPAM) $color ='green';
			$row = str_replace('#com_class', 'alert ' . $color, $row);
			unset($_SESSION['msgcom']);
		#maj message
		$row = str_replace('#com_message',$message , $row);
		echo $row;
		}
?>
<div id="wiz"  class="wizard">
	<div class="container">
		<div class='title-wizard'>
			<h2>Stir it Up!<br><?= $plxPlugin->getParam('mnuName_'.$lang) ?></h2>
			<img src="/plugins/theCrock/icon.png">
			<div id="tab-status">
				<span class="tab active">1</span>
			</div>	
		</div>
		<form method="post"  enctype="multipart/form-data" >
			<div role="tab-list">
				<div id="welcome" role="tabpanel" class="tabpanel">
				<p>Version de test, Si vous avez des défauts, bogues ou critique ou voulez obtenir le script (plugin : theCrock)) ... 
					Venez en parler sur le <a href="https://forum.pluxml.org"target="_blank">Forum du CMS PluXml</a>.</p>
					<h3><?= $plxPlugin->lang('L_TOOL_USE') ?></h3>
					<p><?= $plxPlugin->lang('L_TROUGH_TOOL_USE') ?></p>
					<p><?= $plxPlugin->lang('L_TOOL_COMPONENTS') ?></p>
					<ul style="margin-bottom:0">
						<li><?= $plxPlugin->lang('L_PLUG_FOLDERS') ?></li>
						<li><?= $plxPlugin->lang('L_PLUG_ADMIN_PAGE') ?></li>
						<li><?= $plxPlugin->lang('L_PLUG_PARAMS') ?></li>
						<li><?= $plxPlugin->lang('L_PLUG_FRONT') ?></li>
						<li><?= $plxPlugin->lang('L_PLUG_WIZARD') ?></li>
						<li><?= $plxPlugin->lang('L_PLUG_HOOKS') ?></li>
					</ul>
					<p class="tip"><?= $plxPlugin->lang('L_PLUG_DISCL') ?></p>
					<p><?= $plxPlugin->lang('L_PLUGYBOX') ?></p>
					<p style="text-align:end;font-weight:bold;padding-inline-end:1em;">
						<?= $plxPlugin->lang('L_CLICK_START') ?>
						<q style="color:#06B6D4"><?= L_PAGINATION_NEXT_TITLE ?></q>
						<br>
						<cite>G-Cyrillus</cite>
					</p>
					<div>
					<small style="line-height:1.1;display:block;"><?= $plxPlugin->lang('L_SIMILAR_TOOLS') ?></small>
					<sub style="font-size:0.7rem;margin-top:1em;">RIP Shane MacGowan</sub>
					</div>					
					<input type="hidden"  class="form-input" value="keepGoing"/>
				</div>				
				<fieldset id="xmlDocument"   role="tabpanel" class="tabpanel hidden">
					<legend><?= $plxPlugin->lang('L_PLUG_ID') ?></legend>
					<p><label for="title"><?= $plxPlugin->lang('L_PLUG_NAME') ?></label> <input type="text" name="title" id="title" required  class="form-input"></p>
					<p><label for="author"><?= $plxPlugin->lang('L_PLUG_AUTHOR') ?></label> <input type="text" name="author" id="author"></p>
					<p><label for="version"><?= $plxPlugin->lang('L_PLUG_VERSION') ?></label> <input type="text" name="version" id="version"></p>
					<p><label for="date"><?= $plxPlugin->lang('L_PLUG_DATE') ?></label> <input type="date" name="date" id="date" required  class="form-input"></p>
					<p><label for="site"><?= $plxPlugin->lang('L_PLUG_SITE') ?></label> <input type="text" name="site" id="site"></p>
					<label for="description"><?= $plxPlugin->lang('L_PLUG_DESCRIPTION') ?></label>
					<textarea name="description" id="description" required  class="form-input"></textarea>
					<label for="scope">Scope</label>
					<select name="scope" id="scope"> 
						<option value selected>Front & Back end</option>
						<option value="site">Front-end</option>
						<option value="admin">Back-end</option>
					</select>
					<label for="icone"><?= $plxPlugin->lang('L_PLUG_ICON') ?></label> <input type="file" name="icone" id="icone"  accept="image/jpeg, image/gif, image/png">
				</fieldset>
				<fieldset id="options"   role="tabpanel" class="tabpanel hidden">
					<legend><?= $plxPlugin->lang('L_PLUG_OPTIONS') ?></legend>
					<fieldset>
					<p><label for="multilang"><?= $plxPlugin->lang('L_POLYLINGUAL') ?></label> <input type="checkbox" name="multilang" id="multilang" checked  disabled></p>
					<p><label for="config"><?= $plxPlugin->lang('L_PLUG_CONFIG_PAGE') ?></label> <input type="checkbox" name="config" id="config" checked></p>
					<p><label for="admin"><?= $plxPlugin->lang('L_PLUG_ADMIN_PAGE') ?></label> <input type="checkbox" name="admin" id="admin"></p>
					</fieldset>
					<fieldset>
					<p><label for="static"><?= $plxPlugin->lang('L_PLUG_STATIC_PAGE') ?></label><input type="checkbox" name="static" id="static"></p>
					<p><label for="addWidget"><?= $plxPlugin->lang('L_PLUG_WIDGET') ?></label><input type="checkbox" name="addWidget" id="addWidget"></p>
					<p><label for="wizard"><?= $plxPlugin->lang('L_PLUG_WIZARD_PAGE') ?></label> <input type="checkbox" name="wizard" id="wizard"></p>
					<input type="hidden"  class="form-input" value="keepGoing">
					</fieldset>
				</fieldset>				
				<fieldset id="parametres"   role="tabpanel" class="tabpanel hidden">
					<legend><?= $plxPlugin->lang('L_PLUG_ADD_PARAMS') ?></legend>
					<p><span><input type="checkbox" name="string" id="string" > <sup><?= $plxPlugin->lang('L_PLUG_ADD_PARAMS_EXAMPLE') ?></sup></span><label for="string">Type <b>string</b></label>
						<label for="addstring">Ajouter</label><input type="text" name="addstring" id="addstring" placeholder="string1 , string2">
					</p>
					<p><span><input type="checkbox" name="cdata" id="cdata" > <sup><?= $plxPlugin->lang('L_PLUG_ADD_PARAMS_EXAMPLE') ?></sup></span><label for="cdata">Type <b>cdata</b></label>
						<label for="addcdata">Ajouter</label><input type="text" name="addcdata" id="addcdata"  placeholder="cdata3 , cdata4">
					</p>
					<p><span><input type="checkbox" name="numeric" id="numeric"> <sup><?= $plxPlugin->lang('L_PLUG_ADD_PARAMS_EXAMPLE') ?></sup></span><label for="numeric">type <b>numeric</b></label>
						<label for="addnumeric">Ajouter</label><input type="text" name="addnumeric" id="addnumeric"  placeholder="num5 , num6">
					</p>
					<p style="display:block;grid-column:1/-1;background:lightyellow;margin:0"><small><i><?= $plxPlugin->lang('L_PLUG_ADD_PARAMS_NOTICE') ?></i></small></p>
					<input type="hidden"  class="form-input" value="keepGoing">
				</fieldset>
				<fieldset id="hooks" data-1col   role="tabpanel" class="tabpanel hidden">
					<legend><?= $plxPlugin->lang('L_HOME_MADE_HOOKS') ?></legend>
					<p style="display:block;grid-column:1/-1;background:lightyellow;margin:0"><?= $plxPlugin->lang('L_HOME_MADE_HOOKS_NOTICE') ?></p>
					<fieldset>
						<legend>Inserer vos Hooks ou fonctions specifiques.</legend>
						<p>
							<label for="hookCustom">hook(s) maison</label>
							<input type="text" name="hookCustom" id="hookCustom" placeholder="hook1,hook2">
						</p> 
						<p style="display:block;grid-column:1/-1;background:lightyellow;margin:0">
						<small><i>Un hook est toujours doublé d'une fonction du même nom</i> Il peut-être alors inserer dans un thème ou tout autre partie de PluXml d'où il appelera sa fonction.</small>
						</p>
						<p>
							<label for="functionCustom">fonction(s) maison</label>
							<input type="text" name="functionCustom" id="functionCustom" placeholder="fonction1,fonction2">
						</p> 
						<p style="display:block;grid-column:1/-1;background:lightyellow;margin:0">
						<small><i>Une fonction seule sert généralement au fonctionement interne du plugin lorsque une fonctionnalité n'existe pas dans PluXml.</i></small></p>
						<input type="hidden"  class="form-input" value="keepGoing">
					</fieldset>
				</fieldset>
				<fieldset id="nativeHooks" data-1col role="tabpanel" class="tabpanel hidden">
					<legend><?= $plxPlugin->lang('L_SELECT_NATIVE_HOOKS') ?></legend>
					<div style="background:#A4BC3C;padding:0.25em;"><b><?= $plxPlugin->lang('L_SELECT_HOOKS_LIST') ?></b> <a href="https://wiki.pluxml.org/docs/develop/plugins/hooks.html#" style="color:#EAF378" target="_blank">Documentation hooks <big>☞</big></a></div>
					<fieldset>
						<legend>/core/admin/article.php</legend>
						<label for="adminArticleParam">AdminArticle</label>
						<select name="adminArticleParam[]" id="adminArticle" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminArticleContent</option>
							<option>AdminArticleFoot</option>
							<option>AdminArticleInitData</option>
							<option>AdminArticleParseData</option>
							<option>AdminArticlePostData</option>
							<option>AdminArticlePrepend</option>
							<option>AdminArticlePreview</option>
							<option>AdminArticleSidebar</option>
							<option>AdminArticleTop</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/auth.php</legend>
						<label for="adminAuthParam">adminAuth</label>
						<select name="adminAuthParam[]" id="adminAuth" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminAuthPrepend</option>
							<option>AdminAuthEndHead</option>
							<option>AdminAuthTop</option>
							<option>AdminAuth</option>
							<option>AdminAuthEndBody</option>
							<option>AdminAuthBegin</option>
							<option>AdminAuthTopLostPassword</option>
							<option>AdminAuthLostPassword</option>
							<option>AdminAuthTopChangePassword</option>
							<option>AdminAuthChangePassword</option>
							<option>AdminAuthTopChangePasswordError</option>
							<option>AdminAuthChangePasswordError</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/categorie.php</legend>
						<label for="adminCategoryParam">AdminArticle</label>
						<select name="adminCategoryParam[]" id="adminCategory" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminCategoryPrepend</option>
							<option>AdminCategoryTop</option>
							<option>AdminCategory</option>
							<option>AdminCategoryFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/categories.php</legend>
						<label for="adminCategoriesParam">adminCategories</label>
						<select name="adminCategoriesParam[]" id="adminCategories" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminCategoriesPrepend</option>
							<option>AdminCategoriesTop</option>
							<option>AdminCategoriesFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/comment.php</legend>
						<label for="adminCommentParam">adminComment</label>
						<select name="adminCommentParam[]" id="adminComment" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminCommentPrepend</option>
							<option>AdminCommentTop</option>
							<option>AdminComment</option>
							<option>AdminCommentFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/comments.php</legend>
						<label for="adminCommentsParam">adminComments</label>
						<select name="adminCommentsParam[]" id="adminComments" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminCommentsPrepend</option>
							<option>AdminCommentsTop</option>
							<option>AdminCommentsPagination</option>
							<option>AdminCommentsFoot</option>
							<option>AdminCommentNewPrepend</option>
							<option>AdminCommentNewTop</option>
							<option>AdminCommentNew</option>
							<option>AdminCommentNewList</option>
							<option>AdminCommentNewFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/foot.php</legend>
						<label for="adminFootParam">adminComment</label>
						<select name="adminFootParam" id="adminFoot">
							<option value><?= $plxPlugin->lang('L_CHOICE') ?></option>
							<option>AdminFootEndBody</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/index.php</legend>
						<label for="adminIndexParam">adminIndex</label>
						<select name="adminIndexParam[]" id="adminIndex" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminIndexPrepend</option>
							<option>AdminIndexTop</option>
							<option>AdminIndexPagination</option>
							<option>AdminIndexFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/medias.php</legend>
						<label for="adminMediasParam">adminMedias</label>
						<select name="adminMediasParam[]" id="adminMedias" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminMediasPrepend</option>
							<option>AdminMediasTop</option>
							<option>AdminMediasFoot</option>
							<option>AdminMediasUpload</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_affichage.php</legend>
						<label for="adminSettingsDisplayParam">adminSettingsDisplay</label>
						<select name="adminSettingsDisplayParam[]" id="adminSettingsDisplay" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminSettingsDisplayTop</option>
							<option>AdminSettingsDisplay</option>
							<option>AdminSettingsDisplayFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_avances.php</legend>
						<label for="adminSettingsAdvancedParam">adminSettingsAdvanced</label>
						<select name="adminSettingsAdvancedParam[]" id="adminSettingsAdvanced" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminSettingsAdvancedTop</option>
							<option>AdminSettingsAdvanced</option>
							<option>AdminSettingsAdvancedFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_base.php</legend>
						<label for="adminSettingsBaseParam">adminSettingsBase</label>
						<select name="adminSettingsBaseParam[]" id="adminSettingsBase" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminSettingsBaseTop</option>
							<option>AdminSettingsBase</option>
							<option>AdminSettingsBaseFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_edittpl.php</legend>
						<label for="adminSettingsEdittplParam">adminSettingsEdittpl</label>
						<select name="adminSettingsEdittplParam[]" id="adminSettingsEdittpl" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminSettingsEdittplTop</option>
							<option>AdminSettingsEdittpl</option>
							<option>AdminSettingsEdittplFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_infos.php</legend>
						<label for="adminSettingsInfosParam">adminSettingsInfos</label>
						<select name="adminSettingsInfosParam" id="adminSettingsInfos">
							<option value><?= $plxPlugin->lang('L_CHOICE') ?></option>
							<option>AdminSettingsInfos</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_plugins.php</legend>
						<label for="adminSettingsPluginsParam">adminSettingsPlugins</label>
						<select name="adminSettingsPluginsParam[]" id="adminSettingsPlugins" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminSettingsPluginsTop</option>
							<option>AdminSettingsPluginsFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_themes.php</legend>
						<label for="adminThemesDisplayParam">adminThemesDisplay</label>
						<select name="adminThemesDisplayParam[]" id="adminThemesDisplay" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminThemesDisplay</option>
							<option>AdminThemesDisplayFoot</option>
							<option>AdminThemesDisplayTop</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/parametres_users.php</legend>
						<label for="adminSettingsUsersParam">adminSettingsUsers</label>
						<select name="adminSettingsUsersParam[]" id="adminSettingsUsers" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminSettingsUsersTop</option>
							<option>AdminSettingsUsersFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/prepend.php</legend>
						<label for="adminPrependParam">adminPrepend</label>
						<select name="adminPrependParam" id="adminPrepend">
							<option value><?= $plxPlugin->lang('L_CHOICE') ?></option>
							<option>AdminPrepend</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/profil.php</legend>
						<label for="adminProfilParam">adminProfil</label>
						<select name="adminProfilParam[]" id="adminProfil" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminProfilPrepend</option>
							<option>AdminProfilTop</option>
							<option>AdminProfil</option>
							<option>AdminProfilFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/statique.php</legend>
						<label for="adminStaticParam">adminStatic</label>
						<select name="adminStaticParam[]" id="adminStatic" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminStaticPrepend</option>
							<option>AdminStaticTop</option>
							<option>AdminStatic</option>
							<option>AdminStaticFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/statiques.php</legend>
						<label for="adminStaticsParam">adminStatics</label>
						<select name="adminStaticsParam[]" id="adminStatics" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminStaticsPrepend</option>
							<option>AdminStaticsTop</option>
							<option>AdminStaticsFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/top.php</legend>
						<label for="adminTopParam">adminTop</label>
						<select name="adminTopParam[]" id="adminTop" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminTopEndHead</option>
							<option>AdminTopMenus</option>
							<option disabled>AdminTopBottom</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/admin/user.php</legend>
						<label for="adminUserParam">adminUser</label>
						<select name="adminUserParam[]" id="adminUser" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>AdminUserPrepend</option>
							<option>AdminUserTop</option>
							<option>AdminUser</option>
							<option>AdminUserFoot</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/lib/class.plx.admin.php</legend>
						<label for="plxAdminParam" class>class plxAdmin</label>
						<select name="plxAdminParam[]" id="plxAdmin" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>plxAdminConstruct</option>
							<option>plxAdminEditConfiguration</option>
							<option>plxAdminHtaccess</option>
							<option>plxAdminEditProfil *</option>
							<option>plxAdminEditProfilXml</option>
							<option>plxAdminEditUsersUpdate</option>
							<option>plxAdminEditUsersXml</option>
							<option>plxAdminEditUser</option>
							<option>plxAdminEditCategoriesNew</option>
							<option>plxAdminEditCategoriesUpdate</option>
							<option>plxAdminEditCategoriesXml</option>
							<option>plxAdminEditCategorie</option>
							<option>plxAdminEditStatiquesUpdate</option>
							<option>plxAdminEditStatiquesXml</option>
							<option>plxAdminEditStatique</option>
							<option>plxAdminEditArticle *</option>
							<option>plxAdminEditArticleXml</option>
							<option>plxAdminDelArticle *</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/lib/class.plxfeed.php</legend>
						<label for="plxFeedParam" class>class plxFeed</label>
						<select name="plxFeedParam[]" id="plxFeed" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>plxFeedConstruct</option>
							<option>plxFeedPreChauffageBegin *</option>
							<option>plxFeedPreChauffageEnd</option>
							<option>plxFeedDemarrageBegin *</option>
							<option>plxFeedDemarrageEnd</option>
							<option>plxFeedRssArticlesXml</option>
							<option>plxFeedRssCommentsXml</option>
							<option>plxFeedAdminCommentsXml</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/lib/class.plx.motor.php</legend>
						<label for="plxMotorParam" class>class plxMotor</label>
						<select name="plxMotorParam[]" id="plxMotor" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>plxMotorConstruct</option>
							<option>plxMotorPreChauffageBegin *</option>
							<option>plxMotorPreChauffageEnd</option>
							<option>plxMotorDemarrageBegin *</option>
							<option>plxMotorDemarrageEnd</option>
							<option>plxMotorDemarrageNewCommentaire</option>
							<option>plxMotorDemarrageCommentSessionMessage</option>
							<option>plxMotorGetCategories</option>
							<option>plxMotorGetStatiques</option>
							<option>plxMotorGetUsers</option>
							<option>plxMotorParseArticle</option>
							<option>plxMotorParseCommentaire</option>
							<option>plxMotorRedir301</option>
							<option>plxMotorNewCommentaire *</option>
							<option>plxMotorAddCommentaire *</option>
							<option>plxMotorAddCommentaireXml</option>
							<option>plxMotorSendDownload *</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/core/lib/class.plx.show.php</legend>
						<label for="plxShowParam" class>class plxShow</label>
						<select name="plxShowParam[]" id="plxShow" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>plxShowConstruct</option>
							<option>plxShowPageTitle *</option>
							<option>plxShowMeta *</option>
							<option>plxShowLastCatList *</option>
							<option>plxShowArtTags *</option>
							<option>plxShowArtFeed *</option>
							<option>plxShowLastArtList *</option>
							<option>plxShowComFeed *</option>
							<option>plxShowLastComList *</option>
							<option>plxShowStaticListBegin *</option>
							<option>plxShowStaticListEnd *</option>
							<option>plxShowStaticContentBegin*</option>
							<option>plxShowStaticContent</option>
							<option>plxShowStaticInclude *</option>
							<option>plxShowPagination *</option>
							<option>plxShowTagList *</option>
							<option>plxShowArchList *plxShowPageBlog *</option>
							<option>plxShowTagFeed *</option>
							<option>plxShowTemplateCss *</option>
							<option>plxShowCapchaQ *</option>
							<option>plxShowCapchaR *</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/index.php</legend>
						<label for="IndexParam">Fichier Index</label>
						<select name="IndexParam[]" id="Index" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>Index</option>
							<option>IndexBegin</option>
							<option>IndexEnd</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/sitemap.php</legend>
						<label for="SitemapParam">Sitemap</label>
						<select name="SitemapParam[]" id="Sitemap" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>SitemapStatics</option>
							<option>SitemapCategories</option>
							<option>SitemapArticles</option>
							<option>SitemapBegin</option>
							<option>SitemapEnd</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>/feed.php</legend>
						<label for="FeedParam">Feed</label>
						<select name="FeedParam[]" id="Feed" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option>FeedBegin</option>
							<option>FeedEnd</option>
						</select>
					</fieldset>
					<fieldset>
						<legend>Hooks des thèmes</legend>
						<label for="ThemeParam">Theme</label>
						<select name="ThemeParam[]" id="Theme" multiple>
							<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
							<option disabled>ThemeEndHead</option>
							<option>ThemeEndBody</option>
						</select>
						
					</fieldset>
					<input type="hidden"  class="form-input" value="keepGoing">
				</fieldset>
				<fieldset id="recap"  role="tabpanel" class="tabpanel hidden">
					<legend>Download</legend>
					<p style="display:block;grid-column:1/-1;text-align:center;">
						<label for="rep"><strong> <?php echo $plxPlugin->lang('ANTISPAM_DOWNLOAD') ?></strong> *</label>
						<?php
							$this->plxMotor->plxCapcha = new plxCapcha(); # Création objet captcha
							$this->capchaQ(); 
						?>
						<input type="text"  name="rep" id="id_rep"  size=2 style="width:auto;text-align:center" required class="form-imput">
					</p>
					<h3 style="grid-column:1/-1;font-size: 1.7em;font-weight: bold;font-variant: all-small-caps;"><?= $plxPlugin->lang('L_PLUG_STRUCTURE') ?></h3>
					<fieldset id="folderNfile">
					
					</fieldset>
					<fieldset id="subfolderNfile">
					
					</fieldset>

				</fieldset>
				
				<div class="pagination">
					<a class="btn hidden" id="prev"><?= L_PAGINATION_PREVIOUS_TITLE ?></a>
					<a class="btn" id="next"><?= L_PAGINATION_NEXT_TITLE ?></a>
					<button class="btn btn-submit hidden" id="submitForm"><?= $plxPlugin->lang('L_MAKE_IT') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	const LANG ="<?php echo $lang ?>";
	const SUBFOLDER="<?= $plxPlugin->lang('L_SUB_OF') ?>";
	</script>
<script src="<?= PLX_PLUGINS.basename(__DIR__) ?>/js/static.js"></script>
