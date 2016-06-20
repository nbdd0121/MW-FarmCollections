<?php
namespace FarmColle\GUR;

class UsersPager extends \UsersPager {

	// This is an optimization, but is not required.
	// However, the optimization performed is actually
	// wrong, so it is skipped here
	public function doBatchLookups() {
	}

}

class SpecialListusers extends \SpecialListUsers {

	// Override SpecialListUsers so an overriden pager is used
	public function execute( $par ) {
		$this->setHeaders();
		$this->outputHeader();

		$up = new UsersPager( $this->getContext(), $par, $this->including() );

		// getBody() first to check, if empty
		$usersbody = $up->getBody();

		$s = '';
		if ( !$this->including() ) {
			$s = $up->getPageHeader();
		}

		if ( $usersbody ) {
			$s .= $up->getNavigationBar();
			$s .= \Html::rawElement( 'ul', array(), $usersbody );
			$s .= $up->getNavigationBar();
		} else {
			$s .= $this->msg( 'listusers-noresult' )->parseAsBlock();
		}

		$this->getOutput()->addHTML( $s );
	}

}
