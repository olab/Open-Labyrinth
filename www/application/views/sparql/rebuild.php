
 <div>
     <form class="form-horizontal" action="<?php echo URL::base() . 'sparql/rebuild/go'; ?>">
         <fieldset class="fieldset">
             <legend>Rebuild SPARQL Endpoint</legend>
             <div>Here you can rebuild the SPARQL endpoint index, in order to provide the latest information</div>

             <div class="control-group">
                 <label for="external" class="control-label">Import <a href="<?php echo URL::base() . 'vocabulary/manager'; ?>">External Vocabularies</a></label>

                 <div class="controls">

                     <input id="external" type="checkbox" name="external">
                 </div>
             </div>
         </fieldset>
<div class="form-actions">
    <div class="pull-right">
         <input class="btn btn-large btn-primary" type="submit" value="Rebuild"/></div>
</div>
     </form>
     
 </div>