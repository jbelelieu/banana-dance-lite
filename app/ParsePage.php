<?php namespace App;

use \Michelf\MarkdownExtra as Markdown;

/**
 * This class is used to take raw pages (markdown files) and prepare them for public consumption.
 * 
 * @author      jbelelieu
 * @date        7/3/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */

class ParsePage {


    /**
     * @var string
     */
    private $internalHeadings = array();

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var string
     */
    private $rawData = '';

    /**
     * @var     Markdown    Class that contains the tool for formatting Markdown.
     *                      Thank you to Michel Fortin (michelf.ca)!
     */
    private $markdown;


    /**
     *
     */
    public function __construct()
    {
        $this->markdown = new Markdown();
    }


    /**
     * Get the formatted contents of the page. Should be called after
     * a process() request.
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * Get the internal links for a page in the form of a formatted <ul>.
     *
     * @return  string
     */
    public function getHeadings()
    {
        return $this->internalHeadings;
    }


    /**
     * This is the data from the raw markdown wiki file.
     *
     * @param   string  $data
     *
     * @return  $this
     */
    public function setRawData($data)
    {
        $this->rawData = $data;

        return $this;
    }


    /**
     * Process everything we need to do to make the raw file usable.
     *
     * @return  $this
     */
    public function process()
    {
        $this->content = $this->markdown->transform($this->rawData);

        $this->standardReplacements();

        $this->content = $this->parseInnerHeadings();

        return $this;
    }


    /**
     * Custom replacements available on all pages. Establish standard
     * replacements within the "config/custom_replacements.php" file.
     *
     * @return  bool
     */
    private function standardReplacements()
    {
        $replacements = include dirname(dirname(__FILE__)) . '/wiki/config/custom_replacements.php';

        $lang = \App\getLanguage();

        $useArray = $replacements[$lang];

        // If the user requested a language other than english but there
        // are no entries for that language, try to default to english.
        if (empty($useArray) && $lang != BD_DEFAULT_LANGUAGE) {
            if (empty($replacements[BD_DEFAULT_LANGUAGE])) return false;

            $useArray = $replacements[BD_DEFAULT_LANGUAGE];
        }

        $keys = array_keys($useArray);
        $values = array_values($useArray);

        $this->content = str_replace($keys, $values, $this->content);

        return true;
    }


    /**
     * Builds a list of page headings for easy access and internal linking.
     *
     * @return  string  Final page with added <a name> tags.
     */
    private function parseInnerHeadings()
    {
        $exp = explode("\n", $this->content);

        $newContent = '';

        foreach ($exp as $line) {
            $check = substr(trim($line), 0, 4);

            if ($check == '<h1>' || $check == '<h2>' || $check == '<h3>' || $check == '<h4>') {

                switch ($check) {
                    case '<h2>':
                        $class = 'h2'; break;
                    case '<h3>':
                        $class = 'h3'; break;
                    case '<h4>':
                        $class = 'h4'; break;
                    default:
                        $class = 'h1';
                }

                $aName = str_replace(' ', '_', strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', strip_tags($line))));

                $this->internalHeadings[$aName] = array(
                    'class' => $class,
                    'name' => trim(substr($line, 4)),
                );

                // Add the anchor to the page's content.
                $newContent .= '<a class="anchor" name="' . $aName . '"></a>' . "\n" . $line . "\n";
            }
            else {
                $newContent .= $line . "\n";
            }
        }

        return $newContent;
    }

}