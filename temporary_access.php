<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class Temporary_Access extends Module{

    public function __construct(){
        $this->name = 'temporary_access';
        $this->tab = 'dashboard';
        $this->version = '1.0.0';
        $this->author = 'TheEnumbin';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Temporary Login Access for PrestaShop');
        $this->description = $this->l('Create Temporary Access For Your PrestaShop Site to Make Access Sharing More Secured.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->define_constants();
    }

    public function install(){
        parent::install();
        $this->registerHook( 'displayBackOfficeHeader' );
        $this->registerHook( 'displayHeader' );
        $this->registerHook( 'displayDashboardTop' );
        $this->installTab();
        include_once TEMPACCESS_DATA_PATH . 'install_sql.php';
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
       
        if(!defined('TEMPACCESS_CLASSES_PATH')){
            define('TEMPACCESS_CLASSES_PATH', _PS_MODULE_DIR_ . 'temporary_access/classes/');
        }
        if(!defined('TEMPACCESS_DATA_PATH')){
            define('TEMPACCESS_DATA_PATH', _PS_MODULE_DIR_ . 'temporary_access/data/');
        }
    }

    private function installTab(){
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminTempaccess';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Temporary Access');
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('CONFIGURE');
        $tab->module = $this->name;

        $tab->add();
    }

    public function uninstall_tab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminTempaccess');
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
        if($controller == "AdminTempaccess"){
            $id_temp = Tools::getValue("id_tempaccess");
            if($id_temp){
                include_once TEMPACCESS_CLASSES_PATH . 'tempaccess.php';
                $access = tempaccess::get_access_to_copy($id_temp);
                $link = Context::getContext()->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_);
                echo '<pre>'; 
                echo 'Admin Url: ' . $link . '<br>';
                echo 'Email: ' . $access['temp_email'] . '<br>';
                echo 'Password: ' . $access['password'];
                echo '</pre>';                 
            }
        }
    }

    private function change_expired_access(){
        $checked_date = Configuration::get('TEMPACCESS_CHECK_DATE');
        $todate = date("Y-m-d");
        if($checked_date != $todate){
            include_once TEMPACCESS_CLASSES_PATH . 'tempaccess.php';
            $mail = tempaccess::get_temp_email_by_date($todate);
            if($mail){
                $changed = tempaccess::change_expired_password($mail);
                if($changed){
                    Configuration::updateValue('TEMPACCESS_CHECK_DATE', $todate);
                }
            }
        }
    }
}