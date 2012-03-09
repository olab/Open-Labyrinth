<div>Users: <?php echo $usercount; ?> registered users <a href="usermanager/addUserView">[Add user]</a></div> 
<div>
    <table border="1">
        <tr>
            <td>ID</td>
            <td>Username</td>
            <td>Email</td>
            <td>Display name</td>
            <td>Role name</td>
            <td>Language</td>
            <td>Edit</td>
            <td>Delete</td>
        </tr>
        <?php foreach($users as $user) { ?>
        <tr>
            <td><?php echo $user->id; ?></td>
            <td><?php echo $user->username; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->displayname; ?></td>
            <td><?php foreach($user->roles->find_all() as $role) echo $role->name."; "; ?></td>
            <td><?php echo $user->language->name; ?></td>
            <td><a href="usermanager/editView/<?php echo $user->id; ?>">Edit</a></td>
            <td><a href="usermanager/delete/<?php echo $user->id; ?>">Delete</a></div>
        </tr>
        <?php }?>
    </table>
    
</div>