// Basic transformation object
var Transform = function() {
    var self = this;
    
    this.matrix = [1, 0, 0, 1, 0, 0];
    
    // Reset current transformation matrix (set indetity)
    this.SetIdentity = function() {
        this.matrix = [1, 0, 0, 1, 0, 0];
    }
    
    // Multiply current transformation with another
    // transform - Transform object
    this.Multiply = function(transform) {
        this.matrix[0] = this.matrix[0] * transform.matrix[0] + this.matrix[2] * transform.matrix[1];
        this.matrix[1] = this.matrix[1] * transform.matrix[0] + this.matrix[3] * transform.matrix[1];
        this.matrix[2] = this.matrix[0] * transform.matrix[2] + this.matrix[2] * transform.matrix[3];
        this.matrix[3] = this.matrix[1] * transform.matrix[2] + this.matrix[3] * transform.matrix[3];
        this.matrix[4] = this.matrix[0] * transform.matrix[4] + this.matrix[2] * transform.matrix[5] + this.matrix[4];
        this.matrix[5] = this.matrix[1] * transform.matrix[4] + this.matrix[3] * transform.matrix[5] + this.matrix[5];
    }
    
    // Translate current transfomation on x, y values
    // tx - number X-transalte coord 
    // ty - number Y-transalte coord 
    this.Translate = function(tx, ty) {
        this.matrix[4] += this.matrix[0] * tx + this.matrix[2] * ty;
        this.matrix[5] += this.matrix[1] * tx + this.matrix[3] * ty;
    }
    
    // Transalate current transfomation on x, y, values without scale factor
    // tx - number X-transalte coord 
    // ty - number Y-transalte coord 
    this.TranslateWithoutScale = function(tx, ty) {
        this.matrix[4] += tx;
        this.matrix[5] += ty;
    }
    
    // Scale current transfomation on x, y values
    // sx - number X-scale factor 
    // sy - number Y-scale factor 
    this.Scale = function(sx, sy) {
        this.matrix[0] *= sx;
        this.matrix[1] *= sx;
        this.matrix[2] *= sy;
        this.matrix[3] *= sy;
    }
    
    // Return x, y position 
    // return - array(x, y);
    this.GetPosition = function() {
        return [this.matrix[4], this.matrix[5]];
    }
    
    // Return x, y scale factor
    // return - array(sx, sy);
    this.GetScale = function() {
        return [this.matrix[0], this.matrix[3]];
    }
}