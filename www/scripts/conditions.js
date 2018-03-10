$(function(){
    var $conditionsForm = $('#conditionsForm'),
        $assignConditionsForm = $('#assignConditionsForm');

    $('.add-condition-js').click(function(){
        var $newConditionBl = $('.new-condition').last(),
            $addBlock = $newConditionBl.clone();

        $newConditionBl.before($addBlock.show());
    });

    var deleteTr = function($this){
        $this.parents('tr').remove();
    };

    $conditionsForm.on('click', '.deleteNewCondition', function(){
        deleteTr($(this));
    });

    $('.deleteChangedCondition').click(function(){
        var deletedItem = '<input type="hidden" name="deletedConditions[]" value="' + $(this).data('id') + '">';
        $conditionsForm.append(deletedItem);
        deleteTr($(this));
    });

    $('.addConditionAssign').click(function(){
        var $conditionAssignBl = $('.conditionAssignBl').last(),
            $addBlock = $conditionAssignBl.clone();

        $conditionAssignBl.before($addBlock.show());
    });

    $conditionsForm.on('click', '.deleteNewCondition', function(){
        deleteTr($(this));
    });

    var deleteAssignBl = function($this){
        $this.parents('tr').remove();
    };

    $assignConditionsForm.on('click', '.deleteAssign', function(){
        deleteAssignBl($(this));
    });

    $('.deleteExistingAssign').click(function(){
        deleteAssignBl($(this));
        var deletedItem = '<input type="hidden" name="deleteAssign[]" value="' + $(this).data('id') + '">';
        $assignConditionsForm.append(deletedItem);
    });

    $('.nodesGrid').click(function(){
        var $this = $(this),
            $table = $this.parents('.table'),
            $tBody = $table.find('tbody');

        if ($this.hasClass('icon-minus')) {
            $this.removeClass('icon-minus').addClass('icon-plus');
            $tBody.hide();
        } else {
            $this.removeClass('icon-plus').addClass('icon-minus');
            $tBody.show();
        }
    });
});