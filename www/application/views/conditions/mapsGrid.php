<form action="<?php echo URL::base().'webinarManager/saveMapsGrid/'.$templateData['condition']->id.'/'.$templateData['scenario']->id; ?>" method="post">
    <h1 class="page-header">
        Scenario '<?php echo $templateData['scenario']->title; ?>'
        <button class="btn btn-primary btn-large pull-right" type="submit"><?php echo __('Save changes'); ?></button>
    </h1><?php
    foreach ($templateData['nodes'] as $title => $nodes) {
        $condition = $templateData['condition']; ?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>
                    <h4>
                        <?php echo $title; ?>
                        <button type="button" class="btn btn-success pull-right icon-minus nodesGrid "></button>
                    </h4>
                </th>
                <th style="width:155px;">
                    <?php echo __('Appear on node'); ?>
                    <a href="javascript:void(0)" id="counter_id_<?php echo $condition->id; ?>" class="btn btn-info btn-mini toggle-all-on">all on</a>
                    <a href="javascript:void(0)" id="counter_id_<?php echo $condition->id; ?>" class="btn btn-info btn-mini toggle-all-off">all off</a>
                    <a href="javascript:void(0)" id="counter_id_<?php echo $condition->id; ?>" class="btn btn-info btn-mini toggle-reverse">reverse</a>
                </th>
            </tr>
        </thead>
        <tbody><?php
        foreach ($nodes as $node) {
            $existNode = Arr::get($templateData['existingNode'], $node->id, false);
            $name    = $existNode ? 'existNodes['.$existNode['id'].']' : 'newNodes['.$node->id.']';
            $value   = $existNode ? $existNode['value'] : '';
            $appears = $existNode ? $existNode['appears'] : 0; ?>
            <tr>
                <td><p><?php echo $node->title; ?> [<?php echo $node->id; ?>]</p></td>
                <td>
                    <input class="input-small not-autocomplete" type="text" size="5" name="<?php echo $name; ?>[value]" placeholder="Value" value="<?php echo $value ?>">
                    <label>
                        <input autocomplete="off" name="<?php echo $name; ?>[appears]" class="chk_counter_id_<?php echo $condition->id; ?>" type="checkbox" value="1" <?php if ($appears) echo 'checked'; ?>/>
                        <?php echo __("appear on node"); ?>
                    </label>
                </td>
            </tr><?php
        } ?>
        </tbody>
    </table><?php
    } ?>
    <button class="btn btn-primary btn-large pull-right" type="submit"><?php echo __('Save changes'); ?></button>
</form>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/conditions.js'); ?>"></script>