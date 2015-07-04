<?php namespace App;

/**
 * Very simple class the browses the "wiki" folder and creates
 * an array which will be used to create the navigation.
 *
 * @author      jbelelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */

class Structure {

    /**
     * @var array
     */
    private $nav = array();

    /**
     * @var array
     */
    private $orderMap = array();

    /**
     * @var string
     */
    private $link;

    /**
     *
     */
    public function __construct($page, $category = '')
    {
        $this->link = (! empty($category)) ? $category . '/' . $page : $page;

        $wikiDir = dirname(dirname(__FILE__)) . '/wiki/' . \App\getLanguage();

        $this->orderMap = include dirname(dirname(__FILE__)) . '/app/config/structure_order.php';

        $this->nav = $this->build($wikiDir);
    }


    /**
     * Get the formatted array.
     *
     * @return  array
     */
    public function get()
    {
        return $this->nav;
    }


    /**
     *
     */
    public function getHtml()
    {
        if (empty($this->nav)) return '';

        return $this->buildUl($this->nav);
    }


    /**
     * Build the array that will contain the structure of the wiki.
     *
     * @param   string  $dir
     *
     * @return  array
     */
    private function build($dir)
    {
        $contents = array();

        foreach (scandir($dir) as $node) {

            if (substr($node, 0, 1) == '.')  continue;

            $name = $this->cleanName($node);
            $path = $dir . DIRECTORY_SEPARATOR . $node;

            $componentArray = $this->buildLinkComponents($path);

            if (is_dir($dir . DIRECTORY_SEPARATOR . $node)) {
                $contents[$node] = $this->build($path); // $name
            } else {
                $contents[$node] = \App\buildLink($componentArray, $node);
            }

        }

        $contents = $this->sortArray($contents);

        return $contents;
    }


    /**
     * Builds a "<ul>" element for use within the views.
     *
     * @param   $array
     *
     * @return  string
     */
    private function buildUl($array)
    {
        $output = array();

        $out = "<ul>";

        foreach($array as $key => $elem) {
            if (! is_array($elem)) {
                parse_str($elem, $output);

                if (! empty($output['?c'])) {
                    $check = $output['?c'] . '/' . $output['p'];
                } else {
                    $check = $output['p'];
                }

                if ($check == $this->link) {
                    $class = 'active';
                } else {
                    $class = '';
                }

                $key = \App\findName($check);

                $out .= "<li class=\"$class\"><a href=\"" . $elem . "\">" . $key . "</a></li>";
            }
            else {
                $out .= "<li><span class=\"subTitle\">" . $key . "</span>" . $this->buildUl($elem) . "</li>";
            }
        }

        $out .= "</ul>";

        return $out;
    }


    /**
     * Sorts the array according to the order map.
     *
     * @param   array   $array
     *
     * @return  array
     */
    private function sortArray(array $array)
    {
        if (empty($this->orderMap)) return $array;

        $ordered = array();

        foreach($this->orderMap as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }

        return $ordered + $array;
    }


    /**
     * Clean the name of the page.
     *
     * @param   string  $name
     *
     * @return  string
     */
    private function cleanName($name)
    {
        $exp = explode('.', $name);

        array_pop($exp);

        return str_replace('_', ' ', implode('.', $exp));
    }



    /**
     * Builds an array of items that make up the category and page.
     *
     * @param   string   $category
     *
     * @return  array
     */
    private function buildLinkComponents($category)
    {
        $build = array();

        $dirs = array_reverse(explode('/', $category));

        $lang = \App\getLanguage();

        foreach ($dirs as $item) {
            // We don't care about anything before the language folder.
            if ($item == $lang) break;

            $build[] = $item;
        }

        array_shift($build);

        return array_reverse($build);
    }

}