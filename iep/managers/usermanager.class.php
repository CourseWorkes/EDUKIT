<?php
  declare(strict_types = 1);
	namespace IEP\Managers;
	
	require_once "iep.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/user.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/student.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/teacher.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/parent.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/subject.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/test.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/structures/onequestion.class.php";
    
  use IEP\Structures\User;
  use IEP\Structures\Student;
  use IEP\Structures\Teacher;
  use IEP\Structures\Parent_;
  use IEP\Structures\Subject;
  use IEP\Structures\Test;
  use IEP\Structures\OneQuestion;
	
	class UserManager extends IEP
	{
		
		public function authorizate($email, $password)
		{
			$user_data = $this->get("call authentification(:email, :password)", [":email" => $email, ":password" => $password]);
      
			switch($user_data[0]['id_type_user'])
			{
				case USER_TYPE_STUDENT:
				{
					$student_data = $this->get("call getStudentInfo(:email)", [":email" => $email]);
					
					$s = new Student(new User(
						$user_data[0]['second_name'],
						$user_data[0]['first_name'],
						$user_data[0]['patronymic'],
						$user_data[0]['email'],
						$user_data[0]['password'],
						(int)$user_data[0]['id_type_user']
					),
						$student_data[0]['home_address'],
						$student_data[0]['cell_phone'],
						$student_data[0]['grp']
					);
          
					return $s;
				} break;
				case USER_TYPE_TEACHER:
				{
					$teacaher_data = $this->get("call getTeacherInfo(:email)", [":email" => $email]);
          
					$t = new Teacher(new User(
						$user_data[0]['second_name'],
						$user_data[0]['first_name'],
						$user_data[0]['patronymic'],
						$user_data[0]['email'],
						$user_data[0]['password'],
						(int)$user_data[0]['id_type_user']
					),
						$teacaher_data[0]['info']
					);
          
					return $t;
				} break;
				case USER_TYPE_PARENT:
				{
					$parent_data = $this->get("call getParentInfo(:email)", [":email" => $email]);
					
					$p = new Parent_(new User(
						$user_data[0]['second_name'],
						$user_data[0]['first_name'],
						$user_data[0]['patronymic'],
						$user_data[0]['email'],
						$user_data[0]['password'],
						(int)$user_data[0]['id_type_user']
					),
						(int)$parent_data[0]['age'],
						$parent_data[0]['education'],
						$parent_data[0]['work_place'],
						$parent_data[0]['post'],
						$parent_data[0]['home_phone'],
						$parent_data[0]['cell_phone']
					);
					
					return $p;
				} break;
				case USER_TYPE_ADMIN:
				{
					echo "Add USER_TYPE_ADMIN";
				} break;
				default: return false; break;
			}
		}
		
		public function add($user) : bool
		{
			switch($user->getTypeUser())
			{
				case USER_TYPE_STUDENT:
				{
          try
          {
            $this->dbc()->beginTransaction();
            
            $add_user_query = $this->dbc()->prepare("call addStudent(:sn, :fn, :pt, :email, :paswd, :ha, :cp, :grp)");
            
            $add_user_query->bindValue(":sn", $user->getSn());
            $add_user_query->bindValue(":fn", $user->getFn());
            $add_user_query->bindValue(":pt", $user->getPt());
            $add_user_query->bindValue(":email", $user->getEmail());
            $add_user_query->bindValue(":paswd", $user->getPassword());
            $add_user_query->bindValue(":ha", $user->getHomeAddress());
            $add_user_query->bindValue(":cp", $user->getCellPhone());
            $add_user_query->bindValue(":grp", $user->getGroupID());
            
            if ($add_user_query->execute()) {
              return $this->dbc()->commit();
            } else {
              $this->dbc()->rollBack();
              return false;
						}
            
          }
          catch(PDOException $e)
          {
              $this->dbc()->rollBack();
              return false;
          }
				} break;
				case USER_TYPE_TEACHER:
				{
          try
          {
            $this->dbc()->beginTransaction();
            
            $add_user_query = $this->dbc()->prepare("call addTeacher(:sn, :fn, :pt, :email, :paswd, :info)");
            
            $add_user_query->bindValue(":sn", $user->getSn());
            $add_user_query->bindValue(":fn", $user->getFn());
            $add_user_query->bindValue(":pt", $user->getPt());
            $add_user_query->bindValue(":email", $user->getEmail());
            $add_user_query->bindValue(":paswd", $user->getPassword());
            $add_user_query->bindValue(":info", $user->getInfo());
            
            if ($add_user_query->execute()) {
              
              $subjects = $user->getSubjects();
              
              if (!empty($subjects)) {
                
                $set_subject_query = $this->dbc()->prepare("call setSubject(:email, :subject)");
                $set_subject_query->bindValue(":email", $user->getEmail());
                
                $result = true;
                for ($i = 0; $i < count($subjects); $i++) {
                  $set_subject_query->bindValue(":subject", $subjects[$i]);
                  
                  $result *= $set_subject_query->execute();
                }
                
                if ($result) {
                  return $this->dbc()->commit();
                } else {
                  $this->dbc()->rollBack();
                  return false;
                }
                
              } 
              else return $this->dbc()->commit();
              
            } else {
              $this->dbc()->rollBack();
              return false;
            }
            
          }
          catch(PDOException $e)
          {
            $this->dbc()->rollBack();
            return false;
          }
				} break;
				case USER_TYPE_PARENT:
				{
          try
          {
              $this->dbc()->beginTransaction();
              
              $add_user_query = $this->dbc()->prepare("call addParent(:sn, :fn, :pt, :email, :paswd, :age, :education, :wp, :post, :hp, :cp)");
              
              $add_user_query->bindValue(":sn", $user->getSn());
              $add_user_query->bindValue(":fn", $user->getFn());
              $add_user_query->bindValue(":pt", $user->getPt());
              $add_user_query->bindValue(":email", $user->getEmail());
              $add_user_query->bindValue(":paswd", $user->getPassword());
              $add_user_query->bindValue(":age", $user->getAge());
              $add_user_query->bindValue(":education", $user->getEducation());
              $add_user_query->bindValue(":wp", $user->getWorkPlace());
              $add_user_query->bindValue(":post", $user->getPost());
              $add_user_query->bindValue(":hp", $user->getHomePhone());
              $add_user_query->bindValue(":cp", $user->getCellPhone());
							
							if ($add_user_query->execute()) {
								return $this->dbc()->commit();
							} else {
								$this->dbc()->rollBack();
								print_r($add_user_query->errorInfo());
								return false;
							}
							
          }
          catch(PDOException $e)
          {
              $this->dbc()->rollBack();
              return false;
          }
				} break;
				case USER_TYPE_ADMIN:
				{
          try
          {
              $this->dbc()->beginTransaction();
              
              $add_user_query = $this->dbc()->prepare("INSERT INTO `users`
                  (`second_name`, `first_name`, `patronymic`, `email`, `password`, `id_type_user`)
                  VALUES
                  (:second_name, :first_name, :patronymic, :email, :password, :id_type_user);
              ");
              
              $add_user_query->bindValue(":second_name", $user->getSn());
              $add_user_query->bindValue(":first_name", $user->getFn());
              $add_user_query->bindValue(":patronymic", $user->getPt());
              $add_user_query->bindValue(":email", $user->getEmail());
              $add_user_query->bindValue(":password", $user->getPassword());
              $add_user_query->bindValue(":id_type_user", $user->getTypeUser());
              
              if (!$add_user_query->execute()) {
                  $this->dbc()->rollBack();
                  return false;
              }
              else return $this->dbc()->commit();
          }
          catch(PDOException $e)
          {
              $this->dbc()->rollBack();
              return false;
          }
				} break;
				default: return false; break;
			}
		}
		
		public function getUserByID($id)
		{
			$user_data = $this->get("SELECT * FROM `users` u INNER JOIN `students` s ON s.id_student=u.id_user WHERE `id_user`=:id", [":id" => $id])[0];
			
			return new Student( new User(
        $user_data["second_name"], 
        $user_data["first_name"], 
        $user_data["patronymic"],
        $user_data["email"],
        $user_data["password"],
        (int)$user_data["id_type_user"]
			), (int)$user_data['grp'], $user_data['home_address'], $user_data["cell_phone"]);
		}
		
		public function getUsers() 
		{
			$db_users =  $this->get("SELECT * FROM `users`");
      
      $users = array();
      foreach ($db_users as $db_user) {
        $users[] = new User(
            $db_user['second_name'], 
            $db_user['first_name'], 
            $db_user['patronymic'], 
            $db_user['email'], 
            $db_user['password'], 
            (int)$db_user['id_type_user']
        );
      }
      
      return $users;
		}
		
		public function getStudents()
		{
			$db_students = $this->get("call getAllStudents()");
            
      $students = array();
      foreach ($db_students as $db_student) {
        $new_student = new Student(
          new User(
              $db_student['sn'],
              $db_student['fn'],
              $db_student['pt'],
              $db_student['email'],
              $db_student['paswd'],
              (int)$db_student['id_type_user']
          ),
          $db_student['home_address'],
          $db_student['cell_phone'],
					$db_student['grp']
        );
				
				$students[] = $new_student;
      }
			
      return $students;
		}			
		
		public function getTeachers() : array
		{
			$db_teachers = $this->get("call getAllTeachers()");
      
      $teachers = array();
      foreach ($db_teachers as $db_teacher) {
        $new_teacher = new Teacher(
          new User(
            $db_teacher['sn'],
            $db_teacher['fn'],
            $db_teacher['pt'],
            $db_teacher['email'],
            $db_teacher['paswd'],
            (int)$db_teacher['type_user']
          ),
          $db_teacher['info']
        );
        
        $db_subjects = $this->get("call getSubjects(:emailTeacher)", [":emailTeacher" => $db_teacher['email']]);
        
        $subjects = array();
        foreach ($db_subjects as $db_subject) {
          $new_subject = new Subject($db_subject['description']);
          
          $subjects[] = $new_subject;
        }
        
        $new_teacher->setSubjects($subjects);
        $teachers[] = $new_teacher;
      }
      
      return $teachers;
		}
		
		public function getParents() : array
		{
			$db_parents = $this->get("call getAllParents()");
            
      $parents = array();
      foreach ($db_parents as $db_parent) {
        // $db_childs = $this->get("call getChilds(:emailParent)", [":emailParent" => $db_parent['email']]);
        
        // $childs = array();
        // for ($i = 0; $i < count($db_childs); $i++) {
            
            // $childs[$i]['child'] = new Student(
                // new User(
                    // $db_child['second_name'],
                    // $db_child['first_name'],
                    // $db_child['patronymic'],
                    // $db_child['email'],
                    // $db_child['password'],
                    // (int)$db_child['type_user']
                // ),
                // (int)$db_child['grp'],
                // $db_child['home_address'],
                // $db_child['cell_phone']
            // );
            // $childs[$i]['type_relation'] = $db_childs[$i]['id_type_releation'];
        // }
        
        $new_parent = new Parent_(
            new User(
                $db_parent['sn'],
                $db_parent['fn'],
                $db_parent['pt'],
                $db_parent['email'],
                $db_parent['paswd'],
                (int)$db_parent['type_user']
            ),
            (int)$db_parent['age'],
            $db_parent['education'],
            $db_parent['work_place'],
            $db_parent['post'],
            $db_parent['home_phone'],
            $db_parent['cell_phone']
        );
        // $new_parent->setChilds($childs);
        
        $parents[] = $new_parent;
      }
      
      return $parents;
		}
		
		public function getElders()
		{
			$db_students = $this->get("call getAllElders()");
            
      $students = array();
      foreach ($db_students as $db_student) {
        $new_student = new Student(
          new User(
              $db_student['sn'],
              $db_student['fn'],
              $db_student['pt'],
              $db_student['email'],
              $db_student['paswd'],
              (int)$db_student['type_user']
          ),
          $db_student['home_address'],
          $db_student['cell_phone'],
					$db_student['grp']
        );
				
				$students[] = $new_student;
      }
			
      return $students;
		}
		
		public function grantElder($emailStudent) : bool
		{
			$grant_elder_query = $this->dbc()->prepare("call grantElder(:email)");
			
			$grant_elder_query->bindValue(":email", $emailStudent);
			
			return $grant_elder_query->execute();
		}
		
		public function revokeElder($emailStudent) : bool
		{
			$revoke_elder_query = $this->dbc()->prepare("call revokeElder(:email)");
			
			$revoke_elder_query->bindValue(":email", $emailStudent);
			
			return $revoke_elder_query->execute();
		}
		
		public function remove($email) : bool
		{
      $remove_user_query = $this->dbc()->prepare("DELETE FROM `users` WHERE `email`=:email");
      $remove_user_query->bindValue(":email", $email);
      
      return $remove_user_query->execute();
		}
		
		public function change($old, $new)
		{
			
		}
	}
	
?>