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
  use IEP\Structures\Group;
  
  /*!
    Файл реализации класса UserManager. Менеджер управления пользователями
    
    @package Managers
    @version 1.0
  */
  
	class UserManager extends IEP
	{
		
    /*!
      
      Функция авторизациия пользователя. Возвращает объект класса User для дальнейшего использования
      
      @param $email    - почта
      @param $password - пароль
      @return Дочерний класс User или false в случае ошибки
      
    */
		
		public function authorizate($email, $password)
		{
			$user_data = $this->get("call authentification(:email, :password)", [":email" => $email, ":password" => $password]);
      
      if (!empty($user_data)) {
        switch($user_data[0]['id_type_user'])
        {
          case USER_TYPE_STUDENT:
          {
            $student_data = $this->get("call getStudentInfo(:email)", [":email" => $email]);
            
            if (!empty($student_data)) {
              
              $s_grp = new Group($student_data[0]['grp'], $student_data[0]['code_spec'], (int)$student_data[0]['is_budget']);
              $s_grp->setID((int)$student_data[0]['grp_id']);
              
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
                $s_grp
              );
              
              return $s;
              
            } else {
              return false;
            }
            
          } break;
          case USER_TYPE_TEACHER:
          {
            $teacaher_data = $this->get("call getTeacherInfo(:email)", [":email" => $email]);
            
            if (!empty($teacaher_data)) {
              
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
              
            } else {
              return false;
            }
            
          } break;
          case USER_TYPE_PARENT:
          {
            $parent_data = $this->get("call getParentInfo(:email)", [":email" => $email]);
            
            if (!empty($parent_data)) {
              
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
            
            } else {
              return false;
            }
            
          } break;
          default: return false; break;
        }
      } else {
        
        $admin_data = $this->get("call authentificationAdmin(:email, :passwd)", [":email" => $email, ":passwd" => $password]);
        
        if (!empty($admin_data)) {
          
          $a = new User(
            $admin_data[0]['sn'],
            $admin_data[0]['fn'],
            $admin_data[0]['pt'],
            $admin_data[0]['email'],
            $admin_data[0]['passwd'],
            1
          );
          
          return $a;
          
        } else {
          return false;
        }
        
      }
		}
		
		public function add($user) : bool
		{
			switch($user->getTypeUser())
			{
				case USER_TYPE_STUDENT:
				{
				} break;
				case USER_TYPE_TEACHER:
				{
				} break;
				case USER_TYPE_PARENT:
				{
          
				} break;
				case USER_TYPE_ADMIN:
				{
          $add_admin_query = $this->dbc()->prepare("call addAdmin(:sn, :fn, :pt, :email, :passwd)");
          
          $add_admin_query->bindValue(":sn", $user->getSn());
          $add_admin_query->bindValue(":fn", $user->getFn());
          $add_admin_query->bindValue(":pt", $user->getPt());
          $add_admin_query->bindValue(":email", $user->getEmail());
          $add_admin_query->bindValue(":passwd", $user->getPassword());
          
          $result = $add_admin_query->execute();
          
          if ($result) {
            return $result;
          } else {
            $this->writeLog($add_admin_query->errorInfo()[3]);
            return false;
          }
          
				} break;
				default: return false; break;
			}
		}
		
		public function getAdmins()
		{
			$db_admins = $this->get("call getAllAdmins()");
      
      $admins = array();
      foreach ($db_admins as $db_admin) {
        $admins[] = new User(
					$db_admin['sn'], 
					$db_admin['fn'], 
					$db_admin['pt'], 
					$db_admin['email'], 
					$db_admin['passwd'],
          1
        );
      }
      
      return $admins;
		}
		
		public function getUsers() 
		{
			$db_users = $this->get("call getAllUsers()");
      
      $users = array();
      foreach ($db_users as $db_user) {
        $users[] = new User(
					$db_user['sn'], 
					$db_user['fn'], 
					$db_user['pt'], 
					$db_user['email'], 
					$db_user['paswd'], 
					(int)$db_user['type_user']
        );
      }
      
      return $users;
		}
		
		public function getStudents()
		{
			$db_students = $this->get("call getAllStudents()");
            
      $students = array();
      foreach ($db_students as $db_student) {
				
				$s_grp = new Group($db_student['grp'], $db_student['code_spec'], (int)$db_student['is_budget']);
				$s_grp->setID((int)$db_student['grp_id']);
				
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
					$s_grp
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
				
				// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ОЖИДАЕТ РЕАЛИЗАЦИИ !!!!!!!!!!!!!!!!!!!!!!!
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
      // foreach ($db_students as $db_student) {
        // $new_student = new Student(
          // new User(
              // $db_student['sn'],
              // $db_student['fn'],
              // $db_student['pt'],
              // $db_student['email'],
              // $db_student['paswd'],
              // (int)$db_student['type_user']
          // ),
          // $db_student['home_address'],
          // $db_student['cell_phone'],
					// $db_student['grp']
        // );
				
				// $students[] = $new_student;
      // }
			
      return $students;
		}
		
		public function setUserType(string $u_email, int $type) : bool
		{
			// !!!!!!!!!!!! Удалять запись с текущим типом аккаунта !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			$set_user_type = $this->dbc()->prepare("call setUserType(:email, :type)");
			
			$set_user_type->bindValue(":email", $u_email);
			$set_user_type->bindValue(":type", $type);
			
			return $set_user_type->execute();
		}
		
		public function getTypeUsers() : array
		{
			$db_typeUsers = $this->get("call getTypeUsers()");
			
			$typeUser = array();
			foreach ($db_typeUsers as $db_typeUser) {
				$typeUser[] = new class((int)$db_typeUser['id_type_user'], $db_typeUser['description']) {
					
					private $id;
					private $descp;
					
					function __construct(int $id, string $descp)
					{
						$this->id = $id;
						$this->descp = $descp;
					}
					
					public function getID() : int
					{
						return $this->id;
					}
					
					public function getDescp() : string
					{
						return $this->descp;
					}
					
				};
			}
			
			
			return $typeUser;
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
      $remove_user_query = $this->dbc()->prepare("call removeUser(:email)");
      $remove_user_query->bindValue(":email", $email);
      
      return $remove_user_query->execute();
		}
		
		public function change($old, $new)
		{
			
		}
	}
	
?>