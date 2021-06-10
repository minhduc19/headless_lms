/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
//    config.filebrowserBrowseUrl = '/browser/browse.php';
//    config.filebrowserUploadUrl = '/gamota-cms/admin/uploader/upload';
    //config.uploadUrl = '/gamota-cms/admin/uploader/upload';
    config.uploadUrl = '/js/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json';
    config.filebrowserBrowseUrl = '/js/plugins/ckfinder/ckfinder.html';
    config.filebrowserImageBrowseUrl = '/js/plugins/ckfinder/ckfinder.html?type=Images';
    config.filebrowserUploadUrl = '/js/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
    config.filebrowserImageUploadUrl = '/js/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
};
