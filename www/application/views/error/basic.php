<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
?>

<?php
// Unique error identifier
$error_id = uniqid('error');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo __('Error') ?> > <?php echo __('OpenLabyrinth'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="<?php echo URL::base(); ?>css/error.css" />

    <link rel="shortcut icon" href="<?php echo URL::base(); ?>images/ico/favicon.ico" />

    <style type="text/css">
        #kohana_error { background: #ddd; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }
        #kohana_error h1,
        #kohana_error h2 { margin: 0; padding: 1em; font-size: 1em; font-weight: normal; background: #911; color: #fff; }
        #kohana_error h1 a,
        #kohana_error h2 a { color: #fff; }
        #kohana_error h2 { background: #222; }
        #kohana_error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
        #kohana_error p { margin: 0; padding: 0.2em 0; }
        #kohana_error a { color: #1b323b; }
        #kohana_error pre { overflow: auto; white-space: pre-wrap; }
        #kohana_error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
        #kohana_error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }
        #kohana_error div.content { padding: 0.4em 1em 1em; overflow: hidden; }
        #kohana_error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }
        #kohana_error pre.source span.line { display: block; }
        #kohana_error pre.source span.highlight { background: #f0eb96; }
        #kohana_error pre.source span.line span.number { color: #666; }
        #kohana_error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }
        #kohana_error ol.trace li { margin: 0; padding: 0; }
        .js .collapsed { display: none; }
    </style>

    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/cookie-info.js"></script>
</head>
<body>
        <?php if(isset($center)) echo $center;  ?>
    <script type="text/javascript">
        document.documentElement.className = document.documentElement.className + ' js';
        function koggle(elem)
        {
            elem = document.getElementById(elem);

            if (elem.style && elem.style['display'])
            // Only works with the "style" attr
                var disp = elem.style['display'];
            else if (elem.currentStyle)
            // For MSIE, naturally
                var disp = elem.currentStyle['display'];
            else if (window.getComputedStyle)
            // For most other browsers
                var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');

            // Toggle the state of the "display" style
            elem.style.display = disp == 'block' ? 'none' : 'block';
            return false;
        }
    </script>
    <div id="kohana_error" style="display: none">
        <h1><span class="type"><?php if(isset($type)) echo $type; ?> [ <?php if(isset($code)) echo $code; ?> ]:</span> <span class="message"><?php if(isset($message)) echo html::chars($message) ?></span></h1>
        <div id="<?php echo $error_id ?>" class="content">
            <p><span class="file"><?php echo Debug::path($file) ?> [ <?php echo $line; ?> ]</span></p>
            <?php echo Debug::source($file, $line); ?>
            <ol class="trace">

                <?php foreach (Debug::trace($trace) as $i => $step): ?>
                    <li>
                        <p>
                        <span class="file">
                            <?php if ($step['file']): $source_id = $error_id.'source'.$i; ?>
                                <a href="#<?php echo $source_id ?>" onclick="return koggle('<?php echo $source_id ?>')"><?php echo Debug::path($step['file']) ?> [ <?php echo $step['line'] ?> ]</a>
                            <?php else: ?>
                                {<?php echo __('PHP internal call') ?>}
                            <?php endif ?>
                        </span>
                            &raquo;
                            <?php echo $step['function'] ?>(<?php if ($step['args']): $args_id = $error_id.'args'.$i; ?><a href="#<?php echo $args_id ?>" onclick="return koggle('<?php echo $args_id ?>')"><?php echo __('arguments') ?></a><?php endif ?>)
                        </p>
                        <?php if (isset($args_id)): ?>
                            <div id="<?php echo $args_id ?>" class="collapsed">
                                <table cellspacing="0">
                                    <?php foreach ($step['args'] as $name => $arg): ?>
                                        <tr>
                                            <td><code><?php echo $name ?></code></td>
                                            <td><pre><?php echo Debug::dump($arg) ?></pre></td>
                                        </tr>
                                    <?php endforeach ?>
                                </table>
                            </div>
                        <?php endif ?>
                        <?php if (isset($source_id)): ?>
                            <pre id="<?php echo $source_id ?>" class="source collapsed"><code><?php echo $step['source'] ?></code></pre>
                        <?php endif ?>
                    </li>
                    <?php unset($args_id, $source_id); ?>
                <?php endforeach ?>
            </ol>
        </div>

        <h2><a href="#<?php echo $env_id = $error_id.'environment' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Environment') ?></a></h2>
        <div id="<?php echo $env_id ?>" class="content collapsed">
            <?php $included = get_included_files() ?>
            <h3><a href="#<?php echo $env_id = $error_id.'environment_included' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Included files') ?></a> (<?php echo count($included) ?>)</h3>
            <div id="<?php echo $env_id ?>" class="collapsed">
                <table cellspacing="0">
                    <?php foreach ($included as $file): ?>
                        <tr>
                            <td><code><?php echo Debug::path($file) ?></code></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
            <?php $included = get_loaded_extensions() ?>
            <h3><a href="#<?php echo $env_id = $error_id.'environment_loaded' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Loaded extensions') ?></a> (<?php echo count($included) ?>)</h3>
            <div id="<?php echo $env_id ?>" class="collapsed">
                <table cellspacing="0">
                    <?php foreach ($included as $file): ?>
                        <tr>
                            <td><code><?php echo Debug::path($file) ?></code></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
            <?php foreach (array('_SESSION', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER') as $var): ?>
                <?php if (empty($GLOBALS[$var]) OR ! is_array($GLOBALS[$var])) continue ?>
                <h3><a href="#<?php echo $env_id = $error_id.'environment'.strtolower($var) ?>" onclick="return koggle('<?php echo $env_id ?>')">$<?php echo $var ?></a></h3>
                <div id="<?php echo $env_id ?>" class="collapsed">
                    <table cellspacing="0">
                        <?php foreach ($GLOBALS[$var] as $key => $value): ?>
                            <tr>
                                <td><code><?php echo HTML::chars($key) ?></code></td>
                                <td><pre><?php echo Debug::dump($value) ?></pre></td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var isCtrl = false;
            $(document).keyup(function (e) {
                if(e.which == 17) isCtrl=false;
            }).keydown(function (e) {
                if(e.which == 17) isCtrl=true;
                if( e.which == 81 && isCtrl == true) {
                    if ($('#openError').css('display') == 'none'){
                        $('#kohana_error').hide();
                        $('#openError').show();
                    } else{
                        $('#openError').hide();
                        $('#kohana_error').show();
                    }
                    return false;
                }
            });
        });
    </script>
</body>
</html>