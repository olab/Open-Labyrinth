<style>

    #wheel {
        font: 10px sans-serif;
    }

    .chord path {
        fill-opacity: .67;
        stroke: #000;
        stroke-width: .5px;
    }

</style>
<div class="row">
    <form action="" method="get" class="form" style="text-align: center">
        <select name="map" id="map">
            <?php foreach($templateData["allMaps"] as $map):?>
                <option <?php if($templateData["map"]==$map["map"]) echo "selected";?> value="<?php echo $map['map']?>"><?php echo $map["name"] ?></option>
            <?php endforeach;?>
        </select>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div class="row" id="chart_placeholder">
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="http://www.redotheweb.com/DependencyWheel/js/d3.dependencyWheel.js"></script>
<script>

    // From http://mkweb.bcgsc.ca/circos/guide/tables/
    var matrix =<?php echo(json_encode($templateData["data"])); ?>;
    var packageNames =<?php echo(json_encode($templateData["labels"])); ?>;

    var data = {
        packageNames: packageNames,
        matrix: matrix // B doesn't depend on A or Main
    };
    var chart = d3.chart.dependencyWheel();
    d3.select('#chart_placeholder')
        .datum(data)
        .call(chart);

</script>