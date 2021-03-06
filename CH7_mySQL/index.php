<?php

    session_start();

    $error = "";    
    
    //If user is already logout, clear the session and Cookies
    if (array_key_exists("logout", $_GET)) {
        
        unset($_SESSION);
        setcookie("id", "", time() - 60*60);
        $_COOKIE["id"] = "";  
        
    } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
        //If the user is already login, redirect
        header("Location: loggedinpage.php");
        
    }

    if (array_key_exists("submit", $_POST)) {
        

        include("connection.php");
        
        
        if (!$_POST['email']) {
            
            $error .= "An email address is required<br>";
            
        } 
        
        if (!$_POST['password']) {
            
            $error .= "A password is required<br>";
            
        } 
        
        if ($error != "") {
            
            $error = "<p>There were error(s) in your form:</p>".$error;
            
        } else {
            
            if ($_POST['signUp'] == '1') {//Sign up
            
                //check the email is already used or not
                $query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

                $result = mysqli_query($link, $query);

                if (mysqli_num_rows($result) > 0) {

                    $error = "That email address is already taken.";

                } else {
                    
                    //Store data entered into database
                    $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";

                    if (!mysqli_query($link, $query)) {

                        $error = "<p>Could not sign you up - please try again later.</p>";

                    } else {
                        
                        //Encode the password
                        $query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";

                        mysqli_query($link, $query);
                        
                        //Set session & cookie
                        $_SESSION['id'] = mysqli_insert_id($link);

                        if ($_POST['stayLoggedIn'] == '1') {

                            setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);

                        } 
                        
                        //Redirect User
                        header("Location: loggedinpage.php");

                    }

                } 
                
            } else {// Login 
                    
                    $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
                
                    $result = mysqli_query($link, $query);
                
                    $row = mysqli_fetch_array($result);
                
                    //check the password
                    if (isset($row)) {
                        
                        $hashedPassword = md5(md5($row['id']).$_POST['password']);
                        
                        if ($hashedPassword == $row['password']) {
                            
                            //check the session & cookie
                            $_SESSION['id'] = $row['id'];
                            
                            if ($_POST['stayLoggedIn'] == '1') {

                                setcookie("id", $row['id'], time() + 60*60*24*365);

                            } 
                            
                            //log user in
                            header("Location: loggedinpage.php");
                                
                        } else {
                            
                            $error = "That email/password combination could not be found.";
                            
                        }
                        
                    } else {
                        
                        $error = "That email/password combination could not be found.";
                        
                    }
                }
        }
        
        if ($error != ""){
            $error = '<div class="alert alert-danger" role="alert">'.$error.'</div>';
        }
    }
?>

<?php include("header.php") ?>
      
    <div class="container" id="homepagecontainer">
      
        <h1>Diary</h1>
        
        <p>A place for you to keep your idea safe and permanent !</p>

        <form method="post" id="signUpForm">
            
            <p>Interesed ? Sign up now!</p>
            
            <div id="error"><?php echo $error; ?></div>
            
            <div class="form-group">

                <input type="email" class="form-control" name="email" placeholder="Your Email">
            
            </div>
            
            <div class="form-group">

                <input type="password" class="form-control" name="password" placeholder="Password">
                
            </div>
            
            <div class="form-check">
                
                <label class="form-check-label">

                    <input  class="form-check-input" type="checkbox" name="stayLoggedIn" value=1>
                    Stay logged in
                    
                </label>
                
            </div>
            
            <div class="form-group">

                <input type="hidden" name="signUp" value="1">

                <input type="submit" class="btn btn-success" name="submit" value="Sign Up!">
            
            </div>
            
            <p class="togglebutton"><a class="toggleForms">Log in</a></p>

        </form>

        <form method="post" id="loginForm">
            
            <p>Use email and password to log in!</p>
            
            <div id="error"><?php echo $error; ?></div>
            
            <div class="form-group">

                <input type="email" class="form-control" name="email" placeholder="Your Email">
            
            </div>
            
            <div class="form-group">

                <input type="password" class="form-control" name="password" placeholder="Password">
            
            </div>
            
            <div class="form-check">
                
                <label class="form-check-label">

                    <input type="checkbox" class="form-check-input" name="stayLoggedIn" value=1>
                    Stay logged in
                    
                </label>
                
            </div>
            
            <div class="form-group">

                <input type="hidden" name="signUp" value="0">

                <input type="submit" class="btn btn-success" name="submit" value="Log In!">
                
            </div>
            
            <p class="togglebutton"><a class="toggleForms">Sign up</a></p>

        </form>
    
    </div>

<?php include("footer.php") ?>











