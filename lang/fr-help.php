/* Pense-bete:
 * Récuperer des parametres du fichier parameters.xml
 *	$this->getParam("<nom du parametre>")
 *	$this-> setParam ("param1", 12345, "numeric")
 *	$this->saveParams()
 *
 *	plxUtils::strCheck($string) : sanitize string
 *
 * 
 * Quelques constantes utiles: 
 * PLX_CORE
 * PLX_ROOT
 * PLX_CHARSET
 * PLX_PLUGINS
 * PLX_CONFIG_PATH
 * PLX_ADMIN (true si on est dans admin)
 * PLX_CHARSET
 * PLX_VERSION
 * PLX_FEED
 *
 * Appel de HOOK dans un thème
 *	eval($plxShow->callHook("ThemeEndHead","param1"))  ou eval($plxShow->callHook("ThemeEndHead",array("param1","param2")))
 *	ou $retour=$plxShow->callHook("ThemeEndHead","param1"));
 */