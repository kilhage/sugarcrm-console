<?php

namespace DRI\SugarCRM\Language;

/**
 * @author Emil Kilhage
 */
class LanguageManager
{

    /**
     * @return mixed
     */
    public function getCurrent()
    {
        return $GLOBALS['current_language'];
    }

    /**
     * @param $language
     */
    public function setCurrent($language)
    {
        $GLOBALS['current_language'] = $language;
    }

    /**
     * @return array
     */
    public function getActive()
    {
        return array_keys(\SugarConfig::getInstance()->get('languages'));
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return \SugarConfig::getInstance()->get('default_language');
    }

    /**
     * @param $language
     */
    public function setDefault($language)
    {
        \SugarConfig::getInstance()->clearCache('default_language');
        $GLOBALS['sugar_config']['default_language'] = $language;
    }

    /**
     * @param $module
     * @param bool $language
     * @param bool $refresh
     *
     * @return array
     */
    public function getModuleLanguage($module, $language = false, $refresh = false)
    {
        $language = $language ?: $this->getCurrent();
        return return_module_language($language, $module, $refresh);
    }

    /**
     * @param array $def
     *
     * @return string
     */
    public function translateFieldBasedOnDefinition(array $def)
    {
        $vName = $def['vname'];

        if ($def['type'] == 'currency') {
            $vName = str_replace("USDOLLAR", "USD", $vName);
        }

        return $this->translateLabel($vName);
    }

    public function translateLabel($label)
    {
        $label = preg_replace('/^LBL_/', '', $label);
        $label = strtolower($label);
        $parts = explode("_", $label);
        $parts = array_map('ucfirst', $parts);
        $translation = implode(" ", $parts);

        return $translation;
    }

    /**
     * @param $module
     * @param string $language
     * @param bool $local
     * @param array $baseLanguages
     *
     * @return array
     */
    public function getMissingLabelsInModule($module, $language, $local, $baseLanguages)
    {
        $labels = $this->getModuleLanguage($module, $language, true);
        $bean = \BeanFactory::getBean($module);

        $missingLabels = array ();

        foreach ($bean->getFieldDefinitions() as $name => $def) {
            if (!empty($def['vname'])) {
                $vName = $def['vname'];
                if ($local) {
                    if (empty($labels[$vName])) {
                        $missingLabels[$vName] = $this->translateFieldBasedOnDefinition($def);
                    }
                } else {
                    $translation = translate($vName, $module);
                    if ($translation === $vName) {
                        $missingLabels[$vName] = $this->translateFieldBasedOnDefinition($def);
                    }
                }
            }
        }

        foreach ($baseLanguages as $baseLanguage) {
            $baseLabels = $this->getModuleLanguage($module, $baseLanguage, true);

            foreach ($baseLabels as $label => $translation) {
                if (!isset($labels[$label]) && !isset($missingLabels[$label])) {
                    $missingLabels[$label] = $this->translateLabel($label);
                }
            }
        }

        return $missingLabels;
    }

    /**
     * @param array $options
     * @return array
     */
    public function getLanguagesBasedOnOptions(array $options)
    {
        foreach ($options as $option) {
            switch ($option) {
                case 'current';
                    return array ($this->getCurrent());
                    break;
                case 'active';
                    return $this->getActive();
                    break;
                case 'default';
                    return array ($this->getDefault());
                    break;
            }
        }

        return $options;
    }

    /**
     * @param $module
     * @param $label
     *
     * @return string
     */
    public function translate($module, $label)
    {
        $translation = translate($label, $module);

        return $translation;
    }

}
