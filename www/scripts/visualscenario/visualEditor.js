var VisualEditor = function() {
    var urlBase          = window.location.origin + '/',
        self             = this,
        body             = $('body'),
        stepPanel        = $('#veStepPanel'),
        viewport         = new Transform(),
        canvasOffsetLeft = 0,
        canvasOffsetTop  = 0;

    self.$canvasContainer = null;
    self.$canvas = null;
    self.canvas = null;
    self.context = null;
    self.mouse = new Mouse();
    self.lastMouse = new Mouse();
    self.elements = [];
    self.steps = {};
    self.zoomInFactor = 1.2;
    self.zommOutFactor = 0.8;
    self.isViewportInit = true;
    self.selectorTool = new Selector();
    self.isSelectActive = false;
    self.zoomIn = null;
    self.zoomOut = null;
    self.turnOnPanMode = null;
    self.turnOnSelectMode = null;
    self.$aButtonsContainer  = $('#ve_additionalActionButton');

    // Initialize visual editor
    self.Init = function(params) {
        if('canvasContainer' in params) {
            self.$canvasContainer = $(params.canvasContainer);
            if(self.$canvasContainer != null) {
                self.$canvasContainer.on("contextmenu", function (){ return false; });
                stepPanel.on("contextmenu", function (){ return false; });

                $(window).resize(function() {
                    self.Resize();
                });
            }
        }
        
        if('canvasId' in params) {
            self.$canvas = $(params.canvasId);
            
            if(self.$canvas != null) {
                self.canvas = self.$canvas[0];
                canvasOffsetLeft = self.canvas.offsetLeft;
                canvasOffsetTop = self.canvas.offsetTop;
            }
        }

        if('stepSelect' in params) {
            self.$sectionSelect = $(params.stepSelect);
        }

        if('aButtonsContainer' in params)
        {
            self.$aButtonsContainer = $(params.aButtonsContainer);
            $('#deleteSelected').click(function(){
                var selected = self.getSelectedElements();
                $.each(selected, function(num, selected){
                    $.each(self.elements, function(id, exist){
                        if (exist && selected.id == exist.id && selected.type == exist.type) self.elements.splice(id,1);
                    });
                });
                stepPanel.addClass('hide');
                self.Render();
            });

        }
        
        CreateContext();
        CreateEvents();

        self.Resize(null);
        self.ZoomOut();
        self.ZoomOut();
    };
    
    // Render current state of visual editor
    self.Render = function()
    {
        ClearContext();

        if (self.elements.length > 0)
        {
            for (var j = 0; j < self.elements.length; j++) self.elements[j].Draw(self.context, viewport);
        }

        self.selectorTool.Draw(self.context);

        if(self.$aButtonsContainer != null)
        {
            if(self.getSelectedElements().length) self.$aButtonsContainer.show();
            else self.$aButtonsContainer.hide();
        }
                    
    };

    self.Resize = function() {
        if(self.$canvasContainer != null && self.$canvas != null) {
            var h = window.innerHeight,
                w = window.innerWidth;

            if ( ! $("#fullScreen").hasClass('active')){
                self.$canvas.attr('width', self.$canvasContainer.width());
                h = parseInt(h) - 350;
                if (h < 400) h = 400;
                $(self.$canvasContainer).height(h);
                self.$canvas.attr('height', self.$canvasContainer.height());
            } else {
                $(self.$canvasContainer).height(h);
                if (h > 545) h = 545;
                $('#tab-content-scrollable').css('height', (h - 115) + 'px');
                $(self.$canvasContainer).width(w);
                self.$canvas.attr('height', self.$canvasContainer.height());
                self.$canvas.attr('width', self.$canvasContainer.width());
            }
            self.Render();
        }
    };

    var ClearContext = function() {
        self.context.save();
        self.context.setTransform(1, 0, 0, 1, 0, 0);
        self.context.clearRect(0, 0, self.canvas.width, self.canvas.height);
        self.context.restore();
    };

    var CreateContext = function() {
        if (self.canvas == null) return;
        self.context = self.canvas.getContext('2d');
    };

    self.ZoomIn = function() {
        var scale     = viewport.GetScale(),
            testScale = ((scale[0] + scale[1]) * 0.5) * self.zommOutFactor,
            oldSize   = [self.canvas.width / scale[0], self.canvas.height / scale[1]],
            newSize   = [0, 0],
            newScale  = [1, 1];

        if (testScale <= 1.6){
            viewport.Scale(self.zoomInFactor, self.zoomInFactor);
            newScale = viewport.GetScale();

            newSize = [self.canvas.width / newScale[0], self.canvas.height / newScale[1]];

            viewport.TranslateWithoutScale(-(oldSize[0] - newSize[0]) * 0.5, -(oldSize[1] - newSize[1]) * 0.5);
        }
    };

    self.ZoomOut = function() {
        var scale = viewport.GetScale(),
            testScale = ((scale[0] + scale[1]) * 0.5) / self.zommOutFactor,
            oldSize = [self.canvas.width / scale[0], self.canvas.height / scale[1]],
            newSize = [0, 0],
            newScale = [1, 1];

        if(testScale >= 0.1) {
            viewport.Scale(self.zommOutFactor, self.zommOutFactor);
            newScale = viewport.GetScale();

            newSize = [self.canvas.width / newScale[0], self.canvas.height / newScale[1]];
            viewport.TranslateWithoutScale((newSize[0] - oldSize[0]) * 0.5, (newSize[1] - oldSize[1]) * 0.5);
        }
    };

    // ----- data from php ----- //
    self.deserialize = function(jsonString) {

        var data     = $.parseJSON(jsonString),
            elements = data.elements,
            stepNum  = 0;

        self.elements = [];

        if (typeof data.steps == 'undefined') return;

        $.each(data.steps, function(stepId, name){
            stepNum++;
            var elementInStep = 0;

            saveStepInVisual(stepId, name);

            $.each(elements[stepId], function(index, elementData){
                var element = new Element(),
                    x       = 350*stepNum,
                    y       = 150*elementInStep;

                element.id          = elementData.id;
                element.type        = elementData.type;
                element.headerColor = elementData.type == 'labyrinth' ? '#4c8bff' : '#5ca028';
                element.title       = elementData.name;
                element.stepId      = stepId;
                element.stepName    = name;
                element.stepColor   = self.steps[stepId].color;

                element.transform.Translate(x, y);

                self.elements.push(element);

                elementInStep++;
            });
        });
    };

    // Serialize nodes info
    self.serialize = function() {
        var result   = {},
            elements = {},
            steps    = {};

        $.each(self.steps, function(idStep, stepData){
            steps[idStep] = stepData.name;
            elements[idStep] = {};
            elements[idStep]['labyrinth'] = {};
            elements[idStep]['section'] = {};

            $.each(self.elements, function(index, elementData){
                if (idStep == elementData.stepId) elements[idStep][elementData.type][index] = elementData.id;
            });
        });
        result['steps'] = steps;
        result['elements'] = elements;

        return JSON.stringify(result);
    };
    // ----- end data from php ----- //

    // ----- new element ----- //
    function saveStepInVisual (stepId, name) {
        self.steps[stepId] = {
            name: name,
            color: getRandomColor()
        };
    }

    function getRandomColor () {
        var color = 'rgba(';

        function getRandomInt(min, max){
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        for(var i = 0; i < 3; i++){
            color += getRandomInt(10, 150) + ', ';
        }

        color += '1.0)';
        return color;
    }

    self.addNewElement = function(type){
        var element = new Element();

        if (type == 'labyrinth' && selectedMapId){
            element.id          = selectedMapId;
            element.title       = selectedMapName;
            element.type        = type;
            element.headerColor = '#4c8bff';
        } else if (type == 'section' && selectedSectionId){
            element.id          = selectedSectionId;
            element.title       = selectedSectionName;
            element.type        = type;
            element.headerColor = '#5ca028';
        }
        else return false;

        element.isNew = true;
        
        if(self.$canvas != null) {
            var pos = viewport.GetPosition();

            var w = 100 - pos[0] + Math.random() * (70 - 40) + 40;
            var h = 150 - pos[1] + Math.random() * (70 - 40) + 40;

            element.transform.Translate(w, h);
        }
        
        self.elements.push(element);
        return true;
    };
    // ----- end new element ----- //

    // ----- mouse ----- //

    var CreateEvents = function() {
        if (self.canvas == null) return;

        self.canvas.addEventListener("mousedown", MouseDown, false);
        self.canvas.addEventListener("mouseup", MouseUp, false);
        self.canvas.addEventListener("mousemove", MouseMove, false);
        self.canvas.addEventListener("mouseout", MouseOut, false);
        self.canvas.addEventListener("touchstart", MouseDown, false);
        self.canvas.addEventListener("touchmove", MouseMove, false);
        self.canvas.addEventListener("touchend", MouseUp, false);
    };
    
    var MouseOut = function() {
        body.css('cursor', 'default');
        body.removeClass('clearCursor');
    };

    var UpdateMousePosition = function(event) {
        self.mouse.oldX = self.mouse.x;
        self.mouse.oldY = self.mouse.y;
        
        if(event.offsetX) {
            self.mouse.x = event.offsetX;
            self.mouse.y = event.offsetY;
        } else if(event.layerX) {
            self.mouse.x = event.layerX - canvasOffsetLeft;
            self.mouse.y = event.layerY - canvasOffsetTop;
        } else {
            self.mouse.x = event.pageX - canvasOffsetLeft;
            self.mouse.y = event.pageY - canvasOffsetTop;
        }
        
        if(isNaN(self.mouse.x))
            self.mouse.x = 0;
        
        if(isNaN(self.mouse.y))
            self.mouse.y = 0;
    };

    var MouseDown = function(event) {
        var turnOnPan = true;

        self.mouse.isDown = true;

        UpdateMousePosition(event);

        if (event.button == 2) self.turnOnSelectMode();
        else {
            turnOnPan = true;
            self.turnOnPanMode();
        }

        var isRedraw = false;

        if( ! isRedraw && self.selectorTool != null && self.isSelectActive || turnOnPan) {
            for(var i = self.elements.length - 1; i >= 0; i--) {
                self.elements[i].isSelected = false;
            }
            self.selectorTool.MouseDown(self.mouse);
            isRedraw = true;
        }

        if(isRedraw) self.Render();
    };
    
    var MouseUp = function(event) {
        self.mouse.isDown = false;
        UpdateMousePosition(event);
        self.lastMouse.x = self.mouse.x;
        self.lastMouse.y = self.mouse.y;
        
        var isRedraw = false;

        if( ! isRedraw && self.selectorTool != null && self.isSelectActive) {
            var existSelect = false;
            if(self.elements.length > 0) {
                for(var i = self.elements.length - 1; i >= 0; i--) {
                    if(self.elements[i].IsNodeInRect(self.selectorTool.x, self.selectorTool.y, self.selectorTool.width, self.selectorTool.height, viewport) || self.elements[i].isSelected) {
                        self.elements[i].isSelected = true;
                        if(!existSelect) existSelect = true;
                    }
                }
            }

            self.selectorTool.MouseUp(self.mouse);
            if(self.$aButtonsContainer  != null) {
                if(existSelect) self.$aButtonsContainer.show();
                else self.$aButtonsContainer.hide();
            }
            
            isRedraw = true;
        }

        var selectedNodes = self.getSelectedElements();
        if (selectedNodes.length > 0) stepPanel.removeClass('hide');
        else stepPanel.addClass('hide');

        if (isRedraw) self.Render();
    };

    var MouseMove = function(event) {
        var isCursorSet = false;
        
        if(self.mouse.isDown) event.preventDefault();
        
        UpdateMousePosition(event);

        var isRedraw = false;
        
        event.stopPropagation();
        event.target.style.cursor = 'default';

        if(self.elements.length > 0 && !isRedraw && !(self.isSelectActive && self.selectorTool != null && self.selectorTool.isDragged)) {
            for(var i = self.elements.length - 1; i >= 0; i--) {
                if( ! isCursorSet && self.elements[i].IsMainAreaCollision(self.mouse.x, self.mouse.y, viewport)) {
                    event.target.style.cursor = 'default';
                    isCursorSet = true;
                } else if( ! isCursorSet && self.elements[i].IsHeaderCollision(self.mouse.x, self.mouse.y, viewport)) {
                    event.target.style.cursor = 'move';
                    isCursorSet = true;
                }
                
                if(self.elements[i].MouseMove(self.mouse, viewport, self.elements)) {
                    if( ! isCursorSet && self.elements[i].IsHeaderCollision(self.mouse.x, self.mouse.y, viewport)) {
                        event.target.style.cursor = 'move';
                        isCursorSet = true;
                    }
                    isRedraw = true;
                }
            }
        }

        if ( ! isCursorSet && ! self.isSelectActive) {
            event.target.style.cursor = 'move';
            isCursorSet = true;
        } else if(!isCursorSet) {
            event.target.style.cursor = 'crosshair';
            isCursorSet = true;
        }
        
        if (isCursorSet) {
            body.addClass('clearCursor');
        } else {
            body.removeClass('clearCursor');
        }
        
        if ( ! isRedraw && self.mouse.isDown && ! self.isSelectActive) {
            var scale = viewport.GetScale();
            
            var tx = (self.mouse.x - self.mouse.oldX) / scale[0];
            var ty = (self.mouse.y - self.mouse.oldY) / scale[1];
            
            viewport.TranslateWithoutScale(tx, ty);
            isRedraw = true;
        } else if( ! isRedraw && self.mouse.isDown && self.isSelectActive) {
            if(self.selectorTool.MouseMove(self.mouse)) isRedraw = true;
        }

        if (isRedraw) self.Render();
    };

    self.getSelectedElements = function() {
        var result = [];

        if (self.elements.length < 1) return result;

        for (var i = self.elements.length; i--;) {
            if (self.elements[i].isSelected) {
                result.push(self.elements[i]);
            }
        }
        return result;
    };
    // ----- end mouse ----- //

    // ----- step ----- //

    self.addNewStep = function (stepId, newName) {
        saveStepInVisual(stepId, newName);
    };

    self.updateStep = function (stepId, newName) {
        // change visual step name of elements
        $.each(self.elements, function(index, obj){
            if (obj.stepId == stepId){
                obj.stepName = newName;
            }
        });
        // change step name
        self.steps[stepId].name = newName;
        self.Render();
    };

    self.deleteStep = function (stepId) {
        // delete visual step name of elements
        $.each(self.elements, function(index, obj){
            if (obj.stepId == stepId){
                obj.stepId    = 0;
                obj.stepName  = '';
                obj.stepColor = '';
            }
        });

        // delete step name
        delete self.steps[stepId];
        self.Render();
    };

    self.addToStep = function (stepId) {
        if( ! stepId) return;

        var selected = self.getSelectedElements();
        $.each(selected, function(index, obj){
            obj.stepId    = stepId;
            obj.stepName  = self.steps[stepId].name;
            obj.stepColor = self.steps[stepId].color;
        });
        self.Render();
    };
    // ----- end step ----- //

    // ----- add map or section ----- //
    var selectedMapId       = 0,
        selectedSectionId   = 0,
        selectedMapName     = '',
        selectedSectionName = '';

    $('.visual-map-js').change(function(){
        var mapSelect = $(this);

        selectedMapId       = $(this).val();
        selectedSectionId   = 0;
        selectedMapName     = $(this).find(":selected").text();
        selectedSectionName = '';

        $.get(
            urlBase + 'webinarManager/getSectionAJAX/' + selectedMapId,
            function(data){
                if(data.length > 2){
                    var sectionSelect ='<select class="visual-section-js" size="6">';
                    $.each($.parseJSON(data), function(sectionName, sectionId){
                        sectionSelect +='<option value="'+ sectionId +'">' + sectionName + '</option>';
                    });
                    sectionSelect += '</select>';
                    mapSelect.next().remove();
                    mapSelect.after(sectionSelect);
                } else mapSelect.next().remove();
            }
        )
    });

    $('#canvasContainer').on('change', '.visual-section-js', function(){
        selectedSectionId   = $(this).val();
        selectedSectionName = $(this).find(":selected").text();
    });
    // ----- end map or section ----- //
};