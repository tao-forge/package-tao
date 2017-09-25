require.config({

    baseUrl : '<?=get_data('tao_base_www')?>js',
    catchError: true,
    waitSeconds: <?=get_data('client_timeout')?>,
<?php if(get_data('buster')):?>
    urlArgs : "buster=<?=get_data('buster')?>",
<?php endif; ?>

    config : {
        context : <?=get_data('context')?>,
        text: {
            useXhr: function(){ return true; },
        },
        'ui/themes' : <?= get_data('themesAvailable') ?>,
//dynamic lib config
    <?php foreach (get_data('libConfigs') as $name => $config) :?>
        '<?=$name?>'        : <?=json_encode($config)?>,
    <?php endforeach?>
    },
    paths : {
//require-js plugins
        'text'              : 'lib/text/text',
        'json'              : 'lib/text/json',
        'css'               : 'lib/require-css/css',
        'tpl'               : 'tpl',
//jquery and plugins
        'jquery'            : 'lib/jquery-1.8.0.min',
        'jqueryui'          : 'lib/jquery-ui-1.8.23.custom.min',
        'select2'           : 'lib/select2/select2.min',
        'jquery.autocomplete'  : 'lib/jquery.autocomplete/jquery.autocomplete',
        'jwysiwyg'          : 'lib/jwysiwyg/jquery.wysiwyg',
        'jquery.tree'       : 'lib/jsTree/jquery.tree',
        'jquery.timePicker' : 'lib/jquery.timePicker',
        'jquery.cookie'     : 'lib/jquery.cookie',
        'nouislider'        : 'lib/sliders/jquery.nouislider',
        'jquery.fileDownload'  : 'lib/jquery.fileDownload',
        'qtip'              : 'lib/jquery.qtip/jquery.qtip',
//polyfills
        'polyfill'          : 'lib/polyfill',
//libs
        'lodash'            : 'lib/lodash.min',
        'async'             : 'lib/async',
        'moment'            : 'lib/moment-with-locales.min',
        'handlebars'        : 'lib/handlebars',
        'class'             : 'lib/class',
        'raphael'           : 'lib/raphael/raphael',
        'scale.raphael'     : 'lib/raphael/scale.raphael',
        'spin'              : 'lib/spin.min',
        'html5-history-api'           : 'lib/history/history',
        'pdfjs-dist/build/pdf'        : 'lib/pdfjs/build/pdf',
        'pdfjs-dist/build/pdf.worker' : 'lib/pdfjs/build/pdf.worker',
        'mathJax'           : [
            '../../../taoQtiItem/views/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML-full',
            '../../../taoQtiItem/views/js/MathJaxFallback'
        ],
        'ckeditor'          : 'lib/ckeditor/ckeditor',
        'interact'          : 'lib/interact',
        'd3'                : 'lib/d3js/d3.min',
        'c3'                : 'lib/c3js/c3.min',
//locale loader
        'i18ntr'            : '../locales/<?=get_data('locale')?>',
//extension aliases, and controller loading in prod mode
    <?php foreach (get_data('extensionsAliases') as $name => $path) :?>
        '<?=$name?>'        : '<?=$path?>',
        <?php if(tao_helpers_Mode::is('production')):?>
            <?php if($name == 'tao'): ?>
                'controller/routes' : '<?=$path?>/controllers.min',
            <?php else : ?>
                '<?=$name?>/controller/routes' : '<?=$path?>/controllers.min',
            <?php endif ?>
        <?php endif?>
    <?php endforeach?>
   },
   shim : {
        'moment'                : { exports : 'moment' },
        'ckeditor'              : { exports : 'CKEDITOR' },
        'ckeditor-jquery'       : ['ckeditor'],
        'class'                 : { exports : 'Class'},
        'c3'                    : { deps : ['css!lib/c3js/c3.css']},
        'mathJax' : {
            exports : "MathJax",
            init : function(){
                if(window.MathJax){
                    MathJax.Hub.Config({showMathMenu:false, showMathMenuMSIE:false});
                    MathJax.Hub.Startup.onload();
                    return MathJax;
                }
            }
        }
    }
});
