<?php
if ($GLOBALS['alone_page']) {
    echo '<!-- bootstrap & fontawesome -->';
    echo '<link rel="stylesheet" href="/assets/css/bootstrap.min.css?v=3.3.5" />';
    echo '<link rel="stylesheet" href="/assets/css/font-awesome.min.css?v=4.7.0" />';

    echo '<!-- page specific plugin styles -->';
    echo '<link rel="stylesheet" href="/assets/css/bootstrap-duallistbox.css?v=1.0.0" />';
    echo '<link rel="stylesheet" href="/assets/css/bootstrap-multiselect.css?v=1.0.0" />';
    echo '<link rel="stylesheet" href="/assets/css/bootstrap-datetimepicker.css?v=3.1.3" />';
    echo '<link rel="stylesheet" href="/assets/css/bootstrap-timepicker.css?v=1.0.0" />';
    echo '<link rel="stylesheet" href="/assets/css/bootstrap-editable.css?v=1.5.1" />';
    echo '<link rel="stylesheet" href="/assets/css/jquery-ui.css?v=1.11.2" />';
    echo '<link rel="stylesheet" href="/assets/css/jquery-ui.custom.css?v=1.11.2" />';
    echo '<link rel="stylesheet" href="/assets/css/ui.jqgrid.css?v=1.0.0" />';
    echo '<link rel="stylesheet" href="/assets/css/chosen.css?v=1.2.0" />';
    echo '<link rel="stylesheet" href="/assets/css/datepicker.css?v=2.0" />';
    echo '<link rel="stylesheet" href="/assets/css/daterangepicker.css?v=2.0" />';
    echo '<link rel="stylesheet" href="/assets/css/colorpicker.css?v=2.0" />';
    echo '<link rel="stylesheet" href="/assets/css/jquery.gritter.css?v=1.0.0" />';
    echo '<link rel="stylesheet" href="/assets/css/dropzone.css?v=1.0.0" />';
    echo '<link rel="stylesheet" href="/assets/css/colorbox.css?v=1.0.0" />';
    echo '<link rel="stylesheet" href="/style/ztree/zTreeStyle/zTreeStyle.css?v=3.5.19" />';
    echo '<link rel="stylesheet" href="/style/ztree/diySkin.css?v=1.0.0" />';

    echo '<!-- default styles -->';
    echo '<link rel="stylesheet" href="/assets/css/ace.css?v=1.0.0" />';
    echo '<!--[if lte IE 9]>';
    echo '<link rel="stylesheet" href="/assets/css/ace-part2.css?v=1.0.0" />';
    echo '<![endif]-->';
    echo '<!--[if lte IE 9]>';
    echo '<link rel="stylesheet" href="/assets/css/ace-ie.css?v=1.0.0" />';
    echo '<![endif]-->';

    echo '<!-- skins styles -->';
    echo '<link rel="stylesheet" href="/style/acp-skins.css?v=1.0.0">';

    echo '<!-- inline styles related to this page -->';
    echo '<link rel="stylesheet" href="/style/customStyle.css?v=1.0.0" />';
}