<?php

if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

/**
 * Classyproductextratab object model class for extra tab objects.
 */
class tempaccess extends ObjectModel {




	/**
	 * Id_classyproductextratabs id of the item.
	 *
	 * @var mixed
	 */
	public $id_tempaccess;

	/**
	 * Name
	 *
	 * @var mixed
	 */
	public $name;

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
			'content_type'          => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'modules_list'          => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'module_hook_list'      => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'product_page'          => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'specific_product'      => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'specific_product_catg' => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'position'              => array( 'type' => self::TYPE_INT ),
			'active'                => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
				'required' => true,
			),
			'title'                 => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isString',
				'required' => true,
			),
			'content'               => array(
				'type'     => self::TYPE_HTML,
				'lang'     => true,
				'validate' => 'isString',
			),
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
		Shop::addTableAssociation( 'classyproductextratabs', array( 'type' => 'shop' ) );
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
                FROM `' . _DB_PREFIX_ . 'classyproductextratabs`';
		$position = DB::getInstance()->getValue( $sql );
		return ( is_numeric( $position ) ) ? $position : -1;
	}

	/**
	 * GetInstance provides the instance of the class.
	 */
	public static function GetInstance() {
		$ins = new classyproductextratab();
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
            SELECT `id_classyproductextratabs`, `position`
            FROM `' . _DB_PREFIX_ . 'classyproductextratabs`
            ORDER BY `position` ASC'
		)
		) {
			return false;
		}
		foreach ( $res as $classyproductextratabs ) {
			if ( (int) $classyproductextratabs['id_classyproductextratabs'] == (int) $this->id ) {
				$moved_classyproductextratabs = $classyproductextratabs;
			}
		}
		if ( ! isset( $moved_classyproductextratabs ) || ! isset( $position ) ) {
			return false;
		}
		$query_1 = ' UPDATE `' . _DB_PREFIX_ . 'classyproductextratabs`
        SET `position`= `position` ' . ( $way ? '- 1' : '+ 1' ) . '
        WHERE `position`
        ' . ( $way
		? '> ' . (int) $moved_classyproductextratabs['position'] . ' AND `position` <= ' . (int) $position
		: '< ' . (int) $moved_classyproductextratabs['position'] . ' AND `position` >= ' . (int) $position . '
        ' );
		$query_2 = ' UPDATE `' . _DB_PREFIX_ . 'classyproductextratabs`
        SET `position` = ' . (int) $position . '
        WHERE `id_classyproductextratabs` = ' . (int) $moved_classyproductextratabs['id_classyproductextratabs'];
		return ( Db::getInstance()->execute( $query_1 )
		&& Db::getInstance()->execute( $query_2 ) );
	}


	/**
	 * GetTabContentByProductId gets the tab contents by product id.
	 *
	 * @param mixed $id_product id of the product.
	 */
	public function GetTabContentByProductId( $id_product = 1 ) {
		$reslt       = array();
		$resltcat    = array();
		$id_lang     = (int) Context::getContext()->language->id;
		$id_shop     = (int) Context::getContext()->shop->id;
		$sql         = 'SELECT * FROM `' . _DB_PREFIX_ . 'classyproductextratabs` v 
                INNER JOIN `' . _DB_PREFIX_ . 'classyproductextratabs_lang` vl ON (v.`id_classyproductextratabs` = vl.`id_classyproductextratabs` AND vl.`id_lang` = ' . $id_lang . ')
                INNER JOIN `' . _DB_PREFIX_ . 'classyproductextratabs_shop` vs ON (v.`id_classyproductextratabs` = vs.`id_classyproductextratabs` AND vs.`id_shop` = ' . $id_shop . ')
                WHERE ';
		$sql        .= ' v.`active` = 1 ORDER BY v.`position` ASC';
		$sqlcat      = 'SELECT `id_category` FROM `' . _DB_PREFIX_ . 'category_product` 
                		WHERE `id_product`=' . $id_product;
		$cache_id    = md5( $sql );
		$cachecat_id = md5( $sqlcat );

		if ( ! Cache::isStored( $cache_id ) ) {
			$resultcats = Db::getInstance()->executeS( $sqlcat );
			if ( isset( $resultcats ) && ! empty( $resultcats ) ) {

				foreach ( $resultcats as $i => $result ) {

					$resltcat[] = $result['id_category'];
				}
			}
		}

		if ( ! Cache::isStored( $cache_id ) ) {
			$results = Db::getInstance()->executeS( $sql );
			if ( isset( $results ) && ! empty( $results ) ) {

				foreach ( $results as $i => $result ) {
					if ( isset( $result['product_page'] ) && $result['product_page'] == 1 ) {
						$reslt[ $i ] = $result;
					} elseif ( isset( $result['specific_product_catg'] ) && $result['specific_product_catg'] != '' ) {

						$specific_product_catg_arr = explode( '-', $result['specific_product_catg'] );
						unset( $specific_product_catg_arr[ count( $specific_product_catg_arr ) - 1 ] );
						$intersect = array_intersect( $resltcat, $specific_product_catg_arr );
						if ( isset( $intersect ) && ! empty( $intersect ) ) {
							$reslt[ $i ] = $result;
						}
					} else {
						$specific_product_arr = explode( '-', $result['specific_product'] );
						if ( isset( $specific_product_arr ) && ! empty( $specific_product_arr ) ) {
							unset( $specific_product_arr[ count( $specific_product_arr ) - 1 ] );
							if ( in_array( $id_product, $specific_product_arr ) ) {
								$reslt[ $i ] = $result;
							}
						}
					}
				}
			}
			$outputs = $this->ContentFilterEngine( $reslt );
			Cache::store( $cache_id, $outputs );
		}
		return Cache::retrieve( $cache_id );
	}

	/**
	 * ContentFilterEngine filters the results.
	 *
	 * @param mixed $results results fetched from database.
	 */
	public function ContentFilterEngine( $results = array() ) {
		$outputs = array();
		if ( isset( $results ) && ! empty( $results ) ) {
			$i = 0;
			foreach ( $results as $classypetab_values ) {
				foreach ( $classypetab_values as $classypetab_key => $vcval ) {
					if ( $classypetab_key == 'content' ) {
						// $outputs[ $i ]['content'] = $this->vctab_content_filter( $vcval );
						$outputs[ $i ]['content'] = $vcval;
					}
					if ( $classypetab_key == 'title' ) {
						$outputs[ $i ]['title'] = $vcval;
					}
					if ( $classypetab_key == 'id_classyproductextratabs' ) {
						$outputs[ $i ]['id_classyproductextratabs'] = $vcval;
					}
					if ( $classypetab_key == 'content_type' ) {
						$outputs[ $i ]['content_type'] = $vcval;
					}
					if ( $classypetab_key == 'modules_list' ) {
						$outputs[ $i ]['modules_list'] = $vcval;
					}
					if ( $classypetab_key == 'module_hook_list' ) {
						$outputs[ $i ]['module_hook_list'] = $vcval;
					}
				}
				$i++;
			}
		}
		return $outputs;
	}
}
