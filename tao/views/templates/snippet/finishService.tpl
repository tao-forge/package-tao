<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">
(function(){
    window.onServiceApiReady = function(serviceApi) {
        serviceApi.finish();
    };
     //tell the parent he can trigger onServiceApiReady
     window.parent.$(window.parent.document).trigger('serviceready');
}());
</script>
</head>
<body>
</body>
</html>