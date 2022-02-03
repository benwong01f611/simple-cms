<?php
    class Permission{
        const permissions = array(
            "page"      =>  array(
                                    "add"           => 1,
                                    "edit"          => 2,
                                    "pub_unpub"     => 4,
                                    "delete"        => 8,
                                    "history"       => 16
                            ),
            "template"  =>  array(
                                    "add"           => 1,
                                    "edit"          => 2,
                                    "delete"        => 4,
                                    "history"       => 8
                            ),
            "account"   =>  array(
                                    "password"          => 1,
                                    "passwordother"     => 2,
                                    "en_dis"            => 4,
                                    "add"               => 8,
                                    "delete"            => 16,
                                    "editpermission"    => 1073741824
                            ),
            "site"      =>  array(
                                    "settings"          => 1
                            ),
        );

        private $userPermission = array(
            "page"      => 0,
            "template"  => 0,
            "account"   => 0,
            "site"      => 0,
        );

        private $username = "";

        public function __construct($user){
            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
            $user = connectDB("SELECT username FROM user WHERE username=\"" . $user . "\";");
            if(count($user)){
                $userPermissiontemp = connectDB("SELECT page, template, account, site FROM userPermission WHERE username=\"" . $user[0]["username"] . "\";")[0];
                $this->userPermission["page"]       = (int)$userPermissiontemp["page"];
                $this->userPermission["template"]   = (int)$userPermissiontemp["template"];
                $this->userPermission["account"]    = (int)$userPermissiontemp["account"];
                $this->userPermission["site"]       = (int)$userPermissiontemp["site"];
                unset($userPermissiontemp);
                $this->username = $user;
            }
            else
                throw new Exception("Error: User not found");
        }

        public function permissionCheck($category, $permission){
            return ($this->userPermission[$category] & self::permissions[$category][$permission]) != 0;
        }

        public function getUser(){
            return $this->username;
        }
        public function getPermission($type){
            return $this->userPermission[$type];
        }
    }
    

    
?>

