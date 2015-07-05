<?php namespace App;

/**
 * Light-weight view generation tool.
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
     * Set the view (template) we are working with.
     *
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
     * Set the custom variables that will be available on the template.
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
     * Force a change into the set of available view replacements.
     *
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
     * Get the contents of a view file.
     *
     * @param   string  $view
     *
     * @return  string
     */
    private function getView($view)
    {
        $theFile = dirname(dirname(__FILE__)) . '/app/views/' . BD_THEME . '/' . \App\getLanguage() . '/' . $view . '.phtml';

        if (! file_exists($theFile)) {
            $theFile = dirname(dirname(__FILE__)) . '/app/views/' . BD_THEME . '/en/' . $view . '.phtml';
        }

        ob_start();
        $var = $this->changes;
        include($theFile);
        $content = ob_get_contents();
        ob_end_clean();

        // We need to process the content variable before
        // we process everything else.
        if (isset($this->changes['content'])) {
            $content = str_replace('{{ content }}', $this->changes['content'], $content);
        }

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
        $finalChanges = $this->appendFixedVariables($this->changes);

        preg_match_all('#\{\{(.*?)\}\}#', $content, $matches);

        foreach ($matches[0] as $variable) {
            $clean = str_replace('{', '', $variable);
            $clean = trim(str_replace('}', '', $clean));

            if (array_key_exists($clean, $finalChanges)) {
                $content = str_replace($variable, $finalChanges[$clean], $content);
            }
        }

        return $content;
    }


    /**
     * @param   array   $changes
     *
     * @return  bool
     */
    private function appendFixedVariables(array $changes)
    {
        $lang = \App\getLanguage();

        $fixed = include dirname(dirname(__FILE__)) . '/wiki/config/custom_variables.php';

        if (empty($fixed[$lang])) return $changes;

        $useFixed = $fixed[$lang];

        // If the user requested a language other than english but there
        // are no entries for that language, try to default to english.
        if (empty($useFixed) && $lang != BD_DEFAULT_LANGUAGE) {
            if (empty($fixed[BD_DEFAULT_LANGUAGE])) return false;

            $useFixed = $fixed[BD_DEFAULT_LANGUAGE];
        }

        if (! empty($useFixed)) {
            return array_merge($changes, $useFixed);
        } else {
            return $changes;
        }
    }

}