<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

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
		// if ( ! ( $classyproductextratabs = $this->loadObject( true ) ) ) {
		// 	return;
		// }
		$this->fields_form['submit'] = array(
			'title' => $this->l( 'Save And Close' ),
			'class' => 'btn btn-default pull-right',
		);

		return parent::renderForm();
	}

    public function initContent(){
        parent::initContent();
    }
}
