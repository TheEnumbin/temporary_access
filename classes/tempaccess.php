<?php

if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

/**
 * Classyproductextratab object model class for extra tab objects.
 */
class tempaccess extends ObjectModel {




	/**
	 * id_tempaccess id of the item.
	 *
	 * @var mixed
	 */
	public $id_tempaccess;

	/**
	 * First Name
	 *
	 * @var mixed
	 */
	public $first_name;

	/**
	 * Last Name
	 *
	 * @var mixed
	 */
	public $last_name;

	/**
	 * Email
	 *
	 * @var mixed
	 */
	public $temp_email;

	/**
	 * Password
	 *
	 * @var mixed
	 */
	public $password;

	/**
	 * Role
	 *
	 * @var mixed
	 */
	public $id_role;

	/**
	 * Expire date
	 *
	 * @var mixed
	 */
	public $expire_date;

	/**
	 * Active
	 *
	 * @var int
	 */
	public $active = 1;

	/**
	 * Position
	 *
	 * @var mixed
	 */
	public $position;

	public static $definition = array(
		'table'     => 'tempaccess',
		'primary'   => 'id_tempaccess',
		'multilang' => false,
		'fields'    => array(
			'first_name'                 => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
				'required' => true,
			),
			'last_name'                 => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
				'required' => true,
			),
			'temp_email'                 => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
				'required' => true,
			),
			'password'                 => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
				'required' => true,
			),
			'id_role'     			=> array( 'type' => self::TYPE_INT ),
			'expire_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'active'                => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
				'required' => true,
			),
			'position'     			=> array( 'type' => self::TYPE_INT ),
		),
	);


	/**
	 * __construct
	 *
	 * @param  mixed $id      id of the tab.
	 * @param  mixed $id_lang laguage id.
	 * @param  mixed $id_shop id of the shop.
	 * @return void
	 */
	public function __construct( $id = null, $id_lang = null, $id_shop = null ) {
		parent::__construct( $id, $id_lang, $id_shop );
	}

	/**
	 * Add
	 *
	 * @param mixed $autodate    automatically add the date.
	 * @param mixed $null_values if accept null values.
	 */
	public function add( $autodate = true, $null_values = false ) {

		if ( $this->position <= 0 ) {
			$this->position = self::getHigherPosition() + 1;
		}
		if ( ! parent::add( $autodate, $null_values ) || ! Validate::isLoadedObject( $this ) ) {
			return false;
		}

		return true;
	}

	/**
	 * GetHigherPosition gets the higher position.
	 */
	public static function getHigherPosition() {
		$sql      = 'SELECT MAX(`position`)
                FROM `' . _DB_PREFIX_ . 'tempaccess`';
		$position = DB::getInstance()->getValue( $sql );
		return ( is_numeric( $position ) ) ? $position : -1;
	}

	/**
	 * GetInstance provides the instance of the class.
	 */
	public static function GetInstance() {
		$ins = new tempaccess();
		return $ins;
	}

	/**
	 * UpdatePosition updates the osition of the class.
	 *
	 * @param mixed $way      update way.
	 * @param mixed $position postion of the item.
	 */
	public function updatePosition( $way, $position ) {
		if ( ! $res = Db::getInstance()->executeS(
			'
            SELECT `id_tempaccess`, `position`
            FROM `' . _DB_PREFIX_ . 'tempaccess`
            ORDER BY `position` ASC'
		)
		) {
			return false;
		}
		foreach ( $res as $tempaccess ) {
			if ( (int) $tempaccess['id_tempaccess'] == (int) $this->id ) {
				$moved_tempaccess = $tempaccess;
			}
		}
		if ( ! isset( $moved_tempaccess ) || ! isset( $position ) ) {
			return false;
		}
		$query_1 = ' UPDATE `' . _DB_PREFIX_ . 'tempaccess`
        SET `position`= `position` ' . ( $way ? '- 1' : '+ 1' ) . '
        WHERE `position`
        ' . ( $way
		? '> ' . (int) $moved_tempaccess['position'] . ' AND `position` <= ' . (int) $position
		: '< ' . (int) $moved_tempaccess['position'] . ' AND `position` >= ' . (int) $position . '
        ' );
		$query_2 = ' UPDATE `' . _DB_PREFIX_ . 'tempaccess`
        SET `position` = ' . (int) $position . '
        WHERE `id_tempaccess` = ' . (int) $moved_tempaccess['id_tempaccess'];
		return ( Db::getInstance()->execute( $query_1 )
		&& Db::getInstance()->execute( $query_2 ) );
	}

	public static function get_temp_email_by_date($date){
		$sql = "SELECT `temp_email` FROM `"._DB_PREFIX_."tempaccess`  WHERE DATE(expire_date) <= '$date'";
		$result = Db::getInstance()->getValue($sql);	
		return $result;
	}

	public static function change_expired_password($mail){
		$str = "MNO5668PQ5268WX587YZ";
		$newpass = str_shuffle($str);
		$new_data = array('passwd'=>$newpass);
		$where = "email = '" . $mail . "'";
		$result = Db::getInstance()->update('employee', $new_data, $where );
		return $result;
	}

	public static function is_temporary($mail){
		$sql = "SELECT * FROM `"._DB_PREFIX_."tempaccess`  WHERE temp_email = '$mail'";
		$result = Db::getInstance()->executeS($sql);
		if(isset($result) && !empty($result)){
			return false;
		}else{
			return true;
		}
	}

	public static function get_access_to_copy($id){
		$sql = "SELECT `temp_email`, `password` FROM `"._DB_PREFIX_."tempaccess`  WHERE id_tempaccess = '$id'";
		$result = Db::getInstance()->executeS($sql);	
		return $result[0];
	}
}
