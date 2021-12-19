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

require_once _PS_ROOT_DIR_ . '/modules/labels/class/CategoryLabelsClass.php';

class AdminCategoryLabelsController extends ModuleAdminController
{
    protected $position_identifier = 'id_labels';

    public function __construct()
    {
        parent::__construct();
        $id_lang                   = $this->context->employee->id_lang;
        $this->bootstrap           = true;
        $this->required_database   = false;
        $this->lang                = false;
        $this->table               = 'labels';
        $this->_select             = 'a.*,c.name as category_name';
        $this->_join               = 'LEFT JOIN ' . _DB_PREFIX_ . 'category_lang c ON a.id_category = c.id_category';
        $this->_defaultOrderBy     = 'a.id_labels';
        $this->_defaultOrderWay    = 'ASC';
        $this->orderBy             = 'a.id_labels';
        $this->orderWay            = 'desc';
        $this->identifier          = 'id_labels';
        $this->className           = 'CategoryLabelsClass';
        $this->filter              = 'id_labels';
        $this->position_identifier = 'id_category';

        $this->_group              = 'GROUP BY a.id_labels';
        $this->where               = 'b.id_lang =' . (int)$id_lang;
        $this->list_no_link        = false;
        $this->context             = Context::getContext();
        $this->allow_export        = true;
        $this->addressType         = 'id_label';
        $this->explicitSelect      = false;

        $categories = Category::getCategories($this->context->language->id, false, false);

        $clearCategories = [];

        foreach ($categories as $key => $value) {
            if ($value['id_category'] === labels::categoryLabelFind($value['id_category'])) {
                $key;
                continue;
            }
            $clearCategories[] = $value;
        }

        $values      = [
            [
                'id'    => 'active_on',
                'value' => 1,
                'label' => $this->l('Yes'),
            ],
            [
                'id'    => 'active_off',
                'value' => 0,
                'label' => $this->l('No'),
            ],
        ];

        $getUrl = $_SERVER['REQUEST_URI'];
        if (mb_strpos($getUrl, 'updatelabels') !== false) {
            $html_content = $this->trans('Edit label', [], 'Admin.Global');
            $id_label     = Tools::getValue('id_labels');

            if (\count($clearCategories) === 0) {
                $clearCategories =  Category::getCategoryInformation([Labels::labelFind($id_label)]);
            } else {
                $this->addressType         = 'id_label';
                $this->explicitSelect      = false;

                $categories = Category::getCategories($this->context->language->id, false, false);

                $clearCategories = [];

                foreach ($categories as $key => $value) {
                    if ($value['id_category'] === labels::categoryLabelFind($value['id_category'])) {
                        $key;
                        continue;
                    }
                    $clearCategories[] = $value;
                }
                $clearCategories += Category::getCategoryInformation([Labels::labelFind($id_label)]);
            }
        } else {
            $html_content = $this->trans('Add new label', [], 'Admin.Global');
        }

        $sql              = 'SELECT COUNT(id_labels) FROM ' . _DB_PREFIX_ . 'labels';
        $countLabels      = (int)Db::getInstance()->getValue($sql);

        $sql                  = 'SELECT COUNT(id_category) FROM ' . _DB_PREFIX_ . 'category';
        $countCategories      = (int)Db::getInstance()->getValue($sql);

        if ($countLabels !== $countCategories ||
            mb_strpos($getUrl, 'updatelabels') !== false ||
            !\count($clearCategories) === 0
            ) {
            $this->fields_form = [
                'legend' => [
                    'title' => 'Labels',
                    'icon'  => 'icon-star',
                ],
                'input' => [
                    [
                        'name'    => 'content',
                        'type' => 'html',
                        'html_content' => '<div class="alert alert-info">' . $html_content . '</div>'
                    ],

                    [
                        'name'    => 'name',
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'required' => true,
                        'suffix' => 'Label name',
                    ],

                    [
                        'name'    => 'color',
                        'class' => 'col-12',
                        'type' => 'color',
                        'label' => $this->l('Color'),
                        'required' => true,
                        'desc'     =>  $this->l('Example color') . ' #000000'
                    ],

                    [
                        'name'    => 'id_category',
                        'type'    => 'select', 'label' => $this->l('Category'), 'required' => true, 'class' => 'select',
                        'options' => [
                            'query'              => $clearCategories,
                            'id'                 => 'id_category',
                            'name'               => 'name',
                        ],
                    ],
                    ['name'    => 'active',
                     'type' => 'switch',
                     'label' => $this->l('Active'),
                     'is_bool' => true,
                     'values' => $values,
                     'required' => false],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ];
        } else {
            $html_content = $this->trans('All categories already have labels', [], 'Admin.Global');
            $link         = new Link();

            $this->fields_form = [
                'legend' => [
                    'title' => 'Labels',
                    'icon'  => 'icon-star',
                ],
                'input' => [
                    ['name'    => 'content',
                    'type' => 'html',
                    'html_content' => '<div class="alert alert-warning">' . $html_content . '</div>'],
                ],

                'buttons' => [
                    'save-and-stay' => [
                        'title' => $this->l('Back to list'),
                        'type'  => 'cancel',
                        'class' => 'btn btn-default pull-right',
                        'icon'  => 'process-icon-save',
                        'href'  => $link->getAdminLink('AdminCategoryLabels'),
                        'desc'  => $this->l('Back to list'),
                    ],
                ],
            ];
        }

        parent::__construct();
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = [
            'id_labels' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'category_name'  => ['title' => $this->trans('Category name', [], 'Admin.Global'), ],
            'id_category'    => ['title' => $this->trans('ID Category', [], 'Admin.Global'), ],
            'name'           => ['title' => $this->trans('Name', [], 'Admin.Global'), 'filter_key' => 'a!name'],
            'color'          => ['title' => $this->trans('Color', [], 'Admin.Global'), 'type' => 'text', ],
            'active'         => ['title' => $this->trans('Active', [], 'Admin.Global'), 'type' => 'bool', ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'icon'    => 'icon-trash text-success',
                'text'    => $this->l('Delete selection'),
                'confirm' => $this->l('Delete selected items?'),
            ],
            // 'enableSelectedCustom' => array(
            //     'text' => $this->l('Enable selection'),
            //     'icon' => 'icon-power-off text-success',
            // ),
        ];

        // $this->bulk_actions = false;

        parent::__construct();
    }

    public function processBulkEnableSelectedCustom()
    {
        exit;
    }

    public function initContent()
    {
        $this->renderView();
        return parent::initContent();
    }

    public function renderView()
    {
        $id_label = (int)Tools::getValue('id_labels');
        if (!$id_label && Validate::isLoadedObject($this->object)) {
            $id_label = $this->object->id_label;
        }

        $db              = Db::getInstance();
        $id_lang         = $this->context->employee->id_lang;
        $id_shop_default = (int)$this->context->shop->id;
        $sql             = 'SELECT * FROM ' . _DB_PREFIX_ . 'labels WHERE id_labels=' . (int)$id_label;

        $labelInfo = $db->executeS($sql);

        if ($labelInfo) {
            $this->context->smarty->assign(
                [
                    'id_label'                => $id_label,
                    'labelInfo'               => $labelInfo[0],
                    'url'                     => __PS_BASE_URI__,
                    'id_lang'                 => $id_lang,
                    'id_shop_default'         => $id_shop_default,
                    'category_tree'           => $this->getTreeCategories(),
                ]
            );
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'labels/views/templates/admin/labels.tpl');
        }
    }

    /**
     * Render tree.
     */
    public function getTreeCategories()
    {
        $categories  = [];

        $tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories');
        $tree->setTemplate('tree_associated_categories.tpl')
            ->setHeaderTemplate('tree_associated_header.tpl')
            ->setRootCategory(0)
            ->setUseCheckBox(false)
            ->setUseSearch(true)
            ->setSelectedCategories($categories);
        return $tree->render();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/css/admin/admin.css');
        $this->addJS(__PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/admin/admin.js');
    }
}
