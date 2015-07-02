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


    private $page;

    private $category;

    private $separator = ' &raquo; ';



    /**
     * @param $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }


    /**
     * @param $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }


    /**
     * @return string
     */
    public function build()
    {
        $format = array();

        $pieces = explode('/', $this->category);

        $pieces[] = $this->page;

        $temp = array();

        $up = 0;
        foreach ($pieces as $item) {
            $up++;

            $temp[] = $item;

            $finalItem = '';

            if ($up == sizeof($pieces)) $finalItem .= '<a href="' . BD_BASE_URL . '?c=' . $this->category . '&p=' . $this->page . '">';

            $checkForName = implode('/', $temp);

            $finalItem .= \App\findName($checkForName);

            if ($up == sizeof($pieces)) $finalItem .= '</a>';

            $format[] = $finalItem;
        }

        $ret = '<a href="' . BD_BASE_URL . '">' . BD_NAME . '</a>' . $this->separator;

        // Homepage
        if (sizeof($format) == 2) {
            return $ret . $format['1'];
        } else {
            return $ret . implode($this->separator, $format);
        }
    }

}