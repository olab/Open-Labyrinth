<?php

function getRandomColor() {
    mt_srand((double) microtime() * 1000000);
    $c = '';
    while (strlen($c) < 6) {
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

?>
<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Aggregate report for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>

            </td>
        </tr>
    </table>
<?php } ?>

