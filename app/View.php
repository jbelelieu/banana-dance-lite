<?php namespace App;

/**
 * Generates a view.
 * 
 * @author      jbelelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */
class View {


    /**
     * @var     string  The view we are loading.
     */
    private $file = 'page';


    /**
     * @var     array   Dynamic substitutions for use within the view.
     */
    private $changes = array();



    /**
     *
     */
    public function __construct()
    {
        $this->setPage('page');
    }


    /**
     * @param   string  $view
     *
     * @return  $this
     */
    public function setPage($view)
    {
        $this->file = $view;

        return $this;
    }



    /**
     *
     *
     * @param   array   $changes
     *
     * @return  $this
     */
    public function setChanges(array $changes)
    {
        $this->changes = array_merge($this->changes, $changes);

        return $this;
    }


    /**
     * @param   string  $key
     * @param   string  $value
     */
    public function addChange($key, $value)
    {
        $this->changes[$key] = $value;
    }


    /**
     * Render the file page.
     */
    public function render()
    {
        $content = $this->getView('header') .
            $this->getView($this->file) .
            $this->getView('footer');

        return $this->replacements($content);
    }


    /**
     * @param   string  $view
     *
     * @return  string
     */
    private function getView($view)
    {
        $theFile = dirname(dirname(__FILE__)) . '/app/views/' . BD_THEME . '/' . \App\getLanguage() . '/' . $view . '.phtml';

        if (! file_exists($theFile))
            $theFile = dirname(dirname(__FILE__)) . '/app/views/' . BD_THEME . '/en/' . $view . '.phtml';

        ob_start();
        include($theFile);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }


    /**
     * Make requested changes to the view. Changes are determined by what is available in the changes array.
     *
     * @param   string  $content
     *
     * @return  string
     */
    private function replacements($content)
    {
        preg_match_all('#\{\{(.*?)\}\}#', $content, $matches);

        foreach ($matches[0] as $variable) {
            $clean = str_replace('{', '', $variable);
            $clean = trim(str_replace('}', '', $clean));

            if (array_key_exists($clean, $this->changes))
                $content = str_replace($variable, $this->changes[$clean], $content);
        }

        // Re-do for fixed variables.
        $fixed = include dirname(dirname(__FILE__)) . '/app/config/custom_variables.php';

        if (! empty($fixed)) {
            preg_match_all('#\{\{(.*?)\}\}#', $content, $matches);

            foreach ($matches[0] as $variable) {
                $clean = str_replace('{', '', $variable);
                $clean = trim(str_replace('}', '', $clean));

                if (array_key_exists($clean, $fixed))
                    $content = str_replace($variable, $fixed[$clean], $content);
            }
        }

        return $content;
    }

}