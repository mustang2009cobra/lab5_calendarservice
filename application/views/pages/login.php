<?php

?>

<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
	<div class="container">
	  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </a>
	  <a class="brand" href="<?= site_url('/main/index'); ?>">Pushy</a>
	</div>
  </div>
</div>

<div class="container">
    <div id="error" style="display:none;"><?php  if(isset($_GET['error'])) {
        echo $_GET['error'];
    }?></div>
    <div class="row">
        <div class="span2"></div>
        <div id="alertsArea" class="span8">

        </div>
        <div class="span2"></div>
    </div>
	<div class="row">
		<div class="span2"></div>
		<div class="span4">
			<h2>Login</h2>
			<?php echo validation_errors(); ?>
			<?php echo form_open('users/login'); ?>
			<fieldset>
				<input type="text" name="loginUserEmail" placeholder="Email">
				<input type="password" name="loginUserPassword" placeholder="Password"> <br />
				<button id="login" class="btn btn-primary">Login</button>
			</fieldset>
			</form>
		</div>
		<div class="span4">
			<h2>...Or create an account</h2>
			<?php echo validation_errors(); ?>
			<?php echo form_open('users/register'); ?>
				<fieldset>
					<input type="text" name="createUserName" placeholder="Name">
					<input type="text" name="createUserEmail" placeholder="Email Address">
					<input type="text" name="createUserSignalESL" placeholder="Your Signal ESL">
					<input type="password" name="createUserPassword" placeholder="Password"><br />
					<button type="submit" name="createSubmit" class="btn btn-primary">Create Account</button>
				</fieldset>
			</form>
		</div>
		<div class="span2"></div>
	</div>

</div> <!-- /container -->	

<script type="text/javascript">
	$(document).ready(function(){
		var error = $("#error").html();
        if(error == "baduser"){
            var errorMsg = "<div class='alert alert-error fade in' href='#'>"
                            + "<button type='button' class='close' data-dismiss='alert'>×</button>"
                            + "Invalid User Credentials"
                            + "</div>";
            $("#alertsArea").html(errorMsg);
        }

	});
</script>