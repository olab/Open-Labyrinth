$(document).ready(function () {

    $(".single.tree").jstree({
        "checkbox" : {"two_state" : true},
        "plugins" : [ "themes", "html_data","checkbox", "sort", "ui"  ],
        "core" : {"html_titles" : true},
        "ui": {"select_limit":1}
    });
    $(".multi.tree").jstree({
            "checkbox" : {"two_state" : true},
            "plugins" : [ "themes", "html_data","checkbox", "sort", "ui"  ],
            "core" : {"html_titles" : true}
        });
    $(".tree").on("loaded.jstree", function (event) {

            var id = event.target.id;

            //alert(hash);
            var hiddens = $.find("."+id);
            //alert(hiddens.length);
            var tree = jQuery.jstree._reference ( event.target );
            for(var i=0; i<hiddens.length;i++){
                var hash = "RDF_" + CryptoJS.MD5(hiddens[i].value);


                tree.check_node ( "#"+hash );
                //alert(value);

            }

        });


    $(".single").on("change_state.jstree", function(event,data){
        var tree = jQuery.jstree._reference (event.target);

        var node = data.rslt;
        var checked = tree.get_checked ( null, false );

        if(checked.length<1){
            tree.check_node(node);
        }

        if(checked.length>1){
            if(tree.is_checked(node)){


                for(var i =0 ; i<checked.length; i++){
                    if(checked[i].id!=node.attr("id"))
                        tree.uncheck_node(checked[i]);
                }

            }
        }



    });


    $("form").submit(function(event) {

        var trees = $.find(".tree");


        for(var i=0; i<trees.length;i++){
            var tree = jQuery.jstree._reference ( trees[i] );
            var treeName = trees[i].id;
            var checked = tree.get_checked ( null, false );
            var hiddens = $.find("."+treeName);



            for(var k=0; k<hiddens.length; k++){
                $(hiddens[k]).remove();

            }



            for(var j=0; j<checked.length;j++){

                var valueContainer = $.find("."+checked[j].id);
                var value = $(valueContainer).val();
                var inp=document.createElement('input');
                inp.value = value;
                inp.type = 'hidden';

                var suffix = "";
                if($(trees[i]).hasClass("multi"))
                    suffix = "[]";


                inp.name = treeName + suffix;

                $(event.target).append(inp);


            }


        }




        return true;
    });
});
