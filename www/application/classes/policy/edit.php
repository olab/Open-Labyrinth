<?php
class Policy_Edit extends Policy {

    public function execute(Model_Leap_User $user, array $array = NULL)
    {
        $mapId = Arr::get($array, 'mapId', FALSE);

        if ( ! $mapId) return FALSE;

        $map         = DB_ORM::model('map', array($mapId));
        $authorRight = $user->type_id == 2
            ? (bool) DB_ORM::select('Map_User')->where('user_id', '=', $user->id)->where('map_id', '=', $mapId)->query()->as_array()
            : false;
        $editRight   = ($user->type_id == 4 OR $user->id == $map->author_id OR $authorRight);

        if ( ! $editRight) Request::initial()->redirect(URL::base());

        return TRUE;
    }
}