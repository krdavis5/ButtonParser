<?php
class LinkButtonizer {
   // Register any render callbacks with the parser
   public static function onParserSetup( &$parser ) {

      // Create a function hook associating the "example" magic word with renderExample()
      $parser->setFunctionHook( 'button', 'LinkButtonizer::renderButtonLink' );
   }

   // Render the output of {{#example:}}.
   public static function renderButtonLink( $parser ) {

      // The input parameters are wikitext with templates expanded.
      // $options['link'] = link, relative or direct
      // $options['title'] = link title (optional)
      // $options['style'] = mw-button style (optional; available styles = neutral, constructive, destructive, progressive (default))

      $options = self::extractOptions( array_slice( func_get_args(), 1 ) );

      // Make sure we have some parameters
      if ( empty( $options ) || !isset( $options['link'] ) )
        return array( '<a href="#" class="mw-ui-button mw-ui-destructive">I\'m an empty button</a>', 'noparse' => true, 'isHTML' => true );
      // Determine style option if present
      if ( !empty( $options['style'] ) ) {
          switch( $options['style'] ) {
              case "neutral":
                  $style = ""; // No additional style class necessary
                  break;
              case "constructive":
                  $style = "mw-ui-constructive";
                  break;
              case "destructive":
                  $style = "mw-ui-destructive";
                  break;
              case "progressive":
              default:
                  $style = "mw-ui-progressive";
          }
      } else {
          // If style is not defined, just apply progressive style
          $style = "mw-ui-progressive";
      }

      // Determine if link is local, or external
      if ( substr( $options['link'], 0, 4 ) == "http" ) {
        // Link starts with http, so we have an external link
        if ( empty( $options['title'] ) ) {
          // If this evaluates to true, title string is empty so use the link
          // as button title and return some html
          $output = "<a class=\"mw-ui-button " . $style . "\" href=\"" . $options['link'] . "\">" . $options['link'] . "</a>";
        } else {
          // We apparantly have a title present
          $output = "<a class=\"mw-ui-button " . $style . "\" href=\"" . $options['link'] . "\">" . $options['title'] . "</a>";
        }

        // Return html
        return array( $output, 'noparse' => true, 'isHTML' => true );
      } else {
        // Link must be internal, since we don't have http in the url
        if ( empty( $options['title'] ) ) {
          // If this evaluates to true, title string is empty so use the link
          $internalLink = $parser->replaceInternalLinks( "[[" . $options['link'] . "]]" );

          // Build the rest of the button
          $output = $internalLink;
        } else {
          // We have a title, so lets make a fancy wiki link
          $internalLink = $parser->replaceInternalLinks( "[[" . $options['link'] . "|" . $options['title'] . "]]" );

          // Build the rest of the button
          $output = $internalLink;
        }
        // Prepare the output for html output
        // Since we used MediaWiki's parser, a few different scenarios can occur for generated link...
        // First check if we self-linked -- if so, mediawiki will not make a link, only a <strong> tag
        if ( strpos( $output, "selflink" ) )
        {
            // MediaWiki found a self-link so format is <strong class="selflink">text</strong>
            // We need to change the class to button classes
            $output = str_replace( "selflink", "mw-ui-button " . $style, $output );
        } elseif ( strpos( $output, "LINK" ) ) {
            // MediaWiki handles nonexistent links with an extra parser
            // We need to wrap this preparsed output with a div with the button class
            $output = "<div class=\"mw-ui-button " . $style . "\">" . $output . "</div>";
        } else {
            // MediaWiki will not have appended a class to a normal internal link
            // We need to str_replace the actual tag to inject our class
            $output = str_replace( "<a ", "<a class=\"mw-ui-button " . $style . "\" ", $output );
        }

        // Return html
        return array( $output, 'noparse' => true, 'isHTML' => true );
      }
   }

   public static function extractOptions( array $options ) {
       $results = array();

       foreach ( $options as $option ) {
           $pair = explode( '=', $option, 2 );
           if ( count( $pair ) === 2 ) {
               $name = trim( $pair[0] );
               $value = trim( $pair[1] );
               $results[$name] = $value;
           }

           if ( count( $pair ) === 1 ) {
               $name = trim( $pair[0] );
               $results[$name] = true;
           }
       }

       return $results;
   }
}
