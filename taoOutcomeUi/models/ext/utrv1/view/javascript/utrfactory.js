/* 
 * Younes Djaghloul, CRP Henri Tudor Luxembourg
 * TAO transfer Project
 * UTR Task ( Ultimate Table for Result module)
 * 
 */
var numberOfFilter = 10;
var speed = 333;
var initialInstancesUri = new Array();// array of instances URI
var rootClasses= new Array();//The classes of the initial Instances
var actualClass; //The class choosed

//the actual class information, note that the actualPropertySourceUri is the most important
//it is the bridge between classes and the path is build as sequence of theses URI

var actualClassUri;
var actualClassLabel = "Root Classes";
var actualPropertySourceUri='';

var classesContext = new Array(); // Context classes are in this version the Rang e of the actual class,
var propertiesContext= new Array();//the properties of the actual class

//The path taht contains a sequence of properties 

var pathProperties = new Array();
//the actual path
var pathString = '';

//the actual utr
var actualUTR = new Array();

//the context for undo redo
var historyAction = new Array();


//Visual intro
function utrIntro(){
   
    historyAction = new Array();//Reset history of actions
    pathProperties = new Array();
    $("#divInitialInstances").html("");
    //$("#contextClasses").html("");
    $("#contextProperties").html("");
    //$("#propertyBinding").show();

    $("#propertyBinding").dialog('close');
    $("#propertyBinding").dialog({
        autoOpen:false
    });
    

    $("#divPathWizard").hide();
    $("#menuPathBuilder").hide();
    $("#pieStat").hide();

    $("#utrTemplateManager").hide();
    $("#utrTemplateManager").dialog('close');

    
    $("#filterUtr").hide();
    
    $("#filterUtr").dialog('close');

    //utr menu
    $("#saveUtrBtn").button();

    $("#utrmenu input").button();
    $("#contextClasses").tabs();
   
}

//save context as history to undo redo
function saveContext(){
    
    var action =[];
    action.path = pathProperties;
    action.actualClassUri = actualClassUri;
    action.actualClassLabel = actualClassLabel;
    //alert (action.actualClassUri);
    
    historyAction.push(action);
}
function backContext(){

    var action = new Array();

    if ( historyAction.length>0){


        action = historyAction.pop();
        //action = historyAction.pop();
        //restore path and actualClass
        pathProperties = action.path;
        pathProperties.pop();
        actualClassUri = action.actualClassUri;
        //Refrensh the interface
        //
        //alert(actualClassUri);
        if (actualClassUri == 'rootClasses'){
            actualClassLabel = "Root Classes";
            getRootClassesOfInstances();
            
        
        }else
        {
            actualClassLabel=action.actualClassLabel
            getContextClasses(actualClassUri);
        
        }
    
        getProperties(actualClassUri);
    }else{
        alert("There is no parent");
    }


}

//get the initial instances that will be used to begin the process of path building
function getInitialInstances(){
    //The list of instances are extracted direcly from the server, this list is already prepared by other mechanism

    $.ajax({
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",//"b1.php",//
        data: {
            op:"listInstances"
        },
        dataType :"json",
        success: function(msg){
            //If success, we have a list of instances with this structure tab[Uri] {label}
            initialInstancesUri = msg;

            //prerview the list of Instances , Only Lable
            trli = msg;
            for (i in trli){
                p = trli[i];
                $("#divInitialInstances").append("<br>"+p.label);
            }
        }//succes

    });
    return initialInstancesUri;
}

function manageMC(){
    var t = new Array();
    t=getRootClassesOfInstances();
    //alert("fin");
    for (i in t){
        alert(t[i].label);
    }

}
//view the list of classes
function previewListClasses(listClasses){
    
    //remove old content
    var titleClass;
    var rangeClasses = listClasses['rangeClasses'];
    var subClasses = listClasses['subClasses'];
    var parentClasses = listClasses['parentClasses'];
    
    //put the title in the header of the box
    titleClass = __("List of context classes")+': '+actualClassLabel;
    $("#contextClassHeader h1").text(titleClass);
    //get the actual class info
    
    
    $("#rangeClasses").text('');
    for (i in rangeClasses){
        cl = rangeClasses[i];
        // we have a button with all information to acces to class info
        content = '<input id="'+cl.uriClass+'" class= "classInfos" type="button" value="'+cl.label+'" name ="'+cl.propertySourceUri +'" /></input>';
        $("#rangeClasses").append(content);
    }
    
    
    
    $("#subClasses").text('');
    for (i in subClasses){
        cl = subClasses[i];
        // we have a button with all information to acces to class info
        content = '<input id="'+cl.uriClass+'" class= "classInfos" type="button" value="'+cl.label+'" name ="'+cl.propertySourceUri +'" /></input>';
        $("#subClasses").append(content);
    }
    
    
    
    $("#parentClasses").text('');
    for (i in parentClasses){
        cl = parentClasses[i];
        // we have a button with all information to acces to class info
        content = '<input id="'+cl.uriClass+'" class= "classInfos" type="button" value="'+cl.label+'" name ="'+cl.propertySourceUri +'" /></input>';
        $("#parentClasses").append(content);
    }
}
//preview the list of properties of the actual class
function previewListProperties(listProperties){
    var pl = new Array();
    var titleContextProperties;
    titleContextProperties = __("List of properties")+": "+actualClassLabel;
    $("#contextPropertiesHeader h1").text(titleContextProperties);
   
    $("#contextProperties").text('');
    for (uriP in listProperties){
        pl = listProperties[uriP];
        //we have a button with all informations about properties
        content = '<input id="'+uriP+'" type="button" value="'+pl.label+'" name ="propertyInfos_'+uriP +'" class="property" /></input>';
        $("#contextProperties").append(content);
    }
}
//Get the list of classes according to the intial list of instances,
//the list of instances is savedd on the server side

function getRootClassesOfInstances(){
    
    //According to the list of instances we get the list of classes
    listClasses = new Array();
    
    $.ajax({
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"getClassesOfInstances"
        },
        dataType :"json",
        async : false,
        
        success: function(msg){
            
            var vide=[];
            listClasses = msg;
            rootClasses = msg;
            var initialClasses =[];
            
            // to have the same model of classes list for the preview
            initialClasses['rangeClasses'] = rootClasses;
            initialClasses['subClasses'] = rootClasses;
            initialClasses['parentClasses'] = rootClasses;
            // preview the list of classes
            previewListClasses(initialClasses);
            previewListProperties(vide);
            //save the context
            actualClassUri = 'rootClasses';
            pathProperties = [];

        //return listClasses;
        }//succes

    });
    return listClasses;
}
//get the properties of  the class, according to the URI as parameter
function getProperties(uriC){
    
    //alert(uriC);
    $.ajax({
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"getProperties",
            uriClass:uriC
        },
        dataType :"json",
        success: function(msg){
            propertiesContext= msg;
            previewListProperties(propertiesContext);
        }//succes
    });
    return propertiesContext;
}

//get the range of a class, a range is a set iof classesthat are range of properties of the actual class
function getContextClasses(uriC){

    //alert(uriC);
    $.ajax({
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"getContextClasses",
            uriClass:uriC
        },
        dataType :"json",
        success: function(msg){
            var contextClasses= msg;
            var rangeClasses = contextClasses['rangeClasses'];
            var subClasses = contextClasses['subClasses'];
            var parentClasses = contextClasses['parentClasses'];
            
            var mergedContextClasses=[];
            console.log(contextClasses);
            previewListClasses(contextClasses);
            //TODO add other classes

        }//succes
    });

    return classesContext;
}
//add a new bridge to path

function addToPath(propertyUri){

    if (propertyUri != 'undefined'){
        pathProperties.push(propertyUri);
    }
    
    // just for illustration 
    pathString =pathProperties.join("__");
    //$("#pathProp").val(pathString);

    return pathString;
}

//this method gets the properties and the range of a particular class,
//it uses getContextClasses and get getProperties.
function getClassInfos (){
    saveContext();
    //get the URI of the class
    var uriC = $(this).attr("id");
    //get the label of the class
    var labelClass = $(this).attr("value");
    //get the propertySourceUri
    var propertySource = $(this).attr("name");// le

    //save the uri and label of the class as actual context
    actualClassUri = uriC;
    actualClassLabel = labelClass;
    actualPropertySourceUri= propertySource;

    //add to path
    addToPath(actualPropertySourceUri);

    //Save the context
    
    //alert ("l'uri est "+uri);
    //get the properties and the range of the class, this is thje next step of the process
    getContextClasses(uriC);
    getProperties(uriC);
}
//show the div to preview the values of the property
//in order to add the column
function getPropertyBinding(){
    var uriP = $(this).attr("id");
    var labelP = $(this).attr("value");

    //add to path
    pathString =addToPath(uriP);
    //show the dialog with options

    var option={
        resizable:false,
        buttons: {
            
            "No":function(){
                $("#propertyBinding").dialog("close");
                //delete the last property in the path
                pathProperties.pop();
            },
            "Ok": function() {
                addColumn();
            }
        }
    }

    $("#propertyBinding").dialog(option);
    $("#propertyBinding").dialog("open");
    //whene we close the dialog with the X the path is restored
    $("#propertyBinding").bind("dialogclose",function (){
        pathProperties.pop();
    });
    
    //Put the default values
    $("#columnName").val(labelP);
//$("#finalPath").val(pathString);
    
}
//delete the colomn from the server side and re-preview the table

function deleteColumn(colId){
    //$colId = $(this).attr(id);

    var deleteCol = confirm(__("Do you want to delete column ?"));
    if (deleteCol){
        // add the column on the server side, and preview the table after succes
        $.ajax({
            type: "POST",
            url: "../classes/class.TReg_VirtualTable.php",
            data: {
                op:"deleteColumn",
                'columnId':colId

            },
            dataType :"json",
            success: function(msg){
                //alert ()
                actualUTR = msg;
                previewTable(msg);
                //close the window
                //$("#propertyBinding").hide();
                utrIntro();

            }//succes
        });


    }
    else{
        
}
}
//Verification of the existance of the column name
function verifyColumnLabel(colLabel){
    var exist = false;//if the label exists
    var actualModel = [];
    actualModel = actualUTR['utrModel'];//get only the utrModel of the whole utrTable

    var listNames = [];


    if ( actualModel != undefined ){
        if (actualModel[colLabel]!=undefined){
            exist = true;
        }

    }else
    {
        exist = false;
    }
    return exist;
        
}

//add a column to the virtual table on server by using Ajax
function addColumn(){
    //get parameter from interface
    var cn = $("#columnName").val();
    if (cn==''){
        cn ='noName'
    };
    var te = 'Deprecated';//$("#typeExtraction").val();
    var pf = pathString;//$("#finalPath").val();
    //Verification of the existance of the column name
    if (verifyColumnLabel(cn)==true ){
        alert (__("Name exists"));

    }else{

        // add the column on the server side, and preview the table after succes
        $.ajax({
            type: "POST",
            url: "../classes/class.TReg_VirtualTable.php",
            data: {
                op:"addColumn",
                columnName:cn,
                typeExtraction:te,
                finalPath:pf
            },
            async:false,
            dataType :"json",
            success: function(msg){
                actualUTR = msg;
                previewTable(msg);
                //close the window
                
                
                utrIntro();

            }//succes
        });

    }//else
    

}
// this method prewiews the table generated from the server side, according to the the interface technique. ( jgrid, slick, simple table)
//
//in the actual version we generate an HTML table, I hope to use in the furtur more sophistacted grid...jGrid, slick...
function previewTable(table){
    //save the table in a global var
    actualUTR = table;


    finalTable = new Array();
    //get the 2 tables (Model + rowsHTML + rowsInfo)
    finalUtrModel = table.utrModel;//

    //use the rowsHTML to generate the body part of the table
    finalRowsHTML = table.rowsHTML;//the content that will be used to generate the html Table

    //get the rows infor
    finalRowsInfo = table.rowsInfo;

    //generate the header
    var strTableHead = '';
    var strHeadNameColomn ='';
    //add the first column of rowStat
    strTH ='<th>'+ __("Columns")+'</th>';
    for ( i in finalUtrModel){
        var columnDescription = finalUtrModel[i];
        //get columnDescription
        var columnName = columnDescription['columnName'];
        var totalRows = columnDescription["totalRows"];
        var totalRowsNotNull=columnDescription["totalRowsNotNull"] ;

        columnLabel = columnName ;
        //calculate the pourcentage and add a new header
        pourcentageCol = totalRowsNotNull + '/'+totalRows;
        //
        //button delete
        var btnDelete = '<input id='+i+' title="Delete column" type="button" value="" class = "deleteColumnClass"/></input>';
        var btnInfo = '<input id='+i+' title="Info column" type="button" value="Info" class = "infoColumnClass"/></input>';

        strTH = strTH+ '<th>'+btnDelete +" "+btnInfo+'<br>'+columnLabel+'</th>';
    }
    strHeadNameColomn = strTH;
    //the sat of column
     
    strTH ='<th> % </th>';
    var strStatColumn = '';
    for ( i in finalUtrModel){
        columnDescription = finalUtrModel[i];
        //get columnDescription
        //columnName = columnDescription["columnName"];
        totalRows = columnDescription["totalRows"];
        totalRowsNotNull=columnDescription["totalRowsNotNull"] ;

        
        //calculate the pourcentage and add a new header
        pourcentageCol = (parseFloat(totalRowsNotNull/totalRows)*100).toFixed(2);
        columnLabel =  pourcentageCol;
        strTH = strTH+ '<th>'+columnLabel+'</th>';
    }
    strStatColumn = strTH;

    //final head
    strTableHead = '<tr>'+strHeadNameColomn+'</tr>' + '<tr>'+strStatColumn+'</tr>';

    //put in the table head
    $("#utrHead").html(strTableHead);

    //the body of the table
    strTableBody = '';//the html code of the table body

    for (uri in finalRowsHTML){
        //get the value of the row
        rowHTML = finalRowsHTML[uri];
        //get statistic info of rows
        var rowInfo = finalRowsInfo[uri];
        var totalColumns = rowInfo["totalColumns"];
        var totalColumnsNotNull=rowInfo['totalColumnsNotNull']
        
        var pourcentageRow = parseFloat(totalColumnsNotNull/totalColumns)*100;
        pourcentageRow = pourcentageRow.toFixed(2);

        //generate the html tag for table
        strTR = '';//initialize the row
        strTD = '<td class="statRow"> <input class = "statCheck" type="checkbox" name="rowCheck" value="'+uri+'" >'+pourcentageRow+'</input> </td>';//initialize the cell
        //build the data of the row, a set of cells
        for ( i in rowHTML){

            /*var cellValue = '<pre>'+rowHTML[i]+'</pre>';//
            strTD = strTD+'<td>'+cellValue+'</td>';*/

            var cellValue = rowHTML[i].replace(/\|\$\*/g, '<br>-');//
            strTD = strTD+'<td>'+cellValue+'</td>';

        }
        //create the row
        strTR = '<tr id = "'+ uri+'">'+strTD+'</tr>';
        strTableBody = strTableBody + strTR;
    }
    //alert (strTableBody);
    $("#utrBody").html(strTableBody);

//tableToGrid("#UTR");

}

function removeSession(){
    $.ajax({
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"removeSession"
            
        },
        //dataType :"json",
        success: function(msg){
            //alert(msg);
            //close the window
            //$("#propertyBinding").hide();
            utrIntro();

        }//succes
    });
}
function showColumnInfo(colId){
    
    //get column description
    var raphael;
    var columnDescription = new Array();
    var utrm = actualUTR.utrModel;
    columnDescription = utrm[colId];
    
    var totalRows = columnDescription['totalRows'];
    var totalRowsNotNull=columnDescription["totalRowsNotNull"] ;
    //alert (totalRows+ " -- "+totalRowsNotNull);
   

    $("#pieStat").slideDown();
    //put the two arrays of value and labels
    var pieValues = [],
    pieLabels = [];
    var pourcentageRowNotNull = parseFloat(totalRowsNotNull/totalRows)*100;
    var pourcentageRowNull =100-pourcentageRowNotNull;
    pieValues.push(pourcentageRowNotNull);
    pieValues.push(pourcentageRowNull);
    pieLabels.push('Not Null');
    pieLabels.push('Null');
    
    

    (function (raphael) {
        $(function () {
            //alert ("gfghfdgh"+totalRows+ " -- "+totalRowsNotNull);
            raphael("pieStat", 540, 370).pieChart(270, 200, 120, pieValues, pieLabels, "#fff");
            
        });
    })(Raphael.ninja());

}

//delete the list of rows chosed bu the user
//
function deleteListRows(){
    //get the list of selected rows, from the vlaue attribut of rowStat class
    var listRows =[];
    var listRowsString = '';
    //get only the selected row 
    $(".statCheck:checked").each(function(){
        //if ($(this).attr("checked")=)
        codeRow = $(this).attr("value");

        listRows.push(codeRow);
        
    });
    listRowsString = listRows.join('|');

    //using ajax, send thelist to delete

    options={
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"deleteListRows",
            listRowsToDelete: listRowsString

        },
        dataType:"json",
        success: function(msg){
            //get the new UTR table
            actualUTR = msg;
            
            previewTable(msg);
            //close the window
            //$("#propertyBinding").hide();
            utrIntro();

        }


    };
    $.ajax(options);

}
//request to save the actual utrModel
function saveUtr(){
    //$("#utrTemplateManager").dialog('close');
    var modelName = $("#txtUtrName").val();
    options={
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"saveUtr",
            idModel: modelName

        },
        //dataType:"json",
        success: function(msg){
            alert (msg);
            utrIntro();
        
        }

    };
    $.ajax(options);
    


}
function getUtrTemplate(){
    var modelName = $(this).attr('id');
    

    loadUtr(modelName)
    $("#utrTemplateManager").dialog('close');

}
function loadUtr(modelName){
    //modelName = $("#txtUtrName").val();
    options={
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"loadUtr",
            idModel: modelName

        },
        dataType:"json",
        success: function(msg){
            //get the new UTR table
            actualUTR = msg;
           
            previewTable(msg);
            //close the window
            //$("#propertyBinding").hide();
            utrIntro();

        }

    };
    $.ajax(options);
}

function getUtrModels(){

    var optionsTM ={
        height:450,
        width:700,
        
        hide:'explode',

        modal : false,
        resizable: false,
        title:__("template manager")
        



    };
    // open the dialog
    $("#utrTemplateManager").dialog(optionsTM);
    $("#txtUtrName").focus();
    options={
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"getUtrModels"
        },
        dataType:"json",
        success: function(msg){
            //get the new UTR table
            listUtr = msg;
            //alert (msg);

            //preview the list in the div
            $("#utrTemplateModelList").html("");
            for (i in listUtr){
                cl = listUtr[i];
                content = '<input id="'+i+'" class= "utrTemplate" type="button" value="'+i+'" name ="cl.propertySourceUri " /></input>';
                $("#utrTemplateModelList").append(content);
            }
     
        //close the window
        //$("#propertyBinding").hide();
        //utrIntro();
        }

    };
    $.ajax(options);

}
//load the actual UTR without filter

function loadUnfilteredUtr(){

    options={
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"loadUnfilteredUtr"
        },
        dataType:"json",
        success: function(msg){
            actualUTR = msg;
            previewTable(actualUTR);
        }

    };
    $.ajax(options);

}


//load initial UTR
function loadInitialUtr(){

    options={
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"loadInitialUtr"
        },
        dataType:"json",
        success: function(msg){
            actualUTR = msg;
            previewTable(actualUTR);
        }

    };
    $.ajax(options);

}
//send tyhe filter and recieve the UTR
function sendFilter(){

    var tabOfFilters =[];// the array of all filters
    //
    var i =0;
    //create the complex filter
    for(i=1;i<=numberOfFilter;i++){

        var queryProperty = $("#searchProperty"+i).val();
        var queryOperator = $("#searchOperator"+i).val();
        var queryValue = $("#searchValue"+i).val();

     
        //test if empty
        if ( queryProperty !=''){

            var columnFilter = queryProperty+'|||'+queryOperator+'|||'+queryValue;
            tabOfFilters.push(columnFilter);

        }//if
    }


    var fullFilterText = tabOfFilters.join('|*$');
    
    options={
        type: "POST",
        url: "../classes/class.TReg_VirtualTable.php",
        data: {
            op:"sendFilter",
            filter:fullFilterText
        },
        dataType:"json",
        success: function(msg){
            actualUTR = msg;
            previewTable(actualUTR);
        }

    };
    $.ajax(options);
    
}
//export to csv
function exportCSV (){

    window.location.href = "../classes/class.TReg_VirtualTable.php?op=exoprtCSV";
}
/// export to excel
function exportToExcel(){

    window.location.href = "../classes/class.TReg_VirtualTable.php?op=exportToExcel"
}

//manage the event of the index page
function manageEvents(){
    //alert ("manage");
    $("#closePathBuilder").click(function (){
        $("#divPathWizard").slideUp(speed);
    });
    $("#getInitialInstances").click(getInitialInstances);
    $("#getRootClasses").click(getRootClassesOfInstances);
    //get class infos and create path
    $("input[class *='classInfos']").live('click',getClassInfos);
    $("input[name *='propertyInfos']").live('click', getPropertyBinding);
    $("#remove").click(removeSession);
    //hide the statistic info
    $("input[class = 'utrTemplate']").live('click',getUtrTemplate);

    

    //add column event
    $("#addColumn").click(addColumn);
    $("#exitAddColumn").click(function(){

        $("#propertyBinding").dialog("close");
        
        //delete the last property in the path
        pathProperties.pop();
    });
    //delete column
    $(".deleteColumnClass").live('click',function(){
        //get parameter
        
        var colId = $(this).attr("id");
        //alert ("delete "+ colId);
        deleteColumn(colId);
    });
    $("#columnBuilder").click(function(){
        
        //reset patth and history of actions
        historyAction = new Array();//Reset history of actions
        pathProperties = new Array();


        //show the path bulder div
   
        $("#divPathWizard").show(speed*2);

        //with dialog
        optionPathWizard={
            show:'blind',
            hide:'explode',
            height:420,
            width:800,
            modal:true,
            resizable:false

        };
        //$("#divPathWizard").dialog(open,optionPathWizard);

        getRootClassesOfInstances();

    });
    $("#backClass").click(backContext);
    //
    
    $("#hidePieStat").click(function (){
        $("#pieStat").slideUp(speed);
    });

    //show column detail
    $(".infoColumnClass").live('click', function(){
        //get class id
        var colId = $(this).attr("id");
        showColumnInfo(colId);


    });

    //Manage delete row
    $("#deleteListRows").click(deleteListRows);

    $("#saveUtrBtn").click(saveUtr);
    $("#loadUtrBtn").click(loadUtr);

    $("#manageUtr").click(function(){
        /*$("#saveUtrBtn").toggle();
        $("#loadUtrBtn").toggle();
        $("#txtUtrName").toggle();*/
        //$("link[rel=stylesheet]").attr({href:"green.css"});


        getUtrModels();


    });
    //close utrManager
    $("#cancelUtrManager").click(function(){
        $("#utrTemplateManager").dialog('close');


    });

    //manage filter
    $("#sendFilter").click(function (){

        sendFilter();
        $("#filterUtr").dialog('close');
    });


    $("#cancelFilter").click(function(){
        
        $("#filterUtr").dialog('close');

    });
  
    $("#manageFilter").click(function(){
        var ok = '';
        
        var options ={
            height:450,
            width:550,
            hide:'explode',


            modal : true,
            resizable: false,
            title:__("Filter & Search"),

            buttons: {

                "Cancel":function(){
                    loadUnfilteredUtr();
                    $("#filterUtr").dialog('close');
                },
                "Apply filter": function() {
                    sendFilter();
                    $("#filterUtr").dialog('close');
                }
            }
          
        };

        $("#filterUtr").dialog(options);
        buildFilterLine();

    });

    //export to 
    $("#export").click(function(){
        
        var ok = '';
        
        var options ={
            height:50,
            width:500,
            hide:'explode',
            modal : true,
            resizable: false,
            title:__("Export the Table"),

            buttons: {

                "Export To CSV":function(){
                    exportCSV();
                    $("#exportChoice").dialog('close');
                },
                "Export To MS Excel": function() {
                    exportToExcel();
                    $("#exportChoice").dialog('close');
                }
            }
          
        };

        $("#exportChoice").dialog(options);
        
        
    });


    
    
}


function utrConstructor(){
    $(function(){
    
        removeSession();
        // get the initial UTR, the user will ha ve directely a simple uTR with the properties of the current class
        loadInitialUtr();
        utrIntro();
        manageEvents();
        
    });

}

function trad(){
    __("Build your table");
    __("Add colomn wizard");
    __("Remove rows");
    __("Template manager");
    __("Filter and search ");
    __("Add new filter");
    __("Delete filter");
    __("Delete filter");
    __("Apply filter");
    __("Cancel");
    __("Ok");
    __("Yes");
    __("No");
    __("Template saved");
    __("Back");
    __("Next");
    __("List of context classes");
    __("Root classes");
    __("List of properties");
    __("UTR Builder");
    __("Chose a property");
    __("Column name");
    __("Extraction method");
    __("Query");
    __("Exit");
    __("Info");
    __("Columns");
    __("Do you want to delete this column ?");
    __("Do you want to delete these rows ?");
    __("Error in loading table");
    __("Thank you for using UTR");
    __("Error, action failed !");
    __("Select a property");
    __("With UTR, you can");
    __("Build a flexible table to extract information");
    __("Build a complex table with no unlimited depth");
    __("You can dynamically Add, remove column");
    __("Calculate the percentage of columns and rows");
    __("Create save your own Template of tables");
    __("Get a direct chart diagram on your columns ans rows ");
    __("Welcome to UTR Builder");

}

//create the filter interface according the disponible columns
function buildFilterLine(){
    var actualModel = [];
    $("#filterTableBody").html('');
    
    var optionProperties = '';
    
    
    //faire la boucle
    
    var actualModel = actualUTR['utrModel'];//get only the utrModel of the whole utrTable.
    
    var operators = [];
    operators[0] = '=';
    operators[1] = '<';
    operators[2] = '>';

    operators[3] = '<=';
    operators[4] = '>=';
    operators[5] = 'like';

    
    //build the propety selection
    for ( trI = 1 ;trI <=numberOfFilter;trI++){

        optionProperties = '<select id="searchProperty'+trI+'">';

        //add a blank option
        optionProperties = optionProperties + '<option value=""></option>';

        for (i in actualModel){
            property = i;
            optionProperties = optionProperties + '<option value="'+property+'">'+property+'</option>';
        }
        optionProperties = '<td>'+optionProperties +'</select></td>';

        //build the operator selction
    
        var optionOperators = '';
        optionOperators = '<select id ="searchOperator'+trI+'">';
        var op = '';
        for (i in operators){
            op = operators[i];
            optionOperators= optionOperators + '<option value="'+op+'">'+op+'</option>';
        
        }

        optionOperators = '<td>'+optionOperators +'</select></td>' ;

        var creteriaValue = '<td><input id="searchValue'+trI+'" type="text" name="searchValue'+trI+' value="" size="10" /></td>';
        //build the whole filter element
        var filterElement = '<tr>'+optionProperties+optionOperators+creteriaValue+'</tr>';

        $("#filterTableBody").append(filterElement);

    }// end for

  

}

this.utrConstructor();



