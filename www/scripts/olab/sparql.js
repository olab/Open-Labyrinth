/**
 * Created by larjohns on 31/10/13.
 */
$(document).ready(function () {

        data_types = [];

        function bar(type, className, classOffset, count, total, extras){
            var extrasText = "";
            if(extras!=undefined && extras>0)
                 extrasText = " & "+ extras + " inline triples";
            if(classOffset==total)
                className = "Finished";
            else className = className + " (" + count + " triples"+extrasText+")...";
            $(".bar").css("width",100* classOffset/total + "%");
            $("#bar-text").html("Indexing "+ type+ ": " + className);
        }

        function request(type, classOffset, offset, limit){
            if(type==undefined){
                type = data_types.pop();
                if(type==undefined){
                    $("#bar-text").html("Indexing Finished");//success
                    $(".progress").toggleClass("active", false);
                    $("#rebuild").prop("disabled", false);
                    return;
                }
            }
            ///app_base gets outputted in the sparql indexing page
            $.getJSON( app_base + "sparql/api/cron", {action:type, classOffset:classOffset, offset:offset, limit:limit },
                function( data ) {

                    if(data.status=="pending"){
                        //update progess bar

                        request(type,classOffset, offset+limit,limit);
                        bar(type,data.class,classOffset,data.count,data.total, data.extras);
                    }
                    else if(data.status=="class"){
                        //update progress list
                        bar(type,data.class,classOffset,data.count,data.total, data.extras);
                        request(type,classOffset+1,0,limit);
                    }
                    else if(data.status=="finished"){
                                        //update progress list
                        bar(type,data.class,data.total,0,data.total, undefined);
                     request(undefined,0,0,limit);
                    }


            })
                .fail(function() {
                    $("#bar-text").html("Indexing Failed");//success
                    $(".progress").toggleClass("active", false);
                    $("#rebuild").prop("disabled", false);

                });

        }

        $("#rebuild").click(

            function(event, handler){

                $.getJSON( app_base + "sparql/api/cron",{action:"init", vocabs: $('#external1').prop('checked')}, function( data ){
                    $("#rebuild").prop("disabled", true);
                    data_types = data.types;
                    $(".progress").toggleClass("active", true);
                    $(".bar").css("width",0 + "%");
                    $("#bar-text").html("Preparing to index...");
                    request(undefined,0,0,100);
                })
                    .fail(function() {
                        $("#bar-text").html("Indexing Failed");//success
                        $(".progress").toggleClass("active", false);
                        $("#rebuild").prop("disabled", false);

                    });



            }

        );


}
);