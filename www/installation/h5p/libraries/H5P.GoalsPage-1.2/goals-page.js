var H5P = H5P || {};

/**
 * Goals Page module
 * @external {jQuery} $ H5P.jQuery
 */
H5P.GoalsPage = (function ($) {
  // CSS Classes:
  var MAIN_CONTAINER = 'h5p-goals-page';

  // Goal states
  var GOAL_USER_CREATED = 0;
  var GOAL_PREDEFINED = 1;

  /**
   * Initialize module.
   * @param {Object} params Behavior settings
   * @param {Number} id Content identification
   * @returns {Object} GoalsPage GoalsPage instance
   */
  function GoalsPage(params, id) {
    this.$ = $(this);
    this.id = id;

    // Set default behavior.
    this.params = $.extend({}, {
      title: 'Goals',
      description: '',
      defineGoalText: 'Create a new goal',
      definedGoalLabel: 'User defined goal',
      defineGoalPlaceholder: 'Write here...',
      goalsAddedText: 'Number of goals added:',
      finishGoalText: 'Finish',
      editGoalText: 'Edit',
      removeGoalText: 'Remove',
      helpTextLabel: 'Read more',
      helpText: 'Help text'
    }, params);
  }

  /**
   * Attach function called by H5P framework to insert H5P content into page.
   *
   * @param {jQuery} $container The container which will be appended to.
   */
  GoalsPage.prototype.attach = function ($container) {
    var self = this;
    this.$inner = $('<div>', {
      'class': MAIN_CONTAINER
    }).appendTo($container);

    self.goalList = [];
    self.goalId = 0;

    var goalsTemplate =
      '<div class="goals-header">' +
      ' <div role="button" tabindex="0" class="goals-help-text">{{{helpTextLabel}}}</div>' +
      ' <div class="goals-title">{{{title}}}</div>' +
      '</div>' +
      '<div class="goals-description">{{{description}}}</div>' +
      '<div class="goals-define"></div>' +
      '<div class="goals-counter"></div>' +
      '<div class="goals-view"></div>';

    /*global Mustache */
    self.$inner.append(Mustache.render(goalsTemplate, self.params));
    self.$goalsView = $('.goals-view', self.$inner);

    self.createHelpTextButton();
    self.createGoalsButtons();

    // Initialize resize functionality
    self.initResizeFunctionality();
  };

  /**
   * Initialize listener for resize functionality
   */
  GoalsPage.prototype.initResizeFunctionality = function () {
    var self = this;

    // Listen for resize event on window
    $(window).resize(function () {
      self.resize();
    });

    // Initialize responsive view when view is rendered
    setTimeout(function () {
      self.resize();
    }, 0);
  };

  /**
   * Creates buttons for creating user defined and predefined goals
   */
  GoalsPage.prototype.createGoalsButtons = function () {
    var self = this;
    var $goalButtonsContainer = $('.goals-define', self.$inner);

    // Create new goal on click
    H5P.JoubelUI.createSimpleRoundedButton(self.params.defineGoalText)
      .addClass('goals-create')
      .click(function () {
        var $newGoal = self.addGoal();
        var $goalInput = $('.created-goal', $newGoal);
        $goalInput.prop('contenteditable', true);
        $goalInput.focus();

        // Need to tell world I might need to resize
        $goalInput.on('blur keyup paste input', function () {
          self.trigger('resize');
        });

        self.trigger('resize');
      }).appendTo($goalButtonsContainer);
  };

  /**
   * Adds a new goal to the page
   * @param {Object} competenceAim Optional competence aim which the goal will constructed from
   * @return {jQuery} $newGoal New goal element
   */
  GoalsPage.prototype.addGoal = function (competenceAim) {
    var self = this;
    var goalText = self.params.defineGoalPlaceholder;
    var goalType = GOAL_USER_CREATED;
    var goalTypeDescription = self.params.definedGoalLabel;

    // Use predefined goal
    if (competenceAim !== undefined) {
      goalText = competenceAim.value;
      goalType = !isNaN(competenceAim.goalType) ? competenceAim.goalType : GOAL_PREDEFINED;
      goalTypeDescription = competenceAim.description;
    }

    var newGoal = new H5P.GoalsPage.GoalInstance(goalText, self.goalId, goalType, goalTypeDescription);
    self.goalList.push(newGoal);
    self.goalId += 1;

    // Create goal element and append it to view
    var $newGoal = self.createGoalElementFromGoalInstance(newGoal).prependTo(self.$goalsView);

    self.updateGoalsCounter();
    self.resize();

    return $newGoal;
  };

  /**
   * Creates goal element from goal instance
   * @param {H5P.GoalsPage.GoalInstance} newGoal Goal instance object to create element from
   * @return {jQuery} $newGoal Goal element
   */
  GoalsPage.prototype.createGoalElementFromGoalInstance = function (newGoal) {
    var $newGoal = this.createNewGoal(newGoal).appendTo(this.$goalsView);
    var $newGoalInput = $('.created-goal', $newGoal);
    $newGoal.removeClass()
      .addClass('created-goal-container')
      .addClass('goal-type-' + newGoal.getGoalInstanceType());

    // Make goal input editable on click
    $newGoalInput.click(function () {
      setTimeout(function () {
        $newGoalInput.prop('contenteditable', true);
        $newGoalInput.focus();
      }, 0);
    });

    // Set focus if new user defined goal
    if (!newGoal.goalText().length &&
        (newGoal.getGoalInstanceType() === GOAL_USER_CREATED)) {
      $newGoal.addClass('focused');
      // Set timeout to prevent input instantly losing focus
      setTimeout(function () {
        $newGoalInput.prop('contenteditable', true);
        $newGoalInput.focus();
      }, 0);
    }

    return $newGoal;
  };

  /**
   * Remove chosen goal from the page
   * @param {jQuery} $goalContainer
   */
  GoalsPage.prototype.removeGoal = function ($goalContainer) {
    var goalInstance = this.getGoalInstanceFromUniqueId($goalContainer.data('uniqueId'));

    if (this.goalList.indexOf(goalInstance) > -1) {
      this.goalList.splice(this.goalList.indexOf(goalInstance), 1);
    }
    $goalContainer.remove();
    this.updateGoalsCounter();
  };

  /**
   * Updates goal counter on page with amount of chosen goals.
   */
  GoalsPage.prototype.updateGoalsCounter = function () {
    var self = this;
    var $goalCounterContainer = $('.goals-counter', self.$inner);
    $goalCounterContainer.children().remove();
    if (self.goalList.length) {
      $('<span>', {
        'class': 'goals-counter-text',
        'html': self.params.goalsAddedText + ' ' + self.goalList.length
      }).appendTo($goalCounterContainer);
    }
  };

  /**
   * Returns the goal instance matching provided id
   * @param {Number} goalInstanceUniqueId Id matching unique id of target goal
   * @returns {H5P.GoalsPage.GoalInstance|Number} Returns matching goal instance or -1 if not found
   */
  GoalsPage.prototype.getGoalInstanceFromUniqueId = function (goalInstanceUniqueId) {
    var foundInstance = -1;
    this.goalList.forEach(function (goalInstance) {
      if (goalInstance.getUniqueId() === goalInstanceUniqueId) {
        foundInstance = goalInstance;
      }
    });

    return foundInstance;
  };

  /**
   * Get goal element from goal instance
   * @return {jQuery|Number} Return goal element or -1 if not found
   */
  GoalsPage.prototype.getGoalElementFromGoalInstance = function (goalInstance) {
    var $goalElement = -1;
    this.$goalsView.children().each(function () {
      if ($(this).data('uniqueId') === goalInstance.getUniqueId()) {
        $goalElement = $(this);
      }
    });

    return $goalElement;
  };

  /**
   * Create help text functionality for reading more about the task
   */
  GoalsPage.prototype.createHelpTextButton = function () {
    var self = this;

    if (this.params.helpText !== undefined && this.params.helpText.length) {

      // Create help button
      $('.goals-help-text', this.$inner).click(function () {
        var $helpTextDialog = new H5P.JoubelUI.createHelpTextDialog(self.params.title, self.params.helpText);
        $helpTextDialog.appendTo(self.$inner.parent().parent().parent());
      }).keydown(function (e) {
        var keyPressed = e.which;
        // 32 - space
        if (keyPressed === 32) {
          $(this).click();
          e.preventDefault();
        }
        $(this).focus();
      });

    } else {
      $('.goals-help-text', this.$inner).remove();
    }
  };

  /**
   * Create a new goal container
   * @param {H5P.GoalsPage.GoalInstance} goalInstance Goal instance object to create the goal from
   * @returns {jQuery} $goalContainer New goal element
   */
  GoalsPage.prototype.createNewGoal = function (goalInstance) {
    var self = this;

    // Goal container
    var $goalContainer = $('<div/>', {
      'class': 'created-goal-container'
    }).data('uniqueId', goalInstance.getUniqueId());

    var initialText = goalInstance.goalText();

    // Input paragraph area
    var $goalInputArea = $('<div>', {
      'class': 'created-goal',
      'spellcheck': 'false',
      'contenteditable': false,
      'text': initialText,
      'title': goalInstance.getGoalTypeDescription()
    }).appendTo($goalContainer);

    // Add buttons
    var $goalButtons = $('<div>', {
      'class': 'h5p-goals-buttons'
    });
    this.createRemoveGoalButton(this.params.removeGoalText, $goalContainer).appendTo($goalButtons);
    this.createEditGoalButton(this.params.editGoalText, $goalInputArea).appendTo($goalButtons);
    this.createFinishedGoalButton(this.params.finishGoalText, $goalContainer).appendTo($goalButtons);

    $goalButtons.appendTo($goalContainer);
    self.addCustomHoverEffects($goalContainer);

    return $goalContainer;
  };

  /**
   * Adds custom hover effects to goal container
   * @param {jQuery} $goalContainer Element that will get custom hover effects
   */
  GoalsPage.prototype.addCustomHoverEffects = function ($goalContainer) {
    var self = this;
    var $goalInputArea = $('.created-goal', $goalContainer);

    // Add custom footer tools when input area is focused
    $goalInputArea.focus(function () {
      //Remove placeholder
      if ($(this).text() === self.params.defineGoalPlaceholder) {
        $(this).text('');
      }

      setTimeout(function () {
        $goalContainer.addClass('focused');
      }, 150);
    }).focusout(function () {
      // Delay focus out function slightly in case goal is removed

      setTimeout(function () {
        $goalContainer.removeClass('focused');
        $goalInputArea.prop('contenteditable', false);
      }, 150);

      // Set standard text if textfield is empty
      if ($(this).text() === '') {
        $(this).text(self.params.defineGoalPlaceholder);
      }

      self.getGoalInstanceFromUniqueId($goalContainer.data('uniqueId'))
        .goalText($(this).text());
    });
  };

  /**
   * Creates a button for enabling editing the given goal
   * @param {String} text String to display on the button
   * @param {jQuery} $inputGoal Input area for goal
   * @returns {jQuery} $editGoalButton The button
   */
  GoalsPage.prototype.createEditGoalButton = function (text, $inputGoal) {
    var $editGoalButton = $('<div>', {
      'class': 'h5p-created-goal-edit h5p-goals-button',
      'role': 'button',
      'tabindex': 1,
      'title': text
    }).click(function () {
      //Make goal editable and set focus to it
      $inputGoal.prop('contenteditable', true);
      $inputGoal.focus();
    });

    $('<span>', {
      'text': text,
      'class': 'h5p-created-goal-edit-text'
    }).appendTo($editGoalButton);

    return $editGoalButton;
  };

  /**
   * Creates a button for enabling editing the given goal
   * @param {String} text String to display on the button
   * @param {jQuery} $goalContainer Goal container element
   * @returns {jQuery} $editGoalButton The button
   */
  GoalsPage.prototype.createFinishedGoalButton = function (text, $goalContainer) {
    var $finishedGoalButton = $('<div>', {
      'class': 'h5p-created-goal-done h5p-goals-button',
      'role': 'button',
      'tabindex': 1,
      'title': text
    }).click(function () {
      $('.created-goal', $goalContainer).prop('contenteditable', false);
    });

    $('<span>', {
      'text': text,
      'class': 'h5p-created-goal-done-text'
    }).appendTo($finishedGoalButton);

    return $finishedGoalButton;
  };

  /**
   * Creates a button for removing the given container
   * @param {String} text String to display on the button
   * @param {jQuery} $removeContainer Container that will be removed upon click
   * @returns {jQuery} $removeGoalButton The button
   */
  GoalsPage.prototype.createRemoveGoalButton = function (text, $removeContainer) {
    var self = this;
    var $removeGoalButton = $('<div>', {
      'class': 'h5p-created-goal-remove h5p-goals-button',
      'role': 'button',
      'tabindex': 1,
      'title': text
    }).click(function () {
      self.removeGoal($removeContainer);
    });

    $('<span>', {
      'text': text,
      'class': 'h5p-created-goal-remove-text'
    }).appendTo($removeGoalButton);

    return $removeGoalButton;
  };

  /**
   * Get page title
   * @returns {String} Page title
   */
  GoalsPage.prototype.getTitle = function () {
    return this.params.title;
  };

  /**
   * Get goal list
   * @returns {Array} Goal list
   */
  GoalsPage.prototype.getGoals = function () {
    return this.goalList;
  };

  /**
   * Responsive resize of goals view
   */
  GoalsPage.prototype.resize = function () {
    var staticNoFooterThreshold = 33;
    var staticNoLabelsThreshold = 20;
    var widthInEm = this.$goalsView.width() / parseInt(this.$inner.css('font-size'), 10);

    // Remove footer description
    if (widthInEm < staticNoFooterThreshold) {
      this.$goalsView.addClass('no-footer-description');
    } else {
      this.$goalsView.removeClass('no-footer-description');
    }

    // Remove button labels
    if (widthInEm < staticNoLabelsThreshold) {
      this.$goalsView.addClass('no-footer-labels');
    } else {
      this.$goalsView.removeClass('no-footer-labels');
    }
  };

  return GoalsPage;
}(H5P.jQuery));
