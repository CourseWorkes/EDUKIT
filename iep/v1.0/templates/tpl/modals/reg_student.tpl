<div class="ui modal" id="regStudentModal">
  <div class="header">
    Регистрация студента
  </div>
  <div class="content">
    <div id="message" class="ui warning message"></div>
    {if $groups != NULL}
      <form name="registrationForm" action="php/registration.php" method="POST"  class="ui form">
        <div class="ui stackable grid">
          <div class="row">
            <div class="four wide column">
              <div class="field">
                <label>Фамилия</label>
                <input type="text" name="second_name" required>
              </div>
            </div>
            <div class="four wide column" required>
              <div class="field">
                <label>Имя</label>
                <input type="text" name="first_name" required>
              </div>
            </div>
            <div class="four wide column">
              <div class="field">
                <label>Отчество</label>
                <input type="text" name="patronymic">
              </div>
            </div>
            <div class="four wide column">
              <div class="field">
                <label>Группа</label>
                <select name="grp" required>
                    {foreach from=$groups item=group}
                      <option value="{$group->getGroupID()}">{$group->getNumberGroup()}</option>
                    {/foreach}
                  </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="eight wide column">
              <div class="field">
                <label>E-mail</label>
                <input type="email" name="email" required>
              </div>
              <div class="field">
                <label>Пароль</label>
                <input type="password" name="passwd" id="passwd" required>
              </div>
              <div class="field">
                <label>Повторите пароль</label>
                <input type="password" name="retry_password" id="retry_passwd" required>
              </div>
            </div>
            <div class="eight wide column">
              <div class="field">
                <label>Адрес проживания</label>
                <input type="text" name="home_address" required>
              </div>
              <div class="field">
                <label>Телефон</label>
                <input type="tel" name="cell_phone_child" required>
              </div>
              <div class="three fields">
                <div class="field">
                    <label>&nbsp;</label>
                    <input type="reset" class="ui orange button">
                </div>
                <div class="field">
                  <label>&nbsp;</label>
                  <a href="regparent.php" class="ui green button">Я родитель</a>
                </div>
                <div class="field">
                  <label>&nbsp;</label>
                  <input type="submit" name="registrationStudent" value="Готово" id="sendData" class="ui primary button">
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    {else}
      <h3>Регистрация закрыта</h3>
    {/if}
  </div>
</div>
<script type="text/javascript" src="js/checkStudentRegForm.js"></script>