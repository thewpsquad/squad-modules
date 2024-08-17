<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Language recognition chart.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.4
 */

namespace DiviSquad\Utils;

/**
 * Language recognition chart.
 *
 * @package DiviSquad
 * @since 3.1.4
 */
class LRCart {

	/**
	 * Get supported language recognition chart.
	 *
	 * @since 3.1.4
	 *
	 * @return string
	 */
	public static function get_character_map() {
		$character_map = array(
			// Latin-1 Supplement and Latin Extended-A
			'äëïöüÄËÏÖÜáéíóúýÁÉÍÓÚÝàèìòùÀÈÌÒÙãñõÃÑÕâêîôûÂÊÎÔÛçÇ',
			// Latin Extended-A
			'āēīōūĀĒĪŌŪąęįųĄĘĮŲćłńśźżĆŁŃŚŹŻđĐǄǅǆǇǈǉǊǋǌǍǎǏǐǑǒǓǔǕǖǗǘǙǚǛǜ',
			// Latin Extended-B
			'ǝǞǟǠǡǢǣǤǥǦǧǨǩǪǫǬǭǮǯǰǱǲǳǴǵǶǷǸǹǺǻǼǽǾǿȀȁȂȃȄȅȆȇȈȉȊȋȌȍȎȏȐȑȒȓȔȕȖȗȘșȚț',
			// Greek and Coptic
			'ͰͱͲͳʹ͵Ͷͷͺͻͼͽ;Ϳ΄΅Ά·ΈΉΊΌΎΏΐΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩΪΫάέήίΰαβγδεζηθικλμνξοπρςστυφχψωϊϋόύώϏϐϑϒϓϔϕϖϗϘϙϚϛϜϝϞϟϠϡϢϣϤϥϦϧϨϩϪϫϬϭϮϯ',
			// Cyrillic
			'ЀЁЂЃЄЅІЇЈЉЊЋЌЍЎЏАБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдежзийклмнопрстуфхцчшщъыьэюяѐёђѓєѕіїјљњћќѝўџѠѡѢѣѤѥѦѧѨѩѪѫѬѭѮѯѰѱѲѳѴѵѶѷѸѹѺѻѼѽѾѿҀҁ',
			// Common punctuation and symbols
			'""\'\'©®™§¶†‡•❧☙―‐‒–—―‖‗\'\'‚‛""„‟•‣․‥…‧‰‱′″‴‵‶‷‸‹›※‼‽‾⁀⁁⁂⁃⁄⁅⁆⁇⁈⁉⁊⁋⁌⁍⁎⁏⁐⁑⁒⁓⁔⁕⁖⁗⁘⁙⁚⁛⁜⁝⁞',
			// Currencies
			'₠₡₢₣₤₥₦₧₨₩₪₫€₭₮₯₰₱₲₳₴₵₶₷₸₹₺₻₼₽₾₿',
			// Arrows
			'←↑→↓↔↕↖↗↘↙↚↛↜↝↞↟↠↡↢↣↤↥↦↧↨↩↪↫↬↭↮↯↰↱↲↳↴↵↶↷↸↹↺↻↼↽↾↿⇀⇁⇂⇃⇄⇅⇆⇇⇈⇉⇊⇋⇌⇍⇎⇏⇐⇑⇒⇓⇔⇕⇖⇗⇘⇙⇚⇛⇜⇝⇞⇟⇠⇡⇢⇣⇤⇥⇦⇧⇨⇩⇪',
			// Mathematical symbols
			'∀∁∂∃∄∅∆∇∈∉∊∋∌∍∎∏∐∑−∓∔∕∖∗∘∙√∛∜∝∞∟∠∡∢∣∤∥∦∧∨∩∪∫∬∭∮∯∰∱∲∳∴∵∶∷∸∹∺∻∼∽∾∿≀≁≂≃≄≅≆≇≈≉≊≋≌≍≎≏≐≑≒≓≔≕≖≗≘≙≚≛≜≝≞≟≠≡≢≣≤≥≦≧≨≩≪≫≬≭≮≯≰≱≲≳≴≵≶≷≸≹≺≻≼≽≾≿',
		);

		/**
		 * Filter the character map for the post content.
		 *
		 * @since 3.1.4
		 *
		 * @param string $character_map The character map.
		 *
		 * @return string
		 */
		$character_map = apply_filters( 'divi_squad_language_recognition_chart_characters', $character_map );

		return implode( '', $character_map );
	}
}
