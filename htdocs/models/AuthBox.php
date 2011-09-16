<?php
class Model_AuthBox extends Model_Abstract {
	
	function printBox() {
		$variabila = new Model_Auth();
		$date = $variabila->getUserData();
		$tipuri = array(
			'U' => 'User',
			'P' => 'Content provider',
			'A'	=> 'Administrator'
		);
		
		if (! $variabila->isLoggedIn() ): ?>
			<form method="post" action="/auth/login">
			<table cellspacing="0">
				<tr><td><input type="text" id="user" name="user" placeholder="username"></td>
					<td><input type="password" id="pass" name="pass" placeholder="password"></td>
					<td><input type="submit" value="Login" class="submit" /></td>
				</tr>
				<tr class="small"><td><a href="/auth/requestNewPass">Lost password?</a></td><td colspan="2"><a href="/auth/register">Want an account? Register!</a></td></tr>
			</table>
			</form>
		<?php else: ?>
			<div class="authenticated">
				<p>Bine ati venit, <b><?php echo $date['NAME']; ?></b> - <em><?php echo $tipuri[ $date['USER_TYPE'] ]; ?></em></p>
				<center>
					<button class="edit link" href="/user/account">Optiuni cont</button> 
					<button class="back link" href="/auth/logout">Delogare</button>
				</center>
			</div>
		<?php endif;
	}
}