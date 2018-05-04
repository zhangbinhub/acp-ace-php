# jquery-ui.js
```
jQuery UI Dialog 1.11.2：
（1）10109行，zIndex: 1000；
（2）10383行，$(this.uiDialog).css("z-index", parseInt($(this.uiDialog).css("z-index")) + this.options.zIndex);
```
# jquery.jqGrid.src.js
```
jqGrid  4.6.0
6002行，zIndex : 2000
```
# jquery.validate.js
```
361行，delegate函数：
function delegate( event ) {
    var validator = $.data( this[ 0 ].form, "validator" );
    if(validator!=undefined){
     var eventType = "on" + event.type.replace( /^validate/, "" ),
      settings = validator.settings;
     if ( settings[ eventType ] && !this.is( settings.ignore ) ) {
      settings[ eventType ].call( validator, this[ 0 ], event );
     }
    }
   }
```
# jquery.bootstrap-duallistbox.js
```
421行，429行，col-md-6 改为 col-sm-6
```