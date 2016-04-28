/** @namespace H5PEditor */
var H5PEditor = H5PEditor || {};

H5PEditor.QuestionSetTextualEditor = (function ($) {

  /**
   * Creates a text input widget for editing question sets
   *
   * @class
   * @param {List} list
   */
  function QuestionSetTextualEditor(list) {
    var self = this;
    var entity = list.getEntity();
    var recreation = false;
    var shouldWarn = false;

    /**
     * Instructions as to how this editor widget is used.
     * @public
     */
    self.helpText = t('helpText') + '<pre>' + t('example') + '</pre>';

    // Create list html
    var $input = $('<textarea/>', {
      rows: 20,
      css: {
        resize: 'none'
      },
      placeholder: t('example'),
      on: {
        change: function () {
          recreateList();
        }
      }
    });

    // Used to convert HTML to text and vice versa
    var $cleaner = $('<div/>');

    /**
     * Clever variant of trim that can trim undefined values.
     *
     * @private
     * @param {String} value
     * @returns {String} Trimmed string, empty string if value is undefined.
     */
    var trim = function (value) {
      if (value === undefined) {
        return '';
      }

      return value.trim().replace('¤', ':');
    };

    /**
     * Clears all items from the list, processes the text and add the items
     * from the text. This makes it possible to switch to another widget
     * without losing datas.
     *
     * @private
     */
    var recreateList = function () {
      // Get text input
      var textLines = $input.val().split(LB);
      textLines.push(''); // Add separator

      // Get current list (to re-use values)
      var oldQuestions = list.getValue();

      // Reset list
      list.removeAllItems();
      recreation = true;

      /* In the future it should be possobile to create group structures without
      appending them. Because then we could drop the recreation process, and
      just add back to the textarea like a validation. */

      // Go through text lines and add statements to list
      var question, corrects, numQuestions = 0;
      for (var i = 0; i < textLines.length; i++) {
        var textLine = textLines[i].trim();
        if (textLine === '') {
          // Question seperator
          if (question !== undefined) {
            // Add previous question to list
            list.addItem(question);
            question = undefined;
          }
          continue;
        }

        // Convert text to html
        textLine = $cleaner.text(textLine).html();

        var matches;
        if (question === undefined) {
          numQuestions++;

          // Find out if we should re-use values from an old question
          matches = textLine.match(/^(\d+)\.\s?(.+)$/);
          if (matches !== null && matches.length === 3) {
            // Get old question
            question = oldQuestions[matches[1] - 1];
            textLine = matches[2];
          }

          if (question === undefined) {
            // Create new question
            question = {
              library: 'H5P.MultiChoice 1.5',
              params: {}
            };
          }

          // Update question numbering in textarea
          textLines[i] = numQuestions + '. ' + textLine;

          if (question.library === 'H5P.MultiChoice 1.5') {
            // Update question text using first text line
            question.params.question = textLine;

            // Reset alternatives
            delete question.params.answers;
            corrects = 0;
          }
        }
        else {
          // Add line as answer

          // Split up answer line according to format
          var parts = textLine.replace(/\\:/g, '¤').split(':', 4);
          var correct = false;

          // Determine if this is a correct answer
          parts[0] = trim(parts[0]);
          if (parts[0].substr(0, 1) === '*') {
            correct = true;
            parts[0] = trim(parts[0].substr(1, parts[0].length));
          }

          if (parts[0] !== '') {
            if (question.params.answers === undefined) {
              // Create new set of answers
              question.params.answers = [];
            }

            // Create new answer and add to question
            question.params.answers.push({
              text: parts[0],
              correct: correct,
              chosenFeedback: trim(parts[2]),
              notChosenFeedback: trim(parts[3]),
              tip: trim(parts[1])
            });

            if (correct) {
              corrects++; // Count number of correct answers
            }
            if (question.params.behaviour === undefined) {
              question.params.behaviour = {singleAnswer: true};
            }
            if (corrects > 1) {
              question.params.behaviour.singleAnswer = false;
            }
          }
        }
      }

      $input.val(textLines.join(LB));
      recreation = false;
    };

    /**
     * Find the name of the given field.
     *
     * @private
     * @param {Object} field
     * @return {String}
     */
    var getName = function (field) {
     return (field.getName !== undefined ? field.getName() : field.field.name);
    };

    /**
     * Strips down value to make it text friendly
     *
     * @private
     * @param {(String|Boolean)} value To work with
     * @param {String} [prefix] Prepended to value
     * @param {String} [suffix] Appended to value
     */
    var strip = function (value, prefix, suffix) {
      if (!value) {
        return '';
      }

      value = value.replace(/(<[^>]*>|\r\n|\n|\r)/gm, '').trim();
      if (value !== '') {
        if (prefix) {
          // Add given prefix to value
          value = prefix + value;
        }
        if (suffix) {
          // Add given suffix to value
          value += suffix;
        }
      }

      return value;
    };

    /**
     * Get multi choice question in text friendly format.
     *
     * @private
     * @param {Object} item Field instance
     * @param {Number} id Used for labeling
     */
    var addMultiChoice = function (item, id) {
      var question = '';

      item.forEachChild(function (child) {
        switch (getName(child)) {
          case 'question':
            // Strip value to make it text friendly
            question = strip(child.validate(), (id + 1) + '. ', LB) + question;
            break;

          case 'answers':
            // Loop through list of answers
            child.forEachChild(function (listChild) {

              // Loop through group of answer properties
              var answer = '';
              var feedback = '';
              var tip = '';
              listChild.forEachChild(function (groupChild) {
                switch (getName(groupChild)) {
                  case 'text':
                    // Add to end
                    answer += strip(groupChild.validate()).replace(/:/g, '\\:');
                    break;

                  case 'correct':
                    if (groupChild.value) {
                      // Add to beginning
                      answer = '*' + answer; // Correct answer
                    }
                    break;

                  case 'chosenFeedback':
                    // Add to beginning
                    feedback = strip(groupChild.validate()).replace(/:/g, '\\:') + feedback;
                    break;

                  case 'notChosenFeedback':
                    // Add to end
                    feedback += strip(groupChild.validate().replace(/:/g, '\\:'), ':');
                    break;

                  case 'tip':
                    groupChild.forEachChild(function (tipChild) {
                      // Replace
                      tip = strip(tipChild.validate()).replace(/:/g, '\\:');
                    });
                    break;
                }
              });

              if (feedback !== '') {
                // Add feedback to tip
                tip += ':' + feedback;
              }
              if (tip !== '') {
                // Add tip to answer
                answer += ':' + tip;
              }
              if (answer !== '') {
                // Add answer to question
                question += answer + LB;
              }
            });
            break;
        }
      });

      return question;
    };

    /**
     * Add items to the text input.
     *
     * @public
     * @param {Object} item Field instance added
     * @param {Number} id Used for labeling
     */
    self.addItem = function (item, id) {
      if (recreation) {
        return;
      }

      var question;

      // Get question text formatting
      switch (item.currentLibrary)  {
        case 'H5P.MultiChoice 1.5':
          question = addMultiChoice(item, id);
          break;

        default:
          // Not multi choice question
          question = (id + 1) + '. ' + t('unknownQuestionType') + LB;
          break;

        case undefined:
      }

      if (!warned && item.currentLibrary !== undefined && !shouldWarn) {
        shouldWarn = true;
      }

      // Add question to text field
      if (question) {
        // Convert all escaped html to text
        $cleaner.html(question);
        question = $cleaner.text();

        // Append text
        var current = $input.val();
        if (current !== '') {
          current += LB;
        }
        $input.val(current + question);
      }
    };

    /**
     * Puts this widget at the end of the given container.
     *
     * @public
     * @param {jQuery} $container
     */
    self.appendTo = function ($container) {
      $input.appendTo($container);
      if (shouldWarn && !warned) {
        alert(t('warning'));
        warned = true;
      }
    };

    /**
     * Remove this widget from the editor DOM.
     *
     * @public
     */
    self.remove = function () {
      $input.remove();
    };
  }

  /**
   * Helps localize strings.
   *
   * @private
   * @param {String} identifier
   * @param {Object} [placeholders]
   * @returns {String}
   */
  var t = function (identifier, placeholders) {
    return H5PEditor.t('H5PEditor.QuestionSetTextualEditor', identifier, placeholders);
  };

  /**
   * Line break.
   *
   * @private
   * @constant {String}
   */
  var LB = '\n';

  /**
   * Warn user the first time he uses the editor.
   */
  var warned = false;

  return QuestionSetTextualEditor;
})(H5P.jQuery);


// Add translations
H5PEditor.language['H5PEditor.QuestionSetTextualEditor'] = {
  'libraryStrings': {
    'helpText': 'Use an empty line to separate each question. In multi choice the first line is the question and the next lines are the answer alternatives. The correct alternatives are prefixed with an asterisk(*), tips and feedback can also be added: *alternative:tip:feedback if chosen:feedback if not chosen. Example:',
    'example': 'What number is PI?\n*3.14\n9.82\n\nWhat is 4 * 0?\n1\n4\n*0',
    'warning': 'Warning! If you change the tasks in the textual editor all rich text formatting(incl. line breaks) will be removed.',
    'unknownQuestionType': 'Non-editable question'
  }
};
