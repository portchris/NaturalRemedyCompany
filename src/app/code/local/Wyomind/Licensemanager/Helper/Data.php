<?php

/**     
 * The technical support is guaranteed for all modules proposed by Wyomind.
 * The below code is obfuscated in order to protect the module's copyright as well as the integrity of the license and of the source code.
 * The support cannot apply if modifications have been made to the original source code (https://www.wyomind.com/terms-and-conditions.html).
 * Nonetheless, Wyomind remains available to answer any question you might have and find the solutions adapted to your needs.
 * Feel free to contact our technical team from your Wyomind account in My account > My tickets. 
 * Copyright © 2017 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
  class Wyomind_Licensemanager_Helper_Data extends Mage_Core_Helper_Data {public $xb1=null;public $x17=null;public $x98=null; public function __construct() {$xdad = "\x67\145\164\x42a\x73\145D\151\162";$xe28 = "\147\x65t\x43\x6fn\146\151\x67";$xdfe = "\147e\x74S\x74or\145C\x6fnf\x69\147";         $this->_construct();
         } public function _construct() {$xdad = "\x67\145\x74\102a\x73e\104\151\x72";$xe28 = "\147\145\164\x43\x6f\x6e\146\151g";$xdfe = "\147\145\x74\x53\x74\157\x72\x65\103\x6f\156\x66\151g"; $this->constructor($this, func_get_args()); } public function constructor($x5cb, $x5d6, $x68 = array()) {$xcdd = "\x67et\x5fp\141ren\164\x5f\x63\154\x61\163\x73";$xce7 = "\x73t\162\x69\x73tr";$xcf0 = "\145\x78\160l\157\144\x65";$xd05 = "\147\145t\x5fc\154as\163";$xd10 = "\141\x72r\141y_\x70o\x70";$xd1a = "\x73\x69\155\x70\x6c\x65x\x6d\154\137\154\x6f\141\x64_\146\151\154\x65";$xd25 = "\x6d\x64\65";$xd33 = "\163\165\142\163\x74\x72";$xd4a = "\x69\x73_\163\x74\x72i\156\x67";$xd57 = "\x70\162\x6fp\x65rty\x5f\145\x78\151\x73\164\163";$xd5e = "\163\x74\x72t\157\154\x6fwe\x72";$xd68 = "\x73\x74\x72\x63m\160";$xd73 = "\x6c\x6fg";$xdad = "\x67\x65\164B\x61\163\145\x44\x69\162";$xe28 = "g\145tCon\x66\x69\x67";$xdfe = "ge\x74\x53t\157\x72\145C\157n\146\151\x67";  $x84 = $xcdd($x5cb); if ($xce7($x84, "\x77\171\x6fmi\156d") && $xce7($x84, "\137\x77\141\164c\x68\154o\x67\137") === false) { $xd4 = $xcf0("_", $x84); } else { $xd4 = $xcf0("\x5f", $xd05($x5cb)); } $x1a2 = $xd4[1]; $xd4 = $xd10($xd4); $xb5 = Mage::$xdad() . "/a\x70\x70/co\x64\145\57l\157\143\x61l\57\x57\x79\x6f\155\x69\156d/"; $xc6 = $xd1a($xb5 . $x1a2 . "/\145t\x63/\143\x6f\156\x66\151\147.\x78\155\x6c"); $xc7 = "W\x79\x6f\155\151\x6e\x64_" . $x1a2; $xe0 = $xd25((string) $xc6->modules->$xc7->version); $xef = $xd25($xd4); $x5aa = array("\170" . $xd33($xe0, 0, 2), "\x78" . $xd33($xe0, 2, 2), "\x78" . $xd33($xe0, 4, 2), "x" . $xd33($xef, 0, 2), "\x78" . $xd33($xef, 2, 2), "x" . $xd33($xef, 4, 2)); $x138 = null; $x114 = "W\x79o\x6din\x64\x5fLi\x63\145\156\x73\145m\141\x6e\x61\147\145\162_\110\145\x6cpe\x72\x5f" . $x1a2; $x10a = "W\x79\x6f\155\x69\x6e\x64\137" . $x1a2 . "\x5f\x48\x65\154\160er_" . $x1a2 . ""; $x138 = null; if (mageFindClassFile($x10a)) { $x138 = new $x10a(); } elseif (mageFindClassFile($x114)) { $x138 = new $x114(); } foreach ($x5aa as $x5b7) { if ($x138 != null) { if (!$xd4a($x5d6)) { if ($xd57($x5cb, $x5b7)) { $x5cb->$x5b7 = $x138; } } } } $x72 = $this->x17->x629->{$this->xb1->x629->{$this->x98->x629->{$this->xb1->x629->{$this->x98->x629->x8b5}}}};$xdad = "\147\x65t\x42\141\x73e\x44i\x72";$xe28 = "ge\x74\x43\157\x6e\x66i\147";$xdfe = "\147\x65\x74\123\x74\x6f\x72eC\x6fn\146\151g";$x7d = $this->x17->x629->{$this->x17->x629->{$this->xb1->x629->x8be}};$xdad = "g\145\164\102\141\163e\104\x69r";$xe28 = "g\145\x74C\157\156\x66\151\147";$xdfe = "get\x53\164o\162\x65\103\x6fnfi\x67";$x8b = $this->x98->x629->{$this->x98->x629->{$this->x98->x629->x8cf}};$xdad = "\147et\x42\141se\x44\151\x72";$xe28 = "\x67e\164\103\157\x6e\146\x69\x67";$xdfe = "\147\145\x74\x53\164\157\x72\145\x43on\146\x69g";$x8e = $this->x98->x629->{$this->xb1->x629->{$this->x98->x629->x8e0}};$xdad = "\147\145\x74\x42\141s\x65D\151r";$xe28 = "\x67\x65\x74\103\x6f\156\x66\151\147";$xdfe = "\147\x65t\123t\x6f\162\145Co\x6e\x66\x69\147";$xa4 = $this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x8f0}};$xdad = "\147\x65\x74B\x61s\145\x44ir";$xe28 = "ge\x74\x43\157\156\146\151\147";$xdfe = "\x67\x65t\123t\157\x72e\103\157\156fig";$xb0 = $this->x98->x642->{$this->xb1->x642->{$this->x98->x642->xd22}};$xdad = "\147e\164\x42\x61\163e\x44\x69\x72";$xe28 = "\147\145t\x43\x6f\156\x66\151\x67";$xdfe = "\147\x65tS\x74\157\x72e\x43\x6f\156\x66\151\147";$x5d3 = $this->x98->x642->{$this->x17->x642->{$this->x98->x642->xd2f}};$xdad = "\147\145\x74\102a\163e\x44ir";$xe28 = "\x67\x65\x74\103\157\156\x66\151\x67";$xdfe = "g\145\164\x53\164o\x72e\x43\157\156\x66\x69\x67";$x5d2 = $this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x92f}};$xdad = "getB\x61\163\145\104ir";$xe28 = "g\x65\164Co\156f\x69\x67";$xdfe = "g\145\x74S\164\x6f\162e\x43on\146ig";$x5c1 = $this->xb1->x629->{$this->x98->x629->x938};$xdad = "\147et\x42\141\163\x65\x44i\162";$xe28 = "\x67\145\x74C\157\156\146\x69\147";$xdfe = "\147et\x53tore\103o\156\146\151\147";$x128 = $this->x98->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x98->x629->x94d}}};$xdad = "\147etB\x61s\x65\x44\x69\x72";$xe28 = "\x67etC\x6f\x6e\146ig";$xdfe = "\147\145t\123\x74\x6f\x72eC\157\x6e\146\x69\x67";$x54e = $this->x17->x629->{$this->xb1->x629->x958};$xdad = "g\145\x74Ba\x73\x65Di\x72";$xe28 = "\147e\x74C\157\x6e\146\x69g";$xdfe = "g\145\x74\x53\x74\x6f\162\145\103onf\151\x67";$x57b = $this->x98->x642->{$this->xb1->x642->xd6e};$xdad = "\147\x65tBase\104\151r";$xe28 = "\147\x65\x74C\x6f\x6efi\x67";$xdfe = "g\x65\164\x53tore\103\157n\146ig";$x52f = $this->x17->x629->{$this->x17->x629->{$this->x98->x629->x971}};$xdad = "\147et\102\x61\163\145D\151\162";$xe28 = "ge\164\103on\x66i\x67";$xdfe = "\147\145t\123\x74\157r\x65\x43o\x6e\x66\151\x67"; ${$this->x98->x629->{$this->x17->x629->x6ed}} = "\62"; ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->x17->x642->xb2c}}}} = 0; if ($x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}})) { ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa63}}}}->${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}} = $x5d2($x5d3(${$this->x17->x629->{$this->x98->x629->x652}}), ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->x6f6}}}, ${$this->xb1->x642->xb1b}); ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->x17->x642->xb2c}}}}+=${$this->x17->x629->x6e8}; } ${$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->x6ff}}} = "\x4da\x67\145"; ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x17->x629->x70e}}}}} = "\x68e\x6c\x70\x65\162"; if ($x5c1(${$this->xb1->x642->{$this->x17->x642->xa72}})) { ${$this->x17->x629->x646}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xa73}}} = ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa63}}}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}} . $x5d2($x5d3(${$this->x98->x629->x64d}), ${$this->xb1->x629->x6f1}, ${$this->x98->x629->{$this->x17->x629->x6ed}}); ${$this->xb1->x629->x6f1}+=${$this->x98->x629->{$this->x17->x629->x6ed}}; } ${$this->x98->x629->{$this->x98->x629->x714}} = "\164\x68\162\157\167\x45\x78\143\145\160\164\151o\x6e"; ${$this->x98->x642->{$this->x17->x642->xb4a}} = "\166\145rsion"; ${$this->x98->x642->xb4c} = "\156ul\154"; ${$this->xb1->x629->{$this->x98->x629->x73b}} = ${$this->xb1->x642->{$this->x17->x642->{$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xaa2}}}}}; if ($x5c1(${$this->x98->x629->x64d})) { ${$this->x98->x642->xa5a}->${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}} = ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}} . $x5d2($x5d3(${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xa73}}}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xb27}}}, ${$this->x17->x629->x6e8}); ${$this->xb1->x629->x6f1}+=${$this->x98->x629->{$this->x17->x629->x6ed}}; } ${$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->xb64}}} = "\141c\x74\151va\x74\x69on\x5fcod\x65"; ${$this->x17->x642->{$this->xb1->x642->xb75}} = "act\151v\141t\x69\x6fn_\153e\x79"; ${$this->x17->x629->x74d} = "\142a\x73e_\x75rl"; ${$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x75d}}} = "e\x78ten\x73\151\157\x6e\137\x63od\x65"; if ($x5c1(${$this->x98->x629->x64d})) { ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xa73}}} = ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x98->x642->{$this->x17->x642->xa68}}}}}->${$this->xb1->x642->{$this->x17->x642->xa72}} . $x5d2($x5d3(${$this->x98->x642->xa6d}), ${$this->xb1->x629->{$this->x17->x629->x6f4}}, ${$this->xb1->x642->{$this->xb1->x642->xb1d}}); ${$this->xb1->x629->x6f1}+=${$this->x17->x629->x6e8}; } ${$this->x98->x629->x767} = "l\x69\143"; ${$this->x98->x642->{$this->xb1->x642->xb9b}} = "e\156s"; ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xba5}}} = "\167eb"; if ($x5c1(${$this->xb1->x642->{$this->x17->x642->xa72}})) { ${$this->x98->x642->xa5a}->${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x65c}}}}} = ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa63}}}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}} . $x5d2($x5d3(${$this->x98->x642->xa6d}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xb27}}}, ${$this->xb1->x642->{$this->xb1->x642->xb1d}}); ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xb27}}}+=${$this->xb1->x642->xb1b}; } ${$this->x98->x629->{$this->xb1->x629->x782}} = "\145/a\x63"; ${$this->xb1->x642->{$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xbbf}}}} = "e\x2f\x65x"; ${$this->xb1->x629->{$this->x17->x629->x794}} = "\x74\x69\x76"; ${$this->x17->x629->x7a1} = "t\x65\x6e"; ${$this->x98->x642->xbcb} = "/s\x65\x63"; ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->{$this->x17->x629->x7b9}}}} = "a\x74\x69"; ${$this->x98->x642->{$this->xb1->x642->xbdd}} = "\x72l"; ${$this->x98->x629->{$this->x98->x629->x7c8}} = "\165\x72\x65"; ${$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->x7d5}}} = "\163i\x6f"; ${$this->x17->x642->xbee} = "\x6f\x6e_"; ${$this->x98->x629->{$this->x17->x629->x7e4}} = ${$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x700}}}}::$xe28()->{$this->x98->x629->x9d0}("m\x6f\x64\x75\x6ce\x73\x2f\x57\171\157\x6d\151\156d_" . ${$this->x17->x642->xb52})->${$this->x17->x629->x71c}; if ($x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}})) { ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->xa62}}}->${$this->x98->x629->x64d} = ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa63}}}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}} . $x5d2($x5d3(${$this->x98->x629->x64d}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->xb1->x642->{$this->x98->x642->xb30}}}}}, ${$this->xb1->x642->xb1b}); ${$this->xb1->x629->x6f1}+=${$this->xb1->x642->{$this->xb1->x642->xb1d}}; } ${$this->x98->x629->{$this->xb1->x629->{$this->x17->x629->x7f4}}} = "\x66\154\x61\147"; if ($x5c1(${$this->xb1->x642->{$this->x17->x642->xa72}})) { ${$this->x98->x642->xa5a}->${$this->xb1->x642->{$this->x17->x642->xa72}} = ${$this->x17->x642->{$this->x98->x642->xa5d}}->${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x65c}}}}} . $x5d2($x5d3(${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xb27}}}, ${$this->xb1->x642->{$this->xb1->x642->xb1d}}); ${$this->x98->x642->{$this->xb1->x642->xb25}}+=${$this->xb1->x642->xb1b}; } ${$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x802}}} = "\x6e\137\143"; if ($x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x65c}}}}})) { ${$this->x17->x642->{$this->x98->x642->xa5d}}->${$this->x98->x642->xa6d} = ${$this->x17->x629->x646}->${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x65c}}}}} . $x5d2($x5d3(${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->xb1->x629->x657}}}}), ${$this->xb1->x629->x6f1}, ${$this->xb1->x642->xb1b}); ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->x6f6}}}+=${$this->xb1->x642->xb1b}; } ${$this->x17->x642->{$this->x17->x642->xc18}} = "\x6bey"; if ($x5c1(${$this->x98->x642->xa6d})) { ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->xb1->x629->x657}}}} = ${$this->x98->x642->xa5a}->${$this->x98->x642->xa6d} . $x5d2($x5d3(${$this->x17->x629->{$this->x98->x629->x652}}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->xb1->x642->{$this->x98->x642->xb30}}}}}, ${$this->x17->x629->x6e8}); ${$this->xb1->x629->{$this->x17->x629->x6f4}}+=${$this->xb1->x642->xb1b}; } ${$this->x17->x642->{$this->xb1->x642->xc20}} = "o\x64\x65"; if ($x5c1(${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}})) { ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->xa62}}}->${$this->xb1->x642->{$this->x17->x642->xa72}} = ${$this->x17->x642->{$this->x98->x642->xa5d}}->${$this->x98->x629->x64d} . $x5d2($x5d3(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}}), ${$this->x98->x642->{$this->xb1->x642->xb25}}, ${$this->xb1->x642->xb1b}); ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xb27}}}+=${$this->xb1->x642->xb1b}; } ${$this->x98->x629->{$this->x17->x629->x829}} = "/\x62a\x73"; if ($x5c1(${$this->x17->x629->{$this->x98->x629->x652}})) { ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa63}}}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}} = ${$this->x98->x642->xa5a}->${$this->x17->x629->{$this->x98->x629->x652}} . $x5d2($x5d3(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->xb1->x642->{$this->x98->x642->xb30}}}}}, ${$this->x98->x629->{$this->x17->x629->x6ed}}); ${$this->xb1->x629->{$this->x17->x629->x6f4}}+=${$this->xb1->x642->{$this->xb1->x642->xb1d}}; } ${$this->x98->x642->xc31} = "e_\x75"; if ($x5c1(${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}})) { ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}} = ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->xa62}}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xa73}}} . $x5d2($x5d3(${$this->xb1->x642->{$this->x17->x642->xa72}}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xb27}}}, ${$this->x17->x629->x6e8}); ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->x6f6}}}+=${$this->x98->x629->{$this->x17->x629->x6ed}}; } ${$this->x17->x642->{$this->x17->x642->{$this->xb1->x642->{$this->xb1->x642->xc48}}}} = "\x63\157d\145"; if ($x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->xb1->x629->x657}}}})) { ${$this->x17->x629->x646}->${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x65c}}}}} = ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->x98->x629->x64d} . $x5d2($x5d3(${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xa73}}}), ${$this->x98->x642->xb22}, ${$this->x98->x629->{$this->x17->x629->x6ed}}); ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->x17->x642->xb2c}}}}+=${$this->xb1->x642->{$this->xb1->x642->xb1d}}; } ${$this->xb1->x629->{$this->x98->x629->{$this->xb1->x629->{$this->xb1->x629->x84f}}}}["a\143" . ${$this->xb1->x629->{$this->x17->x629->x794}} . ${$this->x98->x642->xbcf} . ${$this->xb1->x642->{$this->x17->x642->xbf2}} . ${$this->x98->x629->x807}] = ${$this->x98->x642->{$this->xb1->x642->xb36}}::$xdfe($x54e(${$this->x17->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x98->x642->xb5d}}}}) . "\57" . ${$this->xb1->x642->{$this->x17->x642->xb8c}} . ${$this->x98->x642->xb98} . ${$this->x98->x629->{$this->xb1->x629->x782}} . ${$this->x17->x642->xbc2} . ${$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->{$this->xb1->x642->xbd9}}}} . ${$this->xb1->x642->{$this->x17->x642->xbf2}} . ${$this->x17->x642->xc15}, 0); ${$this->x98->x629->x847}["\x65\x78" . ${$this->x17->x629->{$this->x98->x629->x7a2}} . ${$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x7d9}}}} . ${$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x802}}} . ${$this->x17->x642->{$this->xb1->x642->xc20}}] = ${$this->x98->x642->{$this->xb1->x642->xb36}}::$xdfe($x54e(${$this->x17->x642->xb52}) . "/" . ${$this->xb1->x629->{$this->xb1->x629->x76a}} . ${$this->x98->x642->xb98} . ${$this->x98->x642->xbb3} . ${$this->x17->x642->{$this->xb1->x642->xbc9}} . ${$this->xb1->x629->{$this->x98->x629->x7d1}} . ${$this->xb1->x629->{$this->x17->x629->x7fe}} . ${$this->xb1->x629->{$this->xb1->x629->{$this->x17->x629->x819}}}, 0); ${$this->xb1->x629->{$this->x98->x629->{$this->xb1->x629->x84c}}}["ac" . ${$this->xb1->x629->{$this->x17->x629->{$this->x17->x629->x797}}} . ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->x7ba}}}}} . ${$this->xb1->x642->{$this->x17->x642->{$this->x98->x642->xbf7}}} . ${$this->x17->x642->{$this->x17->x642->{$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xc4d}}}}}] = ${$this->xb1->x629->x6fb}::$xdfe($x54e(${$this->x17->x642->{$this->x98->x642->xb56}}) . "\x2f" . ${$this->xb1->x642->{$this->x17->x642->{$this->xb1->x642->xb8d}}} . ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->x778}}} . ${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xbae}}} . ${$this->xb1->x629->{$this->x17->x629->x794}} . ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->x7ba}}}}} . ${$this->x98->x629->x7db} . ${$this->x17->x642->{$this->xb1->x642->xc42}}, 0); ${$this->xb1->x642->{$this->xb1->x642->{$this->x98->x642->xc57}}}["bas" . ${$this->x98->x642->xc31} . ${$this->x98->x629->{$this->x17->x629->{$this->x17->x629->x7c1}}}] = ${$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x700}}}}::$xdfe(${$this->x98->x642->{$this->x17->x642->xba2}} . ${$this->x98->x629->x7a7} . ${$this->x17->x642->{$this->x17->x642->xbe8}} . ${$this->x17->x642->{$this->x17->x642->xc24}} . ${$this->x98->x629->{$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->x841}}}} . ${$this->x98->x629->{$this->x17->x629->x7c0}}, 0); if (!$x57b(${$this->xb1->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->xc58}}}}[${$this->xb1->x642->{$this->xb1->x642->xb63}}], $x5d3($x5d3(${$this->xb1->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->xc58}}}}[${$this->x17->x642->{$this->xb1->x642->xb75}}]) . $x5d3(${$this->xb1->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->xc58}}}}[${$this->x17->x629->{$this->x98->x629->{$this->xb1->x629->{$this->x17->x629->x754}}}}]) . $x5d3(${$this->xb1->x642->{$this->xb1->x642->{$this->x98->x642->xc57}}}[${$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->{$this->x17->x642->xb84}}}}]) . $x5d3(${$this->x98->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->x7ea}}}}))) && $x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}}) && $x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}})) { ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xa73}}} = ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x65c}}}}} . $x5d2($x5d3(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}}), ${$this->x98->x642->xb22}, ${$this->x17->x629->x6e8}); ${$this->xb1->x629->x6f1}+=${$this->x17->x629->x6e8}; } if ($x57b(${$this->xb1->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->xc58}}}}[${$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->xb64}}}], $x5d3($x5d3(${$this->xb1->x642->xc52}[${$this->xb1->x629->{$this->x98->x629->x748}}]) . $x5d3(${$this->xb1->x629->{$this->x98->x629->x84b}}[${$this->x17->x629->{$this->x98->x629->{$this->x17->x629->x753}}}]) . $x5d3(${$this->xb1->x642->xc52}[${$this->x17->x629->x757}]) . $x5d3(${$this->x17->x642->{$this->xb1->x642->{$this->x17->x642->xc00}}}))) && $x5c1(${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xa73}}})) { ${$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->x6ff}}}::$xe28()->{$this->x17->x629->xa27}($x54e(${$this->x17->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x98->x642->xb5d}}}}) . "\57" . ${$this->xb1->x629->{$this->xb1->x629->x76a}} . ${$this->x98->x642->xb98} . ${$this->x17->x642->xba9} . ${$this->x17->x642->xbc2} . ${$this->x98->x642->xbcf} . ${$this->xb1->x629->{$this->x98->x629->x7de}} . ${$this->x98->x629->{$this->xb1->x629->x7f0}}, 1); if (!empty(${$this->xb1->x642->{$this->xb1->x642->{$this->x98->x642->xc57}}}["\141\143" . ${$this->x17->x642->xbc2} . ${$this->x98->x642->{$this->xb1->x642->{$this->xb1->x642->xbd6}}} . ${$this->x98->x629->x7db} . ${$this->x17->x642->{$this->x17->x642->{$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->xc4d}}}}}])) { ${$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->x6ff}}}::$xe28()->{$this->x17->x629->xa27}($x54e(${$this->x98->x629->x738}) . "/" . ${$this->x17->x642->xb89} . ${$this->x98->x642->{$this->xb1->x642->xb9b}} . ${$this->xb1->x642->{$this->xb1->x642->xbab}} . ${$this->x17->x629->x78f} . ${$this->xb1->x629->{$this->x17->x629->{$this->xb1->x629->{$this->x17->x629->x7b9}}}} . ${$this->xb1->x642->{$this->x17->x642->{$this->x98->x642->xbf7}}} . ${$this->xb1->x642->xc41}, ""); $this->{$this->xb1->x642->{$this->x98->x642->xcc7}}(${$this->x17->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x98->x642->xb5d}}}}, ${$this->x98->x629->{$this->x17->x629->x7e4}}, ${$this->xb1->x642->{$this->x17->x642->xc54}}[${$this->x17->x629->{$this->x98->x629->x750}}], ${$this->x98->x629->x847}[${$this->x17->x629->x743}]); } ${$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x700}}}}::${$this->xb1->x642->{$this->x98->x642->xb45}}(${$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->{$this->x17->x629->{$this->x98->x629->x704}}}}}::${$this->xb1->x629->x705}($x54e(${$this->x17->x642->xb52}))->{$this->xb1->x629->xa4f}(${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x98->x642->{$this->x17->x642->xa68}}}}}->error)); } else { if ($x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->xb1->x629->x657}}}})) { ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->x65c}}}}} = ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x98->x642->{$this->x17->x642->xa68}}}}}->${$this->xb1->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa76}}}} . $x5d2($x5d3(${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->xb1->x629->x657}}}}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->xb1->x642->{$this->x98->x642->xb30}}}}}, ${$this->xb1->x642->{$this->xb1->x642->xb1d}}); ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->xb27}}}+=${$this->xb1->x642->{$this->xb1->x642->xb1d}}; } if ($x57b(${$this->xb1->x642->{$this->xb1->x642->{$this->x98->x642->xc57}}}[${$this->x17->x629->x73d}], $x5d3($x5d3(${$this->xb1->x642->{$this->xb1->x642->{$this->x17->x642->{$this->x17->x642->xc58}}}}[${$this->x98->x642->xb72}]) . $x5d3(${$this->xb1->x629->{$this->x98->x629->x84b}}[${$this->x17->x629->{$this->x98->x629->x750}}]) . $x5d3(${$this->xb1->x629->{$this->x98->x629->{$this->xb1->x629->{$this->xb1->x629->x84f}}}}[${$this->xb1->x629->{$this->x98->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x98->x629->x764}}}}}]) . $x5d3(${$this->x98->x629->x7e2}))) && $x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->xb1->x629->x657}}}})) { foreach (${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->xae4}}} as ${$this->x17->x642->xb13}) { if (isset(${$this->x17->x642->{$this->x98->x642->xa5d}}->{${$this->x98->x642->{$this->x98->x642->xb17}}})) { ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x17->x642->xa63}}}}->{${$this->x98->x642->{$this->x98->x642->xb17}}} = ${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->{$this->x17->x629->{$this->xb1->x629->x734}}}}}; } } } else { if ($x5c1(${$this->x17->x629->{$this->xb1->x629->{$this->xb1->x629->{$this->xb1->x629->x657}}}})) { ${$this->x17->x642->{$this->x98->x642->{$this->xb1->x642->{$this->x98->x642->{$this->x17->x642->xa68}}}}}->${$this->x98->x629->x64d} = ${$this->xb1->x629->{$this->x98->x629->x64b}}->${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}} . $x5d2($x5d3(${$this->x17->x629->{$this->xb1->x629->{$this->x98->x629->x655}}}), ${$this->x98->x642->{$this->x17->x642->{$this->x17->x642->{$this->x17->x642->xb2c}}}}, ${$this->x17->x629->x6e8}); ${$this->xb1->x629->{$this->x17->x629->x6f4}}+=${$this->x17->x629->x6e8}; } } } }     public function log($namespace,
            $version,
            $domain,
            $activation_key = null,
            $message = "/!\\ Invalid license /!\\")
    {
        Mage::log($namespace . " v" . $version . " " . $domain . " " . $activation_key . " > " . $message, null, "Wyomind_LicenseManager.log");
    }

    } 