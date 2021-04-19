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

        $this->displayName = $this->l('Temporary Site Access for PrestaShop');
        $this->description = $this->l('Create Temporary Access of Your PrestaShop Site to Make Access Sharing More Secured.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->define_constants();
    }

    public function install(){
        parent::install();
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

}