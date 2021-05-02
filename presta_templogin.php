<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class Presta_Templogin extends Module{

    public function __construct(){
        $this->name = 'presta_templogin';
        $this->tab = 'dashboard';
        $this->version = '1.0.0';
        $this->author = 'TheEnumbin';
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Presta Temporary Login Password');
        $this->description = $this->l('With this module you can create employee account with temporary password which ensures you wont have to change the password after the work is done.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->define_constants();
    }

    public function install(){
        parent::install();
        $this->registerHook( 'displayBackOfficeHeader' );
        $this->registerHook( 'displayHeader' );
        $this->registerHook( 'displayDashboardTop' );
        $this->installTab();
        include_once PRESTATEMPL_DATA_PATH . 'install_sql.php';
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstall_tab() ) {
            return false;
        }

        return true;
    }


    private function define_constants(){
       
        if(!defined('PRESTATEMPL_CLASSES_PATH')){
            define('PRESTATEMPL_CLASSES_PATH', _PS_MODULE_DIR_ . 'presta_templogin/classes/');
        }
        if(!defined('PRESTATEMPL_DATA_PATH')){
            define('PRESTATEMPL_DATA_PATH', _PS_MODULE_DIR_ . 'presta_templogin/data/');
        }
    }

    private function installTab(){
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminTempaccount';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Temporary Accounts');
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('CONFIGURE');
        $tab->module = $this->name;

        $tab->add();
    }

    public function uninstall_tab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminTempaccount');
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        }

        return false;
    }

    public function hookDisplayBackOfficeHeader(){
        $this->change_expired_access();
    }

    public function hookdisplayHeader(){
        $this->change_expired_access();
    }

    public function hookDisplayDashboardTop(){
        $controller = Tools::getValue("controller");
        if($controller == "AdminTempaccount"){
            $id_temp = Tools::getValue("id_tempaccount");
            if($id_temp){
                include_once PRESTATEMPL_CLASSES_PATH . 'tempaccount.php';
                $access = tempaccount::get_access_to_copy($id_temp);
                $link = Context::getContext()->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_);
                echo '<pre>'; 
                echo 'Admin Url: ' . $link . '<br>';
                echo 'Email: ' . $access['tempaccount_email'] . '<br>';
                echo 'Password: ' . $access['tempaccount_pass'];
                echo '</pre>';                 
            }
        }
    }

    private function change_expired_access(){
        $checked_date = Configuration::get('PRESTATEMPL_CHECK_DATE');
        $todate = date("Y-m-d");
        if($checked_date != $todate){
            include_once PRESTATEMPL_CLASSES_PATH . 'tempaccount.php';
            $mail = tempaccount::get_tempaccount_email_by_date($todate);
            if($mail){
                $changed = tempaccount::change_expired_password($mail);
                if($changed){
                    Configuration::updateValue('PRESTATEMPL_CHECK_DATE', $todate);
                }
            }
        }
    }
}