
 <div>
     <form action="<?php echo URL::base() . 'sparql/rebuild/go'; ?>">
         <div>Here you can rebuild the SPARQL endpoint index, in order to provide the latest information</div>
         <input type="checkbox" name="external">Import <a href="<?php echo URL::base() . 'vocabularymanager'; ?>">External Vocabularies</a></input>
         <input type="submit" value="Rebuild"/>
     </form>
     
 </div>