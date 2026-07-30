/**
 * Minimal dependency-free lightbox for Auto Attachments image galleries.
 * Replaces the old Slimbox2 (jQuery) integration. Groups links by their
 * data-aa-lightbox attribute and supports keyboard/click navigation.
 */
( function () {
	'use strict';

	function button( label, className ) {
		var el = document.createElement( 'button' );
		el.type = 'button';
		el.className = className;
		el.setAttribute( 'aria-label', label );
		el.textContent = label;
		return el;
	}

	function AutoAttachmentsLightbox() {
		this.overlay = null;
		this.groups = {};
		this.currentGroup = null;
		this.currentIndex = 0;
	}

	AutoAttachmentsLightbox.prototype.init = function () {
		var self = this;
		var links = document.querySelectorAll( '[data-aa-lightbox]' );

		links.forEach( function ( link ) {
			var group = link.getAttribute( 'data-aa-lightbox' );
			if ( ! self.groups[ group ] ) {
				self.groups[ group ] = [];
			}
			self.groups[ group ].push( link );

			link.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				self.open( group, self.groups[ group ].indexOf( link ) );
			} );
		} );
	};

	AutoAttachmentsLightbox.prototype.build = function () {
		var self = this;
		var overlay = document.createElement( 'div' );
		overlay.className = 'aa-lightbox-overlay';
		overlay.hidden = true;

		var figure = document.createElement( 'figure' );
		figure.className = 'aa-lightbox-figure';

		var img = document.createElement( 'img' );
		var caption = document.createElement( 'figcaption' );
		caption.className = 'aa-lightbox-caption';

		var closeBtn = button( '×', 'aa-lightbox-close' );
		var prevBtn = button( '‹', 'aa-lightbox-prev' );
		var nextBtn = button( '›', 'aa-lightbox-next' );

		figure.appendChild( img );
		figure.appendChild( caption );
		figure.appendChild( closeBtn );
		figure.appendChild( prevBtn );
		figure.appendChild( nextBtn );
		overlay.appendChild( figure );
		document.body.appendChild( overlay );

		closeBtn.addEventListener( 'click', function () { self.close(); } );
		prevBtn.addEventListener( 'click', function () { self.step( -1 ); } );
		nextBtn.addEventListener( 'click', function () { self.step( 1 ); } );
		overlay.addEventListener( 'click', function ( event ) {
			if ( event.target === overlay ) {
				self.close();
			}
		} );
		document.addEventListener( 'keydown', function ( event ) {
			if ( overlay.hidden ) {
				return;
			}
			if ( 'Escape' === event.key ) {
				self.close();
			} else if ( 'ArrowLeft' === event.key ) {
				self.step( -1 );
			} else if ( 'ArrowRight' === event.key ) {
				self.step( 1 );
			}
		} );

		this.overlay = overlay;
		this.img = img;
		this.caption = caption;
		this.prevBtn = prevBtn;
		this.nextBtn = nextBtn;
	};

	AutoAttachmentsLightbox.prototype.open = function ( group, index ) {
		this.currentGroup = group;
		this.currentIndex = index;
		if ( ! this.overlay ) {
			this.build();
		}
		this.render();
		this.overlay.hidden = false;
	};

	AutoAttachmentsLightbox.prototype.close = function () {
		if ( this.overlay ) {
			this.overlay.hidden = true;
		}
	};

	AutoAttachmentsLightbox.prototype.step = function ( delta ) {
		var links = this.groups[ this.currentGroup ];
		this.currentIndex = ( this.currentIndex + delta + links.length ) % links.length;
		this.render();
	};

	AutoAttachmentsLightbox.prototype.render = function () {
		var links = this.groups[ this.currentGroup ];
		var link = links[ this.currentIndex ];
		var thumb = link.querySelector( 'img' );

		this.img.src = link.getAttribute( 'href' );
		this.img.alt = thumb ? thumb.alt : '';
		this.caption.textContent = this.img.alt;

		var multiple = links.length > 1;
		this.prevBtn.hidden = ! multiple;
		this.nextBtn.hidden = ! multiple;
	};

	document.addEventListener( 'DOMContentLoaded', function () {
		new AutoAttachmentsLightbox().init();
	} );
} )();
