<?php

class user {

    public  $error;
    public $errorMessage;
    public $role;
    public $name; 
    public $email;

    public function __construct($conn){
        $this->conn = $conn;

    }

    private function CleanUserInput($exdata){

        $exdata = trim($exdata);
        $exdata = stripslashes($exdata);
        $exdata = htmlspecialchars($exdata);
        return $exdata;

    }

    public function CheckUserRegisterInput(){
        $error=0;


        if(isset($_POST['register'])){
            $CleanUsernameInput = $this->CleanUserInput($_POST['username']);
            $CleanUseremailInput = $this->CleanUserInput($_POST['email']);

                $stmt_CheckIfUserExists = $this->conn->prepare("SELECT * FROM table_user WHERE u_username = :uname OR u_email = :e_mail");
                $stmt_CheckIfUserExists->bindValue(":uname", $CleanUsernameInput, PDO::PARAM_STR);
                $stmt_CheckIfUserExists->bindValue(":e_mail", $CleanUseremailInput, PDO::PARAM_STR);
                $stmt_CheckIfUserExists->execute();

                $userNameMatch = $stmt_CheckIfUserExists->fetch();


            if(!empty($userNameMatch)){

                if($userNameMatch['username'] == $CleanUsernameInput){
                    $this->errorMessage .= " | Username is already taken";
                        $error=1;
                    
                }

                if($userNameMatch['email'] == $CleanUseremailInput){
                    $this->errorMessage .= " | email is already taken";
                        $error=1;
                    
                }
            }
        }
        
        if(isset($_POST['updatinginfo']) && $_POST['password'] == ""){

        }   
        else{
            if($_POST['password'] != $_POST['conpass']){
                $this->errorMessage .= " | Password does not match";
                $error=1;
                
            }   
            
            if(strlen($_POST['password']) < 8){
                $this->errorMessage .= " | Password must be 8 or more characters";
                $error=1;
            }  
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage .= " | Email is not vaild";
            $error=1;
          }
        
        if($error !=0){
            return $this->errorMessage;
        }

        else {
            return "success";
        }
    }


    public function register(){
        $CleanUsernameInput = $this->CleanUserInput($_POST['username']);
        $CleanUserfnameInput = $this->CleanUserInput($_POST['firstname']);
        $CleanUserlnameInput = $this->CleanUserInput($_POST['lastname']);
        $CleanUseremailInput = $this->CleanUserInput($_POST['email']);
        $Encrypted = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $insertinginfo =$this->conn->prepare("INSERT INTO table_user(u_username ,u_firstname ,u_lastname ,u_email ,u_password ,u_role ,u_status)
        VALUES(:frname,:fname ,:lname ,:useremail, :passcon, 1 , 1)");
    
        $insertinginfo->bindParam(":frname", $CleanUsernameInput, PDO::PARAM_STR);
        $insertinginfo->bindParam(":fname", $CleanUserfnameInput, PDO::PARAM_STR);
        $insertinginfo->bindParam(":lname", $CleanUserlnameInput, PDO::PARAM_STR);
        $insertinginfo->bindParam(":useremail", $CleanUseremailInput, PDO::PARAM_STR);
        $insertinginfo->bindParam(":passcon", $Encrypted, PDO::PARAM_STR);
        $check = $insertinginfo->execute();
        
        if($check){
            return "success";
        }
        else {
        return "somthing went wrong";
        }
    }

    public function redirect($url){
        
        header("Location: ".$url);
        exit();

    }

    public function login(){
        
        $CheckUsernameInput = $this->CleanUserInput($_POST['username_email']);

        $CheckInfo = $this->conn->prepare("SELECT * FROM table_user WHERE u_username = :u_sername OR u_email = :e_mail");
        $CheckInfo->bindValue(":u_sername", $CheckUsernameInput, PDO::PARAM_STR);
        $CheckInfo->bindValue(":e_mail", $CheckUsernameInput, PDO::PARAM_STR);
        $CheckInfo->execute();

        $checkuserinput = $CheckInfo->fetch();

        if(!$checkuserinput){

            $this->errorMessage = "user does not exists.";
            return $this->errorMessage;

        }

        $passcheck = password_verify($_POST['password'], $checkuserinput['u_password']);
   
        if($passcheck == true) {
            $_SESSION['name'] = $checkuserinput['u_username'];
            $_SESSION['role'] = $checkuserinput['u_role'];
            $_SESSION['user_id'] = $checkuserinput['u_ID'];
            
           return "success";
        } 
        else {
            $this->errorMessage = "invalid pass";
           return $this->errorMessage;
        }
    }

    public function checkloginstatus(){
        if(isset($_SESSION['user_id'])){
            return true;
        }
        else {
            return false;
        }
    }

    public function Logout(){
        session_unset();
        session_destroy();
        return true;
    }

    public function checkuserole($reg){

        $CheckroleInfo = $this->conn->prepare("SELECT * FROM table_roles WHERE r_ID = :u_role");
        $CheckroleInfo->bindValue(":u_role", $_SESSION['role'], PDO::PARAM_INT);
        $CheckroleInfo->execute();

        $checkinguserrole = $CheckroleInfo->fetch();

        if($checkinguserrole['r_level'] >= $reg){
            return true;
        }
        else{
            return false;
        }

    }

    public function edituserinfo($uid){
        $CleanUseremailInput = $this->CleanUserInput($_POST['email']);

            if(isset($_POST['password'])&& $_POST['password'] != ""){
                $Encrypted = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $edituserinfo = $this->conn->prepare("UPDATE table_user SET u_email = :uemail, u_password = :pass WHERE u_ID = :u_id ");

                $edituserinfo->bindParam(":uemail", $CleanUseremailInput, PDO::PARAM_STR);
                $edituserinfo->bindParam(":pass", $Encrypted, PDO::PARAM_STR);
                $edituserinfo->bindParam(":u_id", $uid, PDO::PARAM_INT);
            }

            else {
                $edituserinfo = $this->conn->prepare("UPDATE table_user SET u_email = :uemail WHERE u_ID = :u_id ");

                $edituserinfo->bindParam(":uemail", $CleanUseremailInput, PDO::PARAM_STR);
                $edituserinfo->bindParam(":u_id", $uid, PDO::PARAM_INT);

            }
      

        if($edituserinfo->execute()){
            return true;
        }

        
    }

    public function getuserinfo($uid){
        $getuserinfo = $this->conn->prepare("SELECT * FROM table_user WHERE u_ID = :u_id");

        $getuserinfo->bindParam("u_id", $uid, PDO::PARAM_STR);
        $getuserinfo->execute();

        $gettinguserinfo = $getuserinfo->fetch();
        return $gettinguserinfo;
    }

    public function searchuser(){

        $CLEAN = $this->CleanUserInput($_POST['search_user']);
        $CLEAN = "%".$CLEAN."%";
        $searchusers =  $this->conn->prepare("SELECT * FROM table_user WHERE u_username LIKE :u_name");
        $searchusers->bindParam(":u_name", $CLEAN, PDO::PARAM_STR);
        $searchusers->execute();
        return $searchusers;
        
        

    }

    public function updateuserstatus($uid){

        $updateuserstatus = $this->conn->prepare("UPDATE table_user SET u_status = :sstatusid WHERE u_ID = :u_id");

        $updateuserstatus->bindParam(":sstatusid", $_POST['editstatus'], PDO::PARAM_INT);
        $updateuserstatus->bindParam(":u_id", $uid, PDO::PARAM_INT);
        if($updateuserstatus->execute()){
            return "success";
        }
        else{
        $this->errorMessage = "something went wrong";
        return $this->errorMessage;
        }
    }


    public function updateuserrole($uid){

        $updateuserrole = $this->conn->prepare("UPDATE table_user SET u_role = :role_id WHERE u_ID = :u_id");

        $updateuserrole->bindParam(":role_id", $_POST['editrole'], PDO::PARAM_INT);
        $updateuserrole->bindParam(":u_id", $uid, PDO::PARAM_INT);
        if($updateuserrole->execute()){
            return "success";
        }
        else{
            $this->errorMessage = "something went wrong";
            return $this->errorMessage;
        }
        

        
    }

    public function Deleteaccount($uid){
        $deleteaccount = $this->conn->prepare("DELETE FROM table_user WHERE u_ID = :u_id");
        $deleteaccount->bindParam(":u_id", $uid, PDO::PARAM_INT);
        if($deleteaccount->execute()){
            return "success";
        }
        else{
            $this->errorMessage = "something went wrong";
            return $this->errorMessage;
        }
    }

}
?>