jQuery(function ($) {
    $(function () {
        if ($("#pp_results").html() != "") {
            $("#pp_results").css("display", "block")
        }
        $("#pre_list").on("click", "a.ppremove_a", function () {
            $(this).closest("li").remove();
            $('#pre_list').trigger("sortstop");
        });
        $("#people_tab_new").click(function () {
            $("#pedit").hide();
            $("#pnew").show();
            $("#submitted").val("1");
            $("#pp_results").css("display","none");;
            $("#pp_del_id").val("0");
        });
        $("#people_tab_edit").click(function () {
            $("#pedit").show();
            $("#pnew").hide();
            $('#people > option:eq(0)').prop('selected', true);
            $("#pp_results").html("");
            $("#pp_del_id").val("0");
        });
        $(".pp_bg").spectrum({
            color: "#fff",
            change: function (color) {
                $("#pp_bg-log").text("change called: " + color.toHexString());
                $(".pps_listing").css("background-color", color.toHexString());
                $("#pp_bg_final").val(color.toHexString());
            }
        });

        $("#wx_slide").slider({
            min: +50,
            max: +1000,
            slide: function (event, ui) {
                $("#wx").val(ui.value);
            }
        });

        $("#hx_slide").slider({
            min: +50,
            max: +1000,
            slide: function (event, ui) {
                $("#hx").val(ui.value);
            }
        });

        $("#mrmt_slide").slider({
            min: +0,
            max: +100,
            slide: function (event, ui) {
                $("#mrmt").val(ui.value);
            }
        });

        $("#add_people_btn").click(function () {
            if ($("#peopleid" + $("#people").val()).length == 0) {
                $("#pre_list").append('<li id="peopleid' + $("#people").val() + '" class="ui-state-default"><span style="cursor: move; position: absolute;margin-left: -1.3em;" class="ui-icon ui-icon-arrowthick-2-n-s"></span>' + $("#people").val() + ": " + $("#people option:selected").text() + ' <a href="#remove" class="ppremove_a" style="color:red;">remove</a></li>');
                $("#pp_sort").val($("#pp_sort").val() + ";" + $("#people").val());
            }
        });

        $("#pre_list").sortable();
        $("#pre_list").disableSelection();

        $("#pre_list").on("sortstop", function () {
            $("#pp_sort").val("");
            $("#pre_list li").each(function () {
                $("#pp_sort").val($("#pp_sort").val() + ";" + $(this).text().split(":")[0]);
            });
        });

        $("#listings").on("change", function () {
            $("#submitted").val($("#listings").val().split("|pp_split|")[0]);
            var json_deets = jQuery.parseJSON($("#listings").val().split("|pp_split|")[1]);
            $("#pp_css").text(json_deets.css);
            $("#mrmt").val(json_deets.mrmt);
            $("#wx").val(json_deets.wx);
            $("#hx").val(json_deets.hx);
            $("#pp_bg").spectrum("set", json_deets.bg);
            $("#pp_bg_final").val(json_deets.bg);

            selectLength("mrmt_length", json_deets.mrmt_length);
            selectLength("wx_length", json_deets.wx_length);
            selectLength("hx_length", json_deets.hx_length);
            selectLength("pp_float", json_deets.pp_float);

            $("#pp_sort").val("");
            $("#pre_list").empty();
            $("#pp_results").html("<p>This list's shortcode is: [peoplepop list='" + $("#submitted").val() + "']<br /><br />Copy + Paste into any Post or Page</p>");
            $("#pp_results").css("display", "block");
            for (var i = 0; i < json_deets.people.length; i++) {
                var cnt = 0;
                $("#people > option").each(function () {
                    if (this.value == json_deets.people[i]) {
                        $('#people').prop('selectedIndex', cnt);
                        $('#add_people_btn').trigger("click");
                    }
                    cnt++;
                });
            }
        });

        $("#pp_delete").click(function () {
            $("#pp_del_id").val($("#listings").val().split("|pp_split|")[0]);
            var killit = confirm("Are you sure you want to delete this listing?");
            if (killit) {
                $(".pp_form").submit();
            }
        });
    });
    function selectLength(measure_field, measure_type) {
        $('#' + measure_field + ' option[value="' + measure_type + '"]').attr('selected', 'selected');
    }

    setInterval(function () {
        if ($("#wx").val() != "") {
            $(".pps_listing").css("width", $("#wx").val() + $("#wx_length").val());
        }
        if ($("#wh").val() != "") {
            $(".pps_listing").css("height", $("#hx").val() + $("#hx_length").val());
        }
        if ($("#mrmt").val() != "") {
            $(".pps_listing").css({
                'margin-right': $("#mrmt").val() + $("#mrmt_length").val(),
                'margin-bottom': $("#mrmt").val() + $("#mrmt_length").val()
            });
        }
        $(".pps_listing img").css({'width': '100%', 'height': 'auto'});
        $("#pps_css").html("");
        $("<style>")
            .prop("type", "text/css")
            .html($("#pp_css").val().replace("pp_", "pps_"))
            .appendTo("#pps_css");
    }, 1000);
});