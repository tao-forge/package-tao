<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */
use oat\tao\helpers\form\elements\xhtml\XhtmlRenderingTrait;

/**
 * Short description of class tao_helpers_form_elements_xhtml_ViewableHiddenbox
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 */
class tao_helpers_form_elements_xhtml_ViewableHiddenbox extends tao_helpers_form_elements_ViewableHiddenbox
{
    use XhtmlRenderingTrait;

    /**
     * Short description of method render
     *
     * @access public
     * @author Christophe Noël <christophe@taotesting.com>
     * @return string
     */
    public function render()
    {
        $uid = helpers_Random::generateString(24);

        $this->addClass('viewable-hiddenbox-input');
        $this->addAttribute('data-identifier', $uid);

        $value = _dh($this->value);

        $html = <<<HTML
<span class="viewable-hiddenbox">
    {$this->renderLabel()}
    <input type='password' name='{$this->name}' id='{$this->name}' {$this->renderAttributes()} value='{$value}'/>
    <span class="viewable-hiddenbox-toggle" data-identifier="{$uid}"></span>
</span>
HTML;

        $script = <<<SCRIPT
<script type="text/javascript">
    (function() {
        var input = document.querySelector('input[data-identifier="$uid"]'),
            toggle = document.querySelector('.viewable-hiddenbox-toggle[data-identifier="$uid"]'),
            
            iconView = document.createElement('span'),
            iconHide = document.createElement('span'),
            
            hasBlurred = false; // this is used to prevent double toggling if the blur happens with a click on the toggle itself
        
        var show = function() {
            if (iconView.parentElement) {
                toggle.removeChild(iconView);
            }
            if (!iconHide.parentElement) {
                toggle.appendChild(iconHide);
            }
            input.type = 'text';
        };
        var hide = function() {
            if (!iconView.parentElement) {
                toggle.appendChild(iconView);
            }
            if (iconHide.parentElement) {
                toggle.removeChild(iconHide);
            }
            input.type = 'password';
        };
        var stopBlurring = function() {
            console.log('stopping blur in window listener');
            hasBlurred = false;
            window.removeEventListener('click', stopBlurring);
        };
        
        iconView.classList.add('icon-preview');
        iconHide.classList.add('icon-eye-slash');
        hide();
        
        toggle.addEventListener('click', function() {
            console.log('toggle click => blur =', hasBlurred);

            if (!hasBlurred) {
                if (input.type === 'password') {
                    show();
                    input.focus(); // force focus so it makes sense to listen to the blur event
                } else {
                    hide();
                }
            }
        });
        
        
        
        // make sure always submit the form with an password input
        input.addEventListener('blur', function() {
            if (input.type === 'text') {
                console.log('blur');
                hide();
                hasBlurred = true;
                window.addEventListener('click', stopBlurring);                
            }
        });
    })();
</script>
SCRIPT;


        return (string) ($html . $script);
    }
}
