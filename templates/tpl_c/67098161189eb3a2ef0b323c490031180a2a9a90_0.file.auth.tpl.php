<?php
/* Smarty version 3.1.29, created on 2017-09-24 14:01:58
  from "C:\OpenServer\domains\EDUKIT\templates\tpl\modals\auth.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_59c790a67d0a27_05361873',
  'file_dependency' => 
  array (
    '67098161189eb3a2ef0b323c490031180a2a9a90' => 
    array (
      0 => 'C:\\OpenServer\\domains\\EDUKIT\\templates\\tpl\\modals\\auth.tpl',
      1 => 1506250913,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59c790a67d0a27_05361873 ($_smarty_tpl) {
?>
<div class="ui mini modal" id="signInModal">
	<i class="close icon"></i>
	<div class="header">
		Вход в систему
	</div>
	<div class="content">
		<form name="signInForm" method="POST" action="php/login.php" class="ui form">
			<div class="field">
				<label>E-mail</label>
				<input type="email" name="email">
			</div>
			<div class="field">
				<label>Пароль</label>
				<input type="password" name="password">
			</div>
			<div class="field">
				<input type="submit" name="signInButton" value="Войти" class="ui primary button">
			</div>
		</form>
	</div>
</div><?php }
}