<?php namespace App;

/**
 * Search functionality for the program.
 *
 * @author      jbelelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */

class Search {

    /**
     * @var
     */
    private $query;

    /**
     * @var array
     */
    private $results = array();

    /**
     * @var string
     */
    private $searchDir;


    /**
     *
     */
    public function __construct()
    {
        $this->setSearchDir();
    }


    /**
     * Determines which directory within the wiki we are searching.
     *
     * @param   string  $dir
     *
     * @return  $this
     */
    public function setSearchDir($dir = '')
    {
        $this->searchDir = dirname(dirname(__FILE__)) . '/wiki/' . \App\getLanguage();

        if (! empty($dir)) $this->searchDir .= '/' . trim($dir, '/');

        return $this;
    }


    /**
     * Set the search query.
     *
     * @param   string  $query
     *
     * @return  $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }


    /**
     * Get the results of the search.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }


    /**
     * Run the search on a specific directory in the wiki.
     *
     * @param   string    $inputDir
     *
     * @return  array
     */
    public function run($inputDir = '')
    {
        if (empty($this->query)) return array();

        if (! empty($inputDir)) {
            $dir = $inputDir;
        } else {
            $dir = $this->searchDir;
        }

        $pattern = '\w+[^\w]+\w+[^\w]+\w+[^\w]+\w+[^\w]+\w+[^\w]+\w+[^\w]+';

        foreach (glob($dir . "/*") as $file) {
            if (is_dir($file)) $this->run($file);

            $contents = file_get_contents($file);

            if (stripos($contents, $this->query) !== false) {
                $find = preg_match('/' . $pattern . $this->query . $pattern . '/i', $contents, $m);
                if ($find) {
                    $this->results[$file] = strip_tags($m['0']);
                } else {
                    $find = preg_match('/' . $this->query . $pattern . '/i', $contents, $m);
                    if ($find) {
                        $this->results[$file] = strip_tags($m['0']);
                    } else {
                        $find = preg_match('/' . $pattern . $this->query . '/i', $contents, $m);
                        if ($find) {
                            $this->results[$file] = strip_tags($m['0']);
                        } else {
                            $this->results[$file] = '';
                        }
                    }
                }
            }
        }

        return $this;
    }


    /**
     * Get total number of results.
     *
     * @return int
     */
    public function count()
    {
        return sizeof($this->results);
    }


    /**
     * Format the results into a <ul> we can use on the views.
     *
     * @return  string
     */
    public function format()
    {
        if (empty($this->results)) return '';

        $formatted = '<ul id="searchResults">';

        $key = key($this->results);

        $remove = explode('/', $key);

        $lang = \App\getLanguage();

        $assemble = '';

        foreach ($remove as $item) {
            $assemble .= $item . '/';

            if ($item == $lang) break;
        }

        foreach ($this->results as $item => $snippet) {
            $item = str_replace($assemble, '', $item);

            $exp = explode('/', $item);

            $page = array_pop($exp);

            $category = implode('/', $exp);

            $item = \App\findName($item);

            $clean = preg_replace("/(" . $this->query . ")/i", "<span class=\"highlight\">$1</span>", $snippet);

            $formatted .= '<li><a href="' . BD_BASE_URL . '?p=' . $page . '&c=' . $category . '">' . $category;
            $formatted .= ' &raquo; ' . $item . '</a>';

            if (! empty($clean)) $formatted .= '<p>' . $clean . '</p>';

            $formatted .= '</li>';
        }

        $formatted .= '</ul>';

        return $formatted;
    }

}