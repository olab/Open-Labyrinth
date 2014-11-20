var Section = (function() {
    function Section(id, name, color, orderBy) {
        this.id         = id;
        this.name       = name;
        this.color      = color;
        this.orderBy    = orderBy;
        this.nodes      = [];
    }

    Section.prototype.deleteNode = function(nodeId) {
        if(this.nodes.length <= 0) return;

        for(var i = this.nodes.length; i--;) {
            if(this.nodes[i].node.id == nodeId) {
                this.nodes.splice(i, 1);
            }
        }
    };

    return Section;
})();