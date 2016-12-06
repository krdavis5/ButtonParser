<?php
class ButtonParser {
   // Register any render callbacks with the parser
   public static function onParserSetup( &$parser ) {

      // Create a function hook associating the "example" magic word with renderExample()
      $parser->setFunctionHook( 'button', 'ButtonParser::renderButtonLink' );
   }

   // Render the output of {{#example:}}.
   public static function renderButtonLink( $parser, $param1, $param2='') {

      // The input parameters are wikitext with templates expanded.
      // param1 = link, relative or direct
      // param2 = link title (optional)

      // Determine if link is local, or external
      if(substr($param1, 0, 4) == "http") {
        // Link starts with http, so we have an external link
        if(empty($param2)) {
          // If this evaluates to true, title string is empty so use the link
          // as button title and return some html
          $output = "<a class=\"mw-ui-button mw-ui-progressive\" href=\"" . $param1 . "\">" . $param1 . "</a>";
        } else {
          // We apparantly have a title present
          $output = "<a class=\"mw-ui-button mw-ui-progressive\" href=\"" . $param1 . "\">" . $param2 . "</a>";
        }

        // Return html
        return array( $output, 'noparse' => true, 'isHTML' => true );
      } else {
        // Link must be internal, since we don't have http in the url
        if(empty($param2)) {
          // If this evaluates to true, title string is empty so use the link
          $internalLink = $parser->replaceInternalLinks("[[" . $param1 . "]]");

          // Build the rest of the button
          $output = $internalLink;
        } else {
          // We have a title, so lets make a fancy wiki link
          $internalLink = $parser->replaceInternalLinks("[[" . $param1 . "|" . $param2 . "]]");

          // Build the rest of the button
          $output = $internalLink;
        }
        // Prepare the output for html output
        // Since we used MediaWiki's parser, a few different scenarios can occur for generated link...
        // First check if we self-linked -- if so, mediawiki will not make a link, only a <strong> tag
        if(strpos($output, "selflink"))
        {
            // MediaWiki found a self-link so format is <strong class="selflink">text</strong>
            // We need to change the class to button classes
            $output = str_replace("selflink", "mw-ui-button mw-ui-progressive", $output);
        } elseif(strpos($output, "LINK")) {
            // MediaWiki handles nonexistent links with an extra parser
            // We need to wrap this preparsed output with a div with the button class
            $output = "<div class=\"mw-ui-button mw-ui-progressive\">" . $output . "</div>";
        } else {
            // MediaWiki will not have appended a class to a normal internal link
            // We need to str_replace the actual tag to inject our class
            $output = str_replace("<a ", "<a class=\"mw-ui-button mw-ui-progressive\" ", $output);
        }

        // Return html
        return array( $output, 'noparse' => true, 'isHTML' => true );
      }
   }
}
