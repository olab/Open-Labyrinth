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
        console.log($this.parent());
        $this.parent().remove();
    };

    $('.deleteAssign').click(function(){
        deleteAssignBl($(this));
    });
});