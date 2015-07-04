<?php namespace App;

use \Michelf\MarkdownExtra as Markdown;

/**
 * This class is used to take raw markdown files and prepare them for public consumption.
 * 
 * @author  j-belelieu
 * @date    7/3/15
 */

class ParsePage {


    /**
     * @var string
     */
    private $internalHeadings = '';

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

        $this->parseInnerHeadings();

        return $this;
    }


    /**
     * Builds a list of page headings for easy access and internal linking.
     *
     * @return  string  Final page with added <a name> tags.
     */
    private function parseInnerHeadings()
    {
        $this->internalHeadings = '<ul id="innerHeadings">';

        $found = false;

        $exp = explode("\n", $this->content);

        $newContent = '';

        foreach ($exp as $line) {

            $check = substr(trim($line), 0, 4);

            if ($check == '<h1>' || $check == '<h2>' || $check == '<h3>' || $check == '<h4>') {

                switch ($check) {
                    case '<h1>':
                        $class = 'h1';
                        break;
                    case '<h2>':
                        $class = 'h2';
                        break;
                    case '<h3>':
                        $class = 'h3';
                        break;
                    case '<h4>':
                        $class = 'h4';
                        break;
                }

                $found = true;

                $aName = str_replace(' ', '_', strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', strip_tags($line))));

                $this->internalHeadings .= '<li class="' . $class . '"><a href="#' . $aName . '">' . trim(substr($line, 4)) . '</a></li>';

                $newContent .= '<a class="anchor" name="' . $aName . '"></a>' . "\n" . $line . "\n";

            }
            else {
                $newContent .= $line . "\n";
            }

        }

        $this->internalHeadings .= '</ul>';

        if (! $found) $this->internalHeadings = '';

        return $newContent;
    }

}