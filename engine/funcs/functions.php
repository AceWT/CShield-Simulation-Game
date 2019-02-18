<?php

function az09($str)
{
	return preg_replace("/[^a-zA-Z0-9]+/", "", $str);
}

function redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

/**
 * toCommandText()
 * Console command text sanitize.
 * @param mixed $str
 * @return
 */
function toCommandText($str)
{
	$r = trim(preg_replace("/[^a-zA-Z0-9 -\/.]+/", "", $str));
    $r = preg_replace("/\.{2,}/", ".", $r); //remove multiple dots so users cant hack directory.
    return $r;
}

function to09($str)
{
    $r = preg_replace("/[^0-9]+/", "", $str);
    return ($r == "") ? '' : (int)preg_replace("/[^0-9]+/", "", $str);
}
function toCroAZ($e)
{
	return trim(preg_replace("/[^a-zA-Z0-9čćšđžČĆŠĐŽ \/_\-:\.\,!\?\(\)@=&]+/u", "", $e));
}
/**
 * toCroAZWithHTMLNewLines()
 * \r\n will be transformed to <br /> via nl2br() php function.
 * @param mixed $e
 * @return
 */
function toCroAZWithHTMLNewLines($e)
{
    return trim(preg_replace("/[^a-zA-Z0-9čćšđžČĆŠĐŽ \/_\-:\.\,!\?\(\)@\<\\/\>]+/u", "", nl2br($e)));
}
/**
 * toCroAZWithNewLines()
 * \n\r allowed.
 * @param mixed $e
 * @return
 */
function toCroAZWithNewLines($e)
{
    return trim(preg_replace("/[^a-zA-Z0-9čćšđžČĆŠĐŽ \/_\-:\.\,!\?\(\)@\r\n]+/s", "", $e));
}

/**
 * toAlphanumeric()
 * For titles, letters numbers ,. ...
 * @param mixed $e
 * @return
 */
function toAlphanumeric($e)
{
	return trim(preg_replace("/[^a-zA-Z0-9 \/_\-:\.\,!\?\(\)]+/u", "", $e));
}

/**
 * toSafeString()
 * SQL safe string, also used for alphanumeric titles in tax.
 * @param mixed $e
 * @return
 */
function toSafeString($e)
{
	return trim(preg_replace("/[^a-zA-Z0-9 \/_\-:\.\,!\?\(\)]+/u", "", $e));
}


/**
 * toDecimal()
 * Formatira string kao decimalni broj, vrati 0 pri neispravnom unosu.
 * Allows negative.
 * @param mixed $num
 * @return 0 on invalid string or decimal number with . (dot)
 */
function toDecimal($num)
{
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

    if (!$sep) {
        return floatval(preg_replace("/[^0-9-]/", "", $num));
    }

    return
        preg_replace("/[^0-9-]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)));

}
function slugify($text, $replacement = '_')
{
    $cro        = array('Č', 'č', 'Ć', 'ć', 'Ž', 'ž', 'Š', 'š', 'Đ', 'đ');
    $urlsafe    = array('C', 'c', 'C', 'c', 'Z', 'z', 'S', 's', 'D', 'd');
    $text = str_replace($cro, $urlsafe, $text);
    return strtolower(trim(preg_replace('/\W+/', $replacement, $text), '-'));
}
/**
 * isWebsiteOwner()
 *
 * @return bool
 * @description If is logged in admin of this website then return true.
 */
function isWebsiteOwner()
{
	global $user,$web;
    return (isset($user) && $user->logged_in && $user->userinfo['id'] == $web->userid) ? true:false;
}

/**
 * isLoggedIn()
 *
 * @return bool
 * @description If is logged (not used yet)
 */
function isLoggedIn()
{
	global $user,$web;
    return (isset($user) && $user->logged_in) ? true:false;
}

/**
 * isAdmin()
 * Is global admin, is CroWebs owner.
 * @return
 */
function isAdmin($userid = false)
{
    global $config,$web,$user;
    if (!$userid)
        return (isset($user) && $user->logged_in && in_array($user->userinfo['id'],$config['globalAdminIds'])) ? true : false;
    else
        return (in_array($userid,$config['globalAdminIds'])) ? true : false;
}
/**
 * credit()
 *
 * @return credit link crowebs
 */
function credit()
{
    global $config,$web;
    return '<span class="copyright"><span class="ca">Copyright &copy; '.date('Y').' '.$web->website_profile['title'].'<span> <span class="cb">'.t('Web izrada').': <a target="_blank" href="http://www.'.$config['main_domain'].'/">'.$config['company_name'].'</a></span></span>';
}
/**
 * Javascript varijabla koja je postavljena u headeru a povlaci je nicedit kad treba dodati
 * link na text tab - pages (za dropdown meni)
 * TODO: KESIRATI! ovo se svaki page load ucitava
 */
function AxeJS_AxePages()
{
    global $web,$db;
    $path = PATHROOT."html/pages/";
    $ret = '"/":"Naslovna",';

    $fixedPages = list_fixed_pages(true);
    foreach($fixedPages as $pagefilename => $pagename)
    {
        $djelovi = explode("_",substr($pagefilename,6));



        if ($pagefilename != 'fixed_index')//skip naslovna
        {
            $ret .= "\"";$ime = "";
            foreach($djelovi as $d)
            {
                 $ret .= "/".$d;
                 if ($d != 'index')
                 $ime .= ucfirst($d)." &#10097; ";
            }
            $ret .= ".html\": \"".substr(trim($ime),0,-9)."\",";
        }


    }
    //get custom pages
    $custompages = list_custom_pages();
    foreach($custompages as $cp)
    {
        //$index = PageSubpageToFixedFilename($cp['page'],$cp['subpage']);
        $ret .= '"/'.$cp['page'].'/'.$cp['subpage'].'.htm":"['.$cp['Naziv'].']",';
    }

    return "{".$ret."}";
}
function CreateCurrentUrl($page,$subpage,$isCustom)
{
    global $web;
    if ($page == 'index') return $web->langpart.'/';
        return  $web->langpart.'/'.$page.'/'.$subpage.'.'.(($isCustom == 1 || $isCustom) ? 'htm':'html');
}
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}
function contains($haystack, $needle)
{
    return (strpos($haystack, $needle) !== FALSE) ? true:false;
}

/**
 * isValidEmail()
 * Check email
 * @param mixed $email
 * @return bool
 */
function isValidEmail($email)
{
    if(!$email || strlen($email = trim($email)) == 0)
        return false;
    elseif(!preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])*(\.([a-z0-9])([-a-z0-9_-])+)*$/i', $email))
        return false;
    else
        return true;
}
//used in tplmenu_class.php
//lists only public pages, dont list private pages like ucp links etc. defined in config.php
/**
 * list_fixed_pages()
 *
 * @param bool blocksystempages
 * @return array of fixed pages <filename> => <page name from config>
 * @example array('fixed_index' => 'Naslovna stranica', ...) ili array('fixed_onama_kontakt' => 'O nama - Kontakt', ...)
 */
function list_fixed_pages($blocksystempages = false,$blockDevPages = true)
{
    global $config,$error,$web;
    $files = scandir(PATHROOT.'html/pages/');
    //var_dump($files);
    $ra = array();
    foreach ($files as $file)
    {
        if (endsWith($file,'.php') && startsWith($file,'fixed_'))
        {
            //remove .php
            $filename = substr($file, 0, -4); // remove .php

            $options = FixedPageOptions($filename);


            if (!isset($config['pages_names'][$filename]))
                $error->Report("Missing pages_names for '".$file."' in config");
            else if ($config['pages_names'][$filename][1] == 0 && $blocksystempages)
            {
                //check if page is in production mode, and if isAdmin() == true allow access:
                if (($config['pages_names'][$filename][2] == 1 || isAdmin($web->userid)) || !$blockDevPages)
                    $ra[$filename] = array( 'title' => $config['pages_names'][$filename][0],
                                            'isPublic' => $options['isPublic'],
                                            'isDev' => ($config['pages_names'][$filename][2] == 0)
                                            );
            }
            else if (!$blocksystempages)
            {
                //check if page is in production mode, and if isAdmin() == true allow access:
                if (($config['pages_names'][$filename][2] == 1 || isAdmin($web->userid)) || !$blockDevPages)
                    $ra[$filename] = array( 'title' => $config['pages_names'][$filename][0],
                                            'isPublic' => $options['isPublic'],
                                            'isDev' => ($config['pages_names'][$filename][2] == 0)
                                            );
            }

        }
    }
    return $ra;
}

function list_custom_pages($blockinactivepages = false)
{
    global $db,$web;

    if ($blockinactivepages)
        $where = array(
                "accountID" => $web->userid,
                "isPublic" => 1,
            );
    else
        $where = array(
                "accountID" => $web->userid
            );

    $custompages = $db->select("website_pages",array('id','Naziv','page','subpage'),array(
            "AND" => $where,
            "ORDER" => array('Naziv' => 'ASC')
        ));
    return $custompages;
}

//cached, todo clear this cache name
function getCustomPageNaziv($page,$subpage)
{
	global $config,$web;
	$cachename = 'getCustomPageNaziv_'.md5($web->userid.$page.$subpage);
	$naziv = phpFastCache::get($cachename);
    if ($naziv == null) {

		global $web,$db;
		$getCustomPage = $db->query("CALL GetCustomPage(".$web->userid.",'".$page."','".$subpage."');")->fetchAll();
		if ( count($getCustomPage) == 1 )
		{
			$naziv = $getCustomPage[0]['menu_Naziv'];
		}
		else
			$naziv = '';
		/*$naziv = $db->get("website_pages",'Naziv',array(
				"AND" => array(
					"accountID" => $web->userid,
					"isPublic" => 1,
					'page' => $page,
					'subpage' => $subpage
				)
			));
		if (!$naziv) $naziv = '';*/
		phpFastCache::set($cachename, $naziv, $config['cache_seconds']);
	}
	return $naziv;
}

/**
 * PageSubpageToURL()
 *
 * @param mixed $page
 * @param mixed $subpage
 * @return string - generated relative url of this combination
 * @example /onama/kontakt.html ili /auti/index.html
 */
function PageSubpageToURL($page,$subpage,$isCustom)
{
    $suffix = ($isCustom) ? '.htm' : '.html';
    if ($page == '') return false;

    if ($page == 'index') return '/';

    $sp = ($subpage == '') ? '/index'.$suffix : '/'.$subpage.$suffix;
    $url = '/'.$page.$sp;
    return $url;
}

function PageSubpageToFixedFilename($page,$subpage,$issyspage = false)
{

    $page = (!$page || $page == '') ? 'index' : $page;

    $curr_subpageq 	= explode("-",$subpage);
	$curr_subpage = ($curr_subpageq[0] == '') ? '' : '_'.$curr_subpageq[0];
	$curr_page 		= (($issyspage)?"sys":"fixed")."_".$page.$curr_subpage;
    return $curr_page;
}
function FixedFilenameToPageSubpage($fixedfilename)
{
    if ($fixedfilename == 'fixed_index') return array('index','');
/*
fixed_index Početna
fixed_onama_index O nama
fixed_onama_kontakt O nama - Kontakt
fixed_shop_index Proizvodi i usluge
*/
    $filenameex = explode("_",$fixedfilename);
    return array($filenameex[1],$filenameex[2]);
}

function list_templates($template)
{
    if (!$template || $template == '')
    {
        $error->Report("Var template is not defined in function 'list_templates'");
        return;
    }
    $tpltemplatesdir = PATHROOT.'html/tpl/'.$template.'/templates/';
    if (!is_dir($tpltemplatesdir))
    {
        $error->Report("Template folder '".$tpltemplatesdir."' does not exist");
        return;
    }

    $files = scandir($tpltemplatesdir);
    $ra = array();
    foreach ($files as $file)
    {
        if (endsWith($file,'.php') && !startsWith($file,'_') && $file != 'blank')
        {
            //remove .php
            $filename = substr($file, 0, -8); // remove .tpl.php
            array_push($ra,$filename);

        }

    }
    return $ra;
}

function toMysqlDate($unixTimestamp) {
    return date("Y-m-d H:i:s", $unixTimestamp);
}
function toMysqlDateOnly($unixTimestamp) {
    return date("Y-m-d", $unixTimestamp);
}
function mysqlDateTimeToUnix($mysqldatetime)
{
    return strtotime($mysqldatetime);
}
/**
 * dateToHumanReadable()
 *
 * @param mixed $variousDateFormat
 * @param string $formatMode - full (date+time), date (date only), time (time only)
 * @return string localized formatted date
 */
function dateToHumanReadable($variousDateFormat, $formatMode = 'full')
{
    try {
        $date = new DateTime($variousDateFormat);
    } catch(Exception $e) {
        return t('-Neispravno vrijeme/datum-');
    }
    global $contentTranslation;
    if (!isset($contentTranslation))
    {
        global $error;
        $error->Report('Global function dateToHumanReadable() called before $contentTranslation was initilazed!');
        return $date->format('d. m. Y. H:i');
    }

    if ($formatMode == 'date')
        return $date->format($contentTranslation->current_localeData['phpdate_format_date']);
    elseif($formatMode == 'time')
        return $date->format($contentTranslation->current_localeData['phpdate_format_time']);
    else
        return $date->format($contentTranslation->current_localeData['phpdate_format_full']);
}

/**
 * secondsToTime()
 *
 * @param mixed $seconds
 * @param string $formatMode - long ( dana sati minuta sekundi), short (dana sati minuta), tiny (dana sati), day (dana)
 * @return
 */
function secondsToTime($seconds,$formatMode = 'long') {
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    switch ($formatMode)
    {
        case 'day':
        return $dtF->diff($dtT)->format('%a '.t('dana'));
        case 'tiny':
        return $dtF->diff($dtT)->format('%a '.t('dana').', %h '.t('sati'));
        break;
        case 'short':
        return $dtF->diff($dtT)->format('%a '.t('dana').', %h '.t('sati').', %i '.t('minuta'));
        break;
        case 'long':
        return $dtF->diff($dtT)->format('%a '.t('dana').', %h '.t('sati').', %i '.t('minuta').' and %s '.t('sekundi').'');
        break;
        default:
        return $dtF->diff($dtT)->format('%a '.t('dana').', %h '.t('sati').', %i '.t('minuta').' and %s '.t('sekundi').'');
        break;
    }

}

/**
 * truncate()
 * Creates summary of provided text string. (tags are not proccessed)
 * @param mixed $text
 * @param integer $chars
 * @return
 */
function truncate($text, $chars = 25) {
return mb_substr(str_pad($text,$chars),0,$chars) . ((strlen($text) <= $chars) ? '' : '...');
}

//FILTER RULES

//axevar1
function FilterGETPage($page)
{
    return ($page != '') ? preg_replace("/[^a-zA-Z0-9-._]+/", "", $page) : 'index';
}
/**
 * FilterGETSubpage()
 * example 'subpage' = 'subpage'; subpage-a = 'subpage'
 * @param mixed $subpage
 * @return
 */
function FilterGETSubpage($subpage)
{
    $e = explode('-',$subpage);
    return ($e[0] != '') ? preg_replace("/[^a-zA-Z0-9._]+/", "", $e[0]) : '';
}
/**
 * FilterGETSubpageParametars()
 * example 'subpage' = ''; subpage-a_1-b_2 = 'a => 1, b => 2'; subpage-b-c = '0 => b, 1 = c'
 * @param mixed $subpage
 * @return
 */
function FilterGETSubpageParametars($subpage)
{
    $efiltered = toSafeString($subpage);
    $e = explode('-',$efiltered);
    $p = array();
    foreach ($e as $k => $v)
    {
        if ($k == 0)
            continue;

        $e2 = explode("_",$v);
        if (isset($e2[1]))
        {
            if (isset($e2[2]))
            {
                $key = $e2[0];
                array_shift($e2);
                $e3 = implode("_",$e2);
                $p[$key] = $e3;
            }
            else
                $p[$e2[0]] = $e2[1];

        }
        else
            array_push($p,$v);
    }
    return $p;
}
/**
 * FilterGETIsCustom()
 *
 * @param mixed $axevarcustom
 * @return bool
 * @description If custom parametar is sent from htaccess then returns true
 * It indicates that custom page is accessed (.html, .htm for fixed page)
 */
function FilterGETIsCustom($axevarcustom)
{
    return ($axevarcustom == '1') ? true : false;
}

function create_file($path,$content = '')
{
    $myfile = fopen($path, "w", true) or $stop = true;
	if (isset($stop)) return false;
	fwrite($myfile, $content);
	fclose($myfile);
	chmod($path, 0777);
    return true;
}

/**
* Limits the string based on the character count. Preserves complete words
* so the character count may not be exactly as specified.
*
* @access   public
* @param    string
* @param    integer
* @param    string  the end character. Usually an ellipsis
* @return   string
*/
function textsummary($str, $n = 500, $end_char = '&#8230;')
{
	//before stripping all html tags, convert <br> to space
	$str = preg_replace("/<br\W*?\/>/", ' ', $str);
	$str = preg_replace("/<br>/", ' ', $str);
    $str = str_replace("&nbsp;", '', $str);
	//strip html tags
    $str = strip_tags($str);
    if (strlen($str) < $n)
    {
        return trim($str);
    }
    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

    if (strlen($str) <= $n)
    {
        return trim($str);
    }

    $out = "";
    foreach (explode(' ', trim($str)) as $val)
    {
        $out .= $val.' ';

        if (strlen($out) >= $n)
        {
            $out = trim($out);
            break;

        }
    }
    //var_dump(trim($out));
    return (strlen($out) == strlen($str)) ? trim($out) : trim($out).$end_char;
 }
/**
 * stripLinks()
 * Removes links from html. Mrs linkovi!
 * @param mixed $str
 * @return void
 */
function stripLinks($str)
{
    return preg_replace('/(?i)<a([^>]+)>(.+?)<\/a>/','\\2',$str);
}

/**
 * t()
 * Returns translated system string.
 * @param mixed $string
 * @return Localized string
 */
function t($string)
{
    global $sysTranslation;
    return $sysTranslation->t($string);
}
/**
 * tc()
 * Returns translated content string.
 * @param mixed $string
 * @return Localized string
 */
function tc($string,$indentifier,$insertunexisting=false)
{
    global $contentTranslation;
    return $contentTranslation->tc($string,$indentifier,$insertunexisting);
}

/**
 * tcUpdate()
 * Updates existing string, also adds if does not exist and clears translated string cache.
 * @param mixed $string
 * @param mixed $indentifier
 * @return
 */
function tcUpdate($string,$indentifier)
{
    global $contentTranslation;
    return $contentTranslation->updateString($string,$indentifier);
}

function LongToShort($str)
{
    return substr(md5($str),-8);
}

/**
 * PageCPHTMLHeader()
 * Creates link on the spot and adds HEAD of modal to $script_end
 * @param bool $title
 * @param string $icon - fa icon without fa-
 * @param string $linktitle
 * @return void
 */
function PageCPHTMLHeader($title = false,$icon = 'cog', $linktitle = '', $appendHTML = '')
{
    global $script_end;
    $rand = rand(1,9999999);
    $title = (!$title) ? t('Postavke') : $title;
    $linktitle = ($linktitle == '') ? $linktitle : ' '.$linktitle;

    echo '<div class="pagecontrolpanelgear">
        <a href="#" onclick="javascript:$(\'#mpagecontrolpanel'.$rand.'\').modal();return false;" title="'.$title.'"><span class="cm-menu fa fa-'.$icon.'"></span>'.$linktitle.'</a>
        '.$appendHTML.'
    </div>';
    $script_end .= '<!--modal-->
	<div class="modal fade" tabindex="-1" role="dialog" id="mpagecontrolpanel'.$rand.'">
  <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title">'.$title.'</h4></div>
  <div class="modal-body" id="pagecontrolpanel'.$rand.'">';

    //echo '<div class="pagecontrolpanelgear"><a href="#" onclick="javascript:$(\'#pagecontrolpanel'.$rand.'\').dialog({width:\'auto\',resizable: false,modal:true});return false;"><span class="cm-menu fa fa-cog"></span>'.$linktitle.'</a></div>
    //<div id="pagecontrolpanel'.$rand.'" style="display:none" title="'.$title.'">';
}
/**
 * PageCPHTMLBody()
 * Creates BODY of modal to $script_end, mast be called after PageCPHTMLHeader
 * @param mixed $html
 * @return void
 */
function PageCPHTMLBody($html)
{
    global $script_end;
    $script_end .= $html;
}
/**
 * PageCPHTMLFooter()
 * Creates FOOTER of modal to $script_end, mast be called after PageCPHTMLBody
 * @return void
 */
function PageCPHTMLFooter()
{
    global $script_end;
    $script_end .= '</div></div></div></div><!--modalend-->';
}
/**
 * IsSubdomainTaken()
 * Check if subdomain is registered or among restricted subdomains (defined in config).
 * @param mixed $subdomain
 * @return
 */
function IsSubdomainTaken($subdomain)
{
    global $config,$db;
    $subdomain = toSafeString($subdomain);
    if (in_array($subdomain,$config['restrictedSubdomains'])) return true;

    $registered = $db->has("website_profile",array("subdomain[=]"=>$subdomain));
    //echo $db->last_query();
    //exit;
    return ($registered) ? true : false;
}
/**
 * TemplateHTMLPreview()
 *
 * @param mixed $tplarray - from TemplateInfo($templatedir)
 * @return html
 */
function TemplateHTMLPreview($templateDir)
{

    /*
    Array
    (
        [naziv] =&gt; Portfolio 1
        [opis] =&gt; Opis
        [kategorije] =&gt; Array
            (
                [0] =&gt; Dizajn
                [1] =&gt; Portfolio
                [2] =&gt;
            )

        [ispublic] =&gt; 1
        [thumburl] =&gt; /html/demoresources/tpl.jpg
    )
    */
	$info = new TemplateInfo;
    $a = $info->getOne($templateDir);

    $r = '<!-- Image card -->
<div class="templatepreview-card-image mdl-card mdl-shadow--2dp" style=" background: url(\''.$a['thumburl'].'\') center / cover;">
  <div class="mdl-card__title mdl-card--expand"></div>
  <div class="mdl-card__actions" style="background: none repeat scroll 0 0 rgba(0, 0, 0, 0.6)">
    <span class="templatepreview-card-image__filename"><a href="/templates/index-prikazi_'.$templateDir.'.phtml">'.$a['naziv'].'</a></span>
  </div>
</div>';

    return $r;
}
function getUserWebsiteUrl($nullreplacement='-')
{
    global $user,$config;
    if (!$user->logged_in)
    return $nullreplacement;
    if (!empty($user->websiteprofile['domain']))
        return 'http://'.$user->websiteprofile['domain'].'/';
    elseif (!empty($user->websiteprofile['subdomain']))
        return 'http://'.$user->websiteprofile['subdomain'].'.'.$config['main_domain'].'/';
    else
        return $nullreplacement;
}

/**
* getRepeaterImageFieldSizes()
* Include repeater in safe containerfunction and get repeaterimagefieldsizes
* @param mixed $repeaterfilename
* @param int or bool $isGeneral - true or 1 - info about repeater if its general, false or 0 - not general repeater
* @return array()
*/
function getRepeaterImageFieldSizes($repeaterfilename = false, $isGeneral = NULL)
{
    global $web;
    if (!isset($web))
    return array();
    if (!$repeaterfilename)
        return array();
    if ($isGeneral === NULL)
        die('isGeneral not defined in getRepeaterImageFieldSizes() function.');
    if ($isGeneral == 1) $isGeneral = true;
    elseif($isGeneral == 0) $isGeneral = false;

    //print_r($web->website_profile['template']);
    $repeaterfilepath = PATHROOT.'html/tpl/'.$web->website_profile['template'].'/repeaters/'.$repeaterfilename.'.php';
    if (!is_file($repeaterfilepath))
    return array();
    $repeater_unique_name='none';
    $repeater_isstandalone = true;
    $nid = NULL;
    //init repeater
    $repeater = new TplRepeater($repeater_unique_name,$repeaterfilename,$nid,$isGeneral); //dodano 05 03 2017
    //init other repeater data from repeater template and do not draw ($repeater_isstandalone = true)
    include($repeaterfilepath);

    return $repeater->GetImageSizes();
    /*if (isset($repeater->imagesizes) && is_array($repeater->imagesizes))
        return $repeater->imagesizes;
    else
        return array();*/
}

/**
 * createImageURL()
 * Creates image url from provided parameters.
 *
 * @param mixed $id
 * @param string $x
 * @param string $y
 * @param string $ext
 * @param string $alt
 * @return string /storage/images/<id>/<x>x<y>/<slugify(alt)>.<ext>
 */
function createImageURL($id,$x = '',$y = '',$ext = 'jpg',$alt = 'CD Image')
{
    $alt = (empty($alt)) ? 'CD Image '.$id : $alt;
    return '/storage/images/'.$id.'/'.$x.'x'.$y.'/'.slugify($alt).'.'.$ext;
}

/**
 * GetIP()
 * Gets IP of the current visitor. Checks for proxie.
 * @return
 */
function GetIP()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
    {
        if (array_key_exists($key, $_SERVER) === true)
        {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
            {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                {
                    return $ip;
                }
            }
        }
    }
}

function GetSysLangInfo($id)
{
    global $db;
    if (!is_numeric($id))
        return false;

    $locale = phpFastCache::get('websitelocalecachefull_id'.$id);
    if ($locale == null) {
        $locale = $db->get("system_languages",array('id','phplocale','phptimezone','currency','iso','iso2'), array("id[=]"=>$id));
        phpFastCache::set('websitelocalecachefull_id'.$id, $locale, 86400); //24hrs = 86400s
    }
    return $locale;
}
function langpart()
{
    global $web;
    return $web->langpart;
}

/**
 * HeadAddScript()
 * Can Append Script file (.js) multiple times, it will render only once.
 * @param mixed $pathToJS - path is unique indentifier
 * @return void
 */
function HeadAddScript($pathToJS)
{
    global $HeadIncludeResources;
    $HeadIncludeResources->HeadAddScript($pathToJS);
}

/**
 * HeadAddStyle()
 * Can Append CSS file (.css) multiple times, it will render only once.
 * @param mixed $pathToCSS - path is unique indentifier
 * @param string $type
 * @param string $media
 * @return void
 */
function HeadAddStyle($pathToCSS,$type = 'text/css' ,$media = 'screen')
{
    global $HeadIncludeResources;
    $HeadIncludeResources->HeadAddStyle($pathToCSS,$type,$media);
}

/**
 * HeadScriptOrStyleExists()
 * Check if file is already included in $head (css or js)
 * @param mixed $pathToFileOrLink
 * @return
 */
function HeadScriptOrStyleExists($pathToFileOrLink)
{
     global $HeadIncludeResources;
     if ($HeadIncludeResources === NULL)
        die( 'HeadIncludeResources Class is not initated in this context, are we loading this from ajax?' );
     //$HeadIncludeResources = new HeadIncludeResources;
     return $HeadIncludeResources->Exists($pathToFileOrLink);
}

/**
 * array_md5()
 * @param mixed $a
 * @return md5 hash of array
 */
function array_md5(Array $a) {
	array_multisort($a);
	return md5(json_encode($a));
}

//Deprechated: Ovo je bilo samo za test jer sam mislio da xkey samo prima brojeve ali prima i stringove.
//morao sam instalirati gmp-php (math funkcije) preko apt-get ali nije ni potreban.
/*function get64BitNumber($str)
{
    return gmp_strval(gmp_init(substr(md5($str), 0, 16), 16), 10);
}*/




/**
 * phpFastCacheKeyed()
 * Gets $cachename from phpFastCache and additionally creates/adds to cache keys.
 * @param mixed $cachename_keys
 * @param mixed $cachename
 * @return
 */
function phpFastCacheKeyed($cachename_keys,$cachename,$debug = false)
{
    global $config;
    $cache_keys = phpFastCache::get($cachename_keys);
    if ($debug) {echo '<pre>';print_r($cache_keys);echo '</pre><hr>';}
	if ($cache_keys == null) {
		$cache_keys = array();
        if ($debug) echo 'added '.$cachename.' to new keys: '.$cachename_keys.'<br><br>';
    }
    else
    {
        $cache_keys = array_unique($cache_keys);
        if ($debug) echo 'added '.$cachename.' to existing keys: '.$cachename_keys.'<br><br>';
    }
    if (!in_array($cachename,$cache_keys))
	   $cache_keys[] = $cachename; //add this cache name to key collection
	phpFastCache::set($cachename_keys, $cache_keys, $config['cache_seconds']);
	return phpFastCache::get($cachename);
}

function CDFastCacheKeyed($folder,$cachename_keys,$cachename,$debug = false)
{
    global $config;
    $cachename_keys = 'ttlfshandlerkeys_'.$cachename_keys;
    $cache_keys = phpFastCache::get($cachename_keys);
    if ($debug) {echo '<pre>';print_r($cache_keys);echo '</pre><hr>';}
	if ($cache_keys == null) {
		$cache_keys = array();
        if ($debug) echo 'added '.$cachename.' to new keys: '.$cachename_keys.'<br><br>';
    }
    else
    {
        $cache_keys = array_unique($cache_keys);
        if ($debug) echo 'added '.$cachename.' to existing keys: '.$cachename_keys.'<br><br>';
    }
    if (!in_array($cachename,$cache_keys))
	   $cache_keys[] = $cachename; //add this cache name to key collection
	phpFastCache::set($cachename_keys, $cache_keys, $config['cache_seconds']);
	return CDFastCache::get($folder,$cachename);
}

/**
 * CDFastCacheDeleteKeyed()
 * Deletes cache collection from disk and deletes key holder from memcache.
 * @param mixed $folder
 * @param mixed $cachename_keys - key holder in memcache
 * @param bool $silent
 * @return void
 */
function CDFastCacheDeleteKeyed($folder,$cachename_keys,$silent = false)
{
    $cachename_keys = 'ttlfshandlerkeys_'.$cachename_keys;
	$cache_keys = phpFastCache::get($cachename_keys);
    if ($cache_keys != null && is_array($cache_keys)) {
        if(!$silent)
        {
            echo '<pre>';
            print_r($cache_keys);
            echo '</pre>';
        }
        foreach($cache_keys as $v)
        {
            CDFastCache::delete($folder,$v); //delete from FS
        }
    }
    phpFastCache::delete($cachename_keys); //delete key holder from memcache
}

function phpFastCacheDeleteKeyed($cachename_keys,$silent = false)
{
	$cache_keys = phpFastCache::get($cachename_keys);
	if ($cache_keys != null && is_array($cache_keys)) {
	   if(!$silent)
	       print_r($cache_keys);
	   phpFastCache::deleteMulti($cache_keys);
	}
	phpFastCache::delete($cachename_keys);
}

/**
  * CroDigitSorting functions designed to be combined to reach desired result.
  *
  * Examples:
  * $array = array('c', 'a', 'b');
  * CroDigitSorting::sort($array, CroDigitSorting.string());
  *
  * $array = array('b' => 2, 'a' => 1);
  * CroDigitSorting::sortAssoc($array, CroDigitSorting::number());
  *
  * $array = array(array('key' => 12), array('key' => 8));
  * CroDigitSorting::sort($array, CroDigitSorting::onKey('key', CroDigitSorting::number()));
  */
class CroDigitSorting {
	public static function string() {
		return function ($a, $b) {
			return strcmp($a, $b);
		};
	}
	public static function stringNaturalOrder() {
		return function ($a, $b) {
			return strnatcmp($a, $b);
		};
	}
	public static function stringCaseInsensitive() {
		return function ($a, $b) {
			return strcasecmp($a, $b);
		};
	}
	public static function number() {
		return function ($a, $b) {
			return $a < $b ? -1 : ($a == $b ? 0 : 1);
		};
	}
	public static function reverse($comparator) {
		return function ($a, $b) use ($comparator) {
			return $comparator($b, $a);
		};
	}
	public static function onKey($key, $function) {
		return function ($a, $b) use ($key, $function) {
			return $function($a[$key], $b[$key]);
		};
	}
	public static function sort(&$array, $comparator) {
		usort($array, $comparator);
		return $array;
	}
	public static function sortAssoc(&$array, $comparator) {
		uasort($array, $comparator);
		return $array;
	}
	public static function sortOnKey(&$array, $comparator) {
		uksort($array, $comparator);
		return $array;
	}
}

function formElementTermsDropdown_helper($elements,$depth = 0)
{
    $r = '';
    $prefix = '';

    for ($x = 1; $x <= $depth; $x++) {
        $prefix .= '-';
    }
    foreach($elements as $k => $v)
    {
		if (is_array($v))
		{
            $r .= '<option value="'.$k .'">'.$prefix.$v['Naziv'].'</option>';
            if (isset($v['children']))
                $r .= formElementTermsDropdown_helper($v['children'],($depth+1));
		}
    }
    return $r;
}
function formElementTermsDropdown($nodetype = 'clanak',$titleTranslated = '',$formname = 'prikazi_kategoriju')
{
    global $tax;
    if (!isset($tax))
        die('$tax is not defined in formElementTermsDropdown() function');

    $terms = $tax->Get($nodetype,true);

    $html = '<div class="control-group">
    <label class="control-label" for="'.$formname.'">'.$titleTranslated.':</label><br />
    <select id="'.$formname.'" name="'.$formname.'">';

    $html .= formElementTermsDropdown_helper($terms);


    $html .= '</select></div>';
    return $html;
}
