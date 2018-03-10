var Utils = function()
{
    var self = this,
        PI180 = 180 / Math.PI;
    
    this.GetRotationAngle = function($object)
    {
        if($object == null) return null;
        
        var tr = $object.css('-webkit-transform') || 
                 $object.css('-moz-transform') ||
                 $object.css('-ms-transform') ||
                 $object.css('-o-transform') ||
                 $object.css('transform');

        if(tr === 'none') return null;

        var values = tr.split('(')[1];
            values = values.split(')')[0];
            values = values.split(',');
        return Math.round(Math.atan2(values[1], values[0]) * PI180);
    };
    
    this.Encode64 = function (input) {
        input = input.replace(/\0/g,"");
        return  B64.encode($.trim(input));
    };

    this.Decode64 = function(input) {
        input = input.replace(/\0/g,"");
        return  B64.decode($.trim(input));
    };
};

var utils = new Utils();