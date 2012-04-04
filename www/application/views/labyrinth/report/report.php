<?php 
function getRandomColor(){
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
} 
?>
<?php if (isset($templateData['session'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Labyrinth session "') . $templateData['session']->map->name . '"' . ' user ' . $templateData['session']->user->nickname; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff" align="left"><td align="left">
                            <p><?php echo __('user'); ?>:&nbsp;<?php echo $templateData['session']->user->nickname; ?><br>
                                session:&nbsp;<?php echo $templateData['session']->id; ?><br>
                                Labyrinth:&nbsp;<?php echo $templateData['session']->map->name; ?> (<?php echo $templateData['session']->map->id; ?>)<br>
                                start time:&nbsp;<?php echo date('Y.m.d H:i:s', $templateData['session']->start_time); ?><br>
                                time taken:&nbsp;
                                <?php
                                if (count($templateData['session']->traces) > 0) {
                                    $max = $templateData['session']->start_time;
                                    foreach($templateData['session']->traces as $val) {
                                        if($val->date_stamp > $max) {
                                            $max = $val->date_stamp;
                                        }
                                    }
                                    $t = $max - $templateData['session']->start_time;
                                    echo date('i:s', $t);
                                }
                                ?>
                                <br>
                                <?php echo count($templateData['session']->traces); ?>&nbsp;<?php echo __('nodes visited altogether of which'); ?>&nbsp;<?php if(count($templateData['session']->traces) > 0) { echo count($templateData['session']->traces); } ?>&nbsp;<?php echo __('required nodes and'); ?>&nbsp;
                                <?php if(count($templateData['session']->traces) > 0) {
                                    $nodesIDs = array();
                                    foreach($templateData['session']->traces as $val) {
                                        $nodesIDs[] = $val->node_id;
                                    }
                                    
                                    if(isset($templateData['nodes'])) {
                                        echo count($templateData['nodes']) - count($nodesIDs);
                                    }
                                }
                                ?>&nbsp;<?php echo __('avoid nodes visited'); ?></p>

                            <hr>
                            <p><?php echo __('general feedback'); ?>: <?php if(isset($templateData['feedbacks']['general'])) echo $templateData['feedbacks']['general']; ?></p>
                            <?php if(isset($templateData['feedbacks']['timeTaken']) and count($templateData['feedbacks']['timeTaken']) > 0) { ?>
                            <p><?php echo __('feedback for time taken'); ?>:<br/>
                                <?php foreach($templateData['feedbacks']['timeTaken'] as $msg) { echo $msg.'<br/>'; } ?>
                            </p>
                            <?php } ?>
                            
                            <?php if(isset($templateData['feedbacks']['nodeVisit']) and count($templateData['feedbacks']['nodeVisit']) > 0) { ?>
                            <p><?php echo __('feedback for nodes visit'); ?>:<br/>
                                <?php foreach($templateData['feedbacks']['nodeVisit'] as $msg) { echo $msg.'<br/>'; } ?>
                            </p>
                            <?php } ?>
                            
                            <?php if(isset($templateData['feedbacks']['mustVisit']) and count($templateData['feedbacks']['mustVisit']) > 0) { ?>
                            <p><?php echo __('feedback for must visit'); ?>:<br/>
                                <?php foreach($templateData['feedbacks']['mustVisit'] as $msg) { echo $msg.'<br/>'; } ?>
                            </p>
                            <?php } ?>
                            
                            <?php if(isset($templateData['feedbacks']['mustAvoid']) and count($templateData['feedbacks']['mustAvoid']) > 0) { ?>
                            <p><?php echo __('feedback for must avoid'); ?>:<br/>
                                <?php foreach($templateData['feedbacks']['mustAvoid'] as $msg) { echo $msg.'<br/>'; } ?>
                            </p>
                            <?php } ?>
                            
                            <?php if(isset($templateData['feedbacks']['counters']) and count($templateData['feedbacks']['counters']) > 0) { ?>
                            <p><?php echo __('feedback for counters'); ?>:<br/>
                                <?php foreach($templateData['feedbacks']['counters'] as $msg) { echo $msg.'<br/>'; } ?>
                            </p>
                            <?php } ?>
                            <hr>

                            <hr>
                            <p><strong><?php echo __('Questions'); ?></strong></p>
                            <table border="1" cellpadding="4" width="100%">
                                <tr>
                                    <td><p>ID</p></td>
                                    <td><p>type</p></td>
                                    <td><p>stem</p></td>
                                    <td><p>response</p></td>
                                    <td><p>correct</p></td>
                                    <td><p><?php echo __('feedback'); ?></p></td>
                                </tr>
                                <?php if($templateData['questions'] != NULL) { ?>
                                    <?php foreach($templateData['questions'] as $question) { ?>
                                <tr>
                                        <td><p><?php echo $question->id; ?></p></td>
                                        <td><p><?php echo $question->type->title; ?></p></td>
                                        <td><p><?php echo $question->stem; ?></p></td>
                                        <td><p><?php if(isset($templateData['responses']) and isset($templateData['responses'][$question->id])) { 
                                            if($templateData['responses'][$question->id]->response != '') {
                                                echo $templateData['responses'][$question->id]->response;
                                            } else {
                                                echo 'no response';
                                            }
                                        } else { echo 'no response'; }
                                        ?></p></td>
                                        <td>
                                            <?php if($question->type->value != 'text' and $question->type->value != 'area') { ?>
                                                <?php if(count($question->responses) > 0) { ?>
                                                    <?php foreach($question->responses as $resp) { ?>
                                                        <?php if($resp->is_correct) { ?>
                                                            <p><?php echo $resp->response; ?></p>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } else { echo '<p>n/a</p>'; } ?>
                                            <?php } else { echo '<p>n/a</p>'; } ?>
                                            
                                        </td>
                                        <td><p>
                                                <?php echo $question->feedback; ?>
                                            </p></td>
                                </tr>
                                    <?php } ?>
                                <?php } ?>
                            </table>
                            <hr>
                            <table border="0" width="100%" cellpadding="4" cellspacing="2">
                                <tr>
                                    <td><p>node</p></td>
                                    <td><p><?php echo __('time elapsed (in seconds)'); ?></p></td>
                                    <td><p><?php echo __('time spent on node'); ?></p></td>
                                </tr>
                                <?php if (count($templateData['session']->traces) > 0) {
                                    $flag = true; ?>
                                <?php for ($i = 0; $i < count($templateData['session']->traces); $i++) { ?>
                                                                <tr bgcolor="<?php if ($flag) {
                                        echo '#BBBBDB';
                                        $flag = false;
                                    } else {
                                        echo '#DDDDFD';
                                        $flag = true;
                                    } ?>">
                                            <td><p><?php echo $templateData['session']->traces[$i]->node->title; ?> (<?php echo $templateData['session']->traces[$i]->node_id; ?>)</p></td>
                                            <td><p><?php echo $templateData['session']->traces[$i]->date_stamp - $templateData['session']->start_time; ?></p></td>
                                            <td><p><?php if ($i > 0) echo $templateData['session']->traces[$i]->date_stamp - $templateData['session']->traces[$i - 1]->date_stamp; else echo 0; ?></p></td>
                                        </tr>
                        <?php } ?>
                    <?php } ?>
                    </tr></table>
                <br><br>
                
                <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="565" height="420">
                    <param name="FlashVars" value="&dataXML=<graph bgcolor='FFFFFF' canvasbgcolor='FFFFFF' xaxisname='node path (node IDs)' yaxisname='time on node (s)' caption='Node Path Analysis'>
                        <?php if (count($templateData['session']->traces) > 0) {
                            $flag = true; ?>
                                            <?php for ($i = 0; $i < count($templateData['session']->traces); $i++) { ?>
                                                    <set name='<?php echo $templateData['session']->traces[$i]->node_id; ?>' value='<?php if ($i > 0) echo $templateData['session']->traces[$i]->date_stamp - $templateData['session']->traces[$i - 1]->date_stamp; else echo 0; ?>' color='<?php if ($flag) {
                                        echo '#666696';
                                        $flag = false;
                                    } else {
                                        echo '#8888A8';
                                        $flag = true;
                                    } ?>'>
                            <?php } ?>
                        <?php } ?>
                           </graph>">
                    <param name="movie" value="<?php echo URL::base(); ?>documents/FC_2_3_Column3D.swf">
                    <param name="quality" value="high">
                    <param name="bgcolor" value="#FFFFFF">
                    <embed src="<?php echo URL::base(); ?>documents/FC_2_3_Column3D.swf" flashvars="&dataXML=<graph bgcolor='FFFFFF' canvasbgcolor='FFFFFF' xaxisname='node path (node IDs)' yaxisname='time on node (s)' caption='Node Path Analysis'>
                        <?php if (count($templateData['session']->traces) > 0) {
                            $flag = true; ?>
                            <?php for ($i = 0; $i < count($templateData['session']->traces); $i++) { ?>
                                                    <set name='<?php echo $templateData['session']->traces[$i]->node_id; ?>' value='<?php if ($i > 0) echo $templateData['session']->traces[$i]->date_stamp - $templateData['session']->traces[$i - 1]->date_stamp; else echo 0; ?>' color='<?php if ($flag) {
                                    echo '#666696';
                                    $flag = false;
                                } else {
                                    echo '#8888A8';
                                    $flag = true;
                                } ?>' />
                            <?php } ?>
                        <?php } ?>
                           </graph>" quality="high" bgcolor="#FFFFFF" name="Line" align="" width="565" height="420" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
                </object>
                <br><br><p><strong>Counters Track</strong></p>
                <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="565" height="420">
                    <param name="FlashVars" value="&dataXML=<graph caption='Counters' lineThickness='2' showValues='1' formatNumberScale='1' rotateNames='1' decimalPrecision='2' anchorRadius='2' anchorBgAlpha='50' showAlternateVGridColor='1' anchorAlpha='100' animation='1' limitsDecimalPrecision='0' divLineDecimalPrecision='1'>
                           <categories>
                           <?php if (count($templateData['session']->traces) > 0) { ?>
                           <?php foreach($templateData['session']->traces as $trace) { ?>
                           <c n='<?php echo $trace->node_id; ?>' />
                           <?php } ?>
                           <?php } ?>
                           </categories>
                           <?php if(count($templateData['counters']) > 0) { ?>
                           <?php foreach($templateData['counters'] as $counter) { ?>
                           <dataset seriesName='Counter: <?php echo $counter[0]; ?>' color='<?php $v[$counter[0]] = getRandomColor(); echo $v[$counter[0]]; ?>' anchorBorderColor='<?php echo $v[$counter[0]]; ?>' >
                                    <?php if(isset($templateData['startValueCounters'])) { ?>
                                    <s v='<?php echo $templateData['startValueCounters'][$counter[2]]; ?>' />
                                    <?php } ?>
                                    <?php if(count($counter[1]) > 0) { ?>
                                        <?php for($i = count($counter[1]) - 1; $i >= 0; $i--) { ?>
                                            <s v='<?php echo $counter[1][$i]; ?>' />
                                        <?php } ?>
                                    <?php } ?>
                           </dataset>
                           <?php } ?>
                           <?php } ?>
                           </graph>"><param name="movie" value="<?php echo URL::base(); ?>documents/FC_2_3_MSLine.swf"><param name="quality" value="high"><param name="bgcolor" value="#FFFFFF"><embed src="<?php echo URL::base(); ?>documents/FC_2_3_MSLine.swf" flashvars="&dataXML=<graph caption='Counters' lineThickness='2' showValues='1' formatNumberScale='1' rotateNames='1' decimalPrecision='2' anchorRadius='2' anchorBgAlpha='50' showAlternateVGridColor='1' anchorAlpha='100' animation='1' limitsDecimalPrecision='0' divLineDecimalPrecision='1'>
                           <categories>
                           <?php if (count($templateData['session']->traces) > 0) { ?>
                           <?php foreach($templateData['session']->traces as $trace) { ?>
                           <c n='<?php echo $trace->node_id; ?>' />
                           <?php } ?>
                           <?php } ?>
                           </categories>
                           <?php if(count($templateData['counters']) > 0) { ?>
                           <?php foreach($templateData['counters'] as $counter) { ?>
                                <dataset seriesName='Counter: <?php echo $counter[0]; ?>' color='<?php echo $v[$counter[0]]; ?>' anchorBorderColor='<?php echo $v[$counter[0]]; ?>' >
                                    <?php if(isset($templateData['startValueCounters'])) { ?>
                                    <s v='<?php echo $templateData['startValueCounters'][$counter[2]]; ?>' />
                                    <?php } ?>
                                    <?php if(count($counter[1]) > 0) { ?>
                                        <?php for($i = count($counter[1]) - 1; $i >= 0; $i--) { ?>
                                            <s v='<?php echo $counter[1][$i]; ?>' />
                                        <?php } ?>
                                    <?php } ?>
                                </dataset>
                           <?php } ?>
                           <?php } ?>
                           </graph>" quality="high" bgcolor="#FFFFFF" name="Line" align="" width="565" height="420" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
                </object>
            </td></tr>
    </table>
    </td>
    </tr>
    </table>
<?php } ?>

