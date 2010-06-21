<?
require_once($_SERVER['DOCUMENT_ROOT']."/generis/common/inc.extension.php");
require_once($_SERVER['DOCUMENT_ROOT']."/taoResults/includes/common.php");
?>
<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">


<html>
    
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="cssfiles/default/basic.css">

        <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.custom.css" />

        <script src="javascript/jquery-1.3.2.min.js"></script>

        <script src="<?=TAOBASE_WWW?>js/jquery-ui-1.8.custom.min.js"></script>

        <script type="text/javascript" src="javascript/raphael.js"></script>
        <script type="text/javascript" src="javascript/pie.js"></script>

        <script type="text/javascript" src="locales/<?=$_SESSION['lang']?>/messages_po.js"></script>
        <script type="text/javascript" src="javascript/i18n.js"></script>

        <script type="text/javascript" src="javascript/utrfactory.js"></script>

    </head>


    <body>
        <div id="utrDiv" >
            <div id="divPathWizard" class="ui-widget-content ui-corner-top" >
                <div id="menuPathWizard" class ="ui-widget-header ui-corner-top" style="margin: 0.1em 0.1em">
                    <? echo __("Choose a property");?>
                    <input class="closePathBuilderClass" id="closePathBuilder" type="button" value=""> </input>
                </div>
                

                <div id="classesDiv" class="ui-widget-content ui-corner-all">
                    <div id="contextClassHeader" class="ui-widget-header" style="margin: 0.1em 0.1em">

                        <input id="backClass" type="button" value="<?__("Back")?>" name="backClass"/>
                        <h1>Classe</h1>

                    </div>
                    <div id="contextClasses" class="contextClassesStyle">

                        <h1> list of classes</h1>
                    </div>
                </div>
                <div id="propertiesDiv" class="ui-widget-content ui-corner-all">
                    <div id="contextPropertiesHeader" class="ui-widget-header ui-corner-top" style="margin: 0.1em 0.1em">
                        <h1>...</h1>
                    </div>

                    <div id="contextProperties" class= " contextPropertiesStyle ui-widget-content ui-corner-all " style="margin: 0.1em 0.1em">
                        <h1>List of properties</h1>
                    </div>

                </div>

                <div id="divFooterPathWizard" style="clear:both">
                </div>
            </div>

            <div id="propertyBinding" title="Add column" class="centered">
                <table border="0" cellpadding="0">

                    <tbody>
                        <tr>
                            <td><? echo __("Column name"); ?>:</td>
                            <td><input id="columnName" type="text" name="" value="" /></td>
                        </tr>

                    </tbody>
                </table>

            </div>


            <div id="utrmenu">
                <input id="columnBuilder" type="submit" value="<?echo __("Add column wizard");?>" /><input id="deleteListRows" type="submit" value="<?echo __("Remove rows");?>" /><input id="manageUtr" type="submit" value="<? echo __("Template manager");?>" /><input id="manageFilter" type="submit" value="<?echo __("Filter and search");?>" /><input id="export" type="submit" value="<?echo __("Export");?>" />
                
            </div>

            <table id="UTR" border="1">
                <thead id="utrHead">


                </thead>
                <tbody id="utrBody" >

                </tbody>
            </table>

            <div id="utrTemplateManager" class="">
                <div id="#utrTemplateTitle">
                    <h1><? echo __("Template manager")?></h1>
                </div>

                <div id="utrTemplateModelList">


                </div>

                <div id="utrTemplateMenu">
                    <input id="saveUtrBtn" type="submit" value="<? echo __("Save Table")?>" /><input id ="txtUtrName" type="text" name="txtUtrName" value="" /><br>
                    <input id="cancelUtrManager" type="submit" value="<? echo ("Cancel");?>" />
                </div>

            </div>
        </div>
        <div id="pieStat" class="pieStatClass">
            <input id="hidePieStat" type="button" value="Hide" />

        </div>

        <div id="filterUtr">
            <table border="1" width="1" cellspacing="1">
                <input id="sendFilter" type="submit" value="<? echo __("Apply filter");?>" /><input id="cancelFilter" type="submit" value="<? echo __("Cancel")?>" />
                <thead>
                    <tr>
                        <th><?echo __("Column")?></th>
                        <th><?echo __("Operator")?></th>
                        <th><?echo __("Value")?></th>
                    </tr>
                </thead>
                <tbody id="filterTableBody">

                    <tr>
                        <td><input id="searchProperty1" type="text" name="searchProperty1" value="" size="10" /></td>
                        <td><input id="searchOperator1" type="text" name="searchOperator1" value="" size="5" /></td>
                        <td><input id="searchValue1" type="text" name="searchValue1" value="" size="10" /></td>
                    </tr>
                    <tr>
                        <td><input id="searchProperty2" type="text" name="searchProperty2" value="" size="10" /></td>
                        <td><input id="searchOperator2" type="text" name="searchOperator2" value="" size="5" /></td>
                        <td><input id="searchValue2" type="text" name="searchValue2" value="" size="10" /></td>
                    </tr>
                    <tr>
                        <td><input id="searchProperty3" type="text" name="searchProperty3" value="" size="10" /></td>
                        <td><input id="searchOperator3" type="text" name="searchOperator3" value="" size="5" /></td>
                        <td><input id="searchValue3" type="text" name="searchValue3" value="" size="10" /></td>
                    </tr>
                    <tr>
                        <td><input id="searchProperty4" type="text" name="searchProperty4" value="" size="10" /></td>
                        <td><input id="searchOperator4" type="text" name="searchOperator4" value="" size="5" /></td>
                        <td><input id="searchValue4" type="text" name="searchValue4" value="" size="10" /></td>
                    </tr>
                    <tr>
                        <td><input id="searchProperty5" type="text" name="searchProperty4" value="" size="10" /></td>
                        <td><input id="searchOperator5" type="text" name="searchOperator4" value="" size="5" /></td>
                        <td><input id="searchValue5" type="text" name="searchValue4" value="" size="10" /></td>
                    </tr>


                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>



                </tbody>
            </table>

        </div>
        


    </body>
</html>
