// Basic transformation object
var Transform = function()
{
    var self = this;
    
    self.matrix = [1, 0, 0, 1, 0, 0];

    // Reset current transformation matrix (set indetity)
    self.SetIdentity = function()
    {
        self.matrix = [1, 0, 0, 1, 0, 0];
    };
    
    // Multiply current transformation with another
    // transform - Transform object
    self.Multiply = function(transform)
    {
        self.matrix[0] = self.matrix[0] * transform.matrix[0] + self.matrix[2] * transform.matrix[1];
        self.matrix[1] = self.matrix[1] * transform.matrix[0] + self.matrix[3] * transform.matrix[1];
        self.matrix[2] = self.matrix[0] * transform.matrix[2] + self.matrix[2] * transform.matrix[3];
        self.matrix[3] = self.matrix[1] * transform.matrix[2] + self.matrix[3] * transform.matrix[3];
        self.matrix[4] = self.matrix[0] * transform.matrix[4] + self.matrix[2] * transform.matrix[5] + self.matrix[4];
        self.matrix[5] = self.matrix[1] * transform.matrix[4] + self.matrix[3] * transform.matrix[5] + self.matrix[5];
    };
    
    // Translate current transfomation on x, y values
    // tx - number X-transalte coord 
    // ty - number Y-transalte coord 
    self.Translate = function(tx, ty)
    {
        self.matrix[4] += self.matrix[0] * tx + self.matrix[2] * ty;
        self.matrix[5] += self.matrix[1] * tx + self.matrix[3] * ty;
    };
    
    // Transalate current transfomation on x, y, values without scale factor
    // tx - number X-transalte coord 
    // ty - number Y-transalte coord 
    self.TranslateWithoutScale = function(tx, ty)
    {
        self.matrix[4] += tx;
        self.matrix[5] += ty;
    };
    
    // Set current transformation position
    // x - number X-position coord
    // y - number Y-posiotion coord
    self.SetPosition = function(x, y)
    {
        self.matrix[4] = x;
        self.matrix[5] = y;
    } ;
    
    // Scale current transfomation on x, y values
    // sx - number X-scale factor 
    // sy - number Y-scale factor 
    self.Scale = function(sx, sy)
    {
        self.matrix[0] *= sx;
        self.matrix[1] *= sx;
        self.matrix[2] *= sy;
        self.matrix[3] *= sy;
    };
    
    // Return x, y position 
    // return - array(x, y);
    self.GetPosition = function()
    {
        return [self.matrix[4], self.matrix[5]];
    };
    
    // Return x, y scale factor
    // return - array(sx, sy);
    self.GetScale = function()
    {
        return [self.matrix[0], self.matrix[3]];
    };
};