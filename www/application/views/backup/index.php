<?php
if(!empty($templateData['result'])) {
    $result = $templateData['result'];
    if(is_int($result)){
        switch($result){
            case 1:
                $message = '<div class="alert alert-success">Backup saved successful!</div>';
                break;
            case 2:
                $message = '<div class="alert alert-danger">Unexpected error.</div>';
                break;
            case 3:
                $message = '<div class="alert alert-danger">Returned empty result.</div>';
                break;
        }
    }else{
        $sql = $result;
    }
}
if(isset($message)) {
    echo $message;
}
?>
<form action="" method="post" class="form-inline">
    <input type="hidden" name="doBackup" value="1">
    <label>
        Please, choose save type:
        <select name="save_type">
            <?php foreach($templateData['save_types'] as $key=>$type){ ?>
            <option value="<?php echo $key ?>"><?php echo $type ?></option>
            <?php } ?>
        </select>
    </label>
    <button type="submit" class="btn btn-success">Backup now!</button>
</form>
<?php
if(isset($sql)){
    echo '<textarea wrap="hard" style="width:100%;height:500px;">'.$sql.'</textarea>';
}
?>