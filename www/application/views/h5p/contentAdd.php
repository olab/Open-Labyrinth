<?php
?>
<h2>Add New</h2>
<form method="post" enctype="multipart/form-data" id="h5p-content-form">
    <div id="post-body-content">
        <div id="titlediv">
            <label class="" id="title-prompt-text" for="title">Enter title here</label>
            <input id="title" type="text" name="title" id="title" value=""/>
        </div>
        <div class="h5p-upload">
            <input type="file" name="h5p_file" id="h5p-file"/>
            <div class="h5p-disable-file-check">
                <label><input type="checkbox" name="h5p_disable_file_check" id="h5p-disable-file-check"/> Disable file
                    extension check</label>
                <div class="h5p-warning">Warning! This may have security implications as it allows for uploading php
                    files. That in turn could make it possible for attackers to execute malicious code on your site.
                    Please make sure you know exactly what you're uploading.
                </div>
            </div>
        </div>
        <div class="h5p-create">
            <div class="h5p-editor">Waiting for javascript...</div>
        </div>
    </div>
    <div class="postbox h5p-sidebar">
        <h2>Actions</h2>
        <div id="minor-publishing">
            <label><input type="radio" name="action" value="upload"/>Upload</label>
            <label><input type="radio" name="action" value="create"/>Create</label>
            <input type="hidden" name="library" value="0"/>
            <input type="hidden" name="parameters" value="{}"/>
            <input type="hidden" id="yes_sir_will_do" name="yes_sir_will_do" value="6ef6b00526"/><input type="hidden"
                                                                                                        name="_wp_http_referer"
                                                                                                        value="/wp-admin/admin.php?page=h5p_new"/>
        </div>
        <div id="major-publishing-actions" class="submitbox">
            <input type="submit" name="submit" value="Create" class="button button-primary button-large"/>
        </div>
    </div>
    <div class="postbox h5p-sidebar">
        <div role="button" class="h5p-toggle" tabindex="0" aria-expanded="true" aria-label="Toggle panel"></div>
        <h2>Display Options</h2>
        <div class="h5p-action-bar-settings h5p-panel">
            <label>
                <input name="frame" type="checkbox" value="true" checked="checked"/>
                Display action bar and frame </label>
            <label>
                <input name="download" type="checkbox" value="true" checked="checked"/>
                Download button </label>
            <label>
                <input name="embed" type="checkbox" value="true" checked="checked"/>
                Embed button </label>
            <label>
                <input name="copyright" type="checkbox" value="true" checked="checked"/>
                Copyright button </label>
        </div>
    </div>
    <div class="postbox h5p-sidebar">
        <div role="button" class="h5p-toggle" tabindex="0" aria-expanded="true" aria-label="Toggle panel"></div>
        <h2>Tags</h2>
        <div class="h5p-panel">
            <textarea rows="2" name="tags" class="h5p-tags"></textarea>
            <p class="howto">Separate tags with commas</p>
        </div>
    </div>
</form>
