$(function(){
        $(".tabs").tabs();

        $("#showUser").dialog({
            autoOpen : false,
            buttons : {
                "Zamknij" : function() {
                    $("#showUser").dialog("close");
                }
            },
            modal: true,
            title: "Informacje o użytkowniku",
            width: 500,
            height: 350
        });

        $("#changeFoto").dialog({
            autoOpen : false,
            buttons : {
                "Poniechaj" : function() {
                    $("#changeFoto").dialog("close");
                }
            },
            modal: true,
            title: "Ładuj nowe zdjęcie",
            width: 500,
            height: 350,
            close : function() {
                if(imagePath != undefined)
                    $("#editFotoSubForm img").attr("src",imagePath);
            }
        });
        $("#addTraining").dialog({
            autoOpen : false,
            buttons : {
                "OK" : function() {
                    $("#addTraining").dialog("close");
                }
            },
            modal: true,
            title: "Nowy trening",
            width: 600,
            height: 800,
            close : function() {},
            open : function() {},
            buttons : {
                "Zapisz" : function() {
                    $.post("/index/run",{
                        "format": "json",
                        "method" : "addTraining",
                        "date" : $("input[name=date]","#addTraining").val(),
                        "users" : templateTraining.users
                    },function(data){
                        if(data.status == "error")
                        {
                            alert("Błąd!\n"+data.msg);
                        } else {
                            $("#addTraining").dialog("close");
                            location.href = location.href;
                        }
                    },"json");
                },
                "Poniechaj" : function() {
                    $(this).dialog("close");
                }

            }
        });
        $("#addTrainingButton").live("click",function(e){
            $("#addTraining").dialog("open");
            e.preventDefault();
        });
        $("#changeFotoLink").live("click",function(e){
            $("#changeFoto").dialog("open");
            e.preventDefault();
        });

        $("#editDataSubform input").live("change",function() {
            var value = $(this).val();
            var name = $(this).attr("name");

            $.post("/index/run",{
                "format": "json",
                "method" : "updateInput",
                "name" : name,
                "value" : value
            },function(data){
                if(data.status == "error")
                {
                    alert("Błąd!\n"+data.msg);
                }
            },"json");
        });

        $("#filtering select").live("change",function(e){
            $("#filtering form").submit();
            e.preventDefault();
        });
        $(".state span","#addTraining").live("click",function(e){
            $(this).toggleClass("hidden");
            $(this).next().toggleClass("hidden");
            $(this).prev().toggleClass("hidden");
            e.preventDefault();
        });

        $(".state span.ui-icon-circle-check","#addTraining").live("click",function(e){
            var userId = ""+$(this).parent().parent().attr("data");
            templateTraining.users[userId] = 0;
        });

        $(".state span.ui-icon-circle-close","#addTraining").live("click",function(e){
            var userId = ""+$(this).parent().parent().attr("data");
            templateTraining.users[userId] = 1;
        });

        $("#obecnosci-table div.name").live("click",function(){
            $("img","#showUser").attr("src", "/images/foto.gif");
            $(".name, .birth, .address, .contact","#showUser").text("-");

            var userId = $(this).parent().attr("user");

            $.post("/index/run",{
                "format": "json",
                "method" : "getUser",
                "id" : userId
            },function(data){
                if(data.status == "error")
                {
                    alert("Błąd!\n"+data.msg);
                } else {

                    if(data.image != null)
                        $("img","#showUser").attr("src", "/images/users/" + data.image);

                    $("div.name","#showUser").text(data.name + " " + data.surname);
                    $("div.birth","#showUser").text(data.birth);
                    $("div.address","#showUser").text(data.address);
                    $("div.contact","#showUser").html(data.email + "<br />" + data.phone + "<br />" + data.im);
                }
            },"json");
            $("#showUser").dialog("open");
        });

    });