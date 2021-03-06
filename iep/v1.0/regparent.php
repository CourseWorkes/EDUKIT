<?php
	require_once "start.php";
	require_once "engine/cform.php";
  
  use IEP\Structures\User;
  use IEP\Structures\Parent_;
	
	$childrens = $UM->getAllStudents();
  
  $studentsByGroup = array();
  for ($i = 0; $i < count($childrens); $i++) {
    $studentsByGroup[$childrens[$i]->getGroup()->getNumberGroup()][] = $childrens[$i];
  }
	
	$CT->assign("studentsByGroup", $studentsByGroup);
	$CT->Show("reg_parent.tpl");
	
	if (!empty($_POST['regParentButton'])) {
    
    if (!empty($_POST['isAgree']) && !empty($_POST['isMyChildren'])) {
      $reg_parent_data = CForm::GetData(array(
        "sn",
        "fn",
        "pt",
        "email",
        "password",
        "home_phone",
        "cell_phone",
        "education",
        "age",
        "work_place",
        "post"
      ));
      $reg_parent_data['password'] = md5($reg_parent_data['password']);
      $reg_parent_data['childs'] = $_POST['childs'];
      
      $parent = new Parent_(
        new User(
          $reg_parent_data['sn'],
          $reg_parent_data['fn'],
          $reg_parent_data['pt'],
          $reg_parent_data['email'],
          $reg_parent_data['password'],
          4
        ),
        (int)$reg_parent_data['age'],
        $reg_parent_data['education'],
        $reg_parent_data['work_place'],
        $reg_parent_data['post'],
        $reg_parent_data['home_phone'],
        $reg_parent_data['cell_phone']
      );
      
      if (!empty($reg_parent_data['childs'])) {
        if ((count($reg_parent_data['childs']) <= 10) && count($reg_parent_data['childs']) > 0) {
          
          $parent->setChilds($reg_parent_data['childs']);
          
          if($UM->add($parent)) {
            CTools::Message("Регистрация прошла успешно");
          } 
          else {
            CTools::Message("При регистрации произошла ошибка");
          }
          
          CTools::Redirect("regparent.php");
        } else {
          CTools::Message("Детей не может быть больше 10ти");
        }
      } else {
        CTools::Message("Вы не можете зарегистрироваться, не выбрав детей");
      }
      
    } else {
      CTools::Message("Вы должны принять соглашения");
    }
		
	}
	
?>