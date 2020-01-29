<?php
/*
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
?>
<responseCondition>
    <responseIf>
        <match>
            <variable identifier="<?=$responseIdentifier?>" />
            <?php if ($multiple) :?>
            <multiple>
                <?php foreach ($choices as $choice) :
                    ?><baseValue baseType="identifier"><?=$choice?></baseValue><?php
                endforeach;?>
            </multiple>
            <?php else :?>
            <baseValue baseType="identifier"><?=$choice?></baseValue>
            <?php endif;?>
        </match>
        <setOutcomeValue identifier="<?=$feedbackOutcomeIdentifier?>">
            <baseValue baseType="identifier"><?=$feedbackIdentifierThen?></baseValue>
        </setOutcomeValue>
    </responseIf>
<?php if (!empty($feedbackIdentifierElse)) :
    ?><responseElse>
        <setOutcomeValue identifier="<?=$feedbackOutcomeIdentifier?>">
    <baseValue baseType="identifier"><?=$feedbackIdentifierElse?></baseValue>
            </setOutcomeValue>
<?php endif;?>
</responseCondition>