/**
 * Created by taichi on 2017/07/18.
 */

(function($) {
    "use strict";
    var listUrl = "/wp-content/plugins/font-stream/list.json";
    var fontList = null;
    var onFetch = false;

    $(document).ready(function () {
        var $form = $("form.font-stream");
        var $button = $form.find("button.add");
        var $fontList = $form.find("ul.fontList");

        var appendFont = function(){
            console.log(fontList);
            var $select = $("<select />");
            for(var i=0; i<fontList.length; i++){
                var font = fontList[i];
                var $option = $("<option />");
                $option.attr("value", i);
                $option.text(font.fontName);
                console.log(font.fontName);
                $select.append($option);
            }
            var $li = $("<li />");
            $li.append($select);
            $fontList.append($li);
            onFetch = false;
        };


        $button.on("click", function(e){
            if(onFetch){
                return;
            }
            onFetch = true;
            e.preventDefault();
            if(fontList === null) {
                $.ajax({
                    url: listUrl,
                    dataType: "json"
                }).done(function (json) {
                    fontList = json;
                    appendFont();
                });
            } else {
                appendFont();
            }
        });
    });
})(jQuery);





