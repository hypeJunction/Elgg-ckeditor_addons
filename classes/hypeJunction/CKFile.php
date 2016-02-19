<?php

namespace hypeJunction;

class CKFile extends ElggFile {

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = "ckeditor_file";
	}
}
