{*
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
*}

<script>
    var linkAdminCategoryLabels = "{$link->getAdminLink('AdminCategoryLabels')|escape:'htmlall':'UTF-8'}";
</script>

<div class="row">
    <div class="col-lg-12">

        <div class="panel" id="label-info">
            <div class="panel-heading">
                <i class="icon-star"></i>{l s='Label Info' mod='labels'}
            </div>

            <div class="label-wrapper">
                <div class="form-group">

                    <div class="text col-12">
                        {l s='ID label' mod='labels'}: {$labelInfo['id_labels']|escape:'htmlall':'UTF-8'}
                    </div>
                    <div class="name col-12">
                        {l s='Label name' mod='labels'}: {$labelInfo['name']|escape:'htmlall':'UTF-8'}
                    </div>
                    <div class="name col-12">
                        {l s='Label color' mod='labels'}: {$labelInfo['color']|escape:'htmlall':'UTF-8'}
                        <input type="color" class="pl-2 d-inline-block" name="color" id="BackgroundColor"
                               value="{$labelInfo['color']|escape:'htmlall':'UTF-8'}" data-hex="true"
                               onclick="return false;" />
                        <div class="color-show d-none"></div>
                    </div>
                    <div class="text form-group col-12">
                        <label class="control-label col-3">
                            {l s='Label active' mod='labels'}:
                            {* {$labelInfo['active']} *}
                        </label>
                        <div class="col-3">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="active" id="active_on" value="1" {if $labelInfo['active']}
                                       checked="checked" {/if} onclick="return false;">
                                <label for="active_on">Yes</label>
                                <input type="radio" name="active" id="active_off" value="0" {if !$labelInfo['active']}
                                       checked="checked" {/if} onclick="return false;">
                                <label for="active_off">No</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>

                    </div>

                    <div class="text col-12">
                        {l s='ID category' mod='labels'}: {$labelInfo['id_category']|escape:'htmlall':'UTF-8'}
                    </div>
                    <div class="text col-12">
                        {l s='Category name' mod='labels'}:
                        {Labels::getCategoryName($labelInfo['id_category'])|escape:'htmlall':'UTF-8'}
                    </div>
                </div>
                <div class="col-12">
                    <b>{l s='Example label' mod='labels'}</b>: <span
                          style="border: 1px solid #000; background-color: {$labelInfo['color']|escape:'htmlall':'UTF-8'}; padding: 8px; border-radius: 5px; color: white;
                          text-shadow: -1px -1px 0 #0008, 1px -1px 0 #0008, -1px 1px 0 #0008, 1px 1px 0 #0008;">{$labelInfo['name']|escape:'htmlall':'UTF-8'}</span>
                </div>

            </div><!-- /.label-wrapper -->

            <div class="panel-footer">
                <a href=" {$link->getAdminLink('AdminCategoryLabels')|escape:'htmlall':'UTF-8'}" class="btn btn-default"
                   id="labels_form_cancel_btn" onclick="javascript:window.history.back();">
                    <i class="process-icon-cancel"></i>
                    {l s='Back' mod='labels'}
                </a>
            </div>

        </div>
    </div>
</div>

{* {$category_tree} *}