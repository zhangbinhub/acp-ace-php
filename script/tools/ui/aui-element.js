/**
 * aui element
 *
 * @param $
 */
(function ($) {

    var element = {};

    /** validation start */
    /**
     * 初始化表单校验插件
     *
     * @param formid
     * @param messages
     * @param rules
     * @param submitFun
     *            校验通过后提交表单的自定义函数 function(form)
     * @param invalidFun
     *            表单校验失败时的回调函数 function(form)
     */
    element.initValidate = function (formid, messages, rules, submitFun, invalidFun) {
        var validate = $('#' + formid).validate(
            {
                errorElement: 'div',
                errorClass: 'help-block col-xs-12 col-sm-reset inline',
                focusInvalid: true,
                ignore: ".ignore",
                rules: rules,
                messages: messages,
                highlight: function (e) {
                    $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
                    if ($(e).parent().find('i.fa-times-circle').length == 0) {
                        $(e).parent().append('<i class="ace-icon fa fa-times-circle"></i>');
                    }
                },
                success: function (e) {
                    $(e).parent().find('i.fa-times-circle').remove();
                    $(e).closest('.form-group').removeClass('has-error');
                    $(e).remove();
                },
                errorPlacement: function (error, element) {
                    if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                        var controls = element.closest('div[class*="col-"]');
                        if (controls.find(':checkbox,:radio').length > 1)
                            controls.append(error);
                        else
                            error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
                    } else if (element.is('.select2')) {
                        error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
                    } else if (element.is('.chosen-select')) {
                        error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
                    } else {
                        error.insertAfter(element.closest('div[class*="col-"]'));
                    }
                },
                submitHandler: function (form) {
                    if (typeof (submitFun) == "function") {
                        submitFun(form);
                    } else {
                        form.submit();
                    }
                },
                invalidHandler: function (form) {
                    if (typeof (invalidFun) == "function") {
                        invalidFun(form);
                    }
                }
            });
        $('#' + formid).find("button[type='reset'],input[type='reset']").click(
            function () {
                setTimeout(function () {
                    element.resetValidate(formid);
                }, 0);
            });
        return validate;
    };

    /**
     * 重置校验提示
     *
     * @param formid
     */
    element.resetValidate = function (formid) {
        setTimeout(function () {
            $('#' + formid).validate().resetForm();
            $('#' + formid).find('.form-group').removeClass('has-error');
            $('#' + formid).find('.form-group i.fa-times-circle').remove();
        }, 0);
    };

    /**
     * 执行校验
     *
     * @param formid
     * @returns
     */
    element.doValidate = function (formid) {
        return $('#' + formid).validate().form();
    };
    /** validation end */

    /** select start */
    /**
     * 动态加载下拉框
     *
     * @param id
     * @param datas
     *            array:[{0:value,1:text},{0:value,1:text}...]
     * @param selectedData
     *            “,”分隔
     * @param hasNull
     * @param isIcon
     */
    element.updateSelectItem = function (id, datas, selectedData, hasNull, isIcon) {
        var value = selectedData.split(",");
        var options = new StringBuffer();
        if (hasNull) {
            options.append("<option value=''></option>");
        }
        if (datas && datas.length > 0) {
            for (var i = 0; i < datas.length; i++) {
                if (isIcon) {
                    options.append("<option value='");
                    options.append(datas[i]);
                    options.append("' ");
                    if (_global_tools_obj.strInArray(datas[i], value)) {
                        options.append("selected");
                    }
                    options.append(">");
                    options.append(datas[i]);
                    options.append("</option>");
                } else {
                    options.append("<option value='");
                    options.append(datas[i][0]);
                    options.append("' ");
                    if (_global_tools_obj.strInArray(datas[i][0], value)) {
                        options.append("selected");
                    }
                    options.append(">");
                    options.append(datas[i][1]);
                    options.append("</option>");
                }
            }
        }
        $("#" + id).html(options.toString());
    };

    /**
     * 设置select组件选中项
     *
     * @param id
     * @param selectedData
     *            “,”分隔
     */
    element.setSelectedItem = function (id, selectedData) {
        var data = selectedData.split(",");
        $("#" + id).find("option").prop("selected", false);
        for (var i = 0; i < data.length; i++) {
            $("#" + id).find("option[value='" + data[i] + "']").prop("selected", true);
        }
    };

    /**
     * 生成可搜索的下拉框
     *
     * @param ids
     *            多个id逗号隔开，以#开头
     */
    element.initChosen = function (ids) {
        $(ids).chosen({
            allow_single_deselect: true,
            no_results_text: "没有匹配项",
            placeholder_text: "请选择"
        });
        $(window).on('resize.chosen', function () {
            $(ids).each(function () {
                var $this = $(this);
                $this.next().css({
                    'width': $this.parent().width()
                });
            })
        }).trigger('resize.chosen');
        $(document).on('settings.ace.chosen', function (e, event_name, event_val) {
            if (event_name != 'sidebar_collapsed')
                return;
            $(ids).each(function () {
                var $this = $(this);
                $this.next().css({
                    'width': $this.parent().width()
                });
            })
        });
    };

    /**
     * 刷新下拉框尺寸
     */
    element.resizeChosen = function () {
        setTimeout(function () {
            $(window).trigger('resize.chosen');
        }, 0);
    };

    /**
     * 刷新下拉框
     *
     * @param id
     */
    element.refreshChosen = function (id) {
        $("#" + id).trigger("chosen:updated");
        element.resizeChosen();
    };

    /**
     * 设置下拉框选中项
     *
     * @param id
     * @param selectData
     *            “,”分隔
     */
    element.setChosenSelected = function (id, selectData) {
        element.setSelectedItem(id, selectData);
        element.refreshChosen(id);
    };

    /**
     * 生成双重选择列表
     *
     * @param id
     */
    element.initDuallist = function (id, param) {
        var param = $.extend({
            filterTextClear: '显示全部',
            filterPlaceHolder: '过滤...',
            moveSelectedLabel: '移动选中项',
            moveAllLabel: '移动全部',
            removeSelectedLabel: '移除选中项',
            removeAllLabel: '移除全部',
            selectorMinimalHeight: 100,
            infoText: '总计 {0}',
            infoTextEmpty: '空',
            selectedListLabel: '<div class="col-sm-12 well well-sm center">已选</div>',
            nonSelectedListLabel: '<div class="col-sm-12 well well-sm center">备选</div>',
            infoTextFiltered: '<span class="label label-purple label-lg">筛选</span>  {0} 项，共 {1} 项'
        }, param);
        var duallist = $('#' + id).bootstrapDualListbox(param);
        var container = duallist.bootstrapDualListbox('getContainer');
        container.find('.btn').addClass('btn-white btn-info btn-bold');
        $(document).one('ajaxloadstart.page', function (e) {
            $('#' + id).bootstrapDualListbox('destroy');
        });
    };

    /**
     * 刷新双重选择列表
     *
     * @param id
     */
    element.refreshDuallist = function (id) {
        $("#" + id).bootstrapDualListbox("refresh");
    };

    /**
     * 设置左右选择选中项
     *
     * @param id
     * @param selectedData
     *            “,”分隔
     */
    element.setDuallistSelected = function (id, selectedData) {
        element.setSelectedItem(id, selectedData);
        element.refreshDuallist(id);
    };

    /**
     * 设置左右选择可编辑
     *
     * @param id
     * @param isEnabled
     */
    element.isEnabledDuallistSelected = function (id, isEnabled) {
        var container = $("#" + id).bootstrapDualListbox("getContainer");
        if (isEnabled) {
            container.find("input,button,select").prop("disabled", false);
        } else {
            container.find("input,button,select").prop("disabled", true);
        }
    };

    /**
     * 生成多选
     *
     * @param id
     * @param param
     */
    element.initSelete2 = function (id, param) {
        var options = $.extend({
            placeholder: "点击选择",
            allowClear: true,
            minimumInputLength: 0,
            formatInputTooShort: "输入的字符太短",
            formatNoMatches: "没有匹配的信息",
            formatSearching: "查询中..."
        }, param);
        $('#' + id).select2(options);
    };
    /** select end */

    /** spinner start */
    /**
     * 生成数字选择组件
     *
     * @param id
     * @param param
     */
    element.initSpinner = function (id, param) {
        var options = $.extend({}, {
            min: 0,
            max: 999,
            step: 1,
            touch_spinner: true,
            on_sides: true,
            icon_up: 'ace-icon fa fa-plus bigger-110',
            icon_down: 'ace-icon fa fa-minus bigger-110',
            btn_up_class: 'btn-success',
            btn_down_class: 'btn-danger'
        }, param);
        $("#" + id).ace_spinner(options);
    };

    /**
     * 刷新数字选择组件
     *
     * @param id
     * @param param
     */
    element.refreshSpinner = function (id, param) {
        setTimeout(function () {
            var input = $("#" + id).clone();
            var spinner = $("#" + id).closest(".ace-spinner");
            var parent = spinner.parent();
            var index = spinner.index();
            var width = spinner.width();
            var appendFlag = 0;// 0-first，1-last，2-after
            var prev = null;
            if (index == 0) {
                appendFlag = 0;
            } else if (index == parent.children().length) {
                appendFlag = 1;
            } else {
                appendFlag = 2;
                prev = parent.children().eq(index - 1);
            }
            $("#" + id).ace_spinner("destroy");
            switch (appendFlag) {
                case 0:
                    parent.prepend(input);
                    break;
                case 1:
                    parent.append(input);
                    break;
                case 2:
                    prev.after(input);
                    break;
            }
            element.initSpinner(id, param);
            $("#" + id).closest(".ace-spinner").width(width);
        }, 0);
    };

    /**
     * 数字选择器设置值
     *
     * @param id
     * @param value
     */
    element.setSpinnerValue = function (id, value) {
        $("#" + id).ace_spinner('value', value);
    };

    /**
     * 设置数字选择器可用状态
     *
     * @param id
     * @param isEnabled
     */
    element.isEnabledSpinner = function (id, isEnabled) {
        if (isEnabled) {
            $("#" + id).ace_spinner("enable");
        } else {
            $("#" + id).ace_spinner("disable");
        }
    };
    /** spinner end */

    /** colorpicker start */
    /**
     * 生成颜色选择器
     */
    element.initColorpicker = function (id) {
        $("#" + id).colorpicker();
    };

    /**
     * 刷新颜色选择器
     *
     * @param id
     * @param value
     */
    element.refreshColorpicker = function (id, value) {
        $("#" + id).val(value);
        $("#" + id).colorpicker('update');
    };

    /**
     * 生成颜色选择
     */
    element.initColorSelect = function (id) {
        $("#" + id).ace_colorpicker();
    };

    /**
     * 刷新颜色选择
     *
     * @param id
     * @param value
     */
    element.setColorSelected = function (id, value) {
        $("#" + id).ace_colorpicker('pick', value);
    };

    /**
     * 刷新颜色选择
     *
     * @param id
     * @param value
     */
    element.refreshColorSelect = function (id, value) {
        setTimeout(function () {
            $("#" + id).ace_colorpicker('destroy');
            element.initColorSelect(id);
        }, 0);
    };
    /** colorpicker end */

    /** file input start */
    /**
     * 生成文件选择组件
     *
     * @param id
     * @param param
     */
    element.initFileInput = function (id, param) {
        var param = $.extend({
            no_file: '没有文件 ...',
            btn_choose: '选择',
            btn_change: '更改',
            droppable: false,
            onchange: null,
            thumbnail: false
        }, param);
        if (param.allowExt) {
            param.allowExt = null;
        }
        if (param.denyExt) {
            param.denyExt = null;
        }
        if (param.allowMime) {
            param.allowMime = null;
        }
        if (param.denyMime) {
            param.denyMime = null;
        }
        $('#' + id).ace_file_input(param);
    };

    /**
     * 刷新文件选择组件
     *
     * @param id
     * @param param
     */
    element.refreshFileInput = function (id, param) {
        setTimeout(function () {
            $('#' + id).parent().replaceWith("<input type=\"file\" id=\"" + id + "\" name=\"" + id + "\" />");
            element.initFileInput(id, param);
        }, 0);
    };
    /** file input end */

    /** date pick start */
    /**
     * 生成日期选择组件
     *
     * @param id
     * @param param
     * @param changeFun
     */
    element.initDatePicker = function (id, param, changeFun) {
        var options = $.extend({
            autoclose: true,
            todayHighlight: true,
            minViewMode: 0,// 0|"months"|"years"
            format: "yyyy-mm-dd",
            language: "cn",
            clearBtn: true,
            orientation: "top"// auto|left|right|top|bottom，ep:top right
        }, param);
        $("#" + id).datepicker(options).next().on('changeDate', function () {
            if (typeof (changeFun) == "function") {
                changeFun();
            }
        }).on(ace.click_event, function () {
            $(this).prev().focus();
        });
    };
    /** date pick end */

    /** colorbox start */
    element.initColorbox = function ($objs, params) {
        params = $.extend({
            hideOverFlow: false,
            previous: '<i class="ace-icon fa fa-arrow-left"></i>',
            next: '<i class="ace-icon fa fa-arrow-right"></i>',
            loadingGraphic: '<i class="ace-icon fa fa-spinner orange fa-spin"></i>'
        }, params);
        var $overflow = "";
        var colorbox_params = $.extend({
            rel: 'colorbox',
            reposition: true,
            scalePhotos: true,
            scrolling: false,
            trapFocus: false,
            previous: params.previous,
            next: params.next,
            close: '&times;',
            className: 'colorbox-class',
            current: '{current} of {total}',
            maxWidth: '100%',
            maxHeight: '100%',
            onOpen: function () {
                if (params.hideOverFlow) {
                    $overflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                }
            },
            onClosed: function () {
                if (params.hideOverFlow) {
                    document.body.style.overflow = $overflow;
                }
            },
            onComplete: function () {
                $.colorbox.resize();
            }
        }, params);
        $objs.colorbox(colorbox_params);
        $("#cboxLoadingGraphic").html(params.loadingGraphic);
    };

    element.appendColorbox = function ($obj, params) {
        params = $.extend({
            hideOverFlow: false,
            previous: '<i class="ace-icon fa fa-arrow-left"></i>',
            next: '<i class="ace-icon fa fa-arrow-right"></i>'
        }, params);
        var $overflow = "";
        var colorbox_params = $.extend({
            rel: 'colorbox',
            reposition: true,
            scalePhotos: true,
            scrolling: false,
            trapFocus: false,
            previous: params.previous,
            next: params.next,
            close: '&times;',
            className: 'colorbox-class',
            current: '{current} of {total}',
            maxWidth: '100%',
            maxHeight: '100%',
            onOpen: function () {
                if (params.hideOverFlow) {
                    $overflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                }
            },
            onClosed: function () {
                if (params.hideOverFlow) {
                    document.body.style.overflow = $overflow;
                }
            },
            onComplete: function () {
                $.colorbox.resize();
            }
        }, params);
        $obj.click(function (e) {
            $.colorbox($.extend(colorbox_params, {href: $(this).attr('href'), open: true}));
            e.preventDefault();
        });
    };
    /** colorbox end */

    AUI.element = element;
})($);