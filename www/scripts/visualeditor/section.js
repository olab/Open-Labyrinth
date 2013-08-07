var Section = (function() {
    function Section(id, name, color) {
        this.id    = id;
        this.name  = name;
        this.color = color;
        this.nodes = [];
    };

    return Section;
})();