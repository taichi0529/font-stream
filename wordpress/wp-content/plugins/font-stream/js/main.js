/**
 * Created by taichi on 2017/07/18.
 */

(function ($) {
    "use strict";
    var listUrl = "/wp-content/plugins/font-stream/list.json";
    var fontList = null;
    var onFetch = true;


    $(document).ready(function () {
        var $form = $("form.font-stream");
        var $buttonAdd = $form.find("button.add");
        var $buttonSave = $form.find("button.save");
        var $fontList = $form.find("tbody.fontList");


        var createFontOptions = function (data) {
            var $tr = $('<tr class="row"/>');
            var $tdFont = $('<td class="font" />');
            var $tdWeight = $('<td class="weight" />');
            var $tdType = $('<td class="type" />');
            var $tdDelete = $('<td class="delete" />');
            $tr.append($tdFont);
            $tr.append($tdWeight);
            $tr.append($tdType);
            $tr.append($tdDelete);

            var $font = $('<select class="font" />');
            for (var font in fontList) {
                var $option = $("<option />");
                $option.attr("value", font);
                $option.text(font);
                $font.append($option);
            }

            $font.on("change", function () {
                $tdWeight.empty();
                $tdType.empty();
                var font = $(this).val();
                var $weight = getWeightOptions(font, data);
                var $type = getTypeOptions(font, data);


                if ($weight !== null) {
                    $tdWeight.append($weight);
                }
                if ($type !== null) {
                    $tdType.append($type);
                }

            });
            var $del = $('<a href="#">削除</a>');


            $del.on('click', function (e) {
                e.preventDefault();
                $tr.remove();
            });
            $tdFont.append($font);
            $tdDelete.append($del);

            $fontList.append($tr);
            if (typeof data !== 'undefined' && data.font) {
                $font.val(data.font);
            }
            $font.trigger("change");
        };

        var getWeightOptions = function (font, data) {
            var weightList = fontList[font].weight;
            if (weightList.length === 0) {
                return null;
            }
            var $weight = $('<select class="weight" />');
            for (var i = 0; i < weightList.length; i++) {
                var $option = $("<option />");
                $option.attr("value", weightList[i]);
                $option.text(weightList[i]);
                $weight.append($option);
            }
            if (typeof data !== 'undefined' && data.weight) {
                $weight.val(data.weight);
            }
            return $weight;
        };

        var getTypeOptions = function (font, data) {
            var typeList = fontList[font].type;
            if (typeList.length === 0) {
                return null;
            }
            var $type = $('<select class="type" />');
            for (var i = 0; i < typeList.length; i++) {
                var $option = $("<option />");
                $option.attr("value", typeList[i]);
                var text = "";
                if (typeList[i] === "pro") {
                    text = "プロポーショナル";
                } else if (typeList[i] === "mono") {
                    text = "等幅";
                }
                $option.text(text);
                $type.append($option);
            }
            if (typeof data !== 'undefined' && data.type) {
                $type.val(data.type);
            }
            return $type;
        };


        $buttonAdd.on("click", function (e) {
            e.preventDefault();
            if (onFetch || fontList === null) {//json取得中は何もしない
                return;
            }
            createFontOptions();
        });

        var createCss = function (token, options) {
            var css = "";
            options.map(function (row) {
                var props = fontList[row.font];
                var fontFamily = props.fontFamily;

                if (row.type === 'mono') {
                    fontFamily += 'Mono';
                }
                var weight = "none";
                var url = props.path + fontFamily;
                if (row.weight !== null) {
                    weight = row.weight;
                    fontFamily = fontFamily + "-" + weight;
                }
                url =  url + "-" + weight;



                css += '@font-face{';
                css += 'font-family: "' + fontFamily + '";'
                css += 'font-weight: normal;'

                css += 'src: ';

                props.format.map(function (format) {
                    var extension = null;
                    switch (format) {
                        case "embedded-opentype":
                            extension = 'eot';
                            break;
                        case "woff":
                            extension = 'woff';
                            break;
                        case "truetype":
                            extension = 'ttf';
                            break;
                        case "opentype":
                            extension = 'otf';
                            break;
                        default:
                            return;
                    }
                    css += 'url("https://www.font-stream.com/fontdata/' + fontFamily + '.' + extension + '?token=' + token + '") ';
                    css += 'format("' + format + '"),';
                });

                css = css.substr(0, css.length - 1) + ";";

                css += '}';
            });
            return css;
        };

        $buttonSave.on("click", function (e) {
            e.preventDefault();
            if (onFetch) {//json取得中は何もしない
                return;
            }
            var token = $("#token").val();
            var $rows = $fontList.find('.row');
            var options = [];
            $rows.each(function () {
                var result = {
                    "font": null,
                    "weight": null,
                    "type": null
                };
                var $font = $(this).find('select.font');
                result.font = $font.val();
                var $weight = $(this).find('select.weight');
                if (typeof $weight.val() !== "undefined") {
                    result.weight = $weight.val();
                }
                var $type = $(this).find('select.type');
                if (typeof $type.val() !== "undefined") {
                    result.type = $type.val();
                }
                options.push(result);
            });
            var css = createCss(token, options);
            var endpoint = FONT_STREAM.endpoint + "?action=font_stream_save";
            $.ajax({
                url: endpoint,
                dataType: "json",
                method: "POST",
                data: {
                    token: token,
                    css: css,
                    options: options
                },
            }).done(function (json) {
                alert("更新完了");
                console.log(json);
            }).fail(function () {
                alert("保存に失敗しました。");
            });

        });

        $.ajax({
            url: listUrl,
            dataType: "json"
        }).done(function (json) {
            fontList = json;
            onFetch = false;
            var endpoint = FONT_STREAM.endpoint + "?action=font_stream_load";
            $.ajax({
                url: endpoint,
                dataType: "json"
            }).done(function (json) {
                console.log(json);
                $("#token").val(json.token);

                for (var i = 0; i < json.options.length; i++) {
                    createFontOptions(json.options[i]);
                }


            });
        });


    });
})(jQuery);





