<?session_start();?>
<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="javascript/raphael.js"></script>
        <script type="text/javascript" src="javascript/pie.js"></script>
		
		<script type="text/javascript" src="locales/<?=$_SESSION['lang']?>/messages_po.js"></script>
		<script type="text/javascript" src="javascript/i18n.js"></script>

        <script type="text/javascript" src="javascript/utrfactory.js"></script>

        <link rel="stylesheet" type="text/css" href="cssfiles/default/basic.css">
		<script type='text/javascript'>
			alert(__("Add colomn wizard"));
		</script>
    </head>
    <body>
        <div id="utrDiv" >
            <div id="divPathWizard" class="divstandard">
                <div id="menuPathWizard">
                    UTR BUILDER... chose the property ...
                    <input class="closePathBuilderClass" id="closePathBuilder" type="button" value=""> </input>
                </div>



                <div id="classesDiv">
                    <div id="contextClassHeader" class="boxHeader">

                        <input id="backClass" type="button" value="Back" name="backClass"/>
                        <h1>Classe</h1>


                    </div>
                    <div id="contextClasses" class="contextClassesStyle">

                        <h1> list of classes</h1>
                    </div>
                </div>
                <div id="propertiesDiv">
                    <div id="contextPropertiesHeader" class="boxHeader">
                        <h1>...</h1>
                    </div>

                    <div id="contextProperties" class="contextPropertiesStyle">
                        <h1>List of properties</h1>
                    </div>

                </div>

                <div id="divFooterPathWizard" style="clear:both">

                </div>
            </div>

            <div id="propertyBinding" class="centered">
                <table border="0" cellpadding="0">

                    <tbody>
                        <tr>
                            <td>Column Name:</td>
                            <td><input id="columnName" type="text" name="" value="" /></td>
                        </tr>
                        <tr>
                            <td>Extraction Method:</td>
                            <td><select id="typeExtraction" name="ExtractionMethodDrop">
                                    <option value="direct" >Direct </option>
                                    <option value="xpath" >XPath query </option>
                                    <option value="function" >Function </option>
                                </select></td>
                        </tr>
                        <tr>
                            <td>Query</td>
                            <td><input id="finalPath" type="text" name="finalPath" value="" size="30" /></td>
                        </tr>

                        <tr
                            <td>
                                <input id="addColumn" type="button" value="Add Column" /><input id="exitAddColumn" type="button" value="Exit" />

                            </td>

                        </tr>

                    </tbody>
                </table>



            </div>


            <div id="utrmenu">
                <input id="columnBuilder" type="submit" value="Column Builder Wizard..." /><input id="deleteListRows" type="submit" value="Delete Rows..." /><input id="manageUtr" type="submit" value="Template Manager..." />
                <input id="searchProperty" type="text" name="searchProperty" value="" size="10" /><input id="searchOperator" type="text" name="searchOperator" value="" size="5" /><input id="searchValue" type="text" name="searchValue" value="" size="10" /><input id="sendFilter" type="submit" value="Send Filter" />
            </div>

            <table id="UTR" border="1">
                <thead id="utrHead">


                </thead>
                <tbody id="utrBody" >

                </tbody>
            </table>

            <div id="utrTemplateManager" class="">
                <div id="#utrTemplateTitle">
                    utr
                </div>
                
                <div id="utrTemplateModelList">
                    

                </div>

                <div id="utrTempateMenu">
                    <input id="saveUtrBtn" type="submit" value="save Table" /><input id ="txtUtrName" type="text" name="txtUtrName" value="" /><br>
                    <input id="cancelUtrManager" type="submit" value="Cancel..." />
                </div>

            </div>
        </div>
        <div id="pieStat" class="pieStatClass">
            <input id="hidePieStat" type="button" value="Hide" />

        </div>




    </body>
</html>
