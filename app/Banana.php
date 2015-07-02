<?php namespace App;

use \Michelf\MarkdownExtra as Markdown;

/**
 * This is the primary "Controller" for the application.
 *
 * @author      jbelelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */
class Banana {

    /**
     * @var     Markdown    Class that contains the tool for formatting Markdown.
     *                      Thank you to Michel Fortin (michelf.ca)!
     */
    private $markdown;

    /**
     * @var     Structure   Class that builds the directory structure.
     */
    private $structure;

    /**
     * @var     Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * @var     View
     */
    private $view;

    /**
     * @var     array
     */
    private $structureArray = array();

    /**
     * @var     string
     */
    private $category = '';

    /**
     * @var     string
     */
    private $page = 'index.md';

    /**
     * @var     string
     */
    private $file;

    /**
     * @var     string
     */
    private $output;

    /**
     * @var     string
     */
    private $internalHeadings;


    /**
     * @param   string      $page       Name of the page we are loading.
     * @param   string      $category   Name of the category we are loading.
     * @param   string      $lang       Two letter language code.
     */
    public function __construct($page = '', $category = '', $lang = '')
    {
        $this->setPage($page);
        $this->setCategory($category);

        if (! empty($lang)) {
            $this->setLanguage($lang);
        } else {
            $this->determineLanguage();
        }

        $this->markdown = new Markdown();
        $this->structure = new Structure();
        $this->breadcrumbs = new Breadcrumbs();
        $this->view = new View();

        $this->buildRequest();
        $this->getStructure();
    }


    /**
     * Get the rendered view.
     *
     * @return  string
     */
    public function getOutput()
    {
        return $this->output;
    }


    /**
     *
     */
    public function setPage($page)
    {
        $this->page = htmlentities($page);
    }


    /**
     *
     */
    public function setCategory($category)
    {
        $this->category = htmlentities($category);
    }


    /**
     * Get the language the user has requested.
     *
     * @return  string
     */
    protected function determineLanguage()
    {
        if (! empty($_SESSION['bd_language'])) {
            return $this->setLanguage($_SESSION['bd_language']);
        } else {
            return $this->setLanguage(BD_DEFAULT_LANGUAGE);
        }
    }


    /**
     * Set the language to use. Confirm it is available first.
     *
     * @param   string  $lang
     *
     * @return  string
     */
    protected function setLanguage($lang = '')
    {
        $find = dirname(dirname(__FILE__)) . '/wiki/' . filter_var($lang, FILTER_SANITIZE_STRING);

        if (file_exists($find)) $_SESSION['bd_language'] = $lang;

        return $lang;
    }


    /**
     * Build the request to determine what wiki page is being requested.
     */
    protected function buildRequest()
    {
        $this->file = dirname(dirname(__FILE__)) . '/wiki/' . \App\getLanguage() . '/';

        $this->file .= (! empty($this->category)) ?
            $this->category . '/' . $this->page :
            $this->page;

        $crumbs = $this->breadcrumbs
            ->setCategory($this->category)
            ->setPage($this->page)
            ->build();

        $this->view->addChange('breadcrumbs', $crumbs);
    }


    /**
     * Changes that apply to all possible templates.
     */
    private function getDefaultChanges()
    {
        return array(
            'wiki_name' => BD_NAME,
            'wiki_theme' => BD_THEME,
            'wiki_base_url' => trim(BD_BASE_URL, '/'),
            'branding_color' => BD_BRANDING_COLOR,
            'languages' => $this->buildLanguages(),
            'query' => '',
            'navigation' => $this->structure->getHtml(),
        );
    }


    /**
     * Build a list of languages that are available.
     */
    private function buildLanguages()
    {
        $languages = '';

        $scan = dirname(dirname(__FILE__)) . '/wiki';
        $scan_mirror = dirname(dirname(__FILE__)) . '/app/views/' . BD_THEME;

        $c = (! empty($_GET['c'])) ? htmlentities($_GET['c']) : '';

        $p = (! empty($_GET['p'])) ? htmlentities($_GET['p']) : '';

        foreach (scandir($scan) as $item) {
            if (is_dir($scan . '/' . $item) && $item != '.' && $item != '..') {

                // The directory must exist in the theme as well.
                if (! file_exists($scan_mirror . '/' . $item)) continue;

                $languages .= '<span class="flag">';
                $languages .= '<a href="' . BD_BASE_URL . '/index.php?c=' . $c . '&p=' . $p . '&lang=' . $item . '">';
                $languages .= '<img src="' . BD_BASE_URL . '/assets/shared/img/' . $item . '.png" border="0" />';
                $languages .= '</a>';
                $languages .= '</span>';
            }
        }

        return $languages;
    }


    /**
     * Get the directory structure.
     */
    protected function getStructure()
    {
        $this->structureArray = $this->structure->get();
    }


    /**
     * Render the wiki page the user is trying to load.
     */
    public function wiki()
    {
        if (file_exists($this->file)) {
            $fileData = file_get_contents($this->file);
            $data = $this->markdown->transform($fileData);
            $data = $this->getInnerHeadings($data);
            $page = 'page';
        } else {
            $data = '';
            $innerStructure = '';
            $page = 'error';
        }

        $changes = array(
            'content' => $data,
            'innerHeadings' => $this->internalHeadings,
        );

        $formatted = $this->view
            ->setPage($page)
            ->setChanges(array_merge($this->getDefaultChanges(), $changes))
            ->render();

        $this->output = $formatted;

        return $this;
    }


    /**
     * Builds a list of page headings for easy access and internal linking.
     *
     * @param   string  $fileData   The page's raw (pre-markdown processed) data.
     *
     * @return  string  Final page with added <a name> tags.
     */
    public function getInnerHeadings($fileData)
    {
        $this->internalHeadings = '<ul id="innerHeadings">';

        $finalPage = '';

        $found = false;

        $exp = explode("\n", $fileData);

        foreach ($exp as $line) {

            $aName = uniqid();

            $check = substr(trim($line), 0, 4);

            if ($check == '<h4>') {
                $found = true;
                $title = trim(substr($line, 4));
                $this->internalHeadings .= '<li class="h4"><a href="#' . $aName . '">' . $title . '</a></li>';
                $finalPage .= '<a class="anchor" name="' . $aName . '"></a>' . "\n" . $line . "\n";
            }
            else if ($check == '<h3>') {
                $found = true;
                $title = trim(substr($line, 4));
                $this->internalHeadings .= '<li class="h3"><a href="#' . $aName . '">' . $title . '</a></li>';
                $finalPage .= '<a class="anchor" name="' . $aName . '"></a>' . "\n" . $line . "\n";
            }
            else if ($check == '<h2>') {
                $found = true;
                $title = trim(substr($line, 4));
                $this->internalHeadings .= '<li class="h2"><a href="#' . $aName . '">' . $title . '</a></li>';
                $finalPage .= '<a class="anchor" name="' . $aName . '"></a>' . "\n" . $line . "\n";
            }
            else if ($check == '<h1>') {
                $found = true;
                $title = trim(substr($line, 4));
                $this->internalHeadings .= '<li class="h1"><a href="#' . $aName . '">' . $title . '</a></li>';
                $finalPage .= '<a class="anchor" name="' . $aName . '"></a>' . "\n" . $line . "\n";
            }
            else {
                $finalPage .= $line . "\n";
            }

        }

        $this->internalHeadings .= '</ul>';

        if (! $found) $this->internalHeadings = '';

        return $finalPage;
    }


    /**
     * Perform a search request.
     *
     * @param   string  $query
     *
     * @return  string
     */
    public function search($query)
    {
        $search = new Search();
        $find = $search->setQuery($query)
            ->run();

        $changes = array(
            'results' => $find->format(),
            'query' => $query,
            'breadcrumbs' => '&nbsp;',
            'count' => $find->count(),
        );

        $formatted = $this->view
            ->setPage('search')
            ->setChanges(array_merge($this->getDefaultChanges(), $changes))
            ->render();

        $this->output = $formatted;

        return $this;
    }

}