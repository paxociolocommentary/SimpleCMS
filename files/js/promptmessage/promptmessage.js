var PromptMessage = function(){
	if( $( '#promptmessage' ).length == 0 ){
		var _section = document.createElement( 'div' );
		_section.setAttribute( 'id', 'promptmessage' );
		_section.setAttribute( 'class', 'alert alert-success' );
		_section.setAttribute( 'role', 'alert' );
		document.body.appendChild( _section );
	}

	this.element = $( '#promptmessage' );
	this.timeout = false;
	this.interval = 5;
	
	this._init();
}

PromptMessage.prototype._init = function(){
	var self = this;
	var _messages = {
		error : ( $( '.errmsg:eq(0)' ).length > 0 && $( '.errmsg:eq(0)' ).html().length > 0 ? $( '.errmsg:eq(0)' ).html() : '' ),
		success : ( $( '.sucmsg:eq(0)' ).length > 0 && $( '.sucmsg:eq(0)' ).html().length > 0 ? $( '.sucmsg:eq(0)' ).html() : '' )
	};
	
	if( _messages.error.length > 0 ){
		this.show( _messages.error );
	}
	
	if( _messages.success.length > 0 ){
		this.show( _messages.success );
	}
}

PromptMessage.prototype.set = function( ele ){
	this.element = $( '#' + ele );
}

PromptMessage.prototype.show = function( message, type, force ){
	var self = this;
	force = typeof force == 'undefined' ? false : force;
	type = typeof type == 'undefined' ? 'success' : type;

	this.element.html( message );

	if( this.element.not( ':visible' ) ){
		this.element.removeClass( 'alert-success alert-info alert-warning alert-danger' );
		
		this.element.addClass( 'alert-' + type ).show().animate({
			top : '0px'
		}, 500 );
	}
	
	if( this.timeout ){
		clearTimeout( this.timeout );
	}
	
	if( !force ){
		this.timeout = setTimeout(function(){
			self.hide();
		}, self.interval * 1000 );
	}
}

PromptMessage.prototype.hide = function(){
	var self = this;
	
	clearTimeout( this.timeout );
	
	if( this.element.is( ':visible' ) ){
		var height = this.element.height();
		height = ( height + 10 ) * -1;
		this.element.animate({
			top : height + 'px'
		}, 500, function(){
			self.element.hide();
		})
	}
}