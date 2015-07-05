<?php namespace App;

/**
 * Primary "Controller" for the application.
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

        $this->structure = new Structure($page, $category);
        $this->breadcrumbs = new Breadcrumbs();
        $this->view = new View();
        $this->parsePage = new ParsePage();

        $this->buildRequest();
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
     * Set the page we are requesting.
     *
     * @param   string  $page
     *
     * @return  $this
     */
    public function setPage($page)
    {
        $this->page = htmlentities($page);

        return $this;
    }


    /**
     * Set the category of the requested page.
     *
     * @param   string  $category
     *
     * @return  $this
     */
    public function setCategory($category)
    {
        $this->category = htmlentities($category);

        return $this;
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
        $lang = substr($lang, 0, 2);

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
        $base = \App\getBaseUrl();

        return array(
            'assets' => $base . '/app/views/' . BD_THEME . '/assets',
            'wiki_name' => BD_NAME,
            'wiki_theme' => BD_THEME,
            'wiki_base_url' => $base,
            'branding_color' => BD_BRANDING_COLOR,
            'page' => $this->page,
            'category' => $this->category,
            'currentLanguage' => \App\getLanguage(),
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
        $languages = array();

        $scan = dirname(dirname(__FILE__)) . '/wiki';

        foreach (scandir($scan) as $item) {
            if (is_dir($scan . '/' . $item) && strlen($item) == 2 && $item != '.' && $item != '..') {
                $languages[] = $item;
            }
        }

        return $languages;
    }


    /**
     * Render the wiki page the user is trying to load.
     */
    public function wiki()
    {
        if (file_exists($this->file)) {
            $data = $this->parsePage
                ->setRawData(file_get_contents($this->file))
                ->process()
                ->getContent();

            $timestamps = \App\getTimeStamps($this->file);

            $page = 'page';
        } else {
            $data = '';
            $timestamps = '';
            $page = 'error';
        }

        $changes = array(
            'content' => $data,
            'innerHeadings' => $this->parsePage->getHeadings($data),
        );

        if (! empty($timestamps)) $changes = array_merge($changes, $timestamps);

        $formatted = $this->view
            ->setPage($page)
            ->setChanges(array_merge($this->getDefaultChanges(), $changes))
            ->render();

        $this->output = $formatted;

        return $this;
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