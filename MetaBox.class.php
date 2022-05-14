<?php
namespace constructor\core\MetaBox;

class MetaBox extends \constructor\core\Blocks\DataBlock {
	const defaults = array(
		'title'			=>	'Box',
		'name'			=>  'box',
		'screen'		=>	'post',
		'context'		=>	'advanced',
		'priority'		=>  'default',
		'callback_args'	=> array(),
		'single'		=> false,
	);

	const exclude = parent::exclude + self::defaults;
 
	protected static $counter = 1;

	function __construct( $params ) {
		$this->params = array_merge(self::defaults,$params);
		$this->name = $this->params['name'];
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	public function add_meta_box() {
		add_meta_box(
			$this->name,
			$this->params['title'],
			array( $this, 'show' ),
			$this->params['screen'],
			$this->params['context'],
			$this->params['priority'],
			$this->params['callback_args']
		);
	}

	protected function addNonce() {
		wp_nonce_field( $this->name . '_meta_box', $this->name . '_meta_box_nonce' );
	}

	protected function verify_nonce() {
		if( isset( $_POST[$this->name . '_meta_box_nonce'] ) && wp_verify_nonce( $_POST[$this->name . '_meta_box_nonce'], $this->name . '_meta_box' ) ) {
			return true;
		}
		return false;
	}

	public function show( $post, $args ) {
		$this->params['sourceObject'] = $post->ID;
		parent::__construct( $this->params );
		$this->addNonce();

		echo $this->render();
	}

	public function save( $id = 0 ) {
		if( !$this->verify_nonce() ) {
			return false;
		}

		$this->params['sourceObject'] = $id;

		parent::__construct( $this->params );
		parent::save();
	}
}