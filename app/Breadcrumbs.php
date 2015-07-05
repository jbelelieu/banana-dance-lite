<?php namespace App;

/**
 * Generates breadcrumbs for use within the application's views.
 *
 * @author      jbelelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */

class Breadcrumbs {


    /**
     * @var string
     */
    private $page;

    /**
     * @var string
     */
    private $category = '';

    /**
     * @var string
     */
    private $separator = ' &raquo; ';



    /**
     * Set the current page.
     *
     * @param   string  $page
     *
     * @return  $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }


    /**
     * Set the current category.
     *
     * @param   string  $category
     *
     * @return  $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }


    /**
     * Build the breadcrumbs.
     *
     * @return  string
     */
    public function build()
    {
        $format = array();

        $pieces = explode('/', $this->category);

        $pieces[] = $this->page;

        $temp = array();

        foreach ($pieces as $item) {
            if (empty($item)) continue;

            $temp[] = $item;

            $checkForName = ltrim(implode('/', $temp), '/');

            $format[] = \App\findName($checkForName);
        }

        $ret = '<a href="' . \App\getBaseUrl() . '">' . BD_NAME . '</a>';

        if (sizeof($format) == 1) {
            return $ret . $this->separator . $format['0'];
        } else {
            return $ret . $this->separator . implode($this->separator, $format);
        }
    }

}