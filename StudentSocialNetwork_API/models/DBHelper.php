<?php

namespace app\models;

/*use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;*/
require_once '../vendor/autoload.php';

use ActiveRecord;
use app\config\ConfigDataDB;
use app\config\ConfigDB;
use app\models\entities;
use DateTime;
use yii\db\Exception;

class DBHelper
{
    const HOST = ControlsAPI::HOST;

    public function __construct()
    {
        ActiveRecord\Config::initialize(function ($cfg) {
            //$connStr = Config_DB::getConnectionString('ovsienkoff');
            $connStr = ConfigDB::getConnectionString('student_social_network');
            $cfg->set_model_directory('application/models');
            $cfg->set_connections(array('development' => $connStr));
        });
    }

    // Добавить ip-адрес для аккаунта
    public function addIpToAccount($idAccount, $ip)
    {
        $accountIp = new entities\AccountIp();
        $accountIp->account_id = $idAccount;
        $accountIp->ip_address = $ip;
        $accountIp->save();
    }

    // Получить все привязанные ip пользователя
    public function getAccountIps($idAccount)
    {
        $ips = entities\AccountIp::all();
        $ipsArray = array();
        foreach ($ips as $key => $value) {
            if ($value->account_id == $idAccount) {
                $ipsArray[] = $value->ip_address;
            }
        }
        return $ipsArray;
    }

    // Проверить, есть ли привязанный ip у данного аккаунта
    public function checkIpExists($idAccount, $ip)
    {
        $ipsAccount = $this->getAccountIps($idAccount);
        foreach ($ipsAccount as $key => $value) {
            if ($value == $ip) {
                return true;
            }
        }
        return false;
    }

    // Получить статус (online/offline) пользователя
    public function getStatusVisit($id)
    {
        $lastVisitDatetime = new DateTime($this->getLastVisit($id));
        $nowDatetime = new DateTime(date("Y-m-d H:i:s"));
        $interval = $lastVisitDatetime->diff($nowDatetime);
        if ($interval->i > 10 || $interval->h > 0 || $interval->d > 0 || $interval->m > 0 || $interval->y > 0) {
            return "offline";
        } else {
            return "online";
        }
    }

    // Получить дату-время последнего визита пользователя
    public function getLastVisit($id)
    {
        return (!empty(entities\VisitUser::first($id))) ? entities\VisitUser::first($id)->datetime : null;
    }

    // Обновить дату-время последнего визита пользователя
    public function refreshVisit($id)
    {
        $visitUser = entities\VisitUser::first($id);
        if ($visitUser != null) {
            $visitUser->datetime = date("Y-m-d H:i:s");
            $visitUser->save();
        }
    }

    // Получить массив объектов аккаунтов
    public function getAccounts()
    {
        return entities\Account::all();
    }

    // Получить аккаунт как объект по id
    public function getAccountAsObject($id)
    {
        return entities\Account::first($id);
    }

    // Получить аккаунт как массив по id
    public function getAccountAsArray($id)
    {
        $arrAccount = array();
        $account = self::getAccountAsObject($id);
        if ($account != null) {
            $props = self::getPropertiesAccount();
            foreach ($props as $key => $value) {
                $arrAccount[$key] = $account->$key;
            }
        }
        return $arrAccount;
    }

    // Получить аккаунт как массив по Email
    public function getAccountAsArrayByEmail($email)
    {
        $accounts = self::getAccounts();
        $accountObj = null;
        foreach ($accounts as $key => $value) {
            if ($email == $value->email) {
                $accountObj = $value;
            }
        }
        if (!empty($accountObj)) {
            $account = array();
            $props = self::getPropertiesAccount();
            foreach ($props as $key => $value) {
                $account[$key] = $accountObj->$key;
            }
            return $account;
        } else {
            return null;
        }
    }

    // Авторизация аккаунта
    public function auth($email, $password)
    {
        if ($email != null && $password != null) {
            $account = $this->getAccountAsArrayByEmail($email);
            if (!empty($account)) {
                //if(password_verify($password, $account['password_hash']))
                if ($password == $account['password_hash']) {
                    return $account;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    // Сравнение паролей (хэшированных)
    public function checkHashPasswords($email, $passwordHash)
    {
        if ($email != null && $passwordHash != null) {
            $account = $this->getAccountAsArrayByEmail($email);
            if (!empty($account)) {
                if ($account['password_hash'] == $passwordHash) {
                    return $account;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    // Получить все свойсва таблицы Account
    public function getPropertiesAccount()
    {
        $props = new entities\Account();
        $props = $props->attributes();
        return $props;
    }

    // Получить все свойсва таблицы Group
    public function getPropertiesGroup()
    {
        $props = new entities\Group();
        $props = $props->attributes();
        return $props;
    }

    // Получить массив групп как объекты
    public function getGroupsAsObject()
    {
        return entities\Group::all();
    }

    // Получить массив групп как массив
    public function getGroupsAsArray()
    {
        $groups = $this->getGroupsAsObject();
        $groupsArray = array();
        foreach ($groups as $key => $value) {
            $groupsArray[] = $value->name;
        }
        return $groupsArray;
    }

    // Получить группу как объект по Name
    public function getGroupByName($name)
    {
        $groups = $this->getGroupsAsObject();
        foreach ($groups as $key => $value) {
            if ($value->name == $name) {
                return $value;
            }
        }
        return null;
    }

    // Проверить существование аккаунта по id
    public function accountIdExists($id)
    {
        return entities\Account::exists($id);
    }

    // Проверить существание группы по Name
    public function groupExists($group)
    {
        $groups = $this->getGroupsAsObject();
        foreach ($groups as $key => $value) {
            if ($value->name == $group) {
                return true;
            }
        }
        return false;
    }

    // Проверить существование роли по Name
    public function roleExists($role)
    {
        $roles = [0 => 'admin', 1 => 'user'];
        if (array_search($role, $roles) !== false) {
            return true;
        } else {
            return false;
        }
    }

    // Проверить использование email по аккаунтам
    public function emailExists($email)
    {
        $accounts = $this->getAccounts();
        foreach ($accounts as $key => $value) {
            if ($value->email == $email) {
                return true;
            }
        }
        return false;
    }

    // Проверить существование пола
    public function genderExists($gender)
    {
        $genders = [0 => 'Мужской', 1 => 'Женский'];
        if (array_search($gender, $genders) !== false) {
            return true;
        } else {
            return false;
        }
    }

    // Получить фото как объект по id
    public function getPhotoById($id)
    {
        return entities\Photo::first($id);
    }

    // Сгенерировать случайный пароль
    public function generatePassword()
    {
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $max = 10;
        $size = StrLen($chars) - 1;
        $password = null;

        while ($max--) {
            $password .= $chars[rand(0, $size)];
        }

        return $password;
    }

    // Отправить сообщение на почту с данными входа авторизации (для регистрации)
    public function sendMessageToEmail($to, $password)
    {
        if (!empty($to)) {
            $mailer = new PHPMailer();

            $from = 'student.social.network.service@gmail.com';

            $mailer->SMTPDebug = 0;

            // Настройки сервера SMTP
            $mailer->isSMTP();
            $mailer->Host = 'smtp.gmail.com';
            $mailer->Port = 465;
            $mailer->SMTPSecure = 'tls';
            $mailer->SMTPAuth = true;
            $mailer->Password = 'socialnetwork';
            $mailer->Username = $from;

            try {
                try {
                    $mailer->smtpConnect(
                        array(
                            "ssl" => array(
                                "verify_peer" => false,
                                "verify_peer_name" => false,
                                "allow_self_signed" => true
                            )
                        )
                    );
                } catch (Exception $e) {
                }

                // Отправляемое письмо
                $mailer->setFrom($from); // отправитель
                $mailer->addAddress($to); // получатели
                $mailer->CharSet = "utf-8";
                $mailer->Subject = "Students Social Network - Ваши данные для входа в аккаунт.";
                $msg = "<h1>Здравствуйте! Мы прислали вам данные для входа на наш сайт.</h1><p>E-mail: $to</p><p>Пароль: $password</p><p>Спасибо за использование нашего сайта! С уважением администрация Студенческой Социальной Сети.</p>";
                $mailer->msgHTML($msg);
                $mailer->AltBody = strip_tags($msg);

                if ($mailer->send()) {
                    return true;
                }
            } catch (Exception $ex) {
                return new Exception($ex->getMessage());
            }
        }
        return false;
    }

    // Регистрация нового аккаунта
    public function registrationAccount($firstName, $lastName, $patronymic, $email, $group, $role, $gender)
    {
        if (!empty($firstName) && !empty($lastName) && !empty($patronymic) && !empty($email) && !empty($group) && !empty($role)) {
            // Генерируем случайный пароль
            $password = $this->generatePassword();
            // Шифруем пароль в двойной md5
            $passwordHash = md5(md5($password));

            $group = $this->getGroupByName($group);

            $personalInfo = new entities\PersonalInfo();
            $personalInfo->date_birthday = date_create("1900-01-01")->Format('Y-m-d');
            $personalInfo->gender = $gender;
            $personalInfo->photo_path = "db/photos/1.jpg";
            $personalInfo->save();

            $settings = new entities\Settings();
            $privacy = new entities\Privacy();
            $privacy->save();
            $settings->privacy_id = $privacy->id;
            $settings->save();

            $account = new entities\Account();
            $account->first_name = $firstName;
            $account->last_name = $lastName;
            $account->patronymic = $patronymic;
            $account->email = $email;
            $account->group_id = $group->id;
            $account->personal_info_id = $personalInfo->id;
            $account->role = $role;
            $account->password_hash = $passwordHash;//password_hash($password, PASSWORD_DEFAULT);
            $account->settings_id = $settings->id;
            $account->save();

            $visitUser = new entities\VisitUser();
            $visitUser->account_id = $account->id;
            $visitUser->datetime = date_create("1900-01-01")->Format('Y-m-d');
            $visitUser->save();

            // Отправить сообщение на почту зарегистрировавшего с данными входа (email, пароль)
            /*if($this->sendMessageToEmail($email, $password)){
                return $account;
            }*/
            return $password;
        }
        return null;
    }

    // Добавить фото в б/д и файл в папку db/photos
    public function addPhoto($data, $description, $accountId)
    {
        $photo = new entities\Photo();
        $photo->path = "";
        $photo->description = $description;
        $photo->datetime_add = date("Y-m-d H:i:s");
        $photo->account_id = $accountId;
        $photo->save();

        $photo->path = "db/photos/" . $photo->id . '.jpg';
        $photo->save();
        return $photo->id;
    }

    // Получить список черного листа как объект
    public function getBlackLists()
    {
        return entities\BlackList::all();
    }

    // Получить черный список пользователя как массив объектов
    public function getBlackListOfUserLimitAndOffset($idUser, $limit = 10, $offset = 0)
    {
        $limit++;
        $res = array();
        try {
            $accounts = entities\BlackList::find('all', array(
                'conditions' => "account_id = $idUser",
                'order' => 'id ASC',
                'limit' => $limit,
                'offset' => $offset
            ));
            // Проверяем, есть ли ещё записи?
            if (count($accounts) == $limit) {
                $res['is_there_more_black_list'] = true;
                array_pop($accounts);
            } else {
                $res['is_there_more_black_list'] = false;
            }
            $res['black_list'] = array();
            foreach ($accounts as $key => $value) {
                $accBL = $this->getAccountAsArray($value->user_black_list_id);
                $perInfoAccBL = $this->getPersonalInfoById($accBL['personal_info_id']);
                $res['black_list'][] = [
                    'id' => $value->id,
                    'user_black_list_id' => $value->user_black_list_id,
                    'first_name' => $accBL['first_name'],
                    'last_name' => $accBL['last_name'],
                    'photo_path' => self::HOST . $perInfoAccBL->photo_path,
                    'status_visit' => $this->getStatusVisit($accBL['id'])
                ];
            }
            return $res;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return null;
    }

    // Получить черный список пользователя как массив объектов
    public function getBlackListOfUser($idUser)
    {
        $blackList = $this->getBlackLists();
        $blackListOfUser = array();
        foreach ($blackList as $key => $value) {
            if ($value->account_id == $idUser) {
                $accBL = $this->getAccountAsArray($value->user_black_list_id);
                $perInfoAccBL = $this->getPersonalInfoById($accBL['personal_info_id']);
                $blackListOfUser[] = [
                    'id' => $value->id,
                    'user_black_list_id' => $value->user_black_list_id,
                    'first_name' => $accBL['first_name'],
                    'last_name' => $accBL['last_name'],
                    'photo_path' => self::HOST . $perInfoAccBL->photo_path,
                    'status_visit' => $this->getStatusVisit($accBL['id'])
                ];
            }
        }
        return $blackListOfUser;
    }

    // Проверить что пользователь уже есть в чёрном списке другого пользователя
    public function checkBlackListUser($idUser, $userIdBlackList)
    {
        $blackList = $this->getBlackListOfUser($idUser);
        $idsBL = array();
        foreach ($blackList as $key => $value) {
            $idsBL[] = $value['user_black_list_id'];
        }
        $res = array_search($userIdBlackList, $idsBL);
        if ($res !== false) {
            return true;
        }
        return false;
    }

    // Добавить пользователя в чёрный список
    public function addUserToBlackList($idUser, $userIdBlackList)
    {
        if (!$this->checkBlackListUser($idUser, $userIdBlackList)) {
            $bl = new entities\BlackList();
            $bl->user_black_list_id = $userIdBlackList;
            $bl->account_id = $idUser;
            $bl->save();
            return true;
        }
        return false;
    }

    // Удалить пользователя из чёрного списка
    public function removeUserFromBlackList($idUser, $userIdBlackList)
    {
        if ($this->checkBlackListUser($idUser, $userIdBlackList)) {
            $bls = $this->getBlackListOfUser($idUser);
            $idBl = -1;
            foreach ($bls as $key => $value) {
                if ($value['user_black_list_id'] == $userIdBlackList) {
                    $idBl = $value['id'];
                }
            }
            $bl = entities\BlackList::first($idBl);
            if (!empty($bl)) {
                $bl->delete();
                return true;
            }
        }
        return false;
    }

    // Получить все фото как массив объектов
    public function getPhotos()
    {
        return entities\Photo::all();
    }

    // Получить все фото пользователя по его id с limit и offset
    public function getPhotoOfUserLimitAndOffset($idUser, $limit = 10, $offset = 0)
    {
        $photosOfUser = array();
        try {
            $photos = entities\Photo::find('all', array(
                'conditions' => "account_id = $idUser",
                'order' => 'datetime_add ASC',
                'limit' => $limit,
                'offset' => $offset
            ));
            foreach ($photos as $key => $value) {
                $photosOfUser[] = [
                    'id' => $value->id,
                    'description' => $value->description,
                    'datetime_add' => date_create($value->datetime_add)->Format('Y-m-d H:i'),
                    'account_id' => $value->account_id,
                    'path' => self::HOST . $value->path
                ];
            }
            return $photosOfUser;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return null;
    }

    // Получить все фото пользователя по его id
    public function getPhotoOfUser($idUser)
    {
        $photos = $this->getPhotos();
        $photosOfUser = array();
        foreach ($photos as $key => $value) {
            if ($value->account_id == $idUser) {
                $photosOfUser[] = [
                    'id' => $value->id,
                    'description' => $value->description,
                    'datetime_add' => $value->datetime_add,
                    'account_id' => $value->account_id,
                    'path' => self::HOST . $value->path
                ];
            }
        }
        return $photosOfUser;
    }

    // Получить путь к аватарке пользователя
    public function getAvatarAccount($idUser)
    {
        $user = $this->getAccountAsObject($idUser);
        if ($user != null) {
            $personalInfo = $this->getPersonalInfoById($user->personal_info_id);
            if ($personalInfo != null) {
                return $personalInfo->photo_path;
            }
        }
        return null;
    }

    // Удалить файл фото по пути
    public function removePhotoByPath($path)
    {
        if (file_exists($path)) {
            if ($path != "db/photos/1.jpg" && $path != "db/photos/conversation.jpg") {
                return unlink($path);
            } else {
                return true;
            }
        }
        return false;
    }

    // Удалить аккаунт и все связанные с ним данные
    public function removeAccount($email)
    {
        if (!empty($email)) {
            $account = $this->getAccountAsArrayByEmail($email);
            if (!empty($account)) {
                $accObj = entities\Account::first($account['id']);
                $accObj->delete();

                $personalInfo = entities\PersonalInfo::first($account['personal_info_id']);
                $this->removePhotoByPath($personalInfo->photo_path); // Удаляем файл картинки
                $personalInfo->delete();

                $settings = entities\Settings::first($account['settings_id']);
                $privacy = entities\Privacy::first($settings->privacy_id);
                $settings->delete();
                $privacy->delete();

                $blackList = $this->getBlackListOfUser($accObj->id);
                foreach ($blackList as $key => $value) {
                    $blDelete = entities\BlackList::first($value['id']);
                    $blDelete->delete();
                }

                $photos = $this->getPhotoOfUser($accObj->id);
                foreach ($photos as $key => $value) {
                    $photoDelete = entities\Photo::first($value['id']);
                    $photoDelete->delete();
                    $this->removePhotoByPath($value['path']); // Удаляем файл картинки
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Получить список аккаунтов с ролью admin как массив
    public function getAdminsAsArray()
    {
        $admins = array();
        $accounts = $this->getAccounts();
        foreach ($accounts as $key => $value) {
            if ($value->role == 'admin') {
                $admins[] = $this->getAccountAsArray($value->id);
            }
        }
        return $admins;
    }

    // Получить персональную информацию
    public function getPersonalInfo()
    {
        return entities\PersonalInfo::all();
    }

    // Получить персональную информацию по id
    public function getPersonalInfoById($id)
    {
        return entities\PersonalInfo::first($id);
    }

    // Получить персональную информацию по id (в виле массива)
    public function getPersonalInfoByIdAsArray($id)
    {
        $personalInfo = $this->getPersonalInfoById($id);
        $personalInfoArray = array();
        if ($personalInfo != null) {
            $personalInfoArray = [
                'id' => $personalInfo->id,
                'gender' => $personalInfo->gender,
                'phone_number' => $personalInfo->phone_number,
                'activities' => $personalInfo->activities,
                'interests' => $personalInfo->interests,
                'about_me' => $personalInfo->about_me,
                'photo_path' => $personalInfo->photo_path,
                'date_birthday' => $personalInfo->date_birthday
            ];
        }
        return $personalInfoArray;
    }

    // Получить информацию профиля в виде массива по Email
    public function getProfile($email)
    {
        $acc = $this->getAccountAsArrayByEmail($email);
        if (!empty($acc)) {
            $profile = [
                'first_name' => $acc['first_name'],
                'last_name' => $acc['last_name'],
                'patronymic' => $acc['patronymic'],
                'email' => $acc['email'],
                'blocked' => $acc['blocked'],
                'role' => $acc['role']
            ];
            $personalInfo = $this->getPersonalInfoById($acc['personal_info_id']);
            if (!empty($personalInfo)) {
                $profile['gender'] = $personalInfo->gender;
                $profile['phone_number'] = $personalInfo->phone_number;
                $profile['activities'] = $personalInfo->activities;
                $profile['interests'] = $personalInfo->interests;
                $profile['about_me'] = $personalInfo->about_me;
                $profile['photo_path'] = self::HOST . $personalInfo->photo_path;
                $profile['date_birthday'] = $personalInfo->date_birthday;
            }
            return $profile;
        } else {
            return null;
        }
    }

    // Получить информацию профиля в виде массива по id
    public function getProfileById($id)
    {
        $acc = $this->getAccountAsArray($id);
        if (!empty($acc)) {
            $group = entities\Group::first($acc['group_id']);
            $profile = [
                'id' => $acc['id'],
                'first_name' => $acc['first_name'],
                'last_name' => $acc['last_name'],
                'patronymic' => $acc['patronymic'],
                'email' => $acc['email'],
                'blocked' => $acc['blocked'],
                'role' => $acc['role'],
                'group' => $group->name
            ];
            $personalInfo = $this->getPersonalInfoById($acc['personal_info_id']);
            if (!empty($personalInfo)) {
                $profile['gender'] = $personalInfo->gender;
                $profile['phone_number'] = $personalInfo->phone_number;
                $profile['activities'] = $personalInfo->activities;
                $profile['interests'] = $personalInfo->interests;
                $profile['about_me'] = $personalInfo->about_me;
                $profile['photo_path'] = self::HOST . $personalInfo->photo_path;
                $profile['date_birthday'] = $personalInfo->date_birthday;
            }
            $profile['status_visit'] = $this->getStatusVisit($id);
            return $profile;
        } else {
            return null;
        }
    }

    // Добавить фото к записи пользователя в папку db/photos
    public function addPostImage($tmpNameImage, $idPost)
    {
        if (!empty($tmpNameImage)) {
            try {
                $path = "db/photos/post" . $idPost . '.jpg';

                $source = fopen($tmpNameImage, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);

                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Добавить обновленное фото пользователя в папку db/photos
    private function updatePhotoProfileTmpImage($tmpNameImage, $userId)
    {
        if (!empty($tmpNameImage)) {
            try {
                $path = "db/photos/profile" . $userId . '.jpg';
                $source = fopen($tmpNameImage, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);
                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Добавить обновленное фото беседы в папку db/photos
    private function updatePhotoConversationTmpImage($tmpNameImage, $conversationId)
    {
        if (!empty($tmpNameImage)) {
            try {
                $path = "db/photos/conversation" . $conversationId . '.jpg';
                $source = fopen($tmpNameImage, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);
                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Добавить новую запись на страницу пользователя
    public function addPost(
        $accountFromId,
        $accountToId,
        $tmpNameImage,
        $message,
        $videoLink,
        $files,
        $pollTheme,
        $pollAnswers,
        $pollAnon
    ) {
        if (!empty($accountFromId) && !empty($accountToId)) {
            // Если такие аккаунты есть
            if (entities\Account::first($accountFromId) && entities\Account::first($accountToId)) {
                // Добавляем опрос (с ответами) если он указан
                $poll = new entities\Poll();
                if (!empty($pollTheme) && !empty($pollAnswers)) {
                    if (count($pollAnswers) > 0) {
                        $poll->theme = $pollTheme;
                        $poll->anon = ($pollAnon == "true") ? 1 : 0;
                        $poll->save();

                        for ($i = 0; $i < count($pollAnswers); $i++) {
                            if (!empty($pollAnswers[$i])) {
                                $pollAnswer = new entities\PollAnswer();
                                $pollAnswer->poll_id = $poll->id;
                                $pollAnswer->answer = $pollAnswers[$i];
                                $pollAnswer->votes = 0;
                                $pollAnswer->save();
                            }
                        }
                    }
                }

                // Формируем запись
                $post = new entities\Post();
                $post->account_from_id = $accountFromId;
                $post->account_to_id = $accountToId;
                $post->message = $message;
                $post->datetime_add = date("Y-m-d H:i:s");
                $post->video_link = $videoLink;
                $post->files = $files;
                $post->poll_id = (!empty($poll->id)) ? $poll->id : null;
                $post->save();

                // Если указано изображение к посту
                if (!empty($tmpNameImage)) {
                    $post->path_to_image = $this->addPostImage($tmpNameImage, $post->id);
                    $post->save();
                }

                return $post;
            }
        }
        return null;
    }

    // Получить все варианты ответов опроса
    public function getPollAnswers($pollId)
    {
        $answersPoll = array();
        if ($this->getPollById($pollId) != null) {
            $answersPollAll = entities\PollAnswer::all();
            foreach ($answersPollAll as $key => $value) {
                if ($value->poll_id == $pollId) {
                    $answersPoll[] = [
                        'id' => $value->id,
                        'poll_id' => $value->poll_id,
                        'answer' => $value->answer,
                        'votes' => $value->votes
                    ];
                }
            }
        }
        return $answersPoll;
    }

    // Удаление записи
    public function removePost($idPost)
    {
        $post = entities\Post::first($idPost);
        if (!empty($post)) {
            try {
                $pollId = null;
                $pollId = $post->poll_id;
                $pathToImage = $post->path_to_image;
                $post->delete();
                $this->removePhotoByPath($pathToImage);
                // Удалить всё что связано с опросом (если он есть)
                if ($pollId != null) {
                    $poll = entities\Poll::first($pollId);
                    $pollVoted = $this->getPollVotedAccounts($pollId);
                    $pollAnswers = $this->getPollAnswers($pollId);
                    foreach ($pollVoted as $keyV => $valueV) {
                        $pollVoted = entities\PollVoted::first($valueV['id']);
                        $pollVoted->delete();
                    }
                    foreach ($pollAnswers as $keyA => $valueA) {
                        $pollAnswer = entities\PollAnswer::first($valueA['id']);
                        $pollAnswer->delete();
                    }
                    $poll->delete();
                }
                return true;
            } catch (ActiveRecord\ActiveRecordException $e) {
            }
        }
        return false;
    }

    // Получить все записи пользователя по id
    public function getPostsAccountById($idUser, $limit, $offset)
    {
        $limit++;
        $res = array();
        $posts = array();
        try {
            $posts = entities\Post::find('all', array(
                'conditions' => "account_to_id = $idUser",
                'order' => 'id DESC',
                'limit' => $limit,
                'offset' => $offset
            ));
        } catch (ActiveRecord\RecordNotFound $e) {
        }

        // Проверяем, есть ли ещё записи?
        if (count($posts) == $limit) {
            $res['is_there_more_posts'] = true;
            array_pop($posts);
        } else {
            $res['is_there_more_posts'] = false;
        }

        $postsAcc = array();
        foreach ($posts as $key => $value) {
            $postsAcc[] = [
                'id' => $value->id,
                'account_from_id' => $value->account_from_id,
                'account_to_id' => $value->account_to_id,
                'datetime_add' => $value->datetime_add,
                'message' => $value->message,
                'path_to_image' => $value->path_to_image,
                'video_link' => $value->video_link,
                'files' => $value->files,
                'poll_id' => $value->poll_id
            ];
        }
        $res['posts'] = $postsAcc;
        return $res;
    }

    // Получить информацию о постах пользователя в едином массиве
    public function getPostsUser($idUser, $limit, $offset)
    {
        $posts = $this->getPostsAccountById($idUser, $limit, $offset);
        $postsResult = array();
        $postsResult['is_there_more_posts'] = $posts['is_there_more_posts'];
        if (!empty($posts['posts'])) {
            foreach ($posts['posts'] as $key => $value) {
                $accFROM = $this->getAccountAsObject($value['account_from_id']);
                $perInfoFROM = $this->getPersonalInfoById($accFROM->personal_info_id);

                // Получаем файлы
                $filesIds = explode('|', $value['files']);
                $filesArray = array();
                for ($i = 0; $i < count($filesIds); $i++) {
                    if (!empty($filesIds[$i])) {
                        $fileArr = $this->getFileAsArray($filesIds[$i]);
                        if (!empty($fileArr)) {
                            $filesArray[] = $this->getFileUserAsArray($accFROM->id, $fileArr['file_name']);
                        }
                    }
                }

                // Если в записи есть опрос
                $poll = array();
                $pollVoted = array();
                if (!empty($value['poll_id'])) {
                    $poll = $this->getPollById($value['poll_id']);
                    $pollVoted = $this->getPollVotedAccounts($value['poll_id']);
                }

                $postsResult['posts'][] = [
                    'id' => $value['id'],
                    'id_FROM' => $accFROM->id,
                    'first_name_FROM' => $accFROM->first_name,
                    'last_name_FROM' => $accFROM->last_name,
                    'photo_FROM' => self::HOST . $perInfoFROM->photo_path,
                    'status_visit_FROM' => $this->getStatusVisit($accFROM->id),
                    'datetime_add' => date_create($value['datetime_add'])->Format('Y-m-d H:i'),
                    'message' => $value['message'],
                    'path_to_image' => (empty($value['path_to_image'])) ? $value['path_to_image'] : self::HOST . $value['path_to_image'],
                    'id_TO' => $idUser,
                    'video_link' => $value['video_link'],
                    'files' => $filesArray,
                    'poll' => $poll,
                    'poll_voted' => $pollVoted
                ];

            }
            return $postsResult;
        } else {
            return null;
        }
    }

    // Проверить на допустимость загружаемой картинки (расширение её)
    public function checkFileImage($ext)
    {
        $allowed = ConfigDataDB::ALLOWS_IMAGE_EXTENSION;
        if (!in_array($ext, $allowed)) {
            return false;
        }
        return true;
    }

    // Проверить на допустимость загружаемого файла
    public function checkFile($ext)
    {
        $allowed = ConfigDataDB::ALLOWS_FILE_EXTENSION;
        if (!in_array($ext, $allowed)) {
            return false;
        }
        return true;
    }

    // Обновление фотографии профиля
    public function updateProfilePhoto($tmpNameImg, $idUser)
    {
        if (!empty($tmpNameImg) && !empty($idUser)) {
            $acc = $this->getAccountAsObject($idUser);
            if (!empty($acc)) {
                $personalInfo = $this->getPersonalInfoById($acc->personal_info_id);
                if (!empty($personalInfo)) {
                    // Если текущее фото не стандартное, то его можно удалить из б/д
                    if ($personalInfo->photo_path != "db/photos/1.jpg") {
                        $this->removePhotoByPath($personalInfo->photo_path);
                    }
                    $personalInfo->photo_path = $this->updatePhotoProfileTmpImage($tmpNameImg, $acc->id);
                    $personalInfo->save();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Уведомление на почту о смене email
    public function sendNotificationAboutChangeEmail($emailTo)
    {
        $mailer = new PHPMailer();

        $from = 'student.social.network.service@gmail.com';

        $mailer->SMTPDebug = 0;

        // Настройки сервера SMTP
        $mailer->isSMTP();
        $mailer->Host = 'smtp.gmail.com';
        $mailer->Port = 465;
        $mailer->SMTPSecure = 'ssl';
        $mailer->SMTPAuth = true;
        $mailer->Password = 'socialnetwork';
        $mailer->Username = $from;

        try {
            $mailer->smtpConnect(
                array(
                    "ssl" => array(
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                        "allow_self_signed" => true
                    )
                )
            );

            // Отправляемое письмо
            $mailer->setFrom($from); // отправитель
            $mailer->addAddress($emailTo); // получатели
            $mailer->CharSet = "utf-8";
            $mailer->Subject = "Students Social Network - Смена email.";
            $msg = "<h1>Здравствуйте! Вы успешно сменили email на нашем сайте!</h1><p>Теперь ваш новый email для входа на наш сайт является $emailTo</p><p>Спасибо за использование нашего сайта! С уважением администрация Студенческой Социальной Сети.</p>";
            $mailer->msgHTML($msg);
            $mailer->AltBody = strip_tags($msg);

            $mailer->send();
        } catch (Exception $ex) {
            return new Exception($ex->getMessage());
        }
    }

    // Изменить данные профиля
    public function changeProfile(
        $id,
        $firstName,
        $lastName,
        $patronymic,
        $email,
        $gender,
        $phoneNumber,
        $activities,
        $interests,
        $aboutMe,
        $dateBirthday
    ) {
        if (!empty($id) && !empty($firstName) && !empty($lastName) && !empty($patronymic) && !empty($email) &&
            !empty($gender)) {
            $acc = entities\Account::first($id);
            if (!empty($acc)) {
                $oldEmail = $acc->email;

                $acc->first_name = $firstName;
                $acc->last_name = $lastName;
                $acc->patronymic = $patronymic;
                $acc->email = $email;
                $personalInfo = $this->getPersonalInfoById($acc->personal_info_id);
                if (!empty($personalInfo)) {
                    $personalInfo->gender = $gender;
                    $personalInfo->phone_number = $phoneNumber;
                    $personalInfo->activities = $activities;
                    $personalInfo->interests = $interests;
                    $personalInfo->about_me = $aboutMe;
                    $personalInfo->date_birthday = $dateBirthday;
                    $personalInfo->save();
                }
                $acc->save();

                // Если новый email не совпадает со старым, то выслать уведомление на почту о смене email
                if ($oldEmail != $email) {
                    $this->sendNotificationAboutChangeEmail($email);
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Задать новый пароль аккаунту
    public function setNewPasswordToAccount($idUser, $newPassword)
    {
        $acc = $this->getAccountAsObject($idUser);
        if (!empty($acc)) {
            $acc->password_hash = $newPassword;
            $acc->save();
            return true;
        } else {
            return false;
        }
    }

    // Получить все id диалогов (массив) заданного пользователя
    public function getIdsDialogsUser($idUser)
    {
        try {
            $dialogs = entities\Dialog::find('all', array(
                'conditions' => "from_id = $idUser OR to_id=$idUser",
                'order' => 'date_change DESC'
            ));
            $userDialogsIds = array();
            foreach ($dialogs as $key => $value) {
                $userDialogsIds[] = $value->id;
            }
            return $userDialogsIds;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return null;
    }

    // Получить все id диалогов (массив) заданного пользователя
    public function getIdsDialogsUserLimitAndOffset($idUser, $limit = 10, $offset = 0)
    {
        $res = array();
        $limit++; // Увеличиваем на 1, чтобы клиент знал, есть ли ещё записи
        try {
            $dialogs = entities\Dialog::find('all', array(
                'conditions' => "from_id = $idUser OR to_id=$idUser",
                'order' => 'date_change DESC',
                'limit' => $limit,
                'offset' => $offset
            ));
            $userDialogsIds = array();
            foreach ($dialogs as $key => $value) {
                $userDialogsIds[] = $value->id;
            }
            // Проверяем, есть ли ещё записи?
            if (count($userDialogsIds) == $limit) {
                $res['is_there_more'] = true;
                array_pop($userDialogsIds);
            } else {
                $res['is_there_more'] = false;
            }
            $res['result'] = $userDialogsIds;
            return $res;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return null;
    }

    // Получить все id бесед (массив) в которых состоит пользователь
    public function getIdsConversationsUser($idUser)
    {
        try {
            $accConv = entities\AccountConversation::find('all', array('conditions' => "account_id = $idUser"));
            $userConvIds = array();
            foreach ($accConv as $key => $value) {
                $userConvIds[] = $value->conversation_id;
            }
            return $userConvIds;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return null;
    }

    // Получить диалог по его id
    public function getDialogById($id)
    {
        return entities\Dialog::first($id);
    }

    // Получить все сообщения (массив) заданного диалога с лимитом и оффсетом
    public function getMessagesIdsOfDialogLimitAndOffset($idDialog, $limit = 10, $offset = 0)
    {
        $limit++;
        $res = array();
        $messagesUser = array();
        try {
            $messages = entities\Message::find('all', array(
                'conditions' => "dialog_id = $idDialog",
                'order' => 'id DESC',
                'limit' => $limit,
                'offset' => $offset
            ));
            foreach ($messages as $key => $value) {
                $messagesUser[] = $value->id;
            }
            // Проверяем, есть ли ещё записи?
            if (count($messagesUser) == $limit) {
                $res['is_there_more'] = true;
                array_pop($messagesUser);
            } else {
                $res['is_there_more'] = false;
            }
            $messagesUser = array_reverse($messagesUser);
            $res['result'] = $messagesUser;
            return $res;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return $res;
    }

    // Получить все сообщения (массив) заданной беседы с лимитом и оффсетом
    public function getMessagesIdsOfConversationLimitAndOffset($idConversation, $limit = 10, $offset = 0)
    {
        $res = array();
        $limit++;
        $messagesUser = array();
        try {
            $messages = entities\Message::find('all', array(
                'conditions' => "conversation_id = $idConversation",
                'order' => 'id DESC',
                'limit' => $limit,
                'offset' => $offset
            ));
            foreach ($messages as $key => $value) {
                $messagesUser[] = $value->id;
            }
            // Проверяем, есть ли ещё записи?
            if (count($messagesUser) == $limit) {
                $res['is_there_more'] = true;
                array_pop($messagesUser);
            } else {
                $res['is_there_more'] = false;
            }
            $messagesUser = array_reverse($messagesUser);
            $res['result'] = $messagesUser;
            return $res;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return $res;
    }

    // Получить все сообщения (массив) заданного диалога
    public function getMessagesIdsOfDialog($idDialog)
    {
        $messages = entities\Message::all();
        $messagesUser = array();
        foreach ($messages as $key => $value) {
            if ($value->dialog_id == $idDialog) {
                $messagesUser[] = $value->id;
            }
        }
        return $messagesUser;
    }

    // Получить все сообщения (массив) заданной беседы
    public function getMessagesIdsOfConversation($idConversation)
    {
        $messages = entities\Message::all();
        $messagesConv = array();
        foreach ($messages as $key => $value) {
            if ($value->conversation_id == $idConversation) {
                $messagesConv[] = $value->id;
            }
        }
        return $messagesConv;
    }

    // Получить сообщение в виде массива по id
    public function getMessageAsArray($idMessage)
    {
        $msg = entities\Message::first($idMessage);
        if (!empty($msg)) {
            $messageArray = [
                'id' => $msg->id,
                'dialog_id' => $msg->dialog_id,
                'conversation_id' => $msg->conversation_id,
                'sender_id' => $msg->sender_id,
                'recipient_id' => $msg->recipient_id,
                'text' => (!empty($msg->text)) ? $msg->text : "",
                'photo_path' => (!empty($msg->photo_path)) ? self::HOST . $msg->photo_path : "",
                'files' => (!empty($msg->files)) ? $msg->files : "",
                'videoYT' => (!empty($msg->video_youtube)) ? $msg->video_youtube : "",
                'date_send' => $msg->date_send,
                'viewed' => $msg->viewed
            ];
            return $messageArray;
        } else {
            return null;
        }
    }

    // Получить сообщение в виде объекта по id
    public function getMessageAsObject($idMessage)
    {
        $msg = entities\Message::first($idMessage);
        return $msg;
    }

    // Получить диалог по отправителю и получателю (объект)
    public function getDialog($fromId, $toId)
    {
        $dialogs = entities\Dialog::all();
        foreach ($dialogs as $key => $value) {
            if (($value->from_id == $fromId || $value->to_id == $fromId) && ($value->from_id == $toId || $value->to_id == $toId)) {
                return $value;
            }
        }
        return null;
    }

    // Сохранить изображение для сообщения в б/д
    public function addMessageImage($tmpNameImage, $msgId)
    {
        if (!empty($tmpNameImage)) {
            try {
                $path = "db/photos/msg" . $msgId . '.jpg';

                $source = fopen($tmpNameImage, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);

                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Отправить сообщение в диалог
    public function sendMessageToDialog($fromId, $toId, $text, $tmpNameImage, $files, $videoYT)
    {
        $from = $this->getAccountAsObject($fromId);
        $to = $this->getAccountAsObject($toId);

        // Проверяем что такие пользователи существуют
        if (!empty($from) && !empty($to)) {
            // Если сообщение или изображение есть
            if (!empty($text) || !empty($tmpNameImage) || !empty($files) || !empty($videoYT)) {
                $dialog = $this->getDialog($fromId, $toId);
                $date = date("Y-m-d H:i:s");
                // Если диалога нет, то создаем его
                if (empty($dialog)) {
                    $dialogNew = new entities\Dialog();
                    $dialogNew->from_id = $fromId;
                    $dialogNew->to_id = $toId;
                    $dialogNew->date_change = $date;
                    $dialogNew->save();
                    $dialog = $dialogNew;
                }

                $msgNew = new entities\Message();
                $msgNew->dialog_id = $dialog->id;
                $msgNew->sender_id = $fromId;
                $msgNew->recipient_id = $toId;
                $msgNew->text = $text;
                $msgNew->photo_path = '';
                $msgNew->files = $files;
                $msgNew->video_youtube = $videoYT;
                $msgNew->date_send = $date;
                $msgNew->viewed = false;
                $msgNew->save();

                // Добавить дату последнего сообщения в диалоге
                $dialog->date_change = $date;
                $dialog->save();

                if (!empty($tmpNameImage)) {
                    $msgNew->photo_path = $this->addMessageImage($tmpNameImage, $msgNew->id);
                    $msgNew->save();
                }

                return $msgNew;
            }
        }
        return null;
    }

    // Отправить сообщение в беседу
    public function sendMessageToConversation($fromId, $conversationId, $text, $tmpNameImage, $files, $videoYT)
    {
        $from = $this->getAccountAsObject($fromId);
        $conversation = $this->getConversationAsObject($conversationId);

        // Проверяем что пользователь сущесвует, и беседа тоже
        if (!empty($from) && !empty($conversation)) {
            // Если сообщение или изображение есть
            if (!empty($text) || !empty($tmpNameImage) || !empty($files) || !empty($videoYT)) {

                $currentDate = date("Y-m-d H:i:s");

                $msgNew = new entities\Message();
                $msgNew->conversation_id = $conversation->id;
                $msgNew->sender_id = $fromId;
                $msgNew->text = $text;
                $msgNew->photo_path = '';
                $msgNew->files = $files;
                $msgNew->video_youtube = $videoYT;
                $msgNew->date_send = $currentDate;
                $msgNew->viewed = false;
                $msgNew->save();

                // Создаем записи в таблице непрочитанных сообщений для каждого участника
                $members = $this->getMembersOfConversationAsArrayIds($conversationId);
                foreach ($members as $key => $value) {
                    if ($value != $fromId) {
                        $msgNoViewed = new entities\MessageNoViewed();
                        $msgNoViewed->conversation_id = $conversationId;
                        $msgNoViewed->message_id = $msgNew->id;
                        $msgNoViewed->recipient_account_id = $value;
                        $msgNoViewed->save();
                    }
                }

                // Добавить дату последнего сообщения в беседе
                $conversation->date_change = $currentDate;
                $conversation->save();

                if (!empty($tmpNameImage)) {
                    $msgNew->photo_path = $this->addMessageImage($tmpNameImage, $msgNew->id);
                    $msgNew->save();
                }

                return $msgNew;
            }
        }
        return null;
    }

    // Получить последнее сообщение (id) диалога
    public function getLastMessageIdOfDialog($idDialog)
    {
        $dialog = entities\Dialog::first($idDialog);
        if (!empty($dialog)) {
            $messagesOfDialog = $this->getMessagesIdsOfDialog($idDialog);
            if (count($messagesOfDialog) > 0) {
                return end($messagesOfDialog);
            }
        }
        return null;
    }

    // Получить последнее сообщение (id) беседы
    public function getLastMessageIdOfConversation($idConversation)
    {
        $conversation = entities\Conversation::first($idConversation);
        if (!empty($conversation)) {
            $messagesOfConversation = $this->getMessagesIdsOfConversation($idConversation);
            if (count($messagesOfConversation) > 0) {
                return end($messagesOfConversation);
            }
        }
        return null;
    }

    // Проверить что заданный диалог принадлежит пользователю
    public function checkDialogUser($idUser, $idDialog)
    {
        $acc = $this->getAccountAsObject($idUser);
        if (!empty($acc)) {
            $dlg = $this->getDialogById($idDialog);
            if (!empty($dlg)) {
                if ($dlg->from_id == $idUser || $dlg->to_id == $idUser) {
                    return true;
                }
            }
        }
        return false;
    }

    // Проверить, существует ли пользователь в беседе
    public function checkConversationUser($idUser, $idConversation)
    {
        $acc = $this->getAccountAsObject($idUser);
        if ($acc != null) {
            $conv = $this->getConversationAsObject($idConversation);
            if ($conv != null) {
                if ($conv->account_author_id == $idUser) {
                    return true;
                }
                $members = $this->getMembersOfConversationAsArrayIds($idConversation);
                return in_array($idUser, $members);
            }
        }
        return false;
    }

    // Получить всех участников беседы (в виде массива id)
    public function getMembersOfConversationAsArrayIds($idConversation)
    {
        $res = $this->getAccountConversationByConversationId($idConversation);
        $members = array();
        if (null != $res) {
            foreach ($res as $key => $value) {
                $members[] = $value->account_id;
            }
        }
        return $members;
    }

    // Получить все account_conversation по id беседы
    public function getAccountConversationByConversationId($idConversation)
    {
        $conv = $this->getConversationAsObject($idConversation);
        if ($conv != null) {
            return entities\AccountConversation::find('all',
                array('conditions' => array('conversation_id' => $idConversation)));
        }
        return null;
    }

    // Получить account_conversation по account_id и conversation_id
    public function getAccountConversationOne($conversationId, $accountId)
    {
        $conv = $this->getConversationAsObject($conversationId);
        if ($conv != null) {
            return entities\AccountConversation::first('all',
                array(
                    'conditions' => array(
                        'conversation_id' => $conversationId,
                        'account_id' => $accountId
                    )
                ));
        }
        return null;
    }

    // Получить account_conversation (связь аккаунт-беседа) (участник беседы) по id
    public function getAccountConversationAsObject($accountConversationId)
    {
        return entities\AccountConversation::first($accountConversationId);
    }

    // Проверить существование диалога
    public function checkExistsDialog($idDialog)
    {
        return entities\Dialog::exists($idDialog);
    }

    // Проверить существование беседы
    public function checkExistsConversation($idConversation)
    {
        return entities\Conversation::exists($idConversation);
    }
    // Получить непрочитанные сообщения в диалоге. Указывается id диалога, и id пользователя для которого
    // нужно вычислить новые сообщения
    /*public function getNewMessages($idDialog, $idUser)
    {
        $allMessages = $this->getMessagesIdsOfDialog($idDialog);
        $messages = array();
        foreach ($allMessages as $key => $value)
        {
            $msg = $this->getMessageAsObject($value);
            if($msg->viewed==0)
            {
                if($msg->recipient_id==$idUser)
                {
                    $messages[] = $value;
                }
            }
        }
        return $messages;
    }*/
    // Получить новые (непрочитанные) сообщения диалога
    public function getNewMessagesOfDialog($idDialog, $idAccount)
    {
        try {
            $messages = entities\Message::find('all',
                array(
                    'conditions' => array(
                        'dialog_id' => $idDialog,
                        'viewed' => 0,
                        'recipient_id' => $idAccount
                    )
                ));
            $arrayResult = array();
            /*if($messages!=null){
                $arrayResult = array_map(function($res){
                    return $res->attributes();
                }, $messages);
            }*/
            for ($i = 0; $i < count($messages); $i++) {
                $arrayResult[] = [
                    'id' => $messages[$i]->id,
                    'dialog_id' => $messages[$i]->dialog_id,
                    'sender_id' => $messages[$i]->sender_id,
                    'recipient_id' => $messages[$i]->recipient_id,
                    'text' => $messages[$i]->text,
                    'photo_path' => ($messages[$i]->photo_path != null) ? self::HOST . $messages[$i]->photo_path : "",
                    'date_send' => $messages[$i]->date_send,
                    'viewed' => $messages[$i]->viewed,
                    'files' => $messages[$i]->files,
                    'video_youtube' => $messages[$i]->video_youtube
                ];
            }

            // Преобразовываем ids файлов в их содержание
            for ($i = 0; $i < count($arrayResult); $i++) {
                if (!empty($arrayResult[$i]['files'])) {
                    $filesIds = explode("|", $arrayResult[$i]['files']);
                    $filesArr = array();
                    foreach ($filesIds as $keyF => $valueF) {
                        if (!empty($valueF)) {
                            $filesArr[] = $this->getFileAsArray($valueF);
                        }
                    }
                    $arrayResult[$i]['files'] = $filesArr;
                }
            }

            return $arrayResult;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return array();
    }

    // Получить новые (непрочитанные) сообщения беседы
    public function getNewMessagesOfConversation($idConversation, $idAccount)
    {
        try {
            $messagesNoViewed = entities\MessageNoViewed::find('all',
                array(
                    'conditions' => array(
                        'conversation_id' => $idConversation,
                        'recipient_account_id' => $idAccount
                    )
                ));
            $arrayResult = array();


            for ($i = 0; $i < count($messagesNoViewed); $i++) {
                $arrayResultElement = array();
                // Получаем подробную инфу по сообщению
                $msg = $this->getMessageAsArray($messagesNoViewed[$i]->message_id);
                if (null != $msg) {
                    $msg['message_photo_path'] = (!empty($msg['photo_path'])) ? $msg['photo_path'] : "";
                    $msg['message_text'] = $msg['text'];
                    $msg['date_send'] = date_create($msg['date_send'])->Format('Y-m-d H:i');
                    $arrayResultElement = $msg;
                }
                // Получаем подробную инфу по отправителю
                $sender = $this->getAccountAsArray($msg['sender_id']);
                if (null != $sender) {
                    $senderPerInfo = $this->getPersonalInfoByIdAsArray($sender['personal_info_id']);
                    if (null != $senderPerInfo) {
                        $arrayResultElement['sender_id'] = $sender['id'];
                        $arrayResultElement['sender_photo'] = $senderPerInfo['photo_path'];
                        $arrayResultElement['sender_first_name'] = $sender['first_name'];
                        $arrayResultElement['sender_last_name'] = $sender['last_name'];
                    }
                }
                $arrayResult[] = $arrayResultElement;
            }

            // Преобразовываем ids файлов в их содержание
            for ($i = 0; $i < count($arrayResult); $i++) {
                if (!empty($arrayResult[$i]['files'])) {
                    $filesIds = explode("|", $arrayResult[$i]['files']);
                    $filesArr = array();
                    foreach ($filesIds as $keyF => $valueF) {
                        if (!empty($valueF)) {
                            $filesArr[] = $this->getFileAsArray($valueF);
                        }
                    }
                    $arrayResult[$i]['files'] = $filesArr;
                }
            }
            return $arrayResult;
        } catch (\Exception $ex) {
        }
        return array();
    }

    // Сделать прочитанными все сообщения диалога для пользователя
    public function readAllMessagesDialog($idDialog, $idRecipient)
    {
        $msgs = $this->getMessagesIdsOfDialog($idDialog);
        foreach ($msgs as $key => $value) {
            $msg = $this->getMessageAsObject($value);
            if ($msg->recipient_id == $idRecipient) {
                $msg->viewed = 1;
                $msg->save();
            }
        }
    }

    // Сделать прочитанными все сообщения беседы для пользователя
    public function readAllMessagesConversation($idConversation, $idRecipient)
    {
        try {
            $messagesNoViewed = entities\MessageNoViewed::find('all',
                array(
                    'conditions' => array(
                        'conversation_id' => $idConversation,
                        'recipient_account_id' => $idRecipient
                    )
                ));
            for ($i = 0; $i < count($messagesNoViewed); $i++) {
                $messagesNoViewed[$i]->delete();
            }
            return true;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return false;
    }

    // Получить кол-во непрочитанных диалогов пользователя
    public function getCountNotViewedDialogsMessages($idUser)
    {
        $count = 0;

        // Получаем кол-во непрочитанных диалогов
        $dlgs = $this->getIdsDialogsUser($idUser);
        foreach ($dlgs as $key => $value) {
            $dlgNewMsgs = $this->getNewMessagesOfDialog($value, $idUser);
            if (!empty($dlgNewMsgs)) {
                $count = $count + 1;
            }
        }

        return $count;
    }

    // Получить кол-во непрочитанных бесед пользователя
    public function getCountNotViewedConversationsMessages($idUser)
    {
        $count = 0;

        // Получаем все беседы в которых состоит пользователь (ids)
        $conversations = $this->getIdsConversationsUser($idUser);
        foreach ($conversations as $key => $value) {
            $convNewMsgs = $this->getNewMessagesOfConversation($value, $idUser);
            if (!empty($convNewMsgs)) {
                $count = $count + 1;
            }
        }

        return $count;
    }

    // Получить id`s друзей пользователя с limit и offset
    public function getFavoritesIdsOfUserLimitAndOffset($idUser, $limit = 10, $offset = 0)
    {
        $limit++;
        $res = array();
        $favoritesOfUser = array();
        try {
            $favorites = entities\Favorite::find('all', array(
                'conditions' => "user_id = $idUser",
                'order' => 'id ASC',
                'limit' => $limit,
                'offset' => $offset
            ));
            foreach ($favorites as $key => $value) {
                $favoritesOfUser[] = $value->favorite_id;
            }

            // Проверяем, есть ли ещё записи?
            if (count($favoritesOfUser) == $limit) {
                $res['is_there_more'] = true;
                array_pop($favoritesOfUser);
            } else {
                $res['is_there_more'] = false;
            }

            $res['favorites'] = $favoritesOfUser;
            return $res;
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return null;
    }

    // Получить id`s друзей пользователя
    public function getFavoritesIdsOfUser($idUser)
    {
        $allFavorites = entities\Favorite::all();
        $favoritesOfUser = array();
        foreach ($allFavorites as $key => $value) {
            if ($value->user_id == $idUser) {
                $favoritesOfUser[] = $value->favorite_id;
            }
        }
        return $favoritesOfUser;
    }

    // Проверить добавлен пользователь в друзья, или нет
    public function checkExistsFavorite($idUser, $idFavorite)
    {
        if ($this->accountIdExists($idUser) && $this->accountIdExists($idFavorite)) {
            $favorites = $this->getFavoritesIdsOfUser($idUser);
            return in_array($idFavorite, $favorites);
        }
        return false;
    }

    // Добавить пользователя в друзья
    public function addUserToFavorite($idUser, $idFavorite)
    {
        if ($this->accountIdExists($idUser) && $this->accountIdExists($idFavorite)) {
            if (!$this->checkExistsFavorite($idUser, $idFavorite)) {
                $newFavorite = new entities\Favorite();
                $newFavorite->user_id = $idUser;
                $newFavorite->favorite_id = $idFavorite;
                $newFavorite->save();
                return true;
            }
        }
        return false;
    }

    // Удалить пользователя из друзей
    public function removeUserFromFavorites($idUser, $idFavorite)
    {
        if ($this->accountIdExists($idUser) && $this->accountIdExists($idFavorite)) {
            if ($this->checkExistsFavorite($idUser, $idFavorite)) {
                $allFavorites = entities\Favorite::all();
                foreach ($allFavorites as $key => $value) {
                    if ($value->user_id == $idUser && $value->favorite_id == $idFavorite) {
                        $favorite = entities\Favorite::first($value->id);
                        $favorite->delete();
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // Получить информацию об избранных для пользователя в пригодном для html виде
    public function getFavoritesForHtml($idUser, $limit = 10, $offset = 0)
    {
        if ($this->accountIdExists($idUser)) {
            $favorites = $this->getFavoritesIdsOfUserLimitAndOffset($idUser, $limit, $offset);
            $favoritesHtml = array();
            foreach ($favorites['favorites'] as $key => $value) {
                $accountFav = $this->getAccountAsObject($value);
                $personalInfoFav = $this->getPersonalInfoById($accountFav->personal_info_id);
                $favoritesHtml['favorites'][] = [
                    'user_favorite_id' => $value,
                    'first_name' => $accountFav->first_name,
                    'last_name' => $accountFav->last_name,
                    'photo_path' => self::HOST . $personalInfoFav->photo_path,
                    'status_visit' => $this->getStatusVisit($accountFav->id)
                ];
            }
            $favoritesHtml['is_there_more_favorites'] = $favorites['is_there_more'];
            return $favoritesHtml;
        }
        return null;
    }

    // Сохранить фото в файл (фото к альбому)
    public function addAlbumImage($tmpNameImage, $idPhoto)
    {
        if (!empty($tmpNameImage)) {
            try {
                $path = "db/photos/photo" . $idPhoto . '.jpg';

                $source = fopen($tmpNameImage, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);

                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Сохранить файл в папку с другими файлами
    public function addFile($tmpNameFile, $nameFile, $idAccount, $ext)
    {
        if (!empty($tmpNameFile)) {
            try {
                if (!file_exists('db/files/' . $idAccount)) {
                    mkdir('db/files/' . $idAccount, 0777, true);
                }
                $path = "db/files/" . $idAccount . '/' . $nameFile;

                $source = fopen($tmpNameFile, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);

                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Загрузить фото в альбом
    public function addPhotoToAlbum($idUser, $tmpNameFile, $description)
    {
        if (!empty($idUser) && !empty($tmpNameFile)) {
            if ($this->accountIdExists($idUser)) {
                $newPhoto = new entities\Photo();
                $newPhoto->account_id = $idUser;
                $newPhoto->description = $description;
                $newPhoto->datetime_add = date("Y-m-d H:i:s");
                $newPhoto->path = ' ';
                $newPhoto->save();

                $newPhoto->path = $this->addAlbumImage($tmpNameFile, $newPhoto->id);
                $newPhoto->save();
                return true;
            }
        }
        return false;
    }

    // Загрузить файл
    public function loadFile($idUser, $tmpNameFile, $nameFile, $ext)
    {
        if (!empty($idUser) && !empty($tmpNameFile)) {
            if ($this->accountIdExists($idUser)) {
                $newFile = new entities\File();
                $newFile->account_id = $idUser;
                $newFile->file_name = $nameFile;
                $newFile->datetime_add = date("Y-m-d H:i:s");
                $newFile->path = ' ';
                $newFile->file_size_bytes = 0;
                $newFile->save();

                $newFile->path = $this->addFile($tmpNameFile, $nameFile, $idUser, $ext);
                $newFile->file_size_bytes = $this->getFileSizeBytes($newFile->path);
                $newFile->save();
                return true;
            }
        }
        return false;
    }

    // Удалить файл
    public function removeFile($idUser, $fileName)
    {
        if (!empty($this->accountIdExists($idUser))) {
            if ($this->checkFileExists($idUser, $fileName)) {
                $file = $this->getFileUserAsArray($idUser, $fileName);
                $fileObj = entities\File::first($file['id']);
                $pathToFile = $fileObj->path;
                if ($fileObj->delete()) {
                    if ($this->removeFileByPath($pathToFile)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // Удалить файл по его пути
    public function removeFileByPath($path)
    {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    // Проверить, есть ли файл у пользователя с таким именем
    public function checkFileExists($idUser, $fileName)
    {
        if ($this->accountIdExists($idUser)) {
            $file = $this->getFileUserAsArray($idUser, $fileName);
            if (!empty($file)) {
                return true;
            }
        }
        return false;
    }

    // Получить размер файла (в байтах) по его пути
    public function getFileSizeBytes($pathToFile)
    {
        $size = 0;
        if (!empty($pathToFile)) {
            if (file_exists($pathToFile)) {
                $size = filesize($pathToFile);
            }
        }
        return $size;
    }

    // Получить файл пользователя как массив
    public function getFileUserAsArray($idUser, $fileName)
    {
        $files = $this->getFiles();
        $file = null;
        foreach ($files as $key => $value) {
            if ($value->file_name == $fileName && $value->account_id == $idUser) {
                $file['id'] = $value->id;
                $file['account_id'] = $value->account_id;
                $file['file_name'] = $value->file_name;
                $file['datetime_add'] = date_create($value->datetime_add)->Format('Y-m-d H:i');
                $file['path'] = ControlsAPI::HOST . $value->path;
                $file['file_size_bytes'] = $value->file_size_bytes;
                return $file;
            }
        }
        return $file;
    }

    // Получить все файлы пользователя как массив
    public function getFilesOfUserAsArray($idUser)
    {
        $filesUser = array();
        if (!empty($this->accountIdExists($idUser))) {
            $files = $this->getFiles();
            foreach ($files as $key => $value) {
                if ($value->account_id == $idUser) {
                    $filesUser[] = [
                        'id' => $value->id,
                        'account_id' => $value->account_id,
                        'file_name' => $value->file_name,
                        'datetime_add' => date_create($value->datetime_add)->Format('Y-m-d H:i'),
                        'path' => ControlsAPI::HOST . $value->path,
                        'file_size_bytes' => $value->file_size_bytes
                    ];
                }
            }
        }
        return $filesUser;
    }

    // Получить файл как массив
    public function getFileAsArray($fileId)
    {
        $fileArray = array();
        try {
            $file = entities\File::first($fileId);
            if (!empty($file)) {
                $fileArray['id'] = $file->id;
                $fileArray['account_id'] = $file->account_id;
                $fileArray['file_name'] = $file->file_name;
                $fileArray['datetime_add'] = date_create($file->datetime_add)->Format('Y-m-d H:i');
                $fileArray['path'] = ControlsAPI::HOST . $file->path;
                $fileArray['file_size_bytes'] = $file->file_size_bytes;
            }
        } catch (\Exception $ex) {
        }
        return $fileArray;
    }

    // Получить все файлы (как объекты)
    public function getFiles()
    {
        return entities\File::all();
    }

    // Проверить что фото принадлежит пользователю
    public function checkOwnPhoto($idPhoto, $idUser)
    {
        if (!empty($idUser) && !empty($idPhoto)) {
            if ($this->accountIdExists($idUser)) {
                $photo = entities\Photo::first($idPhoto);
                if (!empty($photo)) {
                    if ($photo->account_id == $idUser) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // Удаление фото из альбома
    public function removePhotoFromAlbum($idPhoto, $idUser)
    {
        if ($this->checkOwnPhoto($idPhoto, $idUser)) {
            $photo = entities\Photo::first($idPhoto);
            $pathToImage = $photo->path;
            $photo->delete();
            $this->removePhotoByPath($pathToImage);
            return true;
        }
        return false;
    }

    // Получить все фото пользователя. В виде массива id
    public function getPhotosOfUser($idUser)
    {
        $photosAll = entities\Photo::all();
        $photosUser = array();
        foreach ($photosAll as $key => $value) {
            if ($value->account_id == $idUser) {
                $photosUser[] = $value->id;
            }
        }
        return $photosUser;
    }

    // Получить информацию о фото ввиде массива
    public function getPhotoAsArray($idPhoto)
    {
        $photo = entities\Photo::first($idPhoto);
        if (!empty($photo)) {
            $photoArr = [
                'id' => $photo->id,
                'description' => $photo->description,
                'datetime_add' => $photo->datetime_add,
                'account_id' => $photo->account_id,
                'path' => self::HOST . $photo->path
            ];
            return $photoArr;
        }
        return null;
    }

    // Получить настройки приватности в виде массива
    public function getPrivacyOfUser($idUser)
    {
        if ($this->accountIdExists($idUser)) {
            $acc = entities\Account::first($idUser);
            $settings = entities\Settings::first($acc->settings_id);
            $privacy = entities\Privacy::first($settings->privacy_id);
            return [
                'view_my_posts' => $privacy->view_my_posts,
                'write_post' => $privacy->write_post
            ];
        }
        return null;
    }

    // Сохранить настройки приватности
    public function savePrivacyOfUser($idUser, $write_post)
    {
        if ($this->accountIdExists($idUser)) {
            $acc = entities\Account::first($idUser);
            $settings = entities\Settings::first($acc->settings_id);
            $privacy = entities\Privacy::first($settings->privacy_id);
            //$privacy->view_my_posts = $view_my_posts;
            $privacy->write_post = $write_post;
            $privacy->save();
            return true;
        }
        return false;
    }

    // Получить результаты поиска по людям. Возвращает id аккаунтов
    public function getResultSearchByUsers($query_search)
    {
        $users = $this->getAccounts();
        $resultSearchUsers = array();
        foreach ($users as $key => $value) {
            $join = ' ' . $value->first_name . ' ' . $value->last_name . ' ' . $value->patronymic . ' ';
            $res = strripos($join, $query_search);
            if ($res !== false) {
                $resultSearchUsers[] = $value;
            }
        }
        return $resultSearchUsers;
    }

    // Получить группу по id как объект
    public function getGroupById($id)
    {
        return entities\Group::first($id);
    }

    // Добавить фото новости в папку db/photos
    public function addNewsImage($tmpNameImage, $idNews)
    {
        if (!empty($tmpNameImage)) {
            try {
                $path = "db/photos/news" . $idNews . '.jpg';

                $source = fopen($tmpNameImage, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);

                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Добавление новости
    public function addNews(
        $idAuthor,
        $theme,
        $description,
        $imageTmp,
        $videoLink,
        $eventDate,
        $eventDescription,
        $pollTheme,
        $pollAnswers,
        $pollAnon
    ) {
        try {
            if (!empty($idAuthor) && !empty($theme) && !empty($description)) {
                if ($this->accountIdExists($idAuthor)) {
                    // Если указан опрос, то добавляем все его ответы и сам опрос
                    $pollId = null;
                    if ($pollTheme != null && $pollAnswers != null && $pollAnon != null) {
                        $poll = new entities\Poll();
                        $poll->theme = $pollTheme;
                        $poll->anon = ($pollAnon == 'true') ? 1 : 0;
                        $poll->save();

                        $pollId = $poll->id;
                        foreach ($pollAnswers as $key => $value) {
                            $pollAnswer = new entities\PollAnswer();
                            $pollAnswer->poll_id = $pollId;
                            $pollAnswer->answer = $value;
                            $pollAnswer->votes = 0;
                            $pollAnswer->save();
                        }
                    }

                    $news = new entities\News();
                    $news->author_id = $idAuthor;
                    $news->theme = $theme;
                    $news->description = $description;
                    $news->datetime_add = date("Y-m-d H:i:s");
                    $news->video_link = $videoLink;
                    $news->event_date = $eventDate;
                    $news->event_description = $eventDescription;
                    $news->poll_id = ($pollId != null) ? $pollId : null;
                    $news->save();

                    // Если указано изображение
                    if (!empty($imageTmp)) {
                        $news->image_path = $this->addNewsImage($imageTmp, $news->id);
                        $news->save();
                    }


                    return true;
                }
            }
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    // Удаление новости
    public function removeNews($idNews)
    {
        $news = entities\News::first($idNews);
        if (!empty($news)) {
            $pollId = ($news->poll_id != null) ? $news->poll_id : null;
            $pathToImage = $news->image_path;
            try {
                $news->delete();
                $this->removePhotoByPath($pathToImage);

                // Удалить всё что связано с опросом (если он есть)
                if ($pollId != null) {
                    $poll = entities\Poll::first($pollId);
                    $pollVoted = $this->getPollVotedAccounts($pollId);
                    $pollAnswers = $this->getPollAnswers($pollId);
                    foreach ($pollVoted as $keyV => $valueV) {
                        $pollVoted = entities\PollVoted::first($valueV['id']);
                        $pollVoted->delete();
                    }
                    foreach ($pollAnswers as $keyA => $valueA) {
                        $pollAnswer = entities\PollAnswer::first($valueA['id']);
                        $pollAnswer->delete();
                    }
                    $poll->delete();
                }

                return true;
            } catch (ActiveRecord\ActiveRecordException $e) {
            }
        }
        return false;
    }

    // Получить одну новость (по ИД)
    public function getOneNews($id)
    {
        try {
            $news = entities\News::first($id);
            if ($news != null) {

                $poll = null;
                $pollVoted = null;
                if ($news->poll_id != null) {
                    $poll = $this->getPollById($news->poll_id);
                    $pollVoted = $this->getPollVotedAccounts($news->poll_id);
                }

                return [
                    'id' => $news->id,
                    'author_id' => $news->author_id,
                    'theme' => $news->theme,
                    'description' => $news->description,
                    'image_path' => $news->image_path,
                    'datetime_add' => date_create($news->datetime_add)->Format('Y-m-d H:i'),
                    'video_link' => $news->video_link,
                    'event_date' => ($news->event_date == null) ? null : date_create($news->event_date)->Format('Y-m-d H:i'),
                    'event_description' => $news->event_description,
                    'poll' => $poll,
                    'poll_voted' => $pollVoted
                ];
            }
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return null;
    }

    // Получить все новости (в виде массива)
    public function getAllNews($limit, $offset)
    {
        $res = array();
        $limit++;
        $newsObj = array();
        try {
            $newsObj = entities\News::find('all', array(
                'order' => 'id DESC',
                'limit' => $limit,
                'offset' => $offset
            ));
        } catch (ActiveRecord\RecordNotFound $e) {
        }

        // Проверяем, есть ли ещё записи?
        if (count($newsObj) == $limit) {
            $res['is_there_more'] = true;
            array_pop($newsObj);
        } else {
            $res['is_there_more'] = false;
        }

        $news = array();
        foreach ($newsObj as $key => $value) {
            $newsOne = [
                'id' => $value->id,
                'author_id' => $value->author_id,
                'theme' => $value->theme,
                'description' => $value->description,
                'image_path' => (empty($value->image_path)) ? null : self::HOST . $value->image_path,
                'datetime_add' => date_create($value->datetime_add)->Format('Y-m-d H:i'),
                'video_link' => $value->video_link,
                'event_date' => ($value->event_date == null) ? null : date_create($value->event_date)->Format('Y-m-d H:i'),
                'event_description' => $value->event_description
            ];
            $poll = ($value->poll_id != null) ? $this->getPollById($value->poll_id) : null;
            if ($poll != null) {
                $newsOne['poll'] = $poll;
            }
            $news[] = $newsOne;
        }
        $res['news'] = $news;
        return $res;
    }

    // Получить новость (в виде массива)
    public function getNews($idNews)
    {
        $news = entities\News::first($idNews);
        if (!empty($news)) {
            $newsArray = [
                'id' => $news->id,
                'author_id' => $news->author_id,
                'theme' => $news->theme,
                'description' => $news->description,
                'image_path' => $news->image_path,
                'datetime_add' => date_create($news->datetime_add)->Format('Y-m-d H:i'),
                'poll_id' => $news->poll_id
            ];
            return $newsArray;
        }
        return null;
    }

    // Заблокировать аккаунт
    public function blockAccount($idAcc)
    {
        if ($this->accountIdExists($idAcc)) {
            $acc = entities\Account::first($idAcc);
            $acc->blocked = true;
            $acc->save();
            return true;
        }
        return false;
    }

    // Разблокировать аккаунт
    public function unblockAccount($idAcc)
    {
        if ($this->accountIdExists($idAcc)) {
            $acc = entities\Account::first($idAcc);
            $acc->blocked = false;
            $acc->save();
            return true;
        }
        return false;
    }

    // Проверить, заблокирован ли аккаунт
    public function checkBlockAccount($idAcc)
    {
        if ($this->accountIdExists($idAcc)) {
            $acc = entities\Account::first($idAcc);
            if ($acc->blocked) {
                return true;
            }
        }
        return false;
    }

    // Получить список событий (id, event_date)
    public function getAllEvents()
    {
        $eventsResult = array();
        try {
            $events = entities\News::find('all', array('conditions' => "event_date != ''"));
            foreach ($events as $key => $value) {
                $eventsResult[] = [
                    'id' => $value->id,
                    'event_date' => $value->event_date,//date_create($value->event_date)->Format('Y-m-d H:i')
                ];
            }
        } catch (ActiveRecord\RecordNotFound $e) {
        }
        return $eventsResult;
    }

    // Создать группу
    public function createGroup($name)
    {
        if (!empty($name)) {
            if (!$this->groupExists($name)) {
                $group = new entities\Group();
                $group->name = $name;
                $group->save();
                return true;
            } else {
                return false;
            }
        }
    }

    // Переименовать группу
    public function renameGroup($nameOld, $nameNew)
    {
        if (!empty($nameOld) && !empty($nameNew)) {
            $group = $this->getGroupByName($nameOld);
            if (!empty($group)) {
                $group->name = $nameNew;
                $group->save();
                return true;
            }
        }
        return false;
    }

    // Переместить пользователя в другую группу
    public function moveUserToDiferentGroup($idUser, $nameNewGroup)
    {
        if (!empty($idUser) && !empty($nameNewGroup)) {
            $user = entities\Account::first($idUser);
            $group = $this->getGroupByName($nameNewGroup);
            if (!empty($user) && !empty($group)) {
                $user->group_id = $group->id;
                $user->save();
                return true;
            }
        }
        return false;
    }

    // Получить запись (пост)
    public function getPost($id)
    {
        return entities\Post::first($id);
    }

    // Получить массив проголосовавших за конкретный опрос (по его id)
    function getPollVotedAccounts($idPoll)
    {
        $votedAccounts = [];
        if ($this->getPollById($idPoll) != null) {
            $pollVoted = entities\PollVoted::all();
            foreach ($pollVoted as $key => $value) {
                if ($value->poll_id == $idPoll) {
                    $votedAccounts[] = [
                        'id' => $value->id,
                        'account_id' => $value->account_id,
                        'poll_id' => $value->poll_id,
                        'answer_id' => $value->answer_id
                    ];
                }
            }
        }
        return $votedAccounts;
    }

    // Проверить существование варианта ответа опроса (по id записи)(в записях профиля)
    function checkAnswerOfPollExistsInPost($postId, $answerId)
    {
        if ($this->getPost($postId) != null) {
            $post = $this->getPost($postId);
            if ($post->poll_id != null) {
                $poll = $this->getPollById($post->poll_id);
                if (!empty($poll)) {
                    foreach ($poll['answers'] as $key => $value) {
                        if ($value['id'] == $answerId) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    // Проверить существование варианта ответа опроса (по id записи)(в новостях)
    function checkAnswerOfPollExistsInNews($newsId, $answerId)
    {
        if ($this->getNews($newsId) != null) {
            $news = $this->getNews($newsId);
            if ($news['poll_id'] != null) {
                $poll = $this->getPollById($news['poll_id']);
                if (!empty($poll)) {
                    foreach ($poll['answers'] as $key => $value) {
                        if ($value['id'] == $answerId) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    // Отменить голос (пользователя) в опросе
    function cancelVotePoll($accountId, $pollId)
    {
        if ($this->getAccountAsObject($accountId) != null) {
            if ($this->getPollById($pollId) != null) {
                $votesPoll = $this->getPollVotedAccounts($pollId);
                $pollVoted = null;
                foreach ($votesPoll as $key => $value) {
                    if ($value['account_id'] == $accountId) {
                        $pollVoted = entities\PollVoted::first($value['id']);
                        break;
                    }
                }
                if ($pollVoted != null) {
                    try {
                        $pollAnswer = entities\PollAnswer::first($pollVoted->answer_id);
                        $pollAnswer->votes = $pollAnswer->votes - 1;
                        $pollVoted->delete();
                        $pollAnswer->save();
                        return true;
                    } catch (ActiveRecord\ActiveRecordException $e) {
                    }
                }
            }
        }
        return false;
    }

    // Проверить проголосовал ли пользователь
    function checkVotedPoll($accountId, $pollId)
    {
        $votes = $this->getPollVotedAccounts($pollId);
        foreach ($votes as $key => $value) {
            if ($value['account_id'] == $accountId) {
                return true;
            }
        }
        return false;
    }

    // Проголосовать в опросе (в записях профиля)
    function votePollInPost($accountId, $postId, $answerId)
    {
        if ($this->getPost($postId) != null) {
            $post = $this->getPost($postId);
            if ($this->checkAnswerOfPollExistsInPost($postId, $answerId) != null) {
                $answerOfPoll = entities\PollAnswer::first($answerId);
                if ($answerOfPoll != null) {
                    $answerOfPoll->votes = $answerOfPoll->votes + 1;
                    $answerOfPoll->save();

                    $votedPoll = new entities\PollVoted();
                    $votedPoll->account_id = $accountId;
                    $votedPoll->poll_id = $post->poll_id;
                    $votedPoll->answer_id = $answerOfPoll->id;
                    $votedPoll->save();
                    return true;
                }
            }
        }
        return false;
    }

    // Проголосовать в опросе (в новостях)
    function votePollInNews($accountId, $newsId, $answerId)
    {
        if ($this->getNews($newsId) != null) {
            $news = $this->getNews($newsId);
            if ($this->checkAnswerOfPollExistsInNews($newsId, $answerId) != null) {
                $answerOfPoll = entities\PollAnswer::first($answerId);
                if ($answerOfPoll != null) {
                    $answerOfPoll->votes = $answerOfPoll->votes + 1;
                    $answerOfPoll->save();

                    $votedPoll = new entities\PollVoted();
                    $votedPoll->account_id = $accountId;
                    $votedPoll->poll_id = $news['poll_id'];
                    $votedPoll->answer_id = $answerOfPoll->id;
                    $votedPoll->save();
                    return true;
                }
            }
        }
        return false;
    }

    // Получить опрос (как массив с ответами) по его id
    function getPollById($id)
    {
        $poll = array();
        if (!empty(entities\Poll::first($id))) {
            $pollObj = entities\Poll::first($id);

            $poll['id'] = $pollObj->id;
            $poll['theme'] = $pollObj->theme;
            $poll['anon'] = $pollObj->anon;

            $pollAnswers = entities\PollAnswer::all();
            foreach ($pollAnswers as $key => $value) {
                if ($value->poll_id == $pollObj->id) {
                    $poll['answers'][] = [
                        'id' => $value->id,
                        'poll_id' => $value->poll_id,
                        'answer' => $value->answer,
                        'votes' => $value->votes
                    ];
                }
            }
        }
        return $poll;
    }

    // Получить запись (пост) как массив
    public function getPostAsArray($id)
    {
        $post = entities\Post::first($id);
        if ($post != null) {
            $filesIds = explode("|", $post->files);
            $filesArr = array();
            foreach ($filesIds as $keyF => $valueF) {
                if (!empty($valueF)) {
                    $filesArr[] = $this->getFileAsArray($valueF);
                }
            }

            // Если в записи есть опрос
            $poll = array();
            if (!empty($post->poll_id)) {
                $poll = $this->getPollById($post->poll_id);
            }

            return [
                'id' => $post->id,
                'account_from_id' => $post->account_from_id,
                'account_to_id' => $post->account_to_id,
                'datetime_add' => date_create($post->datetime_add)->Format('Y-m-d H:i'),
                'message' => $post->message,
                'path_to_image' => (empty($post->path_to_image)) ? $post->path_to_image : self::HOST . $post->path_to_image,
                'video_link' => $post->video_link,
                'files' => $filesArr,
                'poll' => $poll
            ];
        }
        return null;
    }

    // Получить массив проголосовавших за вариант ответа в опросе (по id варианта ответа)
    public function getPollAnswerVoted($answerId)
    {
        $voted = array();
        $votedAll = entities\PollVoted::all();
        foreach ($votedAll as $key => $value) {
            if ($value->answer_id == $answerId) {
                $acc = $this->getAccountAsArray($value->account_id);
                $personalInfo = $this->getPersonalInfoById($acc['personal_info_id']);
                $acc['status_visit'] = $this->getStatusVisit($acc['id']);
                $acc['photo_path'] = self::HOST . $personalInfo->photo_path;
                $voted[] = $acc;
            }
        }
        return $voted;
    }

    // Получить вариант ответа опроса по его id
    public function getPollAnswer($pollAnswerId)
    {
        return entities\PollAnswer::first($pollAnswerId);
    }

    // Получить все беседы (как объекты)
    public function getConversationsAsObjects()
    {
        return entities\Conversation::all();
    }

    // Получить беседу (как объект)
    public function getConversationAsObject($id)
    {
        return entities\Conversation::first($id);
    }

    // Получить все связи аккаунт-беседа (как объекты)
    public function getAccountsConversationsAsObjects()
    {
        return entities\AccountConversation::all();
    }

    // Создание беседы
    public function createConversation($creatorId, $name, $membersArrayIds, $imageBase64)
    {
        $res = null;
        $creatorAcc = $this->getAccountAsObject($creatorId);
        if ($creatorAcc != null) {
            if (!empty($name)) {
                if (!empty($membersArrayIds)) {
                    if (count($membersArrayIds) > 0) {
                        $photoPath = '';
                        //$photoPath = 'db/photos/conversation.jpg';

                        // Создаем беседу
                        $conversation = new entities\Conversation();
                        $conversation->account_author_id = $creatorAcc->id;
                        $conversation->name = $name;
                        $conversation->date_change = date("Y-m-d H:i:s");
                        $conversation->created = date("Y-m-d H:i:s");
                        $conversation->photo_path = $photoPath;
                        $conversation->save();

                        // Сохраняем изображение в папку
                        if (empty($imageBase64)) {
                            $photoPath = 'db/photos/conversation.jpg';
                        } else {
                            $photoPath = $this->addConversationImage($imageBase64, $conversation->id);
                        }
                        $conversation->photo_path = $photoPath;
                        $conversation->save();

                        // Отправляем в беседу приветствующее сообщение от создателя
                        $msgHi = new entities\Message();
                        $msgHi->conversation_id = $conversation->id;
                        $msgHi->sender_id = $creatorAcc->id;
                        $msgHi->text = "Приветствую всех!";
                        $msgHi->date_send = date("Y-m-d H:i:s");
                        $msgHi->photo_path = '';
                        $msgHi->save();

                        // Добавляем создателя как участника в беседу
                        $accConv = new entities\AccountConversation();
                        $accConv->account_id = $creatorAcc->id;
                        $accConv->conversation_id = $conversation->id;
                        $accConv->save();

                        // Добавляем участников в беседу
                        foreach ($membersArrayIds as $key => $value) {
                            if (!empty($value)) {
                                $member = $this->getAccountAsObject($value);
                                if ($member != null) {
                                    // Добавляем участников в промежуточную таблицу account_conversation
                                    $accountConversation = new entities\AccountConversation();
                                    $accountConversation->account_id = $member->id;
                                    $accountConversation->conversation_id = $conversation->id;
                                    $accountConversation->save();

                                    // Отправляем уведомление всем участникам об создании беседы
                                    $msgNoViewed = new entities\MessageNoViewed();
                                    $msgNoViewed->conversation_id = $conversation->id;
                                    $msgNoViewed->message_id = $msgHi->id;
                                    $msgNoViewed->recipient_account_id = $member->id;
                                    $msgNoViewed->save();
                                }
                            }
                        }

                        $res = $conversation->id;
                    }
                }
            }
        }
        return $res;
    }

    // Удалить всех участников беседы (кроме создателя)
    public function deleteAllMembersConversation($conversationId)
    {
        try {
            $conv = $this->getConversationAsObject($conversationId);
            if (null != $conv) {
                $membersIds = $this->getMembersOfConversationAsArrayIds($conversationId);
                foreach ($membersIds as $memberId) {
                    if ($memberId != $conv->account_author_id) {
                        $accConv = $this->getAccountConversationOne($conversationId, $memberId);
                        if (null != $accConv) {
                            $accConv->delete();
                        }
                    }
                }
                return true;
            }
        } catch (\Exception $ex) {
        }
        return false;
    }

    // Обновить список участников беседы
    public function changeMembersConversation($conversationId, $membersArrayIds)
    {
        $res = null;
        $conversation = $this->getConversationAsObject($conversationId);
        if (null != $conversation) {
            if (!empty($membersArrayIds)) {
                if (count($membersArrayIds) > 0) {

                    $addedMembersIds = array(); // Добавленные участники

                    // Старые участники беседы
                    $oldMembersIds = $this->getMembersOfConversationAsArrayIds($conversationId);

                    // Находим участников, которые были добавлены
                    foreach ($membersArrayIds as $memberArrayId) {
                        // Если в списке старых участников нет id нового
                        if (!in_array($memberArrayId, $oldMembersIds)) {
                            $addedMembersIds[] = $memberArrayId;
                        }
                    }

                    // Находим участников, которые были удалены
                    foreach ($oldMembersIds as $oldMemberId) {
                        // Если в новом списке участников нет старого участника, и этот участник не создатель,
                        // значит он был удалён
                        if (!in_array($oldMemberId,
                                $membersArrayIds) && $conversation->account_author_id != $oldMemberId) {
                            $removedMember = $this->getAccountAsObject($oldMemberId);
                            // Отправляем сообщение всем в беседу, что выгнали участника (сообщение
                            // отправляется от имени создателя беседы
                            $this->sendMessageToConversation($conversation->account_author_id, $conversationId,
                                "Исключил(а) из беседы пользователя " . $removedMember->first_name . " " . $removedMember->last_name,
                                null, null, null);
                        }
                    }

                    // Удаляем старых участников беседы
                    if ($this->deleteAllMembersConversation($conversationId)) {
                        // Добавляем обновленный список участников в беседу
                        foreach ($membersArrayIds as $key => $value) {
                            if (!empty($value)) {
                                $member = $this->getAccountAsObject($value);
                                if (null != $member) {
                                    // Если пользователь ещё не добавлен в беседу, то добавляем
                                    $accConv = $this->getAccountConversationOne($conversationId, $value);
                                    if (null == $accConv) {
                                        // Добавляем участников в промежуточную таблицу account_conversation
                                        $accountConversation = new entities\AccountConversation();
                                        $accountConversation->account_id = $member->id;
                                        $accountConversation->conversation_id = $conversation->id;
                                        $accountConversation->save();

                                        // Если прибыл новый участник
                                        if (in_array($member->id, $addedMembersIds)) {
                                            // Отправляем сообщение всем в беседу, что прибыл новый участник (сообщение
                                            // отправляется от его имени
                                            $this->sendMessageToConversation($member->id, $conversationId,
                                                "Был(а) добавлен(а) в беседу", null, null, null);
                                        }
                                    }
                                }
                            }
                        }

                        $res = $conversation->id;
                    }
                }
            }
        }
        return $res;
    }

    // Добавить основное фото беседы в папку db/photos
    public function addConversationImage($imageBase64, $idConversation)
    {
        if (!empty($imageBase64)) {
            try {
                $path = "db/photos/conversation" . $idConversation . '.jpg';

                $source = fopen($imageBase64, 'r');
                $destination = fopen($path, 'w');

                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);

                return $path;
            } catch (\Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    // Проверить, является ли пользователь автором (создателем) беседы
    public function checkAuthorConversation($accountId, $conversationId)
    {
        $conv = $this->getConversationAsObject($conversationId);
        if (null != $conv) {
            return $conv->account_author_id == $accountId;
        }
        return false;
    }

    // Получить массив записей непросмотреных сообщений по id беседы
    public function getMessagesNoViewedByConversationId($conversationId)
    {
        $conv = $this->getConversationAsObject($conversationId);
        if (null != $conv) {
            try {
                return entities\MessageNoViewed::find('all',
                    array('conditions' => array('conversation_id' => $conversationId)));
            } catch (ActiveRecord\RecordNotFound $e) {
            }
        }
        return null;
    }

    // Удаление беседы
    public function removeConversation($conversationId)
    {
        try {
            $conv = $this->getConversationAsObject($conversationId);
            if (null != $conv) {
                // Удаляем записи не просмотренных сообщений в беседе
                $msgsNoViewedIds = $this->getMessagesNoViewedByConversationId($conversationId);
                for ($i = 0; $i < count($msgsNoViewedIds); $i++) {
                    if (!$msgsNoViewedIds[$i]->delete()) {
                        return false;
                    }
                }
                // Удаляем все сообщения с беседы
                $msgs = $this->getMessagesIdsOfConversation($conversationId);
                for ($i = 0; $i < count($msgs); $i++) {
                    $msg = $this->getMessageAsObject($msgs[$i]);
                    if (null != $msg) {
                        if (!empty($msg->photo_path)) {
                            if (!$this->removePhotoByPath($msg->photo_path)) {
                                return false;
                            }
                        }
                    }
                    if (!$msg->delete()) {
                        return false;
                    }
                }
                // Удаляем участников беседы из неё
                $members = $this->getAccountConversationByConversationId($conversationId);
                for ($i = 0; $i < count($members); $i++) {
                    if (!$members[$i]->delete()) {
                        return false;
                    }
                }
                // Удаляем аватарку беседы
                if (!$this->removePhotoByPath($conv->photo_path)) {
                    return false;
                }
                // Удаляем беседу
                if (!$conv->delete()) {
                    return false;
                }
                return true;
            }
        } catch (ActiveRecord\ActiveRecordException $e) {
        }
        return false;
    }

    // Переименовать название беседы
    public function renameConversation($conversationId, $newName)
    {
        $conv = $this->getConversationAsObject($conversationId);
        if (null != $conv) {
            $conv->name = $newName;
            return $conv->save();
        }
        return false;
    }

    // Обновить фото беседы
    public function refreshPhotoConversation($conversationId, $photoBase64)
    {
        $conv = $this->getConversationAsObject($conversationId);
        if (null != $conv) {
            // Удаляем текущее фото
            $this->removePhotoByPath($conv->photo_path);
            // Устанавливаем новое фото
            $conv->photo_path = $this->updatePhotoConversationTmpImage($photoBase64, $conv->id);
            $conv->save();
            return true;
        }
        return false;
    }

    // Покинуть беседу
    public function toLeaveOfConversation($conversationId, $accountId)
    {
        try {
            $conv = $this->getConversationAsObject($conversationId);
            $acc = $this->getAccountAsObject($accountId);
            if (null != $conv && null != $acc) {
                $accConv = $this->getAccountConversationOne($conversationId, $accountId);
                if (null != $accConv) {
                    $this->sendMessageToConversation($acc->id, $conversationId,
                        "Покинул(а) беседу",
                        null, null, null);
                    return $accConv->delete();
                }
            }
        } catch (\Exception $ex) {
        }
        return false;
    }
}