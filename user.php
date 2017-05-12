<?php
  require_once "start.php";
	
	use IEP\Structures\OneNews;
	use IEP\Structures\Test;
	use IEP\Structures\OneQuestion;
	use IEP\Structures\Subject;
  use IEP\Structures\TrafficEntry;
	
	if(isset($_SESSION['user']))
	{
		$user = $_SESSION['user'];
		
		switch($user->getUserType())
		{
			case USER_TYPE_STUDENT:
			{
				$sogroups = $UM->query("SELECT * FROM `v_Students` WHERE `grp`=:grp AND `email`!=:email",
					[":grp" => $user->getGroup()->getNumberGroup(), ":email" => $user->getEmail()]
				);
				
				$CT->assign("fio", $user->getSn()." ".$user->getFn()." ".$user->getPt());
				$CT->assign("sogroups", $sogroups);
				$CT->assign("user", $user);
				$CT->assign("tests", $TM->getTestForGroup($user->getGroup()->getID()));
				
				$CT->Show("accounts/student.tpl");
			} break;
			case USER_TYPE_TEACHER:
			{
        
				$teacher_subjects = $SM->getTeacherSubjects($user->getEmail());
				$teacher_news = $NM->getTeacherNews($user->getEmail());
				$teacher_tests = $TM->getTeacherTests($user->getEmail());
				$other_subjects = $SM->getSubjects();
				
				// < Удаляем предметы, которые преподаватель уже ведёт
				foreach ($teacher_subjects as $teacher_subject) {
					if (in_array($teacher_subject, $other_subjects)) {
						unset($other_subjects[array_keys($other_subjects, $teacher_subject)[0]]);
					}
				}
				
				$user->setSubjects($teacher_subjects);
				
				$CT->assign("user", $user);
				$CT->assign("subjects", $other_subjects);
				$CT->assign("teachersNews", $teacher_news);
				$CT->assign("teachersTests", $teacher_tests);
				$CT->assign("groups", $GM->getGroups());
				
				$CT->Show("accounts/teacher.tpl");
				
				if (!empty($_POST['addNewsButton'])) {
					$data = CForm::getData(array(
						"caption",
						"content",
						"teacherEmail",
						"dp"
					));
					
					$new_news = new OneNews($data['caption'], $data['content'], $data['teacherEmail'], $data['dp']);
					
					if ($NM->add($new_news)) {
						CTools::Message("Новость опубликована");
					} else {
						CTools::Message("Произошла ошибка");
					}
					
					CTools::Redirect("user.php");
				}
				
				if (!empty($_POST['removeNewsButton'])) {
					$select_news = $_POST['select_news'];
					
					$result = true;
					for ($i = 0; $i < count($select_news); $i++) {
						$result *= $NM->remove($select_news[$i]);
					}
					
					if ($result) {
						CTools::Message("Новости были удалены");
					} else {
						CTools::Message("Произошла ошибка");
					}
					
					CTools::Redirect("user.php");
				}
				
				if (!empty($_POST['setSubjectButton'])) {
					$select_subject = $_POST['select_subject'];
					
					$result = true;
					for ($i = 0; $i < count($select_subject); $i++) {
						$result *= $SM->setSubject($user->getEmail(), $select_subject[$i]);
					}
					
					if ($result) {
						CTools::Message("Предметы успешно назначены");
					} else {
						CTools::Message("Произошла ошибка");
					}
					
					CTools::Redirect("user.php");
				}
				
				if (!empty($_POST['deleteSubjectButton'])) {
					$select_subject = $_POST['select_subject'];
					
					$result = true;
					for ($i = 0; $i < count($select_subject); $i++) {
						$result *= $SM->unsetSubject($user->getEmail(), $select_subject[$i]);
					}
					
					if ($result) {
						CTools::Message("Предметы убраны");
					} else {
						CTools::Message("Произошла ошибка");
					}
					
					CTools::Redirect("user.php");
				}
				
				if (!empty($_POST['addTestButton'])) {
					$caption = htmlspecialchars($_POST['caption']);
					$subject = $_POST['subject'];
					$teacherEmail = $_POST['teacherEmail'];
					$select_group = $_POST['select_group'] ?? array();
					
					$new_test = new Test($caption, $teacherEmail, $select_group);
					$new_test->setSubject(new Subject("", $subject));
					
					if ($TM->add($new_test)) {
						CTools::Message("Тест успешно создан");
					} else {
						CTools::Message("Произошла ошибка");
					}
					
					CTools::Redirect("user.php");
				}
				
				if (!empty($_POST['removeTestButton'])) {
					$test_id = $_POST['test_id'];
					
					if ($TM->remove($test_id)) {
						CTools::Message("Тест удалён");
					} else {
						CTools::Message("Произошла ошибка");
					}
					
					CTools::Redirect("user.php");
				}
				
				if (!empty($_POST['addQuestionButton'])) {
					$question_test = htmlspecialchars($_POST['question_test']);
					$question_caption = htmlspecialchars($_POST['question_caption']);
					$question_r_answer = htmlspecialchars($_POST['question_r_answer']);
					
					$answer_text = $_POST['answer_text'];
					$select_answers = $_POST['select_answers'];
					
					$answers = array();
					for ($i = 0; $i < count($select_answers); $i++) {
						$answers[] = $answer_text[$i];
					}
					$answers[] = $question_r_answer;
					
					$new_question = new OneQuestion($question_caption, $question_r_answer, $answers);
					
					if ($TM->addQuestion($question_test, $new_question)) {
						CTools::Message("Вопрос добавлен");
					} else {
						CTools::Message("Произошла ошибка");
					}
					
					CTools::Redirect("user.php");
				}
				
			} break;
			case USER_TYPE_PARENT:
			{
				$CT->assign("user", $user);
				$CT->assign("childs", $user->getChilds());
        
				$CT->Show("accounts/parent.tpl");
			} break;
      case USER_TYPE_ELDER:
      {
				$sogroups = $UM->query("SELECT * FROM `v_Students` WHERE `grp`=:grp",
					[":grp" => $user->getGroup()->getNumberGroup()]
				);
        
				
        $CT->assign("user", $user);
				$CT->assign("sogroups", $sogroups);
        
        if (!empty($_POST['commitTrafficButton'])) {
          $count_pairs = $_POST['count_pairs'];
          $traffic = $_POST['traffic'];
          
          echo "Count of pairs: ".$count_pairs." ";
          
          CTools::var_dump($traffic);
          
          $result = true;
          foreach ($traffic as $key => $value) {
            $result *= $TRM->add(new TrafficEntry($key, date("Y.m.d"), $value[0]*2, $count_pairs*2)); 
          }
          
          if ($result) {
            CTools::Message("Изменения зафиксированны");
          } else {
            CTools::Message("Ошибка при фиксации");
          }
          
          CTools::Redirect("user.php");
          
        }
        
        
        $CT->Show("accounts/elder.tpl");
      } break;
      default:
      {
        unset($_SESSION['user']);
        CTools::Redirect("index.php");
      } break;
		}
	}
	else CTools::Redirect("index.php");
	
?>
