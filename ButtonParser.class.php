<?php
class ButtonParser {
   // Register any render callbacks with the parser
   public static function onParserSetup( &$parser ) {

      // Create a function hook associating the "example" magic word with renderExample()
      $parser->setFunctionHook( 'button', 'ButtonParser::renderButtonLink' );
   }

   // Render the output of {{#example:}}.
   public static function renderButtonLink( $parser, $param1, $param2) {

      // The input parameters are wikitext with templates expanded.
      // param1 = link, relative or direct
      // param2 = link title (optional)

      // Determine if link is local, or external
      if(substr($param1, 0, 4) == "http") {
        // Link starts with http, so we have an external link
        if(empty($param2)) {
          // If this evaluates to true, title string is empty so use the link
          // as button title and return some html
          $output = "<div class=\"mw-ui-button mw-ui-progressive\"><a href=\"" . $param1 . "\">" . $param1 . "</a></div>";
        } else {
          // We apparantly have a title present
          $output = "<div class=\"mw-ui-button mw-ui-progressive\"><a href=\"" . $param1 . "\">" . $param2 . "</a></div>";
        }

        // Return html
        return array( $output, 'noparse' => true, 'isHTML' => true );
      } else {
        // Link must be internal, since we don't have http in the url
        if(empty($param2)) {
          // If this evaluates to true, title string is empty so use the link
          $internalLink = $parser->replaceInternalLinks("[[" . $param1 . "]]");

          // Build the rest of the button
          $output = "<div class=\"mw-ui-button mw-ui-progressive\">" . $internalLink . "</div>";
        } else {
          // We have a title, so lets make a fancy wiki link
          $internalLink = $parser->replaceInternalLinks("[[" . $param1 . "|" . $param2 . "]]");

          // Build the rest of the button
          $output = "<div class=\"mw-ui-button mw-ui-progressive\">" . $internalLink . "</div>";
        }
        // Prepare the output for html output
        // Since we parsed a wiki link, mediawiki will add those pesky classes to our link... lets remove them
        $output = str_replace(" class=\"external mw-version-ext-name\"", "", $output);

        // Return html
        return array( $output, 'noparse' => true, 'isHTML' => true );
      }
   }
}
