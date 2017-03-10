/*
// @name: RespImg.js
// @version: 0.1
// 
// Copyright 2016 John Henry Pacay
// Licensed under the MIT license
*/
(function($) {
	$.fn.RespImg = function( Options ) {
		var options = {};
	
		options = $.extend( options, Options ); 
		
		$( this ).each(function(){
			var _this = $( this );
			_this.data( 'initialDi',
				{
					width : _this.width(),
					height : _this.height()
				}
			);
			
			$( window ).on( "resize load", function(){
				_resize( _this );
			});
		});
		
		function FindParent( _this ){
			return _this[0].parentNode.tagName.toLowerCase() != 'a' ? _this.parent() : FindParent( _this.parent() );
		}
		
		function _resize( _this ){
			var _container = FindParent( _this );
			var _containerDi = {
				width : _container.innerWidth(),
				height : _container.innerHeight()
			};
			var _ratio = +_containerDi.width / +_this.data( 'initialDi' ).width;
			
			if( _ratio < 1 ){
				// new width and height
				var newWidth = Math.ceil( +_this.data( 'initialDi' ).width * _ratio );
				var newHeight = Math.ceil( +_this.data( 'initialDi' ).height * _ratio );
			} else{
				var newWidth = +_this.data( 'initialDi' ).width;
				var newHeight = +_this.data( 'initialDi' ).height;
			}
			
			_this.css({
				width : newWidth + 'px',
				height : newHeight + 'px'
			});
		}
	};
})(jQuery);