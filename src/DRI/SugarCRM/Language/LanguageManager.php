<?php

namespace DRI\SugarCRM\Language;

use DRI\SugarCRM\Module\Vardefs\VardefManager;

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
     * @param bool $language
     * @param bool $refresh
     *
     * @return array
     */
    public function getAppListStrings($language = false, $refresh = false)
    {
        if ($refresh) {
            $cache_key = 'app_list_strings.'.$language;
            sugar_cache_clear($cache_key);
        }

        $app_list_strings = return_app_list_strings_language($language);

        return $app_list_strings;
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
            $vName = str_replace('USDOLLAR', 'USD', $vName);
        }

        return $this->translateLabel($vName);
    }

    public function translateLabel($label)
    {
        $label = preg_replace('/^LBL_/', '', $label);
        $label = strtolower($label);
        $parts = explode('_', $label);
        $parts = array_map('ucfirst', $parts);
        $translation = implode(' ', $parts);

        return $translation;
    }

    /**
     * @param $module
     * @param string $language
     * @param bool   $local
     * @param array  $baseLanguages
     *
     * @return array
     */
    public function getMissingLabelsInModule($module, $language, $local, $baseLanguages)
    {
        $labels = $this->getModuleLanguage($module, $language, true);
        $bean = \BeanFactory::getBean($module);

        $missingLabels = array();

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
     * @param $module
     * @param $language
     * @param $baseLanguages
     *
     * @return array
     */
    public function getMissingEnumListsInModule($module, $language, $baseLanguages)
    {
        $vardefManager = new VardefManager($module);

        $enumFields = $vardefManager->getFieldsByTypes(array('enum', 'multienum'));
        $app = $this->getAppListStrings($language, true);

        $missing = array();

        foreach ($enumFields as $field) {
            if (!isset($field['options'])) {
                continue;
            }

            if (!isset($app[$field['options']])) {
                $missing[$field['options']] = array(
                    '' => '',
                );
            }
        }

        foreach ($baseLanguages as $baseLanguage) {
            $app = $this->getAppListStrings($baseLanguage, true);

            foreach ($missing as $options => $list) {
                if (isset($app[$options])) {
                    $missing[$options] = array_merge($missing[$options], $app[$options]);
                }
            }
        }

        return $missing;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getLanguagesBasedOnOptions(array $options)
    {
        foreach ($options as $option) {
            switch ($option) {
                case 'current';

                    return array($this->getCurrent());
                    break;
                case 'active';

                    return $this->getActive();
                    break;
                case 'default';

                    return array($this->getDefault());
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
