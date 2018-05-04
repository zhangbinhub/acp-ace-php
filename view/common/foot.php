<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
if ($GLOBALS['alone_page']) {
    $charset = $GLOBALS['charset'];
    /**
     * jquery library
     */
    echo '<!--[if !IE]>-->';
    echo '<script type="text/javascript">';
    echo 'window.jQuery || document.write("<script src=\'/assets/js/jquery.js?v=2.1.1\' type=\'text/javascript\'>"+"<"+"/script>");';
    echo '</script>';
    echo '<!--<![endif]-->';
    echo '<!--[if IE]>';
    echo '<script type="text/javascript">';
    echo 'window.jQuery || document.write("<script src=\'/assets/js/jquery1x.js?v=1.11.1\' type=\'text/javascript\'>"+"<"+"/script>");';
    echo '</script>';
    echo '<![endif]-->';
    echo '<script type="text/javascript">';
    echo 'if(\'ontouchstart\' in document.documentElement) document.write("<script src=\'/assets/js/jquery.mobile.custom.js?v=1.4.5\' type=\'text/javascript\'>"+"<"+"/script>");';
    echo '</script>';

    /**
     * base script
     */
    echo '<script type="text/javascript" src="/assets/js/bootstrap.min.js?v=3.3.5"></script>';
    echo '<!--[if lte IE 8]>';
    echo '<script type="text/javascript" src="/assets/js/excanvas.js?v=2.0"></script>';
    echo '<![endif]-->';
    /**
     * jquery plugin scripts
     */
    echo '<script type="text/javascript" src="/assets/js/jquery-ui.js?v=1.11.2"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery-ui.custom.js?v=1.11.2"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.ui.touch-punch.js?v=0.2.3"></script>';
    echo '<script type="text/javascript" src="/assets/js/chosen.jquery.js?v=1.2.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.gritter.js?v=1.7.4"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.hotkeys.js?v=2.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.autosize.js"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.maskedinput.js?v=1.4.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.validate.js?v=1.13.1"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.bootstrap-duallistbox.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/jquery.colorbox.js?v=1.5.14"></script>';
    echo '<script type="text/javascript" src="/assets/js/jqGrid/i18n/grid.locale-cn.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/jqGrid/jquery.jqGrid.src.js?v=4.6.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/flot/jquery.flot.js?v=0.8.3"></script>';
    echo '<script type="text/javascript" src="/assets/js/flot/jquery.flot.pie.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/flot/jquery.flot.resize.js?v=1.1"></script>';
    echo '<script type="text/javascript" src="/script/jquery/ui/mask/jquery.loadmask.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/script/jquery/ui/ztree/jquery.ztree.all.js?v=3.5.24"></script>';
    echo '<script type="text/javascript" src="/script/jquery/ui/ztree/jquery.ztree.exhide.js?v=3.5.24"></script>';
    echo '<script type="text/javascript" src="/script/jquery/lib/ajaxfileupload.js?v=1.0"></script>';

    /**
     * specific plugin scripts
     */
    echo '<script type="text/javascript" src="/assets/js/bootbox.js?v=4.3.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/date-time/bootstrap-datepicker.js?v=2.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/bootstrap-wysiwyg.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/bootstrap-colorpicker.js?v=2.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/fuelux/fuelux.spinner.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/x-editable/bootstrap-editable.js?v=1.5.1"></script>';
    echo '<script type="text/javascript" src="/assets/js/x-editable/ace-editable.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/fuelux/fuelux.wizard.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/additional-methods.js?v=1.13.1"></script>';
    echo '<script type="text/javascript" src="/script/plugins/lib/dropzone.js?v=1.0"></script>';

    /**
     * platform plugin scripts
     */
    echo '<script type="text/javascript" src="/assets/js/ace/elements.scroller.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.colorpicker.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.fileinput.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.typeahead.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.wysiwyg.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.spinner.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.treeview.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.wizard.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/elements.aside.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.ajax-content.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.touch-drag.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.sidebar.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.sidebar-scroll-1.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.submenu-hover.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.widget-box.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.settings.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.settings-rtl.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.settings-skin.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.widget-on-reload.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/assets/js/ace/ace.searchbox-autocomplete.js?v=1.0"></script>';

    /**
     * crypto scripts
     */
    echo '<script type="text/javascript" src="/script/tools/security/CryptoJS/components/core-min.js?v=3.1.2"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/CryptoJS/components/cipher-core-min.js?v=3.1.2"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/CryptoJS/components/mode-ecb-min.js?v=3.1.2"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/CryptoJS/rollups/md5.js?v=3.1.2"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/CryptoJS/rollups/aes.js?v=3.1.2"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/RSA/base64.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/RSA/jsbn.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/RSA/jsbn2.js?v=1.2"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/RSA/prng4.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/RSA/rng.js?v=1.0"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/RSA/rsa.js?v=1.1"></script>';
    echo '<script type="text/javascript" src="/script/tools/security/RSA/rsa2.js?v=1.1"></script>';

    /**
     * glob variable scripts
     */
    echo '<script type="text/javascript" src="/script/values/Global_IconClasses.js?v=1.0.0"></script>';

    /**
     * custom scripts
     */
    echo '<script type="text/javascript" charset="utf-8" src="/script/tools/charts/echarts.min.js?v=3.6.1"></script>';
    echo '<script type="text/javascript" charset="utf-8" src="/script/tools/charts/theme/dark.js?v=3.6.1"></script>';
    echo '<script type="text/javascript" charset="utf-8" src="/script/tools/charts/theme/shine.js?v=3.6.1"></script>';
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/tools.js?v=1.0.0"></script>';
    /**
     * aui
     */
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/ui/aui.js?v=1.0.0"></script>';
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/ui/aui-grid.js?v=1.0.0"></script>';
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/ui/aui-dialog.js?v=1.0.0"></script>';
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/ui/aui-chart.js?v=1.0.0"></script>';
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/ui/aui-tree.js?v=1.0.0"></script>';
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/ui/aui-element.js?v=1.0.0"></script>';

    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/security/security.js?v=1.0.0"></script>';
    echo '<script type="text/javascript" charset="' . $charset . '" src="/script/tools/file/file.js?v=1.0.0"></script>';
}
echo '<!--[if lte IE 8]>';
echo '<script type="text/javascript" src="/script/jquery/lib/jquery.placeholder.min.js?v=2.1.3"></script>';
echo '<script type="text/javascript">';
echo '$(function () {$("input, textarea").placeholder();});';
echo '</script>';
echo '<![endif]-->';