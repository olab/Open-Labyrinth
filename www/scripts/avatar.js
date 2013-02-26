var globalDir = "/images/avatar/";

var outlineLayerData;
var fillAreaBody = {data: {}};
var imageLoaded = 1;
var params = [];
var stream;

AvatarSetup = function(setParams){
    params = setParams;
    AvatarRender();
}

AvatarRender = function() {
    var canvasWidth = 300,
        canvasHeight = 300,
        drawingAreaX = -20,
        drawingAreaY = 30;

    var curColor = {r: 170, g: 132, b: 88};
    var colorBody = {r: 170, g: 132, b: 88};
    var outlineImageObj = new OutlineImageRepos();
    var avatar = new Avatar();
    var startBg = {r: 255, g: 255, b: 255}
    var canvas = document.getElementById("avatar_canvas");
    canvas.width = canvasWidth;
    canvas.height = canvasHeight;
    var context = canvas.getContext("2d");
    avatarParamDef();

    function avatarParamDef(){
        avatar.addElement("main", 25, globalDir + "images/body/main_male.png", drawingAreaX, drawingAreaY, 'main');
        var nextParent = "main";

        stream = new ObjectStream();
        stream.addElement('none', 'main', false, null, null);

        param_default('sex', 'male');
        param_default('age', '20');
        param_default('mouth', 'smile');
        param_default('eyes', 'open');
        param_default('nose', 'nostrils');
        param_default('accessory1', 'none');
        param_default('accessory2', 'none');
        param_default('accessory3', 'none');
        param_default('skintone', 'AA8458');
        param_default('bubble', 'none');
        param_default('bubbletext', '');

        var addOutfit = false;
        if (!param_default('outfit', 'naked')){addOutfit = true;}
        param_default('clothcolor', 'FF0000');

        if (addOutfit){
            stream.addElement(nextParent, 'outfit', false, changeOutfit, [params['outfit'], params['clothcolor']]);
            nextParent = 'outfit';
        }

        var addHairtype = false;
        if (!param_default('hairtype', 'none')) {addHairtype = true;}
        param_default('haircolor', 'FFFFCC');

        if (addHairtype){
            stream.addElement(nextParent, 'hairtype', false, changeHair, [params['hairtype'], params['haircolor']]);
            nextParent = 'hairtype';
        }

        if (!param_default('environment', 'none')) {
            stream.addElement(nextParent, 'environment', false, changeEnvironment, [params['environment']]);
            nextParent = 'environment';
        }

        changeSex(params['sex']);
        changeAge(params['age']);
        changeMouth(params['mouth']);
        changeEyes(params['eyes']);
        changeNose(params['nose']);
        changeAccessory('acc1', params['accessory1'], 65);
        changeAccessory('acc2', params['accessory2'], 75);
        changeAccessory('acc3', params['accessory3'], 85);
        changeBubble(params['bubble'], params['bubbletext']);
        changeSkintone(params['skintone']);

        redrawMainBody('main');
        StartStream();
    }

    function ObjectStream(){
        this.objStream = {};
        this.addElement = function(parent, objName, status, callFunc, params){
            var el = {
                parent: parent,
                status: status,
                callFunc: callFunc,
                params: params
            };
            this.objStream[objName] = el;
        }
    }

    function StartStream(){
        for(key in stream.objStream){
            var element = stream.objStream[key];
            if (element.parent != 'none'){
                new StreamTimer(key, element);
            }
        }
    }

    function StreamTimer(key, element){
        var parent = element.parent;
        var callFunc = element.callFunc;
        var params = element.params;
        var interval = setInterval(function(){
            if (stream.objStream[parent].status){
                clearInterval(interval);
                if (params.length == 1){
                    callFunc(params[0]);
                }else{
                    callFunc(params[0], params[1]);
                }
            }
        }, 100);
    }

    function param_default(pname, def){
        if (typeof params[pname] == "undefined"){
            params[pname] = def;
            return true;
        }
        return false;
    }

    function clearCanvas() {
        context.clearRect(0, 0, context.canvas.width, context.canvas.height);
    }

    function redraw(zIndex, redraw_id) {
        var objFill = {id: redraw_id, drawX: 0, drawY: 0};
        outlineImageObj.addOutlineImage(zIndex, objFill, fillAreaBody, 'd');
    }

    function matchOutlineColor(r, g, b) {
        return (r + g + b > 100);
    }

    function matchStartColor(pixelPos, startR, startG, startB, curColor){
        var r = outlineLayerData.data[pixelPos],
            g = outlineLayerData.data[pixelPos + 1],
            b = outlineLayerData.data[pixelPos + 2],
            a = outlineLayerData.data[pixelPos + 3];

        var rN = fillAreaBody.data[pixelPos],
            gN = fillAreaBody.data[pixelPos + 1],
            bN = fillAreaBody.data[pixelPos + 2],
            aN = fillAreaBody.data[pixelPos + 3];

        if (rN === curColor.r && gN === curColor.g && bN === curColor.b) {
            return false;
        }

        if (matchOutlineColor(r, g, b)) {
            return true;
        }

        if (a === 255){
            return false;
        }

        if (r === startR && g === startG && b === startB) {
            return true;
        }else{
            return false;
        }
    }

    function floodFill(startX, startY, curColor, isRedraw, redraw_id, zIndex){
        var pixelStack = [[startX, startY]],
            x,
            y,
            newPos,
            pixelPos,
            startR = 0,
            startG = 0,
            startB = 0,
            reachLeft,
            reachRight,
            drawingBoundLeft = 0,
            drawingBoundTop = 0,
            drawingBoundRight = canvasWidth - 1,
            drawingBoundBottom = canvasHeight - 1;

        while (pixelStack.length){
            newPos = pixelStack.pop();
            x = newPos[0];
            y = newPos[1];

            pixelPos = (y * canvasWidth + x) * 4;

            while (y >= drawingBoundTop && matchStartColor(pixelPos, startR, startG, startB, curColor)) {
                y -= 1;
                pixelPos -= canvasWidth * 4;
            }

            pixelPos += canvasWidth * 4;
            y += 1;
            reachLeft = false;
            reachRight = false;

            while (y <= drawingBoundBottom && matchStartColor(pixelPos, startR, startG, startB, curColor)) {
                y += 1;

                colorPixel(pixelPos, curColor.r, curColor.g, curColor.b);

                if (x > drawingBoundLeft) {
                    if (matchStartColor(pixelPos - 4, startR, startG, startB, curColor)) {
                        if (!reachLeft) {
                            // Add pixel to stack
                            pixelStack.push([x - 1, y]);
                            reachLeft = true;
                        }
                    } else if (reachLeft) {
                        reachLeft = false;
                    }
                }

                if (x < drawingBoundRight) {
                    if (matchStartColor(pixelPos + 4, startR, startG, startB, curColor)) {
                        if (!reachRight) {
                            // Add pixel to stack
                            pixelStack.push([x + 1, y]);
                            reachRight = true;
                        }
                    } else if (reachRight) {
                        reachRight = false;
                    }
                }
                pixelPos += canvasWidth * 4;
            }
        }
        if (isRedraw){
            redraw(zIndex, redraw_id);
        }
    }

    function colorPixel(pixelPos, r, g, b, a) {
        fillAreaBody.data[pixelPos] = r;
        fillAreaBody.data[pixelPos + 1] = g;
        fillAreaBody.data[pixelPos + 2] = b;
        fillAreaBody.data[pixelPos + 3] = a !== undefined ? a : 255;
    }

    function redrawMainBody(objName){
        if(typeof(stream.objStream[objName]) != "undefined" && stream.objStream[objName] !== null) {
            stream.objStream[objName].status = false;
        }
        var obj = avatar.elMas['main'];
        var outlineImage = new Image();
        outlineImage.onload = function() {
            new RedrawCanvasLayer(objName, obj, outlineImage, colorBody);
        };
        outlineImage.src = obj.name;
    }

    function RedrawCanvasLayer(objName, obj, outlineImage, color){
        clearCanvas();
        imageLoaded = 1;
        fillAreaBody = context.getImageData(0, 0, canvasWidth, canvasHeight);

        context.drawImage(outlineImage, obj.drawX, obj.drawY, outlineImage.width, outlineImage.height);
        outlineLayerData = context.getImageData(0, 0, canvasWidth, canvasHeight);
        curColor.r = color.r;
        curColor.g = color.g;
        curColor.b = color.b;

        floodFill(190, 215, curColor, false, 'fill', 15);
        floodFill(150, 130, curColor, false, 'fill', 15);
        floodFill(93, 135, curColor, false, 'fill', 15);
        floodFill(216, 140, curColor, false, 'fill', 15);
        floodFill(213, 128, curColor, true, 'fill', 15);
        for(key in avatar.elMas){
            new DrawElement(objName, avatar.elMas[key], context);
        }
    }

    function hex2rgb(hex) {
        if (hex[0]=="#") hex=hex.substr(1);
        if (hex.length==3) {
            var temp=hex; hex='';
            temp = /^([a-f0-9])([a-f0-9])([a-f0-9])$/i.exec(temp).slice(1);
            for (var i=0;i<3;i++) hex+=temp[i]+temp[i];
        }
        var triplets = /^([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i.exec(hex).slice(1);
        return {
            r: parseInt(triplets[0],16),
            g: parseInt(triplets[1],16),
            b: parseInt(triplets[2],16)
        }
    }

    function Avatar(){
        this.elMas = [];
        this.addElement = function(id, zIndex, name, drawX, drawY, index){
            var el = {
                id: id,
                zIndex: zIndex,
                name:name,
                drawX:drawX,
                drawY:drawY
            };
            if (index == ''){
                this.elMas.push(el);
            }else{
                this.elMas[index] = el;
            }
        }
    }

    function DrawElement(objName, obj, context){
        var outlineImageDraw = new Image();
        outlineImageDraw.onload = function() {
            outlineImageObj.addOutlineImage(obj.zIndex, obj, outlineImageDraw, 'i');
            var count = countObj(avatar.elMas);
            if (imageLoaded == count){
                outlineImageObj.imageRepos = sortObject(outlineImageObj.imageRepos);
                for(key in outlineImageObj.imageRepos){
                    var img = outlineImageObj.imageRepos[key]['image'];
                    var object = outlineImageObj.imageRepos[key]['obj'];
                    if (outlineImageObj.imageRepos[key]['type'] == 'i'){
                        context.drawImage(img, object.drawX, object.drawY, img.width, img.height);
                    }

                    if (outlineImageObj.imageRepos[key]['type'] == 'd'){
                        var outputImageData = context.getImageData(0, 0, canvasWidth, canvasHeight);
                        colorContext = context.getImageData(0, 0, canvasWidth, canvasHeight);
                        for(color = 3; color < img.data.length; color += 4){
                            if (img.data[color] != 0){
                                outputImageData.data[color] = img.data[color];
                                outputImageData.data[color - 1] = img.data[color - 1];
                                outputImageData.data[color - 2] = img.data[color - 2];
                                outputImageData.data[color - 3] = img.data[color - 3];
                            }
                        }
                        context.putImageData(outputImageData, object.drawX, object.drawY);
                    }

                    if (outlineImageObj.imageRepos[key]['type'] == 't'){
                        context.font = "bold 12px sans-serif";
                        var strHeight = 0;
                        img = img.substr(0, object.maxlen);
                        var arrayText = transferWords(img, object.strlen);
                        for(t in arrayText){
                            context.fillText(arrayText[t], object.drawX, object.drawY + strHeight);
                            strHeight += 16;
                        }
                    }
                }
                if(typeof(stream.objStream[objName]) != "undefined" && stream.objStream[objName] !== null) {
                    stream.objStream[objName].status = true;
                }
            }
            imageLoaded++;
        };
        outlineImageDraw.src = obj.name;
    }

    function transferWords(text, strlen){
        var textArray = text.split(' ');
        var t;
        var i = 0;
        var array = [""];
        for (t in textArray){
            var len = textArray[t].length;
            var strLen = array[i].length;
            if ((strLen + len) <= strlen){
                array[i] += textArray[t]+" ";
            }else{
                if (len > strlen){
                    var strrem = textArray[t];
                    var substart = 0;
                    var subend = strlen;
                    while(strrem.length > strlen){
                        array[i] = strrem.substr(substart, subend);
                        strrem = strrem.substr(subend, len);
                        i++;
                    }
                    array[i] = strrem + " ";
                }else{
                    i++;
                    array[i] = textArray[t]+" ";
                }
            }
        }
        return array;
    }

    function sortObject(o) {
        var sorted = {},
            key, a = [];

        for (key in o) {
            if (o.hasOwnProperty(key)) {
                a.push(key);
            }
        }

        a.sort();

        for (key = 0; key < a.length; key++) {
            sorted[a[key]] = o[a[key]];
        }
        return sorted;
    }

    function countObj(array){
        var cnt=0;
        for (var i in array){
            if (i){cnt++;}
        }
        return cnt;
    }

    function OutlineImageRepos(){
        this.imageRepos = [];
        this.addOutlineImage = function(zIndex, obj, outlineImage, type){
            this.imageRepos[zIndex + obj['id']] = Array();
            this.imageRepos[zIndex + obj['id']]['obj'] = obj;
            this.imageRepos[zIndex + obj['id']]['image'] = outlineImage;
            this.imageRepos[zIndex + obj['id']]['type'] = type;
        }
    }

    document.getElementById("skintone").onchange = function() {
        var color = this.options[this.selectedIndex].value;
        changeSkintone(color);
        redrawMainBody('skintone');
    };

    function changeSkintone(color){
        try{
            colorBody = hex2rgb(color);
        }catch(e){
            colorBody = hex2rgb("AA8458");
        }
    }

    document.getElementById("sex").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeSex(value);
        var color_value = document.getElementById("bgcolor").value;
        if (color_value != ''){
            try{
                color_value = hex2rgb(color_value);
            }catch(e){
                color_value = hex2rgb("FFFFFF");
            }
            changeBgColor(color_value);
        }else{
            redrawMainBody('sex');
        }
    };

    function changeSex(value){
        deleteElement('main');
        deleteElement('sex');
        deleteElement('eyebrows');
        deleteElement('chin');
        if (value == 'male'){
            avatar.addElement("main", 25, globalDir + "images/body/main_male.png", drawingAreaX, drawingAreaY, 'main');
            avatar.addElement("sex", 25, globalDir + "images/body/sex_male.png", drawingAreaX + 87, drawingAreaY + 192, '');
            avatar.addElement("eyebrows", 25, globalDir + "images/body/eyebrows_male.png", drawingAreaX + 118, drawingAreaY + 66, '');
            avatar.addElement("chin", 25, globalDir + "images/body/chin.png", drawingAreaX + 166, drawingAreaY + 149, '');
        }else{
            avatar.addElement("main", 25, globalDir + "images/body/main_female.png", drawingAreaX, drawingAreaY, 'main');
            avatar.addElement("sex", 25, globalDir + "images/body/sex_female.png", drawingAreaX + 87, drawingAreaY + 255, '');
            avatar.addElement("eyebrows", 25, globalDir + "images/body/eyebrows_female.png", drawingAreaX + 118, drawingAreaY + 66, '');
        }
    }

    document.getElementById("age").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeAge(value);
        redrawMainBody('age');
    };

    function changeAge(value){
        deleteElement('age');
        if (value == '40'){
            avatar.addElement("age", 25, globalDir + "images/age/age_40.png", drawingAreaX + 115, drawingAreaY + 71, '');
        }

        if (value == '60'){
            avatar.addElement("age", 25, globalDir + "images/age/age_60.png", drawingAreaX + 115, drawingAreaY + 71, '');
        }
    }

    document.getElementById("mouth").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeMouth(value);
        redrawMainBody('mouth');
    };

    function changeMouth(value){
        deleteElement('mouth');
        if (value == 'smile'){
            avatar.addElement("mouth", 25, globalDir + "images/body/mouth_smile.png", drawingAreaX + 153, drawingAreaY + 136, '');
        }
        if (value == 'indifferent'){
            avatar.addElement("mouth", 25, globalDir + "images/body/mouth_indifferent.png", drawingAreaX + 154, drawingAreaY + 139, '');
        }
        if (value == 'frown'){
            avatar.addElement("mouth", 25, globalDir + "images/body/mouth_frown.png", drawingAreaX + 154, drawingAreaY + 141, '');
        }
    }

    document.getElementById("outfit").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        var clothColor = document.getElementById("clothcolor").value;
        changeOutfit(value, clothColor);
    };

    document.getElementById("clothcolor").onchange = function() {
        var value = document.getElementById("outfit").value;
        var clothColor = this.value;
        changeOutfit(value, clothColor);

    };

    function changeOutfit(value, clothColor){
        var fillCreated = false;
        deleteElement('outfit');
        deleteFillElement('35outfit');
        deleteFillElement('36outfit');
        deleteElement('outfit_fill');
        deleteElement('outfit_trsp');

        if (value == "woolyjumper"){
            fillCreated = true;
            createFillArea("outfit", 35, globalDir + "images/outfit/outfit_woolyjamper_form.png", drawingAreaX - 1, drawingAreaY + 170, 120, 270, clothColor);
            avatar.addElement("outfit_fill", 36, globalDir + "images/outfit/outfit_woolyjamper_fill.png", drawingAreaX + 0, drawingAreaY + 173, '');
            avatar.addElement("outfit_trsp", 36, globalDir + "images/outfit/outfit_woolyjamper_form_trsp.png", drawingAreaX - 1, drawingAreaY + 173, '');
        }

        if (value == "shirtandtie"){
            fillCreated = true;
            createFillArea("outfit", 36, globalDir + "images/outfit/outfit_shirtandtie_form.png", drawingAreaX + 155, drawingAreaY + 203, 160, 260, clothColor);
            avatar.addElement("outfit_fill", 35, globalDir + "images/outfit/outfit_shirtandtie_fill.png", drawingAreaX + 0, drawingAreaY + 170, '');
            avatar.addElement("outfit_trsp", 37, globalDir + "images/outfit/outfit_shirtandtie_form_trsp.png", drawingAreaX + 137, drawingAreaY + 158, '');
        }

        if (value == "nurse"){
            fillCreated = true;
            createFillArea("outfit", 36, globalDir + "images/outfit/outfit_nurse_form.png", drawingAreaX + 249, drawingAreaY + 245, 236, 283, clothColor);
            avatar.addElement("outfit_fill", 35, globalDir + "images/outfit/outfit_nurse_fill.png", drawingAreaX + 1, drawingAreaY + 165, '');
            avatar.addElement("outfit_trsp", 37, globalDir + "images/outfit/outfit_nurse_form_trsp.png", drawingAreaX + 249, drawingAreaY + 245, '');
        }

        if (value == "scrubs_blue"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_scrubs_blue.png", drawingAreaX + 25, drawingAreaY + 175, '');
        }

        if (value == "scrubs_green"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_scrubs_green.png", drawingAreaX + 25, drawingAreaY + 174, '');
        }

        if (value == "vest"){
            fillCreated = true;
            createFillArea("outfit", 35, globalDir + "images/outfit/outfit_vest_form.png", drawingAreaX + 30, drawingAreaY + 179, 230, 275, clothColor);
            avatar.addElement("outfit_fill", 36, globalDir + "images/outfit/outfit_vest_fill.png", drawingAreaX + 30, drawingAreaY + 179, '');
            avatar.addElement("outfit_trsp", 36, globalDir + "images/outfit/outfit_vest_form_trsp.png", drawingAreaX + 30, drawingAreaY + 179, '');
        }

        if (value == "gown"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_gown.png", drawingAreaX + 25, drawingAreaY + 173, '');
        }

        if (value == "pyjamas_female"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_pyjamas_female.png", drawingAreaX + 30, drawingAreaY + 155, '');
        }

        if (value == "pyjamas_male"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_pyjamas_male.png", drawingAreaX + 30, drawingAreaY + 155, '');
        }

        if (value == "doctor_male"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_doctor_male.png", drawingAreaX + 23, drawingAreaY + 169, '');
        }

        if (value == "doctor_female"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_doctor_female.png", drawingAreaX + 27, drawingAreaY + 165, '');
        }

        if (value == "neck"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_neck.png", drawingAreaX + 21, drawingAreaY + 150, '');
        }

        if (value == "blackshirt"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_blackshirt.png", drawingAreaX + 18, drawingAreaY + 152, '');
        }

        if (value == "winterjacket"){
            avatar.addElement("outfit", 35, globalDir + "images/outfit/outfit_winterjacket.png", drawingAreaX - 21, drawingAreaY + 151, '');
        }

        if (value == "vneck"){
            fillCreated = true;
            createFillArea("outfit", 35, globalDir + "images/outfit/outfit_vneck.png", drawingAreaX + 22, drawingAreaY + 168, 220, 260, clothColor);
        }

        if (value == "fleece"){
            fillCreated = true;
            createFillArea("outfit", 35, globalDir + "images/outfit/outfit_fleece_form.png", drawingAreaX + 5, drawingAreaY + 161, 210, 265, clothColor);
            avatar.addElement("outfit_fill", 36, globalDir + "images/outfit/outfit_fleece_fill.png", drawingAreaX + 182, drawingAreaY + 182, '');
            avatar.addElement("outfit_trsp", 36, globalDir + "images/outfit/outfit_fleece_form_trsp.png", drawingAreaX + 5, drawingAreaY + 161, '');
        }

        if (value == "sweater"){
            fillCreated = true;
            createFillArea("outfit", 35, globalDir + "images/outfit/outfit_sweater.png", drawingAreaX - 5, drawingAreaY + 170, 210, 260, clothColor);
        }

        if (!fillCreated){
            redrawMainBody('outfit');
        }
    }

    document.getElementById("eyes").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeEyes(value);
        redrawMainBody('eyes');
    };

    function changeEyes(value){
        deleteElement('eyes');
        if (value == 'open'){
            avatar.addElement("eyes", 25, globalDir + "images/body/eyes_open.png", drawingAreaX + 133, drawingAreaY + 79, '');
        }else{
            avatar.addElement("eyes", 25, globalDir + "images/body/eyes_close.png", drawingAreaX + 126, drawingAreaY + 82, '');
        }
    }

    document.getElementById("nose").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeNose(value);
        redrawMainBody('nose');
    };

    function changeNose(value){
        deleteElement("nose");
        if (value == 'nostrils'){
            avatar.addElement("nose", 25, globalDir + "images/body/nose_nostrils.png", drawingAreaX + 162, drawingAreaY + 122, '');
        }

        if (value == 'petit'){
            avatar.addElement("nose", 25, globalDir + "images/body/nose_petit.png", drawingAreaX + 165, drawingAreaY + 119, '');
        }

        if (value == 'wide'){
            avatar.addElement("nose", 25, globalDir + "images/body/nose_wide.png", drawingAreaX + 148, drawingAreaY + 102, '');
        }
    }

    document.getElementById("hairtype").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        var color_value = document.getElementById("haircolor").value;
        changeHair(value, color_value);
    };

    document.getElementById("haircolor").onchange = function() {
        var value = document.getElementById("hairtype").value;
        var color_value = this.options[this.selectedIndex].value;
        changeHair(value, color_value);
    };

    function changeHair(value, color_value){
        var fillCreated = false;
        deleteElement('hairtype');
        deleteFillElement('4hairtype');
        deleteElement('hairtype_fill');
        deleteElement('hairtype_trsp');

        if (value == 'shaved'){
            clearCanvas();
            fillCreated = true;
            avatar.addElement("hairtype_form", 45, globalDir + "images/hairtype/hairtype_shaved_form.png", drawingAreaX + 105, drawingAreaY, 'hairtype_form');
            var obj = avatar.elMas["hairtype_form"];
            var outlineImage = new Image();
            outlineImage.onload = function() {
                fillAreaBody = context.getImageData(0, 0, canvasWidth, canvasHeight);
                context.drawImage(outlineImage, obj.drawX, obj.drawY, outlineImage.width, outlineImage.height);
                outlineLayerData = context.getImageData(0, 0, canvasWidth, canvasHeight);
                var rgb;
                try{
                    rgb = hex2rgb(color_value);
                }catch(e){
                    rgb = hex2rgb("FFFFFF");
                }

                floodFill(150, 50, rgb, false, 'fill_hair', 45);
                deleteElement("hairtype_form");
                clearCanvas();
                avatar.addElement("hairtype_shaved", 55, globalDir + "images/hairtype/hairtype_shaved.png", drawingAreaX + 105, drawingAreaY, 'hairtype_shaved');
                obj = avatar.elMas["hairtype_shaved"];
                outlineImage = new Image();
                outlineImage.onload = function() {
                    context.drawImage(outlineImage, obj.drawX, obj.drawY, outlineImage.width, outlineImage.height);
                    var img = context.getImageData(0, 0, canvasWidth, canvasHeight);
                    for(color = 3; color < img.data.length; color += 4){
                        if (img.data[color] == 0){
                            fillAreaBody.data[color] = 0;
                            fillAreaBody.data[color - 1] = 0;
                            fillAreaBody.data[color - 2] = 0;
                            fillAreaBody.data[color - 3] = 0;
                        }
                    }
                    redraw(45, 'hairtype');
                    deleteElement('hairtype_shaved');
                    redrawMainBody('hairtype');
                };
                outlineImage.src = obj.name;
            };
            outlineImage.src = obj.name;
        }

        if (value == 'longblonde'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_longblonde_form.png", drawingAreaX + 103, drawingAreaY - 3, [150, 110, 230], [50, 190, 185], color_value);
            avatar.addElement("hairtype_fill", 55, globalDir + "images/hairtype/hairtype_longblonde_fill.png", drawingAreaX + 103, drawingAreaY - 3, '');
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_longblonde_form_trsp.png", drawingAreaX + 103, drawingAreaY - 3, '');
        }

        if (value == 'short'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_short_form.png", drawingAreaX + 101, drawingAreaY - 8, 150, 60, color_value);
            avatar.addElement("hairtype_fill", 55, globalDir + "images/hairtype/hairtype_short_fill.png", drawingAreaX + 101, drawingAreaY - 8, '');
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_short_form_trsp.png", drawingAreaX + 101, drawingAreaY - 8, '');
        }

        if (value == 'curly'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_curly_form.png", drawingAreaX + 63, drawingAreaY - 34, 150, 60, color_value);
            avatar.addElement("hairtype_fill", 55, globalDir + "images/hairtype/hairtype_curly_fill.png", drawingAreaX + 63, drawingAreaY - 34, '');
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_curly_form_trsp.png", drawingAreaX + 63, drawingAreaY - 34, '');
        }

        if (value == 'bob'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_bob_form.png", drawingAreaX + 88, drawingAreaY - 15, 150, 60, color_value);
            avatar.addElement("hairtype_fill", 55, globalDir + "images/hairtype/hairtype_bob_fill.png", drawingAreaX + 88, drawingAreaY - 15, '');
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_bob_form_trsp.png", drawingAreaX + 88, drawingAreaY - 15, '');
        }

        if (value == 'longred'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_longred_form.png", drawingAreaX + 80, drawingAreaY - 20, 150, 60, color_value);
            avatar.addElement("hairtype_fill", 55, globalDir + "images/hairtype/hairtype_longred_fill.png", drawingAreaX + 80, drawingAreaY - 20, '');
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_longred_form_trsp.png", drawingAreaX + 80, drawingAreaY - 20, '');
        }

        if (value == 'grandpa'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_grandpa_form.png", drawingAreaX + 93, drawingAreaY + 5, [85, 205], [100, 70], color_value);
            avatar.addElement("hairtype_fill", 55, globalDir + "images/hairtype/hairtype_grandpa_fill.png", drawingAreaX + 93, drawingAreaY + 5, '');
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_grandpa_form_trsp.png", drawingAreaX + 93, drawingAreaY + 5, '');
        }

        if (value == 'granny'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_granny_form.png", drawingAreaX + 75, drawingAreaY - 10, 160, 60, color_value);
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_granny_form_trsp.png", drawingAreaX + 75, drawingAreaY - 10, '');
        }

        if (value == 'youngman'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_youngman_form.png", drawingAreaX + 96, drawingAreaY - 7, 140, 50, color_value);
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_youngman_form_trsp.png", drawingAreaX + 96, drawingAreaY - 7, '');
        }

        if (value == 'long'){
            fillCreated = true;
            createFillArea("hairtype", 45, globalDir + "images/hairtype/hairtype_long_form.png", drawingAreaX + 80, drawingAreaY - 25, 130, 40, color_value);
            avatar.addElement("hairtype_trsp", 55, globalDir + "images/hairtype/hairtype_long_form_trsp.png", drawingAreaX + 80, drawingAreaY - 25, '');
        }

        if (!fillCreated){
            redrawMainBody('hairtype');
        }
    }

    document.getElementById("avaccessory1").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeAccessory("acc1", value, 65);
        redrawMainBody('accessory1');
    };

    document.getElementById("avaccessory2").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeAccessory("acc2", value, 75);
        redrawMainBody('accessory2');
    };

    document.getElementById("avaccessory3").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeAccessory("acc3", value, 85);
        redrawMainBody('accessory3');
    };

    function changeAccessory(id, value, zIndex){
        deleteElement(id);
        if (value == 'glasses'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_glasses.png", drawingAreaX + 107, drawingAreaY + 62, '');
        }

        if (value == 'sunglasses'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_sunglasses.png", drawingAreaX + 107, drawingAreaY + 64, '');
        }

        if (value == 'bindi'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_bindi.png", drawingAreaX + 157, drawingAreaY + 69, '');
        }

        if (value == 'moustache'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_moustache.png", drawingAreaX + 147, drawingAreaY + 122, '');
        }

        if (value == 'freckles'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_freckles.png", drawingAreaX + 125, drawingAreaY + 95, '');
        }

        if (value == 'blusher'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_blusher.png", drawingAreaX + 125, drawingAreaY + 95, '');
        }

        if (value == 'earrings'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_earrings.png", drawingAreaX + 106, drawingAreaY + 114, '');
        }

        if (value == 'beads'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_beads.png", drawingAreaX + 135, drawingAreaY + 162, '');
        }

        if (value == 'neckerchief'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_neckerchief.png", drawingAreaX + 116, drawingAreaY + 169, '');
        }

        if (value == 'redscarf'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_redscarf.png", drawingAreaX + 139, drawingAreaY + 161, '');
        }

        if (value == 'beanie'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_beanie.png", drawingAreaX + 104, drawingAreaY - 22, '');
        }

        if (value == 'buttonscarf'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_buttonscarf.png", drawingAreaX + 110, drawingAreaY + 133, '');
        }

        if (value == 'baseballcap'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_baseballcap.png", drawingAreaX + 101, drawingAreaY - 22, '');
        }

        if (value == 'winterhat'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_winterhat.png", drawingAreaX + 97, drawingAreaY - 28, '');
        }

        if (value == 'mask'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_mask.png", drawingAreaX + 107, drawingAreaY + 66, '');
        }

        if (value == 'stethoscope'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_stethoscope.png", drawingAreaX + 104, drawingAreaY + 88, '');
        }

        if (value == 'oxygenmask'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_oxygenmask.png", drawingAreaX + 106, drawingAreaY + 64, '');
        }

        if (value == 'surgeoncap'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_surgeoncap.png", drawingAreaX + 103, drawingAreaY + 2, '');
        }

        if (value == 'eyepatch'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_eyepatch.png", drawingAreaX + 103, drawingAreaY + 55, '');
        }

        if (value == 'scratches'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_scratches.png", drawingAreaX + 120, drawingAreaY + 42, '');
        }

        if (value == 'splitlip'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_splitlip.png", drawingAreaX + 176, drawingAreaY + 139, '');
        }

        if (value == 'blackeyeleft'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_blackeye.png", drawingAreaX + 182, drawingAreaY + 75, '');
        }

        if (value == 'blackeyeright'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_blackeye.png", drawingAreaX + 125, drawingAreaY + 86, '');
        }

        if (value == 'headbandage'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_headbandage.png", drawingAreaX + 104, drawingAreaY + 0, '');
        }

        if (value == 'neckbrace'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_neckbrace.png", drawingAreaX + 110, drawingAreaY + 108, '');
        }

        if (value == 'tearssmall'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_tearssmall.png", drawingAreaX + 130, drawingAreaY + 82, '');
        }

        if (value == 'tearslarge'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_tearslarge.png", drawingAreaX + 130, drawingAreaY + 82, '');
        }

        if (value == 'sweat'){
            avatar.addElement(id, zIndex, globalDir + "images/acc/acc_sweat.png", drawingAreaX + 116, drawingAreaY + 35, '');
        }
    }

    document.getElementById("environment").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        changeEnvironment(value);
    };

    function changeEnvironment(value){
        deleteElement("env");
        deleteElement("0bgcolor");
        deleteFillElement('0bgcolor');
        if (value == 'ambulancebay'){
            avatar.addElement("env", 0, globalDir + "images/env/env_ambulancebay.png", drawingAreaX - 25, drawingAreaY - 30, '');
        }

        if (value == 'bedpillow'){
            avatar.addElement("env", 0, globalDir + "images/env/env_bedpillow.png", drawingAreaX, drawingAreaY - 30, '');
        }

        if (value == 'hospital'){
            avatar.addElement("env", 0, globalDir + "images/env/env_hospital.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'waitingroom'){
            avatar.addElement("env", 0, globalDir + "images/env/env_waitingroom.png", drawingAreaX, drawingAreaY - 38, '');
        }

        if (value == 'insideambulance'){
            avatar.addElement("env", 0, globalDir + "images/env/env_insideambulance.png", drawingAreaX + 30, drawingAreaY - 30, '');
        }

        if (value == 'xray'){
            avatar.addElement("env", 0, globalDir + "images/env/env_xray.png", drawingAreaX + 20, drawingAreaY - 25, '');
        }

        if (value == 'ca'){
            avatar.addElement("env", 0, globalDir + "images/env/env_ca.png", drawingAreaX + 20, drawingAreaY - 70, '');
        }

        if (value == 'medivachelicopter'){
            avatar.addElement("env", 0, globalDir + "images/env/env_medivachelicopter.png", drawingAreaX + 10, drawingAreaY - 40, '');
        }

        if (value == 'heartmonitor'){
            avatar.addElement("env", 0, globalDir + "images/env/env_heartmonitor.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'stopsign'){
            avatar.addElement("env", 0, globalDir + "images/env/env_stopsign.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'bedside'){
            avatar.addElement("env", 0, globalDir + "images/env/env_bedside.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'ambulance2'){
            avatar.addElement("env", 0, globalDir + "images/env/env_ambulance2.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'machine'){
            avatar.addElement("env", 0, globalDir + "images/env/env_machine.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'livingroom'){
            avatar.addElement("env", 0, globalDir + "images/env/env_livingroom.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'basicoffice'){
            avatar.addElement("env", 0, globalDir + "images/env/env_basicoffice.png", drawingAreaX - 15, drawingAreaY - 30, '');
        }

        if (value == 'basicroom'){
            avatar.addElement("env", 0, globalDir + "images/env/env_basicroom.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'corridor'){
            avatar.addElement("env", 0, globalDir + "images/env/env_corridor.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'pillowb'){
            avatar.addElement("env", 0, globalDir + "images/env/env_pillowb.png", drawingAreaX + 35, drawingAreaY - 30, '');
        }

        if (value == 'concourse'){
            avatar.addElement("env", 0, globalDir + "images/env/env_concourse.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'officecubicle'){
            avatar.addElement("env", 0, globalDir + "images/env/env_officecubicle.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'residentialstreet'){
            avatar.addElement("env", 0, globalDir + "images/env/env_residentialstreet.png", drawingAreaX - 60, drawingAreaY - 40, '');
        }

        if (value == 'highstreet'){
            avatar.addElement("env", 0, globalDir + "images/env/env_highstreet.png", drawingAreaX - 10, drawingAreaY - 30, '');
        }

        if (value == 'cityskyline'){
            avatar.addElement("env", 0, globalDir + "images/env/env_cityskyline.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'lakeside'){
            avatar.addElement("env", 0, globalDir + "images/env/env_lakeside.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'suburbs'){
            avatar.addElement("env", 0, globalDir + "images/env/env_suburbs.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'summer'){
            avatar.addElement("env", 0, globalDir + "images/env/env_summer.png", drawingAreaX + 19, drawingAreaY - 30, '');
        }

        if (value == 'longroad'){
            avatar.addElement("env", 0, globalDir + "images/env/env_longroad.png", drawingAreaX - 20, drawingAreaY - 30, '');
        }

        if (value == 'downtown'){
            avatar.addElement("env", 0, globalDir + "images/env/env_downtown.png", drawingAreaX + 20, drawingAreaY - 40, '');
        }

        if (value == 'winter'){
            avatar.addElement("env", 0, globalDir + "images/env/env_winter.png", drawingAreaX + 10, drawingAreaY - 30, '');
        }

        if (value == 'outsidelake'){
            avatar.addElement("env", 0, globalDir + "images/env/env_outsidelake.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'roadside'){
            avatar.addElement("env", 0, globalDir + "images/env/env_roadside.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'forestriver'){
            avatar.addElement("env", 0, globalDir + "images/env/env_forestriver.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'yieldsign'){
            avatar.addElement("env", 0, globalDir + "images/env/env_yieldsign.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'parkinglot'){
            avatar.addElement("env", 0, globalDir + "images/env/env_parkinglot.png", drawingAreaX + 20, drawingAreaY - 30, '');
        }

        if (value == 'none'){
            var color_value = document.getElementById("bgcolor").value;
            if (color_value != ''){
                try{
                    color_value = hex2rgb(color_value);
                }catch(e){
                    color_value = hex2rgb("FFFFFF");
                }
                changeBgColor(color_value);
            }else{

                redrawMainBody('environment');
            }
        }else{
            redrawMainBody('environment');
        }
    }

    document.getElementById("bgcolor").onchange = function() {
        deleteElement('0bgcolor');
        deleteFillElement('0bgcolor');
        var color_value = document.getElementById("bgcolor").value;
        if (color_value != ''){
            var color = '#'+ color_value;
            color_value = {r: 255, g: 255, b: 255};
            color_value.r = hexToR(color);
            color_value.g = hexToG(color);
            color_value.b = hexToB(color);
            changeBackgroundColor(color_value);
        }else{
            redrawMainBody('bgcolor');
        }
    };
    
    function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
    function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
    function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
    function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}

    function changeBackgroundColor(color_value){
        var value = document.getElementById("environment").value;
        if (value == 'none'){
            changeBgColor(color_value);
        }
    }

    function changeBgColor(color_value){
        clearCanvas();
        fillAreaBody = context.getImageData(0, 0, canvasWidth, canvasHeight);
        outlineLayerData = context.getImageData(0, 0, canvasWidth, canvasHeight);
        floodFill(10, 10, color_value, true, 'bgcolor', 0);
        redrawMainBody('bgcolor');
    }

    document.getElementById("bubble").onchange = function() {
        var value = this.options[this.selectedIndex].value;
        var text = document.getElementById("bubbletext").value;
        changeBubble(value, text);
        redrawMainBody('bubble');
    };

    document.getElementById("bubbletext").onblur = function() {
        var value = document.getElementById("bubble").value;
        var text = this.value;
        changeBubble(value, text);
        redrawMainBody('bubbletext');
    };

    function changeBubble(value, text){
        var objText;
        deleteElement("bubble");
        deleteFillElement("9bubbletext");
        if (value == 'normal'){
            avatar.addElement("bubble", 95, globalDir + "images/bubble/bubble_normal.png", drawingAreaX + 170, drawingAreaY + 145, '');
            objText = {id: "bubbletext", drawX: drawingAreaX + 186, drawY: drawingAreaY + 194, strlen: 16, maxlen: 50};
            outlineImageObj.addOutlineImage(95, objText, text, 't');
        }

        if (value == 'think'){
            avatar.addElement("bubble", 95, globalDir + "images/bubble/bubble_think.png", drawingAreaX + 35, drawingAreaY - 5, '');
            objText = {id: "bubbletext", drawX: drawingAreaX + 65, drawY: drawingAreaY + 18, strlen: 14, maxlen: 45};
            outlineImageObj.addOutlineImage(95, objText, text, 't');
        }

        if (value == 'shout'){
            avatar.addElement("bubble", 95, globalDir + "images/bubble/bubble_shout.png", drawingAreaX + 160, drawingAreaY + 145, '');
            objText = {id: "bubbletext", drawX: drawingAreaX + 200, drawY: drawingAreaY + 192, strlen: 10, maxlen: 32};
            outlineImageObj.addOutlineImage(95, objText, text, 't');
        }
    }

    function deleteElement(keyToFind){
        for(key in avatar.elMas){
            if (avatar.elMas[key].id == keyToFind){
                var z = avatar.elMas[key].zIndex;
                delete(avatar.elMas[key]);
                delete(outlineImageObj.imageRepos[z + keyToFind]);
            }
        }
    }

    function deleteFillElement(keyToFind){
        delete(outlineImageObj.imageRepos[keyToFind]);
    }

    function createFillArea(name, zIndex, img_src, drawX, drawY, fillX, fillY, color){
        clearCanvas();
        var outlineImage = new Image();
        var countFill = 0;
        outlineImage.onload = function() {
            fillAreaBody = context.getImageData(0, 0, canvasWidth, canvasHeight);
            context.drawImage(outlineImage, drawX, drawY, outlineImage.width, outlineImage.height);
            outlineLayerData = context.getImageData(0, 0, canvasWidth, canvasHeight);
            var rgb;
            try{
                rgb = hex2rgb(color);
            }catch(e){
                rgb = hex2rgb("FFFFFF");
            }
            if (isArray(fillX)){
                countFill = countObj(fillX);
                var i = 0;
                for(i = 0; i < countFill - 1; i++){
                    floodFill(fillX[i], fillY[i], rgb, false, name, zIndex);
                }
                floodFill(fillX[i], fillY[i], rgb, true, name, zIndex);
            }else{
                floodFill(fillX, fillY, rgb, true, name, zIndex);
            }
            redrawMainBody(name);
        };
        outlineImage.src = img_src;
    }

    function isArray(v) {
        return Object.prototype.toString.apply(v) === '[object Array]';
    }

    document.getElementById("save").onclick = function() {
        saveAsImage();
        document.avatar_form.submit();
    };

    document.getElementById("save_exit").onclick = function() {
        saveAsImage();
        document.getElementById("save_exit_value").value = '1';
        document.avatar_form.submit();
    };

    function saveAsImage(){
        document.getElementById("image_data").value = canvas.toDataURL();
    }
};