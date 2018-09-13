var select_dates = {};
var cur_one, datepicker, sk;
var sym_count = 0;
var summ = 0;
var trans = {
    ru: function(i, v) {
        return "Ð¾Ñ‚ " + i + " Ð´Ð½. - ÑÐºÐ¸Ð´ÐºÐ° " + v + "%"
    },
    kg: function(i, v) {
        return i + " ÐºÒ¯Ð½Ð´Ó©Ð½ - Ð°Ñ€Ð·Ð°Ð½Ð´Ð°Ñ‚ÑƒÑƒ " + v + "%"
    }
};
document.addEventListener("DOMContentLoaded", function() {
    sk = $("#skidka");

    function showModalDateSel(one) {
        if (datepicker !== undefined) {
            datepicker.destroy()
        }
        $("#calend_rows").hide();
        $("#calend_rows input").prop("checked", false);
        $("#calend_rows .one_inp:last").hide();
        $(".items", sk).html("");
        sk.hide();
        cur_one = one;
        if (skidka[parseInt(cur_one.data("id"))] !== undefined) {
            $.each(skidka[parseInt(cur_one.data("id"))], function(i, v) {
                $(".items", sk).append('<div class="sk_line">' + trans[zcms.lang](i, v) + "</div>")
            });
            sk.show()
        }
        $("#modal_date").modal("show")
    }
    $(document).on("click", "#channels .one.active .showcalend", function() {
        showModalDateSel($(this).parents(".one:first"));
        return false
    });
    $("#channels .one").click(function(e) {
        var t = $(e.target);
        if (t.is(".img.info") || t.parents(".img.info").length) {
            $.getJSON("/" + zcms.lang + "/gettvinfo?json=true", {
                id: t.parents(".one:first").data("id")
            }, function(ret) {
                $("#modal .modal-title").html(ret.title);
                $("#modal .modal-body").html(ret.descr);
                $("#modal").modal("show")
            });
            return false
        }
        var one = $(this).hasClass("one") ? $(this) : $(this).parents(".one:first");
        showModalDateSel(one);
        return false
    });

    function modalError(title, body, target) {
        $("#modal .modal-title").html(title);
        $("#modal .modal-body").html(body);
        $("#modal").modal("show");
        if (target != undefined) {
            var targ = typeof target != "string" ? target : $(target);
            $("html, body").animate({
                scrollTop: targ.offset().top - 80
            }, 400)
        }
    }
    $("#msg_text").on("input change propertychange", recalcAll);
    $(".multidate").on("input change", recalcAll);
    var busy = false;
    $("#tv_submit").submit(function() {
        var form = $(this);
        if (busy) {
            return false
        }
        recalcAll();
        var can = true;
        var phone = $("#phone");
        if (phone.val() !== "") {
            if (!/0[0-9]{9,9}/u.test(phone.val())) {
                modalError(tvmessages.error.title, tvmessages.error.phone, "#phone");
                can = false;
                return false
            }
        }
        var cont = $("#channels");
        if (sym_count === 0) {
            modalError(tvmessages.error.title, tvmessages.error.text, "#msg_text");
            can = false;
            return false
        }
        var c = 0;
        $(".one.active .multidate", cont).filter(function() {
            if (this.value) {
                c++
            }
        });
        if (c === 0) {
            modalError(tvmessages.error.title, tvmessages.error.dates, "#channels");
            can = false;
            return false
        }
        if (!can) {
            return false
        }
        busy = true;
        var captcha = grecaptcha.getResponse();
        if (!captcha.length) {
            busy = false;
            modalError(tvmessages.error.title, tvmessages.error.robot, "#robotcheck")
        } else {
            $.post(form.attr("action"), form.serialize(), function(ret) {
                busy = false;
                if (!ret.error) {
                    document.location.href = ret.url
                } else {
                    modalError("ÐžÑˆÐ¸Ð±ÐºÐ°", ret.msg !== undefined ? ret.msg : tvmessages.error.other, ret.inp !== undefined ? ret.inp : undefined);
                    if (ret.cpt_reset != undefined) {
                        grecaptcha.reset()
                    }
                }
            }, "json")
        }
        return false
    });
    $("#modal_date").on("shown.bs.modal", function(e) {
        $.getJSON("/" + zcms.lang + "/getjsontvdates", {
            id: cur_one.data("id")
        }, renderCalendar)
    });
    $(document).on("click", "#calend_rows input", function(e) {
        var me = $(this);
        var days = calendarGetDaysByRow(me.parent());
        var set = me.prop("checked");
        $.each(days, function(e) {
            var day = $(this);
            var dat = new Date(parseInt(day.data("year")), parseInt(day.data("month")), parseInt(day.data("date")));
            if (set) {
                datepicker.selectDate(dat)
            } else {
                datepicker.removeDate(dat)
            }
        })
    });
    $(document).on("click", "#calendar .cal_sel_all", function() {
        var set = $(this).prop("checked");
        $("#calendar .datepicker--cell-day").each(function(e) {
            var day = $(this);
            if (!day.hasClass("-disabled-") && !day.hasClass("-other-month-") && day.data("year") != undefined) {
                var dat = new Date(parseInt(day.data("year")), parseInt(day.data("month")), parseInt(day.data("date")));
                if (set) {
                    datepicker.selectDate(dat)
                } else {
                    datepicker.removeDate(dat)
                }
            }
        })
    });
    $(document).on("click", "#ok_date", function() {
        var str = [];
        var inp_str = [];
        var inp = $(".multidate", cur_one);
        select_dates[cur_one.data("id")] = datepicker.selectedDates;
        $.each(datepicker.selectedDates, function(i, v) {
            str.push(v.getDate() + "/" + (v.getMonth() + 1) + "/" + v.getFullYear());
            inp_str.push(v.getFullYear() + "-" + (v.getMonth() + 1) + "-" + v.getDate())
        });
        inp.val(inp_str.join(","));
        $(".show_dates", cur_one).html(str.join(", "));
        $("#modal_date").modal("hide");
        datepicker.clear();
        recalcAll();
        return false
    });
    $(document).on("click", "#cancel_date", function() {
        $("#modal_date").modal("hide");
        datepicker.clear();
        return false
    })
});

function calendarGetDaysByRow(row_checkbox) {
    var from = row_checkbox.index() * 7;
    var to = from + 7;
    var ret = [];
    $("#calendar .datepicker--cell-day").slice(from, to).each(function(e) {
        var day = $(this);
        if (!day.hasClass("-disabled-") && !day.hasClass("-other-month-") && day.data("year") != undefined) {
            ret.push($(this))
        }
    });
    return ret
}

function calendarGetInputByDay(day) {
    return (day.index() + 1) / 7
}

function calendarCheckSelect() {
    $("#calend_rows .one_inp:last").hide();
    $("#calend_rows input").each(function() {
        var le = calendarGetDaysByRow($(this).parent()).length;
        if (!le) {
            $(this).prop("checked", false).attr("disabled", "disabled")
        } else {
            $(this).attr("disabled", false)
        }
    });
    if ($("#calendar .datepicker--cell-day").length >= 42) {
        $("#calend_rows .one_inp:last").show()
    }
}

function renderCalendar(ret) {
    $("#calend_rows").show();
    var inp = $(".multidate", cur_one);
    var dates = {
        begin: moment(ret.dates.begin_str),
        end: moment(ret.dates.end_str)
    };
    var conf = {
        inline: true,
        language: zcms.lang,
        multipleDates: true,
        minDate: dates.begin.toDate(),
        maxDate: dates.end.toDate(),
        onSelect: function(formattedDate, date, inst) {
            calendarCheckSelect()
        },
        onChangeMonth: function(month, year) {
            $("#calend_rows input").prop("checked", false);
            calendarCheckSelect()
        },
        onRenderCell: function(date, cellType) {
            if (cellType == "day") {
                var mom = moment(date);
                var check = mom.format("YYYY_MM_DD");
                return {
                    disabled: ret.dates.list[check] != undefined ? true : false
                }
            }
        }
    };
    datepicker = $("#calendar").datepicker(conf).data("datepicker");
    if (select_dates[cur_one.data("id")] !== undefined) {
        datepicker.selectDate(select_dates[cur_one.data("id")])
    }
    calendarCheckSelect()
}

function recalcAll() {
    var str = $("#msg_text").val().replace(/\s/gimu, "");
    sym_count = str.length;
    summ = 0;
    $("#sym_count").html(sym_count);
    $("#channels .one").each(function() {
        var one = $(this);
        var tv_id = one.data("id");
        if (select_dates[tv_id] !== undefined && select_dates[tv_id].length > 0 && sym_count > 0) {
            var c = select_dates[tv_id].length;
            var s = parseFloat(one.data("price")) * sym_count * c;
            if (tv_packet[tv_id] !== undefined) {
                $.each(tv_packet[tv_id], function(co, price) {
                    if (sym_count <= parseInt(co)) {
                        s = price * c;
                        return false
                    }
                })
            }
            var old = 0;
            if (skidka[parseInt(tv_id)] !== undefined) {
                var tmp = 0;
                $.each(skidka[parseInt(tv_id)], function(i, v) {
                    if (c >= parseInt(i)) {
                        tmp = s - s * parseInt(v) / 100
                    }
                });
                if (tmp > 0) {
                    old = s;
                    s = tmp
                }
            }
            $(".price_real", one).html(s.formatMoney(2, ".", "") + " " + valuta);
            if (old > 0) {
                $(".price_old", one).html(old.formatMoney(2, ".", "") + " " + valuta)
            } else {
                $(".price_old", one).html("")
            }
            summ += s;
            one.addClass("active")
        } else {
            $(".price_real", one).html("0.0 " + valuta);
            $(".price_old", one).html("");
            one.removeClass("active")
        }
    });
    $("#total").html(summ.formatMoney(2, ".", "") + " " + valuta)
}