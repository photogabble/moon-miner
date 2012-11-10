/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
    config.toolbar = 'BNT';
    config.removePlugins = 'elementspath'; 
    config.skin = 'chris';
    config.toolbar_BNT =
    [
        { name: 'document', items : [ 'NewPage'] },
        { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','Undo','Redo' ] },
        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','RemoveFormat' ] },
        { name: 'paragraph', items : [ 'NumberedList','BulletedList','Outdent','Indent','Blockquote','JustifyLeft','JustifyCenter','JustifyRight'] },
/*        { name: 'insert', items : [ 'HorizontalRule','Smiley' ] }, disable Smiley until we have support for them all worked out */
        { name: 'insert', items : [ 'HorizontalRule' ] },
        { name: 'styles', items : [ 'Styles','Font','FontSize' ] },
        { name: 'colors', items : [ 'TextColor','BGColor' ] },
        { name: 'tools', items : [ 'Maximize','About' ] }
   ];
};
