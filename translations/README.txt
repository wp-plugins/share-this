== Localizing ShareThis Classic ==

ShareThis Classic has been internationalized and is ready for localization. For more information about Wordpress, internationalization, and localization, see the following pages on the Wordpress Codex:

http://codex.wordpress.org/Translating_WordPress

and

http://codex.wordpress.org/Writing_a_Plugin#Internationalizing_Your_Plugin

A great tutorial on localizing a Wordpress plugin has been written by John Godley at Urban Giraffe:

http://urbangiraffe.com/articles/translating-wordpress-themes-and-plugins/

In essence, the task takes the following steps:

1. Download the localization tool Poedit (http://www.poedit.net/download.php), or use gettext or another tool of your choice. These instructions assume you will use PoEdit.

2. Choose "New Catalog from POT file..." in the File menu. Pick share-this.pot. Input your language and country, and your team name if you choose, then save the file using the naming conventions required for Wordpress (see links above).

3. Use Poedit to translate each string in the template file. The upper section lists the strings to be translated. Select a string and type your translations into the lower left text area.

4. Save the translated catalog. Wordpress uses a naming convention to load the correct catalog, so name your catalog following this template:

languagecode_COUNTRYCODE.po

For example, the US English translation catalog would be called en_US.po while the British English version would be called en_UK.po. A list of language codes can be found at http://en.wikipedia.org/wiki/ISO_639 and a list of country codes can be found at http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2 .

5. Poedit will produce both a .mo file and a .po file. Upload the .mo file to your server in the share-this plugin folder. Make sure your installation of Wordpress is configured to use your language by setting the WP_LANG string in wp-config.php. The appropriate .mo file will be loaded by Wordpress and the plugin will appear in your language!
