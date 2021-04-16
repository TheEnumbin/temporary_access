<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;

class AdminTempaccessController extends ModuleAdminController{
    
    public function __construct(){

        $this->table            = 'tempaccess';
		$this->className        = 'tempaccess';
		$this->lang             = false;
		$this->module           = 'temporary_access';
		$this->_defaultOrderBy  = 'position';
		$this->bootstrap        = true;
		$this->_defaultOrderWay = 'DESC';
		$this->context          = Context::getContext();

		parent::__construct();

		include_once TEMPACCESS_CLASSES_PATH . 'tempaccess.php';

		$this->fields_list = array(
			'id_tempaccess' => array(
				'title'   => $this->l( 'Id' ),
				'width'   => 100,
				'type'    => 'text',
				'orderby' => false,
				'filter'  => false,
				'search'  => false,
			),
			'name'       => array(
				'title'   => $this->l( 'Name' ),
				'width'   => 440,
				'type'    => 'text',
				'orderby' => false,
				'filter'  => false,
				'search'  => false,
			),
            'temp_email'  => array(
				'title'   => $this->l( 'Email' ),
				'width'   => 440,
				'type'    => 'text',
				'orderby' => false,
				'filter'  => false,
				'search'  => false,
			),
			'active'                    => array(
				'title'   => $this->l( 'Status' ),
				'width'   => 270,
				'align'   => 'center',
				'active'  => 'status',
				'type'    => 'bool',
				'orderby' => false,
				'filter'  => false,
				'search'  => false,
			),
			'position'                  => array(
				'title'      => $this->l( 'Position' ),
				'filter_key' => 'a!position',
				'position'   => 'position',
			),
		);

    }

	/**
	 * RenderList Renders the whole list.
	 */
	public function renderList() {
		if ( isset( $this->_filter ) && trim( $this->_filter ) == '' ) {
			$this->_filter = $this->original_filter;
		}

		$this->addRowAction( 'edit' );
		$this->addRowAction( 'delete' );
		return parent::renderList();
	}

	/**
	 * RenderForm renders the create and edit form.
	 *
	 * @return void
	 */
	public function renderForm() {
		$tempaccess_is_edit         = false;
		
		$edit_all                     = true;


		$this->fields_form = array(
			'legend'  => array(
				'title' => $this->l( 'Add Temporary Access Account' ),
			),
			'input'   => array(
				array(
					'type'     => 'text',
					'label'    => $this->l( 'Name' ),
					'name'     => 'name',
					'required' => true,
					'desc'     => $this->l( 'Enter The Name' ),
				),
				array(
					'type'     => 'text',
					'label'    => $this->l( 'Email' ),
					'name'     => 'temp_email',
					'required' => true,
					'desc'     => $this->l( 'Enter The Email' ),
				),
				array(
					'type'     => 'text',
					'label'    => $this->l( 'Password' ),
					'name'     => 'password',
					'required' => true,
					'desc'     => $this->l( 'Enter The Email' ),
				),
				array(
					'type'     => 'select',
					'label'    => $this->l( 'Role' ),
					'name'     => 'id_role',
					'required' => true,
					'options' => array(
						'query' => array(
                            array(
								'id' => 'a',
								'name' => 'A',
							),
							array(
								'id' => 'b',
								'name' => 'B',
							),
							array(
								'id' => 'c',
								'name' => 'C',
							),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
					'desc'     => $this->l( 'Select Role for The User' ),
				),
				array(
					'type'     => 'date',
					'label'    => $this->l( 'Expire Date' ),
					'name'     => 'expire_date',
					'required' => true,
				),
				array(
					'type'     => 'switch',
					'label'    => $this->l( 'Status' ),
					'name'     => 'active',
					'required' => false,
					'class'    => 't',
					'is_bool'  => true,
					'values'   => array(
						array(
							'id'    => 'active',
							'value' => 1,
							'label' => $this->l( 'Enabled' ),
						),
						array(
							'id'    => 'active',
							'value' => 0,
							'label' => $this->l( 'Disabled' ),
						),
					),
				),
			),
			'submit'  => array(
				'title' => $this->l( 'Save And Close' ),
				'class' => 'btn btn-default pull-right',
			),
			'buttons' => array(
				'save-and-stay' => array(
					'name'  => 'submitAdd' . $this->table . 'AndStay',
					'type'  => 'submit',
					'title' => $this->l( 'Save And Stay' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'process-icon-save',
				),
			),
		);

		$this->fields_form['submit'] = array(
			'title' => $this->l( 'Save And Close' ),
			'class' => 'btn btn-default pull-right',
		);

		return parent::renderForm();
	}

	public function processAdd()
    {
        $name = Tools::getValue('name');
        $temp_email = Tools::getValue('temp_email');
        $password = Tools::getValue('password');
        try {
            /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
            $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
        } catch (CoreException $e) {
            return false;
        }

		$password = $crypto->hash($password);
        $id_role = Tools::getValue('id_role');

		$insert_query = "INSERT INTO `" . _DB_PREFIX_ . "employee` (`id_profile`, `id_lang`, `lastname`, `firstname`, `email`, `passwd`, `last_passwd_gen`, `stats_date_from`, `stats_date_to`, `stats_compare_from`, `stats_compare_to`, `stats_compare_option`, `preselect_date_range`, `bo_color`, `bo_theme`, `bo_css`, `default_tab`, `bo_width`, `bo_menu`, `active`, `optin`, `id_last_order`, `id_last_customer_message`, `id_last_customer`, `last_connection_date`, `reset_password_token`, `reset_password_validity`) VALUES
(1, 1, 'Eight', '$name', '$temp_email', '$password', '2020-12-26 12:54:46', '2020-11-27', '2020-12-27', '0000-00-00', '0000-00-00', 1, NULL, NULL, 'default', 'theme.css', 1, 0, 1, 1, 1, 0, 0, 0, '2020-12-28', NULL, '0000-00-00 00:00:00');";


// echo $insert_query . '<br>';
// echo __FILE__ . ' ' . __LINE__; 

		Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_query);
		
		

        return parent::processAdd();
    }

    public function initContent(){
        parent::initContent();
    }
}
