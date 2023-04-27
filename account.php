<?php 
include "header.php";

if(!$user->checkloginstatus()){
    $user->redirect('home.php');

}

if($user->checkuserole(20) && isset($_GET['usertoedit'])){
    $usertoedit = $_GET['usertoedit'];
}

else{
    $usertoedit = $_SESSION['user_id'];
}

if(isset($_POST['updatinginfo'])){

    $checkreturn = $user->CheckUserRegisterInput();
    //If all checks are passed 
    if($checkreturn == "success"){
        if($user->edituserinfo($usertoedit)){
            $feedback = "user info updated successfully";
        }
    }
    else {
        $feedback =$checkreturn;
    }
}

if(isset($_POST['return'])){
    $user->redirect('home.php');
}



if(isset($_POST['updatinguserinfo'])){
    echo $yorole;
    echo $yostatus;
    if($_POST['editrole'] != 0)
    $yorole = $user->updateuserrole($usertoedit);
    if($yorole == "success"){
        $feedback .= "user role updated successfullyyyy";

    }
    else {
        $feedback = $yorole;
    }
    if($_POST['editstatus'] != 0)
    $yostatus = $user->updateuserstatus($usertoedit);
    if($yostatus == "success"){
        $feedback .= " user status updated successfully";

    }
    else {
        $feedback = $yostatus;
    }
}


$userinfo = $user->getuserinfo($usertoedit);

$roleinfo = $conn->query("SELECT * FROM table_roles");
$statusinfo = $conn->query("SELECT * FROM table_status");


?>

<div class="feedback-section">
<?php
if(isset($feedback)){
    echo $feedback;
}
?>
</div>



<div class="account">
    <form method="post" action="">
        <h2>Change Account info</h2>
        <label for="username">Username</label><br>
        <input type="text" name="username" value="<?php echo $userinfo['u_username'];?>" disabled><br>

        <label for="email">E-mail</label><br>
        <input type="text" name="email" value="<?php echo $userinfo['u_email'];?>" ><br>

        <label for="password">Enter Old Password</label><br>
        <input type="text" name="oldpassword" placeholder="Enter Old Password"><br>

        <label for="newpass">New Password</label><br>
        <input type="text" name="password" placeholder="New Password"><br>

        <label for="conpass">Confirm New Password</label><br>
        <input type="text" name="conpass" placeholder="Confirm New Password"><br>

        <input type="submit" class="m-2" id="button" name="updatinginfo" value="Update Info">

        <input type="submit" class="m-2" id="button" name="return" value="Return"><br>
    </form>

    <?php 
        if($user->checkuserole(20)){
            
        
    ?>
    <form method="post" action="">

        <label for="editrole">Edit Role:</label><br>
                
            <select name="editrole" id="editrole">
            <option value="0">Choose Role</option>
                <?php 
                foreach($roleinfo as $row){
                    echo "<option value='{$row['r_ID']}'>{$row['r_name']}</option><br> ";
                }
                ?>
            </select>
            <br />
        <label for="editstatus">Edit Status:</label><br>

            <select name="editstatus" id="editstatus">
                <option value="0">Choose Status</option>
            <?php 
                foreach($statusinfo as $row){
                    echo "<option value='{$row['s_id']}'>{$row['s_name']}</option><br> ";
                }
                ?>
            </select>

        <br><input type="submit" class="m-1" id="button" name="updatinguserinfo" value="Update Info">
    </form>
    <?php 
    }
    ?>
    <form method="post" action="confirm_delete.php?usertoedit=<?php echo $usertoedit; ?>">
        <input type="submit" class="m-1" id="button" name="delete_user" value="Delete Account">
    </form>
</div>

<?php
//include "footer.php";
?>