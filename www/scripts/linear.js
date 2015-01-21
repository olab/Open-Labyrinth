$(function() {
    var $nodeContainer = $('#nodesContainer');
    var currentCount = 0;
    $('#applyCount').click(function() {
        var value = $('#nodeCountContainer').children().filter('.active').attr('value');
        var count = 0;
        if(value == 'Custom') {
            value = $('#nodeCount').val();
        }
        
        count = parseInt(value);
        if(isNaN(count)) count = 0;
        
        $('#nodeCount').val(count);
        
        if(count > 0) {
            var add = 0;
            var remove = 0;
            
            if(currentCount < count) {
                add = count - currentCount;
            } else {
                remove = currentCount - count;
            }

            for(var i = 1, j = currentCount + 1; i <= add; i++, j++) {
                var item = '<div id="nodeConatiner' + j + '"><legend>Node #' + j + '</legend><div class="control-group"><label for="nodeTitle' + j + '" class="control-label">Title</label><div class="controls"><input type="text" value="new node" id="nodeTitle' + j + '" name="nodeTitle' + j + '"/></div></div><div class="control-group"><label for="nodeContent' + j + '" class="control-label">Content</label><div class="controls"><textarea id="nodeContent' + j + '" class="mceEditor" name="nodeContent' + j + '"></textarea></div></div></div>';
                $nodeContainer.append(item);
                
                tinyMCE.execCommand("mceAddEditor", false, "nodeContent" + j);
            }
            
            for(var i = 0, j = currentCount; i < remove; i++, j--) {
                tinyMCE.execCommand("mceRemoveEditor", false, "nodeContent" + j);
                $nodeContainer.children().last().remove();
            }
            
            currentCount = count;
        } else {
            $nodeContainer.children().remove();
        }
       
        return false;
    });
    
    $('#step3_submit').click(function() {
       var count = parseInt($('#nodeCount').val());
       if(isNaN(count)) count = 0;
       $('#formNodeCount').val(count);
    });
});