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
define([
    "jquery",
    "mage/translate",
    "Magento_Ui/js/modal/modal",
    "text!BelVG_Popup/template/modal/modal-popup.html",
    "jquery/jquery.cookie"
], function ($, $t, modal, customTpl) {
    'use strict';
    $.widget('mage.promoPopup', {
        options: {
            id:'cpp',
            delay: 3600,
            limit: 10,
            type:'banner',
            area:'global',
            scenario:'delay',
            scrollCount: 3,
            modalSelector: '.custom-promo-popup',
            modalObj: modal,
            modal:{
                type: 'popup',
                responsive: true,
                clickableOverlay: true,
                innerScroll: false,
                modalClass: 'custom-promo-popup-modal',
                popupTpl: customTpl
            }
        },
        translate: $t,
        isSubscribed:false,
        cookie: {
            count:0,
            options:{
                expires: 7,
                path: '/',
                domain: window.location.hostname
            }
        },
        scrollPos:0,
        scrollCounter:0,
        isDisabled: false,
        /** @inheritdoc */
        _create: function () {
            var $this = this;
            $this._initModal();
        },
        _initModal : function() {
            var $this = this;
            var $parent = $($this.options.modalSelector);

            $this.isSubscribed = $this.getCookie("subscribe");
            $this.cookie.count = $this.getCookie("count");

            if (!$this.cookie.count) {
                $this.cookie.count = 0;
                $this.setCookie('count', $this.cookie.count);
            }

            var modal = $this.options.modalObj($this.options.modal, $parent);

            if ($this.options.scenario == 'scroll') {
                $(window).scroll(function () {
                    var scrollPosCur = $(this).scrollTop();
                    if ((scrollPosCur - $this.scrollPos) > 100) {
                        $this.scrollCounter += 1;
                        $this.scrollPos = scrollPosCur;
                    }

                    if($this.scrollCounter >= parseInt($this.options.scrollCount)) {
                        if ($this.options.limit > 0) {
                            if (!$this.isSubscribed && parseInt($this.cookie.count) < parseInt($this.options.limit)) {
                                if(!$this.isDisabled) {
                                    $this.isDisabled = true;
                                    $parent.modal('openModal');
                                }
                            }
                        } else {
                            if(!$this.isDisabled) {
                                $this.isDisabled = true;
                                $parent.modal('openModal');
                            }
                        }
                    }
                });
            }

            if ($this.options.scenario == 'delay') {
                if ($this.options.limit > 0) {
                    if (!$this.isSubscribed && parseInt($this.cookie.count) < parseInt($this.options.limit)) {
                        setTimeout(function () {
                            if(!$this.isDisabled) {
                                $this.isDisabled = true;
                                $parent.modal('openModal');
                            }
                        }, $this.options.delay);

                    }
                } else {
                    setTimeout(function () {
                        if(!$this.isDisabled) {
                            $this.isDisabled = true;
                            $parent.modal('openModal');
                        }
                    }, $this.options.delay);
                }
            }


            $(document).on('click', '.'+$this.options.modal.modalClass+' .action-close', function() {
                var count = $this.getCookie('count');
                count = parseInt(count) + 1;
                $this.setCookie('count',count);
            });

            switch($this.options.type) {
                case 'coupon':
                case 'newsletter':
                    $parent.find('form').submit(function (event) {
                        event.preventDefault();

                        var $form = $(this);
                        var $input = $form.find('input[type="email"]:first');

                        if (!$form.valid() || !$input.length) {
                            return false;
                        }

                        $this.showLoader();

                        $.getJSON($this.options.url,{'email': $input.val()},function (data){
                            $this.hideLoader();

                            if (data.hasError == true) {
                                $this.showMessage('error',data.message);
                            } else {
                                if(data.message!='') {

                                    $this.showMessage('success', data.message);
                                    $this.setCookie("subscribe",1);

                                    setTimeout(function (){
                                        $this.isDisabled == true;
                                        $this.close();
                                    }, 4000);

                                } else {
                                    $this.isDisabled == true;
                                    $this.close();
                                }
                            }
                        });
                    });
                    break;
            }
        },
        setCookie : function (key,value){
            var $this = this;
            $.cookie($this.options.id + '_' + key, value,   $this.cookie.options);
        },
        getCookie: function (key) {
            var $this = this;
            return $.cookie($this.options.id+ '_' +key)
        },
        close : function (){
            var $this = this;
            $($this.options.modalSelector).modal('closeModal');
        },
        showLoader : function () {
            var $this = this;
            var $loader = $($this.options.modalSelector).find("[data-role=loader]");
            $loader.removeClass('no-display');
        },
        hideLoader : function () {
            var $this = this;
            var $loader = $($this.options.modalSelector).find("[data-role=loader]");
            $loader.addClass('no-display');

        },
        showMessage: function(type,message) {
            var $this = this;
            var $parent = $($this.options.modalSelector);
            var $messages = $parent.find('.page.messages');
            $messages.html(
                '<div class="message message-'+type+' '+type+'">'+message+'</div>'
            );
            $messages.removeClass('no-display');
        },
        hideMessage: function() {
            var $this = this;
            var $parent = $($this.options.modalSelector);
            var $messages = $parent.find('.page.messages');
            $messages.addClass('no-display');
            $messages.html('');

        },
    });

    return $.mage.promoPopup
});