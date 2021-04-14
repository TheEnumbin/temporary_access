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


    public function initContent(){
        parent::initContent();
    }
}
