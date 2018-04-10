<?php   include ("includes/header.php");?>
<?php   include ("includes/nav.php");?>
<?php /*  include ("functions/db.php");*/?>



	



	<div class="jumbotron">
		<h1 class="text-center"><?php display_message();?></h1>
	</div>

<?php $sql="select * from users";
        $result = query($sql);
        confirm($result);

       /* $row = fetch_array($result);*/
        $row = fetch_array($result);
?>

<?php  include ("includes/footer.php");?>
	
