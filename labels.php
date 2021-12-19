<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!\defined('_PS_VERSION_')) {
    exit;
}

class Labels extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name          = 'labels';
        $this->tab           = 'administration';
        $this->version       = '1.0.0';
        $this->author        = 'Roman Matviy https://roman.matviy.pp.ua';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('labels');
        $this->description = $this->l('category labels https://roman.matviy.pp.ua');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('LABELS_LIVE_MODE', false);

        /**
         * Create New Tab
         */

        $tab             = new Tab();
        $tab->class_name = 'AdminCategoryLabels';
        $tab->module     = $this->name;
        $tab->id_parent  = 2;
        $tab->icon       = 'star';
        $tab->name[1]    = $this->l('Labels');
        $tab->active     = 1;
        if (!$tab->save()) {
            return false;
        }

        // include __DIR__ . '/sql/install.php';
        include dirname(__FILE__)  . '/sql/install.php';

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayFooterAfter') &&
            $this->registerHook('displayHeaderCategory') &&
            $this->registerHook('displayLeftColumn');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LABELS_LIVE_MODE');

        // include __DIR__ . '/sql/uninstall.php';
        include dirname(__FILE__)  . '/sql/uninstall.php';

        return parent::uninstall();
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab             = new Tab();
        $tab->active     = 1;
        $tab->class_name = $className;
        $tab->name       = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $tab->id_parent = (int)Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;
        return $tab->add();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitLabelsModule')) === true) {
            $this->postProcess();
        }

        $this->context->smarty->assign([
            'module_dir' => $this->_path,
        ]);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->module                   = $this;
        $helper->default_form_language    = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitLabelsModule';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminCategoryLabels', true)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminCategoryLabels');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon'  => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type'    => 'switch',
                        'label'   => $this->l('Live mode'),
                        'name'    => 'LABELS_LIVE_MODE',
                        'is_bool' => true,
                        'desc'    => $this->l('Use this module in live mode'),
                        'values'  => [
                            [
                                'id'    => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id'    => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Enter a valid email address'),
                        'name'   => 'LABELS_ACCOUNT_EMAIL',
                        'label'  => $this->l('Email'),
                    ],
                    [
                        'type'  => 'password',
                        'name'  => 'LABELS_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'LABELS_LIVE_MODE'        => Configuration::get('LABELS_LIVE_MODE', true),
            'LABELS_ACCOUNT_EMAIL'    => Configuration::get('LABELS_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'LABELS_ACCOUNT_PASSWORD' => Configuration::get('LABELS_ACCOUNT_PASSWORD', null),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        // if (Tools::getValue('module_name') == $this->name) {
        $this->context->controller->addJS($this->_path . 'views/js/back.js');
        $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        // }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    /**
     * Add the hookDisplayHeaderCategory on the FO.
     */
    public function hookDisplayHeaderCategory()
    {
        $id_shop     = (int)Context::getContext()->shop->id;
        $id_category = (int)Tools::getValue('id_category');

        if (_PS_VERSION_ >= '1.7.8') {
            $this->context->smarty->assign([
                'module_dir' => $this->_path,
                'label_info' => $this->getLabelInfo($id_category),
            ]);
            $id_shop;
            $output = $this->context->smarty->fetch($this->local_path . 'views/templates/front/label.tpl');
            return $output;
        }
    }

    /**
     * Add the copyright on the FO.
     */
    public function hookDisplayFooterAfter($params)
    {
        return '<a href="https://roman.matviy.pp.ua">Roman Matviy</a>';
    }

    /**
     * Add the hookDisplayLeftColumn on the FO.
     */
    public function hookDisplayLeftColumn($params)
    {
        $id_shop     = (int)Context::getContext()->shop->id;
        $id_category = (int)Tools::getValue('id_category');

        if (_PS_VERSION_ <= '1.7.7') {
            $this->context->smarty->assign([
                'module_dir' => $this->_path,
                'label_info' => $this->getLabelInfo($id_category),
            ]);
            $id_shop;
            $output = $this->context->smarty->fetch($this->local_path . 'views/templates/front/label.tpl');
            return $output;
        }
    }

    /**
     * get id category
     * return category id
     */
    public static function categoryLabelFind($id_category)
    {
        $sql = 'SELECT id_category FROM ' . _DB_PREFIX_ . 'labels WHERE id_category = ' . (int)$id_category;
        return Db::getInstance()->getValue($sql);
    }

    /**
     * get id category
     * return label info
     */
    public static function getLabelInfo($id_category)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'labels WHERE id_category = ' . (int)$id_category;

        if (Db::getInstance()->getRow($sql)) {
            return Db::getInstance()->getRow($sql);
        }
    }

    /**
     * get id label
     * return id category
     */
    public static function labelFind($id_label)
    {
        $sql = 'SELECT id_category FROM ' . _DB_PREFIX_ . 'labels WHERE id_labels = ' . (int)$id_label;
        return Db::getInstance()->getValue($sql);
    }

    /**
     * get id category
     * return category name
     */
    public static function getCategoryName($id_category)
    {
        Category::getCategories();
        Category::getCategoryInformation([$id_category]);
        Category::getCategoryInformations([$id_category]);

        $result = Category::getCategoryInformation([$id_category]);
        $name   = $result[$id_category]['name'];
        return $name;
    }
}
