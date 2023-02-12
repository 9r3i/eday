/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

var clanguage;

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = clanguage=='indonesia'?'id':'en';
	// config.uiColor = '#AADC6E';
};
