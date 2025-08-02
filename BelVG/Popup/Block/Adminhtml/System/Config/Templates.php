<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   BelVG
 * @package    BelVG_Popup
 * @copyright  Copyright (c) BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
namespace BelVG\Popup\Block\Adminhtml\System\Config;

/**
 * Class Templates
 * @package BelVG\Popup\Block\Adminhtml\System\Config
 */
class Templates extends \Magento\Config\Block\System\Config\Form\Field {
    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->_getParentElementHtml($element);
            $html .= "<br/><br/>";
            $html .= $this->_getElementHtml($element);
            $html .= "<br/><br/>";
            $html .= '<button  class="promo-popup-apply" ><span><span><span>Apply template</span></span></span></button>';
            $html .="
            <style>
            table.mceToolbar {
                width:auto !important;
            }
            </style>
            <script>
                require(['jquery'], function($) {
                    $(document).ready(function(){
                       $('#promopopup_settings_type').trigger('change');
                       
                    });
                    
                    $('#promopopup_settings_type').change(function(){
                        var parentValue =  $(this).val();
                        var element = $('#parent_promopopup_settings_templates');
                      
                        element .find('option').removeAttr('selected').hide();
                        element .find('option[data-typeid='+parentValue+']').show();
                        element .find('option[data-typeid='+parentValue+']:first').attr('selected','selected');
                       
                        $('#parent_promopopup_settings_templates').trigger('change');
                    
                    });
                    
                    $('#parent_promopopup_settings_templates').change(function(){
                        var parentValue =  $('#promopopup_settings_type').val()+'_'+$(this).val();
                        console.log(parentValue);
                        var element = $('#promopopup_settings_templates');
                        element .find('option').removeAttr('selected').hide();
                        element .find('option[data-holiday='+parentValue+']').show();
                        element .find('option[data-holiday='+parentValue+']:first').attr('selected','selected');
                    });
                    
                    $('.promo-popup-apply').click(function(event) {
                        event.preventDefault();
                        event.stopImmediatePropagation();
                        var value = $('#promopopup_settings_templates').val();
                    
                        var editor = tinyMCE.get(wysiwygpromopopup_settings_content.id);
                        if(editor !== undefined) {
                          if(wysiwygpromopopup_settings_content.id != undefined) {
                               editor.setContent(value,{'no_events':true});
                          } else {
                              if($('#promopopup_settings_content_ifr').length != 0 ) {
                                $('#promopopup_settings_content_ifr').contents().find('body').html(value);
                              } else {
                                   $('#promopopup_settings_content').val(value);
                              }
                          }
                        } else {
                            $('#'+wysiwygpromopopup_settings_content.id).val(value);
                        }
                    });
                });
            </script>
            ";
        }
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }

    protected function _getParentElementHtml($element) {
        $html = '';

        $html .= '<select id="parent_' . $element->getHtmlId() . '" name="parent_' . $element->getName() . '" ' . $element->serialize(
                $element->getHtmlAttributes()
            )  . '>' . "\n";

        $value = $element->getValue();

        if (!is_array($value)) {
            $value = [$value];
        }

        if ($values = $element->getValues()) {
            $ids = [];
            foreach ($values as $key => $option) {
                if(!in_array($option['typeId'].'_'.$option['holidayId'],$ids)) {
                    $html .= $this->_optionToHtml(['value' => $option['holidayId'], 'label' => $option['holiday'],'typeId'=>$option['typeId']], $value);
                }
                $ids[] =  $option['typeId'].'_'.$option['holidayId'];
            }
        }

        $html .= '</select>';
        return $html;
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $element->addClass('select admin__control-select');

        $html = '';
        if ($element->getBeforeElementHtml()) {
            $html .= '<label class="addbefore" for="' .
                $element->getHtmlId() .
                '">' .
                $element->getBeforeElementHtml() .
                '</label>';
        }

        $html .= '<select id="' . $element->getHtmlId() . '" name="' . $element->getName() . '" ' . $element->serialize(
                $element->getHtmlAttributes()
            )  . '>' . "\n";

        $value = $element->getValue();
        if (!is_array($value)) {
            $value = [$value];
        }

        if ($values = $element->getValues()) {
            foreach ($values as $key => $option) {
                if (!is_array($option)) {
                    $html .= $this->_optionToHtml(['value' => $key, 'label' => $option], $value);
                } elseif (is_array($option['value'])) {
                    $html .= '<optgroup label="' . $option['label'] . '">' . "\n";
                    foreach ($option['value'] as $groupItem) {
                        $html .= $this->_optionToHtml($groupItem, $value);
                    }
                    $html .= '</optgroup>' . "\n";
                } else {
                    $html .= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html .= '</select>' . "\n";
        if ($element->getAfterElementHtml()) {
            $html .= '<label class="addafter" for="' .
                $element->getHtmlId() .
                '">' .
                "\n{$element->getAfterElementHtml()}\n" .
                '</label>' .
                "\n";
        }
        return $html;
    }

    /**
     * Format an option as Html
     *
     * @param array $option
     * @param array $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        if (is_array($option['value'])) {
            $html = '<optgroup label="' . $option['label'] . '">' . "\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }
            $html .= '</optgroup>' . "\n";
        } else {
            $html = '<option '
                . ((isset($option['holidayId'])) ? 'data-holiday="'.$option['typeId'].'_'.$option['holidayId'].'"' :'')
                . ((isset($option['typeId'])) ? 'data-typeId="'.$option['typeId'].'"' :'') .
                ' value="' . $this->_escape($option['value']) . '"';

            $html .= isset($option['title']) ? 'title="' . $this->_escape($option['title']) . '"' : '';
            $html .= isset($option['style']) ? 'style="' . $option['style'] . '"' : '';
            if (in_array($option['value'], $selected)) {
                $html .= ' selected="selected"';
            }
            $html .= '>' . $this->_escape($option['label']) . '</option>' . "\n";
        }
        return $html;
    }

    /**
     * Escape a string's contents.
     *
     * @param string $string
     * @return string
     */
    protected function _escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT);
    }
}