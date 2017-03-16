<div class="row">
	<div class="col-md-12">
		<div class="panel-group" id="u">
			<div class="panel panel-warning">
				<div class="panel-heading">
					<h4 class="panel-title"><a data-toggle="collapse" data-parent="#u" href="#u_teachers">Преподаватели</a></h4>
				</div>
				<div id="u_teachers" class="panel-collapse collapse">
					<div class="panel-body">
						{if $teachers != NULL}
							<div class="row">		
								<div class="col-md-12">
									<table class="table table-hover info_table">
										<tr>
											<td>Фамилия</td>
											<td>Имя</td>
											<td>Отчество</td>
											<td>Email</td>
											<td>Предметы</td>
										</tr>
										{foreach from=$teachers item=teacher}
											<tr>
												<td>{$teacher->getSn()}</td>
												<td>{$teacher->getFn()}</td>
												<td>{$teacher->getPt()}</td>
												<td>{$teacher->getEmail()}</td>
                        <td>{$teacher->getStrSubjects()}</td>
											</tr>
										{/foreach}
									</table>
								</div>
							</div>
						{else}
							<h1 align="center">Преподавателей нету</h1>
						{/if}
					</div>
				</div>
			</div>
			<div class="panel panel-success">
				<div class="panel-heading">
          <h4 class="panel-title"><a data-toggle="collapse" data-parent="#u" href="#u_students">Студенты</a></h4>
				</div>
				<div id="u_students" class="panel-collapse collapse">
					<div class="panel-body">
					{if $groups_students != NULL}
            <div class="panel-group" id="students_groups">
						{foreach from=$groups_students item=it}
							{if $it != NULL}
                <div class="panel panel-default">
                  <div class="panel-heading">
                      <h4 class="panel-title">
                          <a data-toggle="collapse" data-parent="#students_groups" href=#{$it[0]['grp']}>{$it[0]['grp']}</a>
                      </h4>
                  </div>
                  <div id={$it[0]['grp']} class="panel-collapse collapse">
                    <div class="panel-body">
                      <table class="table table-hover info_table">
                          <tr>
                              <td>Фамилия</td>
                              <td>Имя</td>
                              <td>Отчество</td>
                              <td>Email</td>
                              <td>Телефон</td>
                              <td></td>
                          </tr>
                          {foreach from=$it item=this}
                              <tr>
                                  <td>{$this['second_name']}</td>
                                  <td>{$this['first_name']}</td>
                                  <td>{$this['patronymic']}</td>
                                  <td>{$this['email']}</td>
                                  <td>{$this['cell_phone']}</td>
                              </tr>
                          {/foreach}
                      </table>
                    </div>
                  </div>
                </div>
							{/if}
						{/foreach}
            </div>
					{else}
						<h1 align="center">Студентов нету</h1>
					{/if}
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#u" href="#u_elder">Старосты</a>
					</h4>
				</div>
				<div id="u_elder" class="panel-collapse collapse">
					<div class="panel-body">
						{if $elders != NULL}
						<!-- HERE ELDERES -->
						{else}
							<h1 align="center">Старосты не назначены</h1>
						{/if}
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#u" href="#u_parents">Родители</a>
					</h4>
				</div>
				<div id="u_parents" class="panel-collapse collapse">
					<div class="panel-body">
						{if $parents != NULL}
							<table class="table table-hover info_table">
								<tr>
									<td>Фамилия</td>
									<td>Имя</td>
									<td>Отчество</td>
									<td>Возраст</td>
									<td>Контактная информация</td>
									<td>Место работы</td>
									<td>Должность</td>
								</tr>
								{foreach from=$parents item=parent}
									<tr>
										<td>{$parent->getSn()}</td>
										<td>{$parent->getFn()}</td>
										<td>{$parent->getPt()}</td>
										<td>{$parent->getAge()}</td>
										<td>
											<table class="table table-border">
												<tr>
													<td>Email</td>
													<td><a href="mailto:{$parent->getEmail()}">{$parent->getEmail()}</a></td>
												</tr>
												<tr>
													<td>Сотовый телефон</td>
													<td>{$parent->getCellPhone()}</td>
												</tr>
												<tr>
													<td>Домашний телефон</td>
													<td>{$parent->getHomePhone()}</td>
												</tr>
												</tr>
											</table>
										</td>
										<td>{$parent->getWorkPlace()}</td>
										<td>{$parent->getPost()}</td>
									</tr>
								{/foreach}
							</table>
						{else}
							<h1 align="center">Родители незарегистрированы</h1>	
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>		
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">
        <form name="add_teacher" method="POST">
          <fieldset>
            <legend>Добавление преподавателя</legend>
            <div class="col-md-6">
              <div class="form-group">
                <label>Фамилия:</label>
                <input type="text" name="second_name" class="form-control" >
              </div>
              <div class="form-group">
                <label>Имя:</label>
                <input type="text" name="first_name" class="form-control" >
              </div>
              <div class="form-group">
                <label>Отчество:</label>
                <input type="text" name="patronymic" class="form-control">
              </div>
              <div class="form-group">
                <label>E-mail:</label>
                <input type="email" name="email" class="form-control" >
              </div>
              <div id="passwordDiv" class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" class="form-control" >
              </div>
              <div id="retryPasswordDiv" class="form-group">
                <label>Повторите пароль:</label>
                <input type="password" name="retry_password" class="form-control" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Информация об преподавателе</label>
                <textarea class="form-control" name="info"></textarea>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-8">
                    <label>Предметы</label>
                    <select id="sbuss" class="form-control">
                      {foreach from=$subjects item=subject}
                        <option value="{$subject->getID()}">{$subject}</option>
                      {/foreach}
                    </select>
                  </div>
                  <div class="col-md-4">
                    <button id="add_subject" class="btn btn-primary" type="button">Добавить</button>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-12">
                    <table id="subject_table" class="table table-hover">
                      <tr>
                        <td style="text-align: center;" colspan="2">Предмет</td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <input name="addTeacherButton" type="submit" class="btn btn-success form-control" value="Добавить">
              </div>
            </div>
          </fieldset>
        </form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
			
	var subjects = new Array();
	var countChilds = 0;
	
	$("#add_subject").click(function(){
		var sb = document.getElementById("sbuss");
		var subject = sb.selectedIndex != -1 ? sb.options[sb.selectedIndex].value : "None";;
		var subject_text = sb.selectedIndex != -1 ? sb.options[sb.selectedIndex].text : "None";
		
		if(subject != "")
		{
			if($.inArray(subject, subjects) > -1) alert("Вы уже добавили этот предмет");
			else
			{
				subjects.push(subject);
				$("#subject_table").append('<tr id="' + countChilds + '"><td>' + subject_text + '<input name="subjects[]" type="hidden" value="' + subject + '"></td></tr>');
				countChilds++;
			}
		}
		else alert("Выберете предмет");
	});
	
	$("#remov_childs").click(function(){
		$("#subject_table").empty().css("class", "table table-hover").append("<tbody><tr><td style='text-align: center;' colspan='2'>Чадо</td></tr></tbody>");
		subjects = new Array();
		countChilds = 0;
	});
	
	function checkRegParentForm(form)
	{
		if(subjects.toString() == "")
		{
			alert("Вы не выбрали предмет");
			return false;
		}
		else return true;
	}
	
</script>