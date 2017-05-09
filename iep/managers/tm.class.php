<?php
  declare(strict_types = 1);
  namespace IEP\Managers;
  
  require_once "iep.class.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/test.class.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/testquestion.class.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/studentanswer.class.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/subject.class.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/group.class.php";
  
  use IEP\Managers\IEP;
  use IEP\Structures\Subject;
  use IEP\Structures\Specialty;
  use IEP\Structures\Group;
  use IEP\Structures\Test;
  use IEP\Structures\TestQuestion;
  
  class TestManager extends IEP
  {
    
    public function add($test)
    {
      try
			{ //< Блокировка таблиц !!!
				$this->dbc()->beginTransaction();
				
				$test_add_query = $this->dbc()->prepare("call addTest(:emailTeacher, :subject, :caption)");
				
				$test_add_query->bindValue(":subject", $test->getSubject());
				$test_add_query->bindValue(":emailTeacher", $test->getAuthor());
				$test_add_query->bindValue(":caption", $test->getCaption());
				
				if ($test_add_query->execute()) {
          
          $for_groups = $test->getGroups();
          
					if (!empty($for_groups)) {
            
						$last_id = $this->query("SELECT LAST_INSERT_ID() as last_id FROM `tests`");
						$last_id = $last_id[0]['last_id'];
						
						$result = true;
						for ($i = 0; $i < count($for_groups); $i++) {
              $result *= $this->setGroup((int)$last_id, $for_groups[$i]);
						}
						
						if ($result) {
							return $this->dbc()->commit();
						}
						else {
							$this->dbc()->rollBack();
							return false;
						}
						
					} else {
						return $this->dbc()->commit();
					}
					
				}
				else {
					$this->dbc()->rollBack();
					return false;
				}
				
			}
			catch(PDOException $e)
			{
				$this->dbc()->rollBack();
				return false;
			}
    }
    
    public function getTests(string $teacher_email)
    {
      $db_tests = $this->query("call getTests(:t_email)", [":t_email" => $teacher_email]);
      
      $tests = array();
      foreach ($db_tests as $db_test) {
        
        $subject = new Subject($db_test['subject_caption']);
        $subject->setSubjectID((int)$db_test['subject_id']);
        
        $db_groups = $this->query("call getTestGroups(:test_id)", [":test_id" => $db_test['id_test']]);
        
        $groups = array();
        foreach ($db_groups as $db_group) {
          
          $spec = new Specialty($db_group['code_spec'], $db_group['spec_descp']);
          $spec->setSpecialtyID((int)$db_group['id_spec']);
          
          $group = new Group(
            $db_group['grp'],
            $spec,
            $db_group['edu_year'],
            (int)$db_group['is_budget']
          );
          
          $groups[] = $group;
        }
        
        $db_questions = $this->query("call getQuestions(:test_id)", [":test_id" => $db_test['id_test']]);
        
        $questions = array();
        foreach ($db_questions as $db_question) {
          
          $db_answers = $this->query("call getAnswers(:question_id)", [":question_id" => $db_question['id_question']]);
          
          $answers = array();
          foreach ($db_answers as $db_answer) {
            $answers[] = array(
              "id" => $db_answer['id_answer'],
              "answer" => $db_answer['answer']
            );
          }
          
          $question = new TestQuestion($db_question['question'], $db_question['r_answer']);
          $question->setQuestionID((int)$db_question['id_question']);
          $question->setAnswers($answers);
          
          $questions[] = $question;
        }
        
        $test = new Test($db_test['test_caption'], $subject, $db_test['author_email'], $groups);
        $test->setTestID((int)$db_test['id_test']);
        $test->setQuestions($questions);
        
        $tests[] = $test;
      }
      
      return $tests;
    }
    
    public function getAllTests() : array
    {
      $db_tests = $this->query("call getAllTests()");
      
      $tests = array();
      foreach ($db_tests as $db_test) {
        
        $subject = new Subject($db_test['subject_caption']);
        $subject->setSubjectID((int)$db_test['subject_id']);
        
        $db_groups = $this->query("call getTestGroups(:test_id)", [":test_id" => $db_test['id_test']]);
        
        $groups = array();
        foreach ($db_groups as $db_group) {
          
          $spec = new Specialty($db_group['code_spec'], $db_group['spec_descp']);
          $spec->setSpecialtyID((int)$db_group['id_spec']);
          
          $group = new Group(
            $db_group['grp'],
            $spec,
            $db_group['edu_year'],
            (int)$db_group['is_budget']
          );
          
          $groups[] = $group;
        }
        
        $db_questions = $this->query("call getQuestions(:test_id)", [":test_id" => $db_test['id_test']]);
        
        $questions = array();
        foreach ($db_questions as $db_question) {
          
          $db_answers = $this->query("call getAnswers(:question_id)", [":question_id" => $db_question['id_question']]);
          
          $answers = array();
          foreach ($db_answers as $db_answer) {
            $answers[] = array(
              "id" => $db_answer['id_answer'],
              "answer" => $db_answer['answer']
            );
          }
          
          $question = new TestQuestion($db_question['question'], $db_question['r_answer']);
          $question->setQuestionID((int)$db_question['id_question']);
          $question->setAnswers($answers);
          
          $questions[] = $question;
        }
        
        $test = new Test($db_test['test_caption'], $subject, $db_test['author_email'], $groups);
        $test->setTestID((int)$db_test['id_test']);
        $test->setQuestions($questions);
        
        $tests[] = $test;
      }
      
      return $tests;
    }
    
    public function addQuestion(int $test_id, TestQuestion $question)
    {
			try
			{
				$this->dbc()->beginTransaction();
				
				$add_question_query = $this->dbc()->prepare("call addQuestion(:test_id, :question, :r_answer)");
				
				$add_question_query->bindValue(":test_id", $test_id);
				$add_question_query->bindValue(":question", $question->getQuestion());
				$add_question_query->bindValue(":r_answer", $question->getRAnswer());
				
				if (!empty($question->getAnswers())) {					
					if ($add_question_query->execute()) {
						
						$last_id = $this->query("SELECT LAST_INSERT_ID() as last_id FROM `tests` WHERE `id_test`=:test_id", [":test_id" => $test_id]);
						$last_id = $last_id[0]['last_id'];
						
						$add_answer_query = $this->dbc()->prepare("call addAnswer(:question_id, :answ)");
						$add_answer_query->bindValue(":question_id", $last_id);
						
						$result = true;
						foreach ($question->getAnswers() as $answer) {
							$add_answer_query->bindValue(":answ", $answer);
							
							$result *= $add_answer_query->execute();
						}
						
						if ($result) {
							return $this->dbc()->commit();
						} else {							
							$this->dbc()->rollBack();
							return false;
						}
						
					} else {
						$this->dbc()->rollBack();
						return false;
					}
				}
				
			}
			catch(PDOException $e)
			{
				$this->dbc()->rollBack();
				return false;
			}
    }
    
    public function addAnswer(int $question_id, string $answer)
    {
      $add_answer_query = $this->dbc()->prepare("call addAnswer(:question_id, :answ)");
      
      $add_answer_query->bindValue(":question_id", $question_id);
      $add_answer_query->bindValue(":answ", $answer);
      
      return $add_answer_query->execute();
    }
    
		public function getAnswers(int $question_id)
		{
			$answers_query = $this->dbc()->prepare("call getAnswers(:question_id)");
			
			$answers_query->bindValue(":question_id", $question_id);
			
			return $answers_query->execute();
		}
    
		public function changeCaptionTest(int $test_id, string $test_caption) : bool
		{
			$test_change = $this->dbc()->prepare("call changeCaptionTest(:test_id, :test_caption)");
			
			$test_change->bindValue(":test_id", $test_id);
			$test_change->bindValue(":test_caption", $test_caption);
			
			return $test_change->execute();
		}
		
		public function changeSubjectTest(int $test_id, int $subject_id) : bool
		{
			$test_change = $this->dbc()->prepare("call changeSubjectTest(:test_id, :subject_id)");
			
			$test_change->bindValue(":test_id", $test_id);
			$test_change->bindValue(":subject_id", $subject_id);
			
			return $test_change->execute();
		}
		
		public function changeCaptionQuestion(int $question_id, string $new_caption) : bool
		{
			$change_question_query = $this->dbc()->prepare("call changeCaptionQuestion(:question_id, :new_caption)");
			
			$change_question_query->bindValue(":question_id", $question_id);
			$change_question_query->bindValue(":new_caption", $new_caption);
			
			$result = $change_question_query->execute();
			
			if (!$result) {
				$this->writeLog($change_question_query->errorInfo()[2]);
				return false;
			} else {			
				return $result;
			}
		}
		
		public function changeRAnswerQuestion(int $question_id, string $new_RAnswer) : bool
		{
			$change_question_query = $this->dbc()->prepare("call changeRAnswerQuestion(:question_id, :new_RAnswer)");
			
			$change_question_query->bindValue(":question_id", $question_id);
			$change_question_query->bindValue(":new_RAnswer", $new_RAnswer);
			
			$result = $change_question_query->execute();
			
			if (!$result) {
				$this->writeLog($change_question_query->errorInfo()[2]);
				return false;
			} else {			
				return $result;
			}
		}
		
		public function changeCaptionAnswer(int $answer_id, string $new_answer) : bool
		{
			$change_answer_query = $this->dbc()->prepare("call changeCaptionAnswer(:answer_id, :new_answer)");
			
			$change_answer_query->bindValue(":answer_id", $answer_id);
			$change_answer_query->bindValue(":new_answer", $new_answer);
			
			return $change_answer_query->execute();
		}
		
		public function setGroup(int $test_id, int $test_grp) : bool
		{
			$set_group_query = $this->dbc()->prepare("call setGroup(:test_id, :test_grp)");
			
			$set_group_query->bindValue(":test_id", $test_id);
			$set_group_query->bindValue(":test_grp", $test_grp);
			
			return $set_group_query->execute();
		}
		
		public function unsetGroup(int $test_id, int $test_grp) : bool
		{
			$set_group_query = $this->dbc()->prepare("call unsetGroup(:test_id, :test_grp)");
			
			$set_group_query->bindValue(":test_id", $test_id);
			$set_group_query->bindValue(":test_grp", $test_grp);
			
			return $set_group_query->execute();
		}
    
    public function removeQuestion(int $question_id)
		{
			$remove_question_query = $this->dbc()->prepare("call removeQuestion(:question_id)");
			$remove_question_query->bindValue(":question_id", $question_id);
			
			return $remove_question_query->execute();
		}
		
		public function removeAnswer(int $answer_id) : bool
		{
			$remove_answer_query = $this->dbc()->prepare("call removeAnswer(:answer_id)");
			
			$remove_answer_query->bindValue(":answer_id", $answer_id);
			
			return $remove_answer_query->execute();
		}
    
    public function remove($test_id) : bool
    {
      $remove_test_query = $this->dbc()->prepare("call removeTest(:test_id)");
      
      $remove_test_query->bindValue(":test_id", $test_id);
      
      return $remove_test_query->execute();
    }
    
  }
  
  
?>