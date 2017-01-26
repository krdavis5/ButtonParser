# LinkButtonizer

LinkButtonizer is an extension for MediaWiki that generates buttons given a link, and optionally, a title. Styles are defined within [MediaWiki's UI CSS library](https://wikimedia.github.io/WikimediaUI-Style-Guide/) and can be chosen from the following:

- neutral (white)
- destructive (red)
- constructive (green)
- progressive (blue)

### How to install
Clone or copy this repository in to your MediaWiki extensions folder.

In your `LocalSettings.php` file, add the following:
```
// LinkButtonizer
wfLoadExtension( 'LinkButtonizer' );
```

### How to use
##### Internal links
```
{{#button:
   link = Main_page
   | style = constructive
   | title = Some title }}
```

The above would generate a button that links to the Main page with a title of 'Some title' and the green constructive style.

Magic words can be used inside of the parser. For instance, if we want to link to the editing action of an internal page, we can use the `{{fullurl:pagename|query_string}}` syntax to build the link and use that as the button's link as follows:

```
{{#button:
   link = {{fullurl:Main_page|action=edit}}
   | style = constructive
   | title = Edit this page }}
```
##### External links
```
{{#button:
   link = https://www.google.com
   | style = progressive
   | title = Google }}
```

The above would create a blue progressive styled link to Google.


**Note:** This extension has been tested with MediaWiki 1.25.5 on PHP 5.6.14.
