<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
            <script type="text/javascript" src="<?=TAOBASE_WWW?>js/lib/require.js"></script>
            <script type="text/javascript">
            (function(){
                require(['<?=get_data('client_config_url')?>'], function(){
                    require(['taoItems/controller/runtime/itemRunner'], function(itemRunner){
                        itemRunner.start({
                            resultServer    : {
                                endpoint : <?=json_encode(get_data('resultServerEndpoint'));?>
                            },
                            itemService     : {
                                module : 'taoItems/runtime/ItemServiceImpl'
                            },
                            itemId          : <?=json_encode(get_data('itemId'));?>,
                            itemPath        : <?=json_encode(get_data('itemPath'))?>
                        });
                    });
                });
            }());
            </script>
	</head>
	<body>
            <iframe id='item-container' class="toolframe" frameborder="0" style="width:100%;overflow:hidden" scrolling="no"></iframe>
	</body>
</html>