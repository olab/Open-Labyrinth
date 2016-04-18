var H5P = H5P || {};
H5P.GoalsPage = H5P.GoalsPage || {};

/**
 * Goal Instance module
 */
H5P.GoalsPage.GoalInstance = (function () {

  var GOAL_PREDEFINED_SPECIFICATION = 2;

  /**
   * Initialize module.
   * @param {String} defineGoalPlaceholder Placeholder for Goal Instance
   * @param {Number} uniqueId Unique identifier for Goal Instance.
   * @param {Number} goalInstanceType Type of goal instance, 0 = user defined, 1 = pre defined, 2 = specification
   * @param {String} goalTypeDescription String describing the goal type, that will be displayed in its' footer
   * @returns {Object} GoalInstance GoalInstance instance
   */
  function GoalInstance(defineGoalPlaceholder, uniqueId, goalInstanceType, goalTypeDescription) {
    this.uniqueId = uniqueId;
    this.answer = -1;
    this.textualAnswer = '';
    this.text = defineGoalPlaceholder;
    this.goalInstanceType = goalInstanceType;
    this.specificationChildren = [];
    this.goalTypeDescription = goalTypeDescription;
  }

  /**
   * Get goal type description
   * @returns {String} String representation of the goal type
   */
  GoalInstance.prototype.getGoalTypeDescription = function () {
    return this.goalTypeDescription;
  };

  /**
   * Get goal instance type
   * @returns {Number} goalInstanceType 0 = user defined, 1 = predefined, 2 = edited predefined
   */
  GoalInstance.prototype.getGoalInstanceType = function () {
    return this.goalInstanceType;
  };

  /**
   * Get goal id
   * @returns {Number} uniqueId A unique identifier for the goal
   */
  GoalInstance.prototype.getUniqueId = function () {
    return this.uniqueId;
  };

  /**
   * Set or get goal answer/assessment depending on provided parameter
   * @param {Number} answer If defined the goal will be set to this value.
   * @returns {*} Returns answer with no parameters, and return this when setting parameter for chaining
   */
  GoalInstance.prototype.goalAnswer = function (answer) {
    // Get answer value if no arguments
    if (answer === undefined) {
      return this.answer;
    }

    // Set answer value
    this.answer = answer;
    return this;
  };

  /**
   * Get or set goal text depending on provided parameter
   * @param {String} text If defined this will be the new goal text for the goal
   * @returns {*} Returns text with no parameters, and return this when setting parameter for chaining
   */
  GoalInstance.prototype.goalText = function (text) {
    // Get text value if no arguments
    if (text === undefined) {
      return this.text;
    }

    // Set text value
    this.text = text;
    return this;
  };

  /**
   * Adds parent for this goal, used by specifications to set their parent
   */
  GoalInstance.prototype.addParent = function (goalInstance) {
    this.parent = goalInstance;
  };

  /**
   * Gets parent of this specification goal
   * @returns {Object} Goal instance of parent
   */
  GoalInstance.prototype.getParent = function () {
    return this.parent;
  };

  /**
   * Add a specification to this goal
   * @param {string} text Text for a specification of this goal
   * @param {number} goalId Goal id
   * @param {string} goalTypeDescription Goal type description
   * @return {GoalInstance} goalSpecification Goal instance
   */
  GoalInstance.prototype.addSpecification = function (text, goalId, goalTypeDescription) {
    // Create a new goal specification instance and add it to goal specification array
    var goalSpecification = new GoalInstance(text, goalId, GOAL_PREDEFINED_SPECIFICATION, goalTypeDescription);
    goalSpecification.addParent(this);
    this.specificationChildren.push(goalSpecification);

    return goalSpecification;
  };

  /**
   * Remove a specification of this goal
   * @param {Object} goalInstance Goal instance of specification goal that will be removed
   */
  GoalInstance.prototype.removeSpecification = function (goalInstance) {
    if (this.specificationChildren.indexOf(goalInstance) > -1) {
      this.specificationChildren.splice(this.specificationChildren.indexOf(goalInstance), 1);
    }
  };

  /**
   * Get specifications for this goal instance
   * @returns {Array} Array of specifications of this goal instance
   */
  GoalInstance.prototype.getSpecifications = function () {
    return this.specificationChildren;
  };

  /**
   * Set textual answer in goal instance
   * @param {String} textualAnswer Textual answer
   */
  GoalInstance.prototype.setTextualAnswer = function (textualAnswer) {
    this.textualAnswer = textualAnswer;
  };

  /**
   * Get textual answer from goal instance
   * @returns {string} textualAnswer Textual answer
   */
  GoalInstance.prototype.getTextualAnswer = function () {
    return this.textualAnswer;
  };

  return GoalInstance;
}());
